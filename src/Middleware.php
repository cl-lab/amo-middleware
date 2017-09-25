<?php
/**
 * Created by PhpStorm.
 * User: ak
 * Date: 25.09.17
 * Time: 10:51
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
        $amo = $this->getAmo();

        $res = $amo->contact->apiList($parameters, $modified);

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
}