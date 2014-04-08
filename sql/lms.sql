-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 08 Avril 2014 à 19:01
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `lms`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `contracts`
--

INSERT INTO `contracts` (`id`, `name`, `startentdate`, `endentdate`) VALUES
(1, 'PNC Regular Staff member', '01/01', '12/31');

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
  `days` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `entitleddays`
--

INSERT INTO `entitleddays` (`id`, `contract`, `employee`, `startdate`, `enddate`, `type`, `days`) VALUES
(1, 1, 0, '2014-01-01', '2014-12-31', 1, 25),
(2, 0, 6, '2014-01-01', '2014-12-31', 4, 5),
(3, 0, 6, '2014-01-01', '2014-12-31', 1, 2);

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
  `duration` decimal(10,0) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Contenu de la table `leaves`
--

INSERT INTO `leaves` (`id`, `startdate`, `enddate`, `status`, `employee`, `cause`, `startdatetype`, `enddatetype`, `duration`, `type`) VALUES
(17, '2014-03-30', '2014-03-31', 2, 5, '', 'Morning', 'Afternoon', '2', 1),
(18, '2014-03-30', '2014-04-01', 2, 5, 'i''m sick', 'Morning', 'Morning', '3', 1),
(19, '2014-03-30', '2014-04-01', 2, 5, 'i''m sick', 'Morning', 'Morning', '3', 1),
(20, '2014-03-30', '2014-03-31', 2, 5, '', 'Morning', 'Morning', '2', 1),
(21, '2014-03-30', '2014-03-31', 1, 5, 'test', 'Morning', 'Morning', '3', 1),
(22, '2014-04-06', '2014-04-10', 3, 6, '', 'Morning', 'Afternoon', '5', 1),
(23, '2014-04-14', '2014-04-15', 3, 6, '', 'Morning', 'Afternoon', '1', 4),
(24, '2014-05-19', '2014-05-23', 3, 6, '', 'Morning', 'Morning', '5', 1),
(25, '2013-10-08', '2013-10-09', 3, 6, NULL, 'Morning', 'Morning', '2', 1);

-- --------------------------------------------------------

--
-- Structure de la table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(1, 'admin'),
(2, 'user'),
(8, 'HR Officer / HR Manager'),
(16, 'Global HR Manager'),
(32, 'General Manager'),
(64, 'Global Manager');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(1, 'paid leave'),
(2, 'maternity leave'),
(3, 'paternity leave'),
(4, 'special leave'),
(5, 'sick leave');

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
  `password` varchar(128) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `service` int(11) DEFAULT NULL,
  `contract` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `login`, `email`, `password`, `role`, `manager`, `country`, `service`, `contract`) VALUES
(5, 'John', 'DOE', 'DOE', 'benjamin.balet@gmail.com', '$2a$08$NeL/A7xVRbsFr9lxVSDmfOQsXepBom/PfvfTnua4AnFH2YkVZpjCa', 66, 5, NULL, NULL, NULL),
(6, 'Benjamin', 'BALET', 'bbalet', 'benjamin.balet@gmail.com', '$2a$08$KTQ6KRC6rtsPG3Qkf0TKreRjUDB.CyxEY9/1dX1mZc50kqIiI3MYi', 1, 6, NULL, NULL, 1),
(12, 'toto', 'toto', 'ttoto', 'benjamin.balet@gmail.com', '$2a$08$Ijed..gLC.VRaoFNVhW.J.qHiD0.9mv9a8hIZIJHccfBvQ/1jAAEW', 8, 5, NULL, NULL, 1),
(13, 'aaaaaa', 'aaaaaa', 'aaaaaaa', 'benjamin.balet@gmail.com', '$2a$08$pGCACb6wWfXsWHGIH/G1O.Qh.AR6XsKhB1LGmhj8f1/csShUpTm6G', 2, 5, NULL, NULL, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
