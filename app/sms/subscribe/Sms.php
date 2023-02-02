<?php

namespace app\sms\subscribe;

use app\sms\service\MsgService;

class Sms
{
    /**
     * 短信通知
     * @param array $param
     * @return boolean
     */
    public function onSmsSend(array $param)
    {
        return MsgService::send($param['mobile'], $param['templateCode'], $param['templateParams']);
    }

    /**
     * 发送验证码
     * @param array $param
     * @return boolean
     */
    public function onSmsCode(array $param)
    {
        return MsgService::sendCode($param['mobile'], $param['length'] ?? 4);
    }

    /**
     * 校验验证码
     * @param array $param
     * @return boolean
     */
    public function onSmsCheck(array $param)
    {
        return MsgService::checkCode($param['mobile'], $param['code']);
    }
}
