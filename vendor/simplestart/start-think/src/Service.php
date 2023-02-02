<?php

// +----------------------------------------------------------------------
// | Simplestart Think
// +----------------------------------------------------------------------
// | 版权所有: https://www.simplestart.cn copyright 2020
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-think
// +----------------------------------------------------------------------

namespace start;

use think\App;
use start\Model;
use think\Container;
use think\Collection;

/**
 * 自定义服务基类
 * Class Service
 * @package start
 */
abstract class Service
{
    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 服务名称
     * @var string
     */
    protected $name;

    /**
     * 服务模型
     * @var Model
     */
    public $model;

    /**
     * Service constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        // 初始化名称
        if (empty($this->name)) {
            // 当前模型名
            $name       = str_replace('\\', '/', static::class);
            $this->name = basename($name);
        }
        // 绑定数据模型
        $namespace = $this->app->getNamespace();
        if (!empty($this->model)) {
            if (class_exists($this->model)) {
                $this->model = Container::getInstance()->make($this->model);
            } else if (class_exists($object = "{$namespace}\\model\\{$this->model}")) {
                $this->model = Container::getInstance()->make($object);
            } else {
                throw_error("Model $this->model does not exist.");
            }
        } else {
            if (class_exists($object = "{$namespace}\\model\\{$this->name}")) {
                $this->model = Container::getInstance()->make($object);
            }
        }
        $this->initialize();
    }

    /**
     * 初始化服务
     * @return $this
     */
    protected function initialize()
    {
        return $this;
    }

    /**
     * 静态实例对象
     * @param array $args
     * @return static
     */
    public static function instance(...$args)
    {
        return Container::getInstance()->make(static::class, $args);
    }

    /**
     * 获取模型
     * @return Model
     */
    public static function model()
    {
        $model = self::instance()->model;
        if (!is_object($model)) {
            throw_error("Model does not exist.");
        }
        return new $model;
    }

    /**
     * 获取模型
     * @param  string      $scope       查询范围
     * @return object      $this->model 模型实例
     */
    public static function withoutScope(array $scope = null)
    {
        $model = self::instance()->model;
        if (!is_object($model)) {
            throw_error("Model does not exist.");
        }
        return (new $model)->withoutScope($scope);
    }

    /**
     * 获取列表
     * @param  array  $filter 查询条件
     * @param  array  $order  排序方式
     * @param  array  $with   关联查询
     * @return Collection
     */
    public static function getList($filter = [], $order = [], $with = null)
    {
        $model = self::model();
        return $model->list($filter, $order, $with);
    }

    /**
     * 获取分页
     * @param  array  $filter 查询条件
     * @param  array  $order  排序方式
     * @param  array  $with   关联查询
     * @return Collection
     */
    public static function getPage($filter = [], $order = [], $with = null)
    {
        $model = self::model();
        return $model->page($filter, $order, $with);
    }

    /**
     * 获取详情
     * @param  array  $filter 查询条件
     * @param  array  $with   关联查询
     * @return Model
     */
    public static function getInfo($filter, $with = null)
    {
        $model = self::model();
        return $model->info($filter, $with);
    }

    /**
     * 创建记录
     * @param  array  $input [description]
     * @return Model
     */
    public static function create($input)
    {
        $model = self::model();
        if ($model->save($input)) {
            return $model;
        } else {
            throw_error('create fail');
        }
    }

    /**
     * 更新记录
     * @param  array  $input [description]
     * @return Model
     */
    public static function update($input)
    {
        $model = self::model();
        $pk    = $model->getPk();
        if (!isset($input[$pk]) || empty($input[$pk])) {
            throw_error("$pk can not empty");
        }
        $model = $model->find($input[$pk]);
        if ($model->save($input)) {
            return $model;
        } else {
            throw_error('update fail');
        }
    }

    /**
     * 批量更新
     * @param array $list
     * @param array $allowField
     * @return Collection
     */
    public static function saveAll(array $list, array $allow_field = [])
    {
        if(!empty($allow_field)){
            return self::model()->allowField($allow_field)->saveAll($list);
        }else{
            return self::model()->saveAll($list);
        }
    }

    /**
     * 导入记录
     * @param  array $list   [导入列表]
     * @param  array $keys   [查重字段]
     * @return array         [成功及失败数量]
     */
    public static function import(array $list, array $keys=[])
    {
        if (!is_array($list)) {
            $list = json_decode($list, true);
        }
        $success = 0; // 成功记录数
        $error   = 0; // 失败记录数
        self::startTrans();
        try {
            foreach ($list as $item) {
                $where = array_reduce($keys, function ($result, $key) use ($item) {
                    if(isset($item[$key])){
                        return array_merge($result, [$key => $item[$key]]);
                    }
                    return $result;
                }, []);
                $model = self::getInfo($where);
                if ($model) {
                    $model->save($item) ? $success++ : $error++;
                } else {
                    $model = self::model();
                    $model->save($item) ? $success++ : $error++;
                }
            }
            self::startCommit();
            return compact('success', 'error');
        } catch (\Exception $e) {
            self::startRollback();
            throw_error($e->getMessage());
            return false;
        }
    }

    /**
     * 删除记录
     * @param  array  $filter  删除条件
     * @param  boolean $force  是否硬删除
     * @return boolean         [description]
     */
    public static function remove($filter, $force = false)
    {
        if (is_string($filter) && strstr($filter, ',') !== false) {
            $filter = explode(',', $filter);
        }
        $model = self::model();
        if (!is_array($filter)) {
            return $model->find($filter)->remove($force);
        } else {
            $list = $model->where($model->getPk(), 'in', $filter)->select();
            foreach ($list as $item) {
                $item->remove($force);
            }
            return true;
        }
    }

    /**
     * 开启事务(待升级为异步事务)
     * @return void
     */
    public static function startTrans()
    {
        \think\facade\Db::startTrans();
    }

    /**
     * 事务提交(待升级为异步事务)
     * @return void
     */
    public static function startCommit()
    {
        \think\facade\Db::commit();
    }

    /**
     * 事务回滚(待升级为异步事务)
     * @return void
     */
    public static function startRollback()
    {
        \think\facade\Db::rollback();
    }
}
