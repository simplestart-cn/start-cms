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
use think\console\input\Argument;
use think\console\Output;

/**
 * 数据库修复优化指令
 * Class Database
 * @package start\command
 */
class Database extends Command
{
    public function configure()
    {
        $this->setName('database');
        $this->addArgument('action', Argument::OPTIONAL, 'repair|optimize', 'optimize');
        $this->setDescription('Database Optimize and Repair for ThinkStart');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return mixed
     */
    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        if (in_array($action, ['repair', 'optimize'])) {
            return $this->{"_{$action}"}();
        }
        $output->error("Wrong operation, currently allow repair|optimize");
    }

    /**
     * 修复数据表
     * @throws \start\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _repair()
    {

    }

    /**
     * 优化所有数据表
     * @throws \start\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _optimize()
    {

    }

    /**
     * 获取数据库的数据表
     * @return array
     */
    protected function getTables()
    {
        $tables = [];
        foreach ($this->app->db->query("show tables") as $item) {
            $tables = array_merge($tables, array_values($item));
        }
        return $tables;
    }

}