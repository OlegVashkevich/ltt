<?php

use LTT\Cron\Crontab;
use LTT\Cron\Task;

require __DIR__.'/../vendor/autoload.php';

try {
    $crontab = new Crontab(__DIR__.'/..', __DIR__.'/../log');
    $crontab->addTask((new Task('echo "Hello World"'))->daily());
    $crontab->addTask((new Task('echo "Hello World2"'))->hourly());
    $crontab->addTask((new Task('echo "Hello World3"'))->everyThirtyMinutes());
    $crontab->addTask((new Task('echo "Hello World4"'))->hourlyAt([1, 3, 6]));
    $crontab->addTask((new Task('echo "Hello World5"'))->twiceMonthly(7, 17, '19:30'));
    $crontab->saveTasks();
} catch (Exception $e) {
    print_r($e->getMessage());
}
