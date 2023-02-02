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

use think\App;
use think\Request;
use think\exception\HttpResponseException;
use start\helper\TokenHelper;
use start\helper\ValidateHelper;


/**
 * 标准控制器基类
 * Class Controller
 * @package start
 */
abstract class Controller extends \stdClass
{

    /**
     * 应用容器
     * @var App
     */
    public $app;

    /**
     * 请求对象
     * @var Request
     */
    public $request;

    /**
     * 表单CSRF验证状态
     * @var boolean
     */
    public $csrf_state = false;

    /**
     * 表单CSRF验证失败提示
     * @var string
     */
    public $csrf_message;

    /**
     * 控制器中间键
     * @var array
     */
    protected $middleware = [];

    /**
     * Controller constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;
        $this->app->bind('start\Controller', $this);
        if (in_array($this->request->action(), get_class_methods(__CLASS__))) {
            $this->error('Access without permission.', 403);
        }
        $this->csrf_message = lang('csrf_error');
        $this->initialize();
    }

    /**
     * 控制器初始化
     */
    protected function initialize()
    {
    }

    /**
     * 返回失败的操作
     * @param mixed $msg 消息内容
     * @param mixed $data 返回数据
     * @param integer $code 返回代码
     */
    public function error($msg, $data = '{-null-}', $code = 1)
    {
        if ($data === '{-null-}') $data = new \stdClass();
        throw new HttpResponseException(json([
            'code' => $code, 'msg' => $msg, 'data' => $data,
        ]));
    }

    /**
     * 返回成功的操作
     * @param mixed $msg 消息内容
     * @param mixed $data 返回数据
     * @param integer $code 返回代码
     */
    public function success($msg, $data = '{-null-}', $code = 0)
    {

        if ($this->csrf_state) {
            TokenHelper::instance()->clear();
        }
        if(is_array($msg) || is_object($msg) || is_null($msg)){
            $data = $msg;
            $msg = 'ok';
        }
        if ($data === '{-null-}') $data = new \stdClass();
        throw new HttpResponseException(json([
            'code' => $code, 'msg' => $msg, 'data' => $data,
        ]));
    }

    /**
     * URL重定向
     * @param string $url 跳转链接
     * @param integer $code 跳转代码
     */
    public function redirect($url, $code = 301)
    {
        throw new HttpResponseException(redirect($url, $code));
    }

    /**
     * 返回视图内容
     * @param string $tpl 模板名称
     * @param array $vars 模板变量
     * @param string $node CSRF授权节点
     */
    public function fetch($tpl = '', $vars = [], $node = null)
    {
        foreach ($this as $name => $value) $vars[$name] = $value;
        if ($this->csrf_state) {
            TokenHelper::instance()->fetchTemplate($tpl, $vars, $node);
        } else {
            throw new HttpResponseException(view($tpl, $vars));
        }
    }

    /**
     * 模板变量赋值
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        if (is_string($name)) {
            $this->$name = $value;
        } elseif (is_array($name)) {
            foreach ($name as $k => $v) {
                if (is_string($k)) $this->$k = $v;
            }
        }
        return $this;
    }

    /**
     * 数据回调处理机制
     * @param string $name 回调方法名称
     * @param mixed $one 回调引用参数1
     * @param mixed $two 回调引用参数2
     * @return boolean
     */
    public function callback($name, &$one = [], &$two = [])
    {
        if (is_callable($name)) return call_user_func($name, $this, $one, $two);
        foreach ([$name, "_{$this->app->request->action()}{$name}"] as $method) {
            if (method_exists($this, $method) && false === $this->$method($one, $two)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 快捷输入并验证（ 支持 规则 # 别名 ）
     * @param array  $rules   验证规则(验证信息数组)
     * @param string $strict  严格模式(过滤未验证参数 )
     * @param string $type    输入方式(post或get)
     * @return array
     */
    protected function formValidate(array $rules=[], $strict = false, $type = '')
    {
        if($type !== ''){
            if(strtolower($this->app->request->method()) !== strtolower(str_replace('.', '', $type))){
                throw_error(lang("method_limit", [strtoupper($type)]));
            }
        }
        return ValidateHelper::instance()->init($rules, $strict, $type);
    }
    
    /**
     * 检查表单令牌验证
     * @param boolean $return 是否返回结果
     * @return boolean
     */
    protected function tokenValidate($return = true)
    {
        return TokenHelper::instance()->init($return);
    }

}
