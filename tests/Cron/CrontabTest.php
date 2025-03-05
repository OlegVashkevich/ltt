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
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $this->assertObjectHasProperty('root_path', $crontab);
        $this->assertObjectHasProperty('log_path', $crontab);
    }

    /**
     * @throws Exception
     */
    public function testAdd(): void
    {
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $crontab->addTask((new Task('task1.php'))->daily());
        $crontab->addTask(new Task('task2.php'));
        $crontab->addTask(new Task('task2.php'));
        $crontab->saveTasks();

        $crontab2 = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $tasks = $crontab2->tasks;
        foreach ($tasks as &$task) {
            $task = (string)$task;
        }
        $crontab2->removeTasks();
        $this->assertSame([
            '0 0 * * * '.PHP_BINARY.' task1.php',
            '* * * * * '.PHP_BINARY.' task2.php',
        ], $tasks);
    }

    /**
     * @throws Exception
     */
    public function testInit(): void
    {
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
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
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $crontab->init('bad_file_path');
    }

    /**
     * @throws Exception
     */
    public function testEmptyTasksException(): void
    {
        $this->expectExceptionMessage('Список задач пуст. Добавьте задачи и попробуйте еще раз.');
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $crontab->tasks = [];
        $crontab->saveTasks();
    }

    /**
     * @throws Exception
     */
    public function testEmptyCommandTasksException(): void
    {
        $this->expectExceptionMessage('Команда не должна быть пустой. Введите и попробуйте еще раз.');
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $crontab->tasks = [new Task('')];
        $crontab->saveTasks();
    }

    /**
     * @throws Exception
     */
    public function testShow(): void
    {
        $crontab = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $crontab->removeTasks();
        $crontab->addTask((new Task('task1.php'))->daily());
        $crontab->addTask(new Task('task2.php'));
        $crontab->addTask(new Task('task3.php'));
        $crontab->saveTasks();
        //echo $crontab->show();
        $out = PHP_EOL;
        $out .= '1 | 0 0 * * * '.PHP_BINARY.' task1.php'.PHP_EOL;
        $out .= '2 | * * * * * '.PHP_BINARY.' task2.php'.PHP_EOL;
        $out .= '3 | * * * * * '.PHP_BINARY.' task3.php'.PHP_EOL;
        $out .= PHP_EOL;
        $this->assertEquals($out, $crontab->show());
        $crontab->disable(2);
        //echo $crontab->show();
        $out = PHP_EOL;
        $out .= '1 | 0 0 * * * '.PHP_BINARY.' task1.php'.PHP_EOL;
        $out .= '2 | #off# * * * * * '.PHP_BINARY.' task2.php'.PHP_EOL;
        $out .= '3 | * * * * * '.PHP_BINARY.' task3.php'.PHP_EOL;
        $out .= PHP_EOL;
        $crontab->saveTasks();
        $this->assertEquals($out, $crontab->show());
        $crontab_with_off = new Crontab(__DIR__.'/../..', __DIR__.'/../../log', 'TEST');
        $crontab->enable(2);
        //echo $crontab->show();
        $out = PHP_EOL;
        $out .= '1 | 0 0 * * * '.PHP_BINARY.' task1.php'.PHP_EOL;
        $out .= '2 | * * * * * '.PHP_BINARY.' task2.php'.PHP_EOL;
        $out .= '3 | * * * * * '.PHP_BINARY.' task3.php'.PHP_EOL;
        $out .= PHP_EOL;
        $this->assertEquals($out, $crontab->show());
        $crontab->removeTasks();

        $tasks = $crontab_with_off->tasks;
        foreach ($tasks as &$task) {
            $task = (string)$task;
        }
        $this->assertSame([
            '0 0 * * * '.PHP_BINARY.' task1.php',
            '#off# * * * * * '.PHP_BINARY.' task2.php',
            '* * * * * '.PHP_BINARY.' task3.php',
        ], $tasks);
    }
}