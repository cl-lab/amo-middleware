<?php
/**
 * Пример работы с аккаунтом
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CleverLab\AmoCRM\Middleware;

$domain = 'SUBDOMAIN';
$login = 'LOGIN';
$apiKey = 'HASH';

$amo = new Middleware($domain, $login, $apiKey);

// Получение информации пао аккаунту
$result = $amo->getAccount();

print_r($result);