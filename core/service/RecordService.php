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

use core\model\Operation;
use start\Service;
use start\service\NodeService;

/**
 * 日志服务
 */
class RecordService extends Service
{

    /**
     * 记录行为日志
     * @param  [type] $action  [description]
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public static function operation($action, $content = '')
    {
        $model = new Operation;
        return $model->save([
            'node'      => NodeService::instance()->getCurrent(),
            'action'    => $action,
            'content'   => $content,
            'geoip'     => \request()->ip() ?: '127.0.0.1',
            'user_id'   => get_user_id() ?: 0,
            'user_name' => get_user_name() ?: '-',
        ]);
    }

    /**
     * 获取行为日志
     * @param  array  $filder [description]
     * @return [type]         [description]
     */
    public static function getOperation($filter = [])
    {
        $model = new Operation;
        return $model->page($filter, 'create_time desc');

    }

    /**
     * 获取运行日志
     * @param  string $folder [description]
     * @param  [type] $file   [description]
     * @return [type]         [description]
     */
    public static function getRuntime($folder = '', $file = '')
    {
        $path = self::instance()->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . $folder;
        $path .= !empty($folder) ? DIRECTORY_SEPARATOR : '';
        $path .= !empty($file) ? $file : '';

        if (is_file($path)) {
            return file_get_contents($path);
        }
        if (is_dir($path)) {
            $data = array();
            foreach (glob("{$path}*") as $item) {
                $info = pathinfo($item);
                if (is_dir($item)) {
                    array_push($data, array(
                        'name'        => $info['filename'],
                        'folder'      => explode('runtime' . DIRECTORY_SEPARATOR, $item)[1],
                        'type'        => 'folder',
                        'size'        => format_bytes(self::_getFolderSize($item)),
                        'ext'         => '',
                        'update_time' => date('Y/m/d H:i', filemtime($item)),
                    ));
                } elseif (is_file($item) && pathinfo($item, PATHINFO_EXTENSION) === 'log') {
                    array_push($data, array(
                        'name'        => $info['filename'] . '.' . $info['extension'],
                        'folder'      => explode('runtime' . DIRECTORY_SEPARATOR, $item)[1],
                        'type'        => 'file',
                        'size'        => format_bytes(filesize($item)),
                        'ext'         => $info['extension'],
                        'update_time' => date('Y/m/d H:i', filemtime($item)),
                    ));
                }
            }
            return $data;
        }
        throw_error('文件或目录不存在');
    }

    /**
     * 删除运行日志
     * @param  string $folder [description]
     * @param  string $file   [description]
     * @return [type]         [description]
     */
    public static function removeRuntime($folder = '', $file = '')
    {
        $path = self::instance()->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . $folder;
        $path .= !empty($folder) ? DIRECTORY_SEPARATOR : '';
        $path .= !empty($file) ? $file : '';
        return self::_removeFolder($path);
    }

    /**
     * 删除文件或文件夹
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    private static function _removeFolder($path)
    {
        if (is_dir($path)) {
            if (!$handle = @opendir($path)) {
                throw_error($handle);
            }
            while (false !== ($file = readdir($handle))) {
                if ($file !== "." && $file !== "..") {
                    //排除当前目录与父级目录
                    $file = $path . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($file)) {
                        self::_removeFolder($file);
                        //目录清空后删除空文件夹
                        @rmdir($file . DIRECTORY_SEPARATOR);
                    } else {
                        @unlink($file);
                    }
                }
            }
            try {
                return rmdir($path);
            } catch (\Exception $e) {
                $msg = explode(': ', $e->getMessage())[1];
                throw_error($msg);
            }
        }
        if (is_file($path)) {
            try {
                return unlink($path);
            } catch (\Exception $e) {
                $msg = explode(': ', $e->getMessage())[1];
                throw_error($msg);
            }
        }
        return true;
    }

    /**
     * 获取目录大小
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    private static function _getFolderSize($path)
    {
        $size  = 0;
        $files = self::_getFolderFile($path, true);
        foreach ($files as $item) {
            $size += filesize($item);
        }
        return $size;
    }

    /**
     * 获取所有文件
     * @param string $path 扫描目录
     * @return array
     */
    private static function _getFolderFile($path, $deep = false)
    {
        $data = [];
        foreach (glob("{$path}*") as $item) {
            if (is_dir($item) && $deep) {
                $data = array_merge($data, self::_getFolderFile("{$item}" . DIRECTORY_SEPARATOR, $deep));
            } elseif (is_file($item)) {
                $data[] = strtr($item, '\\', '/');
            }
        }
        return $data;
    }
}
