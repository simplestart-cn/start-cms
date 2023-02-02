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

use think\facade\Db;

// 这里应该执行一次备份

// 这里删除应用相关数据表
$tables = ['vue2_page1','vue2_page2'];
foreach ($tables as $table) {
    Db::getPdo()->exec("DROP TABLE IF EXISTS $table");
}
// 这里删除插入的仪表盘数据
$res = Db::getPdo()->exec("DELETE FROM core_dashboard WHERE app='vue2';");