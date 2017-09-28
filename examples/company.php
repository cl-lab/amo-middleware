<?php
/**
 * Пример работы с компанией
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CleverLab\AmoCRM\Middleware;

$domain = 'cleversolutionsru';
$login = 'elchin.mustafaev@cl-lab.ru';
$apiKey = '6fd135128268fba2f7fc9fe396bb0291';

$amo = new Middleware($domain, $login, $apiKey);

// Получение списка сделок
$parameters = array(
    'limit_rows' => 10,
    'limit_offset' => 0,
);
$result = $amo->getCompanies($parameters);

print_r($result);