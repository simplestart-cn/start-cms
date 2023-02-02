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

/**
 * 自定义数据异常
 * Class Exception
 * @package start
 */
class Exception extends \Exception
{
    /**
     * 异常数据对象
     * @var mixed
     */
    protected $data = [];

    /**
     * Exception constructor.
     * @param string $msg
     * @param integer $code
     * @param mixed $data
     */
    public function __construct($msg = "", $code = 0, $data = [])
    {
        $this->data = $data;
        $this->code = $code;
        $this->msg = $msg;
        parent::__construct($msg, $code);
    }

    /**
     * 设置异常消息
     * @param  mixed $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * 获取异常消息
     */
    public function getMsg()
    {
        return $this->msg;;
    }
    
    /**
     * 设置异常停止数据
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 获取异常停止数据
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

}