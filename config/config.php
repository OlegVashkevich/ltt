<?php

use LTT\Config;

$secret = require SECRET_PATH;

$arData = [
    'secret' => $secret['very_secret'],
    'not_secret' => [
        'data1',
        'data2',
        'data3',
        'secret' => $secret['very_secret'],
    ],
];
//возвращаем файл конфигурации
return new Config(SECRET_PATH, $arData);