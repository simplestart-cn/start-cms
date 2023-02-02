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
 * 系统缓存管理服务
 * Class RuntimeService
 * @package start
 */
class RuntimeService extends Service
{
	/**
     * 设置实时运行配置
     * @param array|null $map 应用映射
     * @param array|null $domain 域名映射
     * * @param boolean|null $debug 支持模式
     * @return boolean 是否调试模式
     */
    public function setRuntime($map = [],  $domain = [], $debug = null)
    {
        $data = $this->getRuntime();
        if (is_array($map) && count($map) > 0 && count($data['app_map']) > 0) {
            foreach ($data['app_map'] as $kk => $vv) {
                if (in_array($vv, $map)) {
                    unset($data['app_map'][$kk]);
                }
            }

        }
        if (is_array($domain) && count($domain) > 0 && count($data['app_host']) > 0) {
            foreach ($data['app_host'] as $kk => $vv) {
                if (in_array($vv, $domain)) {
                    unset($data['app_host'][$kk]);
                }
            }

        }
        $file            = "{$this->app->getRuntimePath()}/config.json";
        $data['app_map'] = is_null($map) ? [] : array_merge($data['app_map'], $map);
        $data['app_host'] = is_null($domain) ? [] : array_merge($data['app_host'], $domain);
        $data['app_debug'] = is_null($debug) ? $data['app_debug'] : $debug;
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $this->bindRuntime($data);
    }

    /**
     * 获取实时运行配置
     * @param null|string $key
     * @return array
     */
    public function getRuntime($key = null)
    {
        $file = "{$this->app->getRuntimePath()}/config.json";
        $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        if (empty($data) || !is_array($data)) {
            $data = [];
        }

        if (empty($data['app_map']) || !is_array($data['app_map'])) {
            $data['app_map'] = [];
        }

        if (empty($data['app_host']) || !is_array($data['app_host'])) {
            $data['app_host'] = [];
        }

        if (empty($data['app_debug']) || !is_string($data['app_debug'])) {
            $data['app_debug'] = env('APP_DEBUG') ?? false;
        }

        return is_null($key) ? $data : (isset($data[$key]) ? $data[$key] : null);
    }

    /**
     * 绑定应用实时配置
     * @param array $data 配置数据
     * @return boolean 是否调试模式
     */
    public function bindRuntime($data = [])
    {
        if (empty($data)) {
            $data = $this->getRuntime();
        }
        // 动态绑定应用
        if (!empty($data['app_map'])) {
            $maps = $this->app->config->get('app.app_map', []);
            if (is_array($maps) && count($maps) > 0 && count($data['app_map']) > 0) {
                foreach ($maps as $kk => $vv) {
                    if (in_array($vv, $data['app_map'])) {
                        unset($maps[$kk]);
                    }
                }

            }
            $this->app->config->set(['app_map' => array_merge($maps, $data['app_map'])], 'app');
        }
        // 动态绑定域名
        if (!empty($data['app_host'])) {
            $uris = $this->app->config->get('app.domain_bind', []);
            if (is_array($uris) && count($uris) > 0 && count($data['app_host']) > 0) {
                foreach ($uris as $kk => $vv) {
                    if (in_array($vv, $data['app_host'])) {
                        unset($uris[$kk]);
                    }
                }
            }
            $this->app->config->set(['domain_bind' => array_merge($uris, $data['app_host'])], 'app');
        }
        // 动态设置运行模式
        return $this->app->debug($data['app_debug'])->isDebug();
    }

    /**
     * 打印输出数据到文件
     * @param mixed $data 输出的数据
     * @param string $file 文件名称
     * @param boolean $replace 强制替换文件
     */
    public function debug($data, $file = null, $replace = false)
    {
        if (is_null($file)) {
            $path = $this->app->getRuntimePath() . 'debug';
            if(!is_dir($path)){
                mkdir($path, 0755, true);
            }
            $file = $path . DIRECTORY_SEPARATOR . date('Ymd') . '.log';
        }
        $str = (is_string($data) ? $data : ((is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true))) . PHP_EOL;
        $replace ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }

    /**
     * 判断实时运行模式
     * @return boolean
     */
    public function isDebug()
    {
        return $this->getRuntime('app_debug');
    }

    /**
     * 初始化并运行应用
     * @param \think\App $app
     */
    public function doInit(\think\App $app)
    {
        $app->debug($this->isDebug());
        $response = $app->http->run();
        $response->send();
        $app->http->end($response);
    }
}