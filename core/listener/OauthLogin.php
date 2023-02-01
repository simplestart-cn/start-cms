<?php

namespace core\listener;

use core\service\OauthService;

// 监听授权登录事件
class OauthLogin
{
    public function handle(array $authInfo)
    {
        if (empty($authInfo['app_id'] ?? '')) {
            throw_error('未知应用app_id');
        }
        if (empty($authInfo['client_type'] ?? '')) {
            throw_error('未知客户端类型client_type');
        }
        if (empty($authInfo['plaform_type'] ?? '')) {
            throw_error('未知平台类型plaform_type');
        }
        if (empty($authInfo['openid'] ?? '') && empty($authInfo['unionid'])) {
            throw_error('未知openid和unionid');
        }
        $user = [
            'name'         => preg_replace('/[\xf0-\xf7].{3}/', '', $authInfo['name']) ?? '匿名',
            'avatar'       => $authInfo['avatar'] ?? '',
            'gender'       => $authInfo['gender'] ?? 0,
            'country'      => $authInfo['country'] ?? 0,
            'province'     => $authInfo['province'] ?? 0,
            'city'         => $authInfo['city'] ?? 0,
            'openid'       => $authInfo['openid'],
            'unionid'      => $authInfo['unionid'] ?? '',
            'client_type'  => $authInfo['client_type'],
            'plaform_type' => $authInfo['plaform_type'],
        ];
        OauthService::login($user);
    }
}
