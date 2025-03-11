<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use SensitiveParameterValue;

class ConfigTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $defaultConfig = [
            'secret' => new SensitiveParameterValue('very_secret'),
            'not_secret' => [
                0 => 'data1',
                1 => 'data2',
                2 => 'data3',
                'secret' => new SensitiveParameterValue('very_secret'),
            ],
        ];
        //забираем конфиг
        $config = require APP_ROOT.'/config/config.php';
        $this->assertEquals($defaultConfig, (array)$config);
    }

    public function testGetSecret(): void
    {
        $defaultSecret = 'your_secret_key_here2';
        //забираем конфиг
        $config = require APP_ROOT.'/config/config.php';
        $secret_lvl1 = $config['secret']->getValue();
        $this->assertSame($defaultSecret, $secret_lvl1);
    }
}