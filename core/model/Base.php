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

namespace core\model;

use start\Model;

/**
 * Core 基类
 */
class Base extends Model
{
    protected $readonly = ['create_time'];

    /**
     * 判断创建时间
     * @param  string $value
     * @return int
     */
    public function setCreateTimeAttr($value, $data)
    {
        if(!auth('core/data/append')){
            return time(); // 如无数据补录权限，返回当前时间
        }
        if(!empty($value) && !ctype_digit($value)){
            return strtotime(date('Y-m-d',strtotime($value)) .' '. date('H:i:s', time()));
        }
        return $value;
    }
}
