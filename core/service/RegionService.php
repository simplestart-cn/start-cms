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

namespace core\service;

use start\Service;
use start\extend\DataExtend;
use think\facade\Cache;

/**
 * 地区服务
 */
class RegionService  extends Service
{
    public $model = 'core\model\Region';

    /**
     * 追加了has_children
     * @param  [type] $filter [description]
     * @param  array  $order  [description]
     * @return [type]         [description]
     */
    public static function getPage($filter=[], $order=[], $with=null)
    {
        $all = self::getAll(false);
        $data = self::model()->page($filter, $order)->toArray();
        $pids = array_unique(array_column($all, 'pid'));
        foreach ($data['data'] as &$item) {
            if(in_array($item['id'], $pids)){
                $item['has_children'] = 1;
            }else{
                $item['has_children'] = 0;
            }
        }
        return $data;
    }

    /**
     * 追加了has_children
     * @param  [type] $filter [description]
     * @param  array  $order  [description]
     * @return [type]         [description]
     */
    public static function getList($filter=[], $order=[], $with=null)
    {
        $all = self::getAll(false);
        $data = self::model()->list($filter, $order)->toArray();
        $pids = array_unique(array_column($all, 'pid'));
        foreach ($data as &$item) {
            if(in_array($item['id'], $pids)){
                $item['has_children'] = 1;
            }else{
                $item['has_children'] = 0;
            }
        }
        return $data;
    }

    /**
     * 获取树状数据
     * @return [type] [description]
     */
    public static function getTree($input)
    { 
        $list  = self::getList($input);
        $tree  = DataExtend::arr2tree($list);
        if(count($tree) == 1 && isset($tree[0]['children'])){
            $tree = $tree[0]['children'];
        }
        return $tree;
    }

    /**
     * 获取所有地区
     * @param  boolean $force [description]
     * @return [type]         [description]
     */
    public static function getAll($force = false)
    {
        if(($data = Cache::get('all_region')) && !$force){
            return $data;
        }
        $data = self::model()->list()->toArray();
        Cache::set('all_region', $data);
        return $data;
    }

    /**
     * 获取所有开发地区
     * @param  boolean $force [description]
     * @return [type]         [description]
     */
    public static function getAllOpen($force = false)
    {
        if(($data = Cache::get('all_region')) && !$force){
            return $data;
        }
        $data = self::model()->list(['status' => 1])->toArray();
        Cache::set('all_region', $data);
        return $data;
    }

}