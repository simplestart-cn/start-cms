<?php

// 头部参数
/**
 * @apiDefine CommonHeader
 * @apiHeader         (HTTP)                {string}  APP_MODE=api           应用模式
 * @apiHeader         (HTTP)                {string}  [VERSION="v1.0.0"]     接口版本
 * @apiHeader         (HTTP)                {string}  [USER_TOKEN]           USER TOKEN
 * @apiHeader         (HTTP)                {string}  [CSRF_TOKEN]           CSRF TOKEN
 * @apiHeader         (HTTP)                {string}  [CLIENT_TYPE]          客户端类型(app/applet/h5/web)
 * @apiHeader         (HTTP)                {string}  [PLAFORM_TYPE]         开放平台(weixin/alipay/baidu/toutiao/...)
 * @apiHeader         (HTTP)                {string}  [DATA_TYPE='json']     请求的资源类型(json/xml)
 * @apiHeader         (HTTP)                {string}  [SECRET]               接口密码(按规则生成)
 * @apiHeader         (HTTP)                {string}  [ENCRYPTED]            加密数据
 * @apiHeader         (HTTP)                {number}  [TIMESTAMP=now()]            请求时间戳
 */
 
// 全局参数
/**
 * @apiDefine  CommonParam
 * @apiParam {string} signature  数据签名
 */

//	翻页参数
/**
 * @apiDefine PagingParam
 * @apiParam       {number}        [page=1]      页码
 * @apiParam       {number}        [per_page=15] 每页数量
 */

// 成功状态
/**
 * @apiDefine CommonSuccess
 * @apiSuccess {number} code=1         状态码
 * @apiSuccess {Object} data={}        数据信息
 * @apiSuccess {string} msg="success"  状态消息
 */

// 失败状态
/**
 * @apiDefine CommonError
 * @apiError {number} code=0         状态码
 * @apiError {string} msg="error"    状态消息
 */


/////////////////  常用命令 /////////////////
// @api
// @apiDefine
// @apiDeprecated
// @apiDescription
// @apiError
// @apiErrorExample
// @apiExample
// @apiGroup
// @apiHeader
// @apiHeaderExample
// @apiIgnore
// @apiName
// @apiParam
// @apiParamExample
// @apiPermission
// @apiPrivate
// @apiSampleRequest
// @apiSuccess
// @apiSuccessExample
// @apiUse
// @apiVersion

/////////////////  IDE快速注释配置说明 /////////////////
// Sublime Text DocBlockr 快速注释设置，其他编辑器另寻方法 //
// 普通函数注释 
// {
//   "jsdocs_extra_tags":[
//      // 普通函数注释
//   	"@Description ${1:[description]}",
//      "@Author Colber.Dun",
//      "@Date [{{date}}]",
//      "@Copyright https://www.simplestart.cn"
//   ],
//   "jsdocs_function_description": false
// }
// 
// 接口函数注释  
// {
// 	"jsdocs_extra_tags" : [
// 	    // 接口函数注释
// 	    "@api {${1:method}} ${2:path} ${3:title}",
// 	    "@apiName ${1:接口名称}",
// 	    "@apiGroup ${1:GroupName}",
// 	    "@apiVersion 1.0.0",
// 	    "@apiDescription ${1:接口详细描述}",
// 	    "@apiUse CommonHeader",
// 	    "@apiUse CommonParam",
// 	    "@apiParam {${1:type}} ${2:[field=defaultValue]} ${4:[description]}",
// 	    "@apiUse CommonSuccess",
// 	    "@apiSuccess ${1:{type}} ${2:data} ${3:[description]}",
// 	    "@apiUse CommonError",
// 	    "@apiAuthor [Colber.Dun]"
// 	],
// 	"jsdocs_function_description": false,
// 	"jsdocs_extra_tags_go_after" : false
// }
// Sbulime Text snippet 片段(一个文件一个片段)
// <snippet>
// 	<content><![CDATA[
// @apiParam       {${1:type}}        ${2:[field=defaultValue]} ${3:[description]}
// ]]></content>
// 	<tabTrigger>apiParam</tabTrigger>
// </snippet>

// <snippet>
// 	<content><![CDATA[
// @apiSuccess       {${1:type}}        ${2:[field=defaultValue]} ${3:[description]}
// ]]></content>
// 	<tabTrigger>apiSuccess</tabTrigger>
// </snippet>

// <snippet>
// 	<content><![CDATA[
// @apiError       {${1:type}}        ${2:[field=defaultValue]} ${3:[description]}
// ]]></content>
// 	<tabTrigger>apiError</tabTrigger>
// </snippet>

