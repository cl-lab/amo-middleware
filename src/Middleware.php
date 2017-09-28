<?php
/**
 * Created by PhpStorm.
 * User: ak
 * Date: 25.09.17
 * Time: 10:51
 *
 * Данный класс является обёрткой для библиотеки https://github.com/dotzero/amocrm-php и является частью пакета
 * предназначенного для обеспечения единого интерфейса работы с AmoCRM.
 * При обработке исключений генерируемых данным классом, стоит учесть тот факт, что данный коласс выбрасывает исключения
 * в случаи если был переданны данные не того типа который ожидает функция или в случаи не валидности переданных
 * параметров и генерирует исключения типа \Exception. В то время как библиотека дотзеро генерирует исключения
 * связанные с ошибками при работе с AmoCRM и генерирует исключения типа AmoCRM\Exception. Обратите внимание на эту
 * особенность при написании блока catch.
 */

namespace CleverLab\AmoCRM;

use CleverLab\AmoCRM\Interfaces\iMiddleware;
use AmoCRM\Client;

class Middleware implements iMiddleware
{
    private $domain;

    private $login;

    private $apiKey;

    private $proxy;

    private $amo = null;

    private static $unsortedRequireFields = array(
        'source', // Название источника заявки
        'source_uid', // Уникальный идентификатор заявки
        'source_data', // Данные заявки (зависят от категории)
    );

    /**
     * Middleware constructor.
     *
     * @param string $domain Домен или поддомен аккаунта
     * @param string $login Логин пользователя AmoCRM
     * @param string $apiKey API ключ, который можно взять в админ панели AmoCRM
     * @param string|null $proxy Прокси сервер для отправки запроса
     */
    public function __construct($domain, $login, $apiKey, $proxy = null)
    {
        $this->domain = $domain;
        $this->login = $login;
        $this->apiKey = $apiKey;
        $this->proxy = $proxy;
    }

    /**
     * Возвращает информацию по аккаунту.
     *
     * @link https://developers.amocrm.ru/rest_api/accounts_current.php
     *
     * @param bool $short Краткий формат, только основные поля
     * @param array $parameters Ассоциативный массив параметров к amoCRM API
     *
     * @return array Ответ amoCRM API
     */
    public function getAccount($short = false, $parameters = array())
    {
        $amo = $this->getAmo();

        return $amo->account->apiCurrent($short, $parameters);
    }

    /**
     * Возвращает сведения о пользователе по его логину.
     * Если не указывать логин, вернутся сведения о владельце API ключа.
     *
     * @param string $login Логин пользователя
     *
     * @return mixed Данные о пользователе
     */
    public function getUserByLogin($login)
    {
        $amo = $this->getAmo();

        return $amo->account->getUserByLogin($login);
    }

    /**
     * Возвращает список контактов.
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     */
    public function getContacts($parameters, $modified = null)
    {
        return $this->getObjects('contact', $parameters, $modified);
    }

    /**
     * Добавляет контакт
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор контакта
     */
    public function addContact($parameters, $debug = false)
    {
        return $this->addObject('contact', $parameters, $debug);
    }

    /**
     * Групповое добавление контактов
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов контактов
     */
    public function addGroupOfContacts($dataList, $debug = false)
    {
        return $this->addGroupOfObject('contact', $dataList, $debug);
    }

    /**
     * Обновляет контакт
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_set.php
     *
     * @param int $id Идентификатор контакта
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дата последнего изменения данной сущности
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateContact($id, $parameters, $modified = 'now', $debug = false)
    {
        return $this->updateObject('contact', $id, $parameters, $modified, $debug);
    }

    /**
     * Возвращает связи между сделками и контактами
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_links.php
     *
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array Ответ amoCRM API
     * @throws \Exception
     */
    public function getContactLinks($parameters)
    {
        if (!is_array($parameters)) {
            throw new \Exception('$parameters not valid. $parameters must be an array');
        }

        $amo = $this->getAmo();

        return $amo->contact->apiLinks($parameters);
    }

    /**
     * Возвращает список сделок
     *
     * @link https://developers.amocrm.ru/rest_api/leads_list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     */
    public function getLeads($parameters, $modified = null)
    {
        return $this->getObjects('lead', $parameters, $modified);
    }

    /**
     * Добавляет сделку
     *
     * @link https://developers.amocrm.ru/rest_api/leads_set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор сделки
     */
    public function addLead($parameters, $debug = false)
    {
        return $this->addObject('lead', $parameters, $debug);
    }

    /**
     * Групповое добавление сделок
     *
     * @link https://developers.amocrm.ru/rest_api/leads_set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов сделок
     */
    public function addGroupOfLeads($dataList, $debug = false)
    {
        return $this->addGroupOfObject('lead', $dataList, $debug);
    }

