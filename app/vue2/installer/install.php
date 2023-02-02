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
namespace app\applet\installer;
use think\facade\Db;
//======这里只需处理应用内数据表,在设计应用数据库时建议统一设计规范=========//
/**
 * 应用相关数据表统一以应用标识开头(如storage应用storage_file,storage_folder)
 * 数据表和字段统一采用小写加下划线方式命名，并注意字段名不要以下划线开头
 * 建议存储引擎统一使用innodb引擎
 * 建议字符集统一使用utf8mb4_general_ci
 * 建议表名和字段名统一使用英文名称,不要使用驼峰和中文或拼音作为数据表及字段命名。
 * 建议主键统一使用id命名，外键统一使用数据表名+_id，例如user表主键为id,在其他表中为user_id
 * 建议英文名称统一使用name,中文名称统一使用title(人名除外)
 * 建议表名和字段名统一使用名称单数，不管是不是表示复数（如photos,comments等统一使用photo，comment就好）
 * 建议时间字段统一使用Unix时间戳，如create_time, update_time, delete_time
 * 建议需要添加的索引的字段长度不超过100
 * 建议创建数据表时添加 IF NOT EXISTS 条件
 * 请勿在应用内更新内核数据表以及另一个应用的数据表
 * 请勿在应用安装时添加文件到核心目录
 * 请勿更改核心目录代码
 */
//========================================================================//
$sql = <<<EOF
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `vue2_page1`;
CREATE TABLE `vue2_page1`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `remark` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `vue2_page2`;
CREATE TABLE `vue2_page2`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `remark` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
EOF;
// 安装数据表
Db::getPdo()->exec($sql);
// 导入仪表盘
Db::getPdo()->exec("INSERT INTO `core_dashboard`(`title`, `app`, `name`, `route`, `col`, `sort`, `auth`, `status`, `create_time`, `update_time`) VALUES ('Vu2仪表盘', 'vue2', 'vue2_dashboard', '/vue2/dashboard', 6, 100, 'vue2_dashboard', 1, 1666967048, 1666968047);");

