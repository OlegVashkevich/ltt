<?php

namespace LTT\Console\Cron;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use LTT\Cron\Crontab;

#[AsCommand(name: 'cron:show', description: 'Выводит список задач для текущего приложения')]
class ShowCommand extends Command
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
            $logger->info($crontab->show());
        } catch (\Throwable $e) {
            $logger->error($e->getMessage());
        }

        if ($logger->hasErrored()) {
            return Command::FAILURE;
        } else {
            return Command::SUCCESS;
        }
    }
}