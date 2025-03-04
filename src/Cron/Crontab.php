<?php

namespace LTT\Cron;

use Exception;

class Crontab
{
    private const TASKS_BLOCK_START = '#~ APP_TASKS START';
    private const TASKS_BLOCK_END = '#~ APP_TASKS END';

    /**
     * @var list<Task>
     */
    private array $tasks = [];

    private string $content = '';

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly string $root_path,
        private readonly string $log_path
    ) {
        try {
            $this->tasks = $this->getTasks();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
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

            $this->tasks[$key]->command = str_replace(
                ['{appRoot}', '{appLog}'],
                [$this->root_path, $this->log_path],
                $task->command
            );
        }

        $this->content = $this->getCrontabContent();
        $this->content = $this->cleanSection();
        $this->content = $this->generateSection();

        $this->save();
    }

    /**
     * @param  Task  $task
     * @return void
     */
    public function addTask(Task $task): void
    {
        $this->tasks[] = $task;
    }

    /**
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
     * @return void
     * @throws Exception
     */
    private function checkOS():void
    {
        if (str_contains(PHP_OS, 'WIN')) {
            throw new Exception(
                'Ваша операционная система не поддерживает работу с этой командой'
            );
        }
    }


    /**
     * @return list<Task>
     * @throws Exception
     */
    public function getTasks(): array
    {
        $this->checkOS();
        $content = $this->getCrontabContent();
        $pattern = '!(' . self::TASKS_BLOCK_START . ')(.*?)(' . self::TASKS_BLOCK_END . ')!s';

        if (preg_match($pattern, $content, $matches)) {
            $tasks = trim($matches[2], PHP_EOL);
            $tasks = explode(PHP_EOL, $tasks);
            $data = [];
            foreach ($tasks as $task) {
                $data[] = (new Task())->parseTask($task);
            }
            return $data;
        }

        return [];
    }

    /**
     * Создать раздел с задачами
     *
     * @return string
     */
    private function generateSection(): string
    {
        if ($this->tasks) {
            if (!str_ends_with($this->content, PHP_EOL)) {
                $this->content .= PHP_EOL;
            }

            $this->content .= self::TASKS_BLOCK_START . PHP_EOL;
            foreach ($this->tasks as $task) {
                $this->content .= $task . PHP_EOL;
            }
            $this->content .= self::TASKS_BLOCK_END . PHP_EOL;
        }

        return $this->content;
    }

    /**
     * Очистить раздел задач в содержимом crontab
     *
     * @return string
     */
    private function cleanSection(): string
    {
        /** @var string $out */
        $out =  preg_replace(
            '!' . preg_quote(self::TASKS_BLOCK_START) . '.*?'
            . preg_quote(self::TASKS_BLOCK_END . PHP_EOL) . '!s',
            '',
            $this->content
        );
        return $out;
    }

    /**
     * Получаем содержимое crontab
     *
     * @return string
     */
    private function getCrontabContent(): string
    {
        try {
            $content = (string) shell_exec('crontab -l');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $content;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function save(): void
    {
        $this->content = str_replace(['%', '"', '$'], ['%%', '\"', '\$'], $this->content);
        try {
            exec('echo "' . $this->content . '" | crontab -');
        } catch (Exception $e) {
            $error = '';
            if($e->getPrevious()) {
                $error = $e->getPrevious()->getMessage();
            }
            throw new Exception('Ошибка при сохранении crontab: '.$error, 0, $e);
        }
    }
}