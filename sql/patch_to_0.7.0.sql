-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.7.0
--
-- @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2018 Benjamin BALET

-- Fix default values for database

-- Task management feature is part of a 3rd application
DROP TABLE IF EXISTS `activities`;
DROP TABLE IF EXISTS `activities_employee`;
DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `time`;
DROP TABLE IF EXISTS `settings`;

-- Harmonize the charset and engine
ALTER TABLE `actions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of possible actions';
ALTER TABLE `contracts` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of contracts';
ALTER TABLE `dayoffs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of non working days';
ALTER TABLE `entitleddays` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Add or sub entitlement for employees or contracts';
ALTER TABLE `types` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of leave types';
ALTER TABLE `users` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of employees / users having access to Jorani';

ALTER TABLE `leaves` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Leave requests';
ALTER TABLE `organization` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Tree of the organization';
ALTER TABLE `overtime` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Overtime worked (extra time)';
ALTER TABLE `positions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Position (job position) in the organization';
ALTER TABLE `roles` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Roles in the application';
ALTER TABLE `status` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Status of the Leave Request';
ALTER TABLE `parameters` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Parameters can be global or specific to an object';

ALTER TABLE `delegations` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `excluded_types` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `leaves_history` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `org_lists_employees` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `org_lists` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `oauth_applications` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Random hash for public feeds
DELIMITER $$
CREATE PROCEDURE sp_add_random_hash_users()
    SQL SECURITY INVOKER
BEGIN
    IF NOT EXISTS (
        SELECT NULL
        FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'random_hash'
    ) THEN
        ALTER TABLE `users` ADD `random_hash` varchar(24) DEFAULT NULL COMMENT 'Random hash for public feeds (eg. ICS URL)';
    END IF;
END$$
DELIMITER ;
CALL sp_add_random_hash_users();
DROP PROCEDURE sp_add_random_hash_users;

-- Insert a random hash for all list_users
UPDATE `users` SET
`random_hash` = concat(
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@lid)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed:=round(rand(@seed)*4294967296))*62+1, 1),
  substring('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', rand(@seed)*62+1, 1)
);

-- User properties (for Jorani to become a SAML identity provider)
DELIMITER $$
CREATE PROCEDURE sp_add_user_properties_users()
    SQL SECURITY INVOKER
BEGIN
    IF NOT EXISTS (
        SELECT NULL
        FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'user_properties'
    ) THEN
        ALTER TABLE `users` ADD `user_properties` TEXT NULL DEFAULT NULL COMMENT 'Entity ID (eg. user id) to which the parameter is applied';
    END IF;
END$$
DELIMITER ;
CALL sp_add_user_properties_users();
DROP PROCEDURE sp_add_user_properties_users;

-- Entity ID in table  `parameters` (for user/table scope values)
DELIMITER $$
CREATE PROCEDURE sp_add_entity_id_parameters()
    SQL SECURITY INVOKER
BEGIN
    IF NOT EXISTS (
        SELECT NULL
        FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'parameters' AND column_name = 'entity_id'
    ) THEN
        ALTER TABLE `parameters` ADD `entity_id` TEXT NULL DEFAULT NULL COMMENT 'Entity ID (eg. user id) to which the parameter is applied';
    END IF;
END$$
DELIMITER ;
CALL sp_add_entity_id_parameters();
DROP PROCEDURE sp_add_entity_id_parameters;

-- An entitlement can be linked to an overtime request
DELIMITER $$
CREATE PROCEDURE sp_add_overtime_link_entitleddays()
    SQL SECURITY INVOKER
BEGIN
    IF NOT EXISTS (
        SELECT NULL
        FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'entitleddays' AND column_name = 'overtime'
    ) THEN
        ALTER TABLE `entitleddays` ADD `overtime` int(11) DEFAULT NULL COMMENT 'Link to an overtime request';
    END IF;
END$$
DELIMITER ;
CALL sp_add_overtime_link_entitleddays();
DROP PROCEDURE sp_add_overtime_link_entitleddays;
