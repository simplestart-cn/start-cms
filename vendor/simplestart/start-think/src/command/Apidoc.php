<?php

// +----------------------------------------------------------------------
// | Simplestart Think
// +----------------------------------------------------------------------
// | 版权所有: https://www.simplestart.cn copyright 2020
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-think
// +----------------------------------------------------------------------

namespace start\command;

use start\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;

/**
 * Apidoc构建命令
 * Class Apidoc
 * @package start\command
 */
class Apidoc extends Command
{
    public function configure()
    {
        $this->setName('apidoc');
        $this->addArgument('app', Argument::OPTIONAL, 'App name');
        $this->setDescription('Building app apidoc.');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return mixed
     */
    public function execute(Input $input, Output $output)
    {
        $app  = $input->getArgument('app');
        $path = $this->app->getBasePath() . $app;
        if(empty($app)){
            $output->writeln("app not found!");
            return false;
        }
        if(!file_exists($path . DIRECTORY_SEPARATOR . 'apidoc.json')){
            $output->writeln("$path/apidoc.json not found!");
            return false;
        }
        $output->writeln("start building {$app} apidoc");
        passthru("apidoc -e node_modules -i $path -o $path/apidoc");
    }
}
