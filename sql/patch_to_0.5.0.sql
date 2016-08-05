-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.5.0
-- 
-- @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2016 Benjamin BALET

-- New features:
--      * Bug fix on table dayoffs
--      * Users table now supports Regional translations (e.g. 'en' to 'en_GB').
--      * Add the possibility to exclude leave types from a contract.
--      * Define a default leave type for a contract (overwrite default type set in config file).

ALTER TABLE `dayoffs` MODIFY `title` varchar(128) CHARACTER SET utf8;

ALTER TABLE `users` MODIFY `language` varchar(5);

CREATE TABLE IF NOT EXISTS `excluded_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of exclusion',
  `contract_id` int(11) NOT NULL COMMENT 'Id of contract',
  `type_id` int(11) NOT NULL COMMENT 'Id of leave ype to be excluded to the contract',
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Exclude a leave type from a contract' AUTO_INCREMENT=1 ;

DELIMITER $$
CREATE PROCEDURE sp_add_default_leave_type_contract()
    SQL SECURITY INVOKER
BEGIN
        IF NOT EXISTS (
                SELECT NULL
                FROM information_schema.columns
                WHERE table_schema = DATABASE() AND table_name ='contracts' AND column_name = 'default_leave_type'
        ) THEN
                ALTER TABLE `contracts` ADD `default_leave_type` INT NULL DEFAULT NULL COMMENT 'default leave type for the contract (overwrite default type set in config file).';
        END IF;
END$$
DELIMITER ;

CALL sp_add_default_leave_type_contract();
DROP PROCEDURE sp_add_default_leave_type_contract;
