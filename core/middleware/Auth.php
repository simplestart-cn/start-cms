<?php
declare (strict_types = 1);
// +----------------------------------------------------------------------
// | Simplestart CMS
// +----------------------------------------------------------------------
// | 版权所有: http://www.simplestart.cn copyright 2021
// +----------------------------------------------------------------------
// | 开源协议: https://www.apache.org/licenses/LICENSE-2.0.txt
// +----------------------------------------------------------------------
// | 仓库地址: https://github.com/simplestart-cn/start-cms
// +----------------------------------------------------------------------

namespace core\middleware;
use core\service\AuthService;

/**
 * 权限中间件
 */
class Auth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 自定义头部
        $header = [];
        if (($origin = $request->header('origin', '*')) !== '*') {
            $header['Access-Control-Allow-Origin'] = $origin;
            $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE';
            $header['Access-Control-Allow-Headers'] = 'Authorization,Content-Type,If-Match,If-Modified-Since,If-None-Match,If-Unmodified-Since,X-Requested-With';
            $header['Access-Control-Expose-Headers'] = 'App-Id,App-Secret,Client-Type,Plaform-Type,User-Form-Token,User-Token,Token';
        }
        // 安装模式
        if(($request->root() == '/core' && stripos($request->pathinfo(), 'install') !== false)){
            return $next($request)->header($header);
        }
        // 安装重定向
        if(!is_file(root_path() . '.env')){
            header("Location: /core/install");
            exit;
        }
        // 访问权限检查
        if ($request->isOptions()) {
            return response()->code(204)->header($header);
        } elseif (env('AUTH_DEBUG') || AuthService::instance()->check()) {
            return $next($request)->header($header);
        } elseif (AuthService::instance()->isLogin()) {
            return json(['code' => 403, 'msg' => lang('not_auth')])->header($header);
        } else {
            return json(['code' => 401, 'msg' => lang('not_login'), 'url' => url('core/index/index')])->header($header);
        }
    }
}
