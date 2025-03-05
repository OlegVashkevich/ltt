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

#[AsCommand(name: 'cron:on')]
class OnCommand extends Command
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

        try {
            $crontab = new Crontab(APP_ROOT, APP_ROOT.'/log');
            if (
                isset($input->getArguments()['num'])
                && !empty($input->getArguments()['num'])
                && is_string($input->getArguments()['num'])
            ) {
                $num = (int)$input->getArguments()['num'];
                $crontab->enable($num);
                $crontab->saveTasks();
                $logger->info('Задача номер '.$num.' включена');
            } else {
                $logger->error('Ошибка аргумента num');
                return Command::INVALID;
            }
        } catch (\Throwable $e) {
            $logger->error($e->getMessage());
        }

        if ($logger->hasErrored()) {
            return Command::FAILURE;
        } else {
            return Command::SUCCESS;
        }
    }

    // ...
    protected function configure(): void
    {
        $this
            ->setDescription('Включает закомментированную задачу по номеру в списке cron:show')
            ->setHelp(
                'Эта команда включает закомментированную задачу по номеру в списке cron:show',
            )
            ->addArgument(
                'num',
                InputArgument::REQUIRED,
                'номер задачи в списке вывода cron:show',
            );
    }
}