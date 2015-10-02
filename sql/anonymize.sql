-- ---------------------------------------------------
-- This utility helps you to anonymize the data of your Jorani database
-- 
-- This file is part of Jorani.
-- 
--  Jorani is free software: you can redistribute it and/or modify
--  it under the terms of the GNU General Public License as published by
--  the Free Software Foundation, either version 3 of the License, or
--  (at your option) any later version.
-- 
--  Jorani is distributed in the hope that it will be useful,
--  but WITHOUT ANY WARRANTY; without even the implied warranty of
--  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
--  GNU General Public License for more details.
-- 
--  You should have received a copy of the GNU General Public License
--  along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
-- @copyright  Copyright (c) 2014 - 2015 Benjamin BALET

-- Create a function that will help us to anonymize data
DROP FUNCTION IF EXISTS `shuffle`;

DELIMITER $$
CREATE FUNCTION shuffle(
    v_chars TEXT
)
RETURNS TEXT
NOT DETERMINISTIC
NO SQL
SQL SECURITY INVOKER
COMMENT 'Shuffle an input string'
BEGIN
    DECLARE v_retval TEXT DEFAULT '';
    DECLARE u_pos    INT UNSIGNED;
    DECLARE u        INT UNSIGNED;

    SET u = LENGTH(v_chars);
    WHILE u > 0
    DO
      SET u_pos = 1 + FLOOR(RAND() * u);
      SET v_retval = CONCAT(v_retval, MID(v_chars, u_pos, 1));
      SET v_chars = CONCAT(LEFT(v_chars, u_pos - 1), MID(v_chars, u_pos + 1, u));
      SET u = u - 1;
    END WHILE;

    RETURN v_retval;
END $$
DELIMITER ;

-- Anonymize firstname and lastname
-- Set all e-mail addresses to benjamin.balet@gmail.com
UPDATE `users` SET 
`firstname`=shuffle(`firstname`),
`lastname`=shuffle(`lastname`),
`login`= shuffle(`login`) + FLOOR(RAND() * 401) + 100,
`email`='benjamin.balet@gmail.com'
WHERE `login` != 'bbalet';

-- Uppercase the first letter of lastname, while other characters are in lower case
UPDATE `users` SET `firstname` = CONCAT(UCASE(LEFT(`firstname`, 1)), 
                             LCASE(SUBSTRING(`firstname`, 2)));

-- Set all passwords to bbalet
UPDATE `users` 
SET `password`='$2a$08$LeUbaGFqJjLSAN7to9URsuHB41zcmsMBgBhpZuFp2y2OTxtVcMQ.C';
