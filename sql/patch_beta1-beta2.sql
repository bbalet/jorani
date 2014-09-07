-- -------------------------------------------------------------------
-- Database Patch 
--
-- This patch migrates your database from beta1 to beta2
-- -------------------------------------------------------------------

-- Bufixing (missing UTF-8 config)
-- New features :
--   * History tables
--   * Time management

CREATE TABLE IF NOT EXISTS `users_history` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `organization` int(11) DEFAULT NULL,
  `contract` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `datehired` date DEFAULT NULL COMMENT 'Date hired / Started',
  `identifier` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Internal/company identifier',
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contracts_history` (
  `id` int(11) NOT NULL ,
  `name` varchar(128) NOT NULL,
  `startentdate` varchar(5) NOT NULL,
  `endentdate` varchar(5) NOT NULL,
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `entitleddays_history` (
  `id` int(11) NOT NULL,
  `contract` int(11) NOT NULL,
  `employee` int(11) NOT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `type` int(11) NOT NULL,
  `days` decimal(10,2) NOT NULL,
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `leaves_history` (
  `id` int(11) NOT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `employee` int(11) DEFAULT NULL,
  `cause` text,
  `startdatetype` varchar(12) DEFAULT NULL,
  `enddatetype` varchar(12) DEFAULT NULL,
  `duration` decimal(10,2) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `organization_history` (
  `id` int(11) NOT NULL,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `overtime_history` (
  `id` int(11) NOT NULL,
  `employee` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` decimal(10,2) NOT NULL,
  `cause` text NOT NULL,
  `status` int(11) NOT NULL,
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `positions_history` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `types_history` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `activities_history` (
  `id` int(11) NOT NULL COMMENT 'Activity unique identifier',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of the activity (short)',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Full description of the activity',
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `activities_employee_history` (
  `employee` int(11) NOT NULL COMMENT 'employee identifier',
  `activity` int(11) NOT NULL COMMENT 'activity identifier',
  `modification_id` int(11) NOT NULL AUTO_INCREMENT,
  `modification_type` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`modification_id`),
  KEY `modified_by` (`modified_by`),
  KEY `modified_date` (`modified_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- ------------------------------------------------------------------------------------------
-- Update of the model for Time management module

  ALTER TABLE `contracts` ADD `weekly_duration` INT NOT NULL COMMENT 'Approximate duration of work per week and in minutes' ;

CREATE TABLE IF NOT EXISTS `time` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier of the time declaration',
  `datetime` datetime NOT NULL COMMENT 'date start',
  `duration` int(11) NOT NULL COMMENT 'duration in minutes',
  `activity` int(11) NOT NULL COMMENT 'activity identifier',
  `employee` int(11) NOT NULL COMMENT 'employee identifier',
  PRIMARY KEY (`id`),
  KEY `activity` (`activity`,`employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Time spent by employees' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Activity unique identifier',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of the activity (short)',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Full description of the activity',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of activities' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `activities_employee` (
  `employee` int(11) NOT NULL COMMENT 'employee identifier',
  `activity` int(11) NOT NULL COMMENT 'activity identifier',
  KEY `employee` (`employee`,`activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ------------------------------------------------------------------------------------------
-- Fixing missing UTF-8 configuration
ALTER TABLE  `actions` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `contracts` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `dayoffs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `entitleddays` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `leaves` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `organization` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `overtime` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `positions` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `roles` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `settings` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `status` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `types` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `users` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
