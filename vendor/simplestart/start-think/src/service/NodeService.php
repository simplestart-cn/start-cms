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
use start\extend\DataExtend;

/**
 * 应用节点服务管理
 * Class NodeService
 * @package start
 */
class NodeService extends Service
{
    
    /**
     * 驼峰转下划线规则
     * @param string $name
     * @return string
     */
    public function nameTolower($name)
    {
        $dots = [];
        foreach (explode('.', strtr($name, '/', '.')) as $dot) {
            $dots[] = trim(preg_replace("/[A-Z]/", "_\\0", $dot), "_");
        }
        return strtolower(join('.', $dots));
    }

    /**
     * 获取当前节点内容
     * @param string $type
     * @return string
     */
    public function getCurrent($type = '')
    {
        $prefix = $this->app->getNamespace();
        // 这两个方法有bug
        // $middle = $this->nameTolower($this->app->request->controller());
        // $suffix = ($type === 'controller') ? '' : ($this->app->request->action());
        $pathinfo = str_replace('/', '\\', $this->app->request->pathinfo());
        if($prefix === 'core'){
            return strtr($prefix . '\\' . $pathinfo, '\\', '/');
        }
        return strtr(substr($prefix, stripos($prefix, '\\') + 1). '\\' . $pathinfo, '\\', '/');
        
    }

    /**
     * 检查并完整节点内容
     * @param string $node
     * @return string
     */
    public function fullnode($node)
    {
        if (empty($node)) return $this->getCurrent();
        if (count($attrs = explode('/', $node)) === 1) {
            return $this->getCurrent('controller') . "/{$node}";
        } else {
            $attrs[1] = $this->nameTolower($attrs[1]);
            return join('/', $attrs);
        }
    }

    /**
     * 获取节点列表
     * @return array [description]
     */
    public function getAll($app = '', $force = false)
    {
        list($nodes, $parents, $methods) = [[], [], array_reverse($this->getMethods($app, $force))];
        foreach ($methods as $node => $method) {
            list($count, $parent) = [substr_count($node, '/'), substr($node, 0, strripos($node, '/'))];
            if ($count === 2 && (
                !empty($method['isauth']) || 
                !empty($method['issuper']) ||
                !empty($method['isadmin']) ||
                !empty($method['islogin']) ||
                !empty($method['ismenu']) ||
                !empty($method['isview']) ||
                !empty($method['isroute'])
                )) {
                in_array($parent, $parents) or array_push($parents, $parent);
                $nodes[$node] = [
                    'node' => $node,
                    'title' => $method['title'],
                    'parent' => $parent,
                    'isauth' => $method['isauth'],
                    'issuper' => $method['issuper'],
                    'isadmin' => $method['isadmin'],
                    'islogin' => $method['islogin'],
                    'ismenu' => $method['ismenu'],
                    'isview' => $method['isview'],
                    'isroute' => $method['isroute']
                ];
            } elseif ($count === 1 && in_array($parent, $parents)) {
                $nodes[$node] = [
                    'node' => $node,
                    'title' => $method['title'],
                    'parent' => $parent,
                    'isauth' => $method['isauth'],
                    'issuper' => $method['issuper'],
                    'isadmin' => $method['isadmin'],
                    'islogin' => $method['islogin'],
                    'ismenu' => $method['ismenu'],
                    'isview' => $method['isview'],
                    'isroute' => $method['isroute']
                ];
            }
        }
        foreach (array_keys($nodes) as $key) foreach ($methods as $node => $method) if (stripos($key, "{$node}/") !== false) {
            $parent = substr($node, 0, strripos($node, '/'));
            $nodes[$node] = [
                'node' => $node,
                'title' => $method['title'],
                'parent' => $parent,
                'isauth' => $method['isauth'],
                'issuper' => $method['issuper'],
                'isadmin' => $method['isadmin'],
                'islogin' => $method['islogin'],
                'ismenu'  => $method['ismenu'],
                'isview'  => $method['isview'],
                'isroute'  => $method['isroute']
            ];
            $nodes[$parent] = [
                'node' => $parent,
                'title' => ucfirst($parent),
                'parent' => '',
                'isauth' => $method['isauth'],
                'issuper' => $method['issuper'],
                'isadmin' => $method['isadmin'],
                'islogin' => $method['islogin'],
                'ismenu' => (boolean)$method['ismenu'],
                'isview' => 0,
                'isroute' => $method['isroute']
            ];
        }
        return array_reverse($nodes);
    }

    /**
     * 获取节点树
     * @return [type] [description]
     */
    public function getTree($force = false)
    {
        $nodes = $this->getAll($force);
        return DataExtend::arr2tree($nodes, 'node', 'parent', 'child');
    }

    /**
     * 获取所有应用名称
     * @return [type] [description]
     */
    public function getApps()
    {
        $path = $this->app->getBasePath();
        $apps = [];
        foreach (glob("{$path}*") as $item) {
            if (is_dir($item)) {
                $item = explode(DIRECTORY_SEPARATOR, $item);
                array_push($apps, end($item));
            }
        }
        return $apps;
    }

