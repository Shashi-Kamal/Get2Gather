-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2018 at 10:24 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `socialnet`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `com_id` int(11) NOT NULL,
  `com` text NOT NULL,
  `com_by` int(11) NOT NULL,
  `com_to` int(11) NOT NULL,
  `com_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`com_id`, `com`, `com_by`, `com_to`, `com_date`) VALUES
(26, 'Again', 4, 41, '2018-04-24 20:08:22'),
(27, 'this time\r\nit will\r\nwork', 4, 41, '2018-04-24 20:11:48'),
(28, 'Last time\r\nit will\r\nwork', 4, 41, '2018-04-24 20:24:21'),
(29, 'She''s\r\nreally\r\nbeautiful', 4, 35, '2018-04-24 20:27:13'),
(30, 'What happened', 25, 4, '2018-04-24 20:30:15'),
(31, 'Ya i\r\nunderstand', 4, 42, '2018-04-24 20:33:57'),
(32, 'What you\r\ncan\r\nunderstand', 4, 42, '2018-04-24 20:36:26'),
(33, 'I think it\r\nworked', 4, 44, '2018-04-24 20:40:28'),
(34, 'Congratulations', 12, 4, '2018-04-24 21:10:14'),
(35, 'Why ??', 36, 4, '2018-04-25 01:07:49'),
(36, 'Congratulations !!', 25, 4, '2018-04-25 01:14:56'),
(38, 'hmm\r\nDreaming\r\nbig is\r\ngood', 5, 24, '2018-04-26 08:38:43'),
(39, 'Hm good going..', 5, 24, '2018-04-26 08:41:56'),
(40, 'Thumbs up!', 5, 25, '2018-04-26 08:43:48'),
(41, 'Where''s\r\nthe msg', 5, 45, '2018-04-26 08:58:00'),
(42, 'What''s\r\nthis ?', 5, 43, '2018-04-26 09:07:51'),
(43, 'What u have written ?', 5, 43, '2018-04-26 09:09:35'),
(44, 'what does this mean ?', 5, 43, '2018-04-26 09:12:33'),
(45, 'Try again next time', 5, 43, '2018-04-26 09:16:14'),
(46, 'Write something properly', 5, 43, '2018-04-26 09:18:37'),
(47, 'watch out\r\nnext\r\ntime...', 5, 43, '2018-04-26 09:27:29'),
(48, 'please\r\ntake care', 5, 43, '2018-04-26 09:28:27'),
(49, 'upoohoi', 5, 74, '2018-04-27 09:38:41'),
(50, 'SDDvd', 5, 74, '2018-04-27 09:48:48'),
(51, 'Nice one', 5, 81, '2018-04-29 19:24:22'),
(52, 'Good', 5, 81, '2018-04-29 19:31:07'),
(53, 'Where''s image ?', 5, 51, '2018-04-29 21:44:43'),
(54, 'wow..belur math e kobe geli..', 9, 107, '2018-05-08 13:49:13'),
(55, 'wow', 4, 107, '2018-05-08 18:34:07'),
(56, 'lucky u r', 4, 107, '2018-05-08 18:35:22'),
(57, 'Belur Math', 4, 107, '2018-05-08 20:26:54'),
(58, 'Belur Math', 4, 107, '2018-05-08 20:30:39'),
(59, '/lkn/lk', 4, 107, '2018-05-08 20:31:00'),
(60, '/lkn/lk', 4, 107, '2018-05-08 20:36:29'),
(61, 'Ha bol..', 4, 109, '2018-05-09 04:36:25'),
(62, 'Haha!! yes u r welcome', 4, 103, '2018-05-09 04:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL,
  `user_from` int(11) NOT NULL,
  `user_to` int(11) NOT NULL,
  `msg` text NOT NULL,
  `date_received` date NOT NULL,
  `is_opened` enum('no','yes') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `user_from`, `user_to`, `msg`, `date_received`, `is_opened`) VALUES
(44, 4, 9, 'Hello Sukku...', '2018-05-12', 'yes'),
(45, 9, 4, 'Yaa... what say..', '2018-05-12', 'yes'),
(46, 4, 9, 'Kalke clg jabi re ?', '2018-05-12', 'yes'),
(47, 9, 4, 'jaa matha byatha.. vabchi jabo nah', '2018-05-12', 'yes'),
(48, 4, 9, 'Ki bolish.. tahle ami o jabo naa.. hurr aka aka valo lage na', '2018-05-12', 'yes'),
(49, 9, 4, 'Naa keno ? tui jaa.. kalke AD sir r class ache.. na gele abar sei khub bokbe.  Tui borong ja, ar ki poray notes niye asbi. Ami tor theke por e dekhe nebo.  ', '2018-05-12', 'yes'),
(50, 9, 4, 'Jabi kintu.. Por e jeno na suni.. je tui jaas ni', '2018-05-12', 'yes'),
(51, 4, 9, 'Baba!! kalke AD naki !! Thik ache jabo', '2018-05-12', 'yes'),
(53, 9, 4, 'gechili? naki abar doob marchis?', '2018-05-13', 'yes'),
(54, 4, 9, 'Na na gesilam re', '2018-05-13', 'yes'),
(57, 4, 9, 'Hello.. Sukku how do u do ??', '2018-05-14', 'yes'),
(58, 9, 4, 'Yaa.. i''m as fine as u.. :)', '2018-05-14', 'yes'),
(59, 4, 9, 'Good !!', '2018-05-14', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `status_content` varchar(255) NOT NULL,
  `status_photo` varchar(255) DEFAULT NULL,
  `view_permit` varchar(10) NOT NULL,
  `comments` int(255) DEFAULT NULL,
  `likes` int(255) NOT NULL,
  `status_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `user_id`, `status_content`, `status_photo`, `view_permit`, `comments`, `likes`, `status_date`) VALUES
