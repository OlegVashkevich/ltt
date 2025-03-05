<?php

namespace Tests\Cron;

use Exception;
use LTT\Cron\Crontab;
use LTT\Cron\Task;
use PHPUnit\Framework\TestCase;

class CrontabTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructor(): void
    {
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log');
        $this->assertObjectHasProperty('root_path', $crontab);
        $this->assertObjectHasProperty('log_path', $crontab);
    }

    /**
     * @throws Exception
     */
    public function testAdd(): void
    {
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log');
        $crontab->addTask((new Task('task1.php'))->daily());
        $crontab->addTask(new Task('task2.php'));
        $crontab->addTask(new Task('task2.php'), true);
        $crontab->saveTasks();

        $crontab2 = new Crontab(__DIR__.'/../..', __DIR__.'/../../log');
        $tasks = $crontab2->tasks;
        foreach ($tasks as &$task) {
            $task = (string)$task;
        }
        $this->assertSame([
            '0 0 * * * '.PHP_BINARY.' task1.php',
            '* * * * * '.PHP_BINARY.' task2.php',
        ], $tasks);
        $crontab2->removeTasks();
    }

    /**
     * @throws Exception
     */
    public function testInit(): void
    {
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log');
        $crontab->init();
        $crontab->removeTasks();
        $this->expectNotToPerformAssertions();
    }

    /**
     * @throws Exception
     */
    public function testInitException(): void
    {
        $this->expectExceptionMessage('Файла с расписанием не существует.');
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log');
        $crontab->init('bad_file_path');
    }

    /**
     * @throws Exception
     */
    public function testEmptyTasksException(): void
    {
        $this->expectExceptionMessage('Список задач пуст. Добавьте задачи и попробуйте еще раз.');
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log');
        $crontab->tasks = [];
        $crontab->saveTasks();
    }

    /**
     * @throws Exception
     */
    public function testEmptyCommandTasksException(): void
    {
        $this->expectExceptionMessage('Команда не должна быть пустой. Введите и попробуйте еще раз.');
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log');
        $crontab->tasks = [new Task('')];
        $crontab->saveTasks();
    }
}