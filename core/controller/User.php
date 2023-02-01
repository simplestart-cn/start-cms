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

use core\service\UserService;
use core\service\AuthService;
use core\service\OauthService;
use core\service\ConfigService;
use core\service\CaptchaService;

/**
 * 系统账户
 * @menu
 * @auth {"parent": "core/account", "sort": 1}
 * @view /user/page
 * @package core\controller
 */
class User extends Base
{
    /**
     * 账户登出
     * 
     * @api            {post}    user/logout           登录注销
     * @apiName        logout
     * @apiGroup       登录注册
     * @apiVersion     v1.0.0
     * @apiDescription 退出登录账户
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function logout()
    {
        $this->app->session->clear();
        $this->app->session->destroy();
        $this->success('退出登录成功!', url('admin/index/index'));
    }

    /**
     * 账户登陆
     * 
     * @api            {post}          user/login       账户登录
     * @apiName        login
     * @apiGroup       登录注册
     * @apiVersion     v1.0.0
     * @apiDescription 登录账户
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        account          账户名称
     * @apiParam       {array}         password         登录密码
     * @apiParam       {string}        code             验证码
     * @apiParam       {string}        uniqid           验证码标识(获取验证码的时候有返回)
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     * 
     */
    public function login()
    {
        if ($this->app->request->isGet()) {
            $this->redirect('/web');
        } else {
            $input = $this->formValidate([
                'method.default'   => 'account',
                'mobile.default'   => '',
                'account.default'  => '',
                'password.default' => '',
                'code.default'     => '',
                'uniqid.default'   => '',
                'inviter.default'  => '',
            ]);
            if($input['method'] == 'account'){
                if(empty($input['code']) || empty($input['uniqid'])){
                    $this->error('验证码无效');
                }
                if(!CaptchaService::instance()->check($input['code'], $input['uniqid'])){
                    $this->error('验证码有误');
                }
                if(empty($input['account'])){
                    $this->error('登录账号不能为空!');
                }
                if(empty($input['password'])){
                    $this->error('登录密码不能为空!');
                }
                $account = UserService::accountLogin($input['account'], $input['password']);
            }
            if($input['method'] == 'mobile'){
                if(empty($input['mobile'])){
                    $this->error('手机号码不能为空!');
                }
                if(empty($input['code'])){
                    $this->error('验证码不能为空');
                }
                $account = UserService::mobileLogin($input['mobile'], $input['code']);
            }
            $this->success('登录成功', $account);
        }
    }


    /**
     * 手机号登录
     * @api            {post}          user/mobileLogin       手机号登录
     * @apiName        register
     * @apiGroup       登录注册
     * @apiVersion     v1.0.0
     * @apiDescription 创建权限分组
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        mobile                 手机号
     * @apiParam       {string}        code                   验证码
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function mobileLogin()
    {
        $input = $this->formValidate([
            'mobile.require'      => '手机号不能为空',
            'code.require'        => '验证码不能为空',
        ]);
        $account = UserService::mobileLogin($input['mobile'], $input['code']);
        $this->success('登录成功', $account);
    }

    /**
     * 手机号绑定
     * @api            {post}          user/bindMobile        手机号绑定
     * @apiName        register
     * @apiGroup       登录注册
     * @apiVersion     v1.0.0
     * @apiDescription 创建权限分组
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        mobile                 手机号
     * @apiParam       {string}        code                   验证码
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function bindMobile()
    {
        $input = $this->formValidate([
            'mobile.require'      => '手机号不能为空',
            'code.require'        => '验证码不能为空',
        ]);
        $account = UserService::bindMobile($input['mobile'], $input['code']);
        $this->success('更新成功', $account);
    }

    /**
     * 账户注册
     *
     * @api            {post}           user/register  账户注册
     * @apiName        register
     * @apiGroup       登录注册
     * @apiVersion     v1.0.0
     * @apiDescription 创建权限分组
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        name            姓名
     * @apiParam       {string}        mobile          手机号
     * @apiParam       {string}        code            验证码
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     * 
     */
    public function register()
    {
        $input = $this->formValidate([
            'name.default'       => '',
            'mobile.require'     => '手机号不能为空',
            'code.require'       => '验证码不能为空',
            'is_admin.value'     => 0
        ]);
        $user = UserService::register($input);
        if($user){
            $account = UserService::keepLogin($user);
            $this->success('注册成功', $account);
        }
        $this->error('注册失败');
    }

