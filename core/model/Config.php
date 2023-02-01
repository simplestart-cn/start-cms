<?php

// +----------------------------------------------------------------------
// | Simplestart CMS
// +----------------------------------------------------------------------
// | 版权所有: http://www.simplestart.cn copyright 2021
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------

namespace core\model;

/**
 * 配置信息
 */
class Config extends Base
{
	protected $name = 'core_config';
	protected $hidden = ['update_time','create_time'];

	public function setPropsAttr($value)
	{
		if(empty($value)) return '{}';
		return json_encode($value);
	}

	public function getPropsAttr($value)
	{
		if(empty($value)) return [];
		return $this->formatProps(json_decode($value, true));
	}

	public function setOptionsAttr($value)
	{
		if(empty($value)) return '[]';
		return json_encode($value);
	}

	public function getOptionsAttr($value, $data)
	{
		if(empty($value)) return [];
		return $this->formatProps(json_decode($value, true), $data['field']);
	}

	public function setValidateAttr($value)
	{
		if(empty($value)) return '[]';
		return json_encode($value);
	}

	public function getValidateAttr($value)
	{
		if(empty($value)) return [];
		return json_decode($value);
	}

	public function setSuffixAttr($value)
	{
		if(empty($value)) return '{}';
		return json_encode($value);
	}

	public function getSuffixAttr($value)
	{
		if(empty($value)) return [];
		return $this->formatProps(json_decode($value, true));
	}
	
	public function setIsLockingAttr($value)
	{
		if($value || $value === 'true' ){
			return 1;
		}
		return 0;
	}

	public function getIsLockingAttr($value)
	{
		return boolval($value);
	}

	public function setValueAttr($value)
	{
		return $this->valueEncode($value);
	}

	public function getValueAttr($value, $data)
	{
		if($value === null || $value === '' && !empty($data['default'])){
			$value = $this->valueDecode($data['default']);
		}
		return $this->valueDecode($value);
	}

	public function setDefaultAttr($value)
	{
		return $this->valueEncode($value);
	}

	public function getDefaultAttr($value, $data)
	{
		if(empty($value)){
			$value = $data['default'];
		}
		return $this->valueDecode($value);
	}

	/**
	 * 格式话属性
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function formatProps($data, $field = '')
	{

		if(!is_array($data)) return [];
		foreach ($data as $key => $value) {
			if(!is_string($value) && !is_bool($value) && !is_numeric($value)){
				$data[$key] = $this->formatProps($value, $field);
			}else{
				$data[$key] = $this->valueDecode($value);
			}
		}
		return $data;
	}

	/**
	 * 格式化返回值
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	private function valueDecode($value)
	{
		if(stripos($value, '[') !== false && stripos($value, ']') !== false){
			return json_decode($value, true);
		}
		if($value === 'false' || $value === 'true'){
			return $value === 'true';
		}
		if($value == '0' || (!empty($value) && is_string($value) && preg_match("/^\d*$/",$value))){
			return intval($value);
		}
		return $value;
	}

	/**
	 * 格式化存储值
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	private function valueEncode($value)
	{
		if($value === false || $value === true){
			return $value ? 'true' : 'false';
		}
		if(is_array($value) || is_object($value)){
			return json_encode($value);
		}
		return $value;
	}
}