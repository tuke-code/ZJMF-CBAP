<?php
namespace addon\idcsmart_announcement;

use app\common\lib\Plugin;
use think\facade\Db;

require_once __DIR__ . '/common.php';
/*
 * 公告中心
 * @author theworld
 * @time 2022-06-08
 * @copyright Copyright (c) 2013-2021 https://www.idcsmart.com All rights reserved.
 */
class IdcsmartAnnouncement extends Plugin
{
    # 插件基本信息
    public $info = array(
        'name'        => 'IdcsmartAnnouncement', //插件英文名,作为插件唯一标识,改成你的插件英文就行了
        'title'       => '公告中心',
        'description' => '公告中心',
        'author'      => '智简魔方',  //开发者
        'version'     => '1.0.0',      // 版本号
    );
    # 插件安装
    public function install()
    {
        $sql = [
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_announcement`",
            "CREATE TABLE `idcsmart_addon_idcsmart_announcement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '公告ID',
  `addon_idcsmart_announcement_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '公告分类ID',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '标题',
  `content` longtext NOT NULL COMMENT '内容',
  `keywords` varchar(200) NOT NULL DEFAULT '' COMMENT '关键字',
  `img` text NOT NULL COMMENT '公告缩略图',
  `attachment` text NOT NULL COMMENT '附件',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:显示1:隐藏',
  `read` int(11) NOT NULL DEFAULT '0' COMMENT '阅读量',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '最后操作人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `addon_idcsmart_announcement_type_id` (`addon_idcsmart_announcement_type_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告表'",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_announcement_type`",
            "CREATE TABLE `idcsmart_addon_idcsmart_announcement_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '公告分类ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '最后操作人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告分类表'",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }
        # 安装成功返回true，失败false
        return true;
    }
    # 插件卸载
    public function uninstall()
    {
        $sql = [
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_announcement`",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_announcement_type`",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }
        return true;
    }
}