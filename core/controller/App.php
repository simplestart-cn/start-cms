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

use start\AppManager;
use core\service\AppService;

/**
 * 应用
 * @auth {"icon": "component","sort": 0, "parent": ""}
 * @view /app/page
 */
class App extends Base
{
    /**
     * 应用中心
     * @login
     */
    public function store()
    {
        $manager = AppManager::instance();
        $store = $manager->getStore();
        $this->success($store);
    }

    /**
     * 中心验证
     * @login
     */
    public function storeCaptcha()
    {
        $input = $this->formValidate([
            'type.default'   => 'image',
            'mobile.defualt' => ''
        ]);
        $manager = AppManager::instance();
        $data = $manager->getCaptcha($input);
        $this->success($data);
    }

    /**
     * 中心登录
     * @login
     * @return void
     */
    public function loginStore()
    {
        $input = $this->formValidate([
            'mobile.default'   => '',
            'account.default'  => '',
            'password.default' => '',
            'uniqid.require'   => '验证码无效',
            'code.require'     => '验证码不能为空'
        ]);
        if(empty($input['mobile']) && empty($input['account'])){
            $this->error('手机号和账号不能同时为空!');
        }
        $manager = AppManager::instance();
        $result = $manager->loginStore($input);
        $this->success($result);
    }

    /**
     * 中心注册
     * @login
     * @return void
     */
    public function registerStore()
    {
        $input = $this->formValidate([
            'mobile.require'     => '手机号不能为空',
            'code.require'       => '验证码不能为空',
        ]);
        $manager = AppManager::instance();
        $user = $manager->registerStore($input);
        if($user){
            $this->success('注册成功', $user);
        }
        $this->error('注册失败');
    }

    /**
     * 应用查询
     * @auth
     *
     * @api            {get}            app/page         获取分页
     * @apiName        page
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]          关键词搜索(应用名称/应用简介)
     * @apiParam       {string}        [status]           状态筛选
     * @apiUse         PagingParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function page()
    {
        $input = $this->formValidate([
            'type.default' => '',
            'price.default' => '',
            'keyword.default' => '',
            'category.default' => ''
        ]);
        $data = AppManager::getPage($input);
        $this->success($data);
    }

    /**
     * 获取列表
     * @admin
     *
     * @api            {get}            app/list         获取列表
     * @apiName        list
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]        关键词搜索(应用名称/应用简介)
     * @apiParam       {string}        [status]         状态筛选
     * @apiUse         PagingParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function list()
    {
        $input = $this->formValidate([
            'keyword.default' => '',
            'status.default' => ''
        ]);
        if (!empty($input['keyword'])) {
            $input['title@like|summary@like'] = $input['keyword'];
        }
        $data = AppService::model()->field(['name', 'title'])->select()->toArray();
        array_unshift($data, ['name' => 'core', 'title' => '系统']);
        $this->success($data);
    }

    /**
     * 查看详情
     * @auth
     *
     * @api            {get}          app/info               获取详情
     * @apiName        info
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                      id
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function info()
    {
        $input = $this->formValidate([
            'name.require' => 'name不能为空'
        ], true);
        $data = AppService::getInfo($input);
        $this->success($data);
    }

    /**
     * 应用安装
     * @auth
     *
     * @api            {get}           app/install              应用安装
     * @apiName        install
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 应用安装
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [name]                   应用名称
     * @apiUse         PagingParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function install()
    {
        $input = $this->formValidate([
            'name.require' => '应用名称不能为空',
        ]);
        if (AppService::install($input['name'])) {
            $this->success('安装成功');
        }
        $this->error('操作失败');
    }

    /**
     * 应用卸载
     * @auth
     *
     * @api            {get}          app/uninstall              应用卸载
     * @apiName        uninstall
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 应用卸载
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [name]                    应用名称
     * @apiUse         PagingParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function uninstall()
    {
        $input = $this->formValidate([
            'name.require' => '应用名称不能为空',
        ]);
        if (AppService::uninstall($input['name'])) {
            $this->success('卸载成功');
        }
        $this->error('操作失败');
    }

    /**
     * 本地上传
     * @auth
     * @return void
     */
    public function upload()
    {
        $input = $this->formValidate([
            'filename.require' => '未知文件',
            'action.require' => '未知操作',
            'cover.default' => false,
        ], true);
        $manager = AppManager::instance();
        $result = $manager->upload($input['filename'], $input['action'], $input['cover']);
        if($result){
            $this->success('操作成功', $result);
        }
        $this->error('上传失败');
    }

    /**
     * 应用移除
     * @auth
     *
     * @api            {get}          app/remove              应用移除
     * @apiName        remove
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 应用卸载
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [name]                 应用名称
     * @apiUse         PagingParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function remove()
    {
        $input = $this->formValidate([
            'name.require' => '应用名称不能为空',
        ]);
        if (AppService::remove($input['name'])) {
            $this->success('删除成功');
        }
        $this->error('操作失败');
    }

    /**
     * 更新信息
     * @auth
     * @return void
     */
    public function update()
    {
        $input = $this->formValidate([
            'name.require' => 'name不能为空',
            'status.default' => null,
            'debug.default'  => null,
            'dev_entry.default' => null
        ], true, 'post');
        if (AppService::update($input)) {
            $this->success('ok');
        }
        $this->error('fail');
    }

    /**
     * 更新状态
     * @auth
     * 
     * @api            {post}         app/updateStatus           更新状态
     * @apiName        updateStatus
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 更新状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        name                      应用名称
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateStatus()
    {
        $input = $this->formValidate([
            'name.require' => 'name不能为空',
            'status.require' => '状态不能为空'
        ], true, 'post');
        if (AppService::update($input)) {
            $this->success('ok');
        }
        $this->error('fail');
    }

    /**
     * 升级配置
     * @auth
     * 
     * @api            {post}         app/upgradeConfig           更新配置
     * @apiName        upgradeConfig
     * @apiGroup       应用中心
     * @apiVersion     v1.0.0
     * @apiDescription 更新状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        name                      应用名称
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function upgradeConfig()
    {
        $input = $this->formValidate([
            'name.require' => 'name不能为空',
        ], true, '');
        if (AppService::upgradeConfig($input['name'])) {
            $this->success('ok');
        }
        $this->error('fail');
    }
    
}
