-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.7.0
--
-- @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2018 Benjamin BALET

-- Fix default values for database

-- Task management feature will be deported into a 3rd application
DROP TABLE `activities`;
DROP TABLE `activities_employee`;

-- Harmonize the charset and engine
ALTER TABLE `actions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of possible actions';
ALTER TABLE `contracts` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of contracts';
ALTER TABLE `dayoffs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='List of non working days';
ALTER TABLE `entitleddays` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci, COMMENT='Add or sub entitlement for employees or contracts';

-- `oauth_applications`;
-- `ci_sessions`
-- `org_lists`
-- `org_lists_employees`
-- `parameters`

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
        ALTER TABLE `users` ADD `random_hash` TEXT NULL DEFAULT NULL COMMENT 'Random hash for public feeds (eg. ICS URL)' CHARACTER SET utf8;
    END IF;
END$$
DELIMITER ;
CALL sp_add_random_hash_users();
DROP PROCEDURE sp_add_random_hash_users;

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
        ALTER TABLE `users` ADD `user_properties` TEXT NULL DEFAULT NULL COMMENT 'Entity ID (eg. user id) to which the parameter is applied' CHARACTER SET utf8;
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
        ALTER TABLE `parameters` ADD `entity_id` TEXT NULL DEFAULT NULL COMMENT 'Entity ID (eg. user id) to which the parameter is applied' CHARACTER SET utf8;
    END IF;
END$$
DELIMITER ;
CALL sp_add_entity_id_parameters();
DROP PROCEDURE sp_add_entity_id_parameters;
