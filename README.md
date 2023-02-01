# 介绍
StartCMS是一个基于ThinkPHP6.0+、ElementUI、MicroApp的极速**微应用**开发框架    
前端不限技术栈，支持Vue2、Vue3、Vite、React、Rangular...  
后端不限制语言，支持PHP、Java、Node、Python、Go、C#...    
始终秉承 开源 开放 自由的理念  
让我们简单地开始！  
Simplestart!

## 主要特性
- 大道至简：采用标准CMS(Controller+Model+Service)分层设计，全新开发模式和开发规范给开发更好体验
- 框架独立：分离框架代码与应用代码，框架只保留核心功能，各类业务功能以应用形式实现
- 应用独立：每个应用都可以独立开发、独立运行、独立部署，再将这些小型应用融合为一个完整的应用
- 不限技术：前后端可以用任意语言任意技术栈开发，可以局部/增量升级，代码简洁、解耦、更易维护
- 不限架构：支持微应用架构的同时也兼容单体架构开发，不同应用的前端可分离也可在基座上直接开发
- 命令优化：完善的命令支持，一行命令即可启动前后端服务，无需再搭建本地站点开发
- 代码生成：内置高效代码生成器，一键生成优雅的CURD相关接口及接口文档
- 注解文档：集成[APIDOC](https://apidocjs.com)注解文档，一键生成可调试接口文档
- 注解权限：接口注释添加@super, @auth, @admin, @login等标签即可完成权限控制并生成前端路由
- 角色权限：内置完善的多角色功能权限控制，无限父子级权限分组
- 数据权限：基于组织架构的行数据权限控制，支持无限级组织架构设置
- 全站事件：跨应用事件分发，事件监听、事件订阅自动化完成，无需手动绑定
- 通用模型：内置快速关联查询，分页查询，列表查询，详情查询，数据更新及删除
- 通用服务：模型自动关联，内置快速分页查询，列表查询，详情查询，数据更新及删除
- 通用控制器：快速参数格式校验，安全验证可自动化完成CSRF安全验证
## 环境要求
PHP >= 7.3.0  
Mysql >= 5.6  
Nginx 或 Apache 建议Nginx  
Nginx 或 Apache 都需要配置伪静态   
启用函数 putenv proc_open(composer安装扩展时用到)  

## 演示站点
- 演示地址：[http://demo.startcms.cn](http://demo.startcms.cn)
- 账户密码：admin/admin
## 开发文档
- [点击访问](http://doc.startcms.cn)

## 开发部署
1. [点击下载](https://github.com/simplestart-cn/start-cms/archive/refs/heads/main.zip) 或 ```git clone git@github.com:simplestart-cn/start-cms.git```
2. 安装：```cd start-cms && composer install```
3. 启动：
   - 方式1(仅启动后端服务)：```php start run```
   - 方式2(同时启动前后端)：```npm install && npm run dev```
> 如果composer install失败，请尝试在命令行进行切换配置到国内源，命令如下  
```composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/```

## 正式部署
> 一般内置服务已足以开发使用，当部署至服务器环境正式使用时需要进行如下配置
1. 将域名解析至系统根目录  
2. 修改伪静态配置（请参考下方伪静态设置）
3. 修改环境安全配置（请参考下方安全配置）  
4. 访问您的域名打开站点，填写数据库配置信息即可一键安装

## 伪静态（必须） 
- Niginx
  - 修改nginx.conf配件文件，添加以下内容即可
```
location / {
  index web index.html index.php
   if (!-e $request_filename){
      rewrite  ^(.*)$  /index.php?s=$1  last;   break;
   }
}
location /web {
    try_files $uri $uri/ /web/index.html;
}
```  
- Apache
  - 确保已经启用Apache的伪静态，确保目录已经配置好权限,修改配置
  - 把下面的内容保存为.htaccess文件放到根目录下
```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```
  - 把下面的内容保存到httpd.conf或者httpd-vhost.conf
```
<Directory "你的站点目录/web">
    AllowOverride All
    Allow from all
    Require all granted
    DirectoryIndex index.html index.php index.htm
    Options +FollowSymlinks -Multiviews
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.html?/$1 [QSA,PT,L]
</Directory>
```  

## 安全配置(必须)
- LNMP环境（Linux + Nginx + Mysql + PHP）
  - 修改nginx.conf配件文件，添加以下内容
  ```
    #禁止访问的文件或目录
    location ~ ^/(\.env|\.user.ini|\.htaccess|\.git|\.svn|\.project)
    {
        return 404;
    }
  ```
  - ...
- LAMP环境（Linux + Apache + Mysql + PHP）
  - 把下面的内容保存到httpd.conf或者httpd-vhost.conf
  - ...
## 交流改进
请通过 [GitHub Issues](https://github.com/simplestart-cn/start-cms/issues) 反馈 bug 和 feature 

## 相关项目
- [ApiDoc](https://apidocjs.com)
- [MicroApp](https://micro-zoe.github.io/micro-app)
- [ThinkPHP](https://www.kancloud.cn/manual/thinkphp6_0/1037479)
- [ElementUI](https://element.eleme.cn/#/zh-CN)
- [FormCreate](http://www.form-create.com/)

