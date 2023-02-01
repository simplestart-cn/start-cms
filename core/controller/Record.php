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

use core\service\RecordService;

/**
 * 日志管理
 * @menu
 * @auth {"sort": 14}
 * @package core\controller
 */
class Record extends Base
{
    /**
     * 操作日志
     * @auth
     * @menu
     * @view /record/operation
     *
     * @api            {get}           record/operation          操作日志
     * @apiName        operation
     * @apiGroup       系统日志
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [keyword]                 关键词(动作/行为/用户名)
     * @apiParam       {string}        [user_id]                 用户id
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function operation()
    {
        $input = $this->formValidate([
            'keyword.default' => '',
            'user_id.default' => '',
        ]);
        if (!empty($input['keyword'])) {
            $input['action@like|content@like|user_name@like'] = $input['keyword'];
            unset($input['keyword']);
        }
        $data = RecordService::getOperation($input);
        $this->success($data);
    }

    /**
     * 运行日志
     * @auth
     * @menu
     * @view /record/runtime
     *
     * @api            {get}           record/runtime               运行日志
     * @apiName        runtime
     * @apiGroup       系统日志
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [file]                文件
     * @apiParam       {string}        [folder]              目录
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function runtime()
    {
        $input = $this->formValidate([
            'file.default'   => '',
            'folder.default' => '',
        ]);
        $data = RecordService::getRuntime($input['folder'], $input['file']);
        $this->success('ok', $data);
    }

    /**
     * 删除日志
     * @auth
     * 
     * @api            {get}           record/remove               删除日志
     * @apiName        remove
     * @apiGroup       系统日志
     * @apiVersion     v1.0.0
     * @apiDescription 获取列表数据
     * @apiUse         CommonHeader
     * @apiUse         CommonParam
     * @apiParam       {string}        [file]                文件
     * @apiParam       {string}        [folder]              目录
     * @apiUse         CommonSuccess
     * @apiUse         CommonError
     */
    public function remove()
    {
        $input = $this->formValidate([
            'file.default'   => '',
            'folder.default' => '',
        ]);
        $res = RecordService::removeRuntime($input['folder'], $input['file']);
        if($res){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }
}

