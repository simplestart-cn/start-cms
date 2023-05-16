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
        if (empty($authInfo['open_id'] ?? '') && empty($authInfo['union_id'] ?? '')) {
            throw_error('open_id和union_id不能同时为空');
        }
        if(!empty($authInfo['name'] ?? '')){
            // 清除emote
            $authInfo['name'] = preg_replace('/[\xf0-\xf7].{3}/', '', $authInfo['name']);
        }else{
            $authInfo['name'] = '匿名';
        }
        $user = [
            'name'         => $authInfo['name'],
            'avatar'       => $authInfo['avatar'] ?? '',
            'gender'       => $authInfo['gender'] ?? 0,
            'country'      => $authInfo['country'] ?? 0,
            'province'     => $authInfo['province'] ?? 0,
            'city'         => $authInfo['city'] ?? 0,
            'app_id'        => $authInfo['app_id'],
            'open_id'       => $authInfo['open_id'],
            'union_id'      => $authInfo['union_id'] ?? '',
            'client_type'  => $authInfo['client_type'],
            'plaform_type' => $authInfo['plaform_type'],
        ];
        return OauthService::login($user);
    }
}