    /**
     * Обновляет сделку
     *
     * @link https://developers.amocrm.ru/rest_api/leads_set.php
     *
     * @param int $id Идентификатор сделки
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дата последнего изменения данной сущности
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateLead($id, $parameters, $modified = 'now', $debug = false)
    {
        return $this->updateObject('lead', $id, $parameters, $modified, $debug);
    }

    /**
     * Возвращает список компаний.
     *
     * @link https://developers.amocrm.ru/rest_api/company_list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     */
    public function getCompanies($parameters, $modified = null)
    {
        return $this->getObjects('company', $parameters, $modified);
    }

    /**
     * Добавляет компанию
     *
     * @link https://developers.amocrm.ru/rest_api/company_set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор компании
     */
    public function addCompany($parameters, $debug = false)
    {
        return $this->addObject('company', $parameters, $debug);
    }

    /**
     * Групповое добавление компаний
     *
     * @link https://developers.amocrm.ru/rest_api/company_set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов компаний
     */
    public function addGroupOfCompanies($dataList, $debug = false)
    {
        return $this->addGroupOfObject('company', $dataList, $debug);
    }

    /**
     * Обновляет компанию
     *
     * @link https://developers.amocrm.ru/rest_api/company_set.php
     *
     * @param int $id Идентификатор компании
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дата последнего изменения данной сущности
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateCompany($id, $parameters, $modified = 'now', $debug = false)
    {
        return $this->updateObject('company', $id, $parameters, $modified, $debug);
    }

    /**
     * Возвращает список покупателей.
     *
     * @link https://developers.amocrm.ru/rest_api/customers/list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     */
    public function getCustomers($parameters, $modified = null)
    {
        return $this->getObjects('customer', $parameters, $modified);
    }

    /**
     * Добавляет покупателя
     *
     * @link https://developers.amocrm.ru/rest_api/customers/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор покупателя
     */
    public function addCustomer($parameters, $debug = false)
    {
        return $this->addObject('customer', $parameters, $debug);
    }

    /**
     * Групповое добавление покупателей
     *
     * @link https://developers.amocrm.ru/rest_api/customers/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов покупателей
     */
    public function addGroupOfCustomers($dataList, $debug = false)
    {
        return $this->addGroupOfObject('customer', $dataList, $debug);
    }

    /**
     * Обновляет покупателя
     *
     * @link https://developers.amocrm.ru/rest_api/customers/set.php
     *
     * @param int $id Идентификатор покупателя
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateCustomer($id, $parameters, $debug = false)
    {
        return $this->updateObject('customer', $id, $parameters, null, $debug);
    }

    /**
     * Возвращает список транзакций.
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     */
    public function getTransactions($parameters, $modified = null)
    {
        return $this->getObjects('transaction', $parameters, $modified);
    }

    /**
     * Добавляет транзакцию
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор транзакции
     */
    public function addTransaction($parameters, $debug = false)
    {
        return $this->addObject('transaction', $parameters, $debug);
    }

    /**
     * Групповое добавление транзакций
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов транзакций
     */
    public function addGroupOfTransactions($dataList, $debug = false)
    {
        return $this->addGroupOfObject('transaction', $dataList, $debug);
    }

    /**
     * Удаляет транзакцию
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/set.php
     *
     * @param int $id Идентификатор транзакции
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteTransaction($id)
    {
        $amo = $this->getAmo();

        return $amo->transaction->apiDelete((int)$id);
    }

    /**
     * Возвращает список задач.
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     */
    public function getTasks($parameters, $modified = null)
    {
        return $this->getObjects('task', $parameters, $modified);
    }

    /**
     * Добавляет задачу
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор задачи
     */
    public function addTask($parameters, $debug = false)
    {
        return $this->addObject('task', $parameters, $debug);
    }

    /**
     * Групповое добавление задач
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов задач
     */
    public function addGroupOfTasks($dataList, $debug = false)
    {
        return $this->addGroupOfObject('task', $dataList, $debug);
    }

    /**
     * Обновляет задачу
     *
     * @link https://developers.amocrm.ru/rest_api/tasks_set.php
     *
     * @param int $id Идентификатор задачи
     * @param array $text Список массивов содержащих параметры
     * @param string $modified Дополнительная фильтрация по (изменено с)
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateTask($id, $text, $modified = 'now', $debug = false)
    {
        $amo = $this->getAmo();

        $task = $amo->task;

        if ($debug) {
            $task->debug(true);
        }

        return $task->apiUpdate((int)$id, $task, $modified);
    }

    /**
     * Возвращает список примечаний.
     *
     * @link https://developers.amocrm.ru/rest_api/notes_list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     */
    public function getNotes($parameters, $modified = null)
    {
        return $this->getObjects('note', $parameters, $modified);
    }

    /**
     * Добавляет примечание
     *
     * @link https://developers.amocrm.ru/rest_api/notes_set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор примечания
     */
    public function addNote($parameters, $debug = false)
    {
        return $this->addObject('note', $parameters, $debug);
    }

