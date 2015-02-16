-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.4.0
-- 

-- New features:
--      * Define delegates for a manager (employees who can accept/reject requests in behalf of a manager)

CREATE TABLE IF NOT EXISTS `delegations` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id of delegation',
  `manager_id` int(11) NOT NULL COMMENT 'Manager wanting to delegate',
  `delegate_id` int(11) NOT NULL COMMENT 'Employee having the delegation',
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Delegation of approval' AUTO_INCREMENT=1 ;

