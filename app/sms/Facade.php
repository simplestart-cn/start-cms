<?php

namespace app\sms;

use app\sms\service\MsgService;

/**
 * 短信应用门店
 */
class Face 
{
    /**
	 * 发送短信
	 *
	 * @param  string $mobile
	 * @param  string $templateCode
	 * @param  array  $templateParams
	 * @return boolean
	 */
	public function sendMsg($mobile, $templateCode, $templateParams = [])
	{
		return MsgService::send($mobile, $templateCode, $templateParams);
	}

	/**
	 * 获取验证码
	 *
	 * @param  string  $mobile
	 * @param  integer $length
	 * @return boolean
	 */
	public function sendCode($mobile, $length = 4)
	{
		return MsgService::sendCode($mobile, $length);
	}

	/**
	 * 校对验证码
	 *
	 * @param  string $mobile
	 * @param  string $code
	 * @return boolean
	 */
	public  function checkCode($mobile, $code)
	{
		return MsgService::checkCode($mobile, $code);
	}
}
