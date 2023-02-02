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

namespace start\extend;

/**
 * 数据处理扩展
 * Class DataExtend
 * @package start\extend
 */
class DataExtend
{

    /**
     * 一维数组生成数据树
     * @param array $list 待处理数据
     * @param string $cid 自己的主键
     * @param string $pid 上级的主键
     * @param string $sub 子数组名称
     * @return array
     */
    public static function arr2tree($list, $cid = 'id', $pid = 'pid', $sub = 'children')
    {
        list($tree, $tmp) = [[], array_combine(array_column($list, $cid), array_values($list))];
        foreach ($list as $vo) isset($vo[$pid]) && isset($tmp[$vo[$pid]]) ? $tmp[$vo[$pid]][$sub][] = &$tmp[$vo[$cid]] : $tree[] = &$tmp[$vo[$cid]];
        unset($tmp, $list);
        return $tree;
    }

    /**
     * 一维数组生成数据树
     * @param array $list 待处理数据
     * @param string $cid 自己的主键
     * @param string $pid 上级的主键
     * @param string $cpath 当前 PATH
     * @param string $ppath 上级 PATH
     * @return array
     */
    public static function arr2table(array $list, $cid = 'id', $pid = 'pid', $cpath = 'path', $ppath = '')
    {
        $tree = [];
        foreach (self::arr2tree($list, $cid, $pid) as $attr) {
            $attr[$cpath] = "{$ppath}-{$attr[$cid]}";
            $attr['sub'] = $attr['sub'] ?? [];
            $attr['spt'] = substr_count($ppath, '-');
            $attr['spl'] = str_repeat("　├　", $attr['spt']);
            $sub = $attr['sub'];
            unset($attr['sub']);
            $tree[] = $attr;
            if (!empty($sub)) $tree = array_merge($tree, self::arr2table($sub, $cid, $pid, $cpath, $attr[$cpath]));
        }
        return $tree;
    }

    /**
     * 数组转xml
     *
     * @param array   $data 要转换的数据
     * @param boolean $root 是否返回根节点
     * @return string
     */
    public static function arr2xml(array $data, $root = true)
    {
        $str = "";
        if ($root) {
            $str .= "<xml>";
        }
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $child = array_to_xml($val, false);
                $str .= "<$key>$child</$key>";
            } else {
                $str .= "<$key><![CDATA[$val]]></$key>";
            }
        }
        if ($root) {
            $str .= "</xml>";
        }
        return $str;
    }

    /**
     * xml转数组
     *
     * @param string $xml
     * @return array
     */
    public static function xml2arr(string $xml)
    {
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 获取数据树子ID
     * @param array $list 数据列表
     * @param integer $id 起始ID
     * @param string $key ID_KEY
     * @param string $pkey PID_KEY
     * @return array
     */
    public static function getArrSubIds($list, $id = 0, $key = 'id', $pkey = 'pid')
    {
        $ids = [intval($id)];
        foreach ($list as $vo) if (intval($vo[$pkey]) > 0 && intval($vo[$pkey]) === intval($id)) {
            $ids = array_merge($ids, self::getArrSubIds($list, intval($vo[$key]), $key, $pkey));
        }
        return $ids;
    }
}