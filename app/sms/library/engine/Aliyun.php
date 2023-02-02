<?php

namespace app\sms\library\engine;

use app\sms\library\helper\AliyunSignature;

/**
 * 阿里云短信模块引擎
 * Class Aliyun
 * @package app\sms\library\sms\engine
 */
class Aliyun extends Server
{
    private $config;

    /**
     * 构造方法
     * Qiniu constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 发送短信通知
     * @param $mobile
     * @param $templateCode
     * @param $templateParams
     * @return bool|\stdClass
     */
    public function sendMsg($mobile, $templateCode, $templateParams = [])
    {
        $params = [];
        // *** 需用户填写部分 ***

        // 必填: 短信接收号码
        $params["PhoneNumbers"] = $mobile;

        // 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $this->config['aliyun_sign'];

        // 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $templateCode;

        // 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $templateParams;

        // 可选: 设置发送短信流水号
        // $params['OutId'] = "12345";

        // 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        // $params['SmsUpExtendCode'] = "1234567";

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (is_array($params["TemplateParam"])) {
            if(!empty($params["TemplateParam"])){
                $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
            }else{
                $params["TemplateParam"] = '';
            }
        }
        // 初始化AliyunSignature实例用于设置参数，签名以及发送请求
        $helper = new AliyunSignature;
        // 此处可能会抛出异常，注意catch
        $response = $helper->request(
            $this->config['aliyun_access_key'],
            $this->config['aliyun_access_secret'],
            "dysmsapi.aliyuncs.com",
            array_merge($params, [
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ]),
            // 选填: 启用https
            true
        );
        $this->error = $response->Message;
        return $response->Code === 'OK';
    }

}
