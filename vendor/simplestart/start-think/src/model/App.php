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

namespace start\model;
use start\Model;

/**
 *  App类
 */
class App extends Model
{
	protected $name = 'core_app';

	/**
	 * 设置分类
	 * @param [type] $value [description]
	 */
	public function setCategoryAttr($value)
	{
		return json_encode($value);
	}

	/**
	 * 获取分类
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function getCategoryAttr($value)
	{
		return json_decode($value);
	}

	/**
	 * 设置文档
	 * @param [type] $value [description]
	 */
	public function setDocumentationAttr($value)
	{
		return json_encode($value);
	}

	/**
	 * 获取文档
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function getDocumentationAttr($value)
	{
		return json_decode($value);
	}

	/**
	 * 设置依赖
	 * @param [type] $value [description]
	 */
	public function setDependenciesAttr($value)
	{
		return json_encode($value);
	}

	/**
	 * 获取依赖
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function getDependenciesAttr($value)
	{
		return json_decode($value);
	}

	/**
	 * 设置框架
	 * @param [type] $value [description]
	 */
	public function setStartCmsAttr($value)
	{
		return json_encode($value);
	}

	/**
	 * 获取框架
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function getStartCmsAttr($value)
	{
		return json_decode($value);
	}




}