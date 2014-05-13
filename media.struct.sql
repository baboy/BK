-- MySQL dump 10.13  Distrib 5.6.15, for osx10.7 (x86_64)
--
-- Host: m.tvie.com.cn    Database: mcms
-- ------------------------------------------------------
-- Server version	5.6.11

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wp_media`
--

DROP TABLE IF EXISTS `wp_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_media` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `appid` char(32) NOT NULL,
  `module` varchar(64) NOT NULL,
  `node` char(8) NOT NULL DEFAULT 'CONTENT',
  `uid` int(11) DEFAULT NULL,
  `author` varchar(64) DEFAULT NULL COMMENT '作者',
  `title` varchar(64) NOT NULL,
  `type` int(11) DEFAULT NULL,
  `type_name` varchar(256) DEFAULT NULL,
  `summary` varchar(1024) DEFAULT NULL,
  `tip` varchar(128) DEFAULT NULL,
  `tag` varchar(32) DEFAULT NULL,
  `thumbnail` varchar(512) DEFAULT NULL,
  `pic` varchar(512) DEFAULT NULL,
  `thumbnail_hor` varchar(512) DEFAULT NULL,
  `pic_hor` varchar(512) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `views` int(10) DEFAULT '0',
  `duration` float DEFAULT NULL COMMENT '影片时长',
  `director` varchar(128) DEFAULT NULL COMMENT '导演',
  `actors` varchar(256) DEFAULT NULL COMMENT '主演',
  `area` int(11) DEFAULT NULL COMMENT '地区',
  `publish` int(11) DEFAULT NULL COMMENT '发布平台',
  `status` char(10) NOT NULL DEFAULT 'publish' COMMENT '状态：publish pendding',
  `pubdate` char(32) DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(32) DEFAULT NULL,
  `reference_id` varchar(16) DEFAULT NULL,
  `page_url` varchar(256) DEFAULT NULL,
  `total_count` int(11) NOT NULL DEFAULT '1',
  `update_count` int(11) NOT NULL DEFAULT '1',
  `metadata` varchar(512) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `reference_id` (`reference_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27690 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_media_attr`
--

DROP TABLE IF EXISTS `wp_media_attr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_media_attr` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(10) NOT NULL,
  `key` varchar(32) NOT NULL,
  `index` smallint(6) DEFAULT NULL,
  `value` varchar(4096) DEFAULT NULL,
  `group` char(32) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `attr` (`sid`,`key`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_media_category`
--

DROP TABLE IF EXISTS `wp_media_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_media_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT 'parent id',
  `module` char(16) NOT NULL COMMENT 'module OF application',
  `name` varchar(64) NOT NULL,
  `alias` char(16) NOT NULL DEFAULT '0',
  `description` varchar(128) DEFAULT NULL,
  `icon` varchar(256) DEFAULT NULL,
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metadata` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`module`,`pid`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_media_content`
--

DROP TABLE IF EXISTS `wp_media_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_media_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `content` longtext,
  UNIQUE KEY `id` (`id`),
  KEY `sid` (`sid`),
  CONSTRAINT `wp_media_content_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `wp_media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_media_file`
--

DROP TABLE IF EXISTS `wp_media_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_media_file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `appid` char(32) NOT NULL DEFAULT 'BK',
  `node` char(8) NOT NULL DEFAULT 'file' COMMENT 'dir or file',
  `pid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `ext` char(8) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL,
  `status` char(10) NOT NULL DEFAULT 'publish' COMMENT '状态：publish pendding delete',
  `width` smallint(6) DEFAULT NULL,
  `height` smallint(6) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `path` varchar(256) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `metadata` varchar(512) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_media_list`
--

DROP TABLE IF EXISTS `wp_media_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_media_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gid` char(32) NOT NULL,
  `index` int(11) NOT NULL DEFAULT '0',
  `key` varchar(64) NOT NULL,
  `value` varchar(1024) DEFAULT NULL,
  `desc` varchar(4096) DEFAULT NULL,
  `metadata` varchar(512) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `key` (`gid`,`key`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_media_serial_video`
--

DROP TABLE IF EXISTS `wp_media_serial_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_media_serial_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `index` int(11) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `gid` (`gid`,`sid`,`index`)
) ENGINE=InnoDB AUTO_INCREMENT=23785 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-03-10 12:16:13
