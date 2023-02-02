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

/**
 * 应用门面
 * Class AppFacade
 * @package start
 */
class AppFacade
{
    /**
     * 获取应用门面实例
     * @param string  $name  应用名称
     * @return object
     */
    public static function getFacade(string $name)
    {
        $class = 'app\\' . $name . '\\Facade';
        if(class_exists($class)){
            return new $class();
        }
        return false;
    }

}
