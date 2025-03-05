<?php

namespace Tests\Cron;

use Exception;
use LTT\Cron\Crontab;
use LTT\Cron\Task;
use PHPUnit\Framework\TestCase;

class CrontabTest extends TestCase
{
    public function testConstructor(): void
    {
        $crontab = new Crontab('root_path', 'root_path/log');
        $this->assertObjectHasProperty('root_path', $crontab);
        $this->assertObjectHasProperty('log_path', $crontab);
    }

    /**
     * @throws Exception
     */
    public function testAdd(): void
    {
        $crontab = new Crontab('root_path', 'root_path/log');
        $crontab->addTask((new Task('task1.php'))->daily());
        $crontab->addTask(new Task('task2.php'));
        $crontab->addTask(new Task('task2.php'), true);
        $crontab->saveTasks();
        $tasks = $crontab->tasks;
        foreach ($tasks as &$task) {
            $task = (string)$task;
        }
        $this->assertSame([
            '0 0 * * * '.PHP_BINARY.' task1.php',
            '* * * * * '.PHP_BINARY.' task2.php',
        ], $tasks);
        $crontab->removeTasks();
    }
}