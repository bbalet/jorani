-- ---------------------------------------------------
-- Jorani Schema definition
--
-- @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2018 Benjamin BALET

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS jorani CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jorani;

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

DROP FUNCTION IF EXISTS `GetInitials`;
DELIMITER $$
CREATE FUNCTION `GetInitials`(str text, expr text) RETURNS text CHARSET utf8
    NOT DETERMINISTIC
    READS SQL DATA
    SQL SECURITY INVOKER
BEGIN
    declare result text default '';
    declare buffer text default '';
    declare i int default 1;
    if(str is null) then
        return null;
    end if;
    set buffer = trim(str);
    while i <= length(buffer) do
        if substr(buffer, i, 1) regexp expr then
            set result = concat( result, substr( buffer, i, 1 ));
            set i = i + 1;
            while i <= length( buffer ) and substr(buffer, i, 1) regexp expr do
                set i = i + 1;
            end while;
            while i <= length( buffer ) and substr(buffer, i, 1) not regexp expr do
                set i = i + 1;
            end while;
        else
            set i = i + 1;
        end if;
    end while;
    return result;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS `GetAcronym`;
DELIMITER $$
CREATE FUNCTION `GetAcronym`(str text) RETURNS text CHARSET utf8
    NOT DETERMINISTIC
    READS SQL DATA
    SQL SECURITY INVOKER
BEGIN
    declare result text default '';
    set result = GetInitials( str, '[[:alnum:]]' );
    return result;
END$$
DELIMITER ;

