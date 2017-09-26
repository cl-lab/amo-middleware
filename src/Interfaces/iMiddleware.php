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
     * @return int
     */
    public function addContact($parameters, $debug = false);

    /**
     * Групповое добавление контактов
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfContacts($dataList, $debug = false);

    /**
     * Обновляет контакт
     *
     * @param int $id Идентификатор контакта
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
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
     * @return int
     */
    public function addLead($parameters, $debug = false);

    /**
     * Групповое добавление сделок
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfLeads($dataList, $debug = false);

    /**
     * Обновляет сделку
     *
     * @param int $id Идентификатор сделки
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
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
     * @return int
     */
    public function addCompany($parameters, $debug = false);

    /**
     * Групповое добавление компаний
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfCompanies($dataList, $debug = false);

    /**
     * Обновляет компанию
     *
     * @param int $id Идентификатор компании
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
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
     * @return int
     */
    public function addCustomer($parameters, $debug = false);

    /**
     * Групповое добавление покупателей
     *
     * @param array $dataList Список массивов содержащих параметры
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfCustomers($dataList, $debug = false);

    /**
     * Обновляет покупателя
     *
     * @param int $id Идентификатор покупателя
     * @param array $parameters Ассоциативный массив параметров
     * @param string $modified Дополнительная фильтрация по (изменено с)
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
     */
    public function updateCustomer($id, $parameters, $modified = 'now', $debug = false);

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
     * Add one transaction
     *
     * @param array $parameters
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int
     */
    public function addTransaction($parameters, $debug = false);

    /**
     * Add group of transactions
     *
     * @param array $dataList
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfTransactions($dataList, $debug = false);

    /**
     * Get task list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getTasks($parameters, $modified = null);

    /**
     * Add one task
     *
     * @param array $parameters
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int
     */
    public function addTask($parameters, $debug = false);

    /**
     * Add group of tasks
     *
     * @param array $dataList
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfTasks($dataList, $debug = false);

    /**
     * Update task
     *
     * @param int $id
     * @param array $text
     * @param string $modified
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
     */
    public function updateTask($id, $text, $modified = 'now', $debug = false);

    /**
     * Get note list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getNotes($parameters, $modified = null);

    /**
     * Add one note
     *
     * @param array $parameters
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int
     */
    public function addNote($parameters, $debug = false);

    /**
     * Add group of notes
     *
     * @param array $dataList
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfNotes($dataList, $debug = false);

    /**
     * Update note
     *
     * @param int $id
     * @param array $parameters
     * @param string $modified
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
     */
    public function updateNote($id, $parameters, $modified = 'now', $debug = false);

    /**
     * Add one custom field
     *
     * @param array $parameters
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return int
     */
    public function addCustomField($parameters, $debug = false);

    /**
     * Add group of custom fields
     *
     * @param array $dataList
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return array
     */
    public function addGroupOfCustomFields($dataList, $debug = false);

    /**
     * Delete custom field
     *
     * @param int $id
     * @param string $origin
     * @param bool $debug Флаг определяющий режим отладки. Если true, то будет включена отладка
     *
     * @return bool
     */
    public function deleteCustomField($id, $origin, $debug = false);
}