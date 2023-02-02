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
use start\AppManager;
use start\service\AuthService;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;

/**
 * 权限菜单构建命令
 * Class Auth
 * @package start\command
 */
class Auth extends Command
{
    public function configure()
    {
        $this->setName('auth');
        $this->addArgument('app', Argument::OPTIONAL, 'App name');
        $this->setDescription('Building app auth.');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return mixed
     */
    public function execute(Input $input, Output $output)
    {
        $app     = $input->getArgument('app');
        $service = AuthService::instance();
        if (!empty($app)) {
            $output->writeln("start building {$app}...");
            $res = $service->building($app);
            $output->writeln("{$app} complete!");
        } else {
            $apps    = AppManager::getApps();
            if(empty($apps)){
                $output->writeln("app not found!");
                return false;
            }
            $output->writeln("start building...");
            foreach ($apps as $name) {
                $res = $service->building($name);
                $output->writeln("{$name} complete!");
            }
            $output->writeln("all complete!");
        }
    }

}
