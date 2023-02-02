<?php
namespace app\sms\service;

use start\Service;
use app\sms\library\Driver;

/**
 * 短信服务
 */
class MsgService extends Service
{
	/**
	 * 发送短信
	 *
	 * @param  string $mobile
	 * @param  string $templateCode
	 * @param  array  $templateParams
	 * @return boolean
	 */
	public static function send($mobile, $templateCode, $templateParams = [])
	{
		$config = conf('sms');
		$driver = new Driver($config);
        $result = $driver->sendMsg($mobile, $templateCode, $templateParams);
		if(!$result){
			throw_error($driver->getError());
		}
		return true;
	}

	/**
	 * 获取验证码
	 *
	 * @param  string  $mobile
	 * @param  integer $length
	 * @return boolean
	 */
	public static function sendCode($mobile, $length = 4)
	{
		// 生成验证码
		$charset = '0123456789';
        list($code, $len) = ['', strlen($charset) - 1];
        for ($i = 0; $i < $length; $i++) {
            $code .= $charset[mt_rand(0, $len)];
        }
        // 缓存验证码
		$self = self::instance();
        $self->app->cache->set($mobile, $code, 600);
		// 获取配置
		$config = conf('sms');
		if(!isset($config['register_code']) || empty($config['register_code'])){
			throw_error('未设置短信模板');
		}
		// 发送验证码
		$driver = new Driver($config);
        $result = $driver->sendMsg($mobile, $config['register_code'], ['code' => $code]);
		if(!$result){
			throw_error($driver->getError());
		}
		return true;
	}

	/**
	 * 校对验证码
	 *
	 * @param  string $mobile
	 * @param  string $code
	 * @return boolean
	 */
	public static function checkCode($mobile, $code)
	{
		$self = self::instance();
        $_val = $self->app->cache->get($mobile, '');
        if (is_string($_val) && strtolower($_val) === strtolower($code)) {
            $self->app->cache->delete($mobile);
            return true;
        } else {
            return false;
        }
	}
}