    /**
     * Групповое добавление примечаний
     *
     * @link https://developers.amocrm.ru/rest_api/notes_set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов примечаний
     */
    public function addGroupOfNotes($dataList, $debug = false)
    {
        return $this->addGroupOfObject('note', $dataList, $debug);
    }

    /**
     * Обновление примечаний
     *
     * @link https://developers.amocrm.ru/rest_api/notes_set.php
     *
     * @param int $id Идентификатор примечания
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дата последнего изменения данной сущности
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateNote($id, $parameters, $modified = 'now', $debug = false)
    {
        return $this->updateObject('customer', $id, $parameters, $modified, $debug);
    }

    /**
     * Добавляет дополнительное поле
     *
     * @link https://developers.amocrm.ru/rest_api/fields_set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор поля
     */
    public function addCustomField($parameters, $debug = false)
    {
        return $this->addObject('custom_field', $parameters, $debug);
    }

    /**
     * Групповое добавление дополнительных полей
     *
     * @link https://developers.amocrm.ru/rest_api/fields_set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов полей
     */
    public function addGroupOfCustomFields($dataList, $debug = false)
    {
        return $this->addGroupOfObject('custom_field', $dataList, $debug);
    }

    /**
     * Удаляет дополнительное поле
     *
     * @link https://developers.amocrm.ru/rest_api/fields_set.php
     *
     * @param int $id Идентификатор дополнительного поля
     * @param string $origin Уникальный идентификатор сервиса заданный при создании параметром origin
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteCustomField($id, $origin, $debug = false)
    {
        $amo = $this->getAmo();

        $field = $amo->custom_field;

        if ($debug) {
            $field->debug(true);
        }

        return $field->apiDelete($id, $origin);
    }

    /**
     * Добавляет звонок
     *
     * @link https://developers.amocrm.ru/rest_api/calls_set.php
     *
     * @param string $code Уникальный идентификатор сервиса
     * @param string $key Ключ сервиса, который можно получить написав в техническую поддержку amoCRM
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return string Уникальный идентификатор звонка
     * @throws \Exception
     */
    public function addCall($code, $key, $parameters, $debug = false)
    {
        $call = $this->getCallObject($parameters, $debug);

        return $call->apiAdd($code, $key);
    }

    /**
     * Групповое добавление звонков
     *
     * @link https://developers.amocrm.ru/rest_api/calls_set.php
     *
     * @param string $code Уникальный идентификатор сервиса
     * @param string $key Ключ сервиса, который можно получить написав в техническую поддержку amoCRM
     * @param array $dataList Список массивов содержащих параметры для звонков
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов звонков
     * @throws \Exception
     */
    public function addGroupOfCalls($code, $key, $dataList, $debug = false)
    {
        if (!is_array($dataList)) {
            throw new \Exception('$dataList not valid. $dataList must be an array');
        }

        $arrOfCall = array();
        foreach ($dataList as $k => $data) {
            $call = $this->getCallObject($data, $debug);

            if ($call) {
                $arrOfCall[] = $call;
            }
        }

        return $call->apiAdd($code, $key, $arrOfCall);
    }

    /**
     * Возвращает список неразобранного.
     * Эквивалентно методу /api/unsorted/list/
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/list.php
     *
     * @param array $parameters Массив параметров для выборки объектов
     *
     * @return array Ответ amoCRM API
     */
    public function getUnsorted($parameters = array())
    {
        $allowFields = array(
            'page_size',
            'PAGEN_1',
            'categories',
            'order_by',
        );

        $this->removeNotAllowKeys($parameters, $allowFields);

        $amo = $this->getAmo();

        return $amo->unsorted->apiList($parameters);
    }

    /**
     * Метод для принятия неразобранных заявок
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/accept.php
     *
     * @param string|array $uids Массив uid-ов неразобранных заявок или uid заявки
     * @param int $userId id пользователя аккаунта, от имени которого будут созданы сделки/контакты/компании
     * @param null|int $statusId Статус сделок, которые будут созданы в результате принятия неразобранного
     *
     * @return array Ответ amoCRM API
     */
    public function acceptUnsorted($uids, $userId, $statusId = null)
    {
        $amo = $this->getAmo();

        return $amo->unsorted->apiAccept($uids, $userId);
    }

    /**
     * Отклонение неразобранных заявок
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/decline.php
     *
     * @param string|array $uids Массив uid-ов неразобранных заявок или uid заявки
     * @param int $userId id пользователя аккаунта, от имени которого будут созданы сделки/контакты/компании
     *
     * @return array Ответ amoCRM API
     */
    public function declineUnsorted($uids, $userId)
    {
        $amo = $this->getAmo();

        return $amo->unsorted->apiDecline($uids, $userId);
    }

    /**
     * Агрегирование неразобранных заявок
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/get_all_summary.php
     *
     * @return array Ответ amoCRM API
     */
    public function summaryUnsorted()
    {
        $amo = $this->getAmo();

        return $amo->unsorted->apiGetAllSummary();
    }

