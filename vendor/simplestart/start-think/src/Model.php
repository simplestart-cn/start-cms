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

use start\Query;
use think\Container;

/**
 * 自定义模型基类
 * Class Model
 * @package start
 */
class Model extends \think\Model
{
    /**
     * 自定义查询
     */
    use Query;

    /**
     * 模型名称
     * @var string
     */
    protected $name;

    /**
     * 关联
     * @var array
     */
    protected $with = [];

    /**
     * 查询
     * @var array
     */
    protected $where = [];

    /**
     * 排序
     * @var array
     */
    protected $order = [];

    /**
     * 使用全局查询
     * @var boolean
     */
    public $useScope = true;

    /**
     * 软删除字段
     * @var string
     */
    protected $deleteTime = 'delete_time';

    /**
     * 默认软删除值
     *
     * @var integer
     */
    protected $defaultSoftDelete = 0;

    /**
     * 架构函数
     * @access public
     * @param array $data 数据
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        // 固定name属性为模型名(解决TP关联查询alias别名问题)
        if (!empty($this->name)) {
            if (empty($this->table)) {
                $this->table = $this->name;
            }
            $name       = str_replace('\\', '/', static::class);
            $this->name = basename($name);
        }
        // 执行初始化操作
        $this->initialize();
    }

    // 模型初始化
    protected static function init()
    {
        self::instance()->initialize();
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
     * 获取列表数据
     * @param     array                         $filter   [description]
     * @param     array                         $order    [description]
     * @param     array                         $with     [description]
     * @return    collection                              [description]
     */
    function list($filter = [], $order = [], $with = null)
    {
        $with  = is_array($with) ? $with : $this->with;
        $order = !empty($order) ? $order : $this->order;
        $where = is_array($filter) ? array_merge($this->where, $filter) : $filter;
        return $this
            ->filter($where)
            ->with($with)
            ->order($order)
            ->select();
    }

    /**
     * 获取分页数据
     * @param     array                         $filter   [description]
     * @param     array                         $order    [description]
     * @param     array                         $with     [description]
     * @param     array                         $paging   [description]
     * @return    collection                              [description]
     */
    public function page($filter = [], $order = [], $with = null, $paging = [])
    {
        $with  = is_array($with) ? $with : $this->with;
        $order = !empty($order) ? $order : $this->order;
        $where = is_array($filter) ? array_merge($this->where, $filter) : $filter;
        if (!is_array($paging)) {
            $paging = ['page' => (int) $paging];
        }
        if (!isset($paging['page'])) {
            $paging['page'] = input('page', 1, 'trim');
        }
        if (!isset($paging['per_page'])) {
            $paging['list_rows'] = input('per_page', 30, 'trim');
        }
        return $this
            ->filter($where)
            ->with($with)
            ->order($order)
            ->paginate($paging, false);
    }

    /**
     * 获取详情
     * @param     array                         $filter   [description]
     * @param     array                         $with     [description]
     * @return    object                                  [description]
     */
    public function info($filter, $with = null)
    {
        $with  = is_array($with) ? $with : $this->with;
        $where = is_array($filter) ? array_merge($this->where, $filter) : $filter;
        if (!is_array($filter)) {
            return $this->with($with)->find($where);
        } else {
            return $this->filter($where)->with($with)->find();
        }
    }

    /**
     * 删除数据
     * @return boolean
     */
    public function remove($force = false)
    {
        $fields = $this->getTableFields();
        if($force || !in_array($this->deleteTime, $fields)){
            return $this->force()->delete();
        }
        return $this->delete();
    }

    /**
     * 更新数据
     * 注：修复TP6.0.2开启全局查询时没有添加自动加上主键查询条件的问题
     * @access public
     * @param array  $data       数据数组
     * @param mixed  $where      更新条件
     * @param array  $allowField 允许字段
     * @param string $suffix     数据表后缀
     * @return static
     */
    public static function update(array $data, $where = [], array $allowField = [], string $suffix = '')
    {
        $model = new static($data);
        $pk    = $model->getPk();
        if (!isset($data[$pk]) || empty($data[$pk])) {
            throw_error("$pk can not empty");
        }
        $result = $model->find($data[$pk]);
        if (empty($result)) {
            throw_error($model->name . ' not found');
        }

        if (!empty($allowField)) {
            $result->allowField($allowField);
        }

        if (!empty($where)) {
            $result->setUpdateWhere($where);
        }

        if (!empty($suffix)) {
            $result->setSuffix($suffix);
        }

        $result->exists(true)->save($data);

        return $result;
    }

    /**
     * 关闭全局查询
     * 修复tp6的大问题
     *
     * @param array $scope
     * @return this
     */
    public function withoutScope(array $scope = null)
    {
        if (is_array($scope)) {
            $this->globalScope = array_diff($this->globalScope, $scope);
        }
        if (empty($this->globalScope) || $scope == null) {
            $this->useScope = false;
        }
        return $this;
    }
 
}
