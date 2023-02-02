<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Slince <taosikai@yeah.net>
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace start\command;

use start\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use think\console\input\Argument;

class Run extends Command
{
    public function configure()
    {
        $this->setName('run')
            ->addOption('root', 'r', Option::VALUE_OPTIONAL, 'The document root of the application', '')
            ->addArgument('host', Argument::OPTIONAL, 'Default host: localhost:8080')
            ->setDescription('PHP Built-in Server for StartCMS');
    }

    public function execute(Input $input, Output $output)
    {
        $port = 8080;
        $root = $input->getOption('root');
        $host = $input->getArgument('host');

        if (empty($root)) {
            $root = $this->app->getRootPath();
        }
        if (empty($host)) {
            $host = 'localhost';
        } elseif (stripos($host, ':') !== false) {
            $host = explode(':', $host);
            $port = array_pop($host);
            $host = array_pop($host);
        }

        $command = sprintf(
            'php -S %s:%d -t %s %s',
            $host,
            $port,
            escapeshellarg($root),
            escapeshellarg($root . DIRECTORY_SEPARATOR . 'router.php')
        );
        $output->writeln(sprintf('Simplestart!'));
        $output->writeln(sprintf('Server is run on <http://%s:%s/>', $host, $port));
        $output->writeln(sprintf('Document root is: %s', $root));
        $output->writeln(sprintf('You can exit with <info>`CTRL-C`</info>'));
        passthru($command);
    }
}
