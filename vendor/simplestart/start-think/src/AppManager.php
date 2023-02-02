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

use PhpZip\ZipFile;
use PhpZip\Exception\ZipException;
use start\extend\HttpExtend;
use start\service\AuthService;
use start\service\ConfigService;
use think\facade\Config;

/**
 * App管理器
 */
class AppManager extends Service
{

    public $model = 'start\model\App';

    /**
     * 代码地址
     * @var string
     */
    protected $api;

    /**
     * 项目根目录
     * @var string
     */
    protected $path;

    /**
     * 通信令牌
     * @var string
     */
    protected $token;

    /**
     * 当前版本号
     * @var string
     */
    protected $version;

    /**
     * 文件规则
     * @var array
     */
    protected $rules = [];

    /**
     * 忽略规则
     * @var array
     */
    protected $ignore = [];

    /**
     * 初始化服务
     * @return $this
     */
    protected function initialize()
    {
        // 服务地址
        $this->api = $this->app->config->get('base.api');
        // 框架令牌
        $this->token = $this->getToken();
        // 框架目录
        $this->path = root_path();
        // 框架版本
        $this->version = $this->app->config->get('base.version');
        if (empty($this->version)) {
            $this->version = 'last';
        }
        return $this;
    }

    /**
     * 获取应用
     * @param  array  $filter [description]
     * @param  array  $order  [description]
     * @return [type]         [description]
     */
    public static function getPage($filter = [], $order = [], $with = null)
    {
        $data = [];
        $total = 0;
        $per_page = 15;
        $cur_page = 1;
        $last_page = 1;
        $page = $filter['page'] ?? 1;
        $type = $filter['type'] ?? '';
        $price = $filter['price'] ?? '';
        $keyword = $filter['keyword'] ?? '';
        $category = $filter['category'] ?? '';
        $installed  = self::getInstalled();
        $downloaded = self::getDownloaded();
        switch ($type) {
                // 已下载的
            case 'downloaded':
                $list = self::_filterApps($downloaded, $keyword, $category);
                $total = count($list);
                break;
                // 已安装的
            case 'installed':
                $list = self::_filterApps($installed, $keyword, $category);
                $total = count($list);
                break;
                // 未安装的
            case 'uninstall':
                $list = array_diff_key($downloaded, $installed);
                $list = $list = self::_filterApps($list, $keyword, $category);
                $total = count($list);
                break;
            default:
                // 应用中心
                $apps = self::fetchStoreApps(compact('page', 'price', 'keyword', 'category'));
                $total = $apps['total'];
                $list = array_combine(array_column($apps['data'], 'name'), array_values($apps['data']));
                if (empty($list)) {
                    $list = self::_filterApps($downloaded, $keyword, $category);
                    $total = count($list);
                }
                break;
        }
        // 本地应用补充
        if (count($list) > $per_page) {
            $list = array_slice($list, ($page - 1) * $per_page, $per_page);
            $cur_page = intval($page);
            $last_page = ceil($total / $per_page);
        }
        // 状态检测
        foreach ($list as $name => &$app) {
            $app['installed'] = false;
            $app['downloaded'] = false;
            $app['upgradeable'] = false;
            if (!isset($app['version'])) {
                throw_error($app['name'] . '应用配置错误: app.json error');
            }
            if (isset($downloaded[$name])) {
                $app['downloaded'] = true;
                if ($downloaded[$name]['version'] !== $app['version']) {
                    $app['upgradeable']   = true;
                }
            }
            if (isset($installed[$name])) {
                $app['status'] = true;
                $app['installed'] = true;
                // 健康状态
                if (!empty($app['health'] ?? '')) {
                    // 检查地址支持http://xxxxxx或者tcp:IP地址+:端口
                    // 如果没有配置健康状态检查地址则被认为是无状态应用服务
                    $protocol = strtolower(substr($app['health'], 0, strripos($app['health'], ":")));
                    if (!in_array($protocol, ['http', 'tcp'])) {
                        throw_error('健康检查地址仅支持http或者tcp协议');
                    }
                    // ... 待完善
                }
            }
        }
        $data = array_values($list);
        return compact('data', 'total', 'per_page', 'cur_page', 'last_page');
    }

    /**
     * 获取token
     * @return string
     */
    private function getToken()
    {
        $runtime = rtrim(runtime_path(), DIRECTORY_SEPARATOR);
        $token = substr($runtime, 0, strrpos($runtime, DIRECTORY_SEPARATOR) + 1) . 'session' . DIRECTORY_SEPARATOR . 'cms_token';
        if (is_file($token)) {
            return @file_get_contents($token);
        }
        return '';
    }

