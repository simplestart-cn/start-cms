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
 * 账户角色
 */
class Role extends Base
{
	protected $name = 'core_role';

	public function auth()
	{
		return $this->hasMany("RoleAuth",'role_id','id')->field(['role_id','name','half']);
	}
}