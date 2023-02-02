<?php

declare(strict_types=1);
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

use Closure;
use think\App as ThinkApp;
use think\exception\HttpException;
use think\Request;
use think\Response;

/**
 * 多应用模式支持
 */
class App
{

    /** @var App */
    protected $app;

    /**
     * 应用名称
     * @var string
     */
    protected $name;

    /**
     * 应用名称
     * @var string
     */
    protected $appName;

    /**
     * 应用路径
     * @var string
     */
    protected $path;

    public function __construct(ThinkApp $app)
    {
        $this->app  = $app;
        $this->name = $this->app->http->getName();
        $this->path = $this->app->http->getPath();
    }

    /**
     * 多应用解析
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        if (!$this->parseMultiApp()) {
            return $next($request);
        }

        return $this->app->middleware->pipeline('app')
            ->send($request)
            ->then(function ($request) use ($next) {
                return $next($request);
            });
    }

    /**
     * 获取路由目录
     * @access protected
     * @return string
     */
    protected function getRoutePath($appName = ''): string
    {
        if ($appName === 'core') {
            return $this->app->getRootPath() . 'core' . DIRECTORY_SEPARATOR . 'route' . DIRECTORY_SEPARATOR;
        }
        return $this->app->getAppPath() . 'route' . DIRECTORY_SEPARATOR;
    }

