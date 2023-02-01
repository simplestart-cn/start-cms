<?php

// +----------------------------------------------------------------------
// | Simplestart CMS
// +----------------------------------------------------------------------
// | 版权所有: http://www.simplestart.cn copyright 2021
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------

namespace core\controller;

use core\service\ConfigService;

/**
 * 系统配置
 * @menu
 * @auth {"sort": 11}
 */
class Config extends Base
{

    /**
     * 配置详情
     * 
     * @api            {get}              config/info           配置详情
     * @apiName        info
     * @apiGroup       系统配置
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}           [app]                 应用标识
     * @apiParam       {string}           [group]               分组标识
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function info()
    {
        $input = $this->formValidate([
            'app.default'   => '',
            'group.default' => '',        
        ]);
        $data = ConfigService::getConfig($input['app'], $input['group']);
        $this->success($data);

    }
    
    /**
     * 参数配置
     * @menu
     * @auth {"sort": 1}
     * @view /config/list
     *
     * @api            {get}    config/list                  配置列表
     * @apiName        list
     * @apiGroup       系统配置
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function list()
    {
        $data = ConfigService::getList([],'id asc');
        $this->success($data);
    }

    /**
     * 更新配置
     * @auth {"sort": 2}
     *
     * @api            {post}           config/updateList              更新配置
     * @apiName        updateList
     * @apiGroup       系统配置
     * @apiVersion     v1.0.0
     * @apiDescription 获取详情
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}           list                          配置列表
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function updateList()
    {
        $input = $this->formValidate([
            'list.require' => '配置不能为空'
        ]);
        if(ConfigService::updateList($input['list'])){
            $this->success('更新成功');
        }else{
            $this->error('更新失败');
        }
    }
}
