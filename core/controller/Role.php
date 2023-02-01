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

namespace core\controller;

use core\service\RoleService;

/**
 * 角色权限
 * @menu
 * @auth {"parent": "core/account", "sort": 2}
 * @view /role/tree
 */
class Role extends Base
{
    /**
     * 角色查询
     * @auth
     *
     * @api            {get}            role/tree             树状结构
     * @apiName        tree
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取树状数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]             关键词(名称)
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function tree()
    {
        $input = $this->formValidate([
            'keyword.default'    => '',
            'status.default'   => 1,
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        $data = RoleService::getTree($input);
        $this->success($data);
    }

    /**
     * 获取列表
     * @admin
     *
     * @api            {get}            role/list              获取列表
     * @apiName        list
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]               关键词(名称)
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    function list() {
        $input = $this->formValidate([
            'keyword.default'    => null,
            'status.default'   => 1,
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        $data = RoleService::getList($input);
        $this->success($data);
    }

    /**
     * 查看详情
     * @auth
     *
     * @api            {get}            role/info               获取详情
     * @apiName        info
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                       方式ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function info()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
        ]);
        $data = RoleService::getInfo($input['id']);
        $this->success($data);
    }

    /**
     * 新增角色
     * @auth
     *
     * @api            {post}          role/create        新增记录
     * @apiName        create
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [pid]             上级ID
     * @apiParam       {string}        title             名称
     * @apiParam       {string}        [remark]          备注
     * @apiParam       {string}        [status=1]        状态
     * @apiParam       {string}        [auth_level=0]    权限级别(0仅限指定1包含下级)
     * @apiParam       {string}        [data_level=0]    数据级别(0仅限自己1包含下级)
     * @apiParam       {string}        [authorize=[]]    权限集合
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function create()
    {
        $input = $this->formValidate([
            'pid.default'       => 0,
            'title.require'     => '名称不能为空',
            'remark.default'    => '',
            'status.default'    => 1,
            'auth_level.default' => 0,
            'data_level.default' => 0,
            'authorize.default' => [],
        ]);
        if ($model = RoleService::create($input)) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新角色
     * @auth
     *
     * @api            {post}          role/update       更新记录
     * @apiName        update
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                id
     * @apiParam       {string}        [pid]             上级ID
     * @apiParam       {string}        title             名称
     * @apiParam       {string}        [remark]          备注
     * @apiParam       {string}        [status=1]        状态
     * @apiParam       {string}        [auth_level=0]    权限级别(0仅限指定1包含下级)
     * @apiParam       {string}        [data_level=0]    数据级别(0仅限自己1包含下级)
     * @apiParam       {string}        [authorize=[]]    权限集合
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function update()
    {
        $input = $this->formValidate([
            'id.require'        => 'id不能为空',
            'pid.default'       => 0,
            'title.require'     => '名称不能为空',
            'remark.default'    => '',
            'status.default'    => 1,
            'auth_leve.default' => 0,
            'data_leve.default' => 0,
            'authorize.default' => [],
        ]);
        if ($model = RoleService::update($input)) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新状态
     * @auth
     * 
     * @api            {post}           role/updateStatus           更新状态
     * @apiName        updateStatus
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 更新状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                           ID
     * @apiParam       {string}        status                       状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateStatus()
    {
        $input = $this->formValidate([
            'id.require' => 'ID不能为空',
            'status.require' => '状态不能为空'
        ]);
        if(RoleService::update($input)){
            $this->success('ok');
        }
        $this->error('fail');
    }

    /**
     * 批量更新
     * @auth
     *
     * @api            {post}               role/updateList           批量更新
     * @apiName        updateList
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 批量更新
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {array}              list                      列表数据,单项数据[id,status]
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateList()
    {
        $input = $this->formValidate([
            'list.require' => 'list不能为空',
        ]);
        foreach ($input['list'] as &$item) {
            if(!isset($item['id']) || empty($item['id'])){
                throw_error('记录id不能为空');
            }
            foreach ($item as $key => $value) {
                if(!in_array($key,['id','status'])){
                    unset($item[$key]);
                }
            }
        }
        if ($model = RoleService::model()->allowFields(['status'])->saveAll($input['list'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除记录
     * @auth
     *
     * @api            {post}                  role/remove                 删除记录
     * @apiName        remove
     * @apiGroup       角色管理
     * @apiVersion     v1.0.0
     * @apiDescription 删除记录
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}                id                          ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function remove()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
        ]);
        if (RoleService::remove($input['id'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
