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

use think\App;
use think\facade\Cache;
use think\facade\Session;

use start\Service;
use start\AppManager;
use start\extend\DataExtend;
use start\service\NodeService;

/**
 * 系统权限服务
 * Class AuthService
 * @package core\service
 */
class AuthService extends Service
{
    /**
     * @var object|string
     */
    public $model = 'core\model\Auth';

    public $user = false;

    /**
     * SESSION及Token两种方式保持登录态
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $token      = \request()->header('user-token', '');
        $this->user = Cache::get($token, false);
        if (!$this->user) {
            $this->user = Session::get('user', false);
        }
    }

    /**
     * 是否已经登录
     * @return boolean
     */
    public function isLogin()
    {
        return !!$this->user;
    }

    /**
     * 是否管理员
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->user && isset($this->user['is_admin']) && $this->user['is_admin'];
    }

    /**
     * 是否为超级账户
     * @return boolean
     */
    public function isSuper()
    {
        return $this->user && isset($this->user['is_super']) && $this->user['is_super'];
    }

    /**
     * 获取登录账户
     * @return [type] [description]
     */
    public function getUser($force = true)
    {
        if ($force && !$this->user) {
            throw_error(lang('not_login'), '', -1);
        }
        return $this->user;
    }

    /**
     * 获取账户名称
     * @return string
     */
    public function getUserName($force = true)
    {
        if ($force && !$this->user) {
            throw_error(lang('not_login'), '', -1);
        }
        return $this->user ? $this->user['name'] : '';
    }

    /**
     * 获取账户ID
     * @return integer
     */
    public function getUserId($force = true)
    {
        if ($force && !$this->user) {
            throw_error(lang('not_login'), '', -1);
        }
        return $this->user ? $this->user['id'] : 0;
    }

    /**
     * 获取openid
     *
     * @param boolean $force
     * @return void
     */
    public function getOpenId($user_id = 0)
    {
        if (empty($user_id)) {
            if ($this->user && isset($this->user['open_id']) & !empty($this->user['open_id'])) {
                return $this->user['open_id'];
            }
            throw_error(lang('not_login'), '', -2);
        }
        $openId = OauthService::getOpenId($user_id);
        if (empty($openId)) {
            throw_error(lang('not_login'), '', -2);
        }
        return $openId;
    }

    /**
     * 获取unionid
     *
     * @param boolean $force
     * @return void
     */
    public function getUnionId($user_id = 0)
    {
        if (!$user_id) {
            if ($this->user && isset($this->user['union_id']) & !empty($this->user['union_id'])) {
                return $this->user['union_id'];
            }
            throw_error(lang('not_login'), '', -2);
        }
        $unionId = OauthService::getUnionId($user_id);
        if (empty($unionId)) {
            throw_error(lang('not_login'), '', -2);
        }
        return $unionId;
    }

    /**
     * 检查指定节点授权
     * @param string $node
     * @return boolean
     */
    public function check($node = '')
    {
        if ($this->isSuper()) {
            return true;
        }
        $app = $this->app->getNamespace();
        if (stripos($app, '\\') !== false) {
            $app = substr($app, strrpos($app, "\\") + 1);
        }
        $service            = NodeService::instance();
        list($real, $nodes) = [$service->fullnode($node), $service->getMethods($app)];
        foreach ($nodes as $key => $rule) {
            if (stripos($key, '_') !== false) {
                $nodes[str_replace('_', '', $key)] = $rule;
            }
        }
        if (isset($nodes[$real]['isopen']) && $nodes[$real]['isopen']) {
            return true;
        }
        if (!empty($nodes[$real]['isauth']) || !empty($nodes[$real]['issuper']) || !empty($nodes[$real]['isadmin'])) {
            // 仅限超管员访问
            if ($nodes[$real]['issuper']) {
                return $this->isSuper();
            }
            // 仅限管理员访问
            if ($nodes[$real]['isadmin']) {
                return $this->isAdmin();
            }
            // 仅限授权用户访问
            if ($nodes[$real]['isauth']) {
                $real = str_replace('/', '_', $real);
                $authorize = $this->user['authorize'] ?? [];
                return in_array($real, $authorize);
            }
        } else {
            return !(!empty($nodes[$real]['islogin']) && !$this->isLogin());
        }
    }

    /**
     * 获取列表
     * @param  array  $filter  [description]
     * @param  array  $with    [description]
     * @param  array  $order   [description]
     * @param  [type] $calback [description]
     * @return [type]          [description]
     */
    public static function getList($filter = [], $order = ['sort asc', 'id asc'], $with = null)
    {
        $self = self::instance();
        if ($self->isSuper()) {
            $data = $self->model->filter($filter)->order($order)->select();
        } else {
            $user = get_user();
            if (empty($filter['authorize'] ?? '')) {
                $authorize = $user['authorize'];
            } else {
                $authorize = $filter['authorize'];
                unset($filter['authorize']);
            }
            $model =  $self->model->filter($filter)
                ->where('name', 'in', $authorize)
                ->where(['super' => 0, 'status' => 1]);         // 非仅限超管接口
            if ($user['is_admin']) {
                $model = $model->whereOr('admin', '=', 1);  // 管理员即可访问
            } else {
                $model = $model->where('admin', '=', 0);    // 非仅限管理接口
            }
            $data = $model->order($order)->select();
        }
        return $data->hidden(['create_time', 'update_time']);
    }

    /**
     * 获取节点树
     * @return [type] [description]
     */
    public static function getTree($filter = ['status' => 1])
    {
        $menus = self::getList($filter);
        $menus = DataExtend::arr2tree($menus->toArray());
        if (count($menus) == 1 && isset($menus[0]['children'])) {
            $menus = $menus[0]['children'];
        }
        return self::formatData($menus);
    }

