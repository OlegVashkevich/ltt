<?php

namespace LTT\Console\Cron;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use LTT\Cron\Crontab;

#[AsCommand(name: 'cron:init')]
class InitCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbosityLevelMap = [
            LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
        ];
        $formatLevelMap = [
            LogLevel::CRITICAL => ConsoleLogger::ERROR,
            LogLevel::DEBUG => ConsoleLogger::INFO,
        ];
        $logger = new ConsoleLogger($output, $verbosityLevelMap, $formatLevelMap);

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))

        //print_r($input->getArguments());
        try {
            $crontab = new Crontab(APP_ROOT, APP_ROOT.'/log');
            if (
                isset($input->getArguments()['path'])
                && !empty($input->getArguments()['path'])
                && is_string($input->getArguments()['path'])
            ) {
                $path = (string)$input->getArguments()['path'];
                $crontab->init($path);
                $logger->info('Задача для обновления списка по расписанию добавлена в crontab для вашего файла');
            } else {
                $crontab->init();
                $logger->info('Задача для обновления списка по расписанию добавлена в crontab для файла по умолчанию');
            }
        } catch (\Throwable $e) {
            $logger->error($e->getMessage());
        }

        if ($logger->hasErrored()) {
            return Command::FAILURE;
        } else {
            return Command::SUCCESS;
        }

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }

    // ...
    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Создает скрытую задачу для обновления списка задач по расписанию.')
            // the command help shown when running the command with the "--help" option
            ->setHelp(
                'Эта команда создаст раздел в crontab и добавит системную задачу для обновления списка задач по расписанию из указанного файла',
            )
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'путь к файлу с расписанием задач, по умолчанию - config/schedule.php',
            );
    }
}