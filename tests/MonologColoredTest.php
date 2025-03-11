<?php

namespace Tests;

use OlegV\Logdye;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Tests\Helper\Intercept;

class MonologColoredTest extends TestCase
{
    public function testMonologColored(): void
    {
        $states = [
            'Debug' => '[0;37mDEBUG[0m',
            'Info' => '[1;34mINFO[0m',
            'Notice' => '[1;32mNOTICE[0m',
            'Warning' => '[0;33mWARNING[0m',
            'Error' => '[1;33mERROR[0m',
            'Critical' => '[0;31;51mCRITICAL[0m',
            'Alert' => '[1;30;41mALERT[0m',
            'Emergency' => '[1;5;21;41mEMERGENCY[0m',
        ];
        stream_filter_register("intercept", Intercept::class);
        Intercept::$cache = '';
        // Создаем экземпляр логгера
        $logger = new Logger('test');

        $formatter = new Logdye(
            "%level_name%",
            "",
        );

        // Добавляем обработчик для вывода логов в стандартный поток вывода
        $handler = new StreamHandler("php://stdout", Level::Debug);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
        $logger->warning('just activate');
        //start stream
        $stderr = $handler->getStream();
        if (is_resource($stderr)) {
            stream_filter_append($stderr, "intercept");
        }

        $logger->Debug('test');
        $logger->Info('test');
        $logger->Notice('test');
        $logger->Warning('test');
        $logger->Error('test');
        $logger->Critical('test');
        $logger->Alert('test');
        $logger->Emergency('test');
        //end stream
        $this->assertSame(Intercept::$cache, implode('', $states));
    }
}