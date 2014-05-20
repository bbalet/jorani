-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 20 Mai 2014 à 21:58
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

CREATE DEFINER=`root`@`localhost` FUNCTION `GetFamilyTree`(GivenID INT) RETURNS varchar(1024) CHARSET latin1
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `contracts`
--

INSERT INTO `contracts` (`id`, `name`, `startentdate`, `endentdate`) VALUES
(1, 'PNC Regular Staff member', '01/01', '12/31'),
(4, 'PNF regular', '01/01', '12/31');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `entitleddays`
--

INSERT INTO `entitleddays` (`id`, `contract`, `employee`, `startdate`, `enddate`, `type`, `days`) VALUES
(1, 1, 0, '2014-01-01', '2014-12-31', 1, '25.00'),
(2, 0, 6, '2014-01-01', '2014-12-31', 4, '5.00'),
(3, 0, 6, '2014-01-01', '2014-12-31', 1, '2.00'),
(4, 0, 6, '2014-05-01', '2014-05-31', 1, '-1.00'),
(5, 4, 0, '2014-04-01', '2014-05-31', 1, '1.00'),
(6, 4, 0, '2014-05-01', '2014-05-31', 1, '1.00');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;

--
-- Contenu de la table `leaves`
--

INSERT INTO `leaves` (`id`, `startdate`, `enddate`, `status`, `employee`, `cause`, `startdatetype`, `enddatetype`, `duration`, `type`) VALUES
(17, '2014-03-30', '2014-03-31', 2, 5, '', 'Morning', 'Afternoon', '2.00', 1),
(18, '2014-03-30', '2014-04-01', 4, 5, 'i''m sick', 'Morning', 'Morning', '3.00', 1),
(19, '2014-03-30', '2014-04-01', 2, 5, 'i''m sick', 'Morning', 'Morning', '3.00', 1),
(20, '2014-03-30', '2014-03-31', 4, 5, '', 'Morning', 'Morning', '2.00', 1),
(22, '2014-04-06', '2014-04-10', 3, 6, '', 'Morning', 'Afternoon', '5.00', 1),
(23, '2014-04-14', '2014-04-15', 3, 6, '', 'Morning', 'Afternoon', '1.00', 4),
(24, '2014-05-19', '2014-05-23', 3, 6, '', 'Morning', 'Morning', '5.00', 1),
(25, '2013-10-08', '2013-10-09', 3, 6, NULL, 'Morning', 'Morning', '2.00', 1),
(27, '2014-04-10', '2014-04-17', 3, 6, '', 'Morning', 'Morning', '7.00', 1),
(28, '2014-04-10', '2014-04-17', 4, 6, '', 'Morning', 'Morning', '7.00', 1),
(29, '2014-04-22', '2014-04-22', 3, 6, '', 'Morning', 'Morning', '1.00', 1),
(30, '2014-04-15', '2014-04-16', 3, 6, '', 'Morning', 'Morning', '1.00', 1),
(31, '2014-04-17', '2014-04-18', 3, 6, '', 'Morning', 'Morning', '1.00', 1),
(32, '2014-04-08', '2014-04-09', 4, 6, '', 'Morning', 'Morning', '1.00', 1),
(33, '2014-04-23', '2014-04-24', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(34, '2014-04-23', '2014-04-24', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(35, '2014-04-25', '2014-04-26', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(36, '2014-04-18', '2014-04-19', 2, 6, '', 'Morning', 'Morning', '1.00', 3),
(37, '2014-04-23', '2014-04-23', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(38, '2014-04-24', '2014-04-25', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(39, '2014-04-26', '2014-04-27', 2, 6, '', 'Morning', 'Morning', '1.00', 2),
(40, '2014-04-24', '2014-04-25', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(41, '2014-04-28', '2014-04-29', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(42, '2014-04-29', '2014-04-30', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(43, '2014-04-24', '2014-04-26', 2, 6, '', 'Morning', 'Morning', '2.00', 1),
(44, '2014-04-27', '2014-04-28', 2, 6, '', 'Morning', 'Morning', '3.00', 1),
(45, '2014-04-27', '2014-04-28', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(46, '2014-04-27', '2014-04-28', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(47, '2014-04-25', '2014-04-26', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(48, '2014-04-28', '2014-04-29', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(49, '2014-04-27', '2014-04-29', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(50, '2014-04-17', '2014-04-30', 2, 6, '', 'Morning', 'Morning', '3.00', 1),
(51, '2014-04-07', '2014-04-08', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(52, '2014-04-20', '2014-04-21', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(53, '2014-04-08', '2014-04-23', 1, 6, '', 'Morning', 'Morning', '3.00', 1),
(54, '2014-04-07', '2014-04-14', 2, 6, 'gfg', 'Morning', 'Morning', '2.00', 1),
(55, '2014-04-16', '2014-04-25', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(57, '2014-04-13', '2014-04-14', 2, 6, '', 'Morning', 'Morning', '1.00', 2),
(58, '2014-04-22', '2014-04-24', 2, 6, '', 'Morning', 'Morning', '2.00', 1),
(59, '2014-04-22', '2014-04-23', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(60, '2014-04-01', '2014-04-02', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(61, '2014-04-14', '2014-04-23', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(62, '2014-04-14', '2014-04-15', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(63, '2014-04-27', '2014-04-28', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(64, '2014-04-07', '2014-04-08', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(66, '2014-04-03', '2014-04-04', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(67, '2014-04-13', '2014-04-14', 3, 6, '', 'Morning', 'Morning', '1.00', 1),
(68, '2014-04-06', '2014-04-07', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(69, '2014-04-15', '2014-04-16', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(70, '2014-04-06', '2014-04-07', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(71, '2014-04-07', '2014-04-08', 2, 6, '', 'Morning', 'Morning', '1.00', 1),
(73, '2014-05-01', '2014-05-02', 4, 6, '', 'Morning', 'Morning', '1.00', 1),
(74, '2014-05-11', '2014-05-12', 4, 6, '', 'Morning', 'Morning', '1.00', 1),
(75, '2014-05-07', '2014-05-08', 3, 6, '', 'Morning', 'Morning', '1.00', 1),
(76, '2014-05-28', '2014-05-29', 1, 5, '', 'Morning', 'Morning', '1.00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Contenu de la table `organization`
--

INSERT INTO `organization` (`id`, `name`, `parent_id`) VALUES
(0, 'Passerelles  numériques', -1),
(40, 'PNP', 0),
(41, 'PNF', 0),
(42, 'ERO', 41),
(43, 'ERO', 40),
(44, 'PNV', 0),
(45, 'ERO', 44),
(46, 'Finance', 41),
(47, 'PNC', 0),
(48, 'Finance', 47),
(49, 'Training', 47),
(50, 'Selection', 47),
(51, 'Communication', 47),
(52, 'Pedagogy', 41);

-- --------------------------------------------------------

--
-- Structure de la table `overtime`
--

CREATE TABLE IF NOT EXISTS `overtime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(11) NOT NULL,
  `date` date NOT NULL,
  `duration` decimal(10,0) NOT NULL,
  `cause` text NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `overtime`
--

INSERT INTO `overtime` (`id`, `employee`, `date`, `duration`, `cause`, `status`) VALUES
(2, 6, '2014-05-12', '1', 'test', 4);

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
(2, 'trainer', 'trains'),
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
(8, 'HR user');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Contenu de la table `settings`
--

INSERT INTO `settings` (`id`, `category`, `key`, `value`, `type`) VALUES
(1, 'mail', 'protocol', 'smtp', 'string'),
(2, 'mail', 'useragent', 'CodeIgniter', 'string'),
(3, 'mail', 'smtp_host', 'auth.smtp.1and1.fr', 'string'),
(4, 'mail', 'smtp_user', 'contact@benjamin-balet.info', 'string'),
(5, 'mail', 'smtp_pass', '', 'string'),
(6, 'mail', '_smtp_auth', 'TRUE', 'bool'),
(7, 'mail', 'smtp_port', '587', 'string'),
(8, 'mail', 'smtp_timeout', '20', 'string'),
(9, 'mail', 'charset', 'utf-8', 'string'),
(10, 'mail', 'mailtype', 'html', 'string'),
(11, 'mail', 'wordwrap', 'TRUE', 'bool'),
(12, 'mail', 'wrapchars', '80', 'int'),
(13, 'mail', 'validate', 'FALSE', 'bool'),
(14, 'mail', 'priority', '3', 'int'),
(15, 'mail', 'newline', '\\r\\n', 'string'),
(16, 'mail', 'crlf', '\\r\\n', 'string');

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
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(1, 'paid leave'),
(2, 'maternity leave'),
(3, 'paternity leave'),
(4, 'special leave'),
(5, 'sickness leave'),
(7, 'RTTE'),
(9, 'RTTS');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `login`, `email`, `password`, `role`, `manager`, `country`, `organization`, `contract`, `position`, `datehired`, `identifier`) VALUES
(5, 'John', 'DOE', 'jdoe', 'test@toto.com', '$2a$08$NeL/A7xVRbsFr9lxVSDmfOQsXepBom/PfvfTnua4AnFH2YkVZpjCa', 2, 5, NULL, 0, NULL, 0, '2014-05-30', 'AAA'),
(6, 'Benjamin', 'BALET', 'bbalet', 'benjamin.balet@gmail.com', '$2a$08$lKuLIUd7wGquZQ.tilcd7e9JZkUKtTjXQAhakzIAnU2yTTB3bxf0i', 1, 5, NULL, 42, 1, 10, NULL, ''),
(15, 'Jane', 'DOE', 'jdoe', 'jdoe@gmail.com', '$2a$08$2vJMASbNOsMP2Gf06YI7TONk5mgnnKuGzfogwqtMh6qoj89ZzeIqi', 2, 15, NULL, 0, NULL, 7, '2014-05-04', 'deded'),
(22, 'Bernard', 'DUPOND', 'bdupond', 'bdupond@test.org', '$2a$08$wDiqhwjD2ZEm6YemLzkJae31a9UnxaiojMfIj32AvPzoCFXPwgv9W', 2, 22, NULL, 0, NULL, 0, NULL, ''),
(23, 'georges', 'durand', 'gdurand', 'gdurand@lms.org', '$2a$08$ZtjuIR/z8u8M4j6ed/MHtePG5tACNFJ2YsHFuwoABP1NODhbanFFe', 8, 23, NULL, NULL, NULL, 9, NULL, ''),
(24, 'Georges', 'DUPOND', 'gdupond', 'gdupond@test.com', '$2a$08$u8Op8QfceajdLMcic24NNuzQqR7zbdxlUaC3pDRNx9lAP10q5HE4q', 2, 24, NULL, 0, NULL, 0, NULL, ''),
(25, 'boris', 'vian', 'bvian', 'bvian@pp.vo', '$2a$08$sAA9rruRvw9LPL.mJ/A03uWqcJwSYUAOT2rPT16PMUMkNnovyeZju', 2, 25, NULL, 0, NULL, 0, NULL, ''),
(26, 'Elvis', 'PRESLEY', 'epresley', 'epresley@test.org', '$2a$08$aHS6A7baCjjnGFXG55xGveM7nOSrIhulNJJCAk0uhD3zhqTf74nbW', 2, 26, NULL, 0, NULL, 0, '2014-05-25', 'b123');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
