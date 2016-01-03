-- ---------------------------------------------------
-- Jorani Schema definition
-- 
-- @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2016 Benjamin BALET

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Functions
--
DROP FUNCTION IF EXISTS `GetAncestry`;
DELIMITER $$
CREATE FUNCTION `GetAncestry`(GivenID INT) RETURNS varchar(1024) CHARSET utf8
    NOT DETERMINISTIC
    READS SQL DATA
    SQL SECURITY INVOKER
BEGIN
    DECLARE rv VARCHAR(1024);
    DECLARE cm CHAR(1);
    DECLARE ch INT;

    SET rv = '';
    SET cm = '';
    SET ch = GivenID;
    WHILE ch > 0 DO
        SELECT IFNULL(parent_id,-1) INTO ch FROM
        (SELECT parent_id FROM organization WHERE id = ch) A;
        IF ch > 0 THEN
            SET rv = CONCAT(rv,cm,ch);
            SET cm = ',';
        END IF;
    END WHILE;
    RETURN rv;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `GetFamilyTree`;
DELIMITER $$
CREATE FUNCTION `GetFamilyTree`(`GivenID` INT) RETURNS varchar(1024) CHARSET utf8
    NOT DETERMINISTIC
    READS SQL DATA
    SQL SECURITY INVOKER
BEGIN

    DECLARE rv,q,queue,queue_children VARCHAR(1024);
    DECLARE queue_length,front_id,pos INT;

    SET rv = '';
    SET queue = GivenID;
    SET queue_length = 1;

    WHILE queue_length > 0 DO
        SET front_id = FORMAT(queue,0);
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;

        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM organization WHERE parent_id = front_id) A;

        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;
    RETURN rv;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `GetParentIDByID`;
DELIMITER $$
CREATE FUNCTION `GetParentIDByID`(GivenID INT) RETURNS int(11)
    NOT DETERMINISTIC
    READS SQL DATA
    SQL SECURITY INVOKER
BEGIN
    DECLARE rv INT;

    SELECT IFNULL(parent_id,-1) INTO rv FROM
    (SELECT parent_id FROM organization WHERE id = GivenID) A;
    RETURN rv;
END$$
DELIMITER ;

