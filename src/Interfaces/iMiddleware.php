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
     * Get account information
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
     * Get contact list
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
     * @param array $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addContact($parameters, $debug = false);

    /**
     * Add group of contacts
     *
     * @param array $contacts
     * @param bool $debug
     *
     * @return array
     */
    public function addGroupOfContact($contacts, $debug = false);

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

    /**
     * Get lead list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getLeads($parameters, $modified = null);

    /**
     * Add one lead
     *
     * @param array $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addLead($parameters, $debug = false);

    /**
     * Add group of contacts
     *
     * @param array $contacts
     * @param bool $debug
     *
     * @return array
     */
    public function addGroupOfLead($contacts, $debug = false);

    /**
     * Update lead
     *
     * @param int $id
     * @param array $parameters
     * @param string $modified
     * @param bool $debug
     *
     * @return bool
     */
    public function updateLead($id, $parameters, $modified = 'now', $debug = false);

    /**
     * Get company list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getCompanies($parameters, $modified = null);

    /**
     * Add one company
     *
     * @param array $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addCompany($parameters, $debug = false);

    /**
     * Add group of companies
     *
     * @param array $contacts
     * @param bool $debug
     *
     * @return array
     */
    public function addGroupOfCompanies($contacts, $debug = false);

    /**
     * Update company
     *
     * @param int $id
     * @param array $parameters
     * @param string $modified
     * @param bool $debug
     *
     * @return bool
     */
    public function updateCompany($id, $parameters, $modified = 'now', $debug = false);

    /**
     * Get customer list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getCustomers($parameters, $modified = null);

    /**
     * Add one customer
     *
     * @param array $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addCustomer($parameters, $debug = false);

    /**
     * Add group of companies
     *
     * @param array $contacts
     * @param bool $debug
     *
     * @return array
     */
    public function addGroupOfCustomers($contacts, $debug = false);

    /**
     * Update customer
     *
     * @param int $id
     * @param array $parameters
     * @param string $modified
     * @param bool $debug
     *
     * @return bool
     */
    public function updateCustomer($id, $parameters, $modified = 'now', $debug = false);

    /**
     * Get transaction list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getTransactions($parameters, $modified = null);

    /**
     * Add one transaction
     *
     * @param array $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addTransaction($parameters, $debug = false);

    /**
     * Add group of transactions
     *
     * @param array $contacts
     * @param bool $debug
     *
     * @return array
     */
    public function addGroupOfTransactions($contacts, $debug = false);
}