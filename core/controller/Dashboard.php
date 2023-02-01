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

use core\service\DashboardService;

/**
 * 仪表盘
 * @auth
 */
class Dashboard extends Base
{
    /**
     * 欢迎使用
     * @route
     * @view /dashboard/welcome
     */
    public function welcome()
    {
        
    }

    /**
     * 汇总统计
     * @auth
     * @view /dashboard/gather
     * @return void
     */
    public function gather()
    {

    }

    /**
     * 访问统计
     * @auth
     * @view /dashboard/visit
     */
    public function visit()
    {
        
    }

    /**
     * 栏目查询
     * @login
     *
     * @api            {get}           dashboard/page               获取分页
     * @apiName        page
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]               关键词搜索(名称)
     * @apiParam       {string}        [status]                状态筛选
     * @apiUse         PagingParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    function page() {
        $input = $this->formValidate([
            'keyword.default'    => '',
            'status.default'   => '',
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] =  $input['keyword'];
        }
        $data = DashboardService::getPage($input);
        $this->success($data);
    }

    /**
     * 获取列表
     * @admin
     *
     * @api            {get}           dashboard/list              获取列表
     * @apiName        list
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]              关键词搜索(名称)
     * @apiParam       {string}        [status=1]             状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    function list() {
        $input = $this->formValidate([
            'keyword.default'    => '',
            'status.default'   => 1,
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] =  $input['keyword'];
        }
        $data = DashboardService::getList($input, 'sort asc');
        $this->success($data);
    }

    /**
     * 查看详情
     * @auth
     *
     * @api            {get}          dashboard/info               获取详情
     * @apiName        info
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                      id
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function info($id)
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空'
        ]);
        $data = DashboardService::getInfo($input['id']);
        $this->success($data);
    }

    /**
     * 创建模块
     * @auth
     *
     * @api            {post}           dashboard/create             新增记录
     * @apiName        create
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}         title                    名称
     * @apiParam       {string}         [remark]                 备注
     * @apiParam       {string}         [sort=100]               排序
     * @apiParam       {string}         [status=1]               状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function create()
    {
        $input = $this->formValidate([
            'title.require' => '名称不能为空',
            'remark.defualt' => '',
            'sort.default' => 100,
            'status.default' => 1
        ]);
        if ($model = DashboardService::create($input)) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新模块
     * @auth
     *
     * @api            {post}           dashboard/update          更新记录
     * @apiName        update
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                     ID
     * @apiParam       {string}        [title]                名称
     * @apiParam       {string}        [remark]               备注
     * @apiParam       {string}        [sort=100]             排序
     * @apiParam       {string}        [status=1]             状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function update()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
            'title.require|ifexist' => '名称不能为空',
            'remark.default' => '',
            'sort.default' => 100,
            'status.default' => 1
        ]);
        if ($model = DashboardService::update($input)) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新状态
     * @auth
     * 
     * @api            {post}            dashboard/updateStatus           更新状态
     * @apiName        updateStatus
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 更新状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}         id                           ID
     * @apiParam       {string}         status                       状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateStatus()
    {
        $input = $this->formValidate([
            'id.require' => 'ID不能为空',
            'status.require' => '状态不能为空'
        ]);
        if(DashboardService::update($input)){
            $this->success('ok');
        }
        $this->error('fail');
    }

    /**
     * 批量更新
     * @auth
     *
     * @api            {post}             dashboard/updateList           批量更新
     * @apiName        updateList
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 批量更新
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {array}            list                      列表数据,单项数据[id,status]
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
        if ($model = DashboardService::model()->saveAll($input['list'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除模块
     * @auth
     *
     * @api            {post}                dashboard/remove                 删除记录
     * @apiName        remove
     * @apiGroup       仪表盘
     * @apiVersion     v1.0.0
     * @apiDescription 删除记录
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}              id                          ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function remove()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
        ]);
        if (DashboardService::remove($input['id'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
