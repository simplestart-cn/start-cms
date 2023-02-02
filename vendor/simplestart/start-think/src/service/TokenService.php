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

/**
 * 表单令牌管理服务
 * Class TokenService
 * @package start
 */
class TokenService extends Service
{
    /**
     * 获取当前请求令牌
     * @return array|string
     */
    public function getInputToken()
    {
        return $this->app->request->header('user-form-token', input('_csrf_', ''));
    }

    /**
     * 生成表单CSRF信息
     * @param null|string $node
     * @return array
     */
    public function buildFormToken($node = null)
    {
        list($token, $time) = [uniqid('csrf') . rand(1000, 9999), time()];
        foreach ($this->app->session->all() as $key => $item) {
            if (stripos($key, 'csrf') === 0 && isset($item['time'])) {
                if ($item['time'] + 600 < $time) $this->clearFormToken($key);
            }
        }
        $data = ['node' => NodeService::instance()->fullnode($node), 'token' => $token, 'time' => $time];
        $this->app->session->set($token, $data);
        return $data;
    }

    /**
     * 验证表单令牌是否有效
     * @param string $token 表单令牌
     * @param string $node 授权节点
     * @return boolean
     */
    public function checkFormToken($token = null, $node = null)
    {
        if (is_null($token)) $token = $this->getInputToken();
        if (is_null($node)) $node = NodeService::instance()->getCurrent();
        // 读取缓存并检查是否有效
        $cache = $this->app->session->get($token, []);
        if (empty($cache['node']) || empty($cache['time']) || empty($cache['token'])) return false;
        if ($cache['time'] + 600 < time() || strtolower($cache['node']) !== strtolower($node)) return false;
        return true;
    }

    /**
     * 清理表单CSRF信息
     * @param string $token
     * @return $this
     */
    public function clearFormToken($token = null)
    {
        if (is_null($token)) $token = $this->getInputToken();
        $this->app->session->delete($token);
        return $this;
    }

    
}