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

use core\service\CaptchaService;


/**
 * 登录注册验证
 * Class Index
 * @package core\controller
 */
class Captcha extends Base
{
    /**
     * 图像验证码
     * @api            {post}    captcha/image            图像证码
     * @apiName        image
     * @apiGroup       图片验证码
     * @apiVersion     v1.0.0
     * @apiDescription 获取登录验证码
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function image()
    {
        $image = CaptchaService::instance()->initialize();
        $captcha = ['image' => $image->getData(), 'uniqid' => $image->getUniqid()];
        $this->success('生成验证码成功', $captcha);
    }

    /**
     * 手机验证码
     * @api            {post}          captcha/mobile            手机验证码
     * @apiName        mobile
     * @apiGroup       手机验证码
     * @apiVersion     v1.0.0
     * @apiDescription 获取登录验证码
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        mobile              手机号码
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function mobile()
    {
        $input = $this->formValidate([
            'mobile.require' => '手机号码不能为空',
        ], true);
        if(!app_exist('sms')){
            throw_error('短信应用不存在');
        }
        if(event('SmsCode', $input)){
            $this->success('发送成功');
        }
        $this->error('发送失败');
        
    }
}