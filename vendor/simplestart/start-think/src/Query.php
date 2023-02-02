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

use think\facade\Cache;
use think\db\BaseQuery;

/**
 * 自定义查询基类
 * Class Query
 * @package start
 */
trait Query
{

    public function db($scope = []): BaseQuery
    {
        $query = parent::db($scope);
        // 软删除
        $fields = $query->getTableFields();
        if (in_array($this->deleteTime, $fields) && !$this->withTrashed) {
            $this->withNoTrashed($query);
        }
        // 全局作用域(修复关联查询作用域问题,修复存在主键条件时依然使用全局查询的问题)
        if ($this->useScope && is_array($this->globalScope) && is_array($scope)) {
            $globalScope = array_diff($this->globalScope, $scope);
            $where = $this->getWhere();
            $wherePk = false;
            if (!empty($where) && is_array($where)) {
                foreach ($where as $item) {
                    if (is_string($this->pk)) {
                        if (in_array($this->pk, $item)) {
                            $wherePk = true;
                        }
                    } else if (is_array($this->pk) && count($item) > 0) {
                        if (in_array($item[0], $this->pk)) {
                            $wherePk = true;
                        }
                    }
                }
            }
            if (!$wherePk) {
                $query->scope($globalScope);
            }
        }
        return $query;
    }

    /**
     * 判断当前实例是否被软删除
     * @access public
     * @return bool
     */
    public function trashed(): bool
    {
        $field = $this->getDeleteTimeField();

        if ($field && !empty($this->getOrigin($field))) {
            return true;
        }

        return false;
    }

    /**
     * 全局查询软删除数据
     * @param BaseQuery $query
     * @return void
     */
    public function scopeWithTrashed(BaseQuery $query)
    {
        $query->removeOption('soft_delete');
    }

    /**
     * 仅查询软删除数据
     * @param BaseQuery $query
     * @return void
     */
    public function scopeOnlyTrashed(BaseQuery $query)
    {
        $field = $this->getDeleteTimeField(true);

        if ($field) {
            $query->useSoftDelete($field, $this->getWithTrashedExp());
        }
    }

    /**
     * 获取软删除数据的查询条件
     * @access protected
     * @return array
     */
    protected function getWithTrashedExp(): array
    {
        return is_null($this->defaultSoftDelete) ? ['notnull', ''] : ['<>', $this->defaultSoftDelete];
    }

    /**
     * 删除当前的记录
     * @access public
     * @return bool
     */
    public function delete(): bool
    {
        if (!$this->isExists() || $this->isEmpty() || false === $this->trigger('BeforeDelete')) {
            return false;
        }

        $name  = $this->getDeleteTimeField();
        $force = $this->isForce();

        if ($name && !$force) {
            // 软删除
            $this->set($name, $this->autoWriteTimestamp());

            $this->exists()->withEvent(false)->save();

            $this->withEvent(true);
        } else {
            // 读取更新条件
            $where = $this->getWhere();

            // 删除当前模型数据
            $this->db()
                ->where($where)
                ->removeOption('soft_delete')
                ->delete();

            $this->lazySave(false);
        }

        // 关联删除
        if (!empty($this->relationWrite)) {
            $this->autoRelationDelete($force);
        }

        $this->trigger('AfterDelete');

        $this->exists(false);

        return true;
    }

    /**
     * 删除记录
     * @access public
     * @param mixed $data 主键列表 支持闭包查询条件
     * @param bool $force 是否强制删除
     * @return bool
     */
    public static function destroy($data, bool $force = false): bool
    {
        // 传入空值（包括空字符串和空数组）的时候不会做任何的数据删除操作，但传入0则是有效的
        if (empty($data) && 0 !== $data) {
            return false;
        }
        $model = (new static());

        $query = $model->db(false);

        // 仅当强制删除时包含软删除数据
        if ($force) {
            $query->removeOption('soft_delete');
        }

        if (is_array($data) && key($data) !== 0) {
            $query->where($data);
            $data = null;
        } elseif ($data instanceof \Closure) {
            call_user_func_array($data, [&$query]);
            $data = null;
        } elseif (is_null($data)) {
            return false;
        }

        $resultSet = $query->select($data);

        foreach ($resultSet as $result) {
            /** @var Model $result */
            $result->force($force)->delete();
        }

        return true;
    }