--
-- Structure of table `actions`
--
CREATE TABLE IF NOT EXISTS `actions` (
  `name` varchar(45) CHARACTER SET utf8mb4 NOT NULL,
  `mask` bit(16) NOT NULL,
  `Description` text CHARACTER SET utf8mb4 NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of possible actions';

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
-- Structure of table `contracts`
--
CREATE TABLE IF NOT EXISTS `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
  `startentdate` varchar(5) CHARACTER SET utf8mb4 NOT NULL,
  `endentdate` varchar(5) CHARACTER SET utf8mb4 NOT NULL,
  `weekly_duration` int(11) DEFAULT NULL COMMENT 'Approximate duration of work per week (in minutes)',
  `daily_duration` int(11) DEFAULT NULL COMMENT 'Approximate duration of work per day and (in minutes)',
  `default_leave_type` INT NULL DEFAULT NULL COMMENT 'default leave type for the contract (overwrite default type set in config file).',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of contracts' AUTO_INCREMENT=1;

INSERT INTO `contracts` (`id`, `name`, `startentdate`, `endentdate`, `weekly_duration`, `daily_duration`, `default_leave_type`) VALUES
(1, 'Global', '01/01', '12/31', 2400, 480, 1);

--
-- Structure of table `dayoffs`
--
CREATE TABLE IF NOT EXISTS `dayoffs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract` int(11) NOT NULL COMMENT 'Contract id',
  `date` date NOT NULL COMMENT 'Date of the day off',
  `type` int(11) NOT NULL COMMENT 'Half or full day',
  `title` varchar(128) CHARACTER SET utf8mb4 NOT NULL COMMENT 'Description of day off',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `contract` (`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of non working days' AUTO_INCREMENT=1;

--
-- Structure of table `entitleddays`
--
CREATE TABLE IF NOT EXISTS `entitleddays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract` int(11) DEFAULT NULL,
  `employee` int(11) DEFAULT NULL,
  `overtime` int(11) DEFAULT NULL COMMENT 'Link to an overtime request',
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `type` int(11) NOT NULL,
  `days` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL COMMENT 'Description of a credit / debit',
  PRIMARY KEY (`id`),
  KEY `contract` (`contract`),
  KEY `employee` (`employee`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Add or sub entitlement for employees or contracts' AUTO_INCREMENT=1;

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
  `startdatetype` varchar(12) CHARACTER SET utf8mb4 DEFAULT NULL,
  `enddatetype` varchar(12) CHARACTER SET utf8mb4 DEFAULT NULL,
  `duration` decimal(10,3) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `comments` TEXT NULL DEFAULT NULL COMMENT 'Comments on leave request',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `employee` (`employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Leave requests' AUTO_INCREMENT=1 ;

--
-- Structure of table `organization`
--
CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) CHARACTER SET utf8mb4 DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `supervisor` INT NULL DEFAULT NULL COMMENT 'this user will receive a copy of accepted and rejected leave requests',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tree of the organization' AUTO_INCREMENT=1 ;

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
  `duration` decimal(10,3) NOT NULL,
  `cause` text CHARACTER SET utf8mb4 NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `employee` (`employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Overtime worked (extra time)' AUTO_INCREMENT=1 ;

--
-- Structure of table `positions`
--
CREATE TABLE IF NOT EXISTS `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Position (job position) in the organization' AUTO_INCREMENT=2 ;

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
  `name` varchar(45) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Roles in the application (system table)';

--
-- Content of table `roles`
--
INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'user'),
(8, 'HR admin');

--
-- Structure of table `status`
--
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL,
  `name` varchar(45) CHARACTER SET utf8mb4 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Status of the Leave Request (system table)';

--
-- Content of table `status`
--
INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Planned'),
(2, 'Requested'),
(3, 'Accepted'),
(4, 'Rejected'),
(5, 'Cancellation'),
(6, 'Canceled');

--
-- Structure of table `types`
--
CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
  `acronym` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Acronym of leave type',
  `deduct_days_off` BOOL NOT NULL DEFAULT 0 COMMENT 'Deduct days off when computing the balance of the leave type.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of leave types (LoV table)' AUTO_INCREMENT=6 ;

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
  `firstname` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'First name',
  `lastname` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Last name',
  `login` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Identfier used to login (can be an email address)',
  `email` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Email address',
  `password` varchar(512) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Password encrypted with BCRYPT or a similar method',
  `role` int(11) DEFAULT NULL COMMENT 'Role of the employee',
  `manager` int(11) DEFAULT NULL COMMENT 'Employee validating the requests of the employee',
  `country` int(11) DEFAULT NULL COMMENT 'Country code (for later use)',
  `organization` int(11) DEFAULT 0 COMMENT 'Entity where the employee has a position',
  `contract` int(11) DEFAULT NULL COMMENT 'Contract of the employee',
  `position` int(11) DEFAULT NULL COMMENT 'Position of the employee',
  `datehired` date DEFAULT NULL COMMENT 'Date hired / Started',
  `identifier` varchar(64) CHARACTER SET utf8mb4 NOT NULL COMMENT 'Internal/company identifier',
  `language` varchar(5) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'en' COMMENT 'Language ISO code',
  `ldap_path` varchar(1024) DEFAULT NULL COMMENT 'LDAP Path for complex authentication schemes',
  `active` bool DEFAULT TRUE COMMENT 'Is user active',
  `timezone` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Timezone of user',
  `calendar` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'External Calendar address',
  `random_hash` varchar(24) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Obfuscate public URLs',
  PRIMARY KEY (`id`),
  KEY `manager` (`manager`),
  KEY `organization` (`organization`),
  KEY `contract` (`contract`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of employees / users having access to Jorani' AUTO_INCREMENT=2 ;

--
-- Content of table `users`
--
INSERT INTO `users` (`id`, `firstname`, `lastname`, `login`, `email`, `password`, `role`, `manager`, `country`, `organization`, `contract`, `position`, `datehired`, `identifier`, `language`) VALUES
(1, 'Benjamin', 'BALET', 'bbalet', 'benjamin.balet@gmail.com', '$2a$08$LeUbaGFqJjLSAN7to9URsuHB41zcmsMBgBhpZuFp2y2OTxtVcMQ.C', 8, 1, NULL, 0, 1, 1, '2013-10-28', 'PNC0025', 'en');

--
-- Structure of table `delegations`
--
CREATE TABLE IF NOT EXISTS `delegations` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of delegation',
  `manager_id` int(11) NOT NULL COMMENT 'Manager wanting to delegate',
  `delegate_id` int(11) NOT NULL COMMENT 'Employee having the delegation',
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Delegation of approval' AUTO_INCREMENT=1 ;

--
-- Structure of table `excluded_types`
--
CREATE TABLE IF NOT EXISTS `excluded_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of exclusion',
  `contract_id` int(11) NOT NULL COMMENT 'Id of contract',
  `type_id` int(11) NOT NULL COMMENT 'Id of leave ype to be excluded to the contract',
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Exclude a leave type from a contract' AUTO_INCREMENT=1 ;

--
-- Structure of table `leaves_history`
--
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
  `comments` TEXT NULL DEFAULT NULL COMMENT 'Comments on leave request',
  `document` BLOB NULL COMMENT 'Optional supporting document',
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `change_type` int(11) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `change_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`change_id`),
  KEY `changed_by` (`changed_by`),
  KEY `change_date` (`change_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of changes in leave requests table' COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- Tables for OAuth2 server
CREATE TABLE oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80) DEFAULT NULL, redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id)) ENGINE=InnoDB;
CREATE TABLE oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token)) ENGINE=InnoDB;
CREATE TABLE oauth_authorization_codes (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code)) ENGINE=InnoDB;
CREATE TABLE oauth_refresh_tokens (refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token)) ENGINE=InnoDB;
CREATE TABLE oauth_users (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username)) ENGINE=InnoDB;
CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN) ENGINE=InnoDB;
CREATE TABLE oauth_jwt (client_id VARCHAR(80) NOT NULL, subject VARCHAR(80), public_key VARCHAR(2000), CONSTRAINT jwt_client_id_pk PRIMARY KEY (client_id)) ENGINE=InnoDB;