    /**
     * 账户查询
     * @auth
     *
     * @api            {get}           user/page            获取分页
     * @apiName        page
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [group_id]           部门id
     * @apiParam       {string}        [role_id]            职位id
     * @apiParam       {string}        [keyword]            关键词(账户/名称/手机号)
     * @apiParam       {string}        [status]             状态
     * @apiParam       {string}        [is_admin=1]         是否管理员
     * @apiUse         PagingParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function page()
    {
        $input = $this->formValidate([
            'group_id.default'  => '',
            'role_id.default'   => '',
            'keyword.default'   => '',
            'is_admin.default'  => 1,
            'status.default'    =>1,
        ]);
        if (!empty($input['keyword'])) {
            $input['account@like|name@like|mobile@like'] = $input['keyword'];
            unset($input['keyword']);
        }
        $data = UserService::getPage($input);
        $this->success('ok', $data);
    }

    /**
     * 获取管理员
     * @admin
     *
     * @api            {get}           user/admin           获取管理员
     * @apiName        admin
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取管理员数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [group_id]           部门id
     * @apiParam       {string}        [role_id]            职位id
     * @apiParam       {string}        [keyword]            关键词(账户/名称/手机号)
     * @apiParam       {string}        [is_admin=1]         是否管理员
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    function admin() {
        $input = $this->formValidate([
            'group_id.default'  => '',
            'role_id.default'   => '',
            'keyword.default'   => '',
            'is_admin.default'  => 1,
            'status.value'      => 1,
        ]);
        if (!empty($input['keyword'])) {
            $input['account@like|name@like|mobile@like'] = $input['keyword'];
            unset($input['keyword']);
        }
        $data = UserService::getList($input)
        ->visible(['id','name','avatar','gender','mobile','group_title','role_title']);
        $this->success('ok', $data);
    }

    /**
     * 获取详情
     * @auth
     * @view /user/info
     *
     * @api            {get}           user/info         账户详情
     * @apiName        info
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                方式ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function info()
    {
        $input = $this->formValidate([
            'id.require' => '用户ID不能为空！',
        ]);
        $model = UserService::getInfo($input['id']);
        $this->success('ok', $model);
    }

    /**
     * 账户状态
     * @login
     *
     * @api            {get}           user/state      当前状态
     * @apiName        state
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取登录状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                方式ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function state()
    {
        $user = get_user();
        if($user){
            $account = UserService::getInfo($user['id']);
            if(empty($account) || !$account->status){
                throw_error('账户已被禁用');
            }
            $user['mobile']    = $account->mobile;
            $data['token']     = $user['token'];
            $data['timestamp'] = $user['timestamp'];
            $data['authorize'] = $account->getAuthorize();
            $data['config']    = ConfigService::getConfig('core');
            $data['apps']      = AuthService::getApps($user['authorize']);
            // 移除不必要暴露的参数
            unset($user['token']);
            unset($user['group']);
            unset($user['remark']);
            unset($user['is_super']);
            unset($user['is_admin']);
            unset($user['timestamp']);
            unset($user['authorize']);
            $data['info']      = $user;
            if(env('APP_DEBUG')){
                $data['config']['debug'] = true;
            }
            $this->success('ok', $data);
        } else {
            $this->error('请先登录');
        }
    }

    /**
     * 当前账户
     * @login
     * @route
     * @view /user/current
     *
     * @api            {get}           user/current      当前账户
     * @apiName        current
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                方式ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function current()
    {
        $user = get_user();
        if($user){
            $account = UserService::getInfo($user['id']);
            if(empty($account) || !$account->status){
                throw_error('账户已被禁用');
            }
            $user['mobile']    = $account->mobile;
            // 移除不必要暴露的参数
            unset($user['token']);
            unset($user['is_super']);
            unset($user['is_admin']);
            unset($user['timestamp']);
            unset($user['authorize']);
            $this->success('ok', $user);
        } else {
            $this->error('请先登录');
        }
    }

    /**
     * 新增账户
     * @auth
     *
     * @api            {post}          user/create      新增账户
     * @apiName        create
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        account          登录账户
     * @apiParam       {string}        password         登录密码
     * @apiParam       {string}        repassword       重复密码
     * @apiParam       {string}        [group_id]       部门id
     * @apiParam       {string}        [role_id]        职位id
     * @apiParam       {string}        [name]           姓名
     * @apiParam       {string}        [avatar]         头像地址
     * @apiParam       {string}        [mobile]         手机号
     * @apiParam       {string}        [email]          邮箱
     * @apiParam       {string}        [remark]         备注
     * @apiParam       {string}        [sort=100]       排序
     * @apiParam       {string}        [status=1]       状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function create()
    {
        $input = $this->formValidate([
            'account.require'    => '登录账号不能为空',
            'password.require'   => '账户密码不能为空',
            'repassword.require' => '确认密码不能为空',
            'group_id.default'   => 0,
            'role_id.default'    => 0,
            'name.default'       => '',
            'avatar.default'     => '',
            'mobile.default'     => '',
            'email.default'      => '',
            'remark.default'     => '',
            'sort.default'       => 100,
            'status.default'     => 1,
            'is_admin.default'   => 1
        ]);
        if (!empty($input['password'])) {
            if ($input['password'] !== $input['repassword']) {
                $this->error('两次输入的密码不一致！');
            }
        } else {
            unset($input['password']);
        }
        if ($model = UserService::register($input)) {
            $this->success('ok', $model);
        } else {
            $this->error('error');
        }
    }

    /**
     * 更新账户
     * @auth
     *
     * @api            {post}           user/update    更新账户
     * @apiName        update
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id               ID
     * @apiParam       {string}        account          登录账户
     * @apiParam       {string}        password         登录密码
     * @apiParam       {string}        repassword       重复密码
     * @apiParam       {string}        [group_id]       部门id
     * @apiParam       {string}        [role_id]        职位id
     * @apiParam       {string}        [name]           姓名
     * @apiParam       {string}        [avatar]         头像地址
     * @apiParam       {string}        [mobile]         手机号
     * @apiParam       {string}        [email]          邮箱
     * @apiParam       {string}        [remark]         备注
     * @apiParam       {string}        [sort=100]       排序
     * @apiParam       {string}        [status=1]       状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function update()
    {
        $input = $this->formValidate([
            'id.require'         => 'ID不能为空',
            'account.default'    => '',
            'password.default'   => '',
            'repassword.default' => '',
            'group_id.default'   => 0,
            'role_id.default'    => 0,
            'name.default'       => '',
            'avatar.default'     => '',
            'mobile.default'     => '',
            'email.default'      => '',
            'remark.default'     => '',
            'sort.default'       => 100,
            'status.default'     => 1,
        ]);
        if (!empty($input['password'])) {
            if ($input['password'] !== $input['repassword']) {
                $this->error('两次输入的密码不一致！');
            }
        } else {
            unset($input['password']);
        }
        if ($model = UserService::update($input)) {
            $this->success('ok', $model);
        } else {
            $this->error('error');
        }
    }

    /**
     * 更新当前账户
     * @login
     *
     * @api            {post}           user/updateCurrent        更新当前账户
     * @apiName        updateCurrent
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                         ID
     * @apiParam       {string}        password                   登录密码
     * @apiParam       {string}        repassword                 重复密码
     * @apiParam       {string}        [name]                     姓名
     * @apiParam       {string}        [avatar]                   头像地址
     * @apiParam       {string}        [mobile]                   手机号
     * @apiParam       {string}        [email]                    邮箱
     * @apiParam       {string}        [remark]                   备注
     * @apiParam       {string}        [sort=100]                 排序
     * @apiParam       {string}        [status=1]                 状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError 
     */
    public function updateCurrent()
    {
        $input = $this->formValidate([
            'account.default'    => '',
            'password.default'   => '',
            'repassword.default' => '',
            'name.default'       => '',
            'avatar.default'     => '',
            'email.default'      => '',
        ]);
        if (!empty($input['password'])) {
            if ($input['password'] !== $input['repassword']) {
                $this->error('两次输入的密码不一致！');
            }
        } else {
            unset($input['password']);
        }
        if(isset($input['mobile'])){
            unset($input['mobile']);
        }
        $input['id'] = get_user_id();
        if ($model = UserService::update($input)) {
            $this->success('ok', $model);
        } else {
            $this->error('error');
        }
    }