    /**
     * Добавление неразобранной заявки с примечанием из Письма.
     * Не предусматривает группового добавления
     *
     * @param array $unsortedParameters Массив параметров для неразобранного
     * @param array $leadParameters Массив параметров для сделки
     * @param array $noteParameters Массив параметров для примечания
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор заявки
     */
    public function addMailUnsortedToLead(
        $unsortedParameters,
        $leadParameters = array(),
        $noteParameters = array(),
        $debug = false
    ) {
        $this->removeNotAllowKeys(
            $unsortedParameters,
            array_merge(self::$unsortedRequireFields, array('date_create'))
        );
        $this->checkRequiredKeys($unsortedParameters, self::$unsortedRequireFields);

        $amo = $this->getAmo();

        $unsorted = $amo->unsorted;
        if ($debug) {
            $unsorted->debug(true);
        }
        $this->setParameters($unsorted, $unsortedParameters);

        if ($leadParameters) {
            // Сделка которая будет создана после одобрения заявки.
            $lead = $amo->lead;
            $this->setParameters($lead, $leadParameters);

            if ($noteParameters) {
                // Примечания, которые появятся в сделке после принятия неразобранного
                $note = $amo->note;
                $this->setParameters($note, $noteParameters);
                $lead['notes'] = $note;
            }

            // Присоединение сделки к неразобранному
            $unsorted->addDataLead($lead);
        }

        // Добавление неразобранной заявки с типом MAIL
        $unsortedId = $unsorted->apiAddMail();

        return $unsortedId;
    }

    /**
     * Добавление неразобранного контакта с примечанием из Письма.
     * Не предусматривает группового добавления
     *
     * @param array $unsortedParameters Массив параметров для неразобранного
     * @param array $contactParameters Массив параметров для контакта
     * @param array $noteParameters Массив параметров для примечания
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор заявки
     */
    public function addMailUnsortedToContact(
        $unsortedParameters,
        $contactParameters = array(),
        $noteParameters = array(),
        $debug = false
    ) {
        $this->removeNotAllowKeys(
            $unsortedParameters,
            array_merge(self::$unsortedRequireFields, array('date_create'))
        );
        $this->checkRequiredKeys($unsortedParameters, self::$unsortedRequireFields);

        $amo = $this->getAmo();

        $unsorted = $amo->unsorted;
        if ($debug) {
            $unsorted->debug(true);
        }
        $this->setParameters($unsorted, $unsortedParameters);

        if ($contactParameters) {
            // Добавление контакта или компании которая будет создана после одобрения заявки.
            $contact = $amo->contact;
            $this->setParameters($contact, $contactParameters);

            if ($noteParameters) {
                // Примечания, которые появятся в сделке после принятия неразобранного
                $note = $amo->note;
                $this->setParameters($note, $noteParameters);
                $contact['notes'] = $note;
            }

            // Присоединение контакта к неразобранному
            $unsorted->addDataContact($contact);
        }

        // Добавление неразобранной заявки с типом MAIL
        $unsortedId = $unsorted->apiAddMail();

        return $unsortedId;
    }

    /**
     * Добавление неразобранной заявки с примечанием из Формы.
     * Не предусматривает группового добавления
     *
     * @param array $unsortedParameters Массив параметров для неразобранного
     * @param array $leadParameters Массив параметров для сделки
     * @param array $noteParameters Массив параметров для примечания
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор заявки
     */
    public function addFormUnsortedToLead(
        $unsortedParameters,
        $leadParameters = array(),
        $noteParameters = array(),
        $debug = false
    ) {
        $this->removeNotAllowKeys(
            $unsortedParameters,
            array_merge(self::$unsortedRequireFields, array('date_create'))
        );
        $this->checkRequiredKeys($unsortedParameters, self::$unsortedRequireFields);

        $amo = $this->getAmo();

        $unsorted = $amo->unsorted;
        if ($debug) {
            $unsorted->debug(true);
        }
        $this->setParameters($unsorted, $unsortedParameters);

        if ($leadParameters) {
            // Сделка которая будет создана после одобрения заявки.
            $lead = $amo->lead;
            $this->setParameters($lead, $leadParameters);

            if ($noteParameters) {
                // Примечания, которые появятся в сделке после принятия неразобранного
                $note = $amo->note;
                $this->setParameters($note, $noteParameters);
                $lead['notes'] = $note;
            }

            // Присоединение сделки к неразобранному
            $unsorted->addDataLead($lead);
        }

        // Добавление неразобранной заявки с типом FORMS
        $unsortedId = $unsorted->apiAddForms();

        return $unsortedId;
    }

