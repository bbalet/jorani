-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.5.0
-- 

-- New features:
--      * Define abbreviation on leave types, to display it on tabular calandar

ALTER TABLE `types` MODIFY COLUMN `abbreviation` char(1) DEFAULT NULL;

UPDATE TABLE `types` SET `abbreviation`='C' where name='compensate';
UPDATE TABLE `types` SET `abbreviation`='P' where name='paid leave';
UPDATE TABLE `types` SET `abbreviation`='M' where name='maternity leave';
UPDATE TABLE `types` SET `abbreviation`='Y' where name='paternity leave';
UPDATE TABLE `types` SET `abbreviation`='X' where name='special leave';
UPDATE TABLE `types` SET `abbreviation`='S' where name='Sick leave';

