<?php

namespace tests;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {
    public function testConfig(): void
    {
        $defaultConfig = [
            'secret' => 'secret#very_secret',
            'not_secret' => [
                0 => 'data1',
                1 => 'data2',
                2 => 'data3',
                'secret' => 'secret#very_secret',
            ],
        ];
        //забираем конфиг
        $config = require APP_ROOT . '/config/config.php';
        $this->assertSame($defaultConfig, (array) $config);
    }
}