    /**
     * 恢复被软删除的记录
     * @access public
     * @param array $where 更新条件
     * @return bool
     */
    public function restore($where = []): bool
    {
        $name = $this->getDeleteTimeField();

        if (!$name || false === $this->trigger('BeforeRestore')) {
            return false;
        }

        if (empty($where)) {
            $pk = $this->getPk();
            if (is_string($pk)) {
                $where[] = [$pk, '=', $this->getData($pk)];
            }
        }

        // 恢复删除
        $this->db(false)
            ->where($where)
            ->useSoftDelete($name, $this->getWithTrashedExp())
            ->update([$name => $this->defaultSoftDelete]);

        $this->trigger('AfterRestore');

        return true;
    }

    /**
     * 获取软删除字段
     * @access protected
     * @param bool $read 是否查询操作 写操作的时候会自动去掉表别名
     * @return string|false
     */
    protected function getDeleteTimeField(bool $read = false)
    {
        $field = property_exists($this, 'deleteTime') && isset($this->deleteTime) ? $this->deleteTime : 'delete_time';

        if (false === $field) {
            return false;
        }

        if (false === strpos($field, '.')) {
            $field = '__TABLE__.' . $field;
        }

        if (!$read && strpos($field, '.')) {
            $array = explode('.', $field);
            $field = array_pop($array);
        }

        return $field;
    }

    /**
     * 查询的时候默认排除软删除数据
     * @access protected
     * @param  BaseQuery $query
     * @return void
     */
    protected function withNoTrashed(BaseQuery $query): void
    {
        $field = $this->getDeleteTimeField(true);

        if ($field) {
            $condition = is_null($this->defaultSoftDelete) ? ['null', ''] : ['=', $this->defaultSoftDelete];
            $query->useSoftDelete($field, $condition);
        }
    }

    /**
     * 全局查询
     * 实现db对象链式调用
     * @param [type] $query
     * @param array $input
     * @return void
     */
    public function scopeFilter($query, $input = [])
    {
        return $this->filter($input, $query);
    }

    /**
     * 条件查询，支持操作符查询及关联表查询
     * @param  array  $input [description]
     * @return [type]         [description]
     *
     * input 结构支持
     * $input = 1;
     * $input = [
     *     'key1' => 1,
     *     'key2' => [1,2,3],
     *     'key3' => ['!=', 1],
     *     'key4' => ['in', [1,2,3]],
     *     'key5@!='   => 1,
     *     'key6@like' => "%$keyword%",
     *     'with.key1' => [1,2,3],
     *     'with.key2' => ['like', "%$string%"]
     *     'key1|key2' => value,
     *     'with1.key1|with2.key2' => value,
     *     'with1.key1|key2' => 
     * ];
     */
    public function filter($input = [], $query = null)
    {
        $query = $query ?? $this;  // 查询对象(Query)
        $filter = [];
        $hasModel = [];     // 已关联模型
        $hasWhere = false;  // 是否关联查询
        $hasWhereOr = [];   // 关联OR查询
        $hasWhereAnd = [];  // 关联AND查询
        $options = $query->getOptions();
        
        if (!$this->useScope) {
            $static = new static();
            $static->useScope = false;
            $query =  $static->db(null);
        }
        if (empty($input)) {
            return $query;
        }
        if (!is_array($input)) {
            return $query->where($input);
        } else if (count($input) > 0) {
            // 数据字典
            $table = $this->getTable();
            $tableFields = Cache::get($table . '_fields');
            if (empty($tableFields) || env('APP_DEBUG')) {
                $tableFields = $this->getTableFields();
                Cache::set($table . '_fields', $tableFields);
            }
            // 分割查询
            foreach ($input as $key => $value) {
                // 过滤空字段
                if($value === '' || $value === null){
                    continue;
                }
                // 过滤非表字段
                if (!in_array($key, $tableFields) && stripos($key, '|') === false && stripos($key, '@') === false && stripos($key, '.') === false) {
                    continue;
                }
                // 关联查询
                if (stripos($key, '|') !== false && stripos($key, '.') !== false) {
                    $orFields = explode('|', $key);
                    $orCondition = array();
                    foreach ($orFields as $orField) {
                        if (stripos($orField, '.') !== false) {
                            list($model, $field) = explode('.', $orField);
                            !isset($orCondition[$model]) ? $orCondition[$model] = [] : '';
                            $orCondition[$model][$field] = $value;
                            if (!in_array($model, $hasModel)) {
                                $query = $query->hasWhere($model, []);
                                array_push($hasModel, $model);
                            }
                        } else {
                            !isset($orCondition['this']) ? $orCondition['this'] = [] : '';
                            $orCondition['this'][$orField]  = $value;
                        }
                    }
                    $hasWhereOr[] = $orCondition;
                    continue;
                } else if (stripos($key, '.') !== false) {
                    list($model, $field) = explode('.', $key);
                    !isset($hasWhereAnd[$model]) ? $hasWhereAnd[$model] = [] : '';
                    $hasWhereAnd[$model][$field] = $value;
                    continue;
                }
                $filter[$key] = $value;
            }

            // 关联AND查询
            if (!empty($hasWhereAnd)) {
                $hasWhere = true;
                foreach ($hasWhereAnd as $model => $condition) {
                    $relateTable = $this->$model()->getName();
                    if (in_array($model, $hasModel)) {
                        $query = $this->parseFilter($query, $condition, $relateTable);
                    } else {
                        array_push($hasModel, $model);
                        $query = $query->hasWhere($model, $this->parseFilter($query, $condition, $relateTable));
                    }
                }
            }
            
            // 关联OR查询
            if (!empty($hasWhereOr)) {
                $that = $this;
                $hasWhere = true;
                foreach ($hasWhereOr as $relation) {
                    $query = $query->where(function ($query) use ($that, $relation) {
                        foreach ($relation as $model => $condition) {
                            $query = $query->whereOr(function ($query) use ($that, $model, $condition) {
                                if ($model === 'this') {
                                    $query = $that->parseFilter($query, $condition, $that->getName(), "OR");
                                } else {
                                    $relateTable = $that->$model()->getName();
                                    $query = $that->parseFilter($query, $condition, $relateTable, 'OR');
                                }
                            });
                        }
                    });
                }
            }       
            // 设置别名
            if ($hasWhere) {
                $query = $query->alias($this->getName());
            }
            // 主表查询
            if (is_null($query)) {
                $query = $this->parseFilter($this, $filter, $hasWhere ? $this->getName() : '');
            } else {
                $query = $this->parseFilter($query, $filter, $hasWhere ? $this->getName() : '');
            }
            return $query ?: $this;
        }
    }

