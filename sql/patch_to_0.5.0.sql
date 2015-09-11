-- ---------------------------------------------------
-- Jorani Schema upgrade to 0.5.0
-- 

-- New features:
--      * Define abbreviation on leave types, to display it on tabular calandar

ALTER TABLE `types` ADD COLUMN `abbreviation` char(1) DEFAULT NULL;

UPDATE `types` SET `abbreviation`='C' where name='compensate';
UPDATE `types` SET `abbreviation`='P' where name='paid leave';
UPDATE `types` SET `abbreviation`='M' where name='maternity leave';
UPDATE `types` SET `abbreviation`='Y' where name='paternity leave';
UPDATE `types` SET `abbreviation`='X' where name='special leave';
UPDATE `types` SET `abbreviation`='S' where name='Sick leave';

