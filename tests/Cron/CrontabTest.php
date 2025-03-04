<?php

namespace Tests\Cron;

use LTT\Cron\Crontab;
use LTT\Cron\Task;
use PHPUnit\Framework\TestCase;

class CrontabTest extends TestCase {
    public function testConstructor(): void {
        $crontab = new Crontab('root_path','root_path/log');
        $this->assertObjectHasProperty('root_path', $crontab);
        $this->assertObjectHasProperty('log_path', $crontab);
    }
    public function testAdd(): void {
        $crontab = new Crontab('root_path','root_path/log');
        $crontab->addTask(new Task('task1.php'));
        $crontab->addTask(new Task('task2.php'));
        $crontab->saveTasks();
        $tasks = $crontab->getTasks();
        foreach ($tasks as &$task) {
            $task = (string) $task;
        }
        print_r($tasks);
        $this->assertSame([
            '* * * * * ' . PHP_BINARY . ' task1.php',
            '* * * * * ' . PHP_BINARY . ' task2.php',
        ], $tasks);
        $crontab->removeTasks();
    }
}