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

namespace core\service;

use start\Service;
use start\AppManager;

/**
 * 系统应用服务
 * Class AuthService
 * @package core\service
 */
class AppService extends Service
{
    public $model = 'core\model\App';

    /**
     * 应用安装
     * @param  [type] $name [description]
     * @return [type]      [description]
     */
    public static function install($name)
    {
        return AppManager::install($name);
    }

    /**
     * 应用卸载
     * @param  [type] $app [description]
     * @return [type]      [description]
     */
    public static function uninstall($name)
    {
        if (!AuthService::instance()->isSuper()) {
            throw_error('仅限超管操作');
        }
        return AppManager::uninstall($name);
    }

    /**
     * 应用删除
     * @param string  $name
     * @param boolean $force
     * @return boolean
     */
    public static function remove($name, $force = false)
    {
        if (!AuthService::instance()->isSuper()) {
            throw_error('仅限超管操作');
        }
        return AppManager::remove($name);
    }

    /**
     * 更新信息
     * @param  array  $input [description]
     * @return [type]        [description]
     */
    public static function update($input = [])
    {
        $model = self::getInfo(['name' => $input['name']]);
        if (!$model) {
            throw_error(lang('app_not_installed'));
        }
        return $model->save($input);
    }

    /**
     * 升级配置
     * @param [type] $name
     * @return void
     */
    public static function upgradeConfig($name)
    {
        return AppManager::upgradeConfig($name);
    }
}
