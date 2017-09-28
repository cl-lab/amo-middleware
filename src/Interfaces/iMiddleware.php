<?php
/**
 * Created by PhpStorm.
 * User: ak
 * Date: 25.09.17
 * Time: 11:12
 */

namespace CleverLab\AmoCRM\Interfaces;

interface iMiddleware
{
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
    public function getAccount($short = false, $parameters = array());

    /**
     * Возвращает сведения о пользователе по его логину.
     * Если не указывать логин, вернутся сведения о владельце API ключа.
     *
     * @param string $login Логин пользователя
     *
     * @return mixed Данные о пользователе
     */
    public function getUserByLogin($login);

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
    public function getContacts($parameters, $modified = null);

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
    public function addContact($parameters, $debug = false);

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
    public function addGroupOfContacts($dataList, $debug = false);

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
    public function updateContact($id, $parameters, $modified = 'now', $debug = false);

    /**
     * Возвращает связи между сделками и контактами
     *
     * @link https://developers.amocrm.ru/rest_api/contacts_links.php
     *
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array Ответ amoCRM API
     */
    public function getContactLinks($parameters);

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
    public function getLeads($parameters, $modified = null);

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
    public function addLead($parameters, $debug = false);

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
    public function addGroupOfLeads($dataList, $debug = false);

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
    public function updateLead($id, $parameters, $modified = 'now', $debug = false);

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
    public function getCompanies($parameters, $modified = null);

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
    public function addCompany($parameters, $debug = false);

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
    public function addGroupOfCompanies($dataList, $debug = false);

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
    public function updateCompany($id, $parameters, $modified = 'now', $debug = false);

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
    public function getCustomers($parameters, $modified = null);

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
    public function addCustomer($parameters, $debug = false);

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
    public function addGroupOfCustomers($dataList, $debug = false);

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
    public function updateCustomer($id, $parameters, $debug = false);

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
    public function getTransactions($parameters, $modified = null);

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
    public function addTransaction($parameters, $debug = false);

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
    public function addGroupOfTransactions($dataList, $debug = false);

    /**
     * Удаляет транзакцию
     *
     * @link https://developers.amocrm.ru/rest_api/transactions/set.php
     *
     * @param int $id Идентификатор транзакции
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteTransaction($id);

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
    public function getTasks($parameters, $modified = null);

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
    public function addTask($parameters, $debug = false);

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
    public function addGroupOfTasks($dataList, $debug = false);

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
    public function updateTask($id, $text, $modified = 'now', $debug = false);

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
    public function getNotes($parameters, $modified = null);

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
    public function addNote($parameters, $debug = false);

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
    public function addGroupOfNotes($dataList, $debug = false);

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
    public function updateNote($id, $parameters, $modified = 'now', $debug = false);

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
    public function addCustomField($parameters, $debug = false);

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
    public function addGroupOfCustomFields($dataList, $debug = false);

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
    public function deleteCustomField($id, $origin, $debug = false);

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
     */
    public function addCall($code, $key, $parameters, $debug = false);

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
     */
    public function addGroupOfCalls($code, $key, $dataList, $debug = false);

    /**
     * Возвращает список неразобранного
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/list.php
     *
     * @param array $parameters Массив параметров для выборки объектов
     *
     * @return array Ответ amoCRM API
     */
    public function getUnsorted($parameters = array());

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
    public function acceptUnsorted($uids, $userId, $statusId = null);

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
    public function declineUnsorted($uids, $userId);

    /**
     * Агрегирование неразобранных заявок
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/get_all_summary.php
     *
     * @return array Ответ amoCRM API
     */
    public function summaryUnsorted();

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
    );

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
    );

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
    );

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
    );

    /**
     * Список Webhooks
     *
     * @link https://developers.amocrm.ru/rest_api/webhooks/list.php
     *
     * @return array Ответ amoCRM API
     */
    public function getWebhooks();

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
    public function webhooksSubscribe($url = null, $events = array());

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
    public function webhooksUnsubscribe($url = null, $events = array());

    /**
     * Список воронок и этапов продаж
     *
     * @link https://developers.amocrm.ru/rest_api/pipelines/list.php
     *
     * @param null|int $id Уникальный идентификатор воронки
     *
     * @return array Ответ amoCRM API
     */
    public function getPipelines($id = null);

    /**
     * Обновление воронок и этапов продаж
     *
     * @param int $id Идентификатор этапа(воронки)
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function updatePipeline($id, $parameters, $debug = false);

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
    public function deletePipeline($id);

    /**
     * Возвращает список периодов покупателя
     *
     * @link https://developers.amocrm.ru/rest_api/customers_periods/list.php
     *
     * @return array Ответ amoCRM API
     */
    public function getCustomersPeriods();

    /**
     * Добавляет период покупателя
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор периода
     */
    public function addCustomerPeriod($parameters, $debug = false);

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
     */
    public function setCustomerPeriod($dataList, $debug = false);

    /**
     * Возвращает список виджетов.
     *
     * @link https://developers.amocrm.ru/rest_api/unsorted/list.php
     *
     * @param array $parameters Массив параметров для выборки объектов
     *
     * @return array Ответ amoCRM API
     */
    public function getWidgets($parameters = array());

    /**
     * Включение виджетов
     *
     * @param $parameters Массив параметров для включения виджета
     *
     * @link https://developers.amocrm.ru/rest_api/widgets/set.php
     *
     * @return array Ответ amoCRM API
     */
    public function widgetInstall($parameters);

    /**
     * Выключение виджетов
     *
     * @param $parameters Массив параметров для выключения виджета
     *
     * @link https://developers.amocrm.ru/rest_api/widgets/set.php
     *
     * @return array Ответ amoCRM API
     */
    public function widgetUninstall($parameters);

    /**
     * Список каталогов
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/list.php
     *
     * @param null|int $id Идентификатор каталога
     *
     * @return array Ответ amoCRM API
     */
    public function getCatalogs($id = null);

    /**
     * Добавляет каталог
     *
     * link https://developers.amocrm.ru/rest_api/catalogs/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор каталога
     */
    public function addCatalog($parameters, $debug = false);

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
    public function addGroupOfCatalogs($dataList, $debug = false);

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
    public function updateCatalog($id, $parameters, $debug = false);

    /**
     * Удаляет каталог
     *
     * @link https://developers.amocrm.ru/rest_api/catalogs/set.php
     *
     * @param int $id Идентификатор каталога
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteCatalog($id);

    /**
     * Возвращает список элементов каталога.
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array Ответ amoCRM API
     */
    public function getCatalogElements($parameters = array());

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
    public function addCatalogElement($parameters, $debug = false);

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
    public function addGroupOfCatalogElements($dataList, $debug = false);

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
    public function updateCatalogElement($id, $parameters, $debug = false);

    /**
     * Удаляет элемент каталога
     *
     * @link https://developers.amocrm.ru/rest_api/catalog_elements/set.php
     *
     * @param int $id Идентификатор элемента каталога
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteCatalogElement($id);

    /**
     * Возвращает список ссылок.
     *
     * @link https://developers.amocrm.ru/rest_api/links/list.php
     *
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array Ответ amoCRM API
     */
    public function getLinks($parameters);

    /**
     * Устанавливает связь между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function addLink($parameters, $debug = false);

    /**
     * Групповое добавление связей между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function addGroupOfLinks($dataList, $debug = false);

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
    public function deleteLink($parameters, $debug = false);

    /**
     * Групповое разрывание связей между сущностями
     *
     * @link https://developers.amocrm.ru/rest_api/links/set.php
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteGroupOfLinks($dataList, $debug = false);
}