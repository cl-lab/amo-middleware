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
    public function getAccount($short, $parameters);
}