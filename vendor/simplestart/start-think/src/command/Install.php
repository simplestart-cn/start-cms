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

use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

/**
 * 系统及应用安装指令
 * Class Install
 * @package start\command
 */
class Install extends Command
{

    /**
     * 指定模块名称
     * @var string
     */
    protected $name;

    /**
     * 查询规则
     * @var array
     */
    protected $rules = [];

    /**
     * 忽略规则
     * @var array
     */
    protected $ignore = [];

    /**
     * 规则配置
     * @var array
     */
    protected $bind = [
        'core'  => [
            'rules'  => ['think', 'core'],
            'ignore' => [],
        ],
        'assets' => [
            'rules'  => [],
            'ignore' => [],
        ],
        'config' => [
            'rules'  => [
                'config/app.php',
                'config/cache.php',
                'config/log.php',
                'config/route.php',
                'config/session.php',
                'config/trace.php',
                'config/view.php'
            ],
            'ignore' => [],
        ],
    ];

    protected function configure()
    {
        $this->setName('install');
        $this->addArgument('app', Argument::OPTIONAL, 'App name');
        $this->setDescription("Install core or app");
    }

    protected function execute(Input $input, Output $output)
    {
        $this->name = trim($input->getArgument('app'));
        if (empty($this->name)) {
            $this->name = 'core';
        }
        if ($this->name === 'core') {
            foreach ($this->bind as $bind) {
                $this->rules = array_merge($this->rules, $bind['rules']);
                $this->ignore = array_merge($this->ignore, $bind['ignore']);
            }
            [$this->installFile(), $this->installData()];
        } elseif (isset($this->bind[$this->name])) {
            $this->rules = empty($this->bind[$this->name]['rules']) ? [] : $this->bind[$this->name]['rules'];
            $this->ignore = empty($this->bind[$this->name]['ignore']) ? [] : $this->bind[$this->name]['ignore'];
            [$this->installFile(), $this->installData()];
        } else {
            $this->output->writeln("The specified module {$this->name} is not configured with installation rules");
        }
    }

    protected function installFile()
    {
        $data = AppManager::instance()->generateDifference($this->rules, $this->ignore);
        if (empty($data)) $this->output->writeln('No need to update the file if the file comparison is consistent');
        else foreach ($data as $file) {
            list($state, $mode, $name) = AppManager::instance()->fileSynchronization($file);
            if ($state) {
                if ($mode === 'add') $this->output->writeln("--- {$name} add successfully");
                if ($mode === 'mod') $this->output->writeln("--- {$name} update successfully");
                if ($mode === 'del') $this->output->writeln("--- {$name} delete successfully");
            } else {
                if ($mode === 'add') $this->output->writeln("--- {$name} add failed");
                if ($mode === 'mod') $this->output->writeln("--- {$name} update failed");
                if ($mode === 'del') $this->output->writeln("--- {$name} delete failed");
            }
        }
    }

    protected function installData()
    {

    }

}