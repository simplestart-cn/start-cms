<?php

// +----------------------------------------------------------------------
// | Simplestart CMS
// +----------------------------------------------------------------------
// | 版权所有: http://www.simplestart.cn copyright 2021
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------

namespace core\controller;

use think\facade\Db;

/**
 * 数据管理
 * menu ['parent' => 'core/config', 'sort' => 2]
 */
class Data extends Base
{
    /**
     * 数据导入
     * @auth
     * @return void
     */
    public function import()
    {
    }

    /**
     * 数据备份
     * @auth
     * @return void
     */
    public function backup()
    {
    }

    /**
     * 数据清除
     * @auth
     * @return void
     */
    public function clear()
    {
    }

    /**
     * 数据优化
     * @auth
     * @return void
     */
    public function optimize()
    {
        // 升级数据表
        $database = env('database.database', '');
        $tables = Db::getTables();
        $ignore = [];
        // 添加索引
        $success = [];
        $indexs = ['id','pid','user_id']; // 索引字段
        foreach ($tables as $tab) {
            $fields = Db::getTableFields($tab);  // 数据表字段
            foreach ($indexs as $key) {
                // 添加索引
                if (in_array($key, $fields)) {
                    $last = "SELECT * FROM information_schema.statistics WHERE table_schema = '$database' AND table_name = '$tab' AND index_name = '$key'";
                    if (empty(Db::query($last))) {
                        $raw = "ALTER TABLE `$tab` ADD INDEX $key ( `$key` );";
                        Db::query($raw);
                        if (isset($success[$tab])) {
                            array_push($success[$tab], $key);
                        } else {
                            $success[$tab] = [];
                            array_push($success[$tab], $key);
                        }
                    }
                }
            }
        }
        $this->success('操作成功',$success);
    }

    /**
     * 数据升级
     * @auth
     * @return void
     */
    public function upgrade()
    {

    }

    
}