    /**
     * Добавление неразобранного контакта с примечанием из Формы.
     * Не предусматривает группового добавления
     *
     * @param array $unsortedParameters Массив параметров для неразобранного
     * @param array $contactParameters Массив параметров для контакта
     * @param array $noteParameters Массив параметров для примечания
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор заявки
     */
    public function addFormUnsortedToContact(
        $unsortedParameters,
        $contactParameters = array(),
        $noteParameters = array(),
        $debug = false
    ) {
        $this->removeNotAllowKeys(
            $unsortedParameters,
            array_merge(self::$unsortedRequireFields, array('date_create'))
        );
        $this->checkRequiredKeys($unsortedParameters, self::$unsortedRequireFields);

        $amo = $this->getAmo();

        $unsorted = $amo->unsorted;
        if ($debug) {
            $unsorted->debug(true);
        }
        $this->setParameters($unsorted, $unsortedParameters);

        if ($contactParameters) {
            // Добавление контакта или компании которая будет создана после одобрения заявки.
            $contact = $amo->contact;
            $this->setParameters($contact, $contactParameters);

            if ($noteParameters) {
                // Примечания, которые появятся в сделке после принятия неразобранного
                $note = $amo->note;
                $this->setParameters($note, $noteParameters);
                $contact['notes'] = $note;
            }

            // Присоединение контакта к неразобранному
            $unsorted->addDataContact($contact);
        }

        // Добавление неразобранной заявки с типом FORMS
        $unsortedId = $unsorted->apiAddForms();

        return $unsortedId;
    }

    /**
     * Список Webhooks
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/list.php
     *
     * @return array Ответ amoCRM API
     */
    public function getWebhooks()
    {
        $amo = $this->getAmo();

        return $amo->webhooks->apiList();
    }

    /**
     * Добавление Webhooks.
     * Добавляет хук на одно событие или группу событий
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/subscribe.php
     *
     * @param null|string $url URL на который необходимо присылать уведомления, должен соответствовать стандарту RFC 2396
     * @param array|string $events Список событий, при которых должны отправляться Webhooks
     *
     * @return array|false Ответ amoCRM API
     */
    public function webhooksSubscribe($url = null, $events = array())
    {
        $amo = $this->getAmo();

        return $amo->webhooks->apiSubscribe($url, $events);
    }

    /**
     * Удаления Webhooks.
     * Удаляет хук на одно событие или группу событий
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/unsubscribe.php
     *
     * @param null|string $url URL на который необходимо присылать уведомления, должен соответствовать стандарту RFC 2396
     * @param array|string $events Список событий, при которых должны отправляться Webhooks
     *
     * @return array|false Ответ amoCRM API
     */
    public function webhooksUnsubscribe($url = null, $events = array())
    {
        $amo = $this->getAmo();

        return $amo->webhooks->apiUnsubscribe($url, $events);
    }

    /**
     * Список воронок и этапов продаж
     *
     * @link https://developers.amocrm.ru/rest_api/pipelines/list.php
     *
     * @param null|int $id Уникальный идентификатор воронки
     *
     * @return array Ответ amoCRM API
     */
    public function getPipelines($id = null)
    {
        $amo = $this->getAmo();

        return $amo->pipelines->apiList($id);
    }

    /**
     * Добавляет этап
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор покупателя
     */
    public function addPipeline($parameters, $debug = false)
    {
        return $this->addObject('pipelines', $parameters, $debug);
    }

    /**
     * Групповое добавление этапов
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов покупателей
     */
    public function addGroupOfPipelines($dataList, $debug = false)
    {
        return $this->addGroupOfObject('pipelines', $dataList, $debug);
    }

    /**
     * Обновление воронок и этапов продаж
     *
     * @param int $id Идентификатор этапа(воронки)
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updatePipeline($id, $parameters, $debug = false)
    {
        return $this->updateObject('pipelines', $id, $parameters, null, $debug);
    }

    /**
     * Удаление воронок
     *
     * Удаление последней воронки в аккаунте невозможно,
     * при удалении последней воронки выдается ошибка
     * "Impossible to delete last pipeline"
     *
     * @link https://developers.amocrm.ru/rest_api/pipelines/delete.php
     *
     * @param int $id Уникальный идентификатор воронки
     *
     * @return array Ответ amoCRM API
     */
    public function deletePipeline($id)
    {
        $amo = $this->getAmo();

        return $amo->pipelines->apiDelete((int)$id);
    }

    /**
     * Возвращает список периодов покупателя
     *
     * @link https://developers.amocrm.ru/rest_api/customers_periods/list.php
     *
     * @return array Ответ amoCRM API
     */
    public function getCustomersPeriods()
    {
        $amo = $this->getAmo();

        return $amo->customers_periods->apiList();
    }

    /**
     * Добавляет период покупателя
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор периода
     */
    public function addCustomerPeriod($parameters, $debug = false)
    {
        return $this->addObject('customers_periods', $parameters, $debug);
    }