--
-- Structure of table `oauth_applications`
--
CREATE TABLE IF NOT EXISTS `oauth_applications` (
  `user` int(11) NOT NULL COMMENT 'Identifier of Jorani user',
  `client_id` varchar(80) NOT NULL COMMENT 'Identifier of an application using OAuth2',
  KEY `user` (`user`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of allowed OAuth2 applications';

--
-- Structure of table `ci_sessions`
--
CREATE TABLE IF NOT EXISTS `ci_sessions` (
    `id` varchar(128) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
    `data` blob NOT NULL,
    KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='CodeIgniter sessions (you can empty this table without consequence)';

--
-- Structure of table `org_lists`
--
CREATE TABLE IF NOT EXISTS `org_lists` (
    `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier of a list',
    `user` INT NOT NULL COMMENT ' Identifier of Jorani user owning the list',
    `name` VARCHAR(512) NOT NULL,
PRIMARY KEY (`id`),
INDEX `org_lists_user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT = 'Custom lists of employees are an alternative to organization';

--
-- Structure of table `org_lists_employees`
--
CREATE TABLE IF NOT EXISTS `org_lists_employees` (
    `list` INT NOT NULL COMMENT 'Id of the list',
    `user` INT NOT NULL COMMENT 'id of an employee',
    `orderlist` INT NOT NULL COMMENT 'order in the list',
INDEX `org_list_id` (`list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT = 'Children table of org_lists (custom list of employees)';

--
-- Structure of table `parameters`
--
CREATE TABLE IF NOT EXISTS `parameters` (
`name` VARCHAR(32) NOT NULL,
`scope` INT NOT NULL COMMENT 'Either global(0) or user(1) scope',
`value` TEXT NOT NULL COMMENT 'PHP/serialize value',
`entity_id` TEXT NULL DEFAULT NULL COMMENT 'Entity ID (eg. user id) to which the parameter is applied',
KEY `param_name` (`name`, `scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Application parameters';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
