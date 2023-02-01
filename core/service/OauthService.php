<?php
declare (strict_types = 1);
// +----------------------------------------------------------------------
// | Simplestart CMS
// +----------------------------------------------------------------------
// | 版权所有: http://www.simplestart.cn copyright 2021
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------

namespace core\service;

use start\Service;

class OauthService extends Service
{
    public $model = 'core\model\Oauth';

    /**
     * 授权登陆
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function login($authInfo)
    {
        // 查询授权账户
        $authUser = false;
        if (isset($authInfo['unionid']) && !empty($authInfo['unionid'])) {
            $authUser = self::model()
                ->withoutGlobalScope()
                ->where('delete_time', '=', 0)
                ->where(function ($query) use ($authInfo) {
                    $query->where('unionid', '=', $authInfo['unionid'])->whereOr('openid', '=', $authInfo['openid']);
                })
                ->find();
        } else {
            $authUser = self::model()->withoutGlobalScope()->where(['openid' => $authInfo['openid'], 'delete_time' => 0])->find();
        }
        $authUser = $authUser ?: self::model();

        // 查询系统账户
        $baseUser = false;
        if ($authUser && $authUser->user_id) {
            $baseUser = UserService::getInfo($authUser->user_id);
        }
        // 主账户已存在则登录并返回
        if ($baseUser && !$baseUser->delete_time) {
            $authUser->save($authInfo); // 更新信息
            return UserService::keepLogin($baseUser, ['openid'  =>  $authInfo['openid'], 'unionid' => $authInfo['unionid'] ?? '']);
        }
        // 账户注册并登陆
        self::startTrans();
        try {
            $user = get_user(false);
            if($user){
                // 先登录后授权
                $baseUser = UserService::getInfo($user['id']);
                if(!$baseUser->delete_time){
                    $authInfo['user_id'] = $baseUser->id;
                }else{
                    $baseUser = false;
                }
            }
            if(!$baseUser){
                // 注册主账户
                $baseUser = UserService::register($authInfo);
            }
            // 注册授权账户
            if ($baseUser->id) {
                $authInfo['user_id'] = $baseUser->id;
                $authUser->save($authInfo);
            }
            self::startCommit();
            // 登陆主账户
            return UserService::keepLogin($baseUser, ['openid'  =>  $authInfo['openid'], 'unionid' => $authInfo['unionid'] ?? '']);
        } catch (\Exception $e) {
            self::startRollback();
            throw_error($e->getMessage());
        }
    }

    /**
     * 获取openid
     *
     * @param  string $user_id
     * @return string
     */
    public static function getOpenId($user_id)
    {
        $filter = [
            'user_id'     => $user_id,
            'client_type' => \request()->header('client-type') ?: '-',
            'plaform_type' => \request()->header('plaform-type') ?: '-'
        ];
        return self::model()->where($filter)->value('openid');
    }

    /**
     * 获取unionid
     *
     * @param  string $user_id
     * @return string
     */
    public static function getUnionId($user_id)
    {
        $filter = [
            'user_id'     => $user_id,
            'client_type' => \request()->header('client-type') ?: '-',
            'plaform_type' => \request()->header('plaform-type') ?: '-'
        ];
        return self::model()->where($filter)->value('unionid');
    }
}