    /**
     * Удаление и обновление периодов покупателей.
     * При изменении необходимо передать полный список периодов, включая уже существующие.
     * При удалении периода нужно исключить его из запроса.
     *
     * @link https://developers.amocrm.ru/rest_api/customers_periods/set.php
     *
     * @param $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов
     * @throws \Exception
     */
    public function setCustomerPeriod($dataList, $debug = false)
    {
        if (!is_array($dataList)) {
            throw new \Exception('$dataList must be an array');
        }

        $arrOfPeriods = array();

        $amo = $this->getAmo();

        foreach ($dataList as $k => $data) {
            if (!is_array($data)) {
                throw new \Exception('Data not valid');
            }
            $period = $amo->customers_periods;
            if ($debug) {
                $period->debug(true);
            }
            $this->setParameters($period, $data);
            $arrOfPeriods[] = $period;
        }

        return $amo->customers_periods->apiSet($arrOfPeriods);
    }

    /**
     * Возвращает список виджетов.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/list.php
     *
     * @param array $parameters Массив параметров для выборки объектов
     *
     * @return array Ответ amoCRM API
     */
    public function getWidgets($parameters = array())
    {
        $allowFields = array(
            'widget_id',
            'widget_code',
        );

        $this->removeNotAllowKeys($parameters, $allowFields);

        $amo = $this->getAmo();

        return $amo->widgets->apiList($parameters);
    }

    /**
     * Включение виджетов
     *
     * @param $parameters Массив параметров для включения виджета
     *
     * @link https://developers.amocrm.ru/rest_api/widgets/set.php
     *
     * @return array Ответ amoCRM API
     * @throws \Exception
     */
    public function widgetInstall($parameters)
    {
        if (!is_array($parameters)) {
            throw new \Exception();
        }

        $allowFields = array(
            'widget_id',
            'widget_code',
            'settings',
        );

        $this->removeNotAllowKeys($parameters, $allowFields);

        $amo = $this->getAmo();

        return $amo->widgets->apiInstall($parameters);
    }

    /**
     * Выключение виджетов
     *
     * @param $parameters Массив параметров для выключения виджета
     *
     * @link https://developers.amocrm.ru/rest_api/widgets/set.php
     *
     * @return array Ответ amoCRM API
     * @throws \Exception
     */
    public function widgetUninstall($parameters)
    {
        if (!is_array($parameters)) {
            throw new \Exception();
        }

        $allowFields = array(
            'widget_id',
            'widget_code',
            'settings',
        );

        $this->removeNotAllowKeys($parameters, $allowFields);

        $amo = $this->getAmo();

        return $amo->widgets->apiUninstall($parameters);
    }

    /**
     * Список каталогов
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/list.php
     *
     * @param null|int $id Идентификатор каталога
     *
     * @return array Ответ amoCRM API
     */
    public function getCatalogs($id = null)
    {
        $amo = $this->getAmo();

        return $amo->catalog->apiList($id);
    }

    /**
     * Добавляет каталог
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор каталога
     */
    public function addCatalog($parameters, $debug = false)
    {
        return $this->addObject('catalog', $parameters, $debug);
    }

    /**
     * Групповое добавление каталогов
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов каталогов
     */
    public function addGroupOfCatalogs($dataList, $debug = false)
    {
        return $this->addGroupOfObject('catalog', $dataList, $debug);
    }

    /**
     * Обновляет каталог
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     *
     * @param int $id Идентификатор каталога
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateCatalog($id, $parameters, $debug = false)
    {
        $allowFields = array(
            'name',
        );

        $this->removeNotAllowKeys($parameters, $allowFields);

        return $this->updateObject('catalog', $id, $parameters, null, $debug);
    }

    /**
     * Удаляет каталог
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     *
     * @param int $id Идентификатор каталога
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteCatalog($id)
    {
        $amo = $this->getAmo();

        return $amo->catalog->apiDelete((int)$id);
    }

    /**
     * Возвращает список элементов каталога.
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array Ответ amoCRM API
     */
    public function getCatalogElements($parameters = array())
    {
        $requiredFields = array(
            'catalog_id',
        );
        $this->checkRequiredKeys($parameters, $requiredFields);

        $allowFields = array(
            'id',
            'term',
            'order_by',
            'order_type',
            'PAGEN_1',
        );

        $this->removeNotAllowKeys(
            $parameters,
            array_merge($requiredFields, $allowFields)
        );

        return $this->getObjects('catalog_element', $parameters);
    }

    /**
     * Добавляет элемент каталога
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор контакта
     */
    public function addCatalogElement($parameters, $debug = false)
    {
        return $this->addObject('catalog_element', $parameters, $debug);
    }

    /**
     * Групповое добавление элементов каталога
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов контактов
     */
    public function addGroupOfCatalogElements($dataList, $debug = false)
    {
        return $this->addGroupOfObject('catalog_element', $dataList, $debug);
    }

