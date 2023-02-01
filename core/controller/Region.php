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

use core\service\RegionService;

/**
 * 地区管理
 * @menu
 * @auth {"parent": "core/config", "sort": 30}
 * @view /region/page
 */
class Region extends Base
{
    /**
     * 地区查询
     * @auth
     *
     * @api            {get}           region/page              获取分页
     * @apiName        page
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [pid=0]                  上级id
     * @apiParam       {string}        [status]                 状态
     * @apiParam       {string}        [keyword]                关键词(名称)
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function page() {
        $input = $this->formValidate([
            'pid.default'      => 0,
            'status.default'   => '',
            'keyword.default'    => '',
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        $data = RegionService::getPage($input);
        $this->success($data);
    }

    /**
     * 获取列表
     *
     * @api            {get}            region/list              获取列表
     * @apiName        list
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [pid=0]                  上级id
     * @apiParam       {string}        [status]                 状态
     * @apiParam       {string}        [keyword]                关键词(名称)
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function list() {
        $input = $this->formValidate([
            'pid.default'        => 0,
            'status.default'     => '',
            'keyword.default'    => '',
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] =  $input['keyword'];
        }
        $data = RegionService::getList($input);
        $this->success($data);
    }

    /**
     * 地区树
     *
     * @api            {get}            region/tree              树状结构
     * @apiName        tree
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]                关键词(名称)
     * @apiParam       {string}        [status=1]               状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function tree() {
        $input = $this->formValidate([
            'status.default'     => 1,
            'keyword.default'    => '',
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        $data = RegionService::getTree($input);
        $this->success($data);
    }

    /**
     * 地区选择器
     *
     * @api            {get}           region/cascader                树状结构
     * @apiName        cascader
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]                      名称
     * @apiParam       {string}        [status=1]                     状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function cascader() {
        $input = $this->formValidate([
            'keyword.default'    => '',
            'status.default'   => 1,
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        // 待实现
        $data = RegionService::getTree($input);
        $this->success($data);
    }

    /**
     * 获取详情
     * @login
     *
     * @api            {get}           region/info              获取详情
     * @apiName        info
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                       ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function info()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空'
        ]);
        $data = RegionService::getInfo($input['id']);
        $this->success($data);
    }

    /**
     * 新增地区
     * @auth
     *
     * @api            {post}          region/create     新增记录
     * @apiName        create
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        pid              上级id
     * @apiParam       {string}        title            名称
     * @apiParam       {string}        short_title      简称
     * @apiParam       {string}        merger_title     全称
     * @apiParam       {string}        level            层级(1省2市3区4县)
     * @apiParam       {string}        pinyin           拼音
     * @apiParam       {string}        code             区号
     * @apiParam       {string}        zip_code         邮编
     * @apiParam       {string}        first            首字母
     * @apiParam       {string}        [status=1]       状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function create()
    {
        $input = $this->formValidate([
            'pid.require'  => '上级不能为空',
            'title.require' => '名称不能为空',
            'short_title.require' => '',
            'merger_title.require' => '',
            'level.require' => '',
            'pingyin.require' => '',
            'code.require' => '',
            'zip_code.require' => '',
            'first.require' => '',
            'status.default' => 1
        ]);
        if ($model = RegionService::create($input)) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新地区
     * @auth
     *
     * @api            {post}          region/update     更新记录
     * @apiName        update
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id               ID
     * @apiParam       {string}        pid              上级ID
     * @apiParam       {string}        title            名称
     * @apiParam       {string}        short_title      简称
     * @apiParam       {string}        merger_title     全称
     * @apiParam       {string}        level            层级(1省2市3区4县)
     * @apiParam       {string}        pinyin           拼音
     * @apiParam       {string}        code             区号
     * @apiParam       {string}        zip_code         邮编
     * @apiParam       {string}        first            首字母
     * @apiParam       {string}        [status=1]       状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function update()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
            'title.require|ifexist'  => '名称不能为空',
            'remark.default' => '',
            'status.default' => 1
        ]);
        if ($model = RegionService::update($input)) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新状态
     * @auth
     * 
     * @api            {post}           region/updateStatus           更新状态
     * @apiName        updateStatus
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 更新状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                             ID
     * @apiParam       {string}        status                         状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateStatus()
    {
        $input = $this->formValidate([
            'id.require' => 'ID不能为空',
            'status.require' => '状态不能为空'
        ]);
        if(RegionService::update($input)){
            $this->success('ok');
        }
        $this->error('fail');
    }

    /**
     * 批量更新
     * @auth
     *
     * @api            {post}               region/updateList           批量更新
     * @apiName        updateList
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 批量更新
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {array}              list                        列表数据,单项数据[id,status]
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
        if ($model = RegionService::model()->saveAll($input['list'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }


    /**
     * 删除记录
     * @auth
     *
     * @api            {post}                  region/remove                 删除记录
     * @apiName        remove
     * @apiGroup       地区管理
     * @apiVersion     v1.0.0
     * @apiDescription 删除记录
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}                id                           ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function remove()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
        ]);
        if (RegionService::remove($input['id'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