    /**
     * 设置Token
     * @param string $token
     * @return boolean
     */
    private function setToken(string $token)
    {
        $runtime = rtrim(runtime_path(), DIRECTORY_SEPARATOR);
        $session = substr($runtime, 0, strrpos($runtime, DIRECTORY_SEPARATOR) + 1) . 'session' . DIRECTORY_SEPARATOR;
        if (!is_dir($session)) {
            mkdir($session, 0755, true);
        }
        $tokenPath = $session . 'cms_token';
        $result = @file_put_contents($tokenPath, $token);
        if (!$result) {
            throw_error('No write permission:runtime/session');
        }
        return true;
    }

    /**
     * 重置Token
     * @return boolean
     */
    private function resetToken()
    {
        $runtime = rtrim(runtime_path(), DIRECTORY_SEPARATOR);
        $token = substr($runtime, 0, strrpos($runtime, DIRECTORY_SEPARATOR) + 1) . 'session' . DIRECTORY_SEPARATOR . 'cms_token';
        if (is_file($token)) {
            return unlink($token);
        }
        return true;
    }


    /**
     * 获取框架
     * @return void
     */
    public function getStore()
    {
        $options = [
            'timeout'         => 30,
            'connect_timeout' => 30,
            'verify'          => false,
            'http_errors'     => false,
            'headers'         => [
                'Referer' => dirname(request()->root(true)),
                'X-REQUESTED-WITH' => 'XMLHttpRequest',
                'User-Agent' => 'CmsClient',
                'User-Token' => $this->token
            ]
        ];
        $result = [
            'title'   => 'StartCMS',
            'version' => $this->version,
            'upgradeable' => false
        ];
        try {
            $api = $this->api . '/core/user/state';
            $response = HttpExtend::get($api, [], $options);
            $response = json_decode($response, true);
            if ($response['code'] !== 0) {
                $this->resetToken();
            } else {
                $user = $response['data']['info'];
                $result['user'] = [
                    'uuid'   => $user['uuid'],
                    'name'   => $user['name'],
                    'avater' => $user['avatar']
                ];
            }
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    /**
     * 获取验证信息
     * @param [type] $input
     * @return void
     */
    public function getCaptcha(array $params)
    {
        $type = $params['type'] ?? 'image';
        $params['cms_frame'] = 'ThinkPHP';
        $params['cms_version'] = $this->version;
        $options = [
            'timeout'         => 30,
            'connect_timeout' => 30,
            'verify'          => false,
            'http_errors'     => false,
            'headers'         => [
                'Referer' => dirname(request()->root(true)),
                'X-REQUESTED-WITH' => 'XMLHttpRequest',
                'User-Agent' => 'CmsClient'
            ]
        ];
        try {
            if ($type == 'image') {
                $api = $this->api . '/core/captcha/image';
            } else {
                $api = $this->api . '/core/captcha/code';
            }
            $response = HttpExtend::get($api, $params, $options);
            $response = json_decode($response, true);
            if ($response['code'] !== 0) {
                throw_error($response['msg']);
            }
            return $response['data'];
        } catch (\HttpResponseException $e) {
            throw_error($e->getMessage());
        }
    }

    /**
     * 登录应用中心
     * @param array $input
     * @return array
     */
    public function loginStore(array $params)
    {
        $params['cms_frame'] = 'ThinkPHP';
        $params['cms_version'] = $this->version;
        $options = [
            'timeout'         => 30,
            'connect_timeout' => 30,
            'verify'          => false,
            'http_errors'     => false,
            'headers'         => [
                'Referer' => dirname(request()->root(true)),
                'X-REQUESTED-WITH' => 'XMLHttpRequest',
                'User-Agent' => 'CmsClient'
            ]
        ];
        try {
            $api = $this->api . '/core/user/login';
            $response = HttpExtend::post($api, $params, $options);
            $response = json_decode($response, true);
            if ($response['code'] !== 0) {
                throw_error($response['msg']);
            }
            $user = $response['data'];
            $this->setToken($user['token']);
            return [
                'user'    => [
                    'uuid' => $user['uuid'],
                    'name' => $user['name'],
                    'avatar' => $user['avatar']
                ],
                'title'   => 'StartCMS',
                'frame'   => $this->app->config->get('base.frame'),
                'version' => $this->version,
            ];
            return $response['data'];
        } catch (\HttpResponseException $e) {
            throw_error($e->getMessage());
        }
    }

    /**
     * 注册应用中心
     * @param [type] $input
     * @return void
     */
    public function registerStore($params)
    {
        $params['cms_frame'] = 'ThinkPHP';
        $params['cms_version'] = $this->version;
        $options = [
            'timeout'         => 30,
            'connect_timeout' => 30,
            'verify'          => false,
            'http_errors'     => false,
            'headers'         => [
                'Referer' => dirname(request()->root(true)),
                'X-REQUESTED-WITH' => 'XMLHttpRequest',
                'User-Agent' => 'CmsClient'
            ]
        ];
        try {
            $api = $this->api . '/core/user/register';
            $response = HttpExtend::post($api, $params, $options);
            $response = json_decode($response, true);
            if ($response['code'] !== 0) {
                throw_error($response['msg']);
            }
            return $response['data'];
        } catch (\HttpResponseException $e) {
            throw_error($e->getMessage());
        }
    }

    /**
     * 获取应用
     * @param [type] $filter
     * @return void
     */
    public static function fetchStoreApps($filter = [])
    {
        return array(
            'data'         => [],
            'total'        => 0,
            'cur_page'     => $filter['page'] ?? 1,
            'per_page'     => 15,
            'last_page'    => 1,
        );
    }

    /**
     * 已启用
     * @return [type] [description]
     */
    public static function getActive()
    {
        return self::model()->where('status', 1)->column('name');
    }

    /**
     * 获取已安装
     * @return [type] [description]
     */
    public static function getInstalled()
    {
        $data = self::model()->select()->toArray();
        return array_combine(array_column($data, 'name'), array_values($data));
    }

    /**
     * 获取已下载
     * @return [type] [description]
     */
    public static function getDownloaded($filter = [])
    {
        $apps     = [];
        $basePath = base_path();
        foreach (self::_scanApps($basePath) as $file) {
            if (preg_match("|(\w+)/app.json$|i", $file, $matches)) {
                list($path, $name) = $matches;
                $info              = json_decode(file_get_contents($basePath . DIRECTORY_SEPARATOR . $path), true);
                $info['name']      = strtolower($name);
                $apps[]            = $info;
            }
        }
        $apps = array_combine(array_column($apps, 'name'), array_values($apps));
        return $apps;
    }

    /**
     * 升级配置
     * @param  array  $name  [description]
     * @return boolea        [description]
     */
    public static function upgradeConfig($name)
    {
        $app = self::getPackInfo($name);
        if (isset($app['config']) && count($app['config'])) {
            $config = $app['config'];
            foreach ($config as &$conf) {
                $conf['app']        = strtolower($conf['app'] ?? $app['name']);
                $conf['app_title']  = strtolower($conf['app_title'] ?? $app['title']);
                $where['app']       = $conf['app'];
                $where['field']     = $conf['field'];
                $model = ConfigService::getInfo($where);
                if ($model && $model->id) {
                    unset($conf['value']);
                    $model->save($conf);
                } else {
                    ConfigService::create($conf);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 本地上传
     * @param string $filename
     * @param string $action
     * @param boolean $cover
     * @return void
     */
    public function upload($filename, $action = 'upload', $cover = false)
    {
        if ($action == 'upload') {
            // 接收文件
            $file = \request()->file($filename);
            if (empty($file)) {
                throw_error('未找到上传文件的信息');
            }
            // 文件信息
            $info = $_FILES[$filename];
            // 临时目录
            $downDir = $this->getDownloadDir();
            // 临时文件名
            $tempName = date('YmdHis') . '-' . $info['name'];
            // 临时文件地址
            $tempPath = $downDir . $tempName;
            // 验证文件并上传
            $fileInfo = $file->move($downDir, $tempName);
            if (empty($fileInfo)) {
                throw_error($file->getError());
                return false;
            }
            // 解压应用文件到临时目录
            $tempDir = self::unpack($tempPath);
            $tempFile = self::_scanApps($tempDir);
            $tempJson = array_filter($tempFile, function ($item) {
                return substr($item, -8) === 'app.json';
            });
            if (empty($tempJson)) {
                unlink($tempPath);
                self::_removeFolder($tempDir);
                throw_error('配置有误,应用描述文件(app.json)不存在');
            }
            $tempApp = substr($tempJson[0], 0, -8);
            // 获取应用基础信息
            $appInfo = json_decode(file_get_contents($tempApp . 'app.json'), true);
            if (empty($appInfo['name'] ?? '')) {
                throw_error('未知应用名称');
            }
            $appName = strtolower($appInfo['name']);
            $appPath = base_path() . $appName . DIRECTORY_SEPARATOR;
            if (is_dir($appPath)) {
                try {
                    $cover =  json_decode(file_get_contents($appPath . 'app.json'), true);
                    return [
                        'step' => 2,
                        'cover' => true,
                        'message'  => '已存在同名应用，是否覆盖安装？ 如果不是同一个开发者，请对原应用进行备份！如果是同一个开发者，请确保本次覆盖不影响应用服务！',
                        'appInfo' => [
                            'name'    => $appInfo['name'],
                            'path'    => $tempPath,
                            'title'   => !empty($appInfo['title'] ?? '') ? $appInfo['title'] : '未知',
                            'author'  => !empty($appInfo['author'] ?? '') ? $appInfo['author'] : '未知',
                            'version' => !empty($appInfo['version'] ?? '') ? $appInfo['version'] : '未知',
                        ],
                        'coverInfo' => [
                            'name'    => $cover['name'],
                            'path'    => $appPath,
                            'title'   => !empty($cover['title'] ?? '') ? $cover['title'] : '未知',
                            'author'  => !empty($cover['author'] ?? '') ? $cover['author'] : '未知',
                            'version' => !empty($cover['version'] ?? '') ? $cover['version'] : '未知',
                        ]
                    ];
                } catch (\Throwable $th) {
                    throw_error('存在未知信息同名应用');
                }
            }
            // 复制到应用目录
            if (!$this->_copyDir($tempApp, $appPath)) {
                throw_error('无法写入app目录');
            }
            // 删除临时文件
            unlink($tempPath);
            self::_removeFolder($tempDir);
            // 返回应用信息
            return [
                'step' => 3,
                'message'  => '已上传成功，是否立即安装？',
                'appInfo' => [
                    'name'    => $appInfo['name'],
                    'path'    => $appPath,
                    'title'   => !empty($appInfo['title'] ?? '') ? $appInfo['title'] : '未知',
                    'author'  => !empty($appInfo['author'] ?? '') ? $appInfo['author'] : '未知',
                    'version' => !empty($appInfo['version'] ?? '') ? $appInfo['version'] : '未知',
                ]
            ];
        } else if ($action == 'cover') {
            // 覆盖安装
            if (!$cover) {
                unlink($filename);
                $tempApp = substr($filename, 0, strrpos($filename, '.')) . DIRECTORY_SEPARATOR;
                self::_removeFolder($tempApp);
                return true;
            } else {
                // 解压到临时目录
                $tempDir = self::unpack($filename);
                $tempFile = self::_scanApps($tempDir);
                $tempJson = array_filter($tempFile, function ($item) {
                    return substr($item, -8) === 'app.json';
                });
                $tempApp = substr($tempJson[0], 0, -8);
                // 获取应用基础信息
                $appInfo = json_decode(file_get_contents($tempApp . 'app.json'), true);
                $appName = strtolower($appInfo['name']);
                $appPath = base_path() . $appName . DIRECTORY_SEPARATOR;
                // 覆盖的应用信息
                $cover =  json_decode(file_get_contents($appPath . 'app.json'), true);
                // 解压到应用目录
                if (!$this->_copyDir($tempApp, $appPath)) {
                    throw_error('无法写入app目录');
                }
                // 删除临时文件
                unlink($filename);
                self::_removeFolder($tempDir);
                // 返回应用信息
                return [
                    'step' => 3,
                    'cover' => true,
                    'message'  => '已上传成功，是否立即安装？',
                    'appInfo' => [
                        'name'    => $appInfo['name'],
                        'path'    => $appPath,
                        'title'   => !empty($appInfo['title'] ?? '') ? $appInfo['title'] : '未知',
                        'author'  => !empty($appInfo['author'] ?? '') ? $appInfo['author'] : '未知',
                        'version' => !empty($appInfo['version'] ?? '') ? $appInfo['version'] : '未知',
                    ],
                    'coverInfo' => [
                        'name'    => $cover['name'],
                        'path'    => $appPath,
                        'title'   => !empty($cover['title'] ?? '') ? $cover['title'] : '未知',
                        'author'  => !empty($cover['author'] ?? '') ? $cover['author'] : '未知',
                        'version' => !empty($cover['version'] ?? '') ? $cover['version'] : '未知',
                    ]
                ];
            }
        } else {
            if (is_file($filename)) {
                unlink($filename);
            }
            return true;
        }
    }

    /**
     * 下载(待完成)
     * @param  [type] $app [description]
     * @return [type]      [description]
     */
    public static function download($name, $version)
    {
        $service = self::instance();
        $tempDir = self::getDownloadDir();
        $tmpFile = $tempDir . $name . ".zip";
        try {
            $api = $service->api . '/appstore/download';
            $params = [
                'app' => $name,
                'app_version' => $version,
                'cms_version' => $service->version,
            ];
            $options = [
                'timeout'         => 30,
                'connect_timeout' => 30,
                'verify'          => false,
                'http_errors'     => false,
                'headers'         => [
                    'Referer' => dirname(request()->root(true)),
                    'X-REQUESTED-WITH' => 'XMLHttpRequest',
                    'User-Agent' => 'CmsClient',
                    'User-Token' => $service->token
                ]
            ];
            $response = HttpExtend::get($api, $params, $options);
            $response = json_decode($response, true);
            if ($response['code'] !== 0) {
                throw_error($response['msg']);
            }
            $content = $response['data'];
        } catch (\Exception $e) {
            throw_error($e->getMessage());
        }
        // 保存文件信息
        if ($write = fopen($tmpFile, 'w')) {
            fwrite($write, $content);
            fclose($write);
            return $tmpFile;
        }
        throw_error(lang('No permission to write temporary files'));
    }

    /**
     * 安装
     * @param  [type] $name [description]
     * @return [type]      [description]
     */
    public static function install($name)
    {
        $app = self::getPackInfo($name);
        $path = base_path() . $name;
        foreach ($app as $key => $value) {
            if (stripos($key, '-') !== false) {
                $app[str_replace('-', '_', $key)] = $value;
                unset($app[$key]);
            }
        }
        if (self::getInfo(['name' => $name])) {
            throw_error(lang('app_already_exist'));
        }
        $model = self::model();
        self::startTrans();
        try {
            // 执行安装脚本
            $installer = $path . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR . 'install.php';
            if (file_exists($installer)) {
                require_once $installer;
            }
            // 添加默认配置
            if (isset($app['config']) && count($app['config'])) {
                $config = $app['config'];
                foreach ($config as &$conf) {
                    $conf['app']       = strtolower($conf['app'] ?? $app['name']);
                    $conf['app_title'] = strtolower($conf['app_title'] ?? $app['title']);
                }
                ConfigService::model()->saveAll($config);
            }
            // 构建权限菜单
            AuthService::instance()->building($app['name']);
            // 添加应用记录
            if ($name != 'core') {
                $model->save($app);
            }
            self::startCommit();
            return $model;
        } catch (Exception $e) {
            self::startRollback();
            throw_error($e->getMessage());
            return false;
        }
    }

    /**
     * 升级(待完成)
     * @param  [type] $app [description]
     * @return [type]      [description]
     */
    public static function upgrade($name, $version)
    {
        $app = self::getInfo(['name' => $name]);
        $appPath = base_path() . $name . DIRECTORY_SEPARATOR;
        if ($app['status']) {
            throw_error(lang('app_is_runing'));
        }
        // 下载应用
        $tmpFile = self::download($name, $version);
        // 备份应用
        self::backup($name);
        try {
            // 删除旧版
            self::_removeFolder($appPath);
            // 解压应用文件
            self::unpack($tmpFile, $appPath);
            // 执行升级脚本
            $upgrader = $appPath . 'installer' . DIRECTORY_SEPARATOR . 'upgrade.php';
            if (file_exists($upgrader)) {
                require_once $upgrader;
            }
            // 升级配置信息
            self::upgradeConfig($name);
            // 刷新权限菜单
            AuthService::instance()->building($app['name']);
            // 更新应用信息
            $info = self::getPackInfo($name);
            $app->save($info);
            return $info;
        } catch (\Exception $e) {
            throw_error($e->getMessage());
        } finally {
            // 移除临时文件
            @unlink($tmpFile);
        }
    }

    /**
     * 卸载(待完成)
     * @param  [type] $app [description]
     * @return [type]      [description]
     */
    public static function uninstall($name)
    {
        $app = self::getPackInfo($name);
        $path = base_path() . $name;

        $model = self::getInfo(['name' => $name]);
        if (!$model) {
            throw_error(lang('app_does_not_exist'));
        }

        self::startTrans();
        try {
            // 执行卸载脚本
            $uninstaller = $path . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR . 'uninstall.php';
            if (file_exists($uninstaller)) {
                require_once $uninstaller;
            }
            // 删除权限菜单
            AuthService::model()->where(['app' => $name])->delete();
            // 删除应用配置
            ConfigService::model()->where(['app' => $name])->delete();
            // 删除应用记录
            $model->remove();
            self::startCommit();
            return $model;
        } catch (Exception $e) {
            self::startRollback();
            throw_error($e->getMessage());
            return false;
        }
    }

    /**
     * 删除安装包
     * @param  string  $name [description]
     * @return [type]        [description]
     */
    public static function remove($name, $force = false)
    {
        // 删除对应数据表
        // ....
        // ...
        // 删除应用记录
        self::model()->where(['name' => $name])->delete();
        // 删除应用目录
        $path = base_path() . $name . DIRECTORY_SEPARATOR;
        return self::_removeFolder($path);
    }

    /**
     * 删除文件或文件夹
     * @param  string $path [description]
     * @return [type]       [description]
     */
    private static function _removeFolder($path)
    {
        if (is_dir($path)) {
            if (!$handle = @opendir($path)) {
                throw_error($handle);
            }
            while (false !== ($file = readdir($handle))) {
                if ($file !== "." && $file !== "..") {
                    //排除当前目录与父级目录
                    $file = $path . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($file)) {
                        self::_removeFolder($file);
                        //目录清空后删除空文件夹
                        @rmdir($file . DIRECTORY_SEPARATOR);
                    } else {
                        @unlink($file);
                    }
                }
            }
            try {
                return rmdir($path);
            } catch (\Exception $e) {
                $msg = explode(': ', $e->getMessage())[1];
                throw_error($msg);
            }
        }
        if (is_file($path)) {
            try {
                return unlink($path);
            } catch (\Exception $e) {
                $msg = explode(': ', $e->getMessage())[1];
                throw_error($msg);
            }
        }
        return true;
    }

    /**
     * 是否已下载
     * @param  string $app [description]
     * @return array      [description]
     */
    public static function isDownload($name)
    {
        $apps = self::getDownloaded();
        return !!isset($apps[$name]);
    }

    /**
     * 是否已安装
     * @param  [type] $app [description]
     * @return [type]      [description]
     */
    public static function isInstall($name)
    {
        $apps = self::getInstalled();
        return !!isset($apps[strtolower($name)]);
    }

    /**
     * 是否已启用
     * @param  [type] $app [description]
     * @return [type]      [description]
     */
    public static function isActive($name)
    {
        $apps = self::getInstalled();
        if (isset($apps[strtolower($name)])) {
            return !!$apps[strtolower($name)]['status'];
        }
        return false;
    }

    /**
     * 获取包信息
     * @param  string $name [description]
     * @return [type]       [description]
     */
    public static function getPackInfo($name)
    {
        if ($name === 'core') {
            $path         = root_path() . $name . DIRECTORY_SEPARATOR . 'app.json';
        } else {
            $path         = base_path() . $name . DIRECTORY_SEPARATOR . 'app.json';
        }
        if (!is_file($path)) {
            return false;
        }
        $info         = json_decode(file_get_contents($path), true);
        $info['name'] = strtolower($name);
        return $info;
    }

    /**
     * 获取所有应用名称
     * @return [type] [description]
     */
    public static function getApps()
    {
        $path = base_path();
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
     * 获取备份目录
     * @return string
     */
    private static function getBackupDir()
    {
        $dir = runtime_path() . 'backup' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * 获取下载目录
     * @return string
     */
    private static function getDownloadDir()
    {
        $dir = runtime_path() . 'download' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * 应用备份
     * @param  string $name
     * @return boolean
     */
    public static function backup($name)
    {
        $appPath = base_path() . $name;
        $backupPath = self::getBackupDir();
        $file = $backupPath . $name . '-backup-' . date("YmdHis") . '.zip';
        $zip = new ZipFile();
        try {
            $zip->addDirRecursive($appPath)
                ->saveAsFile($file)
                ->close();
        } catch (ZipException $e) {
            throw_error($e->getMessage());
        } finally {
            $zip->close();
        }
        return true;
    }

    /**
     * 解压文件(默认当前目录)
     * @param string $filePath
     * @param string $targetPath
     * @return string $targetPath
     */
    private static function unpack(string $filePath, $targetPath = '')
    {
        if (!is_file($filePath)) {
            throw_error('File Not Found');
        }
        if (empty($targetPath)) {
            $targetPath = substr($filePath, 0, strrpos($filePath, '.')) . DIRECTORY_SEPARATOR;
        }
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        $zip = new ZipFile();
        try {
            $zip->openFile($filePath);
        } catch (ZipException $e) {
            $zip->close();
            throw_error($e->getMessage());
        }
        try {
            $zip->extractTo($targetPath);
        } catch (ZipException $e) {
            throw_error($e->getMessage());
        } finally {
            $zip->close();
        }
        return $targetPath;
    }



    /**
     * 获取本地应用
     * @param string $path 扫描目录
     * @param string $ext 文件后缀
     * @return array
     */
    private static function _scanApps($path, $ext = 'json')
    {
        $data = [];
        foreach (glob("{$path}*") as $item) {
            if (is_dir($item) && stripos($item, 'node_modules') === false) {
                $data = array_merge($data, self::_scanApps("{$item}/"));
            } elseif (is_file($item) && pathinfo($item, PATHINFO_EXTENSION) === $ext) {
                $data[] = strtr($item, '\\', '/');
            }
        }
        return $data;
    }


    /**
     * 文件夹文件拷贝
     *
     * @param string $src 来源文件夹
     * @param string $dst 目的地文件夹
     * @return bool
     */
    private function _copyDir($sourceDir = '', $destDir = '')
    {
        if(!is_dir($sourceDir)){
            return false;
        }
        if(!is_dir($destDir)){
            if(!mkdir($destDir)){
                return false;
            }
        }
        $dir = opendir($sourceDir);
        if(!$dir){
            return false;
        }
        while(false !== ($file=readdir($dir))){
            if($file != '.' && $file != '..'){
                if(is_dir($sourceDir.'/'.$file)){
                    if(!$this->_copyDir($sourceDir.'/'.$file,$destDir.'/'.$file)) {
                        return false;
                     }
                    }else{
                        if(!copy($sourceDir.'/'.$file,$destDir.'/'.$file)){
     
                        }
     
                }
            }
        }
        closedir($dir);
        return true;
    }

    /**
     * 过滤本地应用
     * @param array $list
     * @param string $keyword
     * @param string $category
     * @return array
     */
    private static function _filterApps(array $list, $keyword = '', $category = '')
    {
        if ($keyword) {
            $list = array_filter($list, function ($item) use ($keyword) {
                if (!empty($item['title'] ?? '')) {
                    if (stripos($item['title'], $keyword) !== false) {
                        return true;
                    }
                }
                if (!empty($item['summary'] ?? '')) {
                    if (stripos($item['summary'], $keyword) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }
        if (!empty($category)) {
            $list = array_filter($list, function ($item) use ($category) {
                $arr = [];
                if (!empty($item['category'] ?? '')) {
                    if (!is_array($item['category'])) {
                        $arr = explode(',', $item['category']);
                    } else {
                        $arr = $item['category'];
                    }
                }
                if (in_array($category, $arr)) {
                    return true;
                }
                return false;
            });
        }
        return $list;
    }

    /**
     * 同步更新文件
     * @param array $file
     * @return array
     */
    public function fileSynchronization($file)
    {
        if (in_array($file['type'], ['add', 'mod'])) {
            if ($this->downloadFile(encode($file['name']))) {
                return [true, $file['type'], $file['name']];
            } else {
                return [false, $file['type'], $file['name']];
            }
        } elseif (in_array($file['type'], ['del'])) {
            $real = $this->path . $file['name'];
            if (is_file($real) && unlink($real)) {
                $this->removeEmptyDirectory(dirname($real));
                return [true, $file['type'], $file['name']];
            } else {
                return [false, $file['type'], $file['name']];
            }
        }
    }

    /**
     * 下载更新文件内容
     * @param string $encode
     * @return boolean|integer
     */
    private function downloadFile($encode)
    {
        $service = self::instance();
        $result = json_decode(HttpExtend::get("{$service->api}/appStore/upgrade/get&encode={$encode}"), true);
        if (empty($result['code'])) {
            return false;
        }

        $filename = $this->path . decode($encode);
        file_exists(dirname($filename)) || mkdir(dirname($filename), 0755, true);
        return file_put_contents($filename, base64_decode($result['data']['content']));
    }

    /**
     * 清理空目录
     * @param string $path
     */
    private function removeEmptyDirectory($path)
    {
        if (is_dir($path) && count(scandir($path)) === 2 && rmdir($path)) {
            $this->removeEmptyDirectory(dirname($path));
        }
    }

    /**
     * 获取文件差异数据
     * @param array $rules 文件规则
     * @param array $ignore 忽略规则
     * @return array
     */
    public function generateDifference($rules = [], $ignore = [])
    {
        $service = self::instance();
        list($this->rules, $this->ignore, $data) = [$rules, $ignore, []];
        $response = HttpExtend::post("{$service->api}?/appstore/upgrade/tree", [
            'rules' => serialize($this->rules), 'ignore' => serialize($this->ignore),
        ]);
        $result = json_decode($response, true);
        if (!empty($result['code'])) {
            $new = $this->getAppFiles($result['data']['rules'], $result['data']['ignore']);
            foreach ($this->generateDifferenceContrast($result['data']['list'], $new['list']) as $file) {
                if (in_array($file['type'], ['add', 'del', 'mod'])) {
                    foreach ($this->rules as $rule) {
                        if (stripos($file['name'], $rule) === 0) {
                            $data[] = $file;
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 两二维数组对比
     * @param array $serve 线上文件列表信息
     * @param array $local 本地文件列表信息
     * @return array
     */
    private function generateDifferenceContrast(array $serve = [], array $local = [])
    {
        // 数据扁平化
        list($_serve, $_local, $_new) = [[], [], []];
        foreach ($serve as $t) {
            $_serve[$t['name']] = $t;
        }

        foreach ($local as $t) {
            $_local[$t['name']] = $t;
        }

        unset($serve, $local);
        // 线上数据差异计算
        foreach ($_serve as $t) {
            isset($_local[$t['name']]) ? array_push($_new, [
                'type' => $t['hash'] === $_local[$t['name']]['hash'] ? null : 'mod', 'name' => $t['name'],
            ]) : array_push($_new, ['type' => 'add', 'name' => $t['name']]);
        }

        // 本地数据增量计算
        foreach ($_local as $t) {
            if (!isset($_serve[$t['name']])) {
                array_push($_new, ['type' => 'del', 'name' => $t['name']]);
            }
        }

        unset($_serve, $_local);
        usort($_new, function ($a, $b) {
            return $a['name'] !== $b['name'] ? ($a['name'] > $b['name'] ? 1 : -1) : 0;
        });
        return $_new;
    }

    /**
     * 获取文件信息列表
     * @param array $rules 文件规则
     * @param array $ignore 忽略规则
     * @param array $data 扫描结果列表
     * @return array
     */
    public function getAppFiles(array $rules, array $ignore = [], array $data = [])
    {
        // 扫描规则文件
        foreach ($rules as $key => $rule) {
            $name = strtr(trim($rule, '\\/'), '\\', '/');
            $data = array_merge($data, $this->scanFiles("{$this->path}{$name}"));
        }
        // 清除忽略文件
        foreach ($data as $key => $item) {
            foreach ($ignore as $ingore) {
                if (stripos($item['name'], $ingore) === 0) {
                    unset($data[$key]);
                }
            }
        }

        return ['rules' => $rules, 'ignore' => $ignore, 'list' => $data];
    }

    /**
     * 获取目录文件列表
     * @param string $path 待扫描的目录
     * @param array $data 扫描结果
     * @return array
     */
    private function scanFiles($path, $data = [])
    {
        if (file_exists($path)) {
            if (is_dir($path)) {
                foreach (scandir($path) as $sub) {
                    if (strpos($sub, '.') !== 0) {
                        if (is_dir($temp = "{$path}/{$sub}")) {
                            $data = array_merge($data, $this->scanFiles($temp));
                        } else {
                            array_push($data, $this->getFileInfo($temp));
                        }
                    }
                }
            } else {
                return [$this->getFileInfo($path)];
            }
        }
        return $data;
    }

    /**
     * 获取指定文件信息
     * @param string $filename
     * @return array
     */
    private function getFileInfo($filename)
    {
        return [
            'name' => str_replace($this->path, '', $filename),
            'hash' => md5(preg_replace('/\s+/', '', file_get_contents($filename))),
        ];
    }
}