--
-- Structure of table `actions`
--
CREATE TABLE IF NOT EXISTS `actions` (
  `name` varchar(45) CHARACTER SET utf8 NOT NULL,
  `mask` bit(16) NOT NULL,
  `Description` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Content of table `actions`
--
INSERT INTO `actions` (`name`, `mask`, `Description`) VALUES
('accept_requests', b'0011000100110010', 'Accept the request of my team members'),
('admin_menu', b'0011000100110010', 'View admin menu'),
('change_password', b'0011000100110010', 'Change password'),
('create_leaves', b'0011000100110010', 'Create a new user leave request'),
('create_user', b'0011000100110010', 'Create a new user'),
('delete_user', b'0011000100110010', 'Delete an existing user'),
('edit_leaves', b'0011000100110010', 'Edit a leave request'),
('edit_settings', b'0011000100110010', 'Edit application settings'),
('edit_user', b'0011000100110010', 'Edit a user'),
('export_leaves', b'0011000100110010', 'Export the list of leave requests into an Excel file'),
('export_user', b'0011000100110010', 'Export the list of users into an Excel file'),
('hr_menu', b'0011000100110010', 'View HR menu'),
('individual_calendar', b'0011000100110010', 'View my leaves in a calendar'),
('list_leaves', b'0011000100110010', 'List my leave requests'),
('list_requests', b'0011000100110010', 'List the request of my team members'),
('list_users', b'0011000100110010', 'List users'),
('reject_requests', b'0011000100110010', 'Reject the request of my team members'),
('reset_password', b'0011000100110010', 'Modifiy the password of another user'),
('team_calendar', b'0011000100110010', 'View the leaves of my team in a calendar'),
('update_user', b'0011000100110010', 'Update a user'),
('view_leaves', b'0011000100110010', 'View the details of a leave request'),
('view_user', b'0011000100110010', 'View user''s details');

--
-- Structure of table `activities`
--
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Activity unique identifier',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of the activity (short)',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Full description of the activity',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of activities' AUTO_INCREMENT=1 ;

--
-- Structure of table `activities_employee`
--
CREATE TABLE IF NOT EXISTS `activities_employee` (
  `employee` int(11) NOT NULL COMMENT 'employee identifier',
  `activity` int(11) NOT NULL COMMENT 'activity identifier',
  KEY `employee` (`employee`,`activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Structure of table `contracts`
--
CREATE TABLE IF NOT EXISTS `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `startentdate` varchar(5) CHARACTER SET utf8 NOT NULL,
  `endentdate` varchar(5) CHARACTER SET utf8 NOT NULL,
  `weekly_duration` int(11) DEFAULT NULL COMMENT 'Approximate duration of work per week (in minutes)',
  `daily_duration` int(11) DEFAULT NULL COMMENT 'Approximate duration of work per day and (in minutes)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Structure of table `dayoffs`
--
CREATE TABLE IF NOT EXISTS `dayoffs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract` int(11) NOT NULL COMMENT 'Contract id',
  `date` date NOT NULL COMMENT 'Date of the day off',
  `type` int(11) NOT NULL COMMENT 'Half or full day',
  `title` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'Description of day off',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `contract` (`contract`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Structure of table `entitleddays`
--
CREATE TABLE IF NOT EXISTS `entitleddays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract` int(11) DEFAULT NULL,
  `employee` int(11) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `type` int(11) NOT NULL,
  `days` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL COMMENT 'Description of a credit / debit',
  PRIMARY KEY (`id`),
  KEY `contract` (`contract`),
  KEY `employee` (`employee`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Structure of table `leaves`
--
CREATE TABLE IF NOT EXISTS `leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `employee` int(11) DEFAULT NULL,
  `cause` text CHARACTER SET utf8,
  `startdatetype` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
  `enddatetype` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
  `duration` decimal(10,2) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `employee` (`employee`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Structure of table `organization`
--
CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `supervisor` INT NULL DEFAULT NULL COMMENT 'this user will receive a copy of accepted and rejected leave requests',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Content of table `organization`
--
INSERT INTO `organization` (`id`, `name`, `parent_id`) VALUES
(0, 'LMS root', -1);

--
-- Structure of table `overtime`
--
CREATE TABLE IF NOT EXISTS `overtime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` decimal(10,2) NOT NULL,
  `cause` text CHARACTER SET utf8 NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `employee` (`employee`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Structure of table `positions`
--
CREATE TABLE IF NOT EXISTS `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Content of table `positions`
--
INSERT INTO `positions` (`id`, `name`, `description`) VALUES
(1, 'Employee', 'Employee.');

--
-- Structure of table `roles`
--
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Content of table `roles`
--
INSERT INTO `roles` (`id`, `name`) VALUES
(2, 'user'),
(8, 'HR admin');

--
-- Structure of table `settings`
--
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `key` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `value` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `type` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Structure of table `status`
--
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL,
  `name` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Content of table `status`
--
INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Planned'),
(2, 'Requested'),
(3, 'Accepted'),
(4, 'Rejected');

--
-- Structure of table `tasks`
--
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier of the task',
  `employee` int(11) NOT NULL COMMENT 'assigned to',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Title (short) of the task',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Long description of the task',
  `due_date` date NOT NULL COMMENT 'Date when the task is due',
  `completed_date` date NOT NULL COMMENT 'Date when the task is finnished',
  `status` int(11) NOT NULL COMMENT 'status are the same than the leaves but a task is always editable',
  `manager_comment` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'comments are added at the top of the field',
  `employee_comment` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'comments are added at the top of the field',
  PRIMARY KEY (`id`),
  KEY `employee` (`employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Structure of table `time`
--
CREATE TABLE IF NOT EXISTS `time` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier of the time declaration',
  `datetime` datetime NOT NULL COMMENT 'date start',
  `duration` int(11) NOT NULL COMMENT 'duration in minutes',
  `activity` int(11) NOT NULL COMMENT 'activity identifier',
  `employee` int(11) NOT NULL COMMENT 'employee identifier',
  PRIMARY KEY (`id`),
  KEY `activity` (`activity`,`employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Time spent by employees' AUTO_INCREMENT=1 ;

--
-- Structure of table `types`
--
CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Content of table `types`
--
INSERT INTO `types` (`id`, `name`) VALUES
(0, 'compensate'),
(1, 'paid leave'),
(2, 'maternity leave'),
(3, 'paternity leave'),
(4, 'special leave'),
(5, 'Sick leave');

--
-- Structure of table `users`
--
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `login` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `organization` int(11) DEFAULT NULL,
  `contract` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `datehired` date DEFAULT NULL COMMENT 'Date hired / Started',
  `identifier` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT 'Internal/company identifier',
  `language` varchar(2) CHARACTER SET utf8 NOT NULL DEFAULT 'en',
  `ldap_path` varchar(1024) DEFAULT NULL COMMENT 'LDAP Path for complex authentication schemes',
  `active` bool DEFAULT TRUE COMMENT 'Is user active',
  `timezone` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Timezone of user',
  `calendar` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'External Calendar address',
  PRIMARY KEY (`id`),
  KEY `manager` (`manager`),
  KEY `organization` (`organization`),
  KEY `contract` (`contract`),
  KEY `position` (`position`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Content of table `users`
--
INSERT INTO `users` (`id`, `firstname`, `lastname`, `login`, `email`, `password`, `role`, `manager`, `country`, `organization`, `contract`, `position`, `datehired`, `identifier`, `language`) VALUES
(1, 'Benjamin', 'BALET', 'bbalet', 'benjamin.balet@gmail.com', '$2a$08$LeUbaGFqJjLSAN7to9URsuHB41zcmsMBgBhpZuFp2y2OTxtVcMQ.C', 8, 1, NULL, 0, NULL, 1, '2013-10-28', 'PNC0025', 'en');

--
-- Structure of table `delegations`
--
CREATE TABLE IF NOT EXISTS `delegations` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of delegation',
  `manager_id` int(11) NOT NULL COMMENT 'Manager wanting to delegate',
  `delegate_id` int(11) NOT NULL COMMENT 'Employee having the delegation',
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Delegation of approval' AUTO_INCREMENT=1 ;

-- Tables for OAuth2 server
CREATE TABLE oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) NOT NULL, redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id));
CREATE TABLE oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));
CREATE TABLE oauth_authorization_codes (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code));
CREATE TABLE oauth_refresh_tokens (refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token));
CREATE TABLE oauth_users (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username));
CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN);
CREATE TABLE oauth_jwt (client_id VARCHAR(80) NOT NULL, subject VARCHAR(80), public_key VARCHAR(2000), CONSTRAINT jwt_client_id_pk PRIMARY KEY (client_id));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
