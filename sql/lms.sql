-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 02 Avril 2014 à 15:25
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.3.13

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
-- Structure de la table `action`
--

CREATE TABLE IF NOT EXISTS `action` (
  `name` varchar(45) NOT NULL,
  `mask` bit(16) NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `action`
--

INSERT INTO `action` (`name`, `mask`, `Description`) VALUES
('accept_requests', b'0011001000110101', 'Accept the request of my team members'),
('admin_menu', b'0011000100000000', 'View admin menu'),
('change_password', b'0011001000110101', 'Change password'),
('create_leaves', b'0011001000110101', 'Create a new user leave request'),
('create_user', b'0011000100000000', 'Create a new user'),
('delete_user', b'0011000100000000', 'Delete an existing user'),
('edit_leaves', b'0011001000110101', 'Edit a leave request'),
('edit_settings', b'0011000100000000', 'Edit application settings'),
('edit_user', b'0011000100000000', 'Edit a user'),
('export_leaves', b'0011001000110101', 'Export the list of leave requests into an Excel file'),
('export_user', b'0011000100000000', 'Export the list of users into an Excel file'),
('hr_menu', b'0011001000111001', 'View HR menu'),
('individual_calendar', b'0011001000110101', 'View my leaves in a calendar'),
('list_leaves', b'0011001000110101', 'List my leave requests'),
('list_requests', b'0011001000110101', 'List the request of my team members'),
('list_users', b'0011000100000000', 'List users'),
('reject_requests', b'0011001000110101', 'Reject the request of my team members'),
('reset_password', b'0011000100000000', 'Modifiy the password of another user'),
('team_calendar', b'0011001000110101', 'View the leaves of my team in a calendar'),
('update_user', b'0011000100000000', 'Update a user'),
('view_leaves', b'0011001000110101', 'View the details of a leave request'),
('view_user', b'0011000100000000', 'View user''s details');

-- --------------------------------------------------------

--
-- Structure de la table `contract`
--

CREATE TABLE IF NOT EXISTS `contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `startentdate` varchar(5) NOT NULL,
  `endentdate` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `entitleddays`
--

CREATE TABLE IF NOT EXISTS `entitleddays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contract` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Contenu de la table `leaves`
--

INSERT INTO `leaves` (`id`, `startdate`, `enddate`, `status`, `employee`, `cause`, `startdatetype`, `enddatetype`, `duration`, `type`) VALUES
(17, '2014-03-30', '2014-03-31', 2, 5, '', 'Morning', 'Afternoon', '2', 1),
(18, '2014-03-30', '2014-04-01', 2, 5, 'i''m sick', 'Morning', 'Morning', '3', 1),
(19, '2014-03-30', '2014-04-01', 2, 5, 'i''m sick', 'Morning', 'Morning', '3', 1),
(20, '2014-03-30', '2014-03-31', 2, 5, '', 'Morning', 'Morning', '2', 1),
(21, '2014-03-30', '2014-03-31', 1, 5, 'test', 'Morning', 'Morning', '3', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `login`, `email`, `password`, `role`, `manager`, `country`, `service`, `contract`) VALUES
(5, 'John', 'DOE', 'DOE', 'benjamin.balet@gmail.com', '$2a$08$4TEenFJqPXnqrRSbrWwQBu.rV9nxsUSmT.3z6TQ4FSFdGSQEV7YX6', 66, 5, NULL, NULL, NULL),
(6, 'Benjamin', 'BALET', 'bbalet', 'benjamin.balet@gmail.com', '$2a$08$KTQ6KRC6rtsPG3Qkf0TKreRjUDB.CyxEY9/1dX1mZc50kqIiI3MYi', 1, 6, NULL, NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
