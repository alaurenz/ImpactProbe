-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 31, 2013 at 04:47 PM
-- Server version: 5.5.29
-- PHP Version: 5.3.10-1ubuntu3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `impact_probe`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_api_sources`
--

CREATE TABLE `active_api_sources` (
  `api_id` smallint(5) NOT NULL,
  `project_id` int(10) NOT NULL,
  PRIMARY KEY (`api_id`,`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `api_metadata`
--

CREATE TABLE `api_metadata` (
  `api_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`api_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `api_sources`
--

CREATE TABLE `api_sources` (
  `api_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `api_name` varchar(60) NOT NULL,
  `gather_method_name` varchar(40) NOT NULL,
  PRIMARY KEY (`api_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `api_sources`
--

INSERT INTO `api_sources` (`api_id`, `api_name`, `gather_method_name`) VALUES
(1, 'Twitter Search', 'twitter_search'),
(2, 'RSS Feed', 'rss_feed');

-- --------------------------------------------------------

--
-- Table structure for table `cached_text`
--

CREATE TABLE `cached_text` (
  `meta_id` int(10) NOT NULL,
  `text` mediumtext NOT NULL,
  PRIMARY KEY (`meta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cluster_log`
--

CREATE TABLE `cluster_log` (
  `project_id` int(10) unsigned NOT NULL,
  `threshold` float(3,3) NOT NULL DEFAULT '0.250',
  `order` varchar(35) NOT NULL DEFAULT 'arbitrarily',
  `num_docs` int(15) NOT NULL DEFAULT '0',
  `date_clustered` int(10) unsigned NOT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doc_clusters`
--

CREATE TABLE `doc_clusters` (
  `meta_id` int(10) unsigned NOT NULL,
  `cluster_id` int(10) unsigned NOT NULL,
  `score` float(6,6) NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`meta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doc_clusters_exact`
--

CREATE TABLE `doc_clusters_exact` (
  `meta_id` int(10) unsigned NOT NULL,
  `cluster_id_exact` int(10) unsigned NOT NULL,
  PRIMARY KEY (`meta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doc_clusters_time`
--

CREATE TABLE `doc_clusters_time` (
  `meta_id` int(10) unsigned NOT NULL,
  `cluster_id` int(10) unsigned NOT NULL,
  `time_plot_id` int(8) NOT NULL,
  `score` float(6,6) NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`meta_id`,`time_plot_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doc_clusters_time_exact`
--

CREATE TABLE `doc_clusters_time_exact` (
  `meta_id` int(10) unsigned NOT NULL,
  `cluster_id_exact` int(10) unsigned NOT NULL,
  `time_plot_id` int(8) NOT NULL,
  PRIMARY KEY (`meta_id`,`time_plot_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gather_log`
--

CREATE TABLE `gather_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gather_queries`
--

CREATE TABLE `gather_queries` (
  `query_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(10) unsigned NOT NULL,
  `search_query` varchar(600) NOT NULL,
  `results_gathered` mediumint(6) unsigned NOT NULL,
  `error` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`query_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keywords_phrases`
--

CREATE TABLE `keywords_phrases` (
  `keyword_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `keyword_phrase` varchar(250) NOT NULL,
  `is_negative` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `exact_phrase` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date_added` int(10) NOT NULL,
  PRIMARY KEY (`keyword_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `keyword_metadata`
--

CREATE TABLE `keyword_metadata` (
  `meta_id` int(10) unsigned NOT NULL,
  `keyword_id` int(10) unsigned NOT NULL,
  `num_occurrences` int(8) unsigned NOT NULL,
  KEY `meta_id` (`meta_id`,`keyword_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mark_negative_keywords`
--

CREATE TABLE `mark_negative_keywords` (
  `meta_id` int(10) unsigned NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`meta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `metadata`
--

CREATE TABLE `metadata` (
  `meta_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `url_id` int(10) unsigned NOT NULL,
  `api_id` smallint(5) unsigned NOT NULL,
  `date_published` int(10) unsigned NOT NULL,
  `date_retrieved` int(10) unsigned NOT NULL,
  `lang` varchar(50) DEFAULT NULL,
  `total_words` int(8) unsigned NOT NULL,
  `geolocation` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`meta_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `metadata_urls`
--

CREATE TABLE `metadata_urls` (
  `url_id` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(600) NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`url_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `preloaded_rss_feeds`
--

CREATE TABLE `preloaded_rss_feeds` (
  `feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) unsigned NOT NULL,
  `feed_name` varchar(100) NOT NULL,
  `is_searchable` tinyint(1) unsigned NOT NULL,
  `url` varchar(600) NOT NULL,
  PRIMARY KEY (`feed_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `preloaded_rss_feeds` (`feed_id`, `cat_id`, `feed_name`, `is_searchable`, `url`) VALUES
(1, 2, 'Google News', 1, 'Searchable: http://news.google.com/news?hl=en&safe=off&um=1&ie=UTF-8&output=rss&q='),
(2, 2, 'New York Times', 0, 'http://feeds.nytimes.com/nyt/rss/HomePage'),
(3, 1, 'Something', 0, 'http://www.something.com/rss');

-- --------------------------------------------------------

--
-- Table structure for table `preloaded_rss_feed_cats`
--

CREATE TABLE `preloaded_rss_feed_cats` (
  `cat_id` int(8) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `preloaded_rss_feed_cats` (`cat_id`, `cat_name`) VALUES
(1, 'Other'),
(2, 'News');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_title` varchar(120) NOT NULL,
  `date_created` int(10) NOT NULL,
  `gather_interval` varchar(25) NOT NULL DEFAULT 'daily',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rss_feeds`
--

CREATE TABLE `rss_feeds` (
  `feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `date_added` int(10) unsigned NOT NULL,
  `url` varchar(600) NOT NULL,
  `searchable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`feed_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
