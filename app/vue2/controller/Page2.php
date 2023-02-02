<?php
declare (strict_types = 1);
// +----------------------------------------------------------------------
// | Generate By StartCMS
// +----------------------------------------------------------------------
// | Github: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------
// | Copyright (c) http://www.simplestart.cn All rights reserved.
// +----------------------------------------------------------------------

namespace app\vue2\controller;

use start\Controller;
use app\vue2\service\Page2Service;

/**
 * 这里的菜单在app.json定义
 */
class Page2 extends Controller
{
   /**
     * 查看记录
     * @auth
     */
    public function page()
    {
        $input = $this->formValidate([
            'keyword.default'  => '',
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        $list = Page2Service::getPage($input);
        $this->success($list);
    }

    /**
     * 获取列表
     * @admin
     */
    public function list()
    {
        $input = $this->formValidate([
            'keyword.default'  => '',
            'status.value'     => 1,
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        $list = Page2Service::getList($input);
        $this->success($list);
    }

    /**
     * 查看详情
     * @auth
     */
    public function info()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空'
        ]);
        $model = Page2Service::getInfo($input['id']);
        $this->success($model);
    }

    /**
     * 新增记录
     * @auth
     */
    public function create()
    {
        $input = $this->formValidate([
            'title.require' => '名称不能为空',
        ]);
        $model = Page2Service::create($input);
        if ($model) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新记录
     * @auth
     */
    public function update()
    {
        $input = $this->formValidate([
            'id.require'            => 'id不能为空',
            'title.require|ifexist' => '名称不能为空'
        ]);
        $model = Page2Service::update($input);
        if ($model) {
            $this->success('操作成功', $model);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除记录
     * @auth
     */
    public function remove()
    {
        $input = $this->formValidate([
            'id.require' => 'id不能为空',
        ]);
        if (Page2Service::remove($input['id'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 更新状态
     * @auth
     */
    public function updateStatus()
    {
        $input = $this->formValidate([
            'id.require'     => 'id不能为空',
            'status.require' => 'status不能为空'
        ], true);
        if (Page2Service::update($input)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 批量更新
     * @auth
     */
    public function updateList()
    {
        $input = $this->formValidate([
            'list.require' => 'list不能为空',
        ]);
        foreach ($input['list'] as $item) {
            if (!isset($item['id']) || empty($item['id'])) {
                throw_error('item主键id不能为空');
            }
        }
        if (Page2Service::saveAll($input['list'], ['status'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 批量删除
     * @auth
     */
    public function removeList()
    {
        $input = $this->formValidate([
            'list.require' => 'list不能为空',
        ], true);
        if (Page2Service::remove($input['list'])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 批量导入
     * @auth
     */
    public function importList()
    {
        $input = $this->formValidate([
            'list.require' => 'list不能为空',
        ]);
        $data = Page2Service::import($input['list'], ['title']);
        if ($data) {
            $this->success('操作成功', $data);
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 批量导出
     * @auth
     */
    public function exportList()
    {
        $input = $this->formValidate([
            'keyword.default'  => '',
            'status.value'     => 1,
            'per_page.default' => 2000
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like'] = $input['keyword'];
        }
        $data = Page2Service::getPage($input);
        $this->success($data);
    }
}
