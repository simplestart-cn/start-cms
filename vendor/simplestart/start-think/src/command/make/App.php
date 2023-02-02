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

namespace start\command\make;

use start\command\Make;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;

class App extends Make
{
    
    /**
     * 应用基础目录
     * @var string
     */
    protected $basePath;

    protected $type = "App";

    protected function getStub(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('make:app')
            ->addOption('apidoc', null, Option::VALUE_NONE, 'Generate apidoc config.')
            ->addOption('common', null, Option::VALUE_NONE, 'Generate common class.')
            ->setDescription('Create a new app dirs');
    }

    protected function execute(Input $input, Output $output)
    {
        $name            = $input->getArgument('name') ?: '';
        $configPath     = $this->app->getConfigPath();
        $this->basePath = $this->app->getBasePath();

        if (empty($name)) {
            $output->writeln("<error>Name can not empty</error>");
            die;
        }
        if (is_file($configPath . 'build.php')) {
            $list = include $configPath . 'build.php';
        } else {
            $list = [
                '__dir__' => ['controller', 'model', 'service'],
            ];
        }

        $this->buildApp($name, $list);
        $output->writeln("<info>Successed</info>");
    }

    /**
     * 创建应用
     * @access protected
     * @param  string $app  应用名
     * @param  array  $list 目录结构
     * @return void
     */
    protected function buildApp(string $app, array $list = []): void
    {
        $appPath   = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '');
        $namespace = 'app' . ($app ? '\\' . $app : '');

        // 创建应用目录
        if (!is_dir($this->basePath . $app)) {
            mkdir($this->basePath . $app);
        }

        // 创建应用配置
        $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'app.json.stub');
        $appjson = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'app.json';
        $content = str_replace(['{%appName%}',  '{%namespace%}'], [$app,$namespace], $stub);
        file_put_contents($appjson, $content);

        // 创建模块的默认页面
        $this->buildHello($app, $namespace);

        // 创建配置文件和公共文件
        if ($this->input->getOption('common')) {
            $this->buildCommon($app);
        }

        // 创建接口文档配置文件
        if ($this->input->getOption('apidoc')) {
            $this->buildApidoc($app);
        }
        

        foreach ($list as $path => $file) {
            if ('__dir__' == $path) {
                // 生成子目录
                foreach ($file as $dir) {
                    $this->checkDirBuild($appPath . $dir);
                }
            } elseif ('__file__' == $path) {
                // 生成（空白）文件
                foreach ($file as $name) {
                    if (!is_file($appPath . $name)) {
                        file_put_contents($appPath . $name, 'php' == pathinfo($name, PATHINFO_EXTENSION) ? '<?php' . PHP_EOL : '');
                    }
                }
            } else {
                // 生成相关MVC文件
                foreach ($file as $val) {
                    $val      = trim($val);
                    $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . '.php';
                    $space    = $namespace . '\\' . $path;
                    $class    = $val;
                    switch ($path) {
                        case 'controller': // 控制器
                            if ($this->app->config->get('route.controller_suffix')) {
                                $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . 'Controller.php';
                                $class    = $val . 'Controller';
                            }
                            $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'controller.stub');
                            $content = $this->stub_replace($stub, $app, $class, $space);
                            break;
                        case 'service': // 服务
                            $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'service.stub');
                            $content = $this->stub_replace($stub, $app, $class, $space);
                            break;
                        case 'model': // 模型
                            $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'model.stub');
                            $content = $this->stub_replace($stub, $app, $class, $space);
                            break;
                        case 'view': // 视图
                            $sufixx = $this->app->config->get('view.view_suffix');
                            $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . $sufixx;
                            $this->checkDirBuild(dirname($filename));
                            $content = '';
                            break;
                        default:
                            // 其他文件
                            $content = "<?php" . PHP_EOL . "namespace {$space};" . PHP_EOL . PHP_EOL . "class {$class}" . PHP_EOL . "{" . PHP_EOL . PHP_EOL . "}";
                    }

                    if (!is_file($filename)) {
                        file_put_contents($filename, $content);
                    }
                }
            }
        }
    }

    /**
     * 创建应用的欢迎页面
     * @access protected
     * @param  string $app 目录
     * @param  string $namespace 类库命名空间
     * @return void
     */
    protected function buildHello(string $app, string $namespace): void
    {
        $suffix   = $this->app->config->get('route.controller_suffix') ? 'Controller' : '';
        $filename = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'controller' . DIRECTORY_SEPARATOR . 'Index' . $suffix . '.php';

        if (!is_file($filename)) {
            $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'controller.plain.stub');
            $content = $this->stub_replace($app, $namespace . '\controller', 'Index', $stub);
            $this->checkDirBuild(dirname($filename));
            file_put_contents($filename, $content);
        }
    }

    /**
     * 创建应用的公共文件
     * @access protected
     * @param  string $app 目录
     * @return void
     */
    protected function buildCommon(string $app): void
    {
        $appPath = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '');

        if (!is_file($appPath . 'common.php')) {
            file_put_contents($appPath . 'common.php', "<?php" . PHP_EOL . "// 这是系统自动生成的公共文件" . PHP_EOL);
        }

        foreach (['event', 'middleware', 'common'] as $name) {
            if (!is_file($appPath . $name . '.php')) {
                file_put_contents($appPath . $name . '.php', "<?php" . PHP_EOL . "// 这是系统自动生成的{$name}定义文件" . PHP_EOL . "return [" . PHP_EOL . PHP_EOL . "];" . PHP_EOL);
            }
        }
    }

    /**
     * 创建应用的公共文件
     * @access protected
     * @param  string $app 目录
     * @return void
     */
    protected function buildApidoc(string $app): void
    {
        $filename = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'apidoc.json';
        if (!is_file($filename)) {
            $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'apidoc.json.stub');
            $content = str_replace(['{%appName%}'], [ucfirst($app)], $stub);
            file_put_contents($filename, $content);
        }

        $filename = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'apidoc.md';
        if (!is_file($filename)) {
            $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'apidoc.md.stub');;
            file_put_contents($filename, $stub);
        }

        $filename = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'apidoc.php';
        if (!is_file($filename)) {
            $stub = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'apidoc.stub');
            file_put_contents($filename, $stub);
        }
    }

    /**
     * 创建目录
     * @access protected
     * @param  string $dirname 目录名称
     * @return void
     */
    protected function checkDirBuild(string $dirname): void
    {
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
    }
}
