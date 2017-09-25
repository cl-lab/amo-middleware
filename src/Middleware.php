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
     */
    public function getContacts($parameters, $modified = null)
    {
        return $this->getObjects('contact', $parameters, $modified);
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
        return $this->addObject('contact', $parameters, $debug);
    }

    /**
     * Add group of contacts
     *
     * @param array $contacts
     * @param bool $debug
     *
     * @return array
     */
    public function addGroupOfContact($contacts, $debug = false)
    {
        return $this->addGroupOfObject('contact', $contacts, $debug);
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
        return $this->updateObject('contact', $id, $parameters, $modified, $debug);
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
     * Get lead list. Equivalent to the method leads/list
     *
     * @param array $parameters
     * @param null|string $modified
     *
     * @return array
     */
    public function getLeads($parameters, $modified = null)
    {
        return $this->getObjects('lead', $parameters, $modified);
    }

    /**
     * Add one lead
     *
     * @param $parameters
     * @param bool $debug
     *
     * @return int
     */
    public function addLead($parameters, $debug = false)
    {
        return $this->addObject('lead', $parameters, $debug);
    }

    /**
     * Add group of contacts
     *
     * @param array $contacts
     * @param bool $debug
     *
     * @return array
     */
    public function addGroupOfLead($contacts, $debug = false)
    {
        return $this->addGroupOfObject('lead', $contacts, $debug);
    }

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
    public function updateLead($id, $parameters, $modified = 'now', $debug = false)
    {
        return $this->updateObject('lead', $id, $parameters, $modified, $debug);
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
     * Get object list
     *
     * @param array $parameters
     * @param null|string $modified
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
     * Add one object
     *
     * @param string $type
     * @param array $parameters
     * @param bool $debug
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
     * Add group of objects
     *
     * @param string $type
     * @param array $objects
     * @param bool $debug
     *
     * @return array
     * @throws \Exception
     */
    private function addGroupOfObject($type, $objects, $debug = false)
    {
        if (!is_array($objects)) {
            throw new \Exception('$objects not valid. $objects must be an array');
        }

        $amo = $this->getAmo();

        $arrOfObjects = array();

        foreach ($objects as $k => $v) {
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
     * Update object
     *
     * @param string $type
     * @param int $id
     * @param array $parameters
     * @param string $modified
     * @param bool $debug
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