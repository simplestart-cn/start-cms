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
namespace core\installer;
use think\facade\Db;

$sql = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data.sql');
// 导入基础数据
Db::getPdo()->exec($sql);
