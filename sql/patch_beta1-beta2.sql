---------------------------------------------------------------------
-- Database Patch 
--
-- This patch migrates your database from beta1 to beta2
---------------------------------------------------------------------

-- New features :
--   * History
--   * Time management

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
