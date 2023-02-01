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

class RoleService  extends Service
{

    public $model = 'core\model\Role';

    /**
     * 获取结构树
     *
     * @param  array $input
     * @return array
     */
    public static function getTree($input)
    {
        $list = self::getList($input)->toArray();
        return DataExtend::arr2tree($list);
    }

    /**
     * 获取详情
     * @param  array  $filter [description]
     * @return object         [description]
     */
    public static function getInfo($filter = [], $with = [])
    {
        $model = self::model();
        return $model->info($filter, ['auth']);
    }

    /**
     * 添加角色权限
     */
    public static function create($input)
    {

        $model = self::model();
        if ($model->save($input)) {
            // 启用core_role_auth表的时候使用：
            $auths = [];
            if (isset($input['auth']) && !empty($input['auth'])) {
                if(!is_array($input['auth'])){
                    $input['auth'] = json_decode($input['auth'], true);
                }
                $authorize = array();
                foreach ($input['auth'] as $value) {
                    array_push($authorize, $value['name']);
                    $auths[] = [
                        'role_id' => $model->id,
                        'name'    => $value['name'],
                        'half'    => isset($value['half']) ? $value['half'] : 0,
                    ];
                }
                $authorize = implode(',', $authorize);
                $model->save(compact('authorize'));
            }
            $model->auth()->saveAll($auths);
            return $model;
        }
        return false;
    }

    /**
     * 更新角色权限
     */
    public static function update($input)
    {
        if (isset($input['id']) && !empty($input['id'])) {
            $model = self::getInfo($input['id']);
        } else {
            $model = self::model();
        }
        if ($model->save($input)) {
            // 启用core_role_auth表的时候使用：
            $auths = [];
            if (isset($input['auth']) && !empty($input['auth'])) {
                if(!is_array($input['auth'])){
                    $input['auth'] = json_decode($input['auth'], true);
                }
                $authorize = array();
                foreach ($input['auth'] as $value) {
                    array_push($authorize, $value['name']);
                    $auths[] = [
                        'role_id' => $model->id,
                        'name' => $value['name'],
                        'half' => isset($value['half']) ? $value['half'] : 0,
                    ];
                }
                $authorize = implode(',', $authorize);
                $model->save(compact('authorize'));
            }
            $model->auth()->where(['role_id' => $model->id])->delete();
            $model->auth()->saveAll($auths);
            return $model;
        }
        return false;
    }

    /**
     * 删除记录
     * @param  [type] $filter [description]
     * @return [type]         [description]
     */
    public static function remove($filter, $force = false)
    {
        if (is_string($filter) && strstr($filter, ',') !== false) {
            $filter = explode(',', $filter);
        }
        if (!is_array($filter)) {
            $model = self::model()->find($filter);
            return $model->remove($force) && $model->auth()->where(['role_id' => $model->id])->remove($force);
        } else {
            $model = self::model();
            $list = $model->where($model->getPk(), 'in', $filter)->select();
            foreach ($list as $item) {
                $item->remove($force) && $item->auth()->where(['role_id' => $model->id])->remove($force);
            }
            return true;
        }
    }

    /**
     * 获取角色权限
     *
     * @param [string] $role_id 角色id
     * @return 以，分隔的角色
     */
    public static function getAuthorize($role_id)
    {
        $model = self::model();
        if (is_string($role_id) && strstr($role_id, ',') !== false) {
            $role_id = explode(',', $role_id);
        }
        $roles = $model
        ->where('id', 'in', $role_id)
        ->where(['status' => 1])
        ->column(['id','authorize','auth_level','data_level']);
        $auths = array();
        foreach ($roles as $item) {
            array_push($auths, $item['authorize']);
        }
        $auths = explode(',', implode(',', $auths));
        return implode(',', array_unique($auths));
    }

}
