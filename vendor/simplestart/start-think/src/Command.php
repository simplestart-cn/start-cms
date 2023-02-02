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

namespace start;

use think\console\Command as ThinkCommand;
use think\console\Input;
use think\console\Output;

/**
 * 自定义指令基类
 * Class Command
 * @package start
 */
class Command extends ThinkCommand
{

    /**
     * 初始化指令变量
     * @param Input $input
     * @param Output $output
     */
    protected function initialize(Input $input, Output $output)
    {

    }

    /**
     * 获取当前应用版本
     * @return string
     */
    public function version()
    {
        return $this->app->config->get('base.version', 'v1.0.0');
    }

    /**
     * 创建并获取Think指令内容
     * @param string $args 指定参数
     * @return string
     */
    public function startThink($args = '')
    {
        $root = $this->app->getRootPath();
        return trim("php {$root}start {$args}");
    }

    
}