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

use core\common\ExceptionHandle;
use core\common\Request;
use core\common\Paginator;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    'think\Paginator'        => Paginator::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
