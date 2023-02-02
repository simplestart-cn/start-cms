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

use start\AppFacade;
use start\AppManager;
use think\exception\HttpResponseException;
use start\extend\HttpExtend;
use start\extend\CodeExtend;
use start\extend\DataExtend;
use start\service\TokenService;
use start\service\RuntimeService;

if (!function_exists('debug')) {
    /**
     * 打印输出数据到文件
     * @param mixed $data 输出的数据
     * @param string $file 保存文件名称
     * @param boolean $replace 强制替换文件
     */
    function debug($data, $file = null, $replace = false)
    {
        RuntimeService::instance()->debug($data, $file, $replace);
    }
}
if (!function_exists('app_facade')) {
    /**
     * 获取应用门面
     */
    function app_facade($name)
    {
        return AppFacade::getFacade($name);
    }
}
if (!function_exists('app_exist')) {
    /**
     * 应用是否存在
     */
    function app_exist($name)
    {
        return AppManager::isActive($name);
    }
}
if (!function_exists('data_path')) {
    /**
     * 数据目录
     * @throws ReflectionException
     */
    function data_path()
    {
        return root_path() .'data' . DIRECTORY_SEPARATOR;
    }
}

if (!function_exists('array_to_xml')) {
    /**
     * 数组转xml
     *
     * @param array   $data 要转换的数据
     * @param boolean $root 是否返回根节点
     * @return string
     */
    function array_to_xml(array $data, $root = true)
    {
        return DataExtend::arr2xml($data, $root);
    }
}

if (!function_exists('xml_to_array')) {
    /**
     * xml转数组
     *
     * @param string $xml
     * @return array
     */
    function xml_to_array(string $xml)
    {
        return DataExtend::xml2arr($xml);
    }
}

if (!function_exists('unique_date')) {
    /**
     * 唯一日期编码
     * @param  integer $size   长度
     * @param  string  $prefix 前缀
     * @return string
     */
    function unique_date($size = 16, $prefix = '')
    {
        return CodeExtend::uniqueDate($size, $prefix);
    }
}
if (!function_exists('unique_number')) {
    /**
     * 唯一数字编码
     * @param  integer $size   长度
     * @param  string  $prefix 前缀
     * @return string
     */
    function unique_number($size = 16, $prefix = '')
    {
        return CodeExtend::uniqueNumber($size, $prefix);
    }
}
if (!function_exists('unique_id')) {
    /**
     * 唯一字符编码
     * @param  integer $size   长度
     * @param  string  $prefix 前缀
     * @return string
     */
    function unique_id($size = 32, $prefix = '')
    {
        return CodeExtend::uniqueId($size, $prefix);
    }
}
if (!function_exists('build_token')) {
    /**
     * 生成 CSRF-TOKEN 参数
     * @param string $node
     * @return string
     */
    function build_token($node = null)
    {
        $result = TokenService::instance()->buildFormToken($node);
        return $result['token'] ?? '';
    }
}
if (!function_exists('http_get')) {
    /**
     * 以get模拟网络请求
     * @param string $url HTTP请求URL地址
     * @param array|string $query GET请求参数
     * @param array $options CURL参数
     * @return boolean|string
     */
    function http_get($url, $query = [], $options = [])
    {
        return HttpExtend::get($url, $query, $options);
    }
}
if (!function_exists('http_post')) {
    /**
     * 以post模拟网络请求
     * @param string $url HTTP请求URL地址
     * @param array|string $data POST请求数据
     * @param array $options CURL参数
     * @return boolean|string
     */
    function http_post($url, $data, $options = [])
    {
        return HttpExtend::post($url, $data, $options);
    }
}
if (!function_exists('form_submit')) {
    /**
     * 以FormData模拟网络请求
     * @param string $url 模拟请求地址
     * @param array $data 模拟请求参数数据
     * @param array $file 提交文件 [field,name,content]
     * @param array $header 请求头部信息，默认带 Content-type
     * @param string $method 模拟请求的方式 [GET,POST,PUT]
     * @param boolean $returnHeader 是否返回头部信息
     * @return boolean|string
     */
    function form_submit($url, array $data = [], array $file = [], array $header = [], $method = 'POST', $returnHeader = true)
    {
        return HttpExtend::submit($url, $data, $file, $header, $method, $returnHeader);
    }
}
if (!function_exists('throw_error')) {
    /**
     * 抛出异常
     * @param  [type]  $msg  异常信息
     * @param  string  $data 异常数据
     * @param  integer $code 异常编码
     */
    function throw_error($msg, $data = '{-null-}', $code = 1)
    {
        if ($data === '{-null-}') $data = new \stdClass();
        throw new HttpResponseException(json([
            'code' => $code, 'msg' => $msg, 'data' => $data,
        ]));
    }
}
if (!function_exists('format_bytes')) {
    /**
     * 文件字节单位转换
     * @param integer $size
     * @return string
     */
    function format_bytes($size)
    {
        if (is_numeric($size)) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
            return round($size, 2) . ' ' . $units[$i];
        } else {
            return $size;
        }
    }
}
if (!function_exists('format_datetime')) {
    /**
     * 日期格式标准输出
     * @param string $datetime 输入日期
     * @param string $format 输出格式
     * @return false|string
     */
    function format_datetime($datetime, $format = 'Y-m-d H:i:s')
    {
        if (empty($datetime)) return '-';
        if (is_numeric($datetime)) {
            return date($format, $datetime);
        } else {
            return date($format, strtotime($datetime));
        }
    }
}
if (!function_exists('enbase64url')) {
    /**
     * Base64安全URL编码
     * @param string $string
     * @return string
     */
    function enbase64url(string $string)
    {
        return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
    }
}
if (!function_exists('debase64url')) {
    /**
     * Base64安全URL解码
     * @param string $string
     * @return string
     */
    function debase64url(string $string)
    {
        return base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
    }
}
if (!function_exists('encode')) {
    /**
     * 加密 UTF8 字符串
     * @param string $content
     * @return string
     */
    function encode($content)
    {
        if(is_array($content)) {
            $content = json_encode($content);
        }
        list($chars, $length) = ['', strlen($string = iconv('UTF-8', 'GBK//TRANSLIT', $content))];
        for ($i = 0; $i < $length; $i++) $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
        return $chars;
    }
}
if (!function_exists('decode')) {
    /**
     * 解密 UTF8 字符串
     * @param string $content
     * @return string
     */
    function decode($content)
    {
        $chars = '';
        foreach (str_split($content, 2) as $char) {
            $chars .= chr(intval(base_convert($char, 36, 10)));
        }
        return iconv('GBK//TRANSLIT', 'UTF-8', $chars);
    }
}
/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 单位 秒
 * @return string
 */
function start_encrypt($data, $key = '', $expire = 0)
{
    $key  = md5(empty($key) ? 'Simplestart' : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }

    $str = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
    return strtoupper(md5($str)) . $str;
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是start_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 */
function start_decrypt($data, $key = '')
{
    $key  = md5(empty($key) ? 'Simplestart' : $key);
    $data = substr($data, 32);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}