-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 17, 2022 at 03:43 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `news_app_203`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'jLtUId+LoiCIF45DEq6XEttXX0pMZviZkL1FASbmv0mwJgjdgxEw567T/D+S+EUeRxeNecIAJ3Lt8lNJfMwEqZAK/Yxoedf8dskCwW3qWUDyq7bP/HFvDQ==');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bookmark`
--

CREATE TABLE `tbl_bookmark` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_breaking_news`
--

CREATE TABLE `tbl_breaking_news` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `id` int(11) NOT NULL,
  `category_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `image` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment`
--

CREATE TABLE `tbl_comment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0=unapproved,1=approved	',
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment_flag`
--

CREATE TABLE `tbl_comment_flag` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '	0=diactive,1=active	',
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment_like`
--

CREATE TABLE `tbl_comment_like` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1=like, 2=dislike, 0=none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment_notification`
--

CREATE TABLE `tbl_comment_notification` (
  `id` int(11) NOT NULL,
  `master_id` int(11) NOT NULL COMMENT 'comment/like',
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_live_streaming`
--

CREATE TABLE `tbl_live_streaming` (
  `id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `image` text NOT NULL,
  `type` varchar(20) NOT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_news`
--

CREATE TABLE `tbl_news` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL DEFAULT 0,
  `tag_id` varchar(200) NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `date` datetime NOT NULL,
  `content_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `counter` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_news_image`
--

CREATE TABLE `tbl_news_image` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `other_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_news_like`
--

CREATE TABLE `tbl_news_like` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1=like, 2=dislike, 0=none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notifications`
--

CREATE TABLE `tbl_notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `type` varchar(12) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `image` varchar(128) NOT NULL,
  `date_sent` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `type`, `message`) VALUES
(1, 'privacy_policy', '<p>Privacy Policy</p>'),
(2, 'fcm_sever_key', 'AAAA_Nk07a0:APA91bHzME8bylwrhQ8YfTq3KN_WieLXPNMIzdgcObB0kKGUofxVcY9xsitoKT9Z_oxEUIs0ZoV0zI7h8NsCn9EiA5yJrdzuTCceK0SMQhTa8a5IVhjG4unnUhwhgcyT9QjfEjKRzWea'),
(3, 'about_us', '<p>About Us</p>'),
(4, 'terms_conditions', '<p>Terms Conditions</p>'),
(5, 'contact_us', '<p>Contact Us</p>'),
(6, 'category_mode', '1'),
(7, 'breaking_news_mode', '1'),
(8, 'comments_mode', '1'),
(9, 'app_name', 'News App'),
(10, 'app_logo', 'logo.png'),
(11, 'app_logo_full', 'logo-full.png'),
(12, 'system_timezone', 'Asia/Kolkata'),
(13, 'jwt_key', 'replace_your_jwt_secret_key'),
(14, 'app_version', '3.1.2'),
(15, 'live_streaming_mode', '1'),
(16, 'live_streaming_mode', '1'),
(17, 'live_streaming_mode', '1'),
(18, 'live_streaming_mode', '1'),
(19, 'subcategory_mode', '1'),
(20, 'fb_rewarded_video_id', 'fb Native Unit Id'),
(21, 'fb_interstitial_id', 'fb Interstitial Id'),
(22, 'fb_banner_id', 'fb Banner Id'),
(23, 'fb_native_unit_id', 'fb Native Unit Id'),
(24, 'ios_fb_rewarded_video_id', 'fb IOS Rewarded Video Id'),
(25, 'ios_fb_interstitial_id', 'fb IOS Interstitial Id'),
(26, 'ios_fb_banner_id', 'fb IOS Banner Id'),
(27, 'ios_fb_native_unit_id', 'fb IOS Native Unit Id'),
(28, 'ios_fb_banner_id', 'fb IOS Banner Id'),
(29, 'ios_fb_banner_id', 'fb IOS Banner Id'),
(30, 'ios_fb_banner_id', 'fb IOS Banner Id'),
(40, 'google_interstitial_id', 'google Interstitial Id'),
(39, 'google_rewarded_video_id', 'google Rewarded Video Id'),
(38, 'ads_type', '1'),
(37, 'in_app_ads_mode', '0'),
(41, 'google_banner_id', 'google Banner Id'),
(42, 'google_native_unit_id', 'google Native Unit Id'),
(43, 'ios_ads_type', '1'),
(44, 'ios_in_app_ads_mode', '0'),
(45, 'ios_google_rewarded_video_id', 'google Rewarded Video Id'),
(46, 'ios_google_interstitial_id', 'google Interstitial Id'),
(47, 'ios_google_banner_id', 'google Banner Id'),
(48, 'ios_google_native_unit_id', 'google Native Unit Id');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subcategory`
--

CREATE TABLE `tbl_subcategory` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_name` text COLLATE utf8mb4_bin NOT NULL,
  `image` text COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_survey_option`
--

CREATE TABLE `tbl_survey_option` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `options` text NOT NULL,
  `counter` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_survey_question`
--

CREATE TABLE `tbl_survey_question` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_survey_result`
--

CREATE TABLE `tbl_survey_result` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tag`
--

CREATE TABLE `tbl_tag` (
  `id` int(11) NOT NULL,
  `tag_name` varchar(225) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_token`
--

CREATE TABLE `tbl_token` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `firebase_id` text CHARACTER SET utf8mb4 NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `type` varchar(10) NOT NULL,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `profile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `fcm_id` text NOT NULL,
  `status` int(11) NOT NULL COMMENT '0=diactive,1=active',
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users_category`
--

CREATE TABLE `tbl_users_category` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_bookmark`
--
ALTER TABLE `tbl_bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_breaking_news`
--
ALTER TABLE `tbl_breaking_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_comment_flag`
--
ALTER TABLE `tbl_comment_flag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `tbl_comment_like`
--
ALTER TABLE `tbl_comment_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `news_id` (`comment_id`);

--
-- Indexes for table `tbl_comment_notification`
--
ALTER TABLE `tbl_comment_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_live_streaming`
--
ALTER TABLE `tbl_live_streaming`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_news`
--
ALTER TABLE `tbl_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_news_image`
--
ALTER TABLE `tbl_news_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `tbl_news_like`
--
ALTER TABLE `tbl_news_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_subcategory`
--
ALTER TABLE `tbl_subcategory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_survey_option`
--
ALTER TABLE `tbl_survey_option`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_survey_question`
--
ALTER TABLE `tbl_survey_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_survey_result`
--
ALTER TABLE `tbl_survey_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tag`
--
ALTER TABLE `tbl_tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_token`
--
ALTER TABLE `tbl_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users_category`
--
ALTER TABLE `tbl_users_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_bookmark`
--
ALTER TABLE `tbl_bookmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_breaking_news`
--
ALTER TABLE `tbl_breaking_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_comment_flag`
--
ALTER TABLE `tbl_comment_flag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_comment_like`
--
ALTER TABLE `tbl_comment_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_comment_notification`
--
ALTER TABLE `tbl_comment_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_live_streaming`
--
ALTER TABLE `tbl_live_streaming`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_news`
--
ALTER TABLE `tbl_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_news_image`
--
ALTER TABLE `tbl_news_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_news_like`
--
ALTER TABLE `tbl_news_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_notifications`
--
ALTER TABLE `tbl_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tbl_subcategory`
--
ALTER TABLE `tbl_subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_survey_option`
--
ALTER TABLE `tbl_survey_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_survey_question`
--
ALTER TABLE `tbl_survey_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_survey_result`
--
ALTER TABLE `tbl_survey_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_tag`
--
ALTER TABLE `tbl_tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_token`
--
ALTER TABLE `tbl_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users_category`
--
ALTER TABLE `tbl_users_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE IF NOT EXISTS `tbl_user_roles` (`id` int(11) NOT NULL AUTO_INCREMENT, `role` varchar(50) NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `tbl_users` ADD `role` INT(11) NOT NULL AFTER `date`;
ALTER TABLE `tbl_news` ADD `user_id` INT(11) NOT NULL AFTER `description`, ADD `admin_id` INT(11) NOT NULL AFTER `user_id`, ADD `show_till` DATE NOT NULL AFTER `admin_id`, ADD `status` INT(11) NOT NULL COMMENT '1-Active, 0-Deactive' AFTER `show_till`;
CREATE TABLE `tbl_languages` ( `id` int(11) NOT NULL, `language` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, `code` varchar(11) NOT NULL, `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1=Enabled, 0=Disabled', `isRTL` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 - no, 1- yes', `image` text NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `tbl_languages` (`id`, `language`, `code`, `status`, `isRTL`, `image`) VALUES (1, 'Amharic', 'am', 0, 0, ''), (2, 'Arabic', 'ar', 0, 0, ''), (3, 'Basque', 'eu', 0, 0, ''), (4, 'Bengali', 'bn', 0, 0, ''), (5, 'English (UK)', 'en-GB', 0, 0, ''), (6, 'Portuguese (Brazil)', 'pt-BR', 0, 0, ''), (7, 'Bulgarian', 'bg', 0, 0, ''), (8, 'Catalan', 'ca', 0, 0, ''), (9, 'Cherokee', 'chr', 0, 0, ''), (10, 'Croatian', 'hr', 0, 0, ''), (11, 'Czech', 'cs', 0, 0, ''), (12, 'Danish', 'da', 0, 0, ''), (13, 'Dutch', 'nl', 0, 0, ''), (14, 'English (US)', 'en', 1, 0, 'en.webp'), (15, 'Estonian', 'et', 0, 0, ''), (16, 'Filipino', 'fil', 0, 0, ''), (17, 'Finnish', 'fi', 0, 0, ''), (18, 'French', 'fr', 0, 0, ''), (19, 'Greek', 'el', 0, 0, ''), (20, 'Gujarati', 'gu', 0, 0, ''), (21, 'Hebrew', 'iw', 0, 0, ''), (22, 'Hindi', 'hi', 0, 0, ''), (23, 'Hungarian', 'hu', 0, 0, ''), (24, 'Icelandic', 'is', 0, 0, ''), (25, 'Indonesian', 'id', 0, 0, ''), (26, 'German', 'de', 0, 0, ''), (27, 'Italian', 'it', 0, 0, ''), (28, 'Japanese', 'ja', 0, 0, ''), (29, 'Kannada', 'kn', 0, 0, ''), (30, 'Korean', 'ko', 0, 0, ''), (31, 'Latvian', 'lv', 0, 0, ''), (32, 'Lithuanian', 'lt', 0, 0, ''), (33, 'Malay', 'ms', 0, 0, ''), (34, 'Malayalam', 'ml', 0, 0, ''), (35, 'Marathi', 'mr', 0, 0, ''), (36, 'Norwegian', 'no', 0, 0, ''), (37, 'Polish', 'pl', 0, 0, ''), (38, 'Portuguese (Portugal)', 'pt-PT', 0, 0, ''), (39, 'Romanian', 'ro', 0, 0, ''), (40, 'Russian', 'ru', 0, 0, ''), (41, 'Serbian', 'sr', 0, 0, ''), (42, 'Chinese (PRC)', 'zh-CN', 0, 0, ''), (43, 'Slovak', 'sk', 0, 0, ''), (44, 'Slovenian', 'sl', 0, 0, ''), (45, 'Spanish', 'es', 0, 0, ''), (46, 'Swahili', 'sw', 0, 0, ''), (47, 'Swedish', 'sv', 0, 0, ''), (48, 'Tamil', 'ta', 0, 0, ''), (49, 'Telugu', 'te', 0, 0, ''), (50, 'Thai', 'th', 0, 0, ''), (51, 'Chinese (Taiwan)', 'zh-TW', 0, 0, ''), (52, 'Turkish', 'tr', 0, 0, ''), (53, 'Urdu', 'ur', 0, 0, ''), (54, 'Ukrainian', 'uk', 0, 0, ''), (55, 'Vietnamese', 'vi', 0, 0, ''), (56, 'Welsh', 'cy', 0, 0, '');
ALTER TABLE `tbl_languages` ADD PRIMARY KEY (`id`);
ALTER TABLE `tbl_languages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57; 
ALTER TABLE `tbl_breaking_news` ADD `language_id` INT(255) NOT NULL DEFAULT '14'  AFTER `description`;
ALTER TABLE `tbl_live_streaming` ADD `language_id` INT(255) NOT NULL  DEFAULT '14'  AFTER `url`;
ALTER TABLE `tbl_category` ADD `language_id` INT(255) NOT NULL DEFAULT '14'  AFTER `image`;
ALTER TABLE `tbl_subcategory` ADD `language_id` INT(255) NOT NULL DEFAULT '14'  AFTER `image`;
ALTER TABLE `tbl_tag` ADD `language_id` INT(255) NOT NULL DEFAULT '14' AFTER `tag_name`;
ALTER TABLE `tbl_news` ADD `language_id` INT(255) NOT NULL DEFAULT '14' AFTER `status`;
ALTER TABLE `tbl_survey_question` ADD `language_id` INT(255) NOT NULL DEFAULT '14' AFTER `status`;
INSERT INTO `tbl_settings` (`type`, `message`) VALUES ('default_language', '14');
CREATE TABLE `tbl_pages` ( `id` int(11) NOT NULL, `title` varchar(500) DEFAULT NULL, `slug` varchar(500) DEFAULT NULL, `meta_description` varchar(500) DEFAULT NULL, `meta_keywords` varchar(500) DEFAULT NULL, `is_custom` tinyint(1) DEFAULT 1 COMMENT '0 - Default, 1-Custom', `page_content` mediumtext DEFAULT NULL, `page_type` varchar(50) DEFAULT 'page', `language_id` int(11) DEFAULT 14, `page_icon` varchar(500) NOT NULL, `is_termspolicy` tinyint(1) NOT NULL, `is_privacypolicy` tinyint(1) NOT NULL, `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Disable, 1- Enable', `created_at` timestamp NULL DEFAULT current_timestamp() ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `tbl_pages`(`id`, `title`, `slug`, `meta_description`, `meta_keywords`, `is_custom`, `page_content`, `page_type`, `language_id`, `page_icon`, `is_termspolicy`, `is_privacypolicy`, `status`, `created_at`) VALUES (1, 'Contact Us', 'contact-us', ' Contact Us', 'gvbgnb', 0, '<p style=\"text-align: center;\"><strong>How can we help you?</strong></p>\r\n<p style=\"text-align: center;\">It looks&nbsp; like you have problems with our system. We are here to help you.&nbsp; so, please get in touch with us.</p>\r\n<p style=\"text-align: left;\"><strong>Head Office No. :</strong></p>\r\n<p style=\"text-align: left;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; +919876543210</p>\r\n<p style=\"text-align: left;\"><strong>&nbsp;Fax No. :</strong></p>\r\n<p style=\"text-align: left;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; +9195324785584</p>\r\n<p style=\"text-align: left;\"><strong>Office Address:</strong></p>\r\n<p style=\"text-align: left;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Gujarat-India</p>\r\n<p style=\"text-align: left;\"><strong>Email Address:</strong></p>\r\n<p style=\"text-align: left;\">&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; newsApp123@gmail.com</p>', 'page', 14, '1672122091.0738.png', 0, 0, 1, '2022-12-16 18:59:20'), (2, 'About Us', 'about-us', 'About Us', 'gvbgnb', 0, '<p><strong>About Us:</strong></p>\r\n<p>Most people wouldn\'t&nbsp; even&nbsp; consider getting&nbsp; a physical morning&nbsp; newspaper anymore, so we&nbsp; depend&nbsp; on digital sources for our news. Finding&nbsp; an app that helps you get&nbsp; the news you want in a timely manner is essential.</p>\r\n<p>Now all are in your handy. The app contains so many polupar categories of news. Such as breaking news, top news, travels, sports, health,entaintainment,world etc. You can read,bookmark,like,comment and share the news with others.</p>\r\n<p><strong>About Our Company:</strong></p>\r\n<p>Our company that provides multi-technology services with multi-skilled and highly competent work force and strong global presence.</p>\r\n<p>Our motto of help the customer to expand their business with help of technology. Yes, we aren&rsquo;t alone, we are Team of Developers &amp; Technology lovers who are Enthusiastic, Passionate, Skilled, Creative, Multi-Talented, Ready to Strive, Helpful &amp; always there to support our lovable clients, who are integral part of our team.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>', 'page', 14, '1672122075.4874.png', 0, 0, 1, '2022-12-16 18:59:40'), (3, 'Terms & Conditions', 'terms-condition', 'Terms & Conditions', 'gvbgnb', 0, '<p style=\"text-align: left;\"><strong>1. Terms Conditions</strong></p>\r\n<p style=\"text-align: left;\">Don\'t misuse our Services. You may use our Services only as permitted by law, including applicable export and re-export control laws and regulations. We may suspend or stop providing our Services to you if you do not comply with our terms or policies or if we are investigating suspected misconduct.</p>\r\n<p style=\"text-align: left;\"><strong>2. Terms Conditions</strong></p>\r\n<p style=\"text-align: left;\">Don\'t misuse our Services. You may use our Services only as permitted by law, including applicable export and re-export control laws and regulations. We may suspend or stop providing our Services to you if you do not comply with our terms or policies or if we are investigating suspected misconduct.</p>\r\n<p style=\"text-align: left;\"><strong>3. Terms Conditions</strong></p>\r\n<p style=\"text-align: left;\">Don\'t misuse our Services. You may use our Services only as permitted by law, including applicable export and re-export control laws and regulations. We may suspend or stop providing our Services to you if you do not comply with our terms or policies or if we are investigating suspected misconduct.</p>', 'page', 14, '1672122059.9894.png', 1, 0, 1, '2022-12-16 19:00:04'), (4, 'Privacy Policy', 'privacy-policy', 'Privacy Policy', 'gvbgnb', 0, '<p style=\"text-align: left;\">NEWS APP &amp; CONTENT POLICY</p>\r\n<p>This page is used to inform visitors regarding our policies with the collection, use, and disclosure of Personal Information if anyone decided to use our Service.</p>\r\n<p>If you choose to use our Service, then you agree to the collection and use of information in relation to this policy. The Personal Information that we collect is used for providing and improving the Service. We will not use or share your information with anyone except as described in this Privacy Policy.</p>\r\n<p>The terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, which is accessible at Infinity news app unless otherwise defined in this Privacy Policy.</p>\r\n<p><strong>Information Collection and Use</strong></p>\r\n<p>For a better experience, while using our Service, we may require you to provide us with certain personally identifiable information. The information that we request will be retained by us and used as described in this privacy policy.</p>\r\n<p>The app does use third party services that may collect information used to identify you.</p>\r\n<p>Link to privacy policy of third party service providers used by the app</p>\r\n<ul>\r\n<li><a href=\"https://www.google.com/policies/privacy/\" target=\"_blank\" rel=\"noopener noreferrer\">Google Play Services</a></li>\r\n<li><a href=\"https://support.google.com/admob/answer/6128543?hl=en\" target=\"_blank\" rel=\"noopener noreferrer\">AdMob</a></li>\r\n<li><a href=\"https://firebase.google.com/policies/analytics\" target=\"_blank\" rel=\"noopener noreferrer\">Google Analytics for Firebase</a></li>\r\n<li><a href=\"https://www.facebook.com/about/privacy/update/printable\" target=\"_blank\" rel=\"noopener noreferrer\">Facebook</a></li>\r\n<li><a href=\"https://unity3d.com/legal/privacy-policy\" target=\"_blank\" rel=\"noopener noreferrer\">Unity</a></li>\r\n</ul>\r\n<p><strong>Log Data</strong></p>\r\n<p>We want to inform you that whenever you use our Service, in a case of an error in the app we collect data and information (through third party products) on your phone called Log Data. This Log Data may include information such as your device Internet Protocol (&ldquo;IP&rdquo;) address, device name, operating system version, the configuration of the app when utilizing our Service, the time and date of your use of the Service, and other statistics.</p>\r\n<p><strong>Cookies</strong></p>\r\n<p>Cookies are files with a small amount of data that are commonly used as anonymous unique identifiers. These are sent to your browser from the websites that you visit and are stored on your device\'s internal memory.</p>\r\n<p>This Service does not use these &ldquo;cookies&rdquo; explicitly. However, the app may use third party code and libraries that use &ldquo;cookies&rdquo; to collect information and improve their services. You have the option to either accept or refuse these cookies and know when a cookie is being sent to your device. If you choose to refuse our cookies, you may not be able to use some portions of this Service.</p>\r\n<p><strong>Service Providers</strong></p>\r\n<p>We may employ third-party companies and individuals due to the following reasons:</p>\r\n<ul>\r\n<li>To facilitate our Service;</li>\r\n<li>To provide the Service on our behalf;</li>\r\n<li>To perform Service-related services; or</li>\r\n<li>To assist us in analyzing how our Service is used.</li>\r\n</ul>\r\n<p>We want to inform users of this Service that these third parties have access to your Personal Information. The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to disclose or use the information for any other purpose.</p>\r\n<p><strong>Security</strong></p>\r\n<p>We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.</p>\r\n<p><strong>Links to Other Sites</strong></p>\r\n<p>This Service may contain links to other sites. If you click on a third-party link, you will be directed to that site. Note that these external sites are not operated by us. Therefore, we strongly advise you to review the Privacy Policy of these websites. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>\r\n<p><strong>Children&rsquo;s Privacy</strong></p>\r\n<p>These Services do not address anyone under the age of 13. We do not knowingly collect personally identifiable information from children under 13 years of age. In the case we discover that a child under 13 has provided us with personal information, we immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so that we will be able to do necessary actions.</p>\r\n<p><strong>Changes to This Privacy Policy</strong></p>\r\n<p>We may update our Privacy Policy from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.</p>\r\n<p><strong>Contact Us</strong></p>\r\n<p>If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us at info@wrteam.in</p>\r\n<p style=\"text-align: left;\">&nbsp;</p>\r\n<p>&nbsp;</p>', 'page', 14, '1672121367.4083.png', 0, 1, 1, '2022-12-16 19:00:27');
ALTER TABLE `tbl_pages` ADD PRIMARY KEY (`id`);
ALTER TABLE `tbl_pages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
ALTER TABLE `tbl_news` ADD `is_clone` INT(255) NOT NULL DEFAULT '0' AFTER `language_id`;
CREATE TABLE `tbl_featured_sections` ( `id` int(255) NOT NULL, `language_id` int(255) NOT NULL, `title` varchar(500) NOT NULL, `short_description` varchar(500) NOT NULL, `news_type` varchar(500) NOT NULL COMMENT 'news, breaking_news, videos', `videos_type` varchar(100) NOT NULL COMMENT 'news, breaking_news', `filter_type` varchar(500) NOT NULL, `category_ids` varchar(500) NOT NULL COMMENT 'filter_type=most_commented, recently_added, most_viewed,most_favorite, most_like\r\n', `subcategory_ids` varchar(500) NOT NULL, `news_ids` varchar(500) NOT NULL COMMENT 'filter_type=custom_news', `style_app` varchar(100) NOT NULL, `row_order` int(50) NOT NULL DEFAULT 0, `created_at` date NOT NULL, `status` int(11) NOT NULL DEFAULT 1 COMMENT '1 - Enable, 0-Disable' ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `tbl_featured_sections` ADD PRIMARY KEY (`id`);
ALTER TABLE `tbl_featured_sections` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
CREATE TABLE `tbl_news_view` ( `id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `news_id` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `tbl_breaking_news_view` ( `id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `breaking_news_id` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `tbl_featured_sections` ADD `is_based_on_user_choice` TINYINT(1) NOT NULL COMMENT '0 - filter_section, 1 = news from user\'s category' AFTER `status`;
ALTER TABLE `tbl_languages` ADD `display_name` VARCHAR(50) NOT NULL AFTER `image`;
ALTER TABLE `tbl_languages` CHANGE `display_name` `display_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL;
CREATE TABLE `tbl_ad_spaces` ( `id` int(11) NOT NULL AUTO_INCREMENT primary key, `ad_space` varchar(200) NOT NULL, `ad_featured_section_id` int(11) NOT NULL COMMENT 'if ad_space = featured_section than add here featured_section_id', `ad_image` varchar(200) NOT NULL, `ad_url` varchar(200) NOT NULL, `language_id` int(11) NOT NULL, `created_at` date NOT NULL, `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - Disable, 1- Enable	' ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `tbl_web_settings` ( `id` int(11) NOT NULL AUTO_INCREMENT primary key, `type` varchar(32) NOT NULL, `message` longtext NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `tbl_featured_sections` ADD `style_web` VARCHAR(100) NOT NULL AFTER `style_app`;
ALTER TABLE `admin` ADD `email` TEXT NOT NULL AFTER `password`, ADD `forgot_unique_code` TEXT NOT NULL AFTER `email`, ADD `forgot_at` DATETIME NOT NULL AFTER `forgot_unique_code`;
ALTER TABLE `tbl_notifications` ADD `language_id` INT(255) NOT NULL DEFAULT '14' AFTER `date_sent`;
ALTER TABLE `tbl_ad_spaces` ADD `web_ad_image` VARCHAR(200) NOT NULL AFTER `ad_image`;