    /**
     * 获取权限应用
     * @param array $authorize
     * @return void
     */
    public static function getApps(array $authorize = [])
    {
        $list = [];
        $apps = AppService::model()->where(['status' => 1])->column(['name', 'entry', 'dev_entry', 'debug']);
        $core = AppManager::getPackInfo('core');
        array_push($apps, [
            'name' => 'core',
            'entry' => $core['entry'],
            'dev_entry' => $core['dev_entry'] ?? $core['entry'],
            'debug' => env('APP_DEBUG')
        ]);
        foreach ($apps as $app) {
            $app['debug'] = boolval($app['debug']);
            if ($app['debug']) {
                $app['entry'] = $app['dev_entry'] ?? $app['entry'];
            }else{
                unset($app['debug']);
            }
            unset($app['dev_entry']);
            $app['routes'] = self::getRoute($authorize, $app['name']);
            $app['config'] = ConfigService::getConfig($app['name']);
            array_push($list, $app);
        }
        return $list;
        return array_filter($list, function ($item) {
            return !empty($item['routes']);
        });
    }

    /**
     * 获取前端路由
     * @param array $authorize
     * @param string $app
     * @return array
     */
    public static function getRoute(array $authorize = [], string $app = '')
    {
        $self             = self::instance();
        $filter['status'] = 1;
        if (empty($app)) {
            $apps          = array_merge(AppService::getActive(), ['core']);
        } else {
            $apps = [$app];
        }
        if ($self->isSuper()) {
            $data = $self->model
                ->where('status', '=', 1)
                ->where('app', 'in', $apps)
                ->order(['sort asc', 'id asc'])
                ->select();
        } else {
            if (empty($authorize)) {
                $user = get_user();
                $authorize = $user['authorize'];
            }
            $model =  $self->model->filter($filter)
                ->where('app', 'in', $apps)
                ->where('name', 'in', $authorize)
                ->where(['super' => 0, 'status' => 1]) // 非仅限超管接口
                ->whereOr(function ($query) use ($apps) {
                    $query->where('app', 'in', $apps)->where(['route' => 1, 'status' => 1]);
                });
            $data = $model->order(['sort asc', 'id asc'])->select();
        }
        $data = $data->toArray();
        array_multisort(array_column($data, 'sort'), SORT_ASC, $data);
        return $data;
    }

    /**
     * 菜单格式化
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    final private static function formatData($menus)
    {
        $routers = [];
        foreach ($menus as $key => $data) {
            $temp              = [];
            $temp['app']       = $data['app'];
            $temp['name']      = $data['name'];
            $temp['path']      = $data['path'];
            $temp['view']      = $data['view'] ?: '';
            $temp['node']      = $data['node'];
            $temp['sort']      = $data['sort'];
            if ($data['hidden'] > -1) {
                $temp['hidden'] = (bool) $data['hidden'];
            }
            if ($data['menu'] > -1) {
                $temp['menu'] = (bool) $data['menu'];
            }
            if ($data['cache'] > -1) {
                $temp['meta']['cache'] = (bool) $data['cache'];
            }
            if ($data['redirect']) {
                $temp['redirect'] = $data['redirect'];
            }
            // 参数拼接
            if (!empty($data['params'])) {
                $temp['path'] .= $data['params'];
            }
            $temp['meta']['icon']  = $data['icon'];
            $temp['meta']['title'] = $data['title'];
            // 递归格式化
            if (isset($data['children']) && count($data['children']) > 0) {
                foreach ($data['children'] as $c) {
                    $temp['children'] = self::formatData($data['children']);
                }
            }
            $routers[] = $temp;
        }
        return $routers;
    }

    /**
     * 更新自身及下级菜单
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function update($input)
    {
        if (isset($input['node']) && empty($input['path'])) {
            $input['path'] = $input['node'];
        }
        if (isset($input['id']) && !empty($input['id'])) {
            $model = self::getInfo($input['id']);
            $list  = self::getList();
            $ids   = DataExtend::getArrSubIds($list, $input['id']);
            if (count($ids) > 0 && isset($input['status'])) {
                self::saveChildren($ids, ['status' => $input['status']]);
            }
        } else {
            $model = self::model();
        }
        return $model->save($input);
    }

    /**
     * 删除自身及下级菜单
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function remove($filter, $force = false)
    {
        if (is_string($filter) && strstr($filter, ',') !== false) {
            $filter = explode(',', $filter);
        }
        $model = self::model();

        self::startTrans();
        try {
            if (!is_array($filter)) {
                // 删除子菜单
                $subIds = self::model()->where('pid', '=', $filter)->column('id');
                if (count($subIds)) {
                    self::remove($subIds);
                }
                // 删除当前记录
                $model->find($filter)->delete();
            } else {
                // 删除子菜单
                $subIds = self::model()->where('pid', 'in', $filter)->column('id');
                if (count($subIds)) {
                    self::remove($subIds);
                }
                // 删除当前记录
                $model->where($model->getPk(), 'in', $filter)->delete();
            }
            self::startCommit();
            return true;
        } catch (\Exception $e) {
            self::startRollback();
            throw_error($e->getMessage());
        }
    }

    /**
     * 更新子菜单信息
     * @param  [type] $pid   [description]
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    protected static function saveChildren($ids, $input, $field = [])
    {
        foreach ($ids as $id) {
            $item = ['id' => $id];
            foreach ($input as $key => $value) {
                if ($key !== 'id') {
                    if (count($field) > 0) {
                        in_array($key, $field) && $item[$key] = $value;
                    } else {
                        $item[$key] = $value;
                    }
                }
            }
            $data[] = $item;
        }
        return self::model()->saveAll($data);
    }
}
