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
use start\AppService;
use think\facade\Cache;

/**
 * 系统配置管理服务
 * Class ConfigService
 * @package start
 */
class ConfigService extends Service
{

    public $model = 'core\model\Config';
    
    /**
     * 配置数据缓存
     * @var array
     */
    protected $data = [];

    /**
     * 设置配置数据
     * @param string $name   配置名称
     * @param string $value  配置内容
     * @param string $column 配置项目
     * @return static
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function set($name, $value = '', $column = 'value')
    {
        list($app, $field) = $this->parse($name);
        $model             = self::model()->where(compact('app', 'field'))->find();
        if (!$model || $field === 'all') {
            throw_error(lang('unknow_field'));
        }
        return $model->save([$column => $value]);
    }

    /**
     * 读取配置数据
     * @param string $name
     * @return array|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get($name)
    {
        list($app, $field) = $this->parse($name);
        $config = [];
        foreach (self::model()->where(compact('app'))->select() as $vo) {
            if(!isset($config[$vo['app']])) $config[$vo['app']] = [];
            $config[$vo['app']][$vo['field']] = $vo['value'];
        }
        if ($field === 'all') {
            return isset($config[$app]) ? $config[$app] : [];
        }
        return isset($config[$app][$field]) ? $config[$app][$field] : null;
    }

    /**
     * 批量更新
     * @param  array $data [description]
     * @return [type]       [description]
     */
    public static function updateList($data)
    {
        $apps = array_unique(array_column($data, 'app'));
        foreach ($apps as $app) {
            Cache::delete($app.'_config');
        }
        return self::model()->saveAll($data, true);
    }

    /**
     * 更新主题
     * @param  array $color [description]
     * @return boolean       [description]
     */
    public static function updateTheme(string $color)
    {
        Cache::delete('core_config');
        return self::model()->where(['app' => 'core', 'field' => 'theme'])->save(['value' => $color]);
    }
    

    /**
     * 获取应用配置
     *
     * @param string  $app        // 应用名称
     * @param string  $group      // 分组名称
     * @param boolean $protected  // 保护配置
     * @return void
     */
    public static function getConfig($app = '', $group = "", $protected = true)
    {
        $filter = array();
        if (!empty($app)) {
            $filter['app'] = $app;
        } else {
            $apps          = array_merge(AppService::getActive(), ['core']);
            $filter['app'] = ['in', $apps];
        }
        if (!empty($group)){
            $filter['group'] = $group;
        }
        if ($protected) {
            $filter['protected'] = 0;
        }
        $data = array();
        $list = self::getList($filter);
        foreach ($list as $item) {
            if(isset($data[$item['app']])){
                $data[$item['app']][$item['field']] = $item['value'];
            }else{
                $data[$item['app']] = [];
                $data[$item['app']][$item['field']] = $item['value'];
            }
        }
        if(!empty($app) && isset($data[$app])){
            return $data[$app];
        }
        return $data;
    }

    /**
     * 解析缓存名称
     * @param string $name 配置名称
     * @param string $app  应用名称
     * @return array
     */
    private function parse($name, $app = 'core')
    {
        if (stripos($name, '.') !== false) {
            [$app, $field] = explode('.', $name);
        }else{
            $app = $name;
        }
        $field = isset($field) && !empty($field) ? $field : 'all';
        return [strtolower($app), strtolower($field)];
    }
    
}
