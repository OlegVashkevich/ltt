<?php

namespace LTT\Cron;

use Exception;

class Crontab
{
    private const TASKS_BLOCK_START = '#~~~ APP_TASKS START ~~~';
    private const TASKS_BLOCK_END = '#~~~ APP_TASKS END ~~~';
    private const HIDDEN_TASK_COMMENT = '#~~~ APP_TASKS SYSTEM';

    /**
     * @var list<Task>
     */
    public array $tasks = [];
    /**
     * @var list<Task>
     */
    private array $hidden_tasks = [];

    private string $content = '';

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly string $root_path,
        private readonly string $log_path,
    ) {
        $this->getTasks();
    }

    /**
     * Создает скрытую задачу для обновления списка задач по расписанию
     *
     * @param  string  $schedule_path
     * @return void
     * @throws Exception
     */
    public function init(string $schedule_path = 'config/schedule.php'): void
    {
        $path = $this->root_path.'/'.$schedule_path;
        if (file_exists($path)) {
            $this->addTask((new Task($path.' '.self::HIDDEN_TASK_COMMENT.' init'))->hourly(), true);
            if (count($this->tasks) > 0) {
                $this->saveTasks();
            }
        } else {
            throw new Exception('Файла с расписанием не существует.');
        }
    }

    public function rebuild(): void
    {
        foreach ($this->hidden_tasks as $task) {
            if (str_contains($task->command, self::HIDDEN_TASK_COMMENT.' init')) {
                $path = str_replace(' '.self::HIDDEN_TASK_COMMENT.' init', '', $task->command);
            }
        }
        if (isset($path) && file_exists($path)) {
            exec(PHP_BINARY.' '.$path);
        }
    }

    /**
     * Выводит список задач
     *
     * @return string
     */
    public function show(): string
    {
        $out = PHP_EOL;
        foreach ($this->tasks as $num => $task) {
            $out .= ($num + 1).' | '.$task.PHP_EOL;
        }
        $out .= PHP_EOL;
        return (string)$out;
    }

    /**
     * Выключает задачу по ее номеру
     *
     * @param  int  $num
     * @return void
     */
    public function disable(int $num): void
    {
        $num = $num - 1;
        if (isset($this->tasks[$num])) {
            $this->tasks[$num]->off = true;
        }
    }

    /**
     * Включает задачу по ее номеру
     *
     * @param  int  $num
     * @return void
     */
    public function enable(int $num): void
    {
        $num = $num - 1;
        if (isset($this->tasks[$num])) {
            $this->tasks[$num]->off = false;
        }
    }

    /**
     * Сохраняет раздел с задачами
     *
     * @return void
     * @throws Exception
     */
    public function saveTasks(): void
    {
        if (!$this->tasks) {
            throw new Exception('Список задач пуст. Добавьте задачи и попробуйте еще раз.');
        }

        $this->checkOS();

        foreach ($this->tasks as $key => $task) {
            if (empty($task->command)) {
                throw new Exception("Команда не должна быть пустой. Введите и попробуйте еще раз.");
            }
            //TODO {appRoot}, {appLog}
            $this->tasks[$key]->command = str_replace(
                ['{appRoot}', '{appLog}'],
                [$this->root_path, $this->log_path],
                $task->command,
            );
        }

        $this->content = $this->getCrontabContent();
        $this->content = $this->cleanSection();
        $this->content = $this->generateSection();

        $this->save();
    }

    /**
     * Добавляет новую задачу к существующим
     *
     * @param  Task  $task
     * @param  bool  $checkUnique
     * @return void
     */
    public function addTask(Task $task, bool $checkUnique = false): void
    {
        $unique = true;
        if ($checkUnique) {
            foreach ($this->tasks as $cron_task) {
                if ($cron_task->command == $task->command) {
                    $unique = false;
                }
            }
            //и среди системных и скрытых задач тоже ищем
            foreach ($this->hidden_tasks as $cron_task) {
                if ($cron_task->command == $task->command) {
                    $unique = false;
                }
            }
        }
        if ($unique) {
            $this->tasks[] = $task;
        }
    }

    /**
     * Удаляет раздел задач
     *
     * @return void
     * @throws Exception
     */
    public function removeTasks(): void
    {
        $this->checkOS();
        $this->content = $this->getCrontabContent();
        $this->content = $this->cleanSection();
        $this->save();
    }

    /**
     * Проверка операционной системы
     *
     * @return void
     * @throws Exception
     */
    private function checkOS(): void
    {
        if (str_contains(PHP_OS, 'WIN')) {
            throw new Exception(
                'Ваша операционная система не поддерживает работу с этой командой',
            );
        }
    }


    /**
     * Достает раздел с задачами и парсит их
     *
     * @throws Exception
     */
    private function getTasks(): void
    {
        $this->checkOS();
        $content = $this->getCrontabContent();
        $pattern = '!('.self::TASKS_BLOCK_START.')(.*?)('.self::TASKS_BLOCK_END.')!s';

        $data = [];
        $hidden = [];

        if (preg_match($pattern, $content, $matches)) {
            $tasks = trim($matches[2], PHP_EOL);
            $tasks = explode(PHP_EOL, $tasks);
            foreach ($tasks as $task) {
                $obj = (new Task())->parseTask($task);
                //задачи помеченные как скрытые - не трогаем
                if (str_contains($obj->command, self::HIDDEN_TASK_COMMENT)) {
                    $hidden[] = $obj;
                } else {
                    $data[] = $obj;
                }
            }
        }
        $this->hidden_tasks = $hidden;
        $this->tasks = $data;
    }

    /**
     * Создает раздел с задачами
     *
     * @return string
     */
    private function generateSection(): string
    {
        if ($this->tasks) {
            if (!str_ends_with($this->content, PHP_EOL)) {
                $this->content .= PHP_EOL;
            }

            $this->content .= self::TASKS_BLOCK_START.PHP_EOL;
            foreach ($this->tasks as $task) {
                $this->content .= $task.PHP_EOL;
            }
            //hidden
            if ($this->hidden_tasks) {
                foreach ($this->hidden_tasks as $task) {
                    $this->content .= $task.PHP_EOL;
                }
            }
            $this->content .= self::TASKS_BLOCK_END.PHP_EOL;
        }

        return $this->content;
    }

    /**
     * Очищает раздел задач в содержимом crontab
     *
     * @return string
     */
    private function cleanSection(): string
    {
        /** @var string $out */
        $out = preg_replace(
            '!'.preg_quote(self::TASKS_BLOCK_START).'.*?'
            .preg_quote(self::TASKS_BLOCK_END.PHP_EOL).'!s',
            '',
            $this->content,
        );
        return trim($out, PHP_EOL);
    }

    /**
     * Получает содержимое crontab
     *
     * @return string
     */
    private function getCrontabContent(): string
    {
        try {
            $content = (string)shell_exec('crontab -l');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $content;
    }

    /**
     * Сохраняет в crontab
     *
     * @return void
     * @throws Exception
     */
    private function save(): void
    {
        $this->content = str_replace(['%', '"', '$'], ['%%', '\"', '\$'], $this->content);
        try {
            exec('echo "'.$this->content.'" | crontab -');
        } catch (Exception $e) {
            $error = '';
            if ($e->getPrevious()) {
                $error = $e->getPrevious()->getMessage();
            }
            throw new Exception('Ошибка при сохранении crontab: '.$error, 0, $e);
        }
    }
}