(48, 5, '', NULL, 'Public', NULL, 0, '2018-04-26 13:48:07'),
(49, 5, '', NULL, 'Public', NULL, 0, '2018-04-26 13:49:45'),
(50, 5, '', '709733.', 'Public', NULL, 0, '2018-04-26 14:25:01'),
(51, 5, 'Trying to\r\nupload an\r\nimage', '714306.', 'Public', NULL, 0, '2018-04-26 14:32:51'),
(52, 5, 'Lets see\r\nnow...', '362637.', 'Public', NULL, 0, '2018-04-26 14:39:04'),
(53, 5, 'jhlhb', '52.jpg', 'Public', NULL, 0, '2018-04-26 14:45:11'),
(54, 5, 'photo', 'images/53.jpg', 'Public', NULL, 0, '2018-04-26 14:52:59'),
(62, 5, 'dfgdg', 'images/61.jpg', 'Public', NULL, 1, '2018-04-27 07:21:03'),
(63, 5, 'dfgdg', '', 'Public', NULL, 0, '2018-04-26 15:47:45'),
(78, 5, '', NULL, 'Public', NULL, 0, '2018-04-27 19:17:44'),
(79, 5, 'liuhiuh;io', NULL, 'Public', NULL, 0, '2018-04-27 19:35:42'),
(80, 5, 'heres a\r\nphoto', NULL, 'Public', NULL, 0, '2018-04-27 19:36:32'),
(82, 5, 'ljlkkjk', 'images/81.jpg', 'Public', NULL, 0, '2018-04-27 19:37:35'),
(83, 5, 'iugi;', 'images/82.jpg', 'Public', NULL, 0, '2018-04-27 19:37:44'),
(84, 5, 'gligliu', NULL, 'Public', NULL, 0, '2018-04-27 20:01:41'),
(91, 5, 'HiHello', 'images/90.jpg', 'Public', NULL, 0, '2018-04-27 20:56:25'),
(92, 5, 'dfzdgzgz', NULL, 'Public', NULL, 0, '2018-04-27 22:54:09'),
(94, 5, 'This is\r\nwith Photo', 'images/93.jpg', 'Public', NULL, 0, '2018-04-27 22:56:45'),
(95, 5, 'Yes! woohoo.. it\r\nworked', NULL, 'Public', NULL, 0, '2018-04-28 20:44:14'),
(98, 5, 'Sukanya has updated her cover photo', 'images/cover5.jpg', 'Friends', NULL, 0, '2018-04-28 18:33:37'),
(99, 7, '', 'images/98.jpg', 'Public', NULL, 0, '2018-05-03 14:47:05'),
(100, 8, 'how r u ?\r\n', NULL, 'Public', NULL, 0, '2018-05-03 16:01:04'),
(101, 8, 'good', 'images/100.jpg', 'Public', NULL, 0, '2018-05-03 16:01:46'),
(102, 8, 'my photo', 'images/101.jpg', 'Public', NULL, 0, '2018-05-03 16:02:36'),
(105, 4, 'This is my\r\nsister', 'images/103.jpg', 'Public', NULL, 0, '2018-05-04 04:04:22'),
(106, 4, 'Me', 'images/105.jpg', 'Public', NULL, 0, '2018-05-07 03:28:21'),
(107, 4, 'Visited\r\nBelurmath.. last\r\nweek', 'images/106.jpg', 'Public', 7, 1, '2018-05-08 20:36:29'),
(108, 5, 'Hello\r\nShashi', NULL, 'Friends', NULL, 0, '2018-05-08 06:25:50'),
(109, 5, 'Ki re\r\nShashi ??', NULL, 'Friends', 1, 1, '2018-05-09 04:36:25'),
(112, 9, 'hello\r\nworld :D', NULL, 'Private', NULL, 0, '2018-05-08 13:53:25'),
(114, 9, 'hello..', 'images/112.jpg', 'Public', NULL, 0, '2018-05-08 14:19:22'),
(116, 4, 'life is\r\ngood', NULL, 'Public', NULL, 1, '2018-05-15 06:20:52'),
(117, 4, 'Hi', NULL, 'Public', NULL, 0, '2018-05-15 07:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `status_likes`
--

CREATE TABLE `status_likes` (
  `id` int(11) NOT NULL,
  `status_liked` int(11) NOT NULL,
  `liked_by` int(11) NOT NULL,
  `is_liked` enum('false','true') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status_likes`
--

INSERT INTO `status_likes` (`id`, `status_liked`, `liked_by`, `is_liked`) VALUES
(1, 43, 4, 'true'),
(2, 44, 4, 'false'),
(3, 44, 4, 'false'),
(4, 35, 4, 'false'),
(5, 47, 4, 'true'),
(10, 24, 5, 'false'),
(11, 25, 5, 'false'),
(12, 35, 5, 'false'),
(13, 25, 5, 'false'),
(14, 24, 5, 'false'),
(15, 62, 5, 'true'),
(16, 74, 5, 'true'),
(17, 81, 5, 'true'),
(18, 107, 9, 'true'),
(19, 109, 4, 'true'),
(20, 116, 4, 'true');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `f_name` varchar(50) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `pass` varchar(10) NOT NULL,
  `dob` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `profile_pic` varchar(256) DEFAULT NULL,
  `cover_pic` varchar(256) DEFAULT NULL,
  `intro` text NOT NULL,
  `relation` varchar(50) NOT NULL,
  `job` varchar(50) NOT NULL,
  `education` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `f_name`, `l_name`, `email`, `mobile`, `pass`, `dob`, `sex`, `profile_pic`, `cover_pic`, `intro`, `relation`, `job`, `education`, `state`, `city`) VALUES
(4, 'Shashi', 'Chakraborty', 'shashikamal0601.slg@gmail.com', '8972574345', 'sasi0601', '1993-05-10', 'Male', 'images/profile4.jpg', 'images/cover4.jpg', 'I''m a cool boy', 'Married', 'Student', 'Master of Computer Application', 'West Bengal', 'Siliguri'),
(5, 'Sukanya', 'Sarkar', 'suku@fake.com', '0', 'sukusuku', NULL, 'Female', 'images/profile5.jpg', 'images/cover5.jpg', '', '', '', '', '', ''),
(6, 'Silpa', 'Shetty', 'silpa@fake.com', '0', 'silpa0', NULL, 'Female', NULL, NULL, '', '', '', '', '', ''),
(7, 'Dibyodak', 'Pai Majumder', 'dibbo2cool@gmail.com', '0', '123456987', NULL, 'Male', NULL, NULL, '', '', '', '', '', ''),
(8, 'Nikita', 'Chakraborty', 'niki@fake.com', '0', '0123456', NULL, 'Female', 'images/profile8.jpg', NULL, '', '', '', '', '', ''),
(9, 'Sukku', 'Sarkar', 'something@gmail.com', '0', 'amimistu', NULL, 'Female', 'images/profile9.jpg', 'images/cover9.jpg', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users_friends`
--

CREATE TABLE `users_friends` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `friend_id` int(10) NOT NULL,
  `is_accepted` enum('false','true') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_friends`
--

INSERT INTO `users_friends` (`id`, `user_id`, `friend_id`, `is_accepted`) VALUES
(18, 5, 6, 'false'),
(23, 4, 5, 'true'),
(24, 8, 5, 'true'),
(25, 0, 0, 'false'),
(26, 9, 4, 'true'),
(27, 4, 9, 'true'),
(28, 5, 8, 'true'),
(29, 0, 0, 'false'),
(30, 0, 0, 'false'),
(31, 0, 0, 'false'),
(32, 0, 0, 'false'),
(35, 4, 5, 'false'),
(36, 9, 7, 'false'),
(39, 11, 4, 'true'),
(40, 4, 11, 'true'),
(41, 4, 11, 'true'),
(42, 11, 4, 'true');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`com_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `status_likes`
--
ALTER TABLE `status_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users_friends`
--
ALTER TABLE `users_friends`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `com_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `status_likes`
--
ALTER TABLE `status_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `users_friends`
--
ALTER TABLE `users_friends`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
