<?php

use LTT\Cron\Crontab;
use LTT\Cron\Task;

require __DIR__.'/../vendor/autoload.php';

try {
    $crontab = new Crontab(__DIR__.'/..', __DIR__.'/../log');
    $crontab->addTask((new Task('echo "Hello World"'))->daily());
    $crontab->saveTasks();
} catch (Exception $e) {
    print_r($e->getMessage());
}