    /**
     * 解析查询语句，支持操作符查询及关联表查询
     * @param  [type] $query     [description]
     * @param  array  $condition [description]
     * @param  string $table     [description]
     *  @param string $logic     查询逻辑 AND OR
     * @return [type]            [description]
     */
    private function parseFilter($query, array $condition = [], $table = '', $logic = 'AND')
    {

        $operator = ['=', '<>', '>', '>=', '<', '<=', 'like', 'not like', 'in', 'not in', 'between', 'not between', 'null', 'not null', 'exists', 'not exists', 'regexp', 'not regexp'];
        if (!empty($table) && stripos($table, '.') === false) {
            $table .= '.';
        }
        foreach ($condition as $key => $value) {
            // 空字段过滤
            if (empty($value) && !is_numeric($value)) {
                continue;
            }
            if(stripos($key, '|') !== false){
                $keys = explode('|', $key);
                $query = $logic === 'AND' ? $query->where(function($query) use ($table, $keys, $value, $operator){
                    foreach ($keys as $k) {
                        // 兼容<1.0.7版本
                        if (is_array($value) && count($value) > 1 && in_array(strtolower($value[0]), $operator)) {
                            $k = $k .'@'. $value[0];
                            $value = $value[1];
                        }
                        $query = $this->parseFilterItem($query, $table, $k, $value, "OR");
                    }
                }) : $query->whereOr(function($query) use ($table, $keys, $value, $operator){
                    foreach ($keys as $k) {
                        // 兼容<1.0.7版本
                        if (is_array($value) && count($value) > 1 && in_array(strtolower($value[0]), $operator)) {
                            $k = $k .'@'. $value[0];
                            $value = $value[1];
                        }
                        $query = $this->parseFilterItem($query, $table, $k, $value, "OR");
                    }
                });
            } else if(is_array($value)){
                // 兼容<1.0.7版本
                if (count($value) > 1 && in_array(strtolower($value[0]), $operator)) {
                    $key = $key .'@'. $value[0];
                    $value = $value[1];
                }
                $query = $this->parseFilterItem($query, $table, $key, $value, $logic);
            } else {
                $query = $this->parseFilterItem($query, $table, $key, $value, $logic);            
            }
        }
        return $query;
    }

    /**
     * 解析单个项目
     * @param object $query
     * @param string $table
     * @param string $key
     * @param string $value
     * @param string $logic
     * @return object
     */
    private function parseFilterItem($query, $table='', $key, $value, $logic = 'AND')
    {
        $opera = '=';
        if(stripos($key, '@') !== false){
            list($key, $opera) = explode('@', $key);
        }
        if ($opera === 'like' || $opera === 'not like') {
            $value = stripos($value, '%') === false ? '%' . $value . '%' : $value;
        }
        return $logic === 'AND' ? $query->where($table . $key, $opera, $value) : $query->whereOr($table . $key, $opera, $value);   
    }
}
