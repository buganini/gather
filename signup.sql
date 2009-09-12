DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `eventid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `expose` tinyint(1) NOT NULL,
  `beg_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `desc` longtext NOT NULL,
  PRIMARY KEY  (`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fields`;
CREATE TABLE `fields` (
  `eventid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `default` text NOT NULL,
  `valid` varchar(255) NOT NULL,
  `pattern` varchar(255) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `key` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `fieldid` (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `eventid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `optionid` int(11) NOT NULL,
  `caption` varchar(255) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `fieldid` (`fieldid`),
  KEY `optionid` (`optionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pool`;
CREATE TABLE `pool` (
  `eventid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `recordid` int(11) NOT NULL,
  `value` text NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `recordid` (`recordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `records`;
CREATE TABLE `records` (
  `eventid` int(11) NOT NULL,
  `recordid` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  `ip` varchar(31) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `recordid` (`recordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tpl_events`;
CREATE TABLE `tpl_events` (
  `eventid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `expose` tinyint(1) NOT NULL,
  `desc` text NOT NULL,
  PRIMARY KEY  (`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tpl_fields`;
CREATE TABLE `tpl_fields` (
  `eventid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `default` text NOT NULL,
  `valid` varchar(255) NOT NULL,
  `pattern` varchar(255) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `key` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `fieldid` (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tpl_options`;
CREATE TABLE `tpl_options` (
  `eventid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  `optionid` int(11) NOT NULL,
  `caption` varchar(255) NOT NULL,
  KEY `eventid` (`eventid`),
  KEY `fieldid` (`fieldid`),
  KEY `optionid` (`optionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
