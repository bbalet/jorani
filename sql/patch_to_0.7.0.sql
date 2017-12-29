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