    /**
     * Обновляет элемента каталога
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     *
     * @param int $id Идентификатор контакта
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateCatalogElement($id, $parameters, $debug = false)
    {
        return $this->updateObject('catalog_element', $id, $parameters, null, $debug);
    }

    /**
     * Удаляет элемент каталога
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     *
     * @param int $id Идентификатор элемента каталога
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteCatalogElement($id)
    {
        $amo = $this->getAmo();

        return $amo->catalog_element->apiDelete((int)$id);
    }

    /**
     * Возвращает список ссылок.
     *
     * @link https://developers.amocrm.ru/rest_api/links/list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array Ответ amoCRM API
     */
    public function getLinks($parameters)
    {
        $requiredFields = array(
            'from',
            'from_id',
        );
        $this->checkRequiredKeys($parameters, $requiredFields);

        $allowFields = array(
            'to',
            'to_id',
            'from_catalog_id',
            'to_catalog_id',
        );
        $this->removeNotAllowKeys(
            $parameters,
            array_merge($requiredFields, $allowFields)
        );

        return $this->getObjects('links', $parameters);
    }

    /**
     * Устанавливает связь между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     * @throws \Exception
     */
    public function addLink($parameters, $debug = false)
    {
        $link = $this->getLinkObject($parameters, $debug);

        return $link->apiLink();
    }

    /**
     * Групповое добавление связей между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     * @throws \Exception
     */
    public function addGroupOfLinks($dataList, $debug = false)
    {
        if (!is_array($dataList)) {
            throw new \Exception('$dataList not valid. $dataList must be an array');
        }

        $arrOfLinks = array();

        foreach ($dataList as $k => $linkParameters) {
            $arrOfLinks[] = getLinkObject($linkParameters, $debug);
        }

        return $this->getAmo()->links->apiLink($arrOfLinks);
    }

    /**
     * Разрывает связь между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
     */
    public function deleteLink($parameters, $debug = false)
    {
        $link = $this->getLinkObject($parameters, $debug);

        return $link->apiUnlink();
    }

    /**
     * Групповое разрывание связей между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     * @throws \Exception
     */
    public function deleteGroupOfLinks($dataList, $debug = false)
    {
        if (!is_array($dataList)) {
            throw new \Exception('$dataList not valid. $dataList must be an array');
        }

        $arrOfLinks = array();

        foreach ($dataList as $k => $linkParameters) {
            $arrOfLinks[] = getLinkObject($linkParameters, $debug);
        }

        return $this->getAmo()->links->apiUnlink($arrOfLinks);
    }

    /**
     * Возвращает объект для работы с библиотекой
     *
     * @return Client
     */
    private function getAmo()
    {
        if (!$this->amo) {
            $amo = Client($this->domain, $this->login, $this->apiKey, $this->proxy);
        }

        return $amo;
    }

    /**
     * Возвращает список объектов указанного типа
     *
     * @param string $type Тип объекта
     * @param array $parameters Массив параметров для выборки объектов
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array Ответ amoCRM API
     * @throws \Exception
     */
    private function getObjects($type, $parameters, $modified = null)
    {
        if (!is_array($parameters)) {
            throw new \Exception('$parameters not valid. $parameters must be an array');
        }

        $amo = $this->getAmo();

        return $amo->{$type}->apiList($parameters, $modified);
    }

    /**
     * Добавляет один объект указанного типа
     *
     * @param string $type Тип объекта
     * @param array $parameters Ассоциативный массив параметров для объекта
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Идентификатор объекта
     */
    private function addObject($type, $parameters, $debug = false)
    {
        $amo = $this->getAmo();
        $object = $amo->{$type};

        if ($debug) {
            $object->debug(true);
        }

        $this->setParameters($object, $parameters);

        $id = $object->apiAdd();

        return $id;
    }

    /**
     * Добавляет группу объектов указанного типа
     *
     * @param string $type
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     * @throws \Exception
     */
    private function addGroupOfObject($type, $dataList, $debug = false)
    {
        if (!is_array($dataList)) {
            throw new \Exception('$dataList not valid. $dataList must be an array');
        }

        $amo = $this->getAmo();

        $arrOfObjects = array();

        foreach ($dataList as $k => $v) {
            if (
                !is_array($v) ||
                !array_key_exists('parameters', $v)
            ) {
                throw new \Exception('List of ' . $type . 's parameters not valid');
            }

            $object = $amo->{$type};
            if ($debug) {
                $object->debug(true);
            }
            $this->setParameters($object, $v['parameters']);

            $arrOfObjects[] = $object;
        }

        if (!$arrOfObjects) {
            return array();
        }

        $ids = $amo->contact->apiAdd($arrOfObjects);

        return $ids;
    }

    /**
     * Обновляет объект
     *
     * @param string $type Тип объекта
     * @param int $id Идентификатор объекта
     * @param array $parameters Массив параметров
     * @param string $modified Дата последнего изменения данной сущности
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
     */
    private function updateObject($type, $id, $parameters, $modified = null, $debug = false)
    {
        $amo = $this->getAmo();
        $object = $amo->{$type};

        if ($debug) {
            $object->debug(true);
        }

        $this->setParameters($object, $parameters);

        if ($modified) {
            $res = $object->apiUpdate((int)$id, $modified);
        } else {
            $res = $object->apiUpdate((int)$id);
        }

        return $res;
    }

