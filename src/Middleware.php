<?php
/**
 * Created by PhpStorm.
 * User: ak
 * Date: 25.09.17
 * Time: 10:51
 */

namespace CleverLab\AmoCRM;

use AmoCRM\Exception;
use CleverLab\AmoCRM\Interfaces\iMiddleware;
use AmoCRM\Client;

class Middleware implements iMiddleware
{
    private $domain;

    private $login;

    private $apiKey;

    private $proxy;

    private $amo = null;

    /**
     * Middleware constructor.
     *
     * @param string $domain
     * @param string $login
     * @param string $apiKey
     * @param string|null $proxy
     */
    public function __construct($domain, $login, $apiKey, $proxy = null)
    {
        $this->domain = $domain;
        $this->login = $login;
        $this->apiKey = $apiKey;
        $this->proxy = $proxy;
    }

    /**
     * Get account information.
     * Equivalent to the method /private/api/v2/json/accounts/current
     *
     * @param bool $short
     * @param array $parameters
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
     * Get user by login
     *
     * @param string $login
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
     * Get contact list. Equivalent to the method contacts/list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     * @throws \Exception
     */
    public function getContacts($parameters, $modified = null)
    {
        if (!is_array($parameters)) {
            throw new \Exception('$parameters not valid. $parameters must be an array');
        }

        $amo = $this->getAmo();

        $res = $amo->contact->apiList($parameters, $modified);

        return $res;
    }

    /**
     * Add one contact
     *
     * @param $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addContact($parameters, $debug = false)
    {
        $amo = $this->getAmo();
        $contact = $amo->contact;

        if ($debug) {
            $contact->debug(true);
        }

        $this->setParameters($contact, $parameters);

        $id = $contact->apiAdd();

        return $id;
    }

    /**
     * Add group of contacts
     *
     * @param $contacts
     *
     * @return array
     * @throws \Exception
     */
    public function addGroupOfContact($contacts)
    {
        if (!is_array($contacts)) {
            throw new \Exception('$contacts not valid. $contacts must be an array');
        }

        $amo = $this->getAmo();

        $arrOfContacts = array();

        foreach ($contacts as $k => $v) {
            if (
                !is_array($v) ||
                !array_key_exists('parameters', $v)
            ) {
                throw new \Exception('List of contacts parameters not valid');
            }

            if (!array_key_exists('debug', $v) || !$v['debug']) {
                $debug = false;
            } else {
                $debug = true;
            }

            $contact = $amo->contact;
            if ($debug) {
                $contact->debug(true);
            }
            $this->setParameters($contact, $v['parameters']);

            $arrOfContacts[] = $contact;
        }

        if (!$arrOfContacts) {
            return array();
        }

        $ids = $amo->contact->apiAdd($arrOfContacts);

        return $ids;
    }

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
    public function updateContact($id, $parameters, $modified = 'now', $debug = false)
    {
        $amo = $this->getAmo();
        $contact = $amo->contact;

        if ($debug) {
            $contact->debug(true);
        }

        $this->setParameters($contact, $parameters);

        $res = $contact->apiUpdate((int)$id, $modified);

        return $res;
    }

    /**
     * Get links between leads and contacts
     *
     * @param array $parameters
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
     * Return object for work via library
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
     * Set parameters for object
     *
     * @param object $object
     * @param array $parameters
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
     * Add custom field to object
     *
     * @param object $object
     * @param array $customFields
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