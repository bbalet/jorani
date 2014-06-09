-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Lun 09 Juin 2014 à 19:21
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `lms`
--

DELIMITER $$
--
-- Fonctions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetAncestry`(GivenID INT) RETURNS varchar(1024) CHARSET utf8
    DETERMINISTIC
BEGIN
    DECLARE rv VARCHAR(1024);
    DECLARE cm CHAR(1);
    DECLARE ch INT;

    SET rv = '';
    SET cm = '';
    SET ch = GivenID;
    WHILE ch > 0 DO
        SELECT IFNULL(parent_id,-1) INTO ch FROM
        (SELECT parent_id FROM organization WHERE id = ch) A;
        IF ch > 0 THEN
            SET rv = CONCAT(rv,cm,ch);
            SET cm = ',';
        END IF;
    END WHILE;
    RETURN rv;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `GetFamilyTree`(`GivenID` INT) RETURNS varchar(1024) CHARSET latin1
    DETERMINISTIC
BEGIN

    DECLARE rv,q,queue,queue_children VARCHAR(1024);
    DECLARE queue_length,front_id,pos INT;

    SET rv = '';
    SET queue = GivenID;
    SET queue_length = 1;

    WHILE queue_length > 0 DO
        SET front_id = FORMAT(queue,0);
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;

        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM organization WHERE parent_id = front_id) A;

        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;

    RETURN rv;

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `GetParentIDByID`(GivenID INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE rv INT;

    SELECT IFNULL(parent_id,-1) INTO rv FROM
    (SELECT parent_id FROM organization WHERE id = GivenID) A;
    RETURN rv;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `name` varchar(45) NOT NULL,
  `mask` bit(16) NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `actions`
--

INSERT INTO `actions` (`name`, `mask`, `Description`) VALUES
('accept_requests', b'0011000100110010', 'Accept the request of my team members'),
('admin_menu', b'0011000100110010', 'View admin menu'),
('change_password', b'0011000100110010', 'Change password'),
('create_leaves', b'0011000100110010', 'Create a new user leave request'),
('create_user', b'0011000100110010', 'Create a new user'),
('delete_user', b'0011000100110010', 'Delete an existing user'),
('edit_leaves', b'0011000100110010', 'Edit a leave request'),
('edit_settings', b'0011000100110010', 'Edit application settings'),
('edit_user', b'0011000100110010', 'Edit a user'),
('export_leaves', b'0011000100110010', 'Export the list of leave requests into an Excel file'),
('export_user', b'0011000100110010', 'Export the list of users into an Excel file'),
('hr_menu', b'0011000100110010', 'View HR menu'),
('individual_calendar', b'0011000100110010', 'View my leaves in a calendar'),
('list_leaves', b'0011000100110010', 'List my leave requests'),
('list_requests', b'0011000100110010', 'List the request of my team members'),
('list_users', b'0011000100110010', 'List users'),
('reject_requests', b'0011000100110010', 'Reject the request of my team members'),
('reset_password', b'0011000100110010', 'Modifiy the password of another user'),
('team_calendar', b'0011000100110010', 'View the leaves of my team in a calendar'),
('update_user', b'0011000100110010', 'Update a user'),
('view_leaves', b'0011000100110010', 'View the details of a leave request'),
('view_user', b'0011000100110010', 'View user''s details');

-- --------------------------------------------------------

--
-- Structure de la table `contracts`
--

CREATE TABLE IF NOT EXISTS `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `startentdate` varchar(5) NOT NULL,
  `endentdate` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `contracts`
--

INSERT INTO `contracts` (`id`, `name`, `startentdate`, `endentdate`) VALUES
(1, 'PNF Regular', '06/01', '05/31'),
(6, 'PNC Regular', '01/01', '12/31'),
(7, 'PNV Regular', '01/01', '12/31'),
(8, 'PNP Regular', '01/01', '12/31');

-- --------------------------------------------------------

--
-- Structure de la table `dayoffs`
--

