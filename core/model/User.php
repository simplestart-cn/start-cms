<?php
declare (strict_types = 1);
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


class User extends Base
{
    protected $name = 'core_user';
    protected $with = ['group'];

    public function getLoginTimeAttr($value)
    {
        return date('Y-m-d H:i', $value);
    }

    /**
     * 关联部门
     * @return [type] [description]
     */
    public function group()
    {
        return $this
            ->belongsTo(Group::class, 'group_id', 'id')
            ->bind(['group_title' => 'title']);
    }

    /**
     * 检查密码
     *
     * @param  string $password
     * @return boolean
     */
    public function checkPass($password)
    {
        return $this->password === md5(md5($password));
    }

    /**
     * 获取权限
     *
     * @return void
     */
    public function getAuthorize()
    {
        if($this->is_super){
            return ['all'];
        }
        $roleId = array();
        if(!empty($this->role_id)){
            $roleId = explode(',', $this->role_id);
        }
        $authorize = array();
        $list = Role::where('id', 'in', $roleId)->where(['status' => 1])->column(['authorize']);
        if(!empty($list)){
            foreach($list as $item){
                $authorize = array_merge($authorize, explode(',', $item));
            }
            $authorize = array_unique($authorize);  // 多角色去重
        }
        return Auth::where(['status' => 1])
        ->where('name', 'in', $authorize)
        ->whereOr(function($query){
            $query->where(['route' => 1, 'status' => 1]);
        })
        ->column('name');
    }

    /**
     * 查询后事件
     * @param $model
     */
    public static function onAfterRead($model)
    {
        // 查询角色
        if(!empty($model->role_id)){
            $roles = Role::where('id','in', $model->role_id)->where(['status' => 1])->column(['title']);
            $model->role_title = implode(',', $roles);
        }else{
            if($model->is_super){
                $model->role_title = '超级管理员';
            }else if($model->is_amdin){
                $model->role_title = '管理员';
            }else{
                $model->role_title = '';
            }
            
        }
    } 
}