    /**
     * 更新密码
     * @login
     *
     * @api            {post}          user/updatePass          更新密码
     * @apiName        updatePass
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 获取分页数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        password                 新密码
     * @apiParam       {string}        repassword               重复密码
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updatePass()
    {
        $input = $this->formValidate([
            'password.require'            => '登录密码不能为空！',
            'repassword.require'          => '重复密码不能为空！',
            'repassword.confirm:password' => '两次输入的密码不一致！',
        ]);
        $input['id'] = get_user_id();
        if ($model = UserService::update($input)) {
            $this->success('ok', $model);
        } else {
            $this->error('error');
        }

    }

    /**
     * 更新状态
     * @auth
     *
     * @api            {post}           user/updateStatus          更新状态
     * @apiName        updateStatus
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 更新账户状态
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                          ID
     * @apiParam       {string}        status                      状态
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateStatus()
    {
        $input = $this->formValidate([
            'id.require'     => '用户ID不能为空！',
            'status.require' => '状态不能为空',
        ]);
        $model = UserService::getInfo($input['id']);
        if ($model['is_super']) {
            $this->error('超级账户禁止修改状态！');
        }
        if ($model->save($input)) {
            $this->success('ok');
        }
        $this->error('error');
    }

    /**
     * 更新身份
     * @auth
     *
     * @api            {post}           user/updateIdentity          更新身份
     * @apiName        updateIdentity
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 更新账户身份
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        id                           ID
     * @apiParam       {string}        is_admin                     是否管理员
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateIdentity()
    {
        $input = $this->formValidate([
            'id.require'       => '用户ID不能为空！',
            'is_admin.require' => '身份标识不能为空',
        ]);
        $model = UserService::getInfo($input['id']);
        if ($model['is_super']) {
            $this->error('超级账户禁止修改状态！');
        }
        if ($model->save($input)) {
            $this->success('ok');
        }
        $this->error('error');
    }

    /**
     * 删除账户
     * @auth
     *
     * @api            {post}                  user/remove                删除账户
     * @apiName        remove
     * @apiGroup       账户
     * @apiVersion     v1.0.0
     * @apiDescription 删除记录
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}                id                         ID
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function remove()
    {
        $input = $this->formValidate([
            'id.require' => '用户ID不能为空！',
        ]);
        if (UserService::remove($input['id'])) {
            $this->success('ok');
        } else {
            $this->error('error');
        }
    }
}
