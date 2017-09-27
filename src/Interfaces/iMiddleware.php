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
     * @param bool $short Краткий формат, только основные поля
     * @param array $parameters Ассоциативный массив параметров к amoCRM API
     *
     * @return array
     */
    public function getAccount($short = false, $parameters = array());

    /**
     * Возвращает сведения о пользователе по его логину.
     * Если не указывать логин, вернутся сведения о владельце API ключа.
     *
     * @param string $login Логин пользователя
     *
     * @return mixed
     */
    public function getUserByLogin($login);

    /**
     * Возвращает список контактов.
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getContacts($parameters, $modified = null);

    /**
     * Добавляет контакт
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
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов контактов
     */
    public function addGroupOfContacts($dataList, $debug = false);

    /**
     * Обновляет контакт
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
     * @param array $parameters Ассоциативный массив параметров
     *
     * @return array
     */
    public function getContactLinks($parameters);

    /**
     * Возвращает список сделок
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getLeads($parameters, $modified = null);

    /**
     * Добавляет сделку
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
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов сделок
     */
    public function addGroupOfLeads($dataList, $debug = false);

    /**
     * Обновляет сделку
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
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getCompanies($parameters, $modified = null);

    /**
     * Добавляет компанию
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
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов компаний
     */
    public function addGroupOfCompanies($dataList, $debug = false);

    /**
     * Обновляет компанию
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
     * Возвращает список покупателей
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getCustomers($parameters, $modified = null);

    /**
     * Добавляет покупателя
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
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов покупателей
     */
    public function addGroupOfCustomers($dataList, $debug = false);

    /**
     * Обновляет покупателя
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
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getTransactions($parameters, $modified = null);

    /**
     * Добавляет транзакцию
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
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов транзакций
     */
    public function addGroupOfTransactions($dataList, $debug = false);

    /**
     * Удаляет транзакцию
     *
     * @param int $id Идентификатор транзакции
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteTransaction($id);

    /**
     * Возвращает список задач
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getTasks($parameters, $modified = null);

    /**
     * Добавляет задачу
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
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов задач
     */
    public function addGroupOfTasks($dataList, $debug = false);

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
    public function updateTask($id, $text, $modified = 'now', $debug = false);

    /**
     * Возвращает список примечаний.
     *
     * @param array $parameters Ассоциативный массив параметров
     * @param null|string $modified Дополнительная фильтрация по (изменено с)
     *
     * @return array
     */
    public function getNotes($parameters, $modified = null);

    /**
     * Добавляет примечание
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
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array Массив уникальных идентификаторов примечаний
     */
    public function addGroupOfNotes($dataList, $debug = false);

    /**
     * Обновление примечаний
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
     * @param array $parameters Ассоциативный массив параметров
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int Уникальный идентификатор поля
     */
    public function addCustomField($parameters, $debug = false);

    /**
     * Групповое добавление дополнительных полей
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
     * @param int $id Идентификатор дополнительного поля
     * @param string $origin Уникальный идентификатор сервиса заданный при создании параметром origin
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool Флаг успешности выполнения запроса
     */
    public function deleteCustomField($id, $origin, $debug = false);

    /**
     * Добавляет один звонок
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
     * Добавляет звонки группой
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
}