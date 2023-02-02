<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 刘志淳 <chun@engineer.com>
// +----------------------------------------------------------------------

namespace start\command;

use start\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

abstract class Make extends Command
{
    
    protected $type = '';
    protected $input = null;
    protected $output = null;

    abstract protected function getStub();

    protected function configure()
    {
        $this->addArgument('name', Argument::REQUIRED, "The name of the class");
    }

    protected function execute(Input $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;

        $name = trim($input->getArgument('name'));

        $appname = $this->getAppName($name);

        $namespace = $this->getNamespace($name);

        $classname = $this->getClassName($name);

        $classpath = $this->getClassPath($namespace, $classname);
        
        $this->buildClass($appname, $namespace, $classname, $classpath);
    }

    public function buildClass(string $app, string $namespace, string $classname, string $classpath,  string $stub = '')
    {
        if (!is_dir(dirname($classpath))) {
            mkdir(dirname($classpath), 0755, true);
        }

        if (is_file($classpath)) {
            $this->output->writeln('<error>' . $this->type . ':' . $classpath . ' already exists!</error>');
            return false;
        }

        if(empty($stub)){
            $stub = file_get_contents($this->getStub());
        }else{
            $stub = file_get_contents($stub);
        }

        $content =  $this->stub_replace($app,  $namespace, $classname, $stub);

        file_put_contents($classpath, $content);

        $this->output->writeln('<info>' . $this->type . ':' . $classpath . ' created successfully.</info>');
    }

    protected function stub_replace(string $app,  string $namespace, string $classname, string $stub)
    {
        return str_replace(['{%appName%}', '{%namespace%}', '{%className%}', '{%actionSuffix%}', '{%app_namespace%}'], [
            $app,
            $namespace,
            $classname,
            $this->app->config->get('route.action_suffix'),
            $this->app->getNamespace(),
        ], $stub);
    }

    protected function getAppName(string $name): string
    {
        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
        } else {
            $app = '';
        }
        return $app;
    }

    protected function getClassName(string $name): string
    {
        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
        } else {
            $app = '';
        }
        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }
        
        $sub = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        return  ucfirst(str_replace($sub . '\\', '', $name));
    }

    protected function getClassPath(string $namespace, string $classname): string
    {
        $namespace = str_replace('app\\', '', $namespace);
        $classpath = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $namespace), DIRECTORY_SEPARATOR) .DIRECTORY_SEPARATOR . $classname . '.php';
        return $this->app->getBasePath() . $classpath;
    }

    protected function getNamespace(string $name, string $type = ''): string
    {
        $type = $type ? $type : $this->type;
        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
        } else {
            $app = '';
        }
        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }
        $sub = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
        return 'app' . ($app ? '\\' . $app : '') . ($type ? '\\' . strtolower($type) : '') . ($sub ? '\\' . strtolower($sub) : '');
    }

}
