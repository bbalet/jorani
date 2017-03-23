-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.6.0
-- 
-- @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2016 Benjamin BALET

-- New features:
-- * Role admin

INSERT IGNORE INTO `roles`
SET `id` = 1, `name` = 'admin';
