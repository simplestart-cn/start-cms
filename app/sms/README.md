## 短信服务
> 提供短信验证，短信通知功能，已接通阿里云、腾讯云短信服务
## 运行条件  
* PHP >= 7.3
* Mysql >= 5.6
* StartCMS > 1.0.0

## 运行说明
* 安装后应用订阅以下短信事件，返回服务执行结果
```php
// 发送短信
event('SmsSend', [
    'mobile' => '',         // 手机号
    'templateCode' => '',   // 模板ID
    'templateParams' => []  // 模板参数
]);
// 发送验证码
event('SmsCode', [
    'mobile' => '', // 手机号
    'length' => 4,  // 验证码长度
]);
// 验证验证码
event('SmsCheck', [
    'mobile' => '', // 手机号
    'code'   => '', // 验证码
]);
```
