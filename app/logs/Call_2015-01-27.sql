# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 106.2.200.204 (MySQL 5.5.8)
# Database: Call
# Generation Time: 2015-01-27 08:38:53 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table Config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Config`;

CREATE TABLE `Config` (
  `Key` varchar(25) NOT NULL DEFAULT '',
  `Value` text,
  `Desc` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Config` WRITE;
/*!40000 ALTER TABLE `Config` DISABLE KEYS */;

INSERT INTO `Config` (`Key`, `Value`, `Desc`)
VALUES
	('request','3',NULL),
	('response','5',NULL);

/*!40000 ALTER TABLE `Config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Device
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Device`;

CREATE TABLE `Device` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Number` varchar(10) NOT NULL,
  `Type` char(1) DEFAULT NULL COMMENT '1 前端；2 后端',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Number` (`Number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Device` WRITE;
/*!40000 ALTER TABLE `Device` DISABLE KEYS */;

INSERT INTO `Device` (`ID`, `Number`, `Type`)
VALUES
	(1,'101','1'),
	(2,'201','2'),
	(3,'202','2'),
	(4,'203','2');

/*!40000 ALTER TABLE `Device` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table DeviceMenu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `DeviceMenu`;

CREATE TABLE `DeviceMenu` (
  `DeviceID` int(10) NOT NULL,
  `MenuID` varchar(255) NOT NULL,
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `DeviceMenu` WRITE;
/*!40000 ALTER TABLE `DeviceMenu` DISABLE KEYS */;

INSERT INTO `DeviceMenu` (`DeviceID`, `MenuID`, `ID`)
VALUES
	(3,'1',41),
	(3,'2',45),
	(2,'1',40),
	(4,'18',38),
	(3,'18',37),
	(2,'18',36),
	(1,'18',35),
	(3,'3',30),
	(1,'3',29),
	(1,'1',39),
	(2,'2',44),
	(1,'2',43),
	(4,'3',31),
	(4,'1',42),
	(4,'2',46);

/*!40000 ALTER TABLE `DeviceMenu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Item`;

CREATE TABLE `Item` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) NOT NULL DEFAULT '',
  `Pass` varchar(50) NOT NULL DEFAULT '',
  `Role` int(11) NOT NULL DEFAULT '1',
  `RoleName` varchar(25) DEFAULT NULL,
  `CreateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `CreateUser` int(11) DEFAULT NULL,
  `TeamID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Item` WRITE;
/*!40000 ALTER TABLE `Item` DISABLE KEYS */;

INSERT INTO `Item` (`ID`, `Name`, `Pass`, `Role`, `RoleName`, `CreateTime`, `CreateUser`, `TeamID`)
VALUES
	(130,'咨询服务1','',1,NULL,'2014-12-19 12:08:02',NULL,57),
	(131,'咨询服务2','',1,NULL,'2014-12-19 12:08:08',NULL,57);

/*!40000 ALTER TABLE `Item` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Log`;

CREATE TABLE `Log` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceID` int(11) DEFAULT NULL,
  `ItemID` int(11) DEFAULT NULL,
  `ItemName` varchar(20) DEFAULT NULL,
  `Action` varchar(45) DEFAULT NULL,
  `ActionTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Log` WRITE;
/*!40000 ALTER TABLE `Log` DISABLE KEYS */;

INSERT INTO `Log` (`ID`, `DeviceID`, `ItemID`, `ItemName`, `Action`, `ActionTime`)
VALUES
	(7,1,4,'咨询1','呼叫','2014-12-31 13:31:11');

/*!40000 ALTER TABLE `Log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Menu`;

CREATE TABLE `Menu` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) DEFAULT NULL,
  `Type` int(11) DEFAULT NULL,
  `TypeName` varchar(25) DEFAULT NULL,
  `LineNumber` int(11) DEFAULT NULL COMMENT '指标线',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Menu` WRITE;
/*!40000 ALTER TABLE `Menu` DISABLE KEYS */;

INSERT INTO `Menu` (`ID`, `Name`, `Type`, `TypeName`, `LineNumber`)
VALUES
	(1,'历下',0,NULL,NULL),
	(2,'市中',0,NULL,NULL),
	(3,'户表工程',0,NULL,NULL),
	(4,'咨询1',1,NULL,NULL),
	(5,'咨询2',1,NULL,NULL),
	(6,'咨询3',1,NULL,NULL),
	(7,'咨询1',2,NULL,NULL),
	(8,'咨询2',2,NULL,NULL),
	(9,'投诉',3,NULL,NULL),
	(18,'真的',0,NULL,NULL);

/*!40000 ALTER TABLE `Menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Module`;

CREATE TABLE `Module` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '操作模块表',
  `Name` varchar(25) DEFAULT NULL,
  `Url` varchar(200) DEFAULT NULL,
  `Icon` varchar(200) DEFAULT NULL,
  `ParentID` int(11) NOT NULL,
  `Sort` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Module` WRITE;
/*!40000 ALTER TABLE `Module` DISABLE KEYS */;

INSERT INTO `Module` (`ID`, `Name`, `Url`, `Icon`, `ParentID`, `Sort`)
VALUES
	(3,'统计查询','count',NULL,-1,0),
	(12,'数据汇总','count/singleinquiries',NULL,3,0),
	(13,'超时接待','count/reconciliationinquiry',NULL,3,0),
	(25,'管理','manager',NULL,-1,0),
	(26,'一级菜单管理','manager/group',NULL,25,0),
	(29,'二级菜单管理','manager/user',NULL,25,0),
	(30,'设置','import/arrears',NULL,25,0);

/*!40000 ALTER TABLE `Module` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Order`;

CREATE TABLE `Order` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `MenuID` int(11) DEFAULT NULL COMMENT '一级菜单',
  `MenuName` varchar(20) DEFAULT NULL COMMENT '一级菜单名称',
  `ItemName` varchar(20) DEFAULT NULL COMMENT '二级菜单名称',
  `RequestTime` datetime DEFAULT NULL COMMENT '请求时间',
  `ResponseTime` datetime DEFAULT NULL COMMENT '响应时间',
  `ReceiveTime` datetime DEFAULT NULL COMMENT '接待时间',
  `Yearly` int(4) DEFAULT NULL COMMENT '年度',
  `Monthly` varchar(10) DEFAULT NULL COMMENT '月度',
  `Quarterly` varchar(11) DEFAULT NULL COMMENT '季度',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Order` WRITE;
/*!40000 ALTER TABLE `Order` DISABLE KEYS */;

INSERT INTO `Order` (`ID`, `MenuID`, `MenuName`, `ItemName`, `RequestTime`, `ResponseTime`, `ReceiveTime`, `Yearly`, `Monthly`, `Quarterly`)
VALUES
	(1,1,'咨询2','咨询2','2014-12-30 17:51:29','2014-12-30 17:51:35','2014-12-30 17:51:37',2014,'2014-12','第四季度'),
	(2,3,'投诉','投诉','2014-12-30 17:51:14',NULL,NULL,2014,'2014-12','第四季度'),
	(3,1,'咨询1','咨询1','2014-12-30 17:51:24',NULL,NULL,2014,'2014-12','第四季度'),
	(4,1,'咨询2','咨询2','2014-12-30 17:51:29',NULL,NULL,2014,'2014-12','第四季度'),
	(5,1,'咨询3','咨询3','2014-12-30 17:51:29',NULL,NULL,2014,'2014-12','第四季度');

/*!40000 ALTER TABLE `Order` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table Role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Role`;

CREATE TABLE `Role` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色表',
  `Name` varchar(25) DEFAULT NULL,
  `Modules` varchar(255) DEFAULT NULL,
  `IndexPage` varchar(100) DEFAULT NULL COMMENT '角色登录的首页面',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `Role` WRITE;
/*!40000 ALTER TABLE `Role` DISABLE KEYS */;

INSERT INTO `Role` (`ID`, `Name`, `Modules`, `IndexPage`)
VALUES
	(1,'抄表员','33,8,11,12,9,10,15,16,17,18,19,20,21,22,32','/site/index'),
	(2,'抄表员班长','33,8,11,9,10,12,15,16,17,18,19,20,21,22,24,32','/site/index'),
	(3,'收费员','6,7,12,13','/charges/charges'),
	(4,'收费员班长','6,7,12,13,19','/count/reconciliationinquiry'),
	(5,'对账员','12,14','/count/Reconciliation'),
	(6,'管理人员','12,13,14,15,16,17,18,19,20,21,22,35','/manager/systemlog'),
	(7,'Admin','33,6,7,8,11,9,10,30,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,29,31,32,34,35','/count/singleinquiries');

/*!40000 ALTER TABLE `Role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table SegmentLog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `SegmentLog`;

CREATE TABLE `SegmentLog` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Number` varchar(25) NOT NULL DEFAULT '' COMMENT '编号',
  `Name` varchar(25) DEFAULT NULL COMMENT '抄表段名称',
  `UserID` int(11) DEFAULT NULL COMMENT '抄表员ID',
  `UserName` varchar(25) DEFAULT NULL COMMENT '抄表员名称',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table User
# ------------------------------------------------------------

DROP TABLE IF EXISTS `User`;

CREATE TABLE `User` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) NOT NULL DEFAULT '',
  `Pass` varchar(50) NOT NULL DEFAULT '',
  `Role` int(11) NOT NULL DEFAULT '1',
  `RoleName` varchar(25) DEFAULT NULL,
  `CreateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `CreateUser` int(11) DEFAULT NULL,
  `TeamID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;

INSERT INTO `User` (`ID`, `Name`, `Pass`, `Role`, `RoleName`, `CreateTime`, `CreateUser`, `TeamID`)
VALUES
	(10,'admin','40bd001563085fc35165329ea1ff5c5ecbdbbeef',7,NULL,'2014-06-07 01:05:29',NULL,-1);

/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