    /**
     * Проверяет наличие в массиве обязательных ключей
     *
     * @param array $array Обрабатываемый массив
     * @param $requiredKeys Массив обязательных параметров
     *
     * @throws \Exception
     */
    private function checkRequiredKeys($array, $requiredKeys)
    {
        foreach ($requiredKeys as $k => $v) {
            if (!array_key_exists($k, $array)) {
                throw new \Exception('You must set "' . $k . '" parameter');
            }
        }
    }

    /**
     * Удаляет недопустимые ключи из массива
     *
     * @param array $array Обрабатываемый массив
     * @param array $allowKeys Массив допустимыч ключей
     *
     * @throws \Exception
     */
    private function removeNotAllowKeys(&$array, $allowKeys)
    {
        if (!is_array($array)) {
            throw new \Exception('$array not valid. $array must be an array');
        }
        if (!is_array($allowKeys)) {
            throw new \Exception('$allowKeys not valid. $allowKeys must be an array');
        }

        foreach ($array as $k => $v) {
            if (!in_array($k, $allowKeys)) {
                unset($array[$k]);
            }
        }
    }

    /**
     * Возвращает \AmoCRM\Models\Call объект
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param boll $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return \AmoCRM\Models\Call
     * @throws \Exception
     */
    private function getCallObject($parameters, $debug)
    {
        $amo = $this->getAmo();

        $call = $amo->call;

        if ($debug) {
            $call->debug(true);
        }

        $requiredFields = array(
            'account_id',
            'uuid',
            'caller',
            'to',
            'date',
            'type',
            'billsec',
        );

        $this->removeNotAllowKeys(
            $parameters,
            array_merge($requiredFields, array('link'))
        );

        $this->checkRequiredKeys($parameters, $requiredFields);

        $this->setParameters($call, $parameters);

        return $call;
    }

    /**
     * Возвращает \AmoCRM\Models\Links объект
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug
     *
     * @return \AmoCRM\Models\Links
     * @throws \Exception
     */
    private function getLinkObject($parameters, $debug)
    {
        if (!is_array($parameters)) {
            throw new \Exception('$parameters not valid. $parameters must be an array');
        }

        $requiredFields = array(
            'from',
            'from_id',
            'to',
            'to_id',
        );
        $this->checkRequiredKeys($parameters, $requiredFields);

        $allowFields = array(
            'from_catalog_id',
            'to_catalog_id',
            'quantity',
        );
        $this->removeNotAllowKeys(
            $parameters,
            array_merge($requiredFields, $allowFields)
        );

        $amo = $this->getAmo();

        $link = $amo->links;
        if ($debug) {
            $link->debug(true);
        }
        $this->setParameters($link, $parameters);

        return $link;
    }

    /**
     * Устанавливает параметры в объект
     *
     * @param object $object Объект для установки в него параметров
     * @param array $parameters Ассоциативный массив параметров
     *
     * @throws \Exception
     */
    private function setParameters($object, $parameters)
    {
        if (!is_array($parameters)) {
            throw new \Exception('$parameters not valid. $parameters must be an array');
        }

        foreach ($parameters as $k => $v) {
            switch ($k) {
                case 'custom_fields':
                    $this->addCustomFields($object, $v);
                    break;
                case 'statuses_fields':
                    $statusesData = &$v;
                    foreach ($statusesData as $data) {
                        $this->checkRequiredKeys($data, array('parameters'));
                        $id = null;
                        if (array_key_exists('id', $data) && (int)$data['id']) {
                            $id = (int)$data['id'];
                        }
                        $object->addStatusField(
                            $data['parameters'],
                            $id
                        );
                    }
                    break;
                default:
                    $object[$k] = $v;
            }
        }
    }

    /**
     * Устанавливает дополнительные поля в объект
     *
     * @param object $object Объект для установки ему дополнительных полей
     * @param array $customFields Массив параметров дополнительных полей
     *
     * @throws \Exception
     */
    private function addCustomFields($object, $customFields)
    {
        if (!is_array($customFields)) {
            throw new \Exception('$customFields not valid. $customFields must be an array');
        }

        foreach ($customFields as $k => $data) {
            if (
                !array_key_exists('id', $data) ||
                !array_key_exists('value', $data)
            ) {
                throw new \Exception('Not valid $customFields array');
            }

            if (!array_key_exists('enum', $data) || !$data['enum']) {
                $enum = false;
            } else {
                $enum = true;
            }
            if (!array_key_exists('subtype', $data) || !$data['subtype']) {
                $subtype = false;
            } else {
                $subtype = true;
            }

            $object->addCustomField((int)$data['id'], $data['value'], $enum, $subtype);
        }
    }
}