-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 11 2018 г., 22:16
-- Версия сервера: 5.7.19-log
-- Версия PHP: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `otake_clean`
--

-- --------------------------------------------------------

--
-- Структура таблицы `otake_bans`
--

CREATE TABLE `otake_bans` (
  `id` int(11) NOT NULL,
  `moderator` varchar(250) NOT NULL,
  `banned_user` varchar(250) NOT NULL,
  `time` varchar(50) NOT NULL,
  `reason` text NOT NULL,
  `sub` varchar(250) NOT NULL,
  `discontinued` int(1) NOT NULL DEFAULT '0',
  `discontinued_in_date` int(11) NOT NULL,
  `ban_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_comments`
--

CREATE TABLE `otake_comments` (
  `id` int(11) NOT NULL,
  `author` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `sub` varchar(250) NOT NULL,
  `parent_post` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_comments_versions`
--

CREATE TABLE `otake_comments_versions` (
  `id` int(11) NOT NULL,
  `author` varchar(250) NOT NULL,
  `editor` varchar(250) NOT NULL,
  `ver_time` int(11) NOT NULL,
  `text` text NOT NULL,
  `post_time` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_invites`
--

CREATE TABLE `otake_invites` (
  `id` int(11) NOT NULL,
  `code` varchar(250) NOT NULL,
  `parent_user` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `is_used` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_moderators`
--

CREATE TABLE `otake_moderators` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `date` int(11) NOT NULL,
  `king` varchar(250) NOT NULL,
  `sub` varchar(250) NOT NULL,
  `discontinued` int(1) NOT NULL DEFAULT '0',
  `discontinued_in_date` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_modlog`
--

CREATE TABLE `otake_modlog` (
  `id` int(11) NOT NULL,
  `moderator` varchar(250) NOT NULL,
  `type` enum('delete_psto','edit_psto','delete_comment','edit_comment','recovery_psto','recovery_comment') NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `sub` varchar(250) NOT NULL,
  `user_moderated` varchar(250) NOT NULL,
  `datetime` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_posts`
--

CREATE TABLE `otake_posts` (
  `id` int(11) NOT NULL,
  `author` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `post_text` text NOT NULL,
  `sub` varchar(250) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `bumped` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_posts_versions`
--

CREATE TABLE `otake_posts_versions` (
  `id` int(11) NOT NULL,
  `author` varchar(250) NOT NULL,
  `editor` varchar(250) NOT NULL,
  `ver_time` int(11) NOT NULL,
  `text` text NOT NULL,
  `post_time` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_subpages`
--

CREATE TABLE `otake_subpages` (
  `id` int(11) NOT NULL,
  `address` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `admin` varchar(250) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `create_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `otake_users`
--

CREATE TABLE `otake_users` (
  `id` int(11) NOT NULL,
  `login` varchar(250) NOT NULL,
  `passwd` varchar(250) NOT NULL,
  `joindate` int(11) NOT NULL,
  `join_ip` varchar(250) NOT NULL,
  `ugroup` enum('user','admin') NOT NULL,
  `email` varchar(250) NOT NULL,
  `parent_user` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `otake_bans`
--
ALTER TABLE `otake_bans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `moderator` (`moderator`),
  ADD KEY `sub` (`sub`);

--
-- Индексы таблицы `otake_comments`
--
ALTER TABLE `otake_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`),
  ADD KEY `sub` (`sub`);

--
-- Индексы таблицы `otake_comments_versions`
--
ALTER TABLE `otake_comments_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`),
  ADD KEY `editor` (`editor`),
  ADD KEY `post_id` (`post_id`);

--
-- Индексы таблицы `otake_invites`
--
ALTER TABLE `otake_invites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `parent_user` (`parent_user`);

--
-- Индексы таблицы `otake_moderators`
--
ALTER TABLE `otake_moderators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `king` (`king`),
  ADD KEY `sub` (`sub`);

--
-- Индексы таблицы `otake_modlog`
--
ALTER TABLE `otake_modlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `moderator` (`moderator`),
  ADD KEY `sub` (`sub`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_moderated` (`user_moderated`);

--
-- Индексы таблицы `otake_posts`
--
ALTER TABLE `otake_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`),
  ADD KEY `sub` (`sub`);

--
-- Индексы таблицы `otake_posts_versions`
--
ALTER TABLE `otake_posts_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author` (`author`),
  ADD KEY `editor` (`editor`),
  ADD KEY `post_id` (`post_id`);

--
-- Индексы таблицы `otake_subpages`
--
ALTER TABLE `otake_subpages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `address_2` (`address`),
  ADD KEY `address` (`address`),
  ADD KEY `admin` (`admin`);

--
-- Индексы таблицы `otake_users`
--
ALTER TABLE `otake_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `parent_user` (`parent_user`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `otake_bans`
--
ALTER TABLE `otake_bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `otake_comments`
--
ALTER TABLE `otake_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `otake_comments_versions`
--
ALTER TABLE `otake_comments_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `otake_invites`
--
ALTER TABLE `otake_invites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;
--
-- AUTO_INCREMENT для таблицы `otake_moderators`
--
ALTER TABLE `otake_moderators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `otake_modlog`
--
ALTER TABLE `otake_modlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `otake_posts`
--
ALTER TABLE `otake_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT для таблицы `otake_posts_versions`
--
ALTER TABLE `otake_posts_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `otake_subpages`
--
ALTER TABLE `otake_subpages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `otake_users`
--
ALTER TABLE `otake_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
