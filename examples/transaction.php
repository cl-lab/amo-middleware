<?php
/**
 * Пример работы с транзакциями
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CleverLab\AmoCRM\Middleware;

$domain = 'SUBDOMAIN';
$login = 'LOGIN';
$apiKey = 'HASH';

$amo = new Middleware($domain, $login, $apiKey);

// Получение списка сделок
$parameters = array(
    'limit_rows' => 10,
    'limit_offset' => 0,
);
$result = $amo->getTransactions($parameters);

print_r($result);