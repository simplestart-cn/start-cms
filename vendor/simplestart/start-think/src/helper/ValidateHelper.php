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

namespace start\helper;

use start\Helper;
use think\Validate;

/**
 * 快捷输入验证器
 * Class ValidateHelper
 * @package start\helper
 */
class ValidateHelper extends Helper
{
    /**
     * 快捷输入并验证（ 支持 规则 # 别名 ）
     * @param array $rules 验证规则（ 验证信息数组 ）
     * @param boolean $strict 严格模式 ( 过滤未验证参数 )
     * @param string $type 输入方式 ( post. 或 get. )
     * @return array
     *  验证器示例
     *  name.require => message
     *  age.max:100 => message
     *  name.between:1,120 => message
     *  自定义规则
     *  name.value => value // 设置当前值
     *  name.default => 100 // 获取并设置默认值
     *  内置规则
     *  'require'     => ':attribute require',
     *  'must'        => ':attribute must',
     *  'number'      => ':attribute must be numeric',
     *  'integer'     => ':attribute must be integer',
     *  'float'       => ':attribute must be float',
     *  'boolean'     => ':attribute must be bool',
     *  'email'       => ':attribute not a valid email address',
     *  'mobile'      => ':attribute not a valid mobile',
     *  'array'       => ':attribute must be a array',
     *  'accepted'    => ':attribute must be yes,on or 1',
     *  'date'        => ':attribute not a valid datetime',
     *  'file'        => ':attribute not a valid file',
     *  'image'       => ':attribute not a valid image',
     *  'alpha'       => ':attribute must be alpha',
     *  'alphaNum'    => ':attribute must be alpha-numeric',
     *  'alphaDash'   => ':attribute must be alpha-numeric, dash, underscore',
     *  'activeUrl'   => ':attribute not a valid domain or ip',
     *  'chs'         => ':attribute must be chinese',
     *  'chsAlpha'    => ':attribute must be chinese or alpha',
     *  'chsAlphaNum' => ':attribute must be chinese,alpha-numeric',
     *  'chsDash'     => ':attribute must be chinese,alpha-numeric,underscore, dash',
     *  'url'         => ':attribute not a valid url',
     *  'ip'          => ':attribute not a valid ip',
     *  'dateFormat'  => ':attribute must be dateFormat of :rule',
     *  'in'          => ':attribute must be in :rule',
     *  'notIn'       => ':attribute be notin :rule',
     *  'between'     => ':attribute must between :1 - :2',
     *  'notBetween'  => ':attribute not between :1 - :2',
     *  'length'      => 'size of :attribute must be :rule',
     *  'max'         => 'max size of :attribute must be :rule',
     *  'min'         => 'min size of :attribute must be :rule',
     *  'after'       => ':attribute cannot be less than :rule',
     *  'before'      => ':attribute cannot exceed :rule',
     *  'expire'      => ':attribute not within :rule',
     *  'allowIp'     => 'access IP is not allowed',
     *  'denyIp'      => 'access IP denied',
     *  'confirm'     => ':attribute out of accord with :2',
     *  'different'   => ':attribute cannot be same with :2',
     *  'egt'         => ':attribute must greater than or equal :rule',
     *  'gt'          => ':attribute must greater than :rule',
     *  'elt'         => ':attribute must less than or equal :rule',
     *  'lt'          => ':attribute must less than :rule',
     *  'eq'          => ':attribute must equal :rule',
     *  'unique'      => ':attribute has exists',
     *  'regex'       => ':attribute not conform to the rules',
     *  'method'      => 'invalid Request method',
     *  'token'       => 'invalid token',
     *  'fileSize'    => 'filesize not match',
     *  'fileExt'     => 'extensions to upload is not allowed',
     *  'fileMime'    => 'mimetype to upload is not allowed',
     */
    public function init(array $rules, $strict = true, $type = '')
    {
        if($type !== '' && stripos($type, '.') === false){
            $type .= '.'; 
        }
        list($input, $rule, $info, $alias) = [input('',[],'trim'), [], [], ''];
        $data = $input;
        $validFields = array();
        foreach ($rules as $name => $message) {
            if (stripos($name, '#') !== false) {
                list($name, $alias) = explode('#', $name);
            }
            if (stripos($name, '.') === false) {
                array_push($validFields, $name);
                if (is_numeric($name)) {
                    $field = $message;
                    if (is_string($message) && stripos($message, '#') !== false) {
                        list($name, $alias) = explode('#', $message);
                        $field = empty($alias) ? $name : $alias;
                    }
                    $data[$name] = input("{$type}{$field}");
                } else {
                    $data[$name] = $message;
                }
            } else {
                list($_rgx) = explode(':', $name);
                list($_key, $_rule) = explode('.', $name);
                array_push($validFields, $_key);
                if (in_array($_rule, ['value', 'default'])) {
                    if ($_rule === 'value') {
                        $data[$_key] = $message;
                    } elseif ($_rule === 'default') {
                        if(isset($input[$_key]) || isset($input[$_key])){
                            $data[$_key] = input($type . ($alias ?: $_key));
                        }else if($message !== null){
                            $data[$_key] = $message;
                        }
                    }
                } else {
                    if(stripos($_rgx, 'ifexist') !== false){
                        $_rgx = str_replace('ifexist|', '', str_replace('|ifexist', '', $_rgx));
                    }
                    $info[$_rgx] = $message;
                    $data[$_key] = $data[$_key] ?? input($type . ($alias ?: $_key));
                    $rule[$_key] = empty($rule[$_key]) ? $_rule : "{$rule[$_key]}|{$_rule}";
                }
            }
        }
        
        foreach ($data as $key => $value) {
            // 仅验证存在的
            if(isset($rule[$key])){
                if(stripos($rule[$key], 'ifexist') !== false){
                    if(isset($input[$key])){
                       $rule[$key] = str_replace('ifexist|', '', str_replace('|ifexist', '', $rule[$key]));
                    }else{
                        unset($info[$key]);
                        unset($rule[$key]);
                        unset($data[$key]);   
                    }
                }
            }
            // 仅接收验证的
            if($strict && !in_array($key, $validFields)){
                unset($data[$key]);
            }
        }
        $validate = new Validate();
        if ($validate->rule($rule)->message($info)->check($data)) {
            return $data;
        } else {
            $this->controller->error($validate->getError());
        }
    }
}