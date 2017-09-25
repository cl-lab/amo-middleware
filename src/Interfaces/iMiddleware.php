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
     * Get account information.
     *
     * @param bool $short
     * @param array $parameters
     *
     * @return array
     */
    public function getAccount($short = false, $parameters = array());

    /**
     * Get user by login
     *
     * @param string $login
     *
     * @return mixed
     */
    public function getUserByLogin($login);

    /**
     * Get contact list.
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getContacts($parameters, $modified = null);

    /**
     * Add one contact
     *
     * @param $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addContact($parameters, $debug = false);

    /**
     * Add group of contacts
     *
     * @param $contacts
     *
     * @return array
     */
    public function addGroupOfContact($contacts);

    /**
     * Update contact
     *
     * @param int $id
     * @param array $parameters
     * @param string $modified
     * @param bool $debug
     *
     * @return bool
     */
    public function updateContact($id, $parameters, $modified = 'now', $debug = false);

    /**
     * Get links between leads and contacts
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getContactLinks($parameters);
}