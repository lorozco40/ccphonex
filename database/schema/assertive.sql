-- MariaDB dump 10.19  Distrib 10.11.5-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: assertive
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
-- Table structure for table `api_con_ep`
--

DROP TABLE IF EXISTS `api_con_ep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_con_ep` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_extapi` int(11) NOT NULL,
  `proto` varchar(6) NOT NULL DEFAULT 'GET',
  `endpoint` varchar(127) NOT NULL DEFAULT '',
  `map_out_data` text NOT NULL DEFAULT '',
  `map_in_data` text NOT NULL DEFAULT '',
  `extra` text NOT NULL DEFAULT '',
  `log` tinyint(1) NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_api_con_ep_extapi` (`id_extapi`),
  CONSTRAINT `fk_api_con_ep_extapi` FOREIGN KEY (`id_extapi`) REFERENCES `extapi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bago_log`
--

DROP TABLE IF EXISTS `bago_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bago_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `evento` varchar(50) NOT NULL DEFAULT '',
  `data` text NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `comentario` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_bago_log_usuario` (`id_user`),
  CONSTRAINT `fk_bago_log_usuario` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=285 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banned_ips`
--

DROP TABLE IF EXISTS `banned_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banned_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `razon` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `break`
--

DROP TABLE IF EXISTS `break`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `break` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `break_entry`
--

DROP TABLE IF EXISTS `break_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `break_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_break` int(10) unsigned NOT NULL,
  `datetime_init` datetime NOT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `duration` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_break` (`id_break`),
  CONSTRAINT `break_entry_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `break_entry_ibfk_2` FOREIGN KEY (`id_break`) REFERENCES `break` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=302398 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_entry`
--

DROP TABLE IF EXISTS `call_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `call_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `queue` varchar(7) NOT NULL,
  `did` varchar(7) NOT NULL,
  `cid_name` varchar(32) NOT NULL,
  `cid_num` varchar(25) NOT NULL,
  `datetime_received` datetime DEFAULT NULL,
  `datetime_queued` datetime DEFAULT NULL,
  `datetime_init` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `duration` smallint(5) unsigned DEFAULT NULL,
  `duration_wait` smallint(5) unsigned DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `grabacion` varchar(100) DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `hangup` varchar(10) NOT NULL,
  `extra` varchar(255) NOT NULL,
  `rate` float(10,2) NOT NULL DEFAULT 0.00,
  `rate_type` varchar(10) NOT NULL DEFAULT 'na',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `id_agent` (`id_user`),
  KEY `datetime_init` (`datetime_init`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `call_entry_ibfk_5` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `call_entry_ibfk_6` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17736145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dids` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `script` text NOT NULL,
  `tlocal` decimal(10,2) DEFAULT NULL,
  `tcell` decimal(10,2) DEFAULT NULL,
  `tin` decimal(10,2) DEFAULT NULL,
  `outbound` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `campaign_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign_data`
--

DROP TABLE IF EXISTS `campaign_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `atributo` varchar(20) NOT NULL,
  `sub` varchar(20) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `orden` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_campaign_atributo_sub` (`id_campaign`,`atributo`,`sub`),
  CONSTRAINT `campaign_data_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign_hour`
--

DROP TABLE IF EXISTS `campaign_hour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_hour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `dia` char(1) NOT NULL,
  `inicio` time DEFAULT NULL,
  `fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_campaign_dia` (`id_campaign`,`dia`),
  CONSTRAINT `campaign_hour_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=750 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign_licenses`
--

DROP TABLE IF EXISTS `campaign_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_licenses` (
  `id_campaign` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  KEY `id_campaign` (`id_campaign`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `campaign_licenses_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `campaign_licenses_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign_vm`
--

DROP TABLE IF EXISTS `campaign_vm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign_vm` (
  `id_campaign` int(11) NOT NULL,
  `extension` varchar(5) NOT NULL,
  KEY `id_campaign_extension` (`id_campaign`,`extension`),
  CONSTRAINT `campaign_vm_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `catalogs`
--

DROP TABLE IF EXISTS `catalogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `catalogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat` varchar(45) NOT NULL,
  `eti` varchar(45) NOT NULL,
  `val` varchar(254) NOT NULL,
  `num_order` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cat_eti_val` (`cat`,`eti`,`val`(20))
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_entry`
--

DROP TABLE IF EXISTS `chat_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_chat_session` int(11) DEFAULT NULL,
  `message` text NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `extra` varchar(254) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chat_session_entries` (`id_chat_session`),
  CONSTRAINT `fk_chat_session_entries` FOREIGN KEY (`id_chat_session`) REFERENCES `chat_session` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_instance`
--

DROP TABLE IF EXISTS `chat_instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_instance` (
  `id` varchar(10) NOT NULL,
  `id_campaign` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_chat_instance_campaign` (`id_campaign`),
  CONSTRAINT `fk_chat_instance_campaign` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_instance_defs`
--

DROP TABLE IF EXISTS `chat_instance_defs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_instance_defs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_chat_instance` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL DEFAULT '',
  `extra` varchar(254) NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_chat_instance_defs_instance` (`id_chat_instance`),
  CONSTRAINT `fk_chat_instance_defs_instance` FOREIGN KEY (`id_chat_instance`) REFERENCES `chat_instance` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_session`
--

DROP TABLE IF EXISTS `chat_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ws` varchar(64) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_chat_instance` varchar(10) NOT NULL,
  `start` datetime NOT NULL DEFAULT current_timestamp(),
  `assign` datetime DEFAULT NULL,
  `answer` datetime DEFAULT NULL,
  `finish` datetime DEFAULT NULL,
  `wait` int(11) NOT NULL DEFAULT 0,
  `duration` mediumint(8) NOT NULL DEFAULT 0,
  `transfer` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'Cola',
  PRIMARY KEY (`id`),
  KEY `fk_chat_session_user` (`id_user`),
  KEY `fk_chat_session_chat_instance` (`id_chat_instance`),
  CONSTRAINT `fk_chat_session_chat_instance` FOREIGN KEY (`id_chat_instance`) REFERENCES `chat_instance` (`id`),
  CONSTRAINT `fk_chat_session_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chatinterno_entry`
--

DROP TABLE IF EXISTS `chatinterno_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatinterno_entry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario_emite` int(10) unsigned NOT NULL,
  `id_usuario_recibe` int(10) unsigned NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_recepcion` datetime DEFAULT NULL,
  `fecha_lectura` datetime DEFAULT NULL,
  `estatus` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `name` varchar(25) NOT NULL,
  `last` varchar(50) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(90) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  `calle` varchar(50) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `interior` varchar(10) NOT NULL,
  `colonia` varchar(100) NOT NULL,
  `dele_muni` varchar(50) NOT NULL,
  `ciudad` varchar(30) NOT NULL,
  `cp` char(5) NOT NULL,
  `pais` varchar(20) NOT NULL,
  `facebook` varchar(40) NOT NULL,
  `twitter` varchar(40) NOT NULL,
  `linkedin` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=865 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_gtwa`
--

DROP TABLE IF EXISTS `client_gtwa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_gtwa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `url` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `json` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `copy_call_entry`
--

DROP TABLE IF EXISTS `copy_call_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `copy_call_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `queue` varchar(7) NOT NULL,
  `did` varchar(7) NOT NULL,
  `cid_name` varchar(32) NOT NULL,
  `cid_num` varchar(25) NOT NULL,
  `datetime_received` datetime DEFAULT NULL,
  `datetime_queued` datetime DEFAULT NULL,
  `datetime_init` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `duration` smallint(5) unsigned DEFAULT NULL,
  `duration_wait` smallint(5) unsigned DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `grabacion` varchar(100) DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `hangup` varchar(10) NOT NULL,
  `extra` varchar(255) NOT NULL,
  `rate` float(10,2) NOT NULL DEFAULT 0.00,
  `rate_type` varchar(10) NOT NULL DEFAULT 'na',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `id_agent` (`id_user`),
  KEY `datetime_init` (`datetime_init`),
  KEY `id_campaign` (`id_campaign`)
) ENGINE=InnoDB AUTO_INCREMENT=16334878 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `copy_tarifas`
--

DROP TABLE IF EXISTS `copy_tarifas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `copy_tarifas` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `uniqueid` varchar(32) NOT NULL,
  `cid_num` varchar(25) NOT NULL,
  `calldate` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `duration` smallint(5) unsigned NOT NULL,
  `tipo_red` varchar(15) NOT NULL,
  `minutos` smallint(5) unsigned NOT NULL,
  `costo` decimal(6,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_campaign` (`id_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crm_light`
--

DROP TABLE IF EXISTS `crm_light`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_light` (
  `id_form` int(11) NOT NULL,
  `time_to_red` int(11) NOT NULL DEFAULT 1440,
  `msg_red` text NOT NULL,
  `notify_red` tinyint(1) NOT NULL DEFAULT 1,
  `time_to_step` int(11) NOT NULL DEFAULT 360,
  `msg_step` text NOT NULL,
  `notify_step` tinyint(1) NOT NULL DEFAULT 0,
  `notify_step_after_red` tinyint(1) NOT NULL DEFAULT 0,
  `time_to_warn` int(11) NOT NULL DEFAULT 1200,
  `msg_warn` text NOT NULL,
  `notify_warn` tinyint(1) NOT NULL DEFAULT 1,
  `campaign_hours` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_form`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `crm_light_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`),
  CONSTRAINT `crm_light_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crm_plant_pdf`
--

DROP TABLE IF EXISTS `crm_plant_pdf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_plant_pdf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `id_form` int(11) DEFAULT NULL,
  `name` varchar(25) NOT NULL,
  `file` varchar(25) NOT NULL,
  `cam_req` varchar(254) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  KEY `Created_by` (`created_by`),
  KEY `id_form` (`id_form`),
  CONSTRAINT `crm_plant_pdf_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `crm_plant_pdf_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `crm_plant_pdf_ibfk_3` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_1`
--

DROP TABLE IF EXISTS `disp_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fuente` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `adress` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `ville` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone_2_o_website` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_10`
--

DROP TABLE IF EXISTS `disp_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_10` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo` varchar(255) NOT NULL DEFAULT '',
  `account` varchar(255) NOT NULL DEFAULT '',
  `card` varchar(255) NOT NULL DEFAULT '',
  `ano` varchar(255) NOT NULL DEFAULT '',
  `tipo` varchar(255) NOT NULL DEFAULT '',
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `telefono2` varchar(255) NOT NULL DEFAULT '',
  `correo` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `suburb` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18555 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_10_qualif`
--

DROP TABLE IF EXISTS `disp_10_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_10_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  `tipificacion3` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_10_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_10` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_10` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1626 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_11`
--

DROP TABLE IF EXISTS `disp_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_11` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) NOT NULL DEFAULT '',
  `account` varchar(255) NOT NULL DEFAULT '',
  `card` varchar(255) NOT NULL DEFAULT '',
  `ano` varchar(255) NOT NULL DEFAULT '',
  `tipo` varchar(255) NOT NULL DEFAULT '',
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `correo` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `suburb` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11541 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_11_qualif`
--

DROP TABLE IF EXISTS `disp_11_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_11_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  `tipificacion3` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_11_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_11` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_11` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=269 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_12`
--

DROP TABLE IF EXISTS `disp_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_12` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promocion` varchar(255) NOT NULL DEFAULT '',
  `telefono_megafon` varchar(255) NOT NULL DEFAULT '',
  `telefono_casa` varchar(255) NOT NULL DEFAULT '',
  `telefono_oficina` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `ultimo_telefono` varchar(255) NOT NULL DEFAULT '',
  `sucursal_grupo` varchar(255) NOT NULL DEFAULT '',
  `sucursal` varchar(255) NOT NULL DEFAULT '',
  `numerosuc` varchar(255) NOT NULL DEFAULT '',
  `suscriptor` varchar(255) NOT NULL DEFAULT '',
  `s_s` varchar(255) NOT NULL DEFAULT '',
  `regi_n` varchar(255) NOT NULL DEFAULT '',
  `compatible_con_netflix` varchar(255) NOT NULL DEFAULT '',
  `compatible_con_xview` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellido_paterno` varchar(255) NOT NULL DEFAULT '',
  `apellido_materno` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `nse` varchar(255) NOT NULL DEFAULT '',
  `colonia` varchar(255) NOT NULL DEFAULT '',
  `domicilio` varchar(255) NOT NULL DEFAULT '',
  `flp` varchar(255) NOT NULL DEFAULT '',
  `serviciocable` varchar(255) NOT NULL DEFAULT '',
  `serviciointernet` varchar(255) NOT NULL DEFAULT '',
  `serviciotelefoniafija` varchar(255) NOT NULL DEFAULT '',
  `profeco` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_12_qualif`
--

DROP TABLE IF EXISTS `disp_12_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_12_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` mediumtext DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_12_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_12` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_12` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_13`
--

DROP TABLE IF EXISTS `disp_13`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_13` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promocion` varchar(255) NOT NULL DEFAULT '',
  `telefono_megafon` varchar(255) NOT NULL DEFAULT '',
  `telefono_casa` varchar(255) NOT NULL DEFAULT '',
  `telefono_oficina` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `ultimo_telefono` varchar(255) NOT NULL DEFAULT '',
  `sucursal_grupo` varchar(255) NOT NULL DEFAULT '',
  `sucursal` varchar(255) NOT NULL DEFAULT '',
  `numerosuc` varchar(255) NOT NULL DEFAULT '',
  `suscriptor` varchar(255) NOT NULL DEFAULT '',
  `s_s` varchar(255) NOT NULL DEFAULT '',
  `regi_n` varchar(255) NOT NULL DEFAULT '',
  `compatible_con_netflix` varchar(255) NOT NULL DEFAULT '',
  `compatible_con_xview` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellido_paterno` varchar(255) NOT NULL DEFAULT '',
  `apellido_materno` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `nse` varchar(255) NOT NULL DEFAULT '',
  `colonia` varchar(255) NOT NULL DEFAULT '',
  `domicilio` varchar(255) NOT NULL DEFAULT '',
  `flp` varchar(255) NOT NULL DEFAULT '',
  `serviciocable` varchar(255) NOT NULL DEFAULT '',
  `serviciointernet` varchar(255) NOT NULL DEFAULT '',
  `serviciotelefoniafija` varchar(255) NOT NULL DEFAULT '',
  `profeco` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_13_qualif`
--

DROP TABLE IF EXISTS `disp_13_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_13_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` mediumtext DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_13_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_13` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_13` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_14`
--

DROP TABLE IF EXISTS `disp_14`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_14` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_de_la_empresa` varchar(255) NOT NULL DEFAULT '',
  `nombre_comercial` varchar(255) NOT NULL DEFAULT '',
  `codigo_postal` varchar(255) NOT NULL DEFAULT '',
  `ciudad` varchar(255) NOT NULL DEFAULT '',
  `estado` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `categoria` varchar(255) NOT NULL DEFAULT '',
  `persona_de_contacto` varchar(255) NOT NULL DEFAULT '',
  `nombre_contacto_nuevo` varchar(255) NOT NULL DEFAULT '',
  `telefono_adicional` varchar(255) NOT NULL DEFAULT '',
  `correo_adicional` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=880 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_14_qualif`
--

DROP TABLE IF EXISTS `disp_14_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_14_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` mediumtext DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_14_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_14` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_14` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_15`
--

DROP TABLE IF EXISTS `disp_15`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_15` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `rfc` varchar(255) NOT NULL DEFAULT '',
  `domicilio` varchar(255) NOT NULL DEFAULT '',
  `numero` varchar(255) NOT NULL DEFAULT '',
  `interior` varchar(255) NOT NULL DEFAULT '',
  `colonia` varchar(255) NOT NULL DEFAULT '',
  `ciudad` varchar(255) NOT NULL DEFAULT '',
  `edo` varchar(255) NOT NULL DEFAULT '',
  `cp` varchar(255) NOT NULL DEFAULT '',
  `tel_contacto` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28901 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_15_qualif`
--

DROP TABLE IF EXISTS `disp_15_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_15_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_15_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_15` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_15` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6742 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_16`
--

DROP TABLE IF EXISTS `disp_16`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_16` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellido1` varchar(255) NOT NULL DEFAULT '',
  `apellido2` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `correo` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1535 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_16_qualif`
--

DROP TABLE IF EXISTS `disp_16_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_16_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_16_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_16` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_16` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2291 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_17`
--

DROP TABLE IF EXISTS `disp_17`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_17` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5864 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_17_qualif`
--

DROP TABLE IF EXISTS `disp_17_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_17_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_17_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_17` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_17` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=688 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_18`
--

DROP TABLE IF EXISTS `disp_18`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_18` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_de_creacion` datetime DEFAULT NULL,
  `estado_de_prospecto` varchar(255) NOT NULL DEFAULT '',
  `estatus` varchar(255) NOT NULL DEFAULT '',
  `identificador_empresa_site` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellidos` varchar(255) NOT NULL DEFAULT '',
  `compania_cuenta` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `origen_del_prospecto` varchar(255) NOT NULL DEFAULT '',
  `propietario_del_prospecto` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3759 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_18_qualif`
--

DROP TABLE IF EXISTS `disp_18_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_18_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_18_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_18` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_18` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_19`
--

DROP TABLE IF EXISTS `disp_19`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_19` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_de_creacion` datetime DEFAULT NULL,
  `estado_de_prospecto` varchar(255) NOT NULL DEFAULT '',
  `estados` varchar(255) NOT NULL DEFAULT '',
  `identificador_empresa_site` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellidos` varchar(255) NOT NULL DEFAULT '',
  `compania_cuenta` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `origen_del_prospecto` varchar(255) NOT NULL DEFAULT '',
  `propietario_del_prospecto` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3510 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_19_qualif`
--

DROP TABLE IF EXISTS `disp_19_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_19_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_19_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_19` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_19` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2802 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_1_qualif`
--

DROP TABLE IF EXISTS `disp_1_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_1_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_1_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_1` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_1` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5075 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_2`
--

DROP TABLE IF EXISTS `disp_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origine` varchar(255) NOT NULL DEFAULT '',
  `commentaire` varchar(255) NOT NULL DEFAULT '',
  `estatus` varchar(255) NOT NULL DEFAULT '',
  `commerciaux` varchar(255) NOT NULL DEFAULT '',
  `prenom` varchar(255) NOT NULL DEFAULT '',
  `telephone` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `commentaire2` varchar(255) NOT NULL DEFAULT '',
  `cp` varchar(255) NOT NULL DEFAULT '',
  `ville` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `consecutivo` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3754 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_20`
--

DROP TABLE IF EXISTS `disp_20`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_20` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `edad` varchar(255) NOT NULL DEFAULT '',
  `sexo` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_20_qualif`
--

DROP TABLE IF EXISTS `disp_20_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_20_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_20_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_20` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_20` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_21`
--

DROP TABLE IF EXISTS `disp_21`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_21` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_21_qualif`
--

DROP TABLE IF EXISTS `disp_21_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_21_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_21_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_21` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_21` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_23`
--

DROP TABLE IF EXISTS `disp_23`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_23` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `campana` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7357 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_23_qualif`
--

DROP TABLE IF EXISTS `disp_23_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_23_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_23_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_23` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_23` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3370 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_24`
--

DROP TABLE IF EXISTS `disp_24`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_24` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `campana` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2850 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_24_qualif`
--

DROP TABLE IF EXISTS `disp_24_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_24_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_24_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_24` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_24` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7372 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_25`
--

DROP TABLE IF EXISTS `disp_25`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_25` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1971 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_25_qualif`
--

DROP TABLE IF EXISTS `disp_25_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_25_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_25_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_25` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_25` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4509 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_26`
--

DROP TABLE IF EXISTS `disp_26`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_26` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellido` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `telefono_2` varchar(255) NOT NULL DEFAULT '',
  `ciudad` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `tipo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_26_qualif`
--

DROP TABLE IF EXISTS `disp_26_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_26_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tratamiento` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_26_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_26` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_26` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_27`
--

DROP TABLE IF EXISTS `disp_27`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_27` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellido` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `ciudad` varchar(255) NOT NULL DEFAULT '',
  `tipo_de_llamada` varchar(255) NOT NULL DEFAULT '',
  `interes` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_27_qualif`
--

DROP TABLE IF EXISTS `disp_27_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_27_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_27_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_27` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_27` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_2_qualif`
--

DROP TABLE IF EXISTS `disp_2_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_2_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_2_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_2` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_2` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3791 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_3`
--

DROP TABLE IF EXISTS `disp_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `tip1` varchar(255) NOT NULL DEFAULT '',
  `comentarios` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_30`
--

DROP TABLE IF EXISTS `disp_30`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_30` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2168 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_30_qualif`
--

DROP TABLE IF EXISTS `disp_30_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_30_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_30_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_30` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_30` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_32`
--

DROP TABLE IF EXISTS `disp_32`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_32` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3235 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_32_qualif`
--

DROP TABLE IF EXISTS `disp_32_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_32_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_32_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_32` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_32` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1953 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_33`
--

DROP TABLE IF EXISTS `disp_33`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_33` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3173 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_33_qualif`
--

DROP TABLE IF EXISTS `disp_33_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_33_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_33_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_33` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_33` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_34`
--

DROP TABLE IF EXISTS `disp_34`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_34` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3173 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_34_qualif`
--

DROP TABLE IF EXISTS `disp_34_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_34_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_34_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_34` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_34` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3604 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_35`
--

DROP TABLE IF EXISTS `disp_35`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_35` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_35_qualif`
--

DROP TABLE IF EXISTS `disp_35_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_35_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_35_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_35` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_35` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=899 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_36`
--

DROP TABLE IF EXISTS `disp_36`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_36` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_36_qualif`
--

DROP TABLE IF EXISTS `disp_36_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_36_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_36_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_36` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_36` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=935 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_37`
--

DROP TABLE IF EXISTS `disp_37`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_37` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_37_qualif`
--

DROP TABLE IF EXISTS `disp_37_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_37_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_37_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_37` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_37` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=459 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_38`
--

DROP TABLE IF EXISTS `disp_38`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_38` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_38_qualif`
--

DROP TABLE IF EXISTS `disp_38_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_38_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_38_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_38` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_38` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=507 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_39`
--

DROP TABLE IF EXISTS `disp_39`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_39` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_39_qualif`
--

DROP TABLE IF EXISTS `disp_39_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_39_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_39_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_39` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_39` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_3_qualif`
--

DROP TABLE IF EXISTS `disp_3_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_3_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` mediumtext DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_3_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_3` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_3` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_4`
--

DROP TABLE IF EXISTS `disp_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `prenom` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `cp` varchar(255) NOT NULL DEFAULT '',
  `ville` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3309 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_40`
--

DROP TABLE IF EXISTS `disp_40`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_40` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_40_qualif`
--

DROP TABLE IF EXISTS `disp_40_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_40_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_40_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_40` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_40` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=642 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_41`
--

DROP TABLE IF EXISTS `disp_41`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_41` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_41_qualif`
--

DROP TABLE IF EXISTS `disp_41_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_41_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_41_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_41` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_41` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_42`
--

DROP TABLE IF EXISTS `disp_42`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_42` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_42_qualif`
--

DROP TABLE IF EXISTS `disp_42_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_42_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_42_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_42` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_42` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=728 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_43`
--

DROP TABLE IF EXISTS `disp_43`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_43` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_43_qualif`
--

DROP TABLE IF EXISTS `disp_43_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_43_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_43_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_43` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_43` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_44`
--

DROP TABLE IF EXISTS `disp_44`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_44` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_44_qualif`
--

DROP TABLE IF EXISTS `disp_44_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_44_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_44_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_44` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_44` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_45`
--

DROP TABLE IF EXISTS `disp_45`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_45` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_45_qualif`
--

DROP TABLE IF EXISTS `disp_45_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_45_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_45_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_45` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_45` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=581 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_46`
--

DROP TABLE IF EXISTS `disp_46`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_46` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_46_qualif`
--

DROP TABLE IF EXISTS `disp_46_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_46_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_46_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_46` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_46` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_48`
--

DROP TABLE IF EXISTS `disp_48`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_48` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_48_qualif`
--

DROP TABLE IF EXISTS `disp_48_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_48_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_48_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_48` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_48` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_49`
--

DROP TABLE IF EXISTS `disp_49`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_49` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_49_qualif`
--

DROP TABLE IF EXISTS `disp_49_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_49_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_49_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_49` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_49` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=418 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_4_qualif`
--

DROP TABLE IF EXISTS `disp_4_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_4_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_4_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_4` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_4` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3356 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_5`
--

DROP TABLE IF EXISTS `disp_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_5` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fuente` varchar(255) NOT NULL DEFAULT '',
  `cabinet` varchar(255) NOT NULL DEFAULT '',
  `adress` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `ville` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `phone2_o_website` varchar(255) NOT NULL DEFAULT '',
  `asesor` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_50`
--

DROP TABLE IF EXISTS `disp_50`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_50` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_50_qualif`
--

DROP TABLE IF EXISTS `disp_50_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_50_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_50_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_50` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_50` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=951 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_52`
--

DROP TABLE IF EXISTS `disp_52`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_52` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_52_qualif`
--

DROP TABLE IF EXISTS `disp_52_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_52_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_52_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_52` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_52` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=520 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_53`
--

DROP TABLE IF EXISTS `disp_53`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_53` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_53_qualif`
--

DROP TABLE IF EXISTS `disp_53_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_53_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_53_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_53` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_53` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_54`
--

DROP TABLE IF EXISTS `disp_54`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_54` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_54_qualif`
--

DROP TABLE IF EXISTS `disp_54_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_54_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_54_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_54` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_54` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=699 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_55`
--

DROP TABLE IF EXISTS `disp_55`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_55` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=395 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_55_qualif`
--

DROP TABLE IF EXISTS `disp_55_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_55_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_55_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_55` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_55` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1032 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_56`
--

DROP TABLE IF EXISTS `disp_56`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_56` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=431 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_56_qualif`
--

DROP TABLE IF EXISTS `disp_56_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_56_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_56_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_56` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_56` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=708 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_57`
--

DROP TABLE IF EXISTS `disp_57`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_57` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=395 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_57_qualif`
--

DROP TABLE IF EXISTS `disp_57_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_57_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_57_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_57` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_57` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=546 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_58`
--

DROP TABLE IF EXISTS `disp_58`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_58` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_58_qualif`
--

DROP TABLE IF EXISTS `disp_58_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_58_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_58_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_58` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_58` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=728 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_59`
--

DROP TABLE IF EXISTS `disp_59`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_59` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_59_qualif`
--

DROP TABLE IF EXISTS `disp_59_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_59_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_59_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_59` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_59` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_5_qualif`
--

DROP TABLE IF EXISTS `disp_5_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_5_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_5_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_5` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_5` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=723 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_6`
--

DROP TABLE IF EXISTS `disp_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_6` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fuente` varchar(255) NOT NULL DEFAULT '',
  `cabinet` varchar(255) NOT NULL DEFAULT '',
  `adress` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `ville` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `asesor` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1573 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_60`
--

DROP TABLE IF EXISTS `disp_60`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_60` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=397 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_60_qualif`
--

DROP TABLE IF EXISTS `disp_60_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_60_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_60_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_60` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_60` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2618 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_61`
--

DROP TABLE IF EXISTS `disp_61`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_61` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_61_qualif`
--

DROP TABLE IF EXISTS `disp_61_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_61_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_61_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_61` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_61` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_62`
--

DROP TABLE IF EXISTS `disp_62`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_62` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_62_qualif`
--

DROP TABLE IF EXISTS `disp_62_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_62_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_62_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_62` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_62` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=774 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_63`
--

DROP TABLE IF EXISTS `disp_63`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_63` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_63_qualif`
--

DROP TABLE IF EXISTS `disp_63_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_63_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_63_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_63` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_63` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=559 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_64`
--

DROP TABLE IF EXISTS `disp_64`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_64` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_64_qualif`
--

DROP TABLE IF EXISTS `disp_64_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_64_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_64_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_64` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_64` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1173 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_65`
--

DROP TABLE IF EXISTS `disp_65`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_65` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_65_qualif`
--

DROP TABLE IF EXISTS `disp_65_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_65_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_65_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_65` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_65` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=557 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_66`
--

DROP TABLE IF EXISTS `disp_66`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_66` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=380 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_66_qualif`
--

DROP TABLE IF EXISTS `disp_66_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_66_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_66_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_66` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_66` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1430 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_67`
--

DROP TABLE IF EXISTS `disp_67`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_67` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_67_qualif`
--

DROP TABLE IF EXISTS `disp_67_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_67_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_67_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_67` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_67` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1634 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_68`
--

DROP TABLE IF EXISTS `disp_68`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_68` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `mail` varchar(255) NOT NULL DEFAULT '',
  `pais` varchar(255) NOT NULL DEFAULT '',
  `camp_platform` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_68_qualif`
--

DROP TABLE IF EXISTS `disp_68_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_68_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_68_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_68` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_68` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1212 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_69`
--

DROP TABLE IF EXISTS `disp_69`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_69` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `puesto` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fecha` datetime DEFAULT NULL,
  `nombre_completo` varchar(255) NOT NULL DEFAULT '',
  `nombre_empresa` varchar(255) NOT NULL DEFAULT '',
  `tel01` varchar(255) NOT NULL DEFAULT '',
  `tel_referencia` varchar(255) NOT NULL DEFAULT '',
  `fecha_seguimiento` datetime DEFAULT NULL,
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1486 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_69_qualif`
--

DROP TABLE IF EXISTS `disp_69_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_69_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_69_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_69` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_69` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1448 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_6_qualif`
--

DROP TABLE IF EXISTS `disp_6_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_6_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_6_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_6` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_6` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1609 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_7`
--

DROP TABLE IF EXISTS `disp_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_7` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_bd` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fuente` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `adress` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `ville` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone2_website` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_71`
--

DROP TABLE IF EXISTS `disp_71`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_71` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_equipo` varchar(255) NOT NULL DEFAULT '',
  `direccion` varchar(255) NOT NULL DEFAULT '',
  `sub_direccion` varchar(255) NOT NULL DEFAULT '',
  `region` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_71_qualif`
--

DROP TABLE IF EXISTS `disp_71_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_71_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_71_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_71` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_71` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_76`
--

DROP TABLE IF EXISTS `disp_76`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_76` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42621 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_76_qualif`
--

DROP TABLE IF EXISTS `disp_76_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_76_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_76_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_76` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_76` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_77`
--

DROP TABLE IF EXISTS `disp_77`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_77` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42621 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_77_qualif`
--

DROP TABLE IF EXISTS `disp_77_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_77_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_77_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_77` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_77` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6016 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_78`
--

DROP TABLE IF EXISTS `disp_78`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_78` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8023 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_78_qualif`
--

DROP TABLE IF EXISTS `disp_78_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_78_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(20) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_78_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_78` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_78` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11933 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_79`
--

DROP TABLE IF EXISTS `disp_79`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_79` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellidos` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `n-a` varchar(255) NOT NULL DEFAULT '',
  `origen_del_prospecto` varchar(255) NOT NULL DEFAULT '',
  `propietario_del_prospecto` varchar(255) NOT NULL DEFAULT '',
  `escuela_de_procedencia` varchar(255) NOT NULL DEFAULT '',
  `grado` varchar(255) NOT NULL DEFAULT '',
  `fecha_de_creacion` varchar(255) NOT NULL DEFAULT '',
  `ultima_modificacion` varchar(255) NOT NULL DEFAULT '',
  `periodo_de_interes_de_ingreso` varchar(255) NOT NULL DEFAULT '',
  `programa_de_interes` varchar(255) NOT NULL DEFAULT '',
  `periodo_de_interes` varchar(255) NOT NULL DEFAULT '',
  `estado_de_prospecto` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1244 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_79_qualif`
--

DROP TABLE IF EXISTS `disp_79_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_79_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_79_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_79` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_79` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74982 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_7_qualif`
--

DROP TABLE IF EXISTS `disp_7_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_7_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_7_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_7` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_7` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7281 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_8`
--

DROP TABLE IF EXISTS `disp_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_8` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fuente` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `direccion` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `ciudad` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone2_website` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6858 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_80`
--

DROP TABLE IF EXISTS `disp_80`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_80` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_de_la_campana` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellidos` varchar(255) NOT NULL DEFAULT '',
  `escuela_de_procedencia` varchar(255) NOT NULL DEFAULT '',
  `programa_de_interes` varchar(255) NOT NULL DEFAULT '',
  `medio_de_preferencia_de_contacto` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fecha_de_creacion` varchar(255) NOT NULL DEFAULT '',
  `estado_de_prospecto` varchar(255) NOT NULL DEFAULT '',
  `periodo_de_interes` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2092 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_80_qualif`
--

DROP TABLE IF EXISTS `disp_80_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_80_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_80_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_80` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_80` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80342 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_82`
--

DROP TABLE IF EXISTS `disp_82`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_82` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `agente` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `nombre_completo` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fase` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion_oportunidades` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progr_hom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7332 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_82_qualif`
--

DROP TABLE IF EXISTS `disp_82_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_82_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_82_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_82` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_82` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40811 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_83`
--

DROP TABLE IF EXISTS `disp_83`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_83` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `agente` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `lead_name` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `otro_telefono` varchar(255) NOT NULL DEFAULT '',
  `estado_de_lead` varchar(255) NOT NULL DEFAULT '',
  `fuente_de_lead` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion` varchar(255) NOT NULL DEFAULT '',
  `ano_creac` varchar(255) NOT NULL DEFAULT '',
  `mes_creac` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progr_hom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3456 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_83_qualif`
--

DROP TABLE IF EXISTS `disp_83_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_83_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_83_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_83` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_83` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_84`
--

DROP TABLE IF EXISTS `disp_84`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_84` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `agente` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `lead_name` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `otro_telefono` varchar(255) NOT NULL DEFAULT '',
  `estado_de_lead` varchar(255) NOT NULL DEFAULT '',
  `fuente_de_lead` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progrhom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3312 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_84_qualif`
--

DROP TABLE IF EXISTS `disp_84_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_84_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_84_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_84` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_84` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_85`
--

DROP TABLE IF EXISTS `disp_85`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_85` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `propietario_de_trato` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `nombre_completo` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fase` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion_oportunidades` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progr_hom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3872 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_85_qualif`
--

DROP TABLE IF EXISTS `disp_85_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_85_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_85_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_85` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_85` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_86`
--

DROP TABLE IF EXISTS `disp_86`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_86` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `agente` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `lead_name` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `otro_telefono` varchar(255) NOT NULL DEFAULT '',
  `estado_de_lead` varchar(255) NOT NULL DEFAULT '',
  `fuente_de_lead` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion` varchar(255) NOT NULL DEFAULT '',
  `ano_creac` varchar(255) NOT NULL DEFAULT '',
  `mes_creac` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progrhom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=651 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_86_qualif`
--

DROP TABLE IF EXISTS `disp_86_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_86_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_86_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_86` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_86` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_87`
--

DROP TABLE IF EXISTS `disp_87`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_87` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `agente` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `nombre_completo` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fase` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion_oportunidades` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progr_hom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=753 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_87_qualif`
--

DROP TABLE IF EXISTS `disp_87_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_87_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_87_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_87` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_87` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_88`
--

DROP TABLE IF EXISTS `disp_88`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_88` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `agente` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `lead_name` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `otro_telefono` varchar(255) NOT NULL DEFAULT '',
  `estado_de_lead` varchar(255) NOT NULL DEFAULT '',
  `fuente_de_lead` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progrhom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1181 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_88_qualif`
--

DROP TABLE IF EXISTS `disp_88_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_88_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_88_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_88` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_88` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_89`
--

DROP TABLE IF EXISTS `disp_89`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_89` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_de_registro` varchar(255) NOT NULL DEFAULT '',
  `agente` varchar(255) NOT NULL DEFAULT '',
  `telefono_agente` varchar(255) NOT NULL DEFAULT '',
  `nombre_completo` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `fase` varchar(255) NOT NULL DEFAULT '',
  `hora_de_creacion_oportunidades` varchar(255) NOT NULL DEFAULT '',
  `programa_academico` varchar(255) NOT NULL DEFAULT '',
  `progr_hom` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1144 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_89_qualif`
--

DROP TABLE IF EXISTS `disp_89_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_89_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion_2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_89_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_89` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_89` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_8_qualif`
--

DROP TABLE IF EXISTS `disp_8_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_8_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_8_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_8` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_8` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_9`
--

DROP TABLE IF EXISTS `disp_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fuente` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `direccion` varchar(255) NOT NULL DEFAULT '',
  `codigo_postal` varchar(255) NOT NULL DEFAULT '',
  `ciudad_de_mexico` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `phone2_o_website` varchar(255) NOT NULL DEFAULT '',
  `tamano_despacho` varchar(255) NOT NULL DEFAULT '',
  `otros` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6027 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_91`
--

DROP TABLE IF EXISTS `disp_91`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_91` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `phone_office` varchar(255) NOT NULL DEFAULT '',
  `telefacs` varchar(255) NOT NULL DEFAULT '',
  `estado` varchar(255) NOT NULL DEFAULT '',
  `modelo` varchar(255) NOT NULL DEFAULT '',
  `ano` varchar(255) NOT NULL DEFAULT '',
  `dia` varchar(255) NOT NULL DEFAULT '',
  `fecha` varchar(255) NOT NULL DEFAULT '',
  `hora` varchar(255) NOT NULL DEFAULT '',
  `agencia` varchar(255) NOT NULL DEFAULT '',
  `observaciones` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42208 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_91_qualif`
--

DROP TABLE IF EXISTS `disp_91_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_91_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_91_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_91` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_91` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_92`
--

DROP TABLE IF EXISTS `disp_92`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_92` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `phone_office` varchar(255) NOT NULL DEFAULT '',
  `telefacs` varchar(255) NOT NULL DEFAULT '',
  `estado` varchar(255) NOT NULL DEFAULT '',
  `modelo` varchar(255) NOT NULL DEFAULT '',
  `ano` varchar(255) NOT NULL DEFAULT '',
  `dia` varchar(255) NOT NULL DEFAULT '',
  `fecha` varchar(255) NOT NULL DEFAULT '',
  `hora` varchar(255) NOT NULL DEFAULT '',
  `agencia` varchar(255) NOT NULL DEFAULT '',
  `observaciones` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14070 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_92_qualif`
--

DROP TABLE IF EXISTS `disp_92_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_92_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_92_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_92` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_92` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16794 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_93`
--

DROP TABLE IF EXISTS `disp_93`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_93` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_cliente` varchar(255) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `cliente` varchar(255) NOT NULL DEFAULT '',
  `segmento` varchar(255) NOT NULL DEFAULT '',
  `qualif` varchar(50) NOT NULL DEFAULT '',
  `llamadas` tinyint(1) NOT NULL DEFAULT 0,
  `invalid` tinyint(1) NOT NULL DEFAULT 0,
  `access` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `busy` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_update` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_93_qualif`
--

DROP TABLE IF EXISTS `disp_93_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_93_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) NOT NULL,
  `linkedid` varchar(50) NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` text DEFAULT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_93_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_93` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_93` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_9_qualif`
--

DROP TABLE IF EXISTS `disp_9_qualif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_9_qualif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_disp_data` int(11) NOT NULL,
  `uniqueid` varchar(50) DEFAULT NULL,
  `linkedid` varchar(50) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_when` datetime NOT NULL,
  `tipificacion2` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_disp_data` (`id_disp_data`),
  KEY `saved_by` (`saved_by`),
  CONSTRAINT `disp_9_qualif_ibfk_1` FOREIGN KEY (`saved_by`) REFERENCES `user` (`id`),
  CONSTRAINT `disp_log_ibfk_9` FOREIGN KEY (`id_disp_data`) REFERENCES `disp_9` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2703 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_cond`
--

DROP TABLE IF EXISTS `disp_cond`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_cond` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dispatcher` int(11) NOT NULL,
  `hora` time NOT NULL,
  `accion` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 nada, 1 iniciar y 2 detener',
  `tipi` varchar(50) NOT NULL,
  `campo` varchar(50) NOT NULL,
  `camcond` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_dispatcher` (`id_dispatcher`),
  CONSTRAINT `disp_cond_ibfk_1` FOREIGN KEY (`id_dispatcher`) REFERENCES `dispatcher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_csv`
--

DROP TABLE IF EXISTS `disp_csv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_csv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dispatcher` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_dispatcher` (`id_dispatcher`),
  CONSTRAINT `disp_csv_ibfk_1` FOREIGN KEY (`id_dispatcher`) REFERENCES `dispatcher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_depend`
--

DROP TABLE IF EXISTS `disp_depend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_depend` (
  `campo` varchar(15) NOT NULL,
  `val1` varchar(100) NOT NULL,
  `val2` varchar(100) NOT NULL,
  `val3` varchar(100) NOT NULL,
  `val4` varchar(100) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `disp_depend_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_field`
--

DROP TABLE IF EXISTS `disp_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dispatcher` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `type` varchar(40) NOT NULL DEFAULT 'text',
  `typedb` tinyint(1) NOT NULL,
  `sfdes` tinyint(1) NOT NULL,
  `depend` tinyint(1) NOT NULL,
  `options` text NOT NULL,
  `showform` varchar(100) NOT NULL,
  `readonly` tinyint(1) NOT NULL DEFAULT 0,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `order` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `typedb` (`id_dispatcher`,`slug`,`typedb`),
  CONSTRAINT `disp_field_ibfk_1` FOREIGN KEY (`id_dispatcher`) REFERENCES `dispatcher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=934 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_sent`
--

DROP TABLE IF EXISTS `disp_sent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_sent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dispatcher` int(11) NOT NULL,
  `id_registro` int(11) NOT NULL,
  `fechahora_lanzada` datetime NOT NULL,
  `fechahora_regreso` datetime NOT NULL,
  `tiempo_ret` tinyint(2) NOT NULL DEFAULT 0,
  `actu` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_dispatcher` (`id_dispatcher`),
  CONSTRAINT `disp_sent_ibfk_1` FOREIGN KEY (`id_dispatcher`) REFERENCES `dispatcher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=172314 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disp_user`
--

DROP TABLE IF EXISTS `disp_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disp_user` (
  `id_dispatcher` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  UNIQUE KEY `id_disp_id_user` (`id_dispatcher`,`id_user`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `disp_user_ibfk_1` FOREIGN KEY (`id_dispatcher`) REFERENCES `dispatcher` (`id`),
  CONSTRAINT `disp_user_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dispatcher`
--

DROP TABLE IF EXISTS `dispatcher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dispatcher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `gateway` varchar(15) NOT NULL DEFAULT '10.10.2.133',
  `dialer` varchar(60) NOT NULL DEFAULT 'alinker',
  `maskname` varchar(60) NOT NULL DEFAULT 'Corporativo',
  `masknum` varchar(20) NOT NULL DEFAULT '5553750000',
  `rounds` tinyint(1) NOT NULL DEFAULT 7,
  `multi` tinyint(1) NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `running` tinyint(1) NOT NULL DEFAULT 0,
  `autodial` varchar(15) NOT NULL DEFAULT 'manual',
  `queue` varchar(5) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_when` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `dispatcher_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `dispatcher_ibfk_2` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_account`
--

DROP TABLE IF EXISTS `email_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(4) NOT NULL,
  `signature_text` varchar(254) NOT NULL,
  `signature_img` varchar(100) NOT NULL,
  `use` tinyint(2) DEFAULT NULL,
  `in_servidor` varchar(70) NOT NULL,
  `in_puerto` smallint(4) NOT NULL,
  `in_seguridad` varchar(10) NOT NULL,
  `in_user` varchar(150) NOT NULL,
  `in_pass` varchar(150) NOT NULL,
  `out_servidor` varchar(70) NOT NULL,
  `out_puerto` smallint(4) NOT NULL,
  `out_seguridad` varchar(10) NOT NULL,
  `out_user` varchar(150) NOT NULL,
  `out_pass` varchar(150) NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime(3) NOT NULL DEFAULT current_timestamp(3),
  `in_tipo` varchar(254) NOT NULL,
  `enviar` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `email_account_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `email_account_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `fk_email_account_campana` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_data`
--

DROP TABLE IF EXISTS `email_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `json` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1860 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_entry`
--

DROP TABLE IF EXISTS `email_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_account` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `transfer` int(11) DEFAULT NULL,
  `duration` int(6) unsigned DEFAULT NULL,
  `duration_wait` int(6) unsigned DEFAULT NULL,
  `duration_asa` int(6) unsigned DEFAULT NULL,
  `htmlmsg` mediumtext NOT NULL,
  `textmsg` text NOT NULL,
  `charset` varchar(15) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `from` varchar(150) NOT NULL,
  `to` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `cco` varchar(255) NOT NULL,
  `replyto` varchar(150) NOT NULL,
  `rawdate` varchar(50) NOT NULL,
  `date` datetime(3) NOT NULL,
  `subject` text NOT NULL,
  `attachments` longtext NOT NULL,
  `datetime_received` datetime(3) NOT NULL,
  `datetime_asigned` datetime(3) DEFAULT NULL,
  `datetime_startre` datetime(3) DEFAULT NULL,
  `datetime_reply` datetime(3) DEFAULT NULL,
  `type` varchar(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_agent` (`id_user`),
  KEY `id_account` (`id_account`),
  CONSTRAINT `email_entry_id_account_email_account_id_foreign` FOREIGN KEY (`id_account`) REFERENCES `email_account` (`id`),
  CONSTRAINT `email_entry_id_user_user_id_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `fk_email_account_mensajes` FOREIGN KEY (`id_account`) REFERENCES `email_account` (`id`),
  CONSTRAINT `fk_email_entry_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3340 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_transfer`
--

DROP TABLE IF EXISTS `email_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_email_entry` int(11) NOT NULL,
  `transferred_by` int(11) NOT NULL COMMENT 'Usuario que esta transfiriendo',
  `id_user` int(11) NOT NULL COMMENT 'Usuario a quien se le transfiere',
  `created_when` timestamp NOT NULL DEFAULT current_timestamp(),
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encuestas`
--

DROP TABLE IF EXISTS `encuestas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encuestas` (
  `uniqueid` varchar(20) NOT NULL,
  `linkedid` varchar(20) NOT NULL,
  `archivo` varchar(50) NOT NULL,
  `respuesta` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`uniqueid`,`archivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `endpoint`
--

DROP TABLE IF EXISTS `endpoint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `endpoint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `des` text NOT NULL,
  `method` varchar(10) NOT NULL,
  `route` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT 2,
  `resp` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `seq` tinyint(2) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_endpoint_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ep_param`
--

DROP TABLE IF EXISTS `ep_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ep_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_endpoint` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `des` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `req` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_endpoint_params` (`id_endpoint`),
  CONSTRAINT `fk_endpoint_params` FOREIGN KEY (`id_endpoint`) REFERENCES `endpoint` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `error_log`
--

DROP TABLE IF EXISTS `error_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `error_log` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `datetime_init` datetime DEFAULT NULL,
  `extension` int(5) DEFAULT NULL,
  `type` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `error_log_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=497532 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extapi`
--

DROP TABLE IF EXISTS `extapi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extapi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) DEFAULT NULL,
  `name` varchar(30) NOT NULL,
  `url` varchar(100) NOT NULL,
  `logloc` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=headers, 1=body, 2=auth',
  `sign` varchar(50) NOT NULL,
  `user` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `token` text NOT NULL,
  `xhash` varchar(65) NOT NULL,
  `info` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  `valid_crt` tinyint(1) NOT NULL DEFAULT 1,
  `valid_to` datetime NOT NULL DEFAULT '2150-12-31 23:59:59',
  `get_tk_ep` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_extapi_name` (`name`),
  KEY `id_campaign` (`id_campaign`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `extapi_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `extapi_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extapi_fields`
--

DROP TABLE IF EXISTS `extapi_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extapi_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_extapi_met` int(11) NOT NULL,
  `field` varchar(30) NOT NULL,
  `ftype` varchar(30) NOT NULL,
  `dir` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = sale, 1 = entra',
  `req` tinyint(1) NOT NULL DEFAULT 0,
  `descript` varchar(254) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_extapi_met` (`id_extapi_met`),
  CONSTRAINT `extapi_fields_ibfk_1` FOREIGN KEY (`id_extapi_met`) REFERENCES `extapi_met` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extapi_met`
--

DROP TABLE IF EXISTS `extapi_met`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extapi_met` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_extapi` int(11) NOT NULL,
  `prot` varchar(10) NOT NULL,
  `met` varchar(50) NOT NULL,
  `xtype` varchar(15) NOT NULL DEFAULT 'json' COMMENT 'json,form o ambos para empezar',
  `info` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_extapi` (`id_extapi`),
  CONSTRAINT `extapi_met_ibfk_1` FOREIGN KEY (`id_extapi`) REFERENCES `extapi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form`
--

DROP TABLE IF EXISTS `form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(10) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `crm` tinyint(1) NOT NULL DEFAULT 0,
  `infijo` varchar(254) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  `last_update` datetime NOT NULL DEFAULT current_timestamp(),
  `repstatdet` tinyint(4) NOT NULL DEFAULT 1,
  `id_email_account` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `id_campaign` (`id_campaign`),
  KEY `fk_form_email_account` (`id_email_account`),
  CONSTRAINT `fk_form_campaign` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `fk_form_email_account` FOREIGN KEY (`id_email_account`) REFERENCES `email_account` (`id`),
  CONSTRAINT `form_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `form_ibfk_3` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `form_ibfk_4` FOREIGN KEY (`id_email_account`) REFERENCES `email_account` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_api`
--

DROP TABLE IF EXISTS `form_api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_api` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `id_extapi` int(11) DEFAULT NULL,
  `proto` varchar(6) NOT NULL DEFAULT 'GET',
  `data_type` varchar(20) NOT NULL DEFAULT 'json',
  `endpoint` varchar(127) NOT NULL DEFAULT '',
  `on_when` tinyint(1) NOT NULL DEFAULT 2,
  `extra` text NOT NULL DEFAULT '',
  `map_out_data` text NOT NULL DEFAULT '',
  `map_in_data` text NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_form_api_form` (`id_form`),
  KEY `fk_form_api_extapi` (`id_extapi`),
  CONSTRAINT `fk_form_api_extapi` FOREIGN KEY (`id_extapi`) REFERENCES `extapi` (`id`),
  CONSTRAINT `fk_form_api_form` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_calc_fields`
--

DROP TABLE IF EXISTS `form_calc_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_calc_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `activator` varchar(12) NOT NULL,
  `field_r` varchar(12) NOT NULL,
  `field_a` varchar(12) NOT NULL,
  `operator` varchar(1) NOT NULL,
  `field_b` varchar(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_form` (`id_form`),
  CONSTRAINT `form_calc_fields_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_closing_operations`
--

DROP TABLE IF EXISTS `form_closing_operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_closing_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `field_r` varchar(55) NOT NULL,
  `field_a` varchar(55) NOT NULL,
  `custom_a` varchar(255) NOT NULL,
  `operator` varchar(3) NOT NULL,
  `field_b` varchar(55) NOT NULL,
  `custom_b` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_form` (`id_form`),
  CONSTRAINT `form_closing_operations_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_fields`
--

DROP TABLE IF EXISTS `form_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `type` varchar(30) NOT NULL,
  `len` smallint(4) DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `searchable` tinyint(1) NOT NULL DEFAULT 0,
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `base` tinyint(1) NOT NULL DEFAULT 0,
  `front` tinyint(1) NOT NULL DEFAULT 1,
  `api` tinyint(1) NOT NULL DEFAULT 0,
  `report` tinyint(1) NOT NULL DEFAULT 1,
  `values` text NOT NULL,
  `depend` tinyint(1) NOT NULL DEFAULT 0,
  `descen` varchar(50) NOT NULL,
  `order` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_form_slug` (`id_form`,`slug`),
  KEY `id_form` (`id_form`),
  CONSTRAINT `fk_form_fields_form` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`),
  CONSTRAINT `form_fields_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1259 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_fields_tbr`
--

DROP TABLE IF EXISTS `form_fields_tbr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_fields_tbr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `id_form_fields` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `type` varchar(30) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `order` tinyint(2) NOT NULL DEFAULT 0,
  `values` text NOT NULL,
  `depend` tinyint(1) NOT NULL DEFAULT 0,
  `descen` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_form_fields_slug` (`id_form_fields`,`slug`),
  KEY `id_form` (`id_form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_filter_dep`
--

DROP TABLE IF EXISTS `form_filter_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_filter_dep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `activator` varchar(100) NOT NULL COMMENT 'Campo de la tabla dependiente que activará el filtro cuando se cargue este valor',
  `field_to_filter` varchar(100) NOT NULL COMMENT 'Campo principal para indicar la tabla dependiente que se convertirá en un combo filtrado',
  `field_to_compare` varchar(100) NOT NULL COMMENT 'Campo con el cual se filtrará la información de la tabla dependiente con el activador',
  `union_table` varchar(100) NOT NULL DEFAULT '' COMMENT 'Avanzado: Tabla union',
  `union_field_a` varchar(100) NOT NULL DEFAULT '' COMMENT 'Avanzado campo union con tabla A',
  `union_field_b` varchar(100) NOT NULL DEFAULT '' COMMENT 'Avanzado campo union con tabla B',
  PRIMARY KEY (`id`),
  KEY `id_form` (`id_form`),
  CONSTRAINT `form_filter_dep_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_1`
--

DROP TABLE IF EXISTS `formd_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_10`
--

DROP TABLE IF EXISTS `formd_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_10` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_11`
--

DROP TABLE IF EXISTS `formd_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_11` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_12`
--

DROP TABLE IF EXISTS `formd_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_12` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_13`
--

DROP TABLE IF EXISTS `formd_13`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_13` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_14`
--

DROP TABLE IF EXISTS `formd_14`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_14` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `idcliente` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `primer_nombre` varchar(255) NOT NULL DEFAULT '',
  `segundo_nombre` varchar(255) NOT NULL DEFAULT '',
  `apellido_paterno` varchar(255) NOT NULL DEFAULT '',
  `apellido_materno` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(255) NOT NULL DEFAULT '',
  `telefono_local` varchar(255) NOT NULL DEFAULT '',
  `telefono_celular` varchar(255) NOT NULL DEFAULT '',
  `telefono_oficina` varchar(255) NOT NULL DEFAULT '',
  `calle` varchar(255) NOT NULL DEFAULT '',
  `num_interior` varchar(255) NOT NULL DEFAULT '',
  `num_exterior` varchar(255) NOT NULL DEFAULT '',
  `entre_calle1` varchar(255) NOT NULL DEFAULT '',
  `entre_calle2` varchar(255) NOT NULL DEFAULT '',
  `referencias` varchar(255) NOT NULL DEFAULT '',
  `colonia` varchar(255) NOT NULL DEFAULT '',
  `delegacion_municipio` varchar(255) NOT NULL DEFAULT '',
  `cp` varchar(255) NOT NULL DEFAULT '',
  `estado` varchar(255) NOT NULL DEFAULT '',
  `contacto` varchar(255) NOT NULL DEFAULT '',
  `fechaplaneadaentrega` varchar(255) NOT NULL DEFAULT '',
  `cobertura` varchar(255) NOT NULL DEFAULT '',
  `tipo_de_pago` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `consecutivo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_15`
--

DROP TABLE IF EXISTS `formd_15`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_15` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre_de_la_empresa` varchar(255) NOT NULL DEFAULT '',
  `nombre_comercial` varchar(255) NOT NULL DEFAULT '',
  `codigo_postal` varchar(255) NOT NULL DEFAULT '',
  `ciudad` varchar(255) NOT NULL DEFAULT '',
  `estado` varchar(255) NOT NULL DEFAULT '',
  `telefono` varchar(255) NOT NULL DEFAULT '',
  `categoria` varchar(255) NOT NULL DEFAULT '',
  `persona_de_contacto` varchar(255) NOT NULL DEFAULT '',
  `contacto_nuevo` varchar(255) NOT NULL DEFAULT '',
  `telefono_adicional` varchar(255) NOT NULL DEFAULT '',
  `correo_adicional` varchar(255) NOT NULL DEFAULT '',
  `comentarios` mediumtext NOT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_16`
--

DROP TABLE IF EXISTS `formd_16`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_16` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `saved_by` int(11) DEFAULT NULL,
  `saved_when` datetime DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `temas_de_ayuda` varchar(255) DEFAULT NULL,
  `departamento` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `nivel_de_prioridad` varchar(255) DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `resumen_problema` varchar(255) DEFAULT NULL,
  `edo_ticket` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_16_crm`
--

DROP TABLE IF EXISTS `formd_16_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_16_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_16_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_16` (`id`),
  CONSTRAINT `formd_16_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_16_file`
--

DROP TABLE IF EXISTS `formd_16_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_16_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_16_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_16` (`id`),
  CONSTRAINT `formd_16_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_17`
--

DROP TABLE IF EXISTS `formd_17`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_17` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `saved_by` int(11) DEFAULT NULL,
  `saved_when` datetime DEFAULT NULL,
  `quien_reporta` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `departamento` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `nivel_prioridad` varchar(255) DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `nombre_aduana` varchar(255) DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `equipo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `atte_en_sitio` varchar(255) DEFAULT NULL,
  `accion_realizada` varchar(255) DEFAULT NULL,
  `tec_contactado` varchar(255) DEFAULT NULL,
  `contacto_tec` datetime DEFAULT NULL,
  `resuelto_por_tel` varchar(255) DEFAULT NULL,
  `atte_menos_2_hrs` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `refacciones` mediumtext DEFAULT NULL,
  `reparacion` datetime DEFAULT NULL,
  `tiempo_respuesta` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_17_crm`
--

DROP TABLE IF EXISTS `formd_17_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_17_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_17_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_17` (`id`),
  CONSTRAINT `formd_17_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_17_file`
--

DROP TABLE IF EXISTS `formd_17_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_17_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_17_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_17` (`id`),
  CONSTRAINT `formd_17_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_18`
--

DROP TABLE IF EXISTS `formd_18`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_18` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `saved_by` int(11) DEFAULT NULL,
  `saved_when` datetime DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `departamento` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `nivel_de_prioridad` varchar(255) DEFAULT NULL,
  `nombre_aduana` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `equipo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `accion_realizada` text DEFAULT NULL,
  `ing_contactado` varchar(255) DEFAULT NULL,
  `fecha_contacto` datetime DEFAULT NULL,
  `resuelto_por_tel` varchar(255) DEFAULT NULL,
  `atte_menos_2_hrs` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `req_sustitucion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_18_crm`
--

DROP TABLE IF EXISTS `formd_18_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_18_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_18_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_18` (`id`),
  CONSTRAINT `formd_18_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_18_file`
--

DROP TABLE IF EXISTS `formd_18_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_18_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_18_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_18` (`id`),
  CONSTRAINT `formd_18_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_19`
--

DROP TABLE IF EXISTS `formd_19`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_19` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `saved_by` int(11) DEFAULT NULL,
  `saved_when` datetime DEFAULT NULL,
  `area_q_reporta` varchar(255) DEFAULT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `plazo` varchar(255) DEFAULT NULL,
  `tema_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `acciones` mediumtext DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `refaccion1` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `refaccion5` varchar(255) DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_19_crm`
--

DROP TABLE IF EXISTS `formd_19_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_19_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_formd` (`id_formd`),
  CONSTRAINT `formd_19_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `formd_19_crm_ibfk_5` FOREIGN KEY (`id_formd`) REFERENCES `formd_19` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_19_file`
--

DROP TABLE IF EXISTS `formd_19_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_19_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_19_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_19` (`id`),
  CONSTRAINT `formd_19_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_2`
--

DROP TABLE IF EXISTS `formd_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=374 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_20`
--

DROP TABLE IF EXISTS `formd_20`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_20` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `saved_by` int(11) DEFAULT NULL,
  `saved_when` datetime DEFAULT NULL,
  `area_q_reporta` varchar(255) DEFAULT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `plazo` varchar(255) DEFAULT NULL,
  `tema_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `acciones` mediumtext DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `refaccion1` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `refaccion5` varchar(255) DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_20_crm`
--

DROP TABLE IF EXISTS `formd_20_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_20_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_20_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_20` (`id`),
  CONSTRAINT `formd_20_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_20_file`
--

DROP TABLE IF EXISTS `formd_20_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_20_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_20_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_20` (`id`),
  CONSTRAINT `formd_20_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_21`
--

DROP TABLE IF EXISTS `formd_21`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_21` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `area_que_reporta` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `acciones` mediumtext DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `refaccion` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `refaccion_extra` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_21_crm`
--

DROP TABLE IF EXISTS `formd_21_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_21_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_21_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_21` (`id`),
  CONSTRAINT `formd_21_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_21_dep`
--

DROP TABLE IF EXISTS `formd_21_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_21_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `terminal` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_21_file`
--

DROP TABLE IF EXISTS `formd_21_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_21_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_21_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_21` (`id`),
  CONSTRAINT `formd_21_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_22`
--

DROP TABLE IF EXISTS `formd_22`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_22` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre_de_cliente` varchar(255) DEFAULT NULL,
  `nombre_de_la_empresa` varchar(255) DEFAULT NULL,
  `telefono_movil` varchar(255) DEFAULT NULL,
  `telefono_empresa` varchar(255) DEFAULT NULL,
  `comentarios_interes` mediumtext DEFAULT NULL,
  `email_de_contacto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_23`
--

DROP TABLE IF EXISTS `formd_23`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_23` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre_de_cliente` varchar(255) DEFAULT NULL,
  `telefono_movil` varchar(255) DEFAULT NULL,
  `email_de_contacto` varchar(255) DEFAULT NULL,
  `nombre_de_la_empresa` varchar(255) DEFAULT NULL,
  `telefono_empresa` varchar(255) DEFAULT NULL,
  `comentarios_interes` text DEFAULT NULL,
  `producto` varchar(255) DEFAULT NULL,
  `tipificacion` varchar(255) DEFAULT NULL,
  `tipificacion_2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_24`
--

DROP TABLE IF EXISTS `formd_24`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_24` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre_de_cliente` varchar(255) DEFAULT NULL,
  `telefono_movil` varchar(255) DEFAULT NULL,
  `email_de_contacto` varchar(255) DEFAULT NULL,
  `nombre_de_la_empresa` varchar(255) DEFAULT NULL,
  `telefono_empresa` varchar(255) DEFAULT NULL,
  `comentarios_interes` text DEFAULT NULL,
  `servicio` varchar(255) DEFAULT NULL,
  `tipificacion` varchar(255) DEFAULT NULL,
  `tipificacion_2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_25`
--

DROP TABLE IF EXISTS `formd_25`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_25` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `area_que_reporta` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `acciones` mediumtext DEFAULT NULL,
  `refaccion` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `refaccion_extra` mediumtext DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `plazo_mto` varchar(255) DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7890 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_25_crm`
--

DROP TABLE IF EXISTS `formd_25_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_25_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_25_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_25` (`id`),
  CONSTRAINT `formd_25_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37707 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_25_dep`
--

DROP TABLE IF EXISTS `formd_25_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_25_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `terminal` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `plazo_mto` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_25_file`
--

DROP TABLE IF EXISTS `formd_25_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_25_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_25_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_25` (`id`),
  CONSTRAINT `formd_25_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_26`
--

DROP TABLE IF EXISTS `formd_26`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_26` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `area_que_reporta` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `acciones` mediumtext DEFAULT NULL,
  `refaccion` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `refaccion_extra` mediumtext DEFAULT NULL,
  `detalle` mediumtext DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `plazo_mto` varchar(255) DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `semaforo` enum('verde','amarillo','rojo') NOT NULL DEFAULT 'verde',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28264 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_26_crm`
--

DROP TABLE IF EXISTS `formd_26_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_26_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_26_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_26` (`id`),
  CONSTRAINT `formd_26_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131861 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_26_dep`
--

DROP TABLE IF EXISTS `formd_26_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_26_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `terminal` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `plazo_mto` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_26_file`
--

DROP TABLE IF EXISTS `formd_26_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_26_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_26_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_26` (`id`),
  CONSTRAINT `formd_26_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_27`
--

DROP TABLE IF EXISTS `formd_27`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_27` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` datetime DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `tipificacion` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_28`
--

DROP TABLE IF EXISTS `formd_28`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_28` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `nivel_de_prioridad` varchar(255) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `accion_realizada` text DEFAULT NULL,
  `ing_contactado` varchar(255) DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `resuelto_por_tel` varchar(255) DEFAULT NULL,
  `atte_menos_2_hrs` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `f_hora_llamada` datetime DEFAULT NULL,
  `como_se_encontro` varchar(255) DEFAULT NULL,
  `refaccion_instalada` text DEFAULT NULL,
  `refaccion_retirada` text DEFAULT NULL,
  `cantidad_muestreos` varchar(255) DEFAULT NULL,
  `estatus_final` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `nombre_contacto` varchar(255) DEFAULT NULL,
  `tiempo_reparacion` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `cliente` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `no_inventario` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `coordinador` varchar(255) NOT NULL,
  `cuenta_de_correo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3634 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_28_crm`
--

DROP TABLE IF EXISTS `formd_28_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_28_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_28_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_28` (`id`),
  CONSTRAINT `formd_28_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8908 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_28_dep`
--

DROP TABLE IF EXISTS `formd_28_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_28_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `coordinador` varchar(100) NOT NULL,
  `cuenta_de_correo` varchar(150) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_28_depasign`
--

DROP TABLE IF EXISTS `formd_28_depasign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_28_depasign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activador` varchar(100) NOT NULL,
  `campo` varchar(100) NOT NULL,
  `copia` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_28_file`
--

DROP TABLE IF EXISTS `formd_28_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_28_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_28_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_28` (`id`),
  CONSTRAINT `formd_28_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_29`
--

DROP TABLE IF EXISTS `formd_29`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_29` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `quien_reporta` varchar(255) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `categoria_falla` varchar(255) DEFAULT NULL,
  `falla` varchar(255) DEFAULT NULL,
  `req_de_acceso` varchar(255) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `cuenta` varchar(255) DEFAULT NULL,
  `edificio` varchar(255) DEFAULT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `tpo_respuesta` varchar(255) DEFAULT NULL,
  `tpo_solucion` varchar(255) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `tipo_ticket` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_29_crm`
--

DROP TABLE IF EXISTS `formd_29_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_29_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_29_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_29` (`id`),
  CONSTRAINT `formd_29_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_29_dep`
--

DROP TABLE IF EXISTS `formd_29_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_29_dep` (
  `no_serie` varchar(25) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `cuenta` varchar(100) NOT NULL,
  `edificio` varchar(100) NOT NULL,
  `domicilio` varchar(100) NOT NULL,
  `tpo_respuesta` varchar(100) NOT NULL,
  `tpo_solucion` varchar(100) NOT NULL,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_29_file`
--

DROP TABLE IF EXISTS `formd_29_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_29_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_29_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_29` (`id`),
  CONSTRAINT `formd_29_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_3`
--

DROP TABLE IF EXISTS `formd_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_30`
--

DROP TABLE IF EXISTS `formd_30`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_30` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `articulos` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_30_crm`
--

DROP TABLE IF EXISTS `formd_30_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_30_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_30_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_30` (`id`),
  CONSTRAINT `formd_30_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_30_file`
--

DROP TABLE IF EXISTS `formd_30_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_30_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_30_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_30` (`id`),
  CONSTRAINT `formd_30_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_31`
--

DROP TABLE IF EXISTS `formd_31`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_31` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `area_que_reporta` varchar(255) DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `refaccion` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `refaccion_extra` text DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `plazo_mto` varchar(255) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_31_crm`
--

DROP TABLE IF EXISTS `formd_31_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_31_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_31_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_31` (`id`),
  CONSTRAINT `formd_31_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_31_dep`
--

DROP TABLE IF EXISTS `formd_31_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_31_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `terminal` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `plazo_mto` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_31_file`
--

DROP TABLE IF EXISTS `formd_31_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_31_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_31_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_31` (`id`),
  CONSTRAINT `formd_31_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_32`
--

DROP TABLE IF EXISTS `formd_32`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_32` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `nivel_de_prioridad` varchar(255) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `accion_realizada` text DEFAULT NULL,
  `ing_contactado` varchar(255) DEFAULT NULL,
  `fecha_contacto` datetime DEFAULT NULL,
  `resuelto_por_tel` varchar(255) DEFAULT NULL,
  `atte_menos_2_hrs` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `aduana` varchar(255) DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `refacciones` varchar(255) DEFAULT NULL,
  `f_hora_de_llamada` datetime DEFAULT NULL,
  `nom_contacto` varchar(255) DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2496 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_32_crm`
--

DROP TABLE IF EXISTS `formd_32_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_32_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_32_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_32` (`id`),
  CONSTRAINT `formd_32_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6697 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_32_dep`
--

DROP TABLE IF EXISTS `formd_32_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_32_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `aduana` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_32_file`
--

DROP TABLE IF EXISTS `formd_32_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_32_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_32_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_32` (`id`),
  CONSTRAINT `formd_32_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_33`
--

DROP TABLE IF EXISTS `formd_33`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_33` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `quien_reporta` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_33_crm`
--

DROP TABLE IF EXISTS `formd_33_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_33_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_33_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_33` (`id`),
  CONSTRAINT `formd_33_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=573 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_33_dep`
--

DROP TABLE IF EXISTS `formd_33_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_33_dep` (
  `serie` varchar(25) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_33_file`
--

DROP TABLE IF EXISTS `formd_33_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_33_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_33_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_33` (`id`),
  CONSTRAINT `formd_33_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_34`
--

DROP TABLE IF EXISTS `formd_34`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_34` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `Mantenimiento` varchar(254) NOT NULL DEFAULT '',
  `quien_reporta` varchar(255) DEFAULT NULL,
  `resolucion` varchar(255) DEFAULT NULL,
  `Atención` datetime DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `fuente` varchar(255) DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `ing_a_cargo` varchar(255) DEFAULT NULL,
  `semaforo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2297 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_34_crm`
--

DROP TABLE IF EXISTS `formd_34_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_34_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_34_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_34` (`id`),
  CONSTRAINT `formd_34_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6400 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_34_dep`
--

DROP TABLE IF EXISTS `formd_34_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_34_dep` (
  `serie` varchar(25) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `ing_a_cargo` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_34_file`
--

DROP TABLE IF EXISTS `formd_34_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_34_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_34_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_34` (`id`),
  CONSTRAINT `formd_34_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_35`
--

DROP TABLE IF EXISTS `formd_35`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_35` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `area_reporta` varchar(255) DEFAULT NULL,
  `fhora_llegada` datetime DEFAULT NULL,
  `fhora_termino` datetime DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `actividad_realizada` text DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `semaforo` enum('verde','amarillo','rojo') NOT NULL DEFAULT 'verde',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73236 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_35_crm`
--

DROP TABLE IF EXISTS `formd_35_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_35_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_35_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_35` (`id`),
  CONSTRAINT `formd_35_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=261769 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_35_dep`
--

DROP TABLE IF EXISTS `formd_35_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_35_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_35_file`
--

DROP TABLE IF EXISTS `formd_35_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_35_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_35_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_35` (`id`),
  CONSTRAINT `formd_35_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_36`
--

DROP TABLE IF EXISTS `formd_36`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_36` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `cliente` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `segmento` varchar(255) DEFAULT NULL,
  `asignacion` varchar(255) DEFAULT NULL,
  `estado_de_maquina` varchar(255) NOT NULL,
  `fech_vig_inicial` varchar(255) DEFAULT NULL,
  `fech_vig_final` varchar(255) DEFAULT NULL,
  `ciudad` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `ejecutivo_ccc` varchar(255) DEFAULT NULL,
  `tipo_de_problema` varchar(255) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) DEFAULT NULL,
  `estatus` varchar(255) DEFAULT NULL,
  `fecha_asignacion` datetime DEFAULT NULL,
  `fecha_llegada` datetime DEFAULT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `tel_contacto` varchar(255) DEFAULT NULL,
  `email_contacto` varchar(255) NOT NULL,
  `no_parte` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `para_que_modelo` varchar(255) DEFAULT NULL,
  `estatus_seguimiento` varchar(255) DEFAULT NULL,
  `serie_parte_actual` varchar(255) NOT NULL,
  `serie_parte_anterior` varchar(255) NOT NULL,
  `fecha_pedido` datetime DEFAULT NULL,
  `cantidad` varchar(255) NOT NULL,
  `proforma` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_36_crm`
--

DROP TABLE IF EXISTS `formd_36_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_36_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_36_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_36` (`id`),
  CONSTRAINT `formd_36_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_36_dep`
--

DROP TABLE IF EXISTS `formd_36_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_36_dep` (
  `serie` varchar(25) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `segmento` varchar(100) NOT NULL,
  `asignacion` varchar(100) NOT NULL,
  `estado_de_maquina` varchar(100) NOT NULL,
  `fech_vig_inicial` varchar(100) NOT NULL,
  `fech_vig_final` varchar(100) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `contacto` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `ejecutivo_ccc` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_36_dep1`
--

DROP TABLE IF EXISTS `formd_36_dep1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_36_dep1` (
  `no_parte` varchar(25) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `para_que_modelo` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_parte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_36_file`
--

DROP TABLE IF EXISTS `formd_36_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_36_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_36_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_36` (`id`),
  CONSTRAINT `formd_36_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_37`
--

DROP TABLE IF EXISTS `formd_37`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_37` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `subcategoria` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `medio` varchar(255) DEFAULT NULL,
  `email_asociado` varchar(255) DEFAULT NULL,
  `tipo_escuela` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2893 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_38`
--

DROP TABLE IF EXISTS `formd_38`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_38` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `medio` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `subcategoria` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `email_asociado` varchar(255) DEFAULT NULL,
  `tipo_escuela` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1590 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_39`
--

DROP TABLE IF EXISTS `formd_39`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_39` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `nombre_completo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `medio` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `subcategoria` varchar(255) DEFAULT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `email_asociado` varchar(255) DEFAULT NULL,
  `tipo_escuela` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_4`
--

DROP TABLE IF EXISTS `formd_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_40`
--

DROP TABLE IF EXISTS `formd_40`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_40` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) NOT NULL,
  `estatus` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_40_crm`
--

DROP TABLE IF EXISTS `formd_40_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_40_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_40_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_40` (`id`),
  CONSTRAINT `formd_40_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_40_file`
--

DROP TABLE IF EXISTS `formd_40_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_40_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_40_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_40` (`id`),
  CONSTRAINT `formd_40_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_41`
--

DROP TABLE IF EXISTS `formd_41`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_41` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) NOT NULL,
  `estatus` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  `area_reporta` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `fhora_llegada` datetime DEFAULT NULL,
  `actividad_realizada` text NOT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `fhora_termino` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1582 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_41_crm`
--

DROP TABLE IF EXISTS `formd_41_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_41_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_41_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_41` (`id`),
  CONSTRAINT `formd_41_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6413 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_41_dep`
--

DROP TABLE IF EXISTS `formd_41_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_41_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_41_file`
--

DROP TABLE IF EXISTS `formd_41_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_41_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_41_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_41` (`id`),
  CONSTRAINT `formd_41_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_42`
--

DROP TABLE IF EXISTS `formd_42`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_42` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) NOT NULL,
  `estatus` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  `creacion` datetime DEFAULT NULL,
  `fuente` varchar(255) DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `quien_reporta` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `resolucion` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `acciones` text DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `serie` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `ing_a_cargo` varchar(255) DEFAULT NULL,
  `coordinador` varchar(255) NOT NULL,
  `cuenta_de_correo` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=717 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_42_crm`
--

DROP TABLE IF EXISTS `formd_42_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_42_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_42_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_42` (`id`),
  CONSTRAINT `formd_42_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1794 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_42_dep`
--

DROP TABLE IF EXISTS `formd_42_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_42_dep` (
  `serie` varchar(25) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `ubicacion` varchar(500) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `ing_a_cargo` varchar(100) NOT NULL,
  `coordinador` varchar(100) NOT NULL,
  `cuenta_de_correo` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_42_dep1`
--

DROP TABLE IF EXISTS `formd_42_dep1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_42_dep1` (
  `serie` varchar(25) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `ing_a_cargo` varchar(100) NOT NULL,
  `coordinador` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_42_depasign`
--

DROP TABLE IF EXISTS `formd_42_depasign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_42_depasign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activador` varchar(100) NOT NULL,
  `campo` varchar(100) NOT NULL,
  `copia` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_42_file`
--

DROP TABLE IF EXISTS `formd_42_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_42_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_42_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_42` (`id`),
  CONSTRAINT `formd_42_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_43`
--

DROP TABLE IF EXISTS `formd_43`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_43` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) NOT NULL,
  `estatus` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  `area_que_reporta` varchar(255) DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `acciones` text NOT NULL,
  `refaccion` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `refaccion_extra` text NOT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `plazo_mto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_43_crm`
--

DROP TABLE IF EXISTS `formd_43_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_43_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_43_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_43` (`id`),
  CONSTRAINT `formd_43_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_43_dep`
--

DROP TABLE IF EXISTS `formd_43_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_43_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `terminal` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `plazo_mto` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_43_file`
--

DROP TABLE IF EXISTS `formd_43_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_43_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_43_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_43` (`id`),
  CONSTRAINT `formd_43_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_44`
--

DROP TABLE IF EXISTS `formd_44`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_44` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) NOT NULL,
  `estatus` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  `area_que_reporta` varchar(255) DEFAULT NULL,
  `creacion` datetime DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `tema_de_ayuda` varchar(255) DEFAULT NULL,
  `plan_ans` varchar(255) DEFAULT NULL,
  `ing_resp` varchar(255) DEFAULT NULL,
  `llegada` datetime DEFAULT NULL,
  `atte_sitio` varchar(255) DEFAULT NULL,
  `incidencias` varchar(255) DEFAULT NULL,
  `acciones` text NOT NULL,
  `refaccion` varchar(255) DEFAULT NULL,
  `refaccion2` varchar(255) DEFAULT NULL,
  `refaccion3` varchar(255) DEFAULT NULL,
  `refaccion4` varchar(255) DEFAULT NULL,
  `refaccion_extra` text NOT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `terminal` varchar(255) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `plazo_mto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2762 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_44_crm`
--

DROP TABLE IF EXISTS `formd_44_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_44_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_44_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_44` (`id`),
  CONSTRAINT `formd_44_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9579 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_44_dep`
--

DROP TABLE IF EXISTS `formd_44_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_44_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `terminal` varchar(100) NOT NULL,
  `contrato` varchar(100) NOT NULL,
  `plazo_mto` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_44_file`
--

DROP TABLE IF EXISTS `formd_44_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_44_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_44_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_44` (`id`),
  CONSTRAINT `formd_44_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_45`
--

DROP TABLE IF EXISTS `formd_45`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_45` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) DEFAULT NULL,
  `tipificacion_2` varchar(255) DEFAULT NULL,
  `tipificacion_3` varchar(255) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `tipificacion_4` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42118 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_46`
--

DROP TABLE IF EXISTS `formd_46`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_46` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) DEFAULT NULL,
  `tipificacion_2` varchar(255) DEFAULT NULL,
  `tipificacion_3` varchar(255) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4604 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_47`
--

DROP TABLE IF EXISTS `formd_47`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_47` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) DEFAULT NULL,
  `tipificacion_2` varchar(255) DEFAULT NULL,
  `tipificacion_3` varchar(255) DEFAULT NULL,
  `tipificacion_4` varchar(255) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_48`
--

DROP TABLE IF EXISTS `formd_48`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_48` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) NOT NULL,
  `estatus` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  `area_reporta` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `fhora_llegada` datetime DEFAULT NULL,
  `actividad_realizada` text NOT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `fhora_termino` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5640 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_48_crm`
--

DROP TABLE IF EXISTS `formd_48_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_48_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_48_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_48` (`id`),
  CONSTRAINT `formd_48_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21780 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_48_dep`
--

DROP TABLE IF EXISTS `formd_48_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_48_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_48_file`
--

DROP TABLE IF EXISTS `formd_48_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_48_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_48_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_48` (`id`),
  CONSTRAINT `formd_48_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_49`
--

DROP TABLE IF EXISTS `formd_49`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_49` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `cierre` datetime DEFAULT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 0,
  `asignar_a` int(11) DEFAULT NULL,
  `informar` varchar(255) NOT NULL,
  `estatus` varchar(255) NOT NULL,
  `semaforo` varchar(255) NOT NULL,
  `area_reporta` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `no_serie` varchar(255) DEFAULT NULL,
  `marca` varchar(255) DEFAULT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `fhora_llegada` datetime DEFAULT NULL,
  `actividad_realizada` text NOT NULL,
  `estado_final` varchar(255) DEFAULT NULL,
  `fhora_termino` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9379 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_49_crm`
--

DROP TABLE IF EXISTS `formd_49_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_49_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_49_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_49` (`id`),
  CONSTRAINT `formd_49_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_49_dep`
--

DROP TABLE IF EXISTS `formd_49_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_49_dep` (
  `no_serie` varchar(25) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`no_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_49_file`
--

DROP TABLE IF EXISTS `formd_49_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_49_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_49_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_49` (`id`),
  CONSTRAINT `formd_49_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=398 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_5`
--

DROP TABLE IF EXISTS `formd_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_5` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_50`
--

DROP TABLE IF EXISTS `formd_50`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_50` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL,
  `tipo` varchar(254) NOT NULL DEFAULT '',
  `prioridad` varchar(254) NOT NULL DEFAULT '',
  `cierre` datetime DEFAULT NULL,
  `id_cliente` varchar(11) NOT NULL DEFAULT '',
  `asignar_a` varchar(11) NOT NULL DEFAULT '',
  `informar` varchar(127) NOT NULL DEFAULT '',
  `estatus` varchar(254) NOT NULL DEFAULT '',
  `semaforo` varchar(254) NOT NULL DEFAULT '',
  `id_externo` varchar(127) NOT NULL DEFAULT '',
  `tipi_1` varchar(254) NOT NULL DEFAULT '',
  `tipi_2` varchar(254) NOT NULL DEFAULT '',
  `tipi_3` varchar(254) NOT NULL DEFAULT '',
  `observaciones` text NOT NULL DEFAULT '',
  `tiempo_reparacion` varchar(127) NOT NULL DEFAULT '',
  `estatus_final` varchar(254) NOT NULL DEFAULT '',
  `cantidad_muestreos` varchar(127) NOT NULL DEFAULT '',
  `atte_menos_2_hrs` varchar(254) NOT NULL DEFAULT '',
  `resuelto_por_tel` varchar(254) NOT NULL DEFAULT '',
  `ing_contactado` varchar(127) NOT NULL DEFAULT '',
  `refaccion_instalada` text NOT NULL DEFAULT '',
  `refaccion_retirada` text NOT NULL DEFAULT '',
  `accion_realizada` text NOT NULL DEFAULT '',
  `diagnostico` text NOT NULL DEFAULT '',
  `como_se_encontro` varchar(254) NOT NULL DEFAULT '',
  `f_hora_llegada` datetime DEFAULT NULL,
  `f_hora_llamada` datetime DEFAULT NULL,
  `plan_ans` varchar(254) NOT NULL DEFAULT '',
  `tema_de_ayuda` varchar(127) NOT NULL DEFAULT '',
  `telefono` varchar(127) NOT NULL DEFAULT '',
  `cargo` varchar(127) NOT NULL DEFAULT '',
  `quien_reporta` varchar(127) NOT NULL DEFAULT '',
  `creacion` datetime DEFAULT NULL,
  `termino` datetime DEFAULT NULL,
  `no_inventario` varchar(127) NOT NULL DEFAULT '',
  `version` varchar(127) NOT NULL DEFAULT '',
  `serie` varchar(127) NOT NULL DEFAULT '',
  `marca` varchar(127) NOT NULL DEFAULT '',
  `modelo` varchar(127) NOT NULL DEFAULT '',
  `cliente` varchar(127) NOT NULL DEFAULT '',
  `direccion` varchar(127) NOT NULL DEFAULT '',
  `ubicacion` varchar(127) NOT NULL DEFAULT '',
  `coordinador` varchar(127) NOT NULL DEFAULT '',
  `cuenta_de_correo` varchar(127) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_50_cats`
--

DROP TABLE IF EXISTS `formd_50_cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_50_cats` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `field` varchar(16) NOT NULL,
  `parent` varchar(16) NOT NULL,
  `eti` varchar(64) NOT NULL,
  `val` varchar(64) NOT NULL,
  `seq` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `field_val` (`field`,`val`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_50_crm`
--

DROP TABLE IF EXISTS `formd_50_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_50_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_50_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_50` (`id`),
  CONSTRAINT `formd_50_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=475 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_50_dep`
--

DROP TABLE IF EXISTS `formd_50_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_50_dep` (
  `serie` varchar(50) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `coordinador` varchar(100) NOT NULL,
  `cuenta_de_correo` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_50_file`
--

DROP TABLE IF EXISTS `formd_50_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_50_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) NOT NULL,
  `filename` varchar(80) NOT NULL,
  `comentario` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_50_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_50` (`id`),
  CONSTRAINT `formd_50_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_51`
--

DROP TABLE IF EXISTS `formd_51`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_51` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL DEFAULT '',
  `tipo` varchar(254) NOT NULL DEFAULT '',
  `prioridad` varchar(254) NOT NULL DEFAULT '',
  `cierre` datetime DEFAULT NULL,
  `id_cliente` varchar(11) NOT NULL DEFAULT '',
  `asignar_a` varchar(11) NOT NULL DEFAULT '',
  `informar` varchar(127) NOT NULL DEFAULT '',
  `estatus` varchar(254) NOT NULL DEFAULT '',
  `semaforo` varchar(254) NOT NULL DEFAULT '',
  `id_externo` varchar(127) NOT NULL DEFAULT '',
  `no_inventario` varchar(127) NOT NULL DEFAULT '',
  `version` varchar(127) NOT NULL DEFAULT '',
  `quien_reporta` varchar(127) NOT NULL DEFAULT '',
  `cargo` varchar(127) NOT NULL DEFAULT '',
  `telefono` varchar(127) NOT NULL DEFAULT '',
  `tema_de_ayuda` varchar(254) NOT NULL DEFAULT '',
  `tipi_1` varchar(254) NOT NULL DEFAULT '',
  `tipi_2` varchar(254) NOT NULL DEFAULT '',
  `tipi_3` varchar(254) NOT NULL DEFAULT '',
  `plan_ans` varchar(254) NOT NULL DEFAULT '',
  `f_hora_llamada` datetime DEFAULT NULL,
  `f_hora_llegada` datetime DEFAULT NULL,
  `como_se_encontro` varchar(254) NOT NULL DEFAULT '',
  `diagnostico` text NOT NULL DEFAULT '',
  `accion_realizada` text NOT NULL DEFAULT '',
  `refaccion_retirada` text NOT NULL DEFAULT '',
  `refaccion_instalada` text NOT NULL DEFAULT '',
  `ing_contactado` varchar(127) NOT NULL DEFAULT '',
  `resuelto_por_tel` varchar(254) NOT NULL DEFAULT '',
  `atte_menos_2_hrs` varchar(254) NOT NULL DEFAULT '',
  `cantidad_muestreos` varchar(127) NOT NULL DEFAULT '',
  `estatus_final` varchar(254) NOT NULL DEFAULT '',
  `tiempo_reparacion` varchar(127) NOT NULL DEFAULT '',
  `observaciones` text NOT NULL DEFAULT '',
  `serie` varchar(127) NOT NULL DEFAULT '',
  `marca` varchar(127) NOT NULL DEFAULT '',
  `modelo` varchar(127) NOT NULL DEFAULT '',
  `cliente` varchar(127) NOT NULL DEFAULT '',
  `aduana` varchar(127) NOT NULL DEFAULT '',
  `ubicacion` varchar(127) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_51_cats`
--

DROP TABLE IF EXISTS `formd_51_cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_51_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field` varchar(16) NOT NULL,
  `parent` varchar(16) NOT NULL,
  `eti` varchar(64) NOT NULL,
  `val` varchar(64) NOT NULL,
  `seq` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `field_val` (`field`,`val`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_51_crm`
--

DROP TABLE IF EXISTS `formd_51_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_51_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_51_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_51` (`id`),
  CONSTRAINT `formd_51_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_51_dep`
--

DROP TABLE IF EXISTS `formd_51_dep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_51_dep` (
  `serie` varchar(50) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `cliente` varchar(100) NOT NULL,
  `aduana` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `active_system_row` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_51_file`
--

DROP TABLE IF EXISTS `formd_51_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_51_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) NOT NULL,
  `filename` varchar(80) NOT NULL,
  `comentario` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_51_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_51` (`id`),
  CONSTRAINT `formd_51_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_52`
--

DROP TABLE IF EXISTS `formd_52`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_52` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `detalle` text NOT NULL DEFAULT '',
  `tipo` varchar(254) NOT NULL DEFAULT '',
  `prioridad` varchar(254) NOT NULL DEFAULT '',
  `cierre` datetime DEFAULT NULL,
  `id_cliente` varchar(11) NOT NULL DEFAULT '',
  `asignar_a` varchar(11) NOT NULL DEFAULT '',
  `informar` varchar(127) NOT NULL DEFAULT '',
  `estatus` varchar(32) NOT NULL DEFAULT '',
  `semaforo` varchar(127) NOT NULL DEFAULT '',
  `tipificacion` varchar(254) NOT NULL DEFAULT '',
  `comentarios` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_52_cats`
--

DROP TABLE IF EXISTS `formd_52_cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_52_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field` varchar(16) NOT NULL,
  `parent` varchar(16) NOT NULL,
  `eti` varchar(64) NOT NULL,
  `val` varchar(254) NOT NULL,
  `seq` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `field_val` (`field`,`val`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_52_crm`
--

DROP TABLE IF EXISTS `formd_52_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_52_crm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `uniqueid` varchar(64) NOT NULL,
  `linkedid` varchar(64) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` text NOT NULL,
  `estatus` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_52_crm_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_52` (`id`),
  CONSTRAINT `formd_52_crm_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_52_file`
--

DROP TABLE IF EXISTS `formd_52_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_52_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_formd` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `name` text NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_formd` (`id_formd`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `formd_52_file_ibfk_1` FOREIGN KEY (`id_formd`) REFERENCES `formd_52` (`id`),
  CONSTRAINT `formd_52_file_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_6`
--

DROP TABLE IF EXISTS `formd_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_6` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_7`
--

DROP TABLE IF EXISTS `formd_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_7` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_8`
--

DROP TABLE IF EXISTS `formd_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_8` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `apertura` datetime DEFAULT NULL,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formd_9`
--

DROP TABLE IF EXISTS `formd_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formd_9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(32) DEFAULT NULL,
  `linkedid` varchar(32) DEFAULT NULL,
  `tipificacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hc_chats`
--

DROP TABLE IF EXISTS `hc_chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hc_chats` (
  `id` int(11) NOT NULL,
  `id_chat_user` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nick` varchar(30) NOT NULL,
  `status` int(1) NOT NULL,
  `time` datetime NOT NULL,
  `ip` varchar(35) NOT NULL,
  `referrer` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `user_status` int(1) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `user_typing` datetime NOT NULL,
  `user_typing_text` varchar(80) NOT NULL,
  `operator_typing` int(1) NOT NULL,
  `has_unread_messages` int(1) NOT NULL,
  `last_user_msg_time` datetime NOT NULL,
  `last_msg_id` int(11) NOT NULL,
  `wait_time` smallint(5) NOT NULL,
  `chat_duration` smallint(5) NOT NULL,
  `lsync` datetime NOT NULL,
  `last_op_msg_time` datetime NOT NULL,
  `user_closed_ts` datetime NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `hc_chats_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hc_messages`
--

DROP TABLE IF EXISTS `hc_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hc_messages` (
  `id` int(11) NOT NULL,
  `id_chat` int(11) NOT NULL,
  `id_chat_user` int(11) NOT NULL,
  `msg` varchar(250) NOT NULL,
  `datetime` datetime NOT NULL,
  `name_support` varchar(50) NOT NULL,
  `meta_msg` varchar(50) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iftdata`
--

DROP TABLE IF EXISTS `iftdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iftdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cve_censal` varchar(20) NOT NULL,
  `poblacion` varchar(100) NOT NULL,
  `municipio` varchar(254) NOT NULL,
  `estado` varchar(10) NOT NULL,
  `prsuscrip` varchar(2) NOT NULL,
  `region` varchar(2) NOT NULL,
  `asl` varchar(4) NOT NULL,
  `nir` varchar(4) NOT NULL,
  `serie` varchar(4) NOT NULL,
  `num_ini` varchar(5) NOT NULL,
  `num_fin` varchar(5) NOT NULL,
  `ocupa` varchar(10) NOT NULL,
  `tipo_red` varchar(10) NOT NULL,
  `modalidad` varchar(5) NOT NULL,
  `razon_soc` varchar(254) NOT NULL,
  `nir_ant` varchar(4) NOT NULL,
  `nirie` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=141988 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `etiqueta` varchar(100) NOT NULL,
  `submenu` tinyint(1) unsigned NOT NULL COMMENT 'es un submenu?',
  `pertenece` int(11) DEFAULT NULL,
  `orden` tinyint(3) unsigned NOT NULL,
  `nivel` tinyint(3) unsigned NOT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `permiso` varchar(100) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `orden_lista` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pertenece` (`pertenece`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`pertenece`) REFERENCES `menu` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meta_msgr_hook`
--

DROP TABLE IF EXISTS `meta_msgr_hook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta_msgr_hook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` text NOT NULL,
  `recibido` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `perm` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_permission_user` (`id_user`),
  CONSTRAINT `fk_permission_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pit_catalog`
--

DROP TABLE IF EXISTS `pit_catalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pit_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `pin` varchar(10) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `name` varchar(150) NOT NULL,
  `last` varchar(150) DEFAULT NULL,
  `aviso` varchar(400) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `motivo` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_when` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `pit_catalog_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `pit_catalog_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pit_entry`
--

DROP TABLE IF EXISTS `pit_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pit_entry` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_pit_catalog` int(11) DEFAULT NULL,
  `msg` text DEFAULT NULL,
  `operator` varchar(100) NOT NULL,
  `resp` varchar(200) NOT NULL,
  `json` text NOT NULL,
  `uid` varchar(40) NOT NULL,
  `status` varchar(10) NOT NULL,
  `status_desc` varchar(100) NOT NULL,
  `redirected` varchar(3) NOT NULL DEFAULT 'NO',
  `datetime_init` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `FK_ptt_user` (`id_user`),
  KEY `id_pit_catalog` (`id_pit_catalog`),
  CONSTRAINT `FK_ptt_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `pit_entry_ibfk_1` FOREIGN KEY (`id_pit_catalog`) REFERENCES `pit_catalog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pit_redirect`
--

DROP TABLE IF EXISTS `pit_redirect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pit_redirect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pit_catalog` int(11) NOT NULL,
  `id_pit_catalog_redirect` int(11) NOT NULL,
  `vigencia` date NOT NULL,
  `vigencia_hora` time NOT NULL DEFAULT '23:59:59',
  PRIMARY KEY (`id`),
  KEY `id_pit_catalog` (`id_pit_catalog`),
  KEY `id_pit_catalog_redirect` (`id_pit_catalog_redirect`),
  CONSTRAINT `pit_redirect_ibfk_1` FOREIGN KEY (`id_pit_catalog`) REFERENCES `pit_catalog` (`id`),
  CONSTRAINT `pit_redirect_ibfk_2` FOREIGN KEY (`id_pit_catalog_redirect`) REFERENCES `pit_catalog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pit_template`
--

DROP TABLE IF EXISTS `pit_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pit_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valor` text NOT NULL,
  `name` varchar(100) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `pit_template_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quality`
--

DROP TABLE IF EXISTS `quality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'llamadas',
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_when` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `quality_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `quality_ibfk_3` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quality_fields`
--

DROP TABLE IF EXISTS `quality_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_quality` int(11) NOT NULL,
  `question` varchar(150) NOT NULL,
  `weight` tinyint(2) NOT NULL DEFAULT 0,
  `num_order` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_quality` (`id_quality`),
  CONSTRAINT `quality_fields_ibfk_1` FOREIGN KEY (`id_quality`) REFERENCES `quality` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=228 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quality_user`
--

DROP TABLE IF EXISTS `quality_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_call_entry` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `datetime_calif` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_call_entry` (`id_call_entry`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `quality_user_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `quality_user_ibfk_3` FOREIGN KEY (`id_call_entry`) REFERENCES `call_entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7840 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quality_values`
--

DROP TABLE IF EXISTS `quality_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_call_entry` int(11) NOT NULL,
  `id_quality_fields` int(11) NOT NULL,
  `value` varchar(600) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_call_entry` (`id_call_entry`),
  KEY `id_quality_fields` (`id_quality_fields`),
  CONSTRAINT `quality_values_ibfk_2` FOREIGN KEY (`id_quality_fields`) REFERENCES `quality_fields` (`id`),
  CONSTRAINT `quality_values_ibfk_3` FOREIGN KEY (`id_call_entry`) REFERENCES `call_entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73511 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quality_was`
--

DROP TABLE IF EXISTS `quality_was`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality_was` (
  `id_wa_ses` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `eva_gen` tinyint(2) NOT NULL DEFAULT 0,
  `comentario` text NOT NULL,
  KEY `id_wa_ses` (`id_wa_ses`),
  CONSTRAINT `quality_was_ibfk_1` FOREIGN KEY (`id_wa_ses`) REFERENCES `whatsapp_session` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) DEFAULT NULL,
  `desc` varchar(250) NOT NULL,
  `name` varchar(7) NOT NULL,
  `show` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rep_abandono`
--

DROP TABLE IF EXISTS `rep_abandono`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_abandono` (
  `id` int(11) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `campaign` varchar(100) NOT NULL,
  `did` varchar(7) NOT NULL,
  `cid_name` varchar(32) NOT NULL,
  `cid_num` varchar(32) NOT NULL,
  `datetime_received` datetime DEFAULT NULL,
  `datetime_queued` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `queue_wait` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_wait` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL,
  `linkedid` varchar(32) NOT NULL,
  `c30` char(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `rep_abandono_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rep_acw`
--

DROP TABLE IF EXISTS `rep_acw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_acw` (
  `id` int(11) NOT NULL,
  `agente` varchar(150) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `fecha` date NOT NULL,
  `veces` smallint(5) unsigned NOT NULL,
  `total` smallint(5) unsigned NOT NULL,
  `promedio` smallint(5) unsigned NOT NULL,
  `largo` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`,`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rep_atendidas`
--

DROP TABLE IF EXISTS `rep_atendidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_atendidas` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `user` varchar(150) NOT NULL DEFAULT '',
  `id_campaign` int(11) DEFAULT NULL,
  `campaign` varchar(100) NOT NULL DEFAULT '',
  `did` varchar(7) NOT NULL,
  `cid_name` varchar(32) NOT NULL,
  `cid_num` varchar(32) NOT NULL,
  `datetime_received` datetime DEFAULT NULL,
  `datetime_queued` datetime DEFAULT NULL,
  `datetime_init` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `duration` smallint(5) unsigned NOT NULL DEFAULT 0,
  `linkedid` varchar(32) NOT NULL,
  `c30` char(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rep_inbound`
--

DROP TABLE IF EXISTS `rep_inbound`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_inbound` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `numero` varchar(25) NOT NULL,
  `linkedid` varchar(32) NOT NULL,
  `id_campana` varchar(11) DEFAULT NULL,
  `campana` varchar(150) NOT NULL,
  `did` varchar(7) NOT NULL,
  `extension` varchar(7) NOT NULL,
  `id_agente` varchar(11) DEFAULT NULL,
  `agente` varchar(150) NOT NULL,
  `espera` mediumint(8) unsigned NOT NULL,
  `duracion` mediumint(8) unsigned NOT NULL,
  `espera_total` mediumint(8) unsigned NOT NULL,
  `estatus` varchar(15) NOT NULL,
  `grabacion` varchar(100) NOT NULL,
  `calidad` varchar(3) NOT NULL,
  `calidad_comentario` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rep_outbound`
--

DROP TABLE IF EXISTS `rep_outbound`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_outbound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` varchar(11) DEFAULT NULL,
  `id_agente` varchar(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `numero` varchar(25) NOT NULL,
  `linkedid` varchar(32) NOT NULL,
  `campana` varchar(150) NOT NULL,
  `agente` varchar(150) NOT NULL,
  `did` varchar(7) NOT NULL,
  `extension` varchar(7) NOT NULL,
  `duracion` mediumint(8) unsigned NOT NULL,
  `hangup` varchar(10) NOT NULL,
  `grabacion` varchar(100) NOT NULL,
  `estatus` varchar(15) NOT NULL,
  `calidad` varchar(3) NOT NULL,
  `calidad_comentario` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17735123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rep_poragente`
--

DROP TABLE IF EXISTS `rep_poragente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_poragente` (
  `fecha` date NOT NULL,
  `id_user` int(11) NOT NULL,
  `agente` varchar(150) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `tipo` char(8) NOT NULL,
  `id_campaign` int(11) NOT NULL,
  `campana` varchar(150) NOT NULL,
  `exito` smallint(5) unsigned NOT NULL DEFAULT 0,
  `abandono` smallint(5) unsigned NOT NULL DEFAULT 0,
  `duracion` smallint(5) unsigned NOT NULL DEFAULT 0,
  `promedio` smallint(5) unsigned NOT NULL DEFAULT 0,
  `larga` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`fecha`,`id_user`,`tipo`,`id_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rep_sesion`
--

DROP TABLE IF EXISTS `rep_sesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_sesion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `login` datetime DEFAULT NULL,
  `primero` datetime DEFAULT NULL,
  `endescanso` int(10) unsigned NOT NULL DEFAULT 0,
  `enllamada` int(10) unsigned NOT NULL DEFAULT 0,
  `ultimo` datetime DEFAULT NULL,
  `logout` datetime DEFAULT NULL,
  `ensesion` int(10) unsigned NOT NULL DEFAULT 0,
  `ocupacion` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `pondescanso` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `disponibilidad` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user_fecha` (`id_user`,`fecha`),
  CONSTRAINT `rep_sesion_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=327898 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `repodin`
--

DROP TABLE IF EXISTS `repodin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repodin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `indexf` tinytext NOT NULL,
  `xfields` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_form` (`id_form`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `repodin_ibfk_1` FOREIGN KEY (`id_form`) REFERENCES `form` (`id`),
  CONSTRAINT `repodin_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `last` varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  `scheduled` datetime NOT NULL,
  `observations` varchar(250) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Activo',
  `parent` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL,
  `modificated_by` int(11) DEFAULT NULL,
  `modificated_when` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1330 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ses_ab`
--

DROP TABLE IF EXISTS `ses_ab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ses_ab` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  `uid` int(11) DEFAULT NULL,
  KEY `ses_ab_timestamp` (`timestamp`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_campaign`
--

DROP TABLE IF EXISTS `sms_campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `camp` varchar(50) NOT NULL,
  `phone` bigint(10) unsigned NOT NULL,
  `msg` varchar(250) DEFAULT NULL,
  `operator` varchar(100) NOT NULL,
  `datetime_init` datetime NOT NULL,
  `resp` varchar(200) NOT NULL,
  `json` text NOT NULL,
  `uid` varchar(40) NOT NULL,
  `status` varchar(10) NOT NULL,
  `status_desc` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9016 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_entry`
--

DROP TABLE IF EXISTS `sms_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `msg` varchar(250) NOT NULL,
  `operator` varchar(100) NOT NULL,
  `datetime_init` datetime NOT NULL,
  `resp` text NOT NULL,
  `json` text NOT NULL,
  `uid` varchar(40) NOT NULL,
  `status` varchar(10) NOT NULL,
  `status_desc` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `id_campaign` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_agent` (`id_user`),
  KEY `fk_sms_entry_campaign` (`id_campaign`),
  CONSTRAINT `fk_sms_entry_campaign` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `fk_sms_entry_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `sms_entry_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34266 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_template`
--

DROP TABLE IF EXISTS `sms_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valor` text NOT NULL,
  `name` varchar(100) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `sms_template_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tarifas`
--

DROP TABLE IF EXISTS `tarifas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarifas` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `uniqueid` varchar(32) NOT NULL,
  `cid_num` varchar(25) NOT NULL,
  `calldate` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `duration` smallint(5) unsigned NOT NULL,
  `tipo_red` varchar(15) NOT NULL,
  `minutos` smallint(5) unsigned NOT NULL,
  `costo` decimal(6,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `tarifas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `tarifas_ibfk_2` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `tarifas_ibfk_4` FOREIGN KEY (`id`) REFERENCES `call_entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_eventos`
--

DROP TABLE IF EXISTS `ticket_eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ticket` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL,
  `nivel` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `last` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_when` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_user_user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=954 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_data`
--

DROP TABLE IF EXISTS `user_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_data` (
  `id_user` int(11) NOT NULL,
  `id_catalog` int(11) NOT NULL,
  `val` varchar(255) NOT NULL,
  UNIQUE KEY `id_user_id_catalog` (`id_user`,`id_catalog`),
  KEY `id_catalog` (`id_catalog`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `user_data_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `user_data_ibfk_2` FOREIGN KEY (`id_catalog`) REFERENCES `catalogs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `user_full`
--

DROP TABLE IF EXISTS `user_full`;
/*!50001 DROP VIEW IF EXISTS `user_full`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `user_full` AS SELECT
 1 AS `id`,
  1 AS `email`,
  1 AS `name`,
  1 AS `last`,
  1 AS `active`,
  1 AS `perfil`,
  1 AS `exten`,
  1 AS `img`,
  1 AS `tel`,
  1 AS `tema`,
  1 AS `pagini`,
  1 AS `genero`,
  1 AS `campanas`,
  1 AS `pervl`,
  1 AS `perci`,
  1 AS `whatsapp`,
  1 AS `ctas_email`,
  1 AS `token`,
  1 AS `servask`,
  1 AS `passask` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `evento` datetime DEFAULT NULL,
  `detalle` tinytext DEFAULT NULL,
  `ip` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6186794 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_status`
--

DROP TABLE IF EXISTS `user_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_status` (
  `id_user` int(11) NOT NULL,
  `sec` varchar(50) NOT NULL,
  `val` tinyint(1) NOT NULL DEFAULT 0,
  `cuando` datetime DEFAULT NULL,
  UNIQUE KEY `id_user_sec` (`id_user`,`sec`),
  CONSTRAINT `user_status_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_trans`
--

DROP TABLE IF EXISTS `user_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_trans` (
  `id_user` int(11) NOT NULL,
  `grupo` varchar(30) NOT NULL,
  PRIMARY KEY (`id_user`,`grupo`),
  CONSTRAINT `fk_user_trans_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_trans_opts`
--

DROP TABLE IF EXISTS `user_trans_opts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_trans_opts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo` varchar(30) NOT NULL,
  `eti` varchar(50) NOT NULL,
  `des` text NOT NULL,
  `busy` varchar(11) NOT NULL,
  `trans` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `videocall_chans`
--

DROP TABLE IF EXISTS `videocall_chans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videocall_chans` (
  `id_user` int(11) NOT NULL,
  `sala` varchar(50) NOT NULL DEFAULT '',
  `cuando` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo` tinyint(1) NOT NULL DEFAULT 4,
  `estatus` tinyint(1) NOT NULL DEFAULT 0,
  `vcreg` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_user`),
  CONSTRAINT `videocall_chans_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `videocall_entry`
--

DROP TABLE IF EXISTS `videocall_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videocall_entry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `incdata` text NOT NULL,
  `callerid` varchar(32) NOT NULL DEFAULT '',
  `folio` varchar(16) NOT NULL DEFAULT '',
  `grabacion` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `priority` tinyint(1) NOT NULL DEFAULT 0,
  `datetime_init` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `duration` int(10) unsigned NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL DEFAULT 'En cola',
  `transfer` int(11) DEFAULT NULL,
  `datetime_entry_queue` datetime DEFAULT NULL,
  `duration_wait` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_agent` (`id_user`),
  KEY `datetime_init` (`datetime_init`),
  KEY `datetime_entry_queue` (`datetime_entry_queue`),
  CONSTRAINT `videocall_entry_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `videocall_serv`
--

DROP TABLE IF EXISTS `videocall_serv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videocall_serv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) DEFAULT NULL,
  `url` varchar(70) NOT NULL,
  `disp` tinyint(2) unsigned NOT NULL DEFAULT 16,
  `ocup` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `activ` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `videocall_serv_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vm_entry`
--

DROP TABLE IF EXISTS `vm_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vm_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) DEFAULT NULL,
  `extension` varchar(5) NOT NULL,
  `grabacion` varchar(100) NOT NULL,
  `datetime_received` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `duration` varchar(100) NOT NULL,
  `context` varchar(100) NOT NULL,
  `exten` varchar(100) NOT NULL,
  `rdnis` varchar(100) NOT NULL,
  `priority` varchar(100) NOT NULL,
  `callerchan` varchar(100) NOT NULL,
  `callerid` varchar(100) NOT NULL,
  `origtime` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `msg_id` varchar(100) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  CONSTRAINT `vm_entry_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1787 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_bot`
--

DROP TABLE IF EXISTS `whatsapp_bot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_wacta` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `label` varchar(30) NOT NULL,
  `intro` varchar(354) NOT NULL DEFAULT 'Hola',
  `bye` varchar(254) NOT NULL DEFAULT 'Hasta pronto',
  `out_of_time` varchar(254) NOT NULL DEFAULT 'Fuera de Horario',
  `wait_time` int(11) NOT NULL DEFAULT 1,
  `ini_script` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_wacta` (`id_wacta`),
  KEY `created_by` (`created_by`),
  KEY `ini_script` (`ini_script`),
  CONSTRAINT `whatsapp_bot_ibfk_1` FOREIGN KEY (`id_wacta`) REFERENCES `whatsapp_cuentas` (`id`),
  CONSTRAINT `whatsapp_bot_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `whatsapp_bot_ibfk_3` FOREIGN KEY (`ini_script`) REFERENCES `whatsapp_bot_script` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_bot_history`
--

DROP TABLE IF EXISTS `whatsapp_bot_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_bot_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) NOT NULL,
  `paso` varchar(15) NOT NULL,
  `hora` datetime NOT NULL DEFAULT current_timestamp(),
  `xtra` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_session` (`id_session`),
  CONSTRAINT `whatsapp_bot_history_ibfk_1` FOREIGN KEY (`id_session`) REFERENCES `whatsapp_session` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_bot_hour`
--

DROP TABLE IF EXISTS `whatsapp_bot_hour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_bot_hour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `whatsapp_bot_id` int(11) NOT NULL,
  `dia` char(1) NOT NULL,
  `inicio` time DEFAULT NULL,
  `fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `whatsapp_bot_id_dia` (`whatsapp_bot_id`,`dia`),
  CONSTRAINT `whatsapp_bot_hour_ibfk_1` FOREIGN KEY (`whatsapp_bot_id`) REFERENCES `whatsapp_bot` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_bot_op`
--

DROP TABLE IF EXISTS `whatsapp_bot_op`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_bot_op` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_wacta` int(11) NOT NULL,
  `id_bot` int(11) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `label` text NOT NULL,
  `action` tinyint(1) NOT NULL DEFAULT 1,
  `option` varchar(2) NOT NULL,
  `redirect` varchar(15) NOT NULL DEFAULT '0',
  `id_script` int(11) NOT NULL DEFAULT 0,
  `routeLvl` varchar(60) NOT NULL DEFAULT '',
  `routeId` varchar(150) NOT NULL DEFAULT '',
  `routeName` varchar(30) NOT NULL DEFAULT '',
  `depth` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_wacta` (`id_wacta`),
  KEY `id_bot` (`id_bot`),
  CONSTRAINT `whatsapp_bot_op_ibfk_1` FOREIGN KEY (`id_wacta`) REFERENCES `whatsapp_cuentas` (`id`),
  CONSTRAINT `whatsapp_bot_op_ibfk_2` FOREIGN KEY (`id_bot`) REFERENCES `whatsapp_bot` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_bot_scr_steps`
--

DROP TABLE IF EXISTS `whatsapp_bot_scr_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_bot_scr_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_whatsapp_bot_script` int(11) NOT NULL,
  `paso` varchar(15) NOT NULL DEFAULT 'variable',
  `camp` varchar(254) NOT NULL,
  `varb` varchar(64) NOT NULL,
  `tipo` varchar(64) NOT NULL,
  `modi` text NOT NULL,
  `cond` varchar(254) NOT NULL,
  `orden` tinyint(2) unsigned NOT NULL DEFAULT 1,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `lastupd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `lastusr` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_whatsapp_bot_script` (`id_whatsapp_bot_script`),
  KEY `lastusr` (`lastusr`),
  CONSTRAINT `whatsapp_bot_scr_steps_ibfk_2` FOREIGN KEY (`lastusr`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_bot_script`
--

DROP TABLE IF EXISTS `whatsapp_bot_script`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_bot_script` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `siespera` varchar(100) NOT NULL,
  `sibien` varchar(15) NOT NULL,
  `simal` varchar(15) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_campaign` (`id_campaign`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `whatsapp_bot_script_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `whatsapp_bot_script_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_cont_data`
--

DROP TABLE IF EXISTS `whatsapp_cont_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_cont_data` (
  `id_contact` int(11) NOT NULL,
  `permanent` text NOT NULL,
  `temporal` text NOT NULL,
  `secure` text NOT NULL,
  `file` text NOT NULL,
  PRIMARY KEY (`id_contact`),
  CONSTRAINT `whatsapp_cont_data_ibfk_1` FOREIGN KEY (`id_contact`) REFERENCES `whatsapp_contact` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_contact`
--

DROP TABLE IF EXISTS `whatsapp_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_wacta` int(11) DEFAULT NULL,
  `account` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nick` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `datetime_register` datetime NOT NULL,
  `last_asigned_to` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_wacta_account` (`id_wacta`,`account`),
  KEY `last_asigned_to` (`last_asigned_to`),
  KEY `id_wacta` (`id_wacta`),
  CONSTRAINT `whatsapp_contact_ibfk_1` FOREIGN KEY (`last_asigned_to`) REFERENCES `user` (`id`),
  CONSTRAINT `whatsapp_contact_ibfk_2` FOREIGN KEY (`id_wacta`) REFERENCES `whatsapp_cuentas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4807 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_cuentas`
--

DROP TABLE IF EXISTS `whatsapp_cuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) NOT NULL,
  `cuenta` varchar(20) NOT NULL,
  `idchatapi` varchar(15) NOT NULL,
  `token` varchar(128) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `almacen` varchar(100) NOT NULL DEFAULT 'localhost',
  `nobotlog` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `alta_quien` int(11) NOT NULL,
  `alta_cuando` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cuenta` (`cuenta`),
  KEY `id_campaign` (`id_campaign`),
  KEY `alta_quien` (`alta_quien`),
  CONSTRAINT `whatsapp_cuentas_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`),
  CONSTRAINT `whatsapp_cuentas_ibfk_2` FOREIGN KEY (`alta_quien`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_entry`
--

DROP TABLE IF EXISTS `whatsapp_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_session` int(11) DEFAULT NULL,
  `id_wacta` int(11) DEFAULT NULL,
  `json` text NOT NULL,
  `json_bot` text NOT NULL,
  `message` text NOT NULL,
  `datetime_received` datetime NOT NULL,
  `type` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL,
  `watype` varchar(30) NOT NULL,
  `caption` varchar(100) NOT NULL,
  `mimetype` varchar(30) NOT NULL,
  `size` int(11) unsigned NOT NULL,
  `duration` smallint(6) unsigned NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `thumb` mediumtext NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_contact` (`id_contact`),
  KEY `id_user` (`id_user`),
  KEY `id_session` (`id_session`),
  KEY `id_wacta` (`id_wacta`),
  CONSTRAINT `whatsapp_entry_ibfk_1` FOREIGN KEY (`id_contact`) REFERENCES `whatsapp_contact` (`id`),
  CONSTRAINT `whatsapp_entry_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `whatsapp_entry_ibfk_3` FOREIGN KEY (`id_session`) REFERENCES `whatsapp_session` (`id`),
  CONSTRAINT `whatsapp_entry_ibfk_4` FOREIGN KEY (`id_wacta`) REFERENCES `whatsapp_cuentas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4880 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_gateway`
--

DROP TABLE IF EXISTS `whatsapp_gateway`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_gateway` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queviene` mediumtext NOT NULL,
  `dest_tel` varchar(20) NOT NULL,
  `dest_ip` varchar(20) NOT NULL,
  `dest_resp` varchar(255) NOT NULL,
  `hora` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_hooks`
--

DROP TABLE IF EXISTS `whatsapp_hooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_hooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `json` mediumtext NOT NULL,
  `datetime_received` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4917 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_message_rating`
--

DROP TABLE IF EXISTS `whatsapp_message_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_message_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_whatsapp_entry` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_whatsapp_entry` (`id_whatsapp_entry`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `whatsapp_message_rating_ibfk_1` FOREIGN KEY (`id_whatsapp_entry`) REFERENCES `whatsapp_entry` (`id`),
  CONSTRAINT `whatsapp_message_rating_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_quality_user`
--

DROP TABLE IF EXISTS `whatsapp_quality_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_quality_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `datetime_calif` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_session` (`id_session`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `whatsapp_quality_user_ibfk_1` FOREIGN KEY (`id_session`) REFERENCES `whatsapp_session` (`id`),
  CONSTRAINT `whatsapp_quality_user_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_quality_values`
--

DROP TABLE IF EXISTS `whatsapp_quality_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_quality_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_session` int(11) NOT NULL,
  `id_quality_fields` int(11) NOT NULL,
  `value` varchar(600) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_session` (`id_session`),
  KEY `id_quality_fields` (`id_quality_fields`),
  CONSTRAINT `whatsapp_quality_values_ibfk_1` FOREIGN KEY (`id_session`) REFERENCES `whatsapp_session` (`id`),
  CONSTRAINT `whatsapp_quality_values_ibfk_2` FOREIGN KEY (`id_quality_fields`) REFERENCES `quality_fields` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_rate`
--

DROP TABLE IF EXISTS `whatsapp_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_wacta` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `comment` varchar(254) NOT NULL,
  `extra` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_when` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_wacta` (`id_wacta`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_rate_rctv`
--

DROP TABLE IF EXISTS `whatsapp_rate_rctv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_rate_rctv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_wr` int(11) NOT NULL,
  `tipo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 apagado, 1 numerico, 2 texto',
  `reporte` tinyint(1) NOT NULL DEFAULT 0,
  `reactivo` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_wr` (`id_wr`),
  CONSTRAINT `whatsapp_rate_rctv_ibfk_3` FOREIGN KEY (`id_wr`) REFERENCES `whatsapp_rate` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_serve`
--

DROP TABLE IF EXISTS `whatsapp_serve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_serve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(15) NOT NULL,
  `idchatapi` varchar(15) NOT NULL,
  `token` varchar(128) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `detalle` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `whatsapp_session`
--

DROP TABLE IF EXISTS `whatsapp_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_wacta` int(11) DEFAULT NULL,
  `datetime_received` datetime NOT NULL,
  `datetime_assigned` datetime NOT NULL,
  `datetime_start` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `duration_wait` int(10) unsigned NOT NULL DEFAULT 0,
  `duration` int(10) unsigned NOT NULL DEFAULT 0,
  `type` varchar(15) NOT NULL,
  `message` mediumtext NOT NULL,
  `id_user_transfer` int(11) DEFAULT NULL,
  `subtipo` varchar(10) NOT NULL,
  `paso` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_contact_datetime_assigned` (`id_contact`,`datetime_assigned`),
  KEY `id_contact` (`id_contact`),
  KEY `id_user` (`id_user`),
  KEY `id_wacta` (`id_wacta`),
  CONSTRAINT `whatsapp_session_ibfk_1` FOREIGN KEY (`id_contact`) REFERENCES `whatsapp_contact` (`id`),
  CONSTRAINT `whatsapp_session_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  CONSTRAINT `whatsapp_session_ibfk_3` FOREIGN KEY (`id_wacta`) REFERENCES `whatsapp_cuentas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=526 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'assertive'
--

--
-- Dumping routines for database 'assertive'
--

--
-- Final view structure for view `user_full`
--

/*!50001 DROP VIEW IF EXISTS `user_full`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`aldo`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `user_full` AS select `u`.`id` AS `id`,`u`.`user` AS `email`,`u`.`name` AS `name`,`u`.`last` AS `last`,`u`.`active` AS `active`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'perfil',`ud`.`val`,NULL) separator ','),''),'agente') AS `perfil`,ifnull(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'userask',`ud`.`val`,NULL) separator ','),'') AS `exten`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'avatar',`ud`.`val`,NULL) separator ','),''),'user_icon.png') AS `img`,ifnull(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'telefono',`ud`.`val`,NULL) separator ','),'') AS `tel`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'theme',`ud`.`val`,NULL) separator ','),''),'default') AS `tema`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'pagini',`ud`.`val`,NULL) separator ','),''),'inicio') AS `pagini`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'genero',`ud`.`val`,NULL) separator ','),''),'N') AS `genero`,ifnull(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'campanas',`ud`.`val`,NULL) separator ','),'') AS `campanas`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'pervidllam',`ud`.`val`,NULL) separator ','),''),'0,0,0,0,0') AS `pervl`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'chatinterno',`ud`.`val`,NULL) separator ','),''),'0,0,0,0,0') AS `perci`,ifnull(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'whatsapp',`ud`.`val`,NULL) separator ','),'') AS `whatsapp`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'email',`ud`.`val`,NULL) separator ','),''),'') AS `ctas_email`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'token',`ud`.`val`,NULL) separator ','),''),'') AS `token`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'servask',`ud`.`val`,NULL) separator ','),''),'ccphonex.assertivebusiness.com.mx') AS `servask`,ifnull(nullif(group_concat(if(`c`.`cat` = 'userData' and `c`.`val` = 'passask',`ud`.`val`,NULL) separator ','),''),'ph0n3x1') AS `passask` from ((`user` `u` left join `user_data` `ud` on(`ud`.`id_user` = `u`.`id`)) left join `catalogs` `c` on(`c`.`id` = `ud`.`id_catalog`)) group by `u`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-30 19:30:06
