-- MariaDB dump 10.19  Distrib 10.11.5-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: asterisk
-- ------------------------------------------------------
-- Server version	10.11.5-MariaDB-1:10.11.5+maria~ubu1804

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `variable` varchar(20) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`variable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ampusers`
--

DROP TABLE IF EXISTS `ampusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ampusers` (
  `username` varchar(190) NOT NULL,
  `password_sha1` varchar(40) NOT NULL,
  `extension_low` varchar(20) NOT NULL DEFAULT '',
  `extension_high` varchar(20) NOT NULL DEFAULT '',
  `deptname` varchar(20) NOT NULL DEFAULT '',
  `sections` longblob NOT NULL,
  `email` varchar(40) DEFAULT '',
  `extension` varchar(40) DEFAULT '',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `announcement`
--

DROP TABLE IF EXISTS `announcement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcement` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `recording_id` int(11) DEFAULT NULL,
  `allow_skip` int(11) DEFAULT NULL,
  `post_dest` varchar(255) DEFAULT NULL,
  `return_ivr` tinyint(1) NOT NULL DEFAULT 0,
  `noanswer` tinyint(1) NOT NULL DEFAULT 0,
  `repeat_msg` varchar(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_access_tokens`
--

DROP TABLE IF EXISTS `api_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_access_tokens` (
  `token` varchar(80) NOT NULL DEFAULT '',
  `aid` int(10) unsigned NOT NULL,
  `expiry` int(11) NOT NULL,
  `scopes` longblob NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `ip_address` varchar(80) NOT NULL DEFAULT '',
  `last_accessed` int(11) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_applications`
--

DROP TABLE IF EXISTS `api_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL,
  `grant_type` varchar(20) NOT NULL DEFAULT '',
  `client_id` varchar(128) NOT NULL DEFAULT '',
  `client_secret` varchar(64) DEFAULT NULL,
  `redirect_uri` varchar(150) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `algo` varchar(10) DEFAULT NULL,
  `allowed_scopes` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_auth_codes`
--

DROP TABLE IF EXISTS `api_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_auth_codes` (
  `code` varchar(80) NOT NULL DEFAULT '',
  `aid` int(10) unsigned NOT NULL,
  `expiry` int(11) NOT NULL,
  `scopes` longblob NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `ip_address` varchar(80) NOT NULL DEFAULT '',
  `last_accessed` int(11) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_refresh_tokens`
--

DROP TABLE IF EXISTS `api_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_refresh_tokens` (
  `token` varchar(80) NOT NULL DEFAULT '',
  `access_token` varchar(80) NOT NULL DEFAULT '',
  `expiry` int(11) NOT NULL,
  `ip_address` varchar(80) NOT NULL DEFAULT '',
  `last_accessed` int(11) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `arimanager`
--

DROP TABLE IF EXISTS `arimanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arimanager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(190) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `password_format` varchar(255) DEFAULT NULL,
  `read_only` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `callrecording`
--

DROP TABLE IF EXISTS `callrecording`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callrecording` (
  `callrecording_id` int(11) NOT NULL AUTO_INCREMENT,
  `callrecording_mode` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `dest` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`callrecording_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `callrecording_module`
--

DROP TABLE IF EXISTS `callrecording_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callrecording_module` (
  `extension` varchar(50) DEFAULT NULL,
  `cidnum` varchar(50) DEFAULT '',
  `callrecording` varchar(10) DEFAULT NULL,
  `display` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `certman_cas`
--

DROP TABLE IF EXISTS `certman_cas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certman_cas` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `basename` varchar(190) NOT NULL,
  `cn` varchar(255) NOT NULL,
  `on` varchar(255) NOT NULL,
  `passphrase` varchar(255) DEFAULT NULL,
  `salt` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `basename` (`basename`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `certman_certs`
--

DROP TABLE IF EXISTS `certman_certs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certman_certs` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `caid` int(11) DEFAULT NULL,
  `basename` varchar(190) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` varchar(2) NOT NULL DEFAULT 'ss',
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `additional` longblob DEFAULT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `basename_UNIQUE` (`basename`),
  UNIQUE KEY `basename` (`basename`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `certman_csrs`
--

DROP TABLE IF EXISTS `certman_csrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certman_csrs` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `basename` varchar(190) NOT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `basename` (`basename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `certman_mapping`
--

DROP TABLE IF EXISTS `certman_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certman_mapping` (
  `id` varchar(20) NOT NULL,
  `cid` int(11) DEFAULT NULL,
  `verify` varchar(255) DEFAULT NULL,
  `setup` varchar(45) DEFAULT NULL,
  `rekey` int(11) DEFAULT NULL,
  `auto_generate_cert` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cidlookup`
--

DROP TABLE IF EXISTS `cidlookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cidlookup` (
  `cidlookup_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `sourcetype` varchar(100) NOT NULL,
  `cache` smallint(6) NOT NULL DEFAULT 0,
  `deptname` varchar(30) DEFAULT NULL,
  `http_host` varchar(100) DEFAULT NULL,
  `http_port` varchar(30) DEFAULT NULL,
  `http_username` varchar(50) DEFAULT NULL,
  `http_password` varchar(50) DEFAULT NULL,
  `http_path` varchar(100) DEFAULT NULL,
  `http_query` varchar(100) DEFAULT NULL,
  `mysql_host` varchar(60) DEFAULT NULL,
  `mysql_dbname` varchar(60) DEFAULT NULL,
  `mysql_query` varchar(255) DEFAULT NULL,
  `mysql_username` varchar(30) DEFAULT NULL,
  `mysql_password` varchar(30) DEFAULT NULL,
  `mysql_charset` varchar(60) DEFAULT NULL,
  `opencnam_account_sid` varchar(34) DEFAULT NULL,
  `opencnam_auth_token` varchar(34) DEFAULT NULL,
  `cm_group` varchar(60) DEFAULT '',
  `cm_format` varchar(5) DEFAULT '',
  PRIMARY KEY (`cidlookup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cidlookup_incoming`
--

DROP TABLE IF EXISTS `cidlookup_incoming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cidlookup_incoming` (
  `cidlookup_id` int(11) NOT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `cidnum` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_entry_emails`
--

DROP TABLE IF EXISTS `contactmanager_entry_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_entry_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryid` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=417 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_entry_images`
--

DROP TABLE IF EXISTS `contactmanager_entry_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_entry_images` (
  `entryid` int(11) NOT NULL,
  `image` longblob DEFAULT NULL,
  `format` varchar(45) NOT NULL,
  `gravatar` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`entryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_entry_numbers`
--

DROP TABLE IF EXISTS `contactmanager_entry_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_entry_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryid` int(11) NOT NULL,
  `number` varchar(100) DEFAULT NULL,
  `extension` varchar(100) DEFAULT NULL,
  `countrycode` varchar(4) DEFAULT NULL,
  `nationalnumber` varchar(100) DEFAULT NULL,
  `regioncode` varchar(2) DEFAULT NULL,
  `locale` varchar(2) DEFAULT NULL,
  `stripped` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `flags` varchar(100) DEFAULT NULL,
  `E164` varchar(100) DEFAULT NULL,
  `possibleshort` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17097 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_entry_speeddials`
--

DROP TABLE IF EXISTS `contactmanager_entry_speeddials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_entry_speeddials` (
  `id` int(11) NOT NULL,
  `entryid` int(11) NOT NULL,
  `numberid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_entry_userman_images`
--

DROP TABLE IF EXISTS `contactmanager_entry_userman_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_entry_userman_images` (
  `uid` int(11) NOT NULL,
  `image` longblob DEFAULT NULL,
  `format` varchar(45) NOT NULL,
  `gravatar` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_entry_websites`
--

DROP TABLE IF EXISTS `contactmanager_entry_websites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_entry_websites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryid` int(11) NOT NULL,
  `website` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_entry_xmpps`
--

DROP TABLE IF EXISTS `contactmanager_entry_xmpps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_entry_xmpps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryid` int(11) NOT NULL,
  `xmpp` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_group_entries`
--

DROP TABLE IF EXISTS `contactmanager_group_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_group_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `displayname` varchar(100) NOT NULL DEFAULT '',
  `fname` varchar(100) NOT NULL DEFAULT '',
  `lname` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `company` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(200) NOT NULL DEFAULT '',
  `uuid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid_index` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contactmanager_groups`
--

DROP TABLE IF EXISTS `contactmanager_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contactmanager_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `type` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_jobs`
--

DROP TABLE IF EXISTS `cron_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cron_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modulename` varchar(170) NOT NULL DEFAULT '',
  `jobname` varchar(170) NOT NULL DEFAULT '',
  `command` longtext DEFAULT NULL,
  `class` varchar(255) DEFAULT '',
  `schedule` varchar(255) NOT NULL DEFAULT '',
  `max_runtime` int(11) NOT NULL DEFAULT 30,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `execution_order` int(11) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modulename` (`modulename`,`jobname`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cronmanager`
--

DROP TABLE IF EXISTS `cronmanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cronmanager` (
  `module` varchar(50) NOT NULL DEFAULT '',
  `id` varchar(24) NOT NULL DEFAULT '',
  `time` varchar(5) DEFAULT NULL,
  `freq` int(11) NOT NULL DEFAULT 0,
  `lasttime` int(11) NOT NULL DEFAULT 0,
  `command` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`module`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_extensions`
--

DROP TABLE IF EXISTS `custom_extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_extensions` (
  `custom_exten` varchar(80) NOT NULL DEFAULT '',
  `description` varchar(40) NOT NULL DEFAULT '',
  `notes` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`custom_exten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dahdi`
--

DROP TABLE IF EXISTS `dahdi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dahdi` (
  `id` varchar(20) NOT NULL DEFAULT '-1',
  `keyword` varchar(30) NOT NULL DEFAULT '',
  `data` varchar(255) NOT NULL DEFAULT '',
  `flags` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dahdichandids`
--

DROP TABLE IF EXISTS `dahdichandids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dahdichandids` (
  `channel` int(11) NOT NULL DEFAULT 0,
  `description` varchar(40) NOT NULL DEFAULT '',
  `did` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daynight`
--

DROP TABLE IF EXISTS `daynight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daynight` (
  `ext` varchar(10) NOT NULL DEFAULT '',
  `dmode` varchar(40) NOT NULL DEFAULT '',
  `dest` varchar(190) NOT NULL DEFAULT '',
  PRIMARY KEY (`ext`,`dmode`,`dest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `tech` varchar(10) NOT NULL DEFAULT '',
  `dial` varchar(255) NOT NULL DEFAULT '',
  `devicetype` varchar(5) NOT NULL DEFAULT '',
  `user` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `emergency_cid` varchar(100) DEFAULT NULL,
  `hint_override` varchar(100) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `tech` (`tech`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `directory_details`
--

DROP TABLE IF EXISTS `directory_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `directory_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dirname` varchar(50) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `announcement` int(11) DEFAULT NULL,
  `callid_prefix` varchar(10) DEFAULT NULL,
  `alert_info` varchar(50) DEFAULT NULL,
  `rvolume` varchar(2) NOT NULL DEFAULT '',
  `repeat_loops` varchar(3) DEFAULT NULL,
  `repeat_recording` int(11) DEFAULT NULL,
  `invalid_recording` int(11) DEFAULT NULL,
  `invalid_destination` varchar(50) DEFAULT NULL,
  `retivr` varchar(5) DEFAULT NULL,
  `say_extension` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `directory_entries`
--

DROP TABLE IF EXISTS `directory_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `directory_entries` (
  `id` int(11) NOT NULL,
  `e_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(25) DEFAULT NULL,
  `foreign_id` varchar(25) DEFAULT NULL,
  `audio` varchar(50) DEFAULT NULL,
  `dial` varchar(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emergencydevices`
--

DROP TABLE IF EXISTS `emergencydevices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emergencydevices` (
  `id` varchar(20) NOT NULL,
  `tech` varchar(10) NOT NULL,
  `dial` varchar(255) NOT NULL,
  `devicetype` varchar(10) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `emergency_cid` varchar(100) DEFAULT NULL,
  `hint_override` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fax_details`
--

DROP TABLE IF EXISTS `fax_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fax_details` (
  `key` varchar(50) DEFAULT NULL,
  `value` varchar(710) DEFAULT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fax_incoming`
--

DROP TABLE IF EXISTS `fax_incoming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fax_incoming` (
  `cidnum` varchar(20) DEFAULT NULL,
  `extension` varchar(50) DEFAULT NULL,
  `detection` varchar(20) DEFAULT NULL,
  `detectionwait` varchar(5) DEFAULT NULL,
  `destination` varchar(50) DEFAULT NULL,
  `legacy_email` varchar(50) DEFAULT NULL,
  `ring` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fax_users`
--

DROP TABLE IF EXISTS `fax_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fax_users` (
  `user` varchar(15) DEFAULT NULL,
  `faxenabled` varchar(10) DEFAULT NULL,
  `faxemail` longtext DEFAULT NULL,
  `faxattachformat` varchar(10) DEFAULT NULL,
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `featurecodes`
--

DROP TABLE IF EXISTS `featurecodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `featurecodes` (
  `modulename` varchar(50) NOT NULL DEFAULT '',
  `featurename` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `helptext` varchar(500) NOT NULL DEFAULT '',
  `defaultcode` varchar(20) DEFAULT NULL,
  `customcode` varchar(20) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `providedest` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`modulename`,`featurename`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `findmefollow`
--

DROP TABLE IF EXISTS `findmefollow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `findmefollow` (
  `grpnum` varchar(20) NOT NULL,
  `strategy` varchar(50) NOT NULL,
  `grptime` smallint(6) NOT NULL,
  `grppre` varchar(100) DEFAULT NULL,
  `grplist` varchar(255) NOT NULL,
  `annmsg_id` int(11) DEFAULT NULL,
  `postdest` varchar(255) DEFAULT NULL,
  `dring` varchar(255) DEFAULT NULL,
  `rvolume` varchar(2) DEFAULT NULL,
  `remotealert_id` int(11) DEFAULT NULL,
  `needsconf` varchar(10) DEFAULT NULL,
  `toolate_id` int(11) DEFAULT NULL,
  `pre_ring` smallint(6) NOT NULL DEFAULT 0,
  `ringing` varchar(80) DEFAULT NULL,
  `calendar_enable` tinyint(1) DEFAULT 0,
  `calendar_id` varchar(80) DEFAULT '',
  `calendar_group_id` varchar(80) DEFAULT '',
  `calendar_match` varchar(4) DEFAULT 'yes',
  PRIMARY KEY (`grpnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `freepbx_log`
--

DROP TABLE IF EXISTS `freepbx_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `freepbx_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `level` varchar(150) NOT NULL DEFAULT 'error',
  `status` int(11) NOT NULL DEFAULT 0,
  `message` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`,`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `freepbx_settings`
--

DROP TABLE IF EXISTS `freepbx_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `freepbx_settings` (
  `keyword` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `name` varchar(80) DEFAULT NULL,
  `level` tinyint(1) DEFAULT 0,
  `description` longtext DEFAULT NULL,
  `type` varchar(25) DEFAULT NULL,
  `options` longtext DEFAULT NULL,
  `defaultval` varchar(255) DEFAULT NULL,
  `readonly` tinyint(1) DEFAULT 0,
  `hidden` tinyint(1) DEFAULT 0,
  `category` varchar(50) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `emptyok` tinyint(1) DEFAULT 1,
  `sortorder` int(11) DEFAULT 0,
  PRIMARY KEY (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `globals`
--

DROP TABLE IF EXISTS `globals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `globals` (
  `variable` varchar(190) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`variable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotelwakeup`
--

DROP TABLE IF EXISTS `hotelwakeup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotelwakeup` (
  `maxretries` int(11) NOT NULL,
  `waittime` int(11) NOT NULL,
  `retrytime` int(11) NOT NULL,
  `extensionlength` int(11) NOT NULL,
  `cid` varchar(30) DEFAULT NULL,
  `cnam` varchar(30) DEFAULT NULL,
  `operator_mode` int(11) NOT NULL,
  `operator_extensions` varchar(30) DEFAULT NULL,
  `application` varchar(30) DEFAULT NULL,
  `data` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`maxretries`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotelwakeup_calls`
--

DROP TABLE IF EXISTS `hotelwakeup_calls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hotelwakeup_calls` (
  `time` int(11) NOT NULL,
  `ext` int(11) NOT NULL,
  `maxretries` int(11) NOT NULL,
  `retrytime` int(11) NOT NULL,
  `waittime` int(11) NOT NULL,
  `cid` varchar(30) DEFAULT NULL,
  `cnam` varchar(30) DEFAULT NULL,
  `application` varchar(30) DEFAULT NULL,
  `data` varchar(30) DEFAULT NULL,
  `tempdir` varchar(100) DEFAULT NULL,
  `outdir` varchar(100) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `frequency` int(11) NOT NULL,
  PRIMARY KEY (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iax`
--

DROP TABLE IF EXISTS `iax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iax` (
  `id` varchar(20) NOT NULL DEFAULT '-1',
  `keyword` varchar(30) NOT NULL DEFAULT '',
  `data` varchar(255) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iaxsettings`
--

DROP TABLE IF EXISTS `iaxsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iaxsettings` (
  `keyword` varchar(50) NOT NULL DEFAULT '',
  `seq` tinyint(1) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `data` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`keyword`,`seq`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `incoming`
--

DROP TABLE IF EXISTS `incoming`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoming` (
  `cidnum` varchar(50) DEFAULT NULL,
  `extension` varchar(50) NOT NULL,
  `destination` varchar(50) DEFAULT NULL,
  `privacyman` tinyint(1) DEFAULT NULL,
  `alertinfo` varchar(255) DEFAULT NULL,
  `ringing` varchar(20) DEFAULT NULL,
  `fanswer` varchar(20) DEFAULT NULL,
  `mohclass` varchar(80) NOT NULL DEFAULT 'default',
  `description` varchar(80) DEFAULT NULL,
  `grppre` varchar(80) DEFAULT NULL,
  `delay_answer` int(11) DEFAULT NULL,
  `pricid` varchar(20) DEFAULT NULL,
  `pmmaxretries` varchar(2) DEFAULT NULL,
  `pmminlength` varchar(2) DEFAULT NULL,
  `reversal` varchar(10) DEFAULT NULL,
  `rvolume` varchar(2) DEFAULT '',
  `indication_zone` varchar(20) DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `indications_zonelist`
--

DROP TABLE IF EXISTS `indications_zonelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `indications_zonelist` (
  `iso` varchar(20) NOT NULL,
  `name` varchar(80) NOT NULL,
  `conf` longblob DEFAULT NULL,
  PRIMARY KEY (`iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ivr_details`
--

DROP TABLE IF EXISTS `ivr_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ivr_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `announcement` int(11) DEFAULT NULL,
  `directdial` varchar(50) DEFAULT NULL,
  `invalid_loops` varchar(10) DEFAULT NULL,
  `invalid_retry_recording` varchar(25) DEFAULT NULL,
  `invalid_destination` varchar(50) DEFAULT NULL,
  `timeout_enabled` varchar(50) DEFAULT NULL,
  `invalid_recording` varchar(25) DEFAULT NULL,
  `retvm` varchar(8) DEFAULT NULL,
  `timeout_time` int(11) DEFAULT NULL,
  `timeout_recording` varchar(25) DEFAULT NULL,
  `timeout_retry_recording` varchar(25) DEFAULT NULL,
  `timeout_destination` varchar(50) DEFAULT NULL,
  `timeout_loops` varchar(10) DEFAULT NULL,
  `timeout_append_announce` tinyint(1) NOT NULL DEFAULT 1,
  `invalid_append_announce` tinyint(1) NOT NULL DEFAULT 1,
  `timeout_ivr_ret` tinyint(1) NOT NULL DEFAULT 0,
  `invalid_ivr_ret` tinyint(1) NOT NULL DEFAULT 0,
  `alertinfo` varchar(150) DEFAULT NULL,
  `rvolume` varchar(2) NOT NULL DEFAULT '',
  `strict_dial_timeout` tinyint(1) NOT NULL DEFAULT 2,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ivr_entries`
--

DROP TABLE IF EXISTS `ivr_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ivr_entries` (
  `ivr_id` int(11) NOT NULL,
  `selection` varchar(30) DEFAULT NULL,
  `dest` varchar(200) DEFAULT NULL,
  `ivr_ret` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvblobstore`
--

DROP TABLE IF EXISTS `kvblobstore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvblobstore` (
  `uuid` char(36) NOT NULL,
  `type` char(32) DEFAULT NULL,
  `content` longblob DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_Dashboard`
--

DROP TABLE IF EXISTS `kvstore_Dashboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_Dashboard` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_Fax`
--

DROP TABLE IF EXISTS `kvstore_Fax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_Fax` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX`
--

DROP TABLE IF EXISTS `kvstore_FreePBX`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_Framework`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_Framework`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_Framework` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_Hooks`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_Hooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_Hooks` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_Media`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_Media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_Media` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Amd`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Amd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Amd` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Calendar`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Calendar` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Conferences`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Conferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Conferences` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Contactmanager`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Contactmanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Contactmanager` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Core`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Core`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Core` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Customappsreg`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Customappsreg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Customappsreg` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Filestore`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Filestore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Filestore` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Paging`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Paging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Paging` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Sipstation`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Sipstation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Sipstation` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Userman`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Userman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Userman` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Voicemail`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Voicemail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Voicemail` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_FreePBX_modules_Webrtc`
--

DROP TABLE IF EXISTS `kvstore_FreePBX_modules_Webrtc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_FreePBX_modules_Webrtc` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_OOBE`
--

DROP TABLE IF EXISTS `kvstore_OOBE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_OOBE` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kvstore_Sipsettings`
--

DROP TABLE IF EXISTS `kvstore_Sipsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kvstore_Sipsettings` (
  `key` char(255) NOT NULL,
  `val` varchar(4096) DEFAULT NULL,
  `type` char(16) DEFAULT NULL,
  `id` char(255) DEFAULT NULL,
  UNIQUE KEY `uniqueindex` (`key`(190),`id`(190)),
  KEY `keyindex` (`key`(190)),
  KEY `idindex` (`id`(190))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logfile_logfiles`
--

DROP TABLE IF EXISTS `logfile_logfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logfile_logfiles` (
  `name` varchar(25) NOT NULL DEFAULT '',
  `debug` varchar(25) DEFAULT NULL,
  `dtmf` varchar(25) DEFAULT NULL,
  `error` varchar(25) DEFAULT NULL,
  `fax` varchar(25) DEFAULT NULL,
  `notice` varchar(25) DEFAULT NULL,
  `verbose` varchar(25) DEFAULT NULL,
  `warning` varchar(25) DEFAULT NULL,
  `security` varchar(25) DEFAULT NULL,
  `permanent` tinyint(1) NOT NULL DEFAULT 0,
  `readonly` tinyint(1) NOT NULL DEFAULT 0,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logfile_settings`
--

DROP TABLE IF EXISTS `logfile_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logfile_settings` (
  `key` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manager`
--

DROP TABLE IF EXISTS `manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager` (
  `manager_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `secret` varchar(50) DEFAULT NULL,
  `deny` varchar(255) DEFAULT NULL,
  `permit` varchar(255) DEFAULT NULL,
  `read` varchar(255) DEFAULT NULL,
  `write` varchar(255) DEFAULT NULL,
  `writetimeout` int(11) DEFAULT NULL,
  PRIMARY KEY (`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meetme`
--

DROP TABLE IF EXISTS `meetme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetme` (
  `exten` varchar(50) NOT NULL,
  `options` varchar(15) DEFAULT NULL,
  `userpin` varchar(50) DEFAULT NULL,
  `adminpin` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `joinmsg_id` int(11) DEFAULT NULL,
  `music` varchar(80) DEFAULT NULL,
  `users` smallint(5) unsigned DEFAULT 0,
  `language` varchar(10) NOT NULL DEFAULT '',
  `timeout` int(10) unsigned DEFAULT 21600,
  PRIMARY KEY (`exten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module_xml`
--

DROP TABLE IF EXISTS `module_xml`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_xml` (
  `id` varchar(20) NOT NULL DEFAULT 'xml',
  `time` int(11) NOT NULL DEFAULT 0,
  `data` longblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulename` varchar(50) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `signature` longblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `music`
--

DROP TABLE IF EXISTS `music`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(190) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `random` tinyint(1) DEFAULT 0,
  `application` varchar(255) DEFAULT NULL,
  `format` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_UNIQUE` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `module` varchar(50) NOT NULL DEFAULT '',
  `id` varchar(24) NOT NULL DEFAULT '',
  `level` int(11) NOT NULL DEFAULT 0,
  `display_text` varchar(255) NOT NULL DEFAULT '',
  `extended_text` longblob NOT NULL,
  `link` varchar(255) NOT NULL DEFAULT '',
  `reset` tinyint(1) NOT NULL DEFAULT 0,
  `candelete` tinyint(1) NOT NULL DEFAULT 0,
  `timestamp` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`module`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `outbound_route_email`
--

DROP TABLE IF EXISTS `outbound_route_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outbound_route_email` (
  `route_id` int(11) NOT NULL,
  `emailfrom` varchar(320) DEFAULT '',
  `emailto` varchar(320) DEFAULT '',
  `emailsubject` longtext DEFAULT NULL,
  `emailbody` longtext DEFAULT NULL,
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `outbound_route_patterns`
--

DROP TABLE IF EXISTS `outbound_route_patterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outbound_route_patterns` (
  `route_id` int(11) NOT NULL,
  `match_pattern_prefix` varchar(60) NOT NULL DEFAULT '',
  `match_pattern_pass` varchar(60) NOT NULL DEFAULT '',
  `match_cid` varchar(60) NOT NULL DEFAULT '',
  `prepend_digits` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`route_id`,`match_pattern_prefix`,`match_pattern_pass`,`match_cid`,`prepend_digits`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `outbound_route_sequence`
--

DROP TABLE IF EXISTS `outbound_route_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outbound_route_sequence` (
  `route_id` int(11) NOT NULL,
  `seq` int(11) NOT NULL,
  PRIMARY KEY (`route_id`,`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `outbound_route_trunks`
--

DROP TABLE IF EXISTS `outbound_route_trunks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outbound_route_trunks` (
  `route_id` int(11) NOT NULL,
  `trunk_id` int(11) NOT NULL,
  `seq` int(11) NOT NULL,
  PRIMARY KEY (`route_id`,`trunk_id`,`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `outbound_routes`
--

DROP TABLE IF EXISTS `outbound_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outbound_routes` (
  `route_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `outcid` varchar(255) DEFAULT NULL,
  `outcid_mode` varchar(20) DEFAULT NULL,
  `password` varchar(30) DEFAULT NULL,
  `emergency_route` varchar(4) DEFAULT NULL,
  `intracompany_route` varchar(4) DEFAULT NULL,
  `mohclass` varchar(80) DEFAULT NULL,
  `time_group_id` int(11) DEFAULT NULL,
  `dest` varchar(255) DEFAULT NULL,
  `time_mode` varchar(20) DEFAULT '',
  `calendar_id` varchar(255) DEFAULT NULL,
  `calendar_group_id` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `notification_on` varchar(255) DEFAULT 'call',
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `outroutemsg`
--

DROP TABLE IF EXISTS `outroutemsg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outroutemsg` (
  `keyword` varchar(40) NOT NULL DEFAULT '',
  `data` varchar(10) NOT NULL,
  PRIMARY KEY (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paging_autoanswer`
--

DROP TABLE IF EXISTS `paging_autoanswer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paging_autoanswer` (
  `useragent` varchar(190) NOT NULL,
  `var` varchar(20) NOT NULL,
  `setting` varchar(255) NOT NULL,
  PRIMARY KEY (`useragent`,`var`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paging_config`
--

DROP TABLE IF EXISTS `paging_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paging_config` (
  `page_group` varchar(190) NOT NULL DEFAULT '',
  `force_page` int(11) NOT NULL,
  `duplex` int(11) NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT '',
  `announcement` varchar(255) DEFAULT NULL,
  `volume` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`page_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paging_core_routing`
--

DROP TABLE IF EXISTS `paging_core_routing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paging_core_routing` (
  `route` varchar(25) NOT NULL DEFAULT '',
  `page_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`route`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paging_groups`
--

DROP TABLE IF EXISTS `paging_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paging_groups` (
  `page_number` varchar(50) NOT NULL DEFAULT '',
  `ext` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`page_number`,`ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parkplus`
--

DROP TABLE IF EXISTS `parkplus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parkplus` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `defaultlot` varchar(10) NOT NULL DEFAULT 'no',
  `type` varchar(10) NOT NULL DEFAULT 'public',
  `name` varchar(40) NOT NULL DEFAULT '',
  `parkext` varchar(40) NOT NULL DEFAULT '',
  `parkpos` varchar(40) NOT NULL DEFAULT '',
  `numslots` int(11) NOT NULL DEFAULT 4,
  `parkingtime` int(11) NOT NULL DEFAULT 45,
  `parkedmusicclass` varchar(100) DEFAULT 'default',
  `generatefc` varchar(10) NOT NULL DEFAULT 'yes',
  `findslot` varchar(10) NOT NULL DEFAULT 'first',
  `parkedplay` varchar(10) NOT NULL DEFAULT 'both',
  `parkedcalltransfers` varchar(10) NOT NULL DEFAULT 'caller',
  `parkedcallreparking` varchar(10) NOT NULL DEFAULT 'caller',
  `alertinfo` varchar(254) NOT NULL DEFAULT '',
  `rvolume` varchar(2) NOT NULL DEFAULT '',
  `cidpp` varchar(100) NOT NULL DEFAULT '',
  `autocidpp` varchar(10) NOT NULL DEFAULT 'none',
  `announcement_id` int(11) DEFAULT NULL,
  `comebacktoorigin` varchar(10) NOT NULL DEFAULT 'yes',
  `dest` varchar(100) NOT NULL DEFAULT 'app-blackhole,hangup,1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pinset_usage`
--

DROP TABLE IF EXISTS `pinset_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pinset_usage` (
  `dispname` varchar(30) NOT NULL DEFAULT '',
  `foreign_id` varchar(30) NOT NULL DEFAULT '',
  `pinsets_id` int(11) NOT NULL,
  PRIMARY KEY (`dispname`,`foreign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pinsets`
--

DROP TABLE IF EXISTS `pinsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pinsets` (
  `pinsets_id` int(11) NOT NULL AUTO_INCREMENT,
  `passwords` longtext DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `addtocdr` tinyint(1) DEFAULT NULL,
  `deptname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pinsets_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pjsip`
--

DROP TABLE IF EXISTS `pjsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pjsip` (
  `id` varchar(20) NOT NULL DEFAULT '-1',
  `keyword` varchar(30) NOT NULL DEFAULT '',
  `data` varchar(8100) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presencestate_list`
--

DROP TABLE IF EXISTS `presencestate_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presencestate_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(25) DEFAULT NULL,
  `message` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presencestate_prefs`
--

DROP TABLE IF EXISTS `presencestate_prefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presencestate_prefs` (
  `extension` varchar(20) NOT NULL,
  `item_id` int(11) NOT NULL,
  `pref` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`extension`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `queueprio`
--

DROP TABLE IF EXISTS `queueprio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queueprio` (
  `queueprio_id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_priority` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `dest` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`queueprio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `queues_config`
--

DROP TABLE IF EXISTS `queues_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queues_config` (
  `extension` varchar(20) NOT NULL DEFAULT '',
  `descr` varchar(35) NOT NULL DEFAULT '',
  `grppre` varchar(100) NOT NULL DEFAULT '',
  `alertinfo` varchar(254) NOT NULL DEFAULT '',
  `ringing` tinyint(1) NOT NULL DEFAULT 0,
  `maxwait` varchar(8) NOT NULL DEFAULT '',
  `password` varchar(20) NOT NULL DEFAULT '',
  `ivr_id` varchar(8) NOT NULL DEFAULT '0',
  `dest` varchar(50) NOT NULL DEFAULT '',
  `cwignore` tinyint(1) NOT NULL DEFAULT 0,
  `queuewait` tinyint(1) DEFAULT 0,
  `use_queue_context` tinyint(1) DEFAULT 0,
  `togglehint` tinyint(1) DEFAULT 0,
  `qnoanswer` tinyint(1) DEFAULT 0,
  `callconfirm` tinyint(1) DEFAULT 0,
  `callconfirm_id` int(11) DEFAULT NULL,
  `qregex` varchar(255) DEFAULT NULL,
  `agentannounce_id` int(11) DEFAULT NULL,
  `joinannounce_id` int(11) DEFAULT NULL,
  `monitor_type` varchar(5) DEFAULT NULL,
  `monitor_heard` int(11) DEFAULT NULL,
  `monitor_spoken` int(11) DEFAULT NULL,
  `callback_id` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`extension`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `queues_details`
--

DROP TABLE IF EXISTS `queues_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queues_details` (
  `id` varchar(45) NOT NULL DEFAULT '-1',
  `keyword` varchar(30) NOT NULL DEFAULT '',
  `data` varchar(150) NOT NULL DEFAULT '',
  `flags` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`keyword`,`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recordings`
--

DROP TABLE IF EXISTS `recordings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recordings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayname` varchar(50) DEFAULT NULL,
  `filename` longblob DEFAULT NULL,
  `description` varchar(254) DEFAULT NULL,
  `fcode` tinyint(1) DEFAULT 0,
  `fcode_pass` varchar(20) DEFAULT NULL,
  `fcode_lang` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ringgroups`
--

DROP TABLE IF EXISTS `ringgroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ringgroups` (
  `grpnum` varchar(20) NOT NULL,
  `strategy` varchar(50) NOT NULL,
  `grptime` smallint(6) NOT NULL,
  `grppre` varchar(100) DEFAULT NULL,
  `grplist` varchar(255) NOT NULL,
  `annmsg_id` int(11) DEFAULT NULL,
  `postdest` varchar(255) DEFAULT NULL,
  `description` varchar(35) NOT NULL,
  `alertinfo` varchar(255) DEFAULT NULL,
  `remotealert_id` int(11) DEFAULT NULL,
  `needsconf` varchar(10) DEFAULT NULL,
  `toolate_id` int(11) DEFAULT NULL,
  `ringing` varchar(80) DEFAULT NULL,
  `cwignore` varchar(10) DEFAULT NULL,
  `cfignore` varchar(10) DEFAULT NULL,
  `cpickup` varchar(10) DEFAULT NULL,
  `recording` varchar(10) DEFAULT 'dontcare',
  `progress` varchar(10) DEFAULT NULL,
  `elsewhere` varchar(10) DEFAULT NULL,
  `rvolume` varchar(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`grpnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setcid`
--

DROP TABLE IF EXISTS `setcid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setcid` (
  `cid_id` int(11) NOT NULL AUTO_INCREMENT,
  `cid_name` varchar(150) DEFAULT NULL,
  `cid_num` varchar(150) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `dest` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sip`
--

DROP TABLE IF EXISTS `sip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sip` (
  `id` varchar(20) NOT NULL DEFAULT '-1',
  `keyword` varchar(30) NOT NULL DEFAULT '',
  `data` varchar(8100) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sipsettings`
--

DROP TABLE IF EXISTS `sipsettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sipsettings` (
  `keyword` varchar(50) NOT NULL DEFAULT '',
  `seq` tinyint(1) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `data` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`keyword`,`seq`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soundlang_customlangs`
--

DROP TABLE IF EXISTS `soundlang_customlangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soundlang_customlangs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(20) NOT NULL,
  `description` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soundlang_packages`
--

DROP TABLE IF EXISTS `soundlang_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soundlang_packages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `module` varchar(80) NOT NULL,
  `language` varchar(20) NOT NULL,
  `license` longblob DEFAULT NULL,
  `author` varchar(80) DEFAULT NULL,
  `authorlink` varchar(256) DEFAULT NULL,
  `format` varchar(20) NOT NULL,
  `version` varchar(20) DEFAULT NULL,
  `installed` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `unique` (`type`,`module`,`language`,`format`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soundlang_prompts`
--

DROP TABLE IF EXISTS `soundlang_prompts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soundlang_prompts` (
  `type` varchar(20) NOT NULL,
  `module` varchar(80) NOT NULL,
  `language` varchar(20) NOT NULL,
  `format` varchar(20) NOT NULL,
  `filename` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soundlang_settings`
--

DROP TABLE IF EXISTS `soundlang_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soundlang_settings` (
  `keyword` varchar(20) NOT NULL,
  `value` varchar(80) NOT NULL,
  PRIMARY KEY (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timeconditions`
--

DROP TABLE IF EXISTS `timeconditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeconditions` (
  `timeconditions_id` int(11) NOT NULL AUTO_INCREMENT,
  `displayname` varchar(50) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `truegoto` varchar(50) DEFAULT NULL,
  `falsegoto` varchar(50) DEFAULT NULL,
  `deptname` varchar(50) DEFAULT NULL,
  `generate_hint` tinyint(1) DEFAULT 0,
  `invert_hint` tinyint(1) DEFAULT 0,
  `fcc_password` varchar(20) DEFAULT '',
  `priority` varchar(50) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `mode` varchar(20) DEFAULT 'time-group',
  `calendar_id` varchar(150) DEFAULT NULL,
  `calendar_group_id` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`timeconditions_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timegroups_details`
--

DROP TABLE IF EXISTS `timegroups_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timegroups_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timegroupid` int(11) NOT NULL DEFAULT 0,
  `time` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timegroups_groups`
--

DROP TABLE IF EXISTS `timegroups_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timegroups_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `display` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trunk_dialpatterns`
--

DROP TABLE IF EXISTS `trunk_dialpatterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trunk_dialpatterns` (
  `trunkid` int(11) NOT NULL DEFAULT 0,
  `match_pattern_prefix` varchar(50) NOT NULL DEFAULT '',
  `match_pattern_pass` varchar(50) NOT NULL DEFAULT '',
  `prepend_digits` varchar(50) NOT NULL DEFAULT '',
  `seq` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`trunkid`,`match_pattern_prefix`,`match_pattern_pass`,`prepend_digits`,`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trunks`
--

DROP TABLE IF EXISTS `trunks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trunks` (
  `trunkid` int(11) NOT NULL DEFAULT 0,
  `tech` varchar(20) NOT NULL,
  `channelid` varchar(190) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `outcid` varchar(40) NOT NULL DEFAULT '',
  `keepcid` varchar(4) DEFAULT 'off',
  `maxchans` varchar(6) DEFAULT '',
  `failscript` varchar(255) NOT NULL DEFAULT '',
  `dialoutprefix` varchar(255) NOT NULL DEFAULT '',
  `usercontext` varchar(255) DEFAULT NULL,
  `provider` varchar(40) DEFAULT NULL,
  `disabled` varchar(4) DEFAULT 'off',
  `continue` varchar(4) DEFAULT 'off',
  `routedisplay` varchar(4) DEFAULT 'on',
  PRIMARY KEY (`trunkid`,`tech`,`channelid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ttsengines`
--

DROP TABLE IF EXISTS `ttsengines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ttsengines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ucp_sessions`
--

DROP TABLE IF EXISTS `ucp_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ucp_sessions` (
  `session` varchar(190) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `socketid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`session`),
  UNIQUE KEY `session_UNIQUE` (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userman_directories`
--

DROP TABLE IF EXISTS `userman_directories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userman_directories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `driver` varchar(150) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 5,
  `default` tinyint(1) NOT NULL DEFAULT 0,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userman_groups`
--

DROP TABLE IF EXISTS `userman_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userman_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth` varchar(150) DEFAULT 'freepbx',
  `authid` varchar(750) DEFAULT NULL,
  `groupname` varchar(150) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `dateformat` varchar(100) DEFAULT NULL,
  `timeformat` varchar(100) DEFAULT NULL,
  `datetimeformat` varchar(100) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 5,
  `users` longblob DEFAULT NULL,
  `permissions` longblob DEFAULT NULL,
  `local` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `groupname_UNIQUE` (`groupname`,`auth`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userman_groups_settings`
--

DROP TABLE IF EXISTS `userman_groups_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userman_groups_settings` (
  `gid` int(11) NOT NULL,
  `module` varchar(65) NOT NULL,
  `key` varchar(190) NOT NULL,
  `val` longblob NOT NULL,
  `type` varchar(16) DEFAULT NULL,
  UNIQUE KEY `index4` (`gid`,`module`,`key`),
  KEY `index2` (`gid`,`key`),
  KEY `index6` (`module`,`gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userman_template_settings`
--

DROP TABLE IF EXISTS `userman_template_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userman_template_settings` (
  `tid` int(11) NOT NULL,
  `module` varchar(65) NOT NULL,
  `key` varchar(190) NOT NULL,
  `val` longblob NOT NULL,
  `type` varchar(16) DEFAULT NULL,
  UNIQUE KEY `index4` (`tid`,`module`,`key`),
  KEY `index2` (`tid`,`key`),
  KEY `index6` (`module`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userman_ucp_templates`
--

DROP TABLE IF EXISTS `userman_ucp_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userman_ucp_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templatename` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `importedfromuname` varchar(255) DEFAULT NULL,
  `importedfromuid` varchar(255) DEFAULT NULL,
  `defaultexten` varchar(255) DEFAULT NULL,
  `hasupdated` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userman_users`
--

DROP TABLE IF EXISTS `userman_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userman_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth` varchar(150) DEFAULT 'freepbx',
  `authid` varchar(750) DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `default_extension` varchar(45) NOT NULL DEFAULT 'none',
  `primary_group` int(11) DEFAULT NULL,
  `permissions` longblob DEFAULT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `displayname` varchar(200) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `dateformat` varchar(100) DEFAULT NULL,
  `timeformat` varchar(100) DEFAULT NULL,
  `datetimeformat` varchar(100) DEFAULT NULL,
  `email` longtext DEFAULT NULL,
  `cell` varchar(100) DEFAULT NULL,
  `work` varchar(100) DEFAULT NULL,
  `home` varchar(100) DEFAULT NULL,
  `fax` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`,`auth`)
) ENGINE=InnoDB AUTO_INCREMENT=264 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userman_users_settings`
--

DROP TABLE IF EXISTS `userman_users_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userman_users_settings` (
  `uid` int(11) NOT NULL,
  `module` varchar(65) NOT NULL,
  `key` varchar(190) NOT NULL,
  `val` longblob NOT NULL,
  `type` varchar(16) DEFAULT NULL,
  UNIQUE KEY `index4` (`uid`,`module`,`key`),
  KEY `index2` (`uid`,`key`),
  KEY `index6` (`module`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `extension` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(20) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `voicemail` varchar(50) DEFAULT NULL,
  `ringtimer` int(11) DEFAULT NULL,
  `noanswer` varchar(100) DEFAULT NULL,
  `recording` varchar(50) DEFAULT NULL,
  `outboundcid` varchar(50) DEFAULT NULL,
  `sipname` varchar(50) DEFAULT NULL,
  `noanswer_cid` varchar(20) NOT NULL DEFAULT '',
  `busy_cid` varchar(20) NOT NULL DEFAULT '',
  `chanunavail_cid` varchar(20) NOT NULL DEFAULT '',
  `noanswer_dest` varchar(255) NOT NULL DEFAULT '',
  `busy_dest` varchar(255) NOT NULL DEFAULT '',
  `chanunavail_dest` varchar(255) NOT NULL DEFAULT '',
  `mohclass` varchar(80) DEFAULT 'default',
  KEY `extension` (`extension`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vmblast`
--

DROP TABLE IF EXISTS `vmblast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vmblast` (
  `grpnum` bigint(20) NOT NULL,
  `description` varchar(35) NOT NULL,
  `audio_label` int(11) NOT NULL DEFAULT -1,
  `password` varchar(20) NOT NULL,
  PRIMARY KEY (`grpnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vmblast_groups`
--

DROP TABLE IF EXISTS `vmblast_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vmblast_groups` (
  `grpnum` bigint(20) NOT NULL,
  `ext` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`grpnum`,`ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voicemail_admin`
--

DROP TABLE IF EXISTS `voicemail_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voicemail_admin` (
  `variable` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`variable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webrtc_clients`
--

DROP TABLE IF EXISTS `webrtc_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webrtc_clients` (
  `user` varchar(190) NOT NULL,
  `device` varchar(190) NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `module` varchar(100) NOT NULL,
  `certid` int(11) DEFAULT NULL,
  UNIQUE KEY `userandprefix` (`user`,`prefix`),
  UNIQUE KEY `device` (`device`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'asterisk'
--

--
-- Dumping routines for database 'asterisk'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-30 19:30:07
