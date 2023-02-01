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

use core\service\AuthService;

/**
 * 权限管理
 * @menu
 * @auth {"parent": "core/config", "sort": 20}
 * @view /auth/tree
 * @package core\controller
 */
class Auth extends Base
{
    /**
     * 权限查询
     * @auth
     *
     * @api            {get}                auth/tree               树状结构
     * @apiName        tree
     * @apiGroup       权限管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取树状结构数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function tree()
    {
        $data = AuthService::getTree();
        $this->success('ok', $data);
    }

    /**
     * 获取列表
     * @admin
     *
     * @api            {get}             auth/list              获取列表
     * @apiName        list
     * @apiGroup       权限管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    function list() {
        $data = AuthService::getList();
        $this->success('ok', $data);
    }

    /**
     * 获取详情
     * @auth
     *
     * @api            {get}             auth/info               获取详情
     * @apiName        info
     * @apiGroup       权限管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}         id                      id
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function info($id)
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
        ]);
        $data = AuthService::getInfo($input['id']);
        $this->success($data);
    }

    /**
     * 新增记录
     * @auth
     *
     * @api            {post}          auth/create         新增记录
     * @apiName        create
     * @apiGroup       权限管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        app                 app标识
     * @apiParam       {string}        title               菜单名称
     * @apiParam       {string}        name                菜单标识
     * @apiParam       {string}        node                后端路径
     * @apiParam       {string}        [icon]              图标
     * @apiParam       {string}        [path]              前端路径
     * @apiParam       {string}        [component]         前端组件
     * @apiParam       {string}        [params]            路由参数
     * @apiParam       {string}        [redirect=0]        跳转地址
     * @apiParam       {string}        [condition]         路由验证
     * @apiParam       {string}        [cache]             是否缓存
     * @apiParam       {string}        [sort]              顺序
     * @apiParam       {string}        [menu=0]         菜单显示
     * @apiParam       {string}        [status=1]          状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function create()
    {
        $input = $this->formValidate([
            'app.require'        => '请选择所属应用',
            'title.require'      => '名称不能为空',
            'name.require'       => '标识不能为空',
            'node.require'       => '节点不能为空',
            'icon.default'       => '',
            'path.default'       => '',
            'component.default'  => '',
            'params.default'     => '',
            'redirect.default'   => '',
            'condition.default'  => '',
            'cache.default'      => 1,
            'sort.default'       => 100,
            'menu.default'       => 0,
            'status.default'     => 1,
        ]);
        if (empty($input['path'])) {
            $input['path'] = $input['node'];
        }
        if (AuthService::create($input)) {
            $this->success('更新成功');
        } else {
            $this->error('error');
        }
    }

    /**
     * 更新记录
     * @auth
     *
     * @api            {post}          auth/update         更新记录
     * @apiName        update
     * @apiGroup       权限管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                  ID
     * @apiParam       {string}        app                 app标识
     * @apiParam       {string}        title               菜单名称
     * @apiParam       {string}        name                菜单标识
     * @apiParam       {string}        node                后端路径
     * @apiParam       {string}        [icon]              图标
     * @apiParam       {string}        [path]              前端路径
     * @apiParam       {string}        [component]         前端组件
     * @apiParam       {string}        [params]            路由参数
     * @apiParam       {string}        [redirect=0]        跳转地址
     * @apiParam       {string}        [condition]         路由验证
     * @apiParam       {string}        [cache]             是否缓存
     * @apiParam       {string}        [sort]              顺序
     * @apiParam       {string}        [menu=0]            菜单显示
     * @apiParam       {string}        [status=1]          状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function update()
    {
        $input = $this->formValidate([
            'id.require'            => 'id不能为空',
            'pid.require|ifexist'   => 'pid不能为空',
            'app.require|ifexist'   => '请选择所属应用',
            'title.require|ifexist' => '名称不能为空',
            'name.require|ifexist'  => '标识不能为空',
            'node.require|ifexist'  => '节点不能为空',
            'icon.default'          => null,
            'path.default'          => null,
            'component.default'     => null,
            'params.default'        => null,
            'redirect.default'      => null,
            'condition.default'     => '',
            'cache.default'         => 1,
            'sort.default'          => 100,
            'menu.default'          => 0,
            'status.default'        => 1,
        ]);
        if (AuthService::update($input)) {
            $this->success('更新成功');
        } else {
            $this->error('error');
        }
    }

    /**
     * 更新状态
     * @auth
     *
     * @api            {post}          auth/updateStatus     更新状态
     * @apiName        updateStatus
     * @apiGroup       权限管理
     * @apiVersion     v1.0.0
     * @apiDescription 更新状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                    ID
     * @apiParam       {string}        status                状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateStatus()
    {
        $input = $this->formValidate([
            'id.require'     => 'id不能为空',
            'status.default' => 0,
        ]);
        if (AuthService::update($input)) {
            $this->success('ok');
        }
        $this->error('error');
    }

    /**
     * 批量更新
     * @auth
     *
     * @api            {post}               auth/updateList            批量更新
     * @apiName        updateList
     * @apiGroup       权限管理
     * @apiVersion     v1.0.0
     * @apiDescription 批量更新
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {array}              list                        列表数据,单项数据[[id,status]]
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateList()
    {
        $input = $this->formValidate([
            'list.require' => 'list不能为空',
        ]);
        foreach ($input['list'] as &$item) {
            if (!isset($item['id']) || empty($item['id'])) {
                throw_error('记录id不能为空');
            }
            foreach ($item as $key => $value) {
                if (!in_array($key, ['id', 'status'])) {
                    unset($item[$key]);
                }
            }
        }
        if ($model = AuthService::model()->saveAll($input['list'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除记录
     * @auth
     *
     * @api            {post}                  auth/remove                 删除记录
     * @apiName        remove
     * @apiGroup       权限管理
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
        if (AuthService::remove($input['id'])) {
            $this->success('ok');
        } else {
            $this->error('fail');
        }
    }

    /**
     * 刷新权限
     * @auth
     *
     * @api            {post}                  auth/refresh                 刷新权限
     * @apiName        refresh
     * @apiGroup       权限菜单
     * @apiVersion     v1.0.0
     * @apiDescription 刷新权限
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}                app                          app
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function refresh()
    {
        $input = $this->formValidate([
            'app.require' => 'app不能为空'
        ]);
        $data = $this->app->cache->get($input['app'].'_auth_node', []);
        if (count($data) > 0){
            if($this->app->cache->delete($input['app'].'_auth_node')){
                $this->success('Refresh success!');
            }
            $this->error('Refresh error');
        }
        $this->error('Cahce empty');
    }

}
