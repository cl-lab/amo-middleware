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
     * Эквивалентно методу /private/api/v2/json/accounts/current
     *
     * @param bool $short Краткий формат, только основные поля
     * @param array $parameters Ассоциативный массив параметров к amoCRM API
     *
     * @return array
     */
    public function getAccount($short = false, $parameters = array())
    {
        $amo = $this->getAmo();

        $res = $amo->account->apiCurrent($short, $parameters);

        return $res;
    }

    /**
     * Возвращает сведения о пользователе по его логину.
     * Если не указывать логин, вернутся сведения о владельце API ключа.
     *
     * @param string $login Логин пользователя
     *
     * @return mixed
     */
    public function getUserByLogin($login)
    {
        $amo = $this->getAmo();

        $res = $amo->account->getUserByLogin($login);

        return $res;
    }

    /**
     * Возвращает список контактов.
     * Эквивалентно методу contacts/list
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getContacts($parameters, $modified = null)
    {
        return $this->getObjects('contact', $parameters, $modified);
    }

    /**
     * Добавляет контакт
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
     * @param int $id Идентификатор контакта
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
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
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array
     * @throws \Exception
     */
    public function getContactLinks($parameters)
    {
        if (!is_array($parameters)) {
            throw new \Exception('$parameters not valid. $parameters must be an array');
        }

        $amo = $this->getAmo();

        $res = $amo->contact->apiLinks($parameters);

        return $res;
    }

    /**
     * Возвращает список сделок
     * Эквивалентно методу leads/list
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getLeads($parameters, $modified = null)
    {
        return $this->getObjects('lead', $parameters, $modified);
    }

    /**
     * Добавляет сделку
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
     * @param int $id Идентификатор сделки
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
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
     * Эквивалентно методу company/list
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getCompanies($parameters, $modified = null)
    {
        return $this->getObjects('company', $parameters, $modified);
    }

    /**
     * Добавляет компанию
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
     * @param int $id Идентификатор компании
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
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
     * Эквивалентно customers/list
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getCustomers($parameters, $modified = null)
    {
        return $this->getObjects('customer', $parameters, $modified);
    }

    /**
     * Добавляет покупателя
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
     * @param int $id Идентификатор покупателя
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updateCustomer($id, $parameters, $modified = 'now', $debug = false)
    {
        return $this->updateObject('customer', $id, $parameters, $modified, $debug);
    }

    /**
     * Возвращает список транзакций.
     * Эквивалентно методу transactions/list
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getTransactions($parameters, $modified = null)
    {
        return $this->getObjects('transaction', $parameters, $modified);
    }

    /**
     * Добавляет транзакцию
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
     * @param int $id Идентификатор транзакции
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteTransaction($id)
    {
        $amo = $this->getAmo();

        $res = $amo->transaction->apiDelete((int)$id);

        return $res;
    }

    /**
     * Возвращает список задач.
     * Эквивалентно методу tasks/list
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getTasks($parameters, $modified = null)
    {
        return $this->getObjects('task', $parameters, $modified);
    }

    /**
     * Добавляет задачу
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
     * Эквивалентно методу notes/list
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getNotes($parameters, $modified = null)
    {
        return $this->getObjects('note', $parameters, $modified);
    }

    /**
     * Добавляет примечание
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
     * @param int $id Идентификатор примечания
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
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

        $res = $field->apiDelete($id, $origin);

        return $res;
    }

    /**
     * Добавляет один звонок. Эквивалентно методу /api/calls/add/
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

        $res = $call->apiAdd($code, $key);

        return $res;
    }

    /**
     * Добавляет звонки группой
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

        $res = $call->apiAdd($code, $key, $arrOfCall);

        return $res;
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

        $res = $amo->unsorted->apiAccept($uids, $userId);

        return $res;
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

        $res = $amo->unsorted->apiDecline($uids, $userId);

        return $res;
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
     * @return array
     * @throws \Exception
     */
    private function getObjects($type, $parameters, $modified = null)
    {
        if (!is_array($parameters)) {
            throw new \Exception('$parameters not valid. $parameters must be an array');
        }

        $amo = $this->getAmo();

        $res = $amo->{$type}->apiList($parameters, $modified);

        return $res;
    }

    /**
     * Добавляет один объект указанного типа
     *
     * @param string $type Тип объекта
     * @param array $parameters Ассоциативный массив параметров для объекта
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int
     */
    private function addObject($type, $parameters, $debug = false)
    {
        $amo = $this->getAmo();
        $contact = $amo->{$type};

        if ($debug) {
            $contact->debug(true);
        }

        $this->setParameters($contact, $parameters);

        $id = $contact->apiAdd();

        return $id;
    }

    /**
     * Добавляет группу объектов указанного типа
     *
     * @param string $type
     * @param array $dataList
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
     * @param string $type
     * @param int $id
     * @param array $parameters
     * @param string $modified Дополнительная фильтрация по (изменено с)
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
     */
    private function updateObject($type, $id, $parameters, $modified = 'now', $debug = false)
    {
        $amo = $this->getAmo();
        $object = $amo->{$type};

        if ($debug) {
            $object->debug(true);
        }

        $this->setParameters($object, $parameters);

        $res = $object->apiUpdate((int)$id, $modified);

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
     * @param $parameters
     * @param $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
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
            if ('custom_fields' == $k) {
                $this->addCustomFields($object, $v);
            } else {
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