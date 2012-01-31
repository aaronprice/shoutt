-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 12, 2009 at 05:31 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.0
-- 
-- Database: `shoutt`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `abuse`
-- 

DROP TABLE IF EXISTS `abuse`;
CREATE TABLE `abuse` (
  `id` int(11) NOT NULL auto_increment,
  `story_id` int(11) NOT NULL,
  `comment_id` int(11) default NULL,
  `user_id` int(11) NOT NULL,
  `ignore` int(1) NOT NULL,
  `date_registered` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `abuse`
-- 
-- --------------------------------------------------------

-- 
-- Table structure for table `captcha`
-- 

DROP TABLE IF EXISTS `captcha`;
CREATE TABLE `captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL auto_increment,
  `captcha_time` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) collate latin1_general_ci NOT NULL default '0',
  `word` varchar(20) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`captcha_id`),
  KEY `word` (`word`)
);

-- 
-- Dumping data for table `captcha`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `comment` longtext collate latin1_general_ci NOT NULL,
  `dateposted` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci NOT NULL,
  `reply_to` int(11) default NULL,
  `view` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `comments`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `favorites`
-- 

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id` int(11) NOT NULL auto_increment,
  `story_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_registered` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `favorites`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `invites`
-- 

DROP TABLE IF EXISTS `invites`;
CREATE TABLE `invites` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(150) collate latin1_general_ci NOT NULL,
  `vcode` varchar(32) collate latin1_general_ci NOT NULL,
  `datesent` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `invites`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `stories`
-- 

DROP TABLE IF EXISTS `stories`;
CREATE TABLE `stories` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `url` varchar(255) collate latin1_general_ci default NULL,
  `headline` varchar(65) collate latin1_general_ci NOT NULL,
  `headline_txt` varchar(60) collate latin1_general_ci NOT NULL,
  `what` longtext collate latin1_general_ci NOT NULL,
  `category` varchar(20) collate latin1_general_ci NOT NULL,
  `subcat` varchar(20) collate latin1_general_ci NOT NULL,
  `where` varchar(40) collate latin1_general_ci default NULL,
  `posx` varchar(32) collate latin1_general_ci default NULL,
  `posy` varchar(32) collate latin1_general_ci default NULL,
  `datesubmitted` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci NOT NULL,
  `view` int(1) NOT NULL,
  `popular` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `headline` (`headline`)
);

-- 
-- Dumping data for table `stories`
-- 
-- --------------------------------------------------------

-- 
-- Table structure for table `story_chain`
-- 

DROP TABLE IF EXISTS `story_chain`;
CREATE TABLE `story_chain` (
  `story_id` int(11) NOT NULL,
  `chain_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_added` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`story_id`,`chain_id`)
);

-- 
-- Dumping data for table `story_chain`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `story_views`
-- 

DROP TABLE IF EXISTS `story_views`;
CREATE TABLE `story_views` (
  `id` int(11) NOT NULL auto_increment,
  `story_id` int(11) NOT NULL,
  `user_id` int(11) default NULL,
  `date_viewed` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci default NULL,
  `user_agent` varchar(255) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `story_views`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `trash`
-- 

DROP TABLE IF EXISTS `trash`;
CREATE TABLE `trash` (
  `id` int(11) NOT NULL auto_increment,
  `story_id` int(11) NOT NULL,
  `comment_id` int(11) default NULL,
  `status` int(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_registered` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `trash`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `url_map`
-- 

DROP TABLE IF EXISTS `url_map`;
CREATE TABLE `url_map` (
  `id` int(11) NOT NULL auto_increment,
  `story_id` int(11) NOT NULL,
  `headline` varchar(65) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `url_map`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `user_invites`
-- 

DROP TABLE IF EXISTS `user_invites`;
CREATE TABLE `user_invites` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `remaining` int(2) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `user_invites`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `user_log`
-- 

DROP TABLE IF EXISTS `user_log`;
CREATE TABLE `user_log` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `report` varchar(255) collate latin1_general_ci NOT NULL,
  `score` int(11) NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `user_log`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(25) collate latin1_general_ci NOT NULL,
  `pwd` varchar(32) collate latin1_general_ci NOT NULL,
  `email` varchar(120) collate latin1_general_ci NOT NULL,
  `dob` date NOT NULL,
  `name` varchar(50) collate latin1_general_ci default NULL,
  `gender` int(1) default '0',
  `location` varchar(50) collate latin1_general_ci default NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `dateregistered` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
);

-- 
-- Dumping data for table `users`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `view_settings`
-- 

DROP TABLE IF EXISTS `view_settings`;
CREATE TABLE `view_settings` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `profanity` int(1) NOT NULL,
  `openextlinks` int(1) NOT NULL,
  `openstorylinks` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `view_settings`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `votes`
-- 

DROP TABLE IF EXISTS `votes`;
CREATE TABLE `votes` (
  `id` int(11) NOT NULL auto_increment,
  `type` int(1) NOT NULL,
  `type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date_registered` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `user_agent` varchar(255) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

-- 
-- Dumping data for table `votes`
-- 

-- --------------------------------------------------------

