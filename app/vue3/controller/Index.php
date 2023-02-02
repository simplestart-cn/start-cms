<?php
declare (strict_types = 1);
// +----------------------------------------------------------------------
// | Generate By StartCMS
// +----------------------------------------------------------------------
// | Github: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------
// | Copyright (c) http://www.simplestart.cn All rights reserved.
// +----------------------------------------------------------------------

namespace app\vue3\controller;

use start\Controller;

class Index extends Controller
{
    /**
     * 应用入口
     * @return [type] [description]
     */
    public function index()
    {
        return $this->fetch('/index');
    }
}
