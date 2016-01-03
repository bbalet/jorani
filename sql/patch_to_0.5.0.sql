-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.5.0
-- 
-- @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
-- @copyright  Copyright (c) 2014-2016 Benjamin BALET

-- New features:
--      * Bug fix on table dayoffs

ALTER TABLE `dayoffs` MODIFY `title` varchar(128) CHARACTER SET utf8;