    /**
     * 控制器方法扫描处理
     * @param boolean $force
     * @return array
     * @throws \ReflectionException
     */
    public function getMethods($app = '',$force = false)
    {
        static $data = [];
        if (!$force) {
            $data = $this->app->cache->get($app.'_auth_node', []);
            if (count($data) > 0) return $data;
        } else {
            $data = [];
        }
        $ignores = get_class_methods('\start\Controller');
        if(empty($app)){
            $directory = $this->app->getBasePath();
        }else {
            $directory = $app === 'core' ? $this->app->getRootPath() . $app . DIRECTORY_SEPARATOR : $this->app->getBasePath() . $app . DIRECTORY_SEPARATOR;
        }
        foreach ($this->_scanDirectory($directory) as $file) {
            if($app === 'core'){
                if (preg_match("|/(\w+)/controller/(.+)\.php$|i", $file, $matches)) {
                    list(, $namespace, $classname) = $matches;
                    $class = new \ReflectionClass(strtr("{$namespace}/controller/{$classname}", '/', '\\'));
                    $prefix = strtr("{$namespace}/{$this->nameTolower($classname)}", '\\', '/');
                    $data[$prefix] = $this->_parseComment($class->getDocComment(), $classname);
                    foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                        if (in_array($metname = $method->getName(), $ignores)) continue;
                        $data["{$prefix}/{$metname}"] = $this->_parseComment($method->getDocComment(), $metname);
                    }
                }
            }else{
                if (preg_match("|/(\w+)/(\w+)/controller/(.+)\.php$|i", $file, $matches)) {
                    list(, $namespace, $appname, $classname) = $matches;
                    $class = new \ReflectionClass(strtr("{$namespace}/{$appname}/controller/{$classname}", '/', '\\'));
                    $prefix = strtr("{$appname}/{$this->nameTolower($classname)}", '\\', '/');
                    $data[$prefix] = $this->_parseComment($class->getDocComment(), $classname);
                    foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                        if (in_array($metname = $method->getName(), $ignores)) continue;
                        $data["{$prefix}/{$metname}"] = $this->_parseComment($method->getDocComment(), $metname);
                    }
                }
            }
            
        }
        $data = array_change_key_case($data, CASE_LOWER);
        $this->app->cache->set($app.'_auth_node', $data);
        return $data;
    }

    /**
     * 解析硬节点属性
     * @param string $comment
     * @param string $default
     * @return array
     */
    private function _parseComment($comment, $default = '')
    {

        $text = strtr($comment, "\n", ' ');
        $title = preg_replace('/^\/\*\s*\*\s*\*\s*(.*?)\s*\*.*?$/', '$1', $text);
        foreach (['@auth', '@super', '@admin', '@menu', '@view', '@route'] as $find) if (stripos($title, $find) === 0) {
            $title = $default;
        }
        $method =  [
            'title'   => $title ? $title : $default,
            'isauth'  => intval(preg_match('/@auth\s*/i', $text)),   // 仅限授权访问
            'issuper' => intval(preg_match('/@super\s*/i', $text)),  // 仅限超管访问
            'isadmin' => intval(preg_match('/@admin\s*/i', $text)),  // 仅限管理访问
            'islogin' => intval(preg_match('/@login\s*/i', $text)),  // 登录即可访问
            'ismenu'  => intval(preg_match('/@menu\s*/i', $text)),   // 作为菜单显示
            'isview'  => intval(preg_match('/@view\s*/i', $text)),   // 渲染视图模板
            'isroute'  => intval(preg_match('/@route\s*/i', $text)), // 仅作前端路由
        ];
        // 匹配设置的auth值
        if($method['isauth']){
            preg_match('/@auth\s\{(.*?)\}\s/i', $text, $auth);
            if(count($auth) > 1){
                $auth = '{'.str_replace("'", '"', preg_replace("/\s/i", '', $auth[1])).'}';
                $method['isauth'] = json_decode($auth, true) ?? 1;
            }
        }
        // 匹配设置的view值
        if($method['isview']){
            preg_match('/@view\s(.*?)\s/i', $text, $view);
            if(count($view) > 1){
                if(!empty($view[1])){
                    if(is_array($method['isauth'])){
                        $method['isauth']['view'] = $view[1];
                    }else{
                        $method['isauth'] = ['menu' => $method['ismenu'], 'view' => $view[1]];
                    }
                }
            }
        }
        return $method;
    }

    /**
     * 获取所有PHP文件列表
     * @param string $path 扫描目录
     * @param array $data 额外数据
     * @param string $ext 有文件后缀
     * @return array
     */
    private function _scanDirectory($path, $data = [], $ext = 'php')
    {
        foreach (glob("{$path}*") as $item) {
            if (is_dir($item)) {
                $data = array_merge($data, $this->_scanDirectory("{$item}/"));
            } elseif (is_file($item) && pathinfo($item, PATHINFO_EXTENSION) === $ext) {
                $data[] = strtr($item, '\\', '/');
            }
        }
        return $data;
    }
}