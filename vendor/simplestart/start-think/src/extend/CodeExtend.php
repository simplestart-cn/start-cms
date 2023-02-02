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

namespace start\extend;

/**
 * 随机数码管理扩展
 * Class CodeExtend
 * @package start\extend
 */
class CodeExtend
{
    /**
     * 获取随机字符串编码
     * @param integer $size 字符串长度
     * @param integer $type 字符串类型(1纯数字,2纯字母,3数字字母)
     * @param string $prefix 编码前缀
     * @return string
     */
    public static function random($size = 10, $type = 1, $prefix = '')
    {
        $numbs = '0123456789';
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        if (intval($type) === 1) $chars = $numbs;
        if (intval($type) === 3) $chars = "{$numbs}{$chars}";
        $string = $prefix . $chars[rand(1, strlen($chars) - 1)];
        if (isset($chars)) while (strlen($string) < $size) {
            $string .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $string;
    }

    /**
     * 唯一日期编码
     * @param integer $size
     * @param string $prefix
     * @return string
     */
    public static function uniqueDate($size = 16, $prefix = '')
    {
        if ($size < 14) $size = 14;
        $string = $prefix . date('Ymd') . (date('H') + date('i')) . date('s');
        while (strlen($string) < $size) $string .= rand(0, 9);
        return $string;
    }

    /**
     * 唯一数字编码
     * @param integer $size
     * @param string $prefix
     * @return string
     */
    public static function uniqueNumber($size = 16, $prefix = '')
    {
        $time = time() . '';
        if ($size < 10) $size = 10;
        $string = $prefix . (intval($time[0]) + intval($time[1])) . substr($time, 2) . rand(0, 9);
        while (strlen($string) < $size) $string .= rand(0, 9);
        return $string;
    }

    /**
     * 唯一字符编码
     * @param  integer $size   [description]
     * @param  string  $prefix [description]
     * @return [type]          [description]
     */
    public static function uniqueId($size = 32, $prefix = '')
    {
        $numbs = self::uniqueNumber(24);
        $chars = 'abcdefghijklmnopqrstuvwxyz'.$numbs;
        $string = $prefix . $chars[rand(1, strlen($chars) - 1)];
        if (isset($chars)) while (strlen($string) < $size) {
            $string .= $chars[rand(0, strlen($chars) - 1)];
        }
        return strtoupper($string);
    }
    
}