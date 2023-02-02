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

namespace start\service;

use start\Service;
use think\facade\Cache;

/**
 * 系统配置管理服务
 * Class ConfigService
 * @package start
 */
class ConfigService extends Service
{

    public $model = 'start\model\Config';
    /**
     * 配置数据缓存
     * @var array
     */
    protected $data = [];

    /**
     * 设置配置数据
     * @param string $name 配置名称
     * @param string $value 配置内容
     * @return static
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function set($name, $value = '', $field = 'value')
    {
        list($app, $field) = $this->parse($name);
        $model             = self::model()->where(compact('app', 'field'))->find();
        if (!$model || $field === 'all') {
            throw_error(lang('unknow_field'));
        }
        return $model->save([
            'app'   => $app,
            $field => $value,
        ]);
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
        $config = Cache::get($app.'_config') ?: [];
        if (!count($config) || env('APP_DEBUG')) {
            foreach (self::model()->where(compact('app'))->select() as $vo) {
                $config[$vo['app']][$vo['field']] = $vo['value'];
            }
            Cache::set($app.'_config', $config);
        }
        if ($field === 'all') {
            return isset($config[$app]) ? $config[$app] : [];
        }
        return isset($config[$app][$field]) ? $config[$app][$field] : null;
    }
    
    /**
     * 解析配置名称
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