    /**
     * 解析多应用
     * @return bool
     */
    protected function parseMultiApp(): bool
    {
        $scriptName = $this->getScriptName();
        $defaultApp = $this->app->config->get('app.default_app') ?: 'index';

        if ($this->name || ($scriptName && !in_array($scriptName, ['index', 'router', 'think']))) {
            $appName = $this->name ?: $scriptName;
            $this->app->http->setBind();
        } else {
            // 自动多应用识别
            $this->app->http->setBind(false);
            $appName       = null;
            $this->appName = '';

            $bind = $this->app->config->get('app.domain_bind', []);

            if (!empty($bind)) {
                // 获取当前子域名
                $subDomain = $this->app->request->subDomain();
                $domain    = $this->app->request->host(true);

                if (isset($bind[$domain])) {
                    $appName = $bind[$domain];
                    $this->app->http->setBind();
                } elseif (isset($bind[$subDomain])) {
                    $appName = $bind[$subDomain];
                    $this->app->http->setBind();
                } elseif (isset($bind['*'])) {
                    $appName = $bind['*'];
                    $this->app->http->setBind();
                }
            }

            if (!$this->app->http->isBind()) {
                $path = $this->app->request->pathinfo();
                $map  = $this->app->config->get('app.app_map', []);
                $deny = $this->app->config->get('app.deny_app_list', []);
                $name = current(explode('/', $path));

                if (strpos($name, '.')) {
                    $name = strstr($name, '.', true);
                }
                if (isset($map[$name])) {
                    if ($map[$name] instanceof Closure) {
                        $result  = call_user_func_array($map[$name], [$this->app]);
                        $appName = $result ?: $name;
                    } else {
                        $appName = $map[$name];
                    }
                } elseif ($name && (false !== array_search($name, $map) || in_array($name, $deny))) {
                    throw new HttpException(404, 'app not exists:' . $name);
                } elseif ($name && isset($map['*'])) {
                    $appName = $map['*'];
                } else {

                    $appName = $name ?: $defaultApp;
                    if ($appName === 'core') {
                        $appPath = $this->path ?: $this->app->getRootPath() . $appName . DIRECTORY_SEPARATOR;
                    } else {
                        $appPath = $this->path ?: $this->app->getBasePath() . $appName . DIRECTORY_SEPARATOR;
                    }

                    if (!is_dir($appPath)) {
                        $express = $this->app->config->get('app.app_express', false);
                        if ($express) {
                            $this->setApp($defaultApp);
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
                if ($name) {
                    $this->app->request->setRoot('/' . $name);
                    $this->app->request->setPathinfo(strpos($path, '/') ? ltrim(strstr($path, '/'), '/') : '');
                }
            }
        }

        $this->setApp($appName ?: $defaultApp);
        return true;
    }

    /**
     * 获取当前运行入口名称
     * @access protected
     * @codeCoverageIgnore
     * @return string
     */
    protected function getScriptName(): string
    {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $file = $_SERVER['SCRIPT_FILENAME'];
        } elseif (isset($_SERVER['argv'][0])) {
            $file = realpath($_SERVER['argv'][0]);
        }

        return isset($file) ? pathinfo($file, PATHINFO_FILENAME) : '';
    }

    /**
     * 设置应用
     * @param string $appName
     */
    protected function setApp(string $appName): void
    {
        $this->appName = $appName;
        $this->app->http->name($appName);
        if ($appName === 'core') {
            $appPath = $this->path ?: $this->app->getRootPath() . $appName . DIRECTORY_SEPARATOR;
        } else {
            $appPath = $this->path ?: $this->app->getBasePath() . $appName . DIRECTORY_SEPARATOR;
        }

        $this->app->setAppPath($appPath);
        // 设置应用命名空间
        if ($appName === 'core') {
            $this->app->setNamespace('core');
        } else {
            $this->app->setNamespace($this->app->config->get('app.app_namespace') ?: 'app\\' . $appName);
        }
        if (is_dir($appPath)) {
            $this->app->setRuntimePath($this->app->getRuntimePath() . $appName . DIRECTORY_SEPARATOR);
            $this->app->http->setRoutePath($this->getRoutePath($appName));
            //加载应用
            $this->loadApp($appName, $appPath);
        }
    }

    /**
     * 加载应用文件
     * @param string $appName 应用名
     * @return void
     */
    protected function loadApp(string $appName, string $appPath): void
    {
        $rootPath = $this->app->getRootPath();
        $basePath = $this->app->getBasePath();
        // 加载核心
        if ($appName !== 'core') {
            if (is_file($rootPath . 'core' . DIRECTORY_SEPARATOR . 'common.php')) {
                include_once $rootPath . 'core' . DIRECTORY_SEPARATOR  . 'common.php';
            }
            if (is_file($rootPath . 'core' . DIRECTORY_SEPARATOR . 'middleware.php')) {
                $this->app->middleware->import(include $rootPath . 'core' . DIRECTORY_SEPARATOR . 'middleware.php', 'app');
            }
        }

        // 加载应用函数
        if (is_file($appPath . 'common.php')) {
            include_once $appPath . 'common.php';
        }

        // 加载应用配置
        $files = [];
        $files = array_merge($files, glob($appPath . 'config' . DIRECTORY_SEPARATOR . '*' . $this->app->getConfigExt()));
        foreach ($files as $file) {
            $this->app->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }

        // 加载应用事件
        $apps = $this->app->cache->get('apps', []);
        if (env('APP_DEBUG') || empty($apps)) {
            $apps = AppManager::getApps();
            $this->app->cache->set('apps', $apps);
        }
        if (is_file($rootPath . 'core' . DIRECTORY_SEPARATOR . 'event.php')) {
            $this->loadEvent(include $rootPath . 'core' . DIRECTORY_SEPARATOR  . 'event.php');
        }else{
            $this->autoLoadEvent('core', $rootPath);
        }
        foreach ($apps as $_app) {
            if (is_file($basePath . $_app . DIRECTORY_SEPARATOR . 'event.php')) {
                // 手动注册事件
                $this->loadEvent(include $basePath . $_app . DIRECTORY_SEPARATOR . 'event.php');
            } else {
                // 自动注册事件
                $this->autoLoadEvent($_app, $rootPath);
            }
        }

        // 加载应用中间件
        if (is_file($appPath . 'middleware.php')) {
            $this->app->middleware->import(include $appPath . 'middleware.php', 'app');
        }

        // 加载应用默认语言包
        $this->app->loadLangPack($this->app->lang->defaultLangSet());
    }

    /**
     * 主动注册应用事件
     * @access protected
     * @param array $event 事件数据
     * @return void
     */
    public function loadEvent(array $event): void
    {
        if (isset($event['bind'])) {
            $this->app->event->bind($event['bind']);
        }

        if (isset($event['listen'])) {
            $this->app->event->listenEvents($event['listen']);
        }

        if (isset($event['subscribe'])) {
            $this->app->event->subscribe($event['subscribe']);
        }
    }

    /**
     * 自动注册应用事件
     * @param string $appName
     * @param string $rootPath
     * @return void
     */
    public function autoLoadEvent(string $appName, string $rootPath)
    {
        $event = $rootPath . 'app' . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR . 'event' . DIRECTORY_SEPARATOR;
        $listener = $rootPath . 'app' . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR . 'listener' . DIRECTORY_SEPARATOR;
        $subscribe = $rootPath . 'app' . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR . 'subscribe' . DIRECTORY_SEPARATOR;
        if (is_dir($event)) {
            // 事件绑定
            $event = array_reduce(glob("{$event}*.php"), function ($result, $item) use ($rootPath) {
                $eventName = basename($item, '.php');
                return array_merge($result, [
                    $eventName => str_replace('.php', '', str_replace('/', '\\', str_replace($rootPath, '', $item)))
                ]);
            }, []);
            if (!empty($event)) {
                $this->app->event->bind($event);
            }
        }
        if (is_dir($listener)) {
            // 事件监听
            $listener = array_reduce(glob("{$listener}*.php"), function ($result, $item) use ($rootPath) {
                $eventName = basename($item, '.php');
                return array_merge($result, [
                    $eventName => [str_replace('.php', '', str_replace('/', '\\', str_replace($rootPath, '', $item)))]
                ]);
            }, []);
            if (!empty($listener)) {
                $this->app->event->listenEvents($listener);
            }
        }
        if (is_dir($subscribe)) {
            // 事件订阅
            $subscribe = array_map(function ($item) use ($rootPath) {
                return str_replace('.php', '', str_replace('/', '\\', str_replace($rootPath, '', $item)));
            }, glob("{$subscribe}*.php"));
            if (!empty($subscribe)) {
                $this->app->event->subscribe($subscribe);
            }
        }
    }
}
