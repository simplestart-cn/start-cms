<?php
declare (strict_types = 1);
// +----------------------------------------------------------------------
// | Simplestart Think
// +----------------------------------------------------------------------
// | 版权所有: https://www.simplestart.cn copyright 2020
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-think
// +----------------------------------------------------------------------

namespace start\model;
use start\Model;

/**
 * @mixin think\model
 */
class Config extends Model
{
	protected $name = 'core_config';

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

	public function getOptionsAttr($value)
	{
		if(empty($value)) return [];
		return $this->formatProps(json_decode($value, true));
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
		if(empty($value)){
			$value = $data['default'];
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
	private function formatProps($data)
	{

		if(!is_array($data)) return [];
		foreach ($data as $key => $value) {
			if(is_array($value)){
				$data[$key] = $this->formatProps($value);
			}
			$data[$key] = $this->valueDecode($value);
		}
		return $data;
	}

	/**
	 * 格式化返回值
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	private function valueDecode($value)
	{
		if($value === 'false' || $value === 'true'){
			return $value === 'true';
		}
		if(!empty($value) && is_string($value) && preg_match("/^\d*$/",$value)){
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