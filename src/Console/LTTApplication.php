<?php

namespace LTT\Console;

use Exception;
use LTT\Console\Cron\DestroyCommand;
use LTT\Console\Cron\OffCommand;
use LTT\Console\Cron\OnCommand;
use LTT\Console\Cron\ShowCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \LTT\Console\Cron\InitCommand as CronInitCommand;

class LTTApplication extends Application
{
    const NAME = 'Libs, tricks and tips application';
    const VERSION = '0.2';

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);
        $this->add(new InitCommand(false));
        $this->add(new CronInitCommand());
        $this->add(new ShowCommand());
        $this->add(new OnCommand());
        $this->add(new OffCommand());
        $this->add(new DestroyCommand());
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        try {
            return parent::run($input, $output);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption(
                '--help',
                '-h',
                InputOption::VALUE_NONE,
                'Display help for the given command. When no command is given display help for the <info>list</info> command',
            ),
            //new InputOption('--silent', null, InputOption::VALUE_NONE, 'Do not output any message'),
            //new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Only errors are displayed. All other output is suppressed'),
            //new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version'),
            //new InputOption('--ansi', '', InputOption::VALUE_NEGATABLE, 'Force (or disable --no-ansi) ANSI output', null),
            //new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question'),
        ]);
    }

    protected function getDefaultCommands(): array
    {
        return [
            //new HelpCommand(),
            new ListCommand(),
            //new CompleteCommand(),
            //new DumpCompletionCommand()
        ];
    }
}