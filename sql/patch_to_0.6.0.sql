-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.6.0
-- 
-- @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2017 Benjamin BALET

-- New features:
-- * Role admin
-- * Bug fix: allow OAuth2 client's secret to be null (case of Authorization).
-- * Table listing OAuth2 authorized application by a user.
-- * Migration to CI3: we now store the seesions into database.

INSERT IGNORE INTO `roles` SET `id` = 1, `name` = 'admin';

ALTER TABLE `oauth_clients` MODIFY `client_secret` varchar(80) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `oauth_applications` (
  `user` int(11) NOT NULL COMMENT 'Identifier of Jorani user',
  `client_id` varchar(80) NOT NULL COMMENT 'Identifier of an application using OAuth2',
  KEY `user` (`user`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='List of allowed OAuth2 applications' AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(128) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
);