CREATE TABLE IF NOT EXISTS `dayoffs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract` int(11) NOT NULL COMMENT 'Contract id',
  `date` date NOT NULL COMMENT 'Date of the day off',
  `type` int(11) NOT NULL COMMENT 'Half or full day',
  `title` varchar(128) NOT NULL COMMENT 'Description of day off',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `contract` (`contract`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Contenu de la table `dayoffs`
--

INSERT INTO `dayoffs` (`id`, `contract`, `date`, `type`, `title`) VALUES
(4, 1, '2014-05-01', 1, 'labour day'),
(5, 1, '2014-04-01', 2, 'test'),
(6, 1, '2014-04-02', 0, 'test 3'),
(7, 1, '2014-04-04', 0, 'fdfdfdf'),
(8, 1, '2014-01-03', 1, 'jjjjj'),
(9, 1, '2014-03-05', 1, 'test'),
(10, 1, '2014-04-10', 1, 'test'),
(11, 1, '2014-02-05', 1, 'essai'),
(12, 1, '2014-02-12', 1, 'fvf'),
(13, 1, '2014-05-14', 1, 'hghfghfghfg'),
(14, 1, '2014-07-10', 3, 'gfgfg'),
(15, 1, '2014-07-14', 2, 'gghfhfg');

-- --------------------------------------------------------

--
-- Structure de la table `entitleddays`
--

CREATE TABLE IF NOT EXISTS `entitleddays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract` int(11) NOT NULL,
  `employee` int(11) NOT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `type` int(11) NOT NULL,
  `days` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contract` (`contract`),
  KEY `employee` (`employee`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Contenu de la table `entitleddays`
--

INSERT INTO `entitleddays` (`id`, `contract`, `employee`, `startdate`, `enddate`, `type`, `days`) VALUES
(7, 5, 0, '2014-01-01', '2014-12-31', 1, '25.00'),
(11, 6, 0, '2014-01-01', '2014-12-31', 1, '24.00'),
(12, 7, 0, '2014-01-01', '2014-12-31', 1, '20.00'),
(14, 1, 0, '2013-06-01', '2014-05-31', 1, '30.00'),
(15, 1, 0, '2013-06-01', '2014-05-31', 7, '10.00'),
(18, 0, 34, '2013-06-01', '2014-05-31', 1, '-20.00'),
(19, 0, 34, '2013-06-01', '2014-05-31', 7, '-8.00');

-- --------------------------------------------------------

--
-- Structure de la table `leaves`
--

CREATE TABLE IF NOT EXISTS `leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `employee` int(11) DEFAULT NULL,
  `cause` text,
  `startdatetype` varchar(12) DEFAULT NULL,
  `enddatetype` varchar(12) DEFAULT NULL,
  `duration` decimal(10,2) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `employee` (`employee`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;

--
-- Contenu de la table `leaves`
--

INSERT INTO `leaves` (`id`, `startdate`, `enddate`, `status`, `employee`, `cause`, `startdatetype`, `enddatetype`, `duration`, `type`) VALUES
(78, '2014-05-05', '2014-05-06', 3, 33, '', 'Morning', 'Morning', '2.00', 1),
(79, '2014-05-07', '2014-05-07', 4, 33, 'test', 'Morning', 'Morning', '1.00', 1),
(80, '2014-05-08', '2014-05-08', 4, 33, '', 'Morning', 'Afternoon', '1.00', 1),
(81, '2014-05-26', '2014-05-26', 3, 31, '', 'Morning', 'Morning', '1.00', 1),
(82, '2014-05-14', '2014-05-14', 2, 31, '', 'Morning', 'Morning', '1.00', 1),
(83, '2014-05-30', '2014-05-30', 3, 31, '', 'Morning', 'Afternoon', '1.00', 0),
(84, '2014-05-26', '2014-05-30', 3, 1, '', 'Morning', 'Morning', '5.00', 1),
(86, '2014-06-02', '2014-06-03', 1, 1, '', 'Morning', 'Morning', '0.50', 1),
(87, '2014-06-02', '2014-06-10', 3, 1, 'test', 'Morning', 'Morning', '2.00', 1),
(88, '2014-06-16', '2014-06-19', 4, 1, 'test fr', 'Morning', 'Morning', '3.00', 5);

-- --------------------------------------------------------

--
-- Structure de la table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

--
-- Contenu de la table `organization`
--

INSERT INTO `organization` (`id`, `name`, `parent_id`) VALUES
(0, 'Passerelles numériques', -1),
(40, 'PNP', 0),
(41, 'PNF', 0),
(43, 'ERO', 40),
(44, 'PNV', 0),
(45, 'ERO', 44),
(46, 'Finance', 41),
(47, 'PNC', 0),
(48, 'Finance', 47),
(49, 'Training', 47),
(50, 'Selection', 47),
(51, 'Communication', 47),
(52, 'Pedagogy', 41),
(53, 'Communication', 41),
(55, 'Training', 40),
(56, 'Training & IT Support', 44),
(57, 'Education & Selection', 44),
(58, 'External relations', 44),
(59, 'Finance & Admin', 44),
(60, 'HR', 59),
(61, 'Account', 59),
(62, 'Finance & Admin', 47),
(63, 'Account', 62),
(64, 'HR', 62),
(65, 'WEP', 49),
(66, 'SNA', 49);

-- --------------------------------------------------------

--
-- Structure de la table `overtime`
--

CREATE TABLE IF NOT EXISTS `overtime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` decimal(10,2) NOT NULL,
  `cause` text NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `employee` (`employee`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Contenu de la table `overtime`
--

INSERT INTO `overtime` (`id`, `employee`, `date`, `duration`, `cause`, `status`) VALUES
(5, 33, '2014-05-08', '1.00', 'Selection process', 3),
(6, 33, '2014-05-06', '1.00', 'test', 1),
(7, 31, '2014-05-17', '2.00', 'Selection process', 3),
(8, 1, '2014-06-28', '1.00', 'test', 2),
(9, 1, '2014-06-03', '1.00', 'test', 4),
(10, 1, '2014-06-02', '2.00', 'test', 3);

-- --------------------------------------------------------

--
-- Structure de la table `positions`
--

CREATE TABLE IF NOT EXISTS `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Contenu de la table `positions`
--

INSERT INTO `positions` (`id`, `name`, `description`) VALUES
(2, 'Trainer', 'Teacher. Prepares and delivers trainings to students.'),
(3, 'WEP Coordinator', 'Manages trainers'),
(4, 'PNC Manager', 'General manager of the PNC center'),
(5, 'HR Officer', 'Assists the HR Manager'),
(6, 'Cleaner', 'Cleans the PN premises'),
(7, 'Accountant', 'Book keeping'),
(8, 'Training manager', ''),
(9, 'ERO Manager', ''),
(10, 'ERO Officer', ''),
(11, 'S&L Officer', '');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(2, 'user'),
(8, 'HR admin');

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(45) DEFAULT NULL,
  `key` varchar(45) DEFAULT NULL,
  `value` varchar(1024) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `status`
--

INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Planned'),
(2, 'Requested'),
(3, 'Accepted'),
(4, 'Rejected');

-- --------------------------------------------------------

--
-- Structure de la table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Contenu de la table `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(0, 'compensate'),
(1, 'paid leave'),
(2, 'maternity leave'),
(3, 'paternity leave'),
(4, 'special leave'),
(5, 'Sick leave for staff'),
(7, 'RTT'),
(11, 'Sick leave for staff''s child'),
(12, 'Wedding leave for staff'),
(13, 'Wedding leave for staff''s children'),
(14, 'Bereavement leave for staff''s children or spouse'),
(15, 'Bereavement leave for staff''s parents, god parents & siblings'),
(16, 'Sabbatical leave'),
(17, 'Leave for training'),
(18, 'Solo Parent Leave'),
(19, 'Calamity Leave');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `login` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(512) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `organization` int(11) DEFAULT NULL,
  `contract` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `datehired` date DEFAULT NULL COMMENT 'Date hired / Started',
  `identifier` varchar(64) NOT NULL COMMENT 'Internal/company identifier',
  `language` varchar(2) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`id`),
  KEY `manager` (`manager`),
  KEY `organization` (`organization`),
  KEY `contract` (`contract`),
  KEY `position` (`position`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `login`, `email`, `password`, `role`, `manager`, `country`, `organization`, `contract`, `position`, `datehired`, `identifier`, `language`) VALUES
(1, 'Benjamin', 'BALET', 'bbalet', 'benjamin.balet@gmail.com', '$2a$08$LeUbaGFqJjLSAN7to9URsuHB41zcmsMBgBhpZuFp2y2OTxtVcMQ.C', 8, 1, NULL, 65, 6, 3, '2013-10-28', 'PNC0025', 'fr'),
(31, 'John', 'DOE', 'jdoe', 'jdoe@lms.org', '$2a$08$Rq88xJDk1UPiCDYdfoq1jeqr5.45KcI4dQXu1.NAtXoBjDE9L6vsa', 2, 1, NULL, 65, 6, 2, '2014-04-04', 'PNC0085', 'en'),
(32, 'Jean', 'DUPONT', 'jdupont', 'jdupont@lms.org', '$2a$08$uyH4RJ1hO2.GyAzLcaTRAu7kDZdKageGj4hyta2ue/bQFto9LICg.', 2, 1, NULL, 65, 6, 2, '2014-04-28', 'PNC0093', 'en'),
(33, 'Elvis', 'PRESLEY', 'epresley', 'epresley@lms.org', '$2a$08$9PuQCJBKJ2QhVjzosKRqQemmxQ5VdM5PYQXARKJVZNVqgMovA/x/K', 2, 1, NULL, 65, 6, 2, '2013-09-02', 'PNC0092', 'en'),
(34, 'Sandra', 'BULLOCK', 'sbullock', 'sbullock@lms.org', '$2a$08$SojyeFLag.hmS.FWVHqABuwVHitSQyX323.xsFG6wHSSDmRx48fUm', 2, 34, NULL, 41, 1, 7, '2014-04-04', 'PNF001', 'en'),
(35, 'Bernard', 'DURAND', 'bdurand', 'bdurand@lms.org', '$2a$08$uTlCiLF12Cz7Es75wzxWse.JAmzftEqS8ZrCpGJnMgw4PfpaoeXX.', 2, 35, NULL, 41, 1, 6, NULL, '', 'en'),
(36, 'test', 'TEST', 'ttest', 'ttest@lms.org', '$2a$08$5P4EYmmVHDHmlc9PZwvCT.BM1dEn.bjh3gpA/kQhQDRQldMbkyZrS', 2, 36, NULL, 0, 1, 2, '2014-06-02', '001', 'fr');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
