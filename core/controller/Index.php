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

/**
 * 站点入口
 * Class Index
 * @package core\controller
 */
class Index extends Base
{
    /**
     * 系统首页
     * @return [type] [description]
     */
    public function index()
    {
        $this->redirect('/web');
    }

    /**
     * 系统入口
     * @return void
     */
    public function entry()
    {
        $this->fetch('index');
    }



}
