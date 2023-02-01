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

use core\service\AuthService;
use core\service\ConfigService;
use core\service\RecordService;
use think\exception\HttpResponseException;

if (!function_exists('get_user')) {
    /**
     * 获取当前登录信息
     * @param string $node
     * @return boolean
     */
    function get_user($force = true)
    {
        return AuthService::instance()->getUser($force);
    }
}
if (!function_exists('get_user_id')) {
    /**
     * 获取当前管理员ID
     * @param string $node
     * @return boolean
     */
    function get_user_id($force = true)
    {
        return AuthService::instance()->getUserId($force);
    }
}
if (!function_exists('get_open_id')) {
    /**
     * 获取授权openid
     * @param string $node
     * @return boolean
     */
    function get_open_id($user_id = 0)
    {
        return AuthService::instance()->getOpenId($user_id);
    }
}
if (!function_exists('get_union_id')) {
    /**
     * 获取授权unionid
     * @param string $node
     * @return boolean
     */
    function get_union_id($user_id = 0)
    {
        return AuthService::instance()->getUnionId($user_id);
    }
}
if (!function_exists('get_user_name')) {
    /**
     * 获取当前管理员名称
     * @param string $node
     * @return boolean
     */
    function get_user_name($force = true)
    {
        return AuthService::instance()->getUserName($force);
    }
}
if (!function_exists('auth')) {
    /**
     * 访问权限检查
     * @param string $node
     * @return boolean
     */
    function auth($node)
    {
        return AuthService::instance()->check($node);
    }
}
if (!function_exists('conf')) {
    /**
     * 获取或配置系统参数
     * @param string $name 参数名称
     * @param string $value 参数内容
     */
    function conf($name = '', $value = null, $field = 'value')
    {
        if (is_null($value) && is_string($name)) {
            return ConfigService::instance()->get($name);
        } else {
            return ConfigService::instance()->set($name, $value, $field);
        }
    }
}
if (!function_exists('oplog')) {
    /**
     * 记录行为日志
     * @param string $action 日志行为
     * @param string $content 日志内容
     * @return boolean
     */
    function oplog($action, $content)
    {
        return RecordService::operation($action, $content);
    }
}
