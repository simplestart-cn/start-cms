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

use think\App;
use think\Container;
use think\Db;
use think\db\Query;

/**
 * 控制器挂件
 * Class Helper
 * @package start
 */
abstract class Helper
{
    /**
     * 应用容器
     * @var App
     */
    public $app;

    /**
     * 模型实例
     * @var Query
     */
    public $model;

    /**
     * 控制器实例
     * @var Controller
     */
    public $controller;

    /**
     * Helper constructor.
     * @param App $app
     * @param Controller $controller
     */
    public function __construct(Controller $controller, App $app)
    {
        $this->app = $app;
        $this->controller = $controller;
    }

    /**
     * 挂件初始化
     * @param string|Query $dbQuery
     * @return Db|Query
     */
    protected function initModel($model)
    {
        $this->model = $model;
    }

    /**
     * 实例对象反射
     * @param array $args
     * @return static
     */
    public static function instance(...$args): Helper
    {
        return Container::getInstance()->invokeClass(static::class, $args);
    }
}