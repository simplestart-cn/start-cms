<?php

declare(strict_types=1);
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
use think\facade\Cache;
use think\facade\Session;


class UserService extends Service
{

    public $model = 'core\model\User';

    /**
     * 账户注册
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function register($input)
    {
        $model = self::model();
        // 账户密码注册时必须设置密码
        if (isset($input['account']) && !empty($input['account'])) {
            if (!isset($input['password']) || empty($input['password'])) throw_error('密码不能为空');
            $input['account']  = strtolower($input['account']);
            $input['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
            $User = self::model();
            if ($User::withoutGlobalScope()->where(['account' => $input['account']])->find()) {
                throw_error('账户名已存在');
            }
            if (!isset($input['name']) || empty($input['name'])) {
                $input['name'] = $input['account'];
            }
        }
        // 手机号注册时必须验证手机号
        if (isset($input['mobile']) && !empty($input['mobile'])) {
            if(!app_exist('sms')){
                throw_error('短信应用不存在');
            }
            if (!isset($input['code']) || empty($input['code'])) throw_error('验证码不能为空');
            if(!event('SmsCheck', $input)){
                throw_error('验证码有误');
            }
            if (!isset($input['name']) || empty($input['name'])) {
                $input['name'] = substr_replace($input['mobile'], '***', 3, 5);
            }
            // 验证身份
            $user = self::getInfo(['mobile' => $input['mobile']]);
            if($user){
                return $user;
            }
        }
        // 生成uniqid
        $input['uuid']   = unique_id();
        $input['status'] = 1;
        $input['is_admin'] = $input['is_admin'] ?? 0;
        $input['is_super'] = 0;
        if ($model->save($input)) {
            event('UserRegister', $model->toArray());
            return $model;
        }
        return false;
    }

    /**
     * 密码登录
     * @param  string $account  [description]
     * @param  string $password [description]
     * @return array           [description]
     */
    public static function accountLogin($account, $password)
    {
        // 验证身份
        $user = self::getInfo(['account|mobile' => strtolower($account)]);
        if (empty($user->id)) {
            throw_error('账户不存在');
        }
        //密码验证

        if (!password_verify($password, $user['password'])) {
            throw_error('密码错误!');
        }
        if ($account = self::keepLogin($user)) {
            // oplog('账户登录', '账户密码登陆');
            return $account;
        }
    }

    /**
     * 手机号登录/注册
     */
    public static function mobileLogin($mobile, $code)
    {
        if(!app_exist('sms')){
            throw_error('短信应用不存在');
        }
        if(!event('SmsCheck', compact('mobile', 'code'))){
            throw_error('验证码有误');
        }
        // 验证身份
        $user = self::getInfo(['mobile' => $mobile]);
        if (empty($user->id)) {
            // 用户注册
            $user = self::register([
                'name'   => substr_replace($mobile, '***', 3, 5),
                'mobile' => $mobile,
                'code'   => $code
            ]);
            if($user == false) throw_error('注册失败');
        }
        if ($account = self::keepLogin($user)) {
            // oplog('账户登录', '手机验证登陆');
            return $account;
        }
    }

    /**
     * 手机号绑定
     */
    public static function bindMobile($mobile, $code)
    {
        $user = get_user();
        // 验证号码
        if(!app_exist('sms')){
            throw_error('短信应用不存在');
        }
        if(!event('SmsCheck', compact('mobile', 'code'))){
            throw_error('验证码有误');
        }
        // 验证身份
        $current = self::getInfo($user['id']);
        $account = self::getInfo(['mobile' => $mobile]);
        if (!empty($account) && $current->id != $account->id) {
            // 把授权账户合并至手机注册账户
            $current->save(['delete_time' => time(), 'remark' => '已合并至' . $account->id]);
            OauthService::model()->where(['user_id' => $current->id])->save(['user_id' => $account->id]);
            $current = $account;
        }else{
            // 更新当前账户手机号
            $current->save(['mobile' => $mobile]);
        }
        // 更新登陆态
        return self::extendLogin([
            'id'        => $current->id,
            'name'      => $current->name,
            'mobile'    => $current->mobile,
            'account'   => $current->account,
            'group_id'  => $current->group_id,
            'role_id'   => $current->role_id,
        ], $user['token']);
    }

    /**
     * 保持登录态
     * @param  [type] $account [对象]
     * @param  [array] $extend [额外数据]
     * @return [type]          [description]
     */
    public static function keepLogin($account, $extend = [])
    {
        if (empty($account) || !is_object($account)) {
            throw_error('Account is not an object');
        }
        // 检测账户状态
        if (!$account->status) {
            throw_error('Account disabled');
        }
        // 更新登录信息
        $account->save([
            'login_ip'     => \request()->ip(),
            'login_time'   => time(),
            'login_client' => \request()->header('client-type'),
            'login_count'  => $account->login_count + 1,
        ]);
        // 追加登陆信息
        $account['token']        = start_encrypt($account->uuid);
        $account['timestamp']    = time();
        $account['authorize']    = $account->getAuthorize();
        // 绑定授权信息
        $oauth = OauthService::getInfo(['user_id' => $account['id']]);
        if ($oauth) {
            $account['openid']  = $oauth->openid;
            $account['unionid'] = $oauth->unionid;
        }
        // 隐藏隐私信息
        $data = $account->hidden([
            'password',
            'group_id',
            'role_id',
            'sort',
            'status',
            'create_time',
            'update_time',
            'delete_time',
            'login_ip',
            'login_time',
            'login_client',
            'login_count'
        ])->toArray();
        // 拓展登录信息
        if (!empty($extend)) {
            $data = self::extendData($data, $extend);
        }
        // 保持登陆信息
        Session::set('user', $data);
        Cache::set($data['token'], $data, 3600 * 24 * 7);
        event('UserLogin', $account);
        unset($data['is_super']);
        return $data;
    }

    /**
     * 拓展登录态
     *
     * @param  array  $data
     * @param  string $token
     * @return array
     */
    public static function extendLogin($data = [], $token = null)
    {
        if (empty($token)) {
            $user = get_user();
            $token = $user['token'];
        } else {
            $user = Cache::get($token, false);
        }
        if ($user) {
            $user = self::extendData($user, $data);
            Session::set('user', $user);
            Cache::set($token, $user, 3600 * 24 * 7); // 7天有效期
            return $user;
        }
        return false;
    }

    /**
     * 拓展数据集
     *
     * @param array $data
     * @param array $extend
     * @return $data
     */
    private static function extendData($data = [], $extend = [])
    {
        if (!empty($extend)) {
            foreach ($extend as $key => $value) {
                if (!empty($value) || $value == 0) {
                    if (isset($data[$key])) {
                        $data[$key] = is_array($data[$key]) && is_array($value) ? array_merge($data[$key], $value) : $value;
                    } else {
                        $data[$key] = $value;
                    }
                }
            }
        }
        return $data;
    }
    
    /**
     * 账户更新
     * @return array $input [description]
     */
    public static function update($input=[])
    {
        $model = self::model()->withoutGlobalScope()->find($input['id']);
        if (!$model->id) {
            throw_error('账户不存在');
        }
        // 更新账户
        if (isset($input['account']) && !empty($input['account'])) {
            $input['account'] = strtolower($input['account']);
            if (empty($model->account) || $model->account != $input['account']) {
                $User = self::model();
                if ($User::withoutGlobalScope()->where(['account' => $input['account']])->find()) {
                    throw_error('账户名已存在');
                }
            }
        }
        // 更新密码
        if (isset($input['password']) && !empty($input['password'])) {
            $input['password'] =  password_hash($input['password'], PASSWORD_BCRYPT);
        }
        // 过滤字段
        $input = array_filter($input, function ($key) {
            return !in_array($key, ['login_ip', 'login_time', 'login_count', 'create_time', 'update_time']);
        }, ARRAY_FILTER_USE_KEY);
        // 更新账户
        if ($model->save($input)) {
            return $model;
        }
    }

    /**
     * 删除记录
     * @param  [type] $filter [description]
     * @return [type]         [description]
     */
    public static function remove($filter, $force = false)
    {
        if (is_string($filter) && strstr($filter, ',') !== false) {
            $filter = explode(',', $filter);
        }
        $model = self::model();
        if (!is_array($filter)) {
            $model = $model->find($filter);
            if ($model->is_super) {
                throw_error('禁止删除超级账号！');
            }
            if($model->id == get_user_id()){
                throw_error('禁止删除自己账号！');
            }
            return $model->remove($force);
        } else {
            $list = $model->where($model->getPk(), 'in', $filter)->select();
            foreach ($list as $item) {
                if ($item->is_super) {
                    throw_error('禁止删除自己账号！');
                }
                $item->remove($force);
            }
            return true;
        }
    }

    public static function checkPassword($password, $account = null)
    {
        if (!$account) {
            $account = self::getInfo(get_user_id());
        }
        //密码验证
        return password_verify($password, $account['password']);
    }
}
