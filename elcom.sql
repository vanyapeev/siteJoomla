-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 27 2018 г., 17:48
-- Версия сервера: 10.1.31-MariaDB
-- Версия PHP: 5.6.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `elcom`
--

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_assets`
--

CREATE TABLE `orexv_assets` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Primary Key',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set parent.',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `level` int(10) UNSIGNED NOT NULL COMMENT 'The cached level in the nested tree.',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The unique name for the asset.\n',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The descriptive title for the asset.',
  `rules` varchar(5120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON encoded access control.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_assets`
--

INSERT INTO `orexv_assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES
(1, 0, 0, 141, 0, 'root.1', 'Root Asset', '{\"core.login.site\":{\"6\":1,\"2\":1},\"core.login.admin\":{\"6\":1},\"core.login.offline\":{\"6\":1},\"core.admin\":{\"8\":1},\"core.manage\":{\"7\":1},\"core.create\":{\"6\":1,\"3\":1},\"core.delete\":{\"6\":1},\"core.edit\":{\"6\":1,\"4\":1},\"core.edit.state\":{\"6\":1,\"5\":1},\"core.edit.own\":{\"6\":1,\"3\":1}}'),
(2, 1, 1, 2, 1, 'com_admin', 'com_admin', '{}'),
(3, 1, 3, 6, 1, 'com_banners', 'com_banners', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(4, 1, 7, 8, 1, 'com_cache', 'com_cache', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}'),
(5, 1, 9, 10, 1, 'com_checkin', 'com_checkin', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}'),
(6, 1, 11, 12, 1, 'com_config', 'com_config', '{}'),
(7, 1, 13, 16, 1, 'com_contact', 'com_contact', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}'),
(8, 1, 17, 32, 1, 'com_content', 'com_content', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}'),
(9, 1, 33, 34, 1, 'com_cpanel', 'com_cpanel', '{}'),
(10, 1, 35, 36, 1, 'com_installer', 'com_installer', '{\"core.admin\":[],\"core.manage\":{\"7\":0},\"core.delete\":{\"7\":0},\"core.edit.state\":{\"7\":0}}'),
(11, 1, 37, 38, 1, 'com_languages', 'com_languages', '{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(12, 1, 39, 40, 1, 'com_login', 'com_login', '{}'),
(13, 1, 41, 42, 1, 'com_mailto', 'com_mailto', '{}'),
(14, 1, 43, 44, 1, 'com_massmail', 'com_massmail', '{}'),
(15, 1, 45, 46, 1, 'com_media', 'com_media', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":{\"5\":1}}'),
(16, 1, 47, 48, 1, 'com_menus', 'com_menus', '{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(17, 1, 49, 50, 1, 'com_messages', 'com_messages', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}'),
(18, 1, 51, 106, 1, 'com_modules', 'com_modules', '{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(19, 1, 107, 110, 1, 'com_newsfeeds', 'com_newsfeeds', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}'),
(20, 1, 111, 112, 1, 'com_plugins', 'com_plugins', '{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(21, 1, 113, 114, 1, 'com_redirect', 'com_redirect', '{\"core.admin\":{\"7\":1},\"core.manage\":[]}'),
(22, 1, 115, 116, 1, 'com_search', 'com_search', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1}}'),
(23, 1, 117, 118, 1, 'com_templates', 'com_templates', '{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(24, 1, 119, 122, 1, 'com_users', 'com_users', '{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(26, 1, 123, 124, 1, 'com_wrapper', 'com_wrapper', '{}'),
(27, 8, 18, 27, 2, 'com_content.category.2', 'Uncategorised', '{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}'),
(28, 3, 4, 5, 2, 'com_banners.category.3', 'Uncategorised', '{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(29, 7, 14, 15, 2, 'com_contact.category.4', 'Uncategorised', '{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}'),
(30, 19, 108, 109, 2, 'com_newsfeeds.category.5', 'Uncategorised', '{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}'),
(32, 24, 120, 121, 1, 'com_users.category.7', 'Uncategorised', '{\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(33, 1, 125, 126, 1, 'com_finder', 'com_finder', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1}}'),
(34, 1, 127, 128, 1, 'com_joomlaupdate', 'com_joomlaupdate', '{\"core.admin\":[],\"core.manage\":[],\"core.delete\":[],\"core.edit.state\":[]}'),
(35, 1, 129, 130, 1, 'com_tags', 'com_tags', '{\"core.admin\":[],\"core.manage\":[],\"core.manage\":[],\"core.delete\":[],\"core.edit.state\":[]}'),
(36, 1, 131, 132, 1, 'com_contenthistory', 'com_contenthistory', '{}'),
(37, 1, 133, 134, 1, 'com_ajax', 'com_ajax', '{}'),
(38, 1, 135, 136, 1, 'com_postinstall', 'com_postinstall', '{}'),
(39, 18, 52, 53, 2, 'com_modules.module.1', 'Main Menu', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(40, 18, 54, 55, 2, 'com_modules.module.2', 'Login', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(41, 18, 56, 57, 2, 'com_modules.module.3', 'Popular Articles', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(42, 18, 58, 59, 2, 'com_modules.module.4', 'Recently Added Articles', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(43, 18, 60, 61, 2, 'com_modules.module.8', 'Toolbar', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(44, 18, 62, 63, 2, 'com_modules.module.9', 'Quick Icons', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(45, 18, 64, 65, 2, 'com_modules.module.10', 'Logged-in Users', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(46, 18, 66, 67, 2, 'com_modules.module.12', 'Admin Menu', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(47, 18, 68, 69, 2, 'com_modules.module.13', 'Admin Submenu', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(48, 18, 70, 71, 2, 'com_modules.module.14', 'User Status', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(49, 18, 72, 73, 2, 'com_modules.module.15', 'Title', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(50, 18, 74, 75, 2, 'com_modules.module.16', 'Login Form', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(51, 18, 76, 77, 2, 'com_modules.module.17', 'Breadcrumbs', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"module.edit.frontend\":[]}'),
(52, 18, 78, 79, 2, 'com_modules.module.79', 'Multilanguage status', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(53, 18, 80, 81, 2, 'com_modules.module.86', 'Joomla Version', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
(56, 1, 137, 138, 1, 'com_comprofiler', 'comprofiler', '{}'),
(57, 18, 82, 83, 2, 'com_modules.module.87', 'Вход/Регистрация', '{\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"module.edit.frontend\":[]}'),
(58, 18, 84, 85, 2, 'com_modules.module.88', 'CB Online', '{}'),
(59, 18, 86, 87, 2, 'com_modules.module.89', 'CB Workflows', '{}'),
(60, 18, 88, 89, 2, 'com_modules.module.90', 'CB Admin Dropdown Menu', '{}'),
(61, 18, 90, 91, 2, 'com_modules.module.91', 'Community Builder News', '{}'),
(62, 18, 92, 93, 2, 'com_modules.module.92', 'Community Builder Updates', '{}'),
(63, 18, 94, 95, 2, 'com_modules.module.93', 'CB Admin Version Checker', '{}'),
(64, 18, 96, 97, 2, 'com_modules.module.94', 'CB Gallery', '{}'),
(65, 18, 98, 99, 2, 'com_modules.module.95', 'CB Activity', '{}'),
(66, 18, 100, 101, 2, 'com_modules.module.96', 'CB Content', '{}'),
(67, 18, 102, 103, 2, 'com_modules.module.97', 'CB PB Latest', '{}'),
(68, 18, 104, 105, 2, 'com_modules.module.98', 'CB GroupJive', '{}'),
(69, 27, 19, 20, 3, 'com_content.article.1', 'Харрис, Харрис: Цифровая схемотехника и архитектура компьютера. Дополнение по архитектуре ARM ', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}'),
(70, 1, 139, 140, 1, 'com_jce', 'COM_JCE', '{}'),
(71, 27, 21, 22, 3, 'com_content.article.2', 'Александр Алексеев: Курсовое проектирование для криптографов. Учебное пособие ', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}'),
(72, 27, 23, 24, 3, 'com_content.article.3', 'Гулаков, Трубаков, Трубаков: Структуры и алгоритмы обработки многомерных данных', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}'),
(73, 27, 25, 26, 3, 'com_content.article.4', 'Адам Гринфилд: Радикальные технологии: устройство повседневной жизни ', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}'),
(74, 8, 28, 31, 2, 'com_content.category.8', 'Контакты', '{}'),
(75, 74, 29, 30, 3, 'com_content.article.5', 'Контакты', '{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_associations`
--

CREATE TABLE `orexv_associations` (
  `id` int(11) NOT NULL COMMENT 'A reference to the associated item.',
  `context` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The context of the associated item.',
  `key` char(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The key for the association computed from an md5 on associated ids.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_banners`
--

CREATE TABLE `orexv_banners` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `imptotal` int(11) NOT NULL DEFAULT '0',
  `impmade` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `clickurl` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `custombannercode` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sticky` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `own_prefix` tinyint(1) NOT NULL DEFAULT '0',
  `metakey_prefix` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `purchase_type` tinyint(4) NOT NULL DEFAULT '-1',
  `track_clicks` tinyint(4) NOT NULL DEFAULT '-1',
  `track_impressions` tinyint(4) NOT NULL DEFAULT '-1',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reset` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `version` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_banner_clients`
--

CREATE TABLE `orexv_banner_clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `extrainfo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `own_prefix` tinyint(4) NOT NULL DEFAULT '0',
  `metakey_prefix` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `purchase_type` tinyint(4) NOT NULL DEFAULT '-1',
  `track_clicks` tinyint(4) NOT NULL DEFAULT '-1',
  `track_impressions` tinyint(4) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_banner_tracks`
--

CREATE TABLE `orexv_banner_tracks` (
  `track_date` datetime NOT NULL,
  `track_type` int(10) UNSIGNED NOT NULL,
  `banner_id` int(10) UNSIGNED NOT NULL,
  `count` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_categories`
--

CREATE TABLE `orexv_categories` (
  `id` int(11) NOT NULL,
  `asset_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `path` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `extension` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadesc` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_categories`
--

INSERT INTO `orexv_categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(1, 0, 0, 0, 13, 0, '', 'system', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '{}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(2, 27, 1, 1, 2, 1, 'uncategorised', 'com_content', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{\"category_layout\":\"\",\"image\":\"\"}', '', '', '{\"author\":\"\",\"robots\":\"\"}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(3, 28, 1, 3, 4, 1, 'uncategorised', 'com_banners', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{\"category_layout\":\"\",\"image\":\"\"}', '', '', '{\"author\":\"\",\"robots\":\"\"}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(4, 29, 1, 5, 6, 1, 'uncategorised', 'com_contact', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{\"category_layout\":\"\",\"image\":\"\"}', '', '', '{\"author\":\"\",\"robots\":\"\"}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(5, 30, 1, 7, 8, 1, 'uncategorised', 'com_newsfeeds', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{\"category_layout\":\"\",\"image\":\"\"}', '', '', '{\"author\":\"\",\"robots\":\"\"}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(7, 32, 1, 9, 10, 1, 'uncategorised', 'com_users', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{\"category_layout\":\"\",\"image\":\"\"}', '', '', '{\"author\":\"\",\"robots\":\"\"}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(8, 74, 1, 11, 12, 1, 'kontakty', 'com_content', 'Контакты', 'kontakty', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{\"category_layout\":\"\",\"image\":\"\",\"image_alt\":\"\"}', '', '', '{\"author\":\"\",\"robots\":\"\"}', 15, '2018-09-27 15:40:28', 15, '2018-09-27 15:40:41', 0, '*', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler`
--

CREATE TABLE `orexv_comprofiler` (
  `id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(150) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `message_last_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message_number_sent` int(11) NOT NULL DEFAULT '0',
  `avatar` text,
  `avatarapproved` tinyint(4) NOT NULL DEFAULT '1',
  `canvas` text,
  `canvasapproved` tinyint(4) NOT NULL DEFAULT '1',
  `canvasposition` tinyint(4) NOT NULL DEFAULT '50',
  `approved` tinyint(4) NOT NULL DEFAULT '1',
  `confirmed` tinyint(4) NOT NULL DEFAULT '1',
  `lastupdatedate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registeripaddr` varchar(50) NOT NULL DEFAULT '',
  `cbactivation` varchar(50) NOT NULL DEFAULT '',
  `banned` tinyint(4) NOT NULL DEFAULT '0',
  `banneddate` datetime DEFAULT NULL,
  `unbanneddate` datetime DEFAULT NULL,
  `bannedby` int(11) DEFAULT NULL,
  `unbannedby` int(11) DEFAULT NULL,
  `bannedreason` mediumtext,
  `acceptedterms` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `fb_userid` varchar(255) DEFAULT NULL,
  `twitter_userid` varchar(255) DEFAULT NULL,
  `google_userid` varchar(255) DEFAULT NULL,
  `linkedin_userid` varchar(255) DEFAULT NULL,
  `windowslive_userid` varchar(255) DEFAULT NULL,
  `instagram_userid` varchar(255) DEFAULT NULL,
  `foursquare_userid` varchar(255) DEFAULT NULL,
  `github_userid` varchar(255) DEFAULT NULL,
  `vkontakte_userid` varchar(255) DEFAULT NULL,
  `steam_userid` varchar(255) DEFAULT NULL,
  `reddit_userid` varchar(255) DEFAULT NULL,
  `twitch_userid` varchar(255) DEFAULT NULL,
  `stackexchange_userid` varchar(255) DEFAULT NULL,
  `pinterest_userid` varchar(255) DEFAULT NULL,
  `amazon_userid` varchar(255) DEFAULT NULL,
  `yahoo_userid` varchar(255) DEFAULT NULL,
  `paypal_userid` varchar(255) DEFAULT NULL,
  `disqus_userid` varchar(255) DEFAULT NULL,
  `wordpress_userid` varchar(255) DEFAULT NULL,
  `meetup_userid` varchar(255) DEFAULT NULL,
  `flickr_userid` varchar(255) DEFAULT NULL,
  `vimeo_userid` varchar(255) DEFAULT NULL,
  `line_userid` varchar(255) DEFAULT NULL,
  `spotify_userid` varchar(255) DEFAULT NULL,
  `invite_code` text,
  `cb_pb_enable` text,
  `cb_pb_autopublish` text,
  `cb_pb_notifyme` text,
  `cb_pb_enable_blog` text,
  `cb_pb_enable_wall` text,
  `cb_pb_autopublish_wall` text,
  `cb_pb_notifyme_wall` text,
  `template_profile` text,
  `cb_city_feild` text,
  `cb_phone_n` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler`
--

INSERT INTO `orexv_comprofiler` (`id`, `user_id`, `alias`, `firstname`, `middlename`, `lastname`, `hits`, `message_last_sent`, `message_number_sent`, `avatar`, `avatarapproved`, `canvas`, `canvasapproved`, `canvasposition`, `approved`, `confirmed`, `lastupdatedate`, `registeripaddr`, `cbactivation`, `banned`, `banneddate`, `unbanneddate`, `bannedby`, `unbannedby`, `bannedreason`, `acceptedterms`, `fb_userid`, `twitter_userid`, `google_userid`, `linkedin_userid`, `windowslive_userid`, `instagram_userid`, `foursquare_userid`, `github_userid`, `vkontakte_userid`, `steam_userid`, `reddit_userid`, `twitch_userid`, `stackexchange_userid`, `pinterest_userid`, `amazon_userid`, `yahoo_userid`, `paypal_userid`, `disqus_userid`, `wordpress_userid`, `meetup_userid`, `flickr_userid`, `vimeo_userid`, `line_userid`, `spotify_userid`, `invite_code`, `cb_pb_enable`, `cb_pb_autopublish`, `cb_pb_notifyme`, `cb_pb_enable_blog`, `cb_pb_enable_wall`, `cb_pb_autopublish_wall`, `cb_pb_notifyme_wall`, `template_profile`, `cb_city_feild`, `cb_phone_n`) VALUES
(15, 15, NULL, NULL, NULL, NULL, 0, '0000-00-00 00:00:00', 0, NULL, 1, NULL, 1, 50, 1, 1, '0000-00-00 00:00:00', '', '', 0, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 16, NULL, '', '', 'Jhon2', 0, '0000-00-00 00:00:00', 0, NULL, 1, NULL, 1, 50, 1, 1, '0000-00-00 00:00:00', '::1', '', 0, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_fields`
--

CREATE TABLE `orexv_comprofiler_fields` (
  `fieldid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `tablecolumns` text NOT NULL,
  `table` varchar(50) NOT NULL DEFAULT '#__comprofiler',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `maxlength` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `required` tinyint(4) DEFAULT '0',
  `tabid` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `cols` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  `default` mediumtext,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `registration` tinyint(1) NOT NULL DEFAULT '0',
  `edit` tinyint(1) NOT NULL DEFAULT '1',
  `profile` tinyint(1) NOT NULL DEFAULT '1',
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  `searchable` tinyint(1) NOT NULL DEFAULT '0',
  `calculated` tinyint(1) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  `pluginid` int(11) NOT NULL DEFAULT '0',
  `cssclass` varchar(255) DEFAULT NULL,
  `params` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_fields`
--

INSERT INTO `orexv_comprofiler_fields` (`fieldid`, `name`, `tablecolumns`, `table`, `title`, `description`, `type`, `maxlength`, `size`, `required`, `tabid`, `ordering`, `cols`, `rows`, `value`, `default`, `published`, `registration`, `edit`, `profile`, `readonly`, `searchable`, `calculated`, `sys`, `pluginid`, `cssclass`, `params`) VALUES
(17, 'canvas', 'canvas,canvasapproved,canvasposition', '#__comprofiler', 'USER_CANVAS_IMAGE_TITLE', '', 'image', NULL, NULL, 0, 7, 1, NULL, NULL, NULL, NULL, 1, 0, 1, 4, 0, 0, 1, 1, 1, NULL, NULL),
(24, 'connections', '', '#__comprofiler', '_UE_CONNECTION', '', 'connections', NULL, NULL, 0, 6, 1, NULL, NULL, NULL, NULL, 1, 0, 0, 2, 1, 0, 1, 1, 1, NULL, NULL),
(25, 'hits', 'hits', '#__comprofiler', '_UE_HITS', '_UE_HITS_DESC', 'counter', NULL, NULL, 0, 6, 2, NULL, NULL, NULL, NULL, 1, 0, 0, 2, 1, 0, 1, 1, 1, NULL, NULL),
(26, 'onlinestatus', '', '#__comprofiler', '_UE_ONLINESTATUS', '', 'status', NULL, NULL, 0, 20, 2, NULL, NULL, NULL, NULL, 1, 0, 0, 4, 0, 0, 1, 1, 1, NULL, NULL),
(27, 'lastvisitDate', 'lastvisitDate', '#__users', '_UE_LASTONLINE', '', 'datetime', NULL, NULL, 0, 21, 2, NULL, NULL, NULL, NULL, 1, 0, 0, 2, 1, 0, 1, 1, 1, NULL, 'field_display_by=2'),
(28, 'registerDate', 'registerDate', '#__users', '_UE_MEMBERSINCE', '', 'datetime', NULL, NULL, 0, 21, 1, NULL, NULL, NULL, NULL, 1, 0, 0, 2, 1, 0, 1, 1, 1, NULL, 'field_display_by=6'),
(29, 'avatar', 'avatar,avatarapproved', '#__comprofiler', '_UE_IMAGE', '', 'image', NULL, NULL, 0, 20, 1, NULL, NULL, NULL, NULL, 1, 0, 1, 4, 0, 0, 1, 1, 1, NULL, NULL),
(41, 'name', 'name', '#__users', '_UE_NAME', '_UE_REGWARN_NAME', 'predefined', NULL, NULL, 1, 11, 2, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 1, 1, 1, 1, NULL, NULL),
(42, 'username', 'username', '#__users', '_UE_UNAME', '_UE_VALID_UNAME', 'predefined', NULL, NULL, 1, 11, 6, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 1, 1, 1, 1, NULL, NULL),
(44, 'acceptedterms', 'acceptedterms', '#__comprofiler', 'USER_TERMS_AND_CONDITIONS_TITLE', '', 'terms', NULL, NULL, 0, 11, 12, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1, 1, 1, NULL, NULL),
(45, 'formatname', '', '#__comprofiler', '_UE_FORMATNAME', '', 'formatname', NULL, NULL, 0, 11, 1, NULL, NULL, NULL, NULL, 1, 0, 0, 1, 1, 0, 1, 1, 1, NULL, NULL),
(46, 'firstname', 'firstname', '#__comprofiler', '_UE_YOUR_FNAME', '_UE_REGWARN_FNAME', 'predefined', NULL, NULL, 1, 11, 3, NULL, NULL, NULL, NULL, 0, 1, 1, 0, 0, 0, 1, 1, 1, NULL, NULL),
(47, 'middlename', 'middlename', '#__comprofiler', '_UE_YOUR_MNAME', '_UE_REGWARN_MNAME', 'predefined', NULL, NULL, 0, 11, 4, NULL, NULL, NULL, NULL, 0, 1, 1, 0, 0, 0, 1, 1, 1, NULL, NULL),
(48, 'lastname', 'lastname', '#__comprofiler', '_UE_YOUR_LNAME', '_UE_REGWARN_LNAME', 'predefined', NULL, NULL, 1, 11, 5, NULL, NULL, NULL, NULL, 0, 1, 1, 0, 0, 0, 1, 1, 1, NULL, NULL),
(49, 'lastupdatedate', 'lastupdatedate', '#__comprofiler', '_UE_LASTUPDATEDON', '', 'datetime', NULL, NULL, 0, 21, 3, NULL, NULL, NULL, NULL, 1, 0, 0, 2, 1, 0, 1, 1, 1, NULL, 'field_display_by=2'),
(50, 'email', 'email', '#__users', '_UE_EMAIL', '_UE_REGWARN_MAIL', 'primaryemailaddress', NULL, NULL, 1, 11, 8, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, 1, 1, NULL, NULL),
(51, 'password', 'password', '#__users', '_UE_PASS', '_UE_VALID_PASS', 'password', 50, NULL, 1, 11, 9, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, 1, 1, NULL, NULL),
(52, 'params', 'params', '#__users', '_UE_USERPARAMS', '', 'userparams', NULL, NULL, 0, 11, 10, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 1, 1, 1, NULL, NULL),
(53, 'pm', '', '#__comprofiler', '_UE_PM', '', 'pm', NULL, NULL, 0, 11, 11, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1, 1, 1, NULL, NULL),
(54, 'alias', 'alias', '#__comprofiler', 'YOUR_PROFILE_URL', 'YOUR_PROFILE_URL_DESC', 'predefined', NULL, NULL, 0, 11, 7, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1, 1, 1, NULL, NULL),
(55, 'antispam_ipaddress', '', '#__comprofiler', 'IP Address', '', 'antispam_ipaddress', NULL, NULL, 0, 11, 36, NULL, NULL, NULL, NULL, 1, 0, 0, 1, 0, 0, 0, 0, 548, NULL, NULL),
(56, 'antispam_captcha', '', '#__comprofiler', 'Captcha', '', 'antispam_captcha', NULL, NULL, 0, 11, 30, NULL, NULL, NULL, NULL, 1, 1, 0, 0, 0, 0, 0, 0, 548, NULL, NULL),
(57, 'fb_userid', 'fb_userid', '#__comprofiler', 'Facebook ID', 'Your Facebook ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 37, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(58, 'twitter_userid', 'twitter_userid', '#__comprofiler', 'Twitter ID', 'Your Twitter ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 38, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(59, 'google_userid', 'google_userid', '#__comprofiler', 'Google ID', 'Your Google ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 39, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(60, 'linkedin_userid', 'linkedin_userid', '#__comprofiler', 'LinkedIn ID', 'Your LinkedIn ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 40, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(61, 'windowslive_userid', 'windowslive_userid', '#__comprofiler', 'Windows Live ID', 'Your Windows Live ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 41, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(62, 'instagram_userid', 'instagram_userid', '#__comprofiler', 'Instagram ID', 'Your Instagram ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 42, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(63, 'foursquare_userid', 'foursquare_userid', '#__comprofiler', 'Foursquare ID', 'Your Foursquare ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 43, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(64, 'github_userid', 'github_userid', '#__comprofiler', 'GitHub ID', 'Your GitHub ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 29, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(65, 'vkontakte_userid', 'vkontakte_userid', '#__comprofiler', 'VKontakte ID', 'Your VKontakte ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 28, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(66, 'steam_userid', 'steam_userid', '#__comprofiler', 'Steam ID', 'Your Steam ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 27, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(67, 'reddit_userid', 'reddit_userid', '#__comprofiler', 'Reddit ID', 'Your Reddit ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 13, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(68, 'twitch_userid', 'twitch_userid', '#__comprofiler', 'Twitch ID', 'Your Twitch ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 14, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(69, 'stackexchange_userid', 'stackexchange_userid', '#__comprofiler', 'Stack Exchange ID', 'Your Stack Exchange ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 15, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(70, 'pinterest_userid', 'pinterest_userid', '#__comprofiler', 'Pinterest ID', 'Your Pinterest ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 16, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(71, 'amazon_userid', 'amazon_userid', '#__comprofiler', 'Amazon ID', 'Your Amazon ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 17, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(72, 'yahoo_userid', 'yahoo_userid', '#__comprofiler', 'Yahoo ID', 'Your Yahoo ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 18, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(73, 'paypal_userid', 'paypal_userid', '#__comprofiler', 'PayPal ID', 'Your PayPal ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 19, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(74, 'disqus_userid', 'disqus_userid', '#__comprofiler', 'Disqus ID', 'Your Disqus ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 20, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(75, 'wordpress_userid', 'wordpress_userid', '#__comprofiler', 'WordPress ID', 'Your WordPress ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 21, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(76, 'meetup_userid', 'meetup_userid', '#__comprofiler', 'Meetup ID', 'Your Meetup ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 22, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(77, 'flickr_userid', 'flickr_userid', '#__comprofiler', 'Flickr ID', 'Your Flickr ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 23, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(78, 'vimeo_userid', 'vimeo_userid', '#__comprofiler', 'Vimeo ID', 'Your Vimeo ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 24, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(79, 'line_userid', 'line_userid', '#__comprofiler', 'LINE ID', 'Your LINE ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 25, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(80, 'spotify_userid', 'spotify_userid', '#__comprofiler', 'Spotify ID', 'Your Spotify ID allowing API calls; if unauthorized only public calls will validate.', 'socialid', NULL, NULL, 0, 11, 26, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, 0, 0, 0, 552, NULL, NULL),
(81, 'invite_code', 'invite_code', '#__comprofiler', 'Invite Code', 'Your registration invite code.', 'invite_code', NULL, NULL, 0, 11, 31, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 1, 0, 0, 0, 558, NULL, NULL),
(82, 'privacy_profile', '', '#__comprofiler', 'Profile Privacy', 'Select your profile privacy. Profile privacy determines who can see your profile and its related information.', 'privacy_profile', NULL, NULL, 0, 11, 32, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 560, NULL, NULL),
(83, 'privacy_disable_me', '', '#__comprofiler', 'Disable My Account', 'This will disable your account and hide all profile information associated with it.', 'privacy_disable_me', NULL, NULL, 0, 11, 33, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 560, NULL, NULL),
(84, 'privacy_delete_me', '', '#__comprofiler', 'Delete My Account', 'This will delete your account and all profile information associated with it.', 'privacy_delete_me', NULL, NULL, 0, 11, 34, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 560, NULL, NULL),
(85, 'cb_pb_enable', 'cb_pb_enable', '#__comprofiler', 'Enable Profile Entries', 'Enable visitors to your profile to make comments about you and your profile.', 'radio', NULL, NULL, 0, 32, 99, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, NULL),
(86, 'cb_pb_autopublish', 'cb_pb_autopublish', '#__comprofiler', 'Auto Publish', 'Enable Auto Publish if you want entries submitted to be automatically approved and displayed on your profile.', 'radio', NULL, NULL, 0, 32, 99, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, NULL),
(87, 'cb_pb_notifyme', 'cb_pb_notifyme', '#__comprofiler', 'Notify Me', 'Enable Notify Me if you\'d like to receive an email notification each time someone submits an entry.  This is recommended if you are not using the Auto Publish feature.', 'radio', NULL, NULL, 0, 32, 99, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, NULL),
(88, 'cb_pb_enable_blog', 'cb_pb_enable_blog', '#__comprofiler', 'Enable Profile Blog', 'Enable your blog on your profile.', 'radio', NULL, NULL, 0, 33, 99, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, NULL),
(89, 'cb_pb_enable_wall', 'cb_pb_enable_wall', '#__comprofiler', 'Enable Profile Wall', 'Enable the wall on your profile so yourself and visitors can write on it.', 'radio', NULL, NULL, 0, 34, 99, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, NULL),
(90, 'cb_pb_autopublish_wall', 'cb_pb_autopublish_wall', '#__comprofiler', 'Auto Publish', 'Enable Auto Publish if you want entries submitted to be automatically approved and displayed on your profile.', 'radio', NULL, NULL, 0, 34, 99, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, NULL),
(91, 'cb_pb_notifyme_wall', 'cb_pb_notifyme_wall', '#__comprofiler', 'Notify Me', 'Enable Notify Me if you\'d like to receive an email notification each time someone submits an entry.  This is recommended if you are not using the Auto Publish feature.', 'radio', NULL, NULL, 0, 34, 99, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 1, NULL, NULL),
(92, 'cb_pb_profile_rating', '', '#__comprofiler', 'Rating', '', 'pb_profile_rating', NULL, NULL, 0, 21, 99, NULL, NULL, NULL, NULL, 1, 0, 0, 1, 1, 0, 1, 0, 562, NULL, NULL),
(93, 'template_profile', 'template_profile', '#__comprofiler', 'Profile Template', '', 'templatechanger_cb', NULL, NULL, 0, 11, 35, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, 0, 567, NULL, NULL),
(94, 'cb_city_feild', 'cb_city_feild', '#__comprofiler', 'CITY_FEILD', '', 'text', 0, 0, 0, 11, 44, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\",\"cbconditional_conditioned\":\"0\",\"cbconditional_conditions\":[{\"condition\":[{\"field\":\"\",\"field_custom\":\"\",\"field_custom_translate\":\"0\",\"operator_viewaccesslevels\":\"0\",\"field_viewaccesslevels\":\"\",\"operator_usergroups\":\"0\",\"field_usergroups\":\"\",\"operator_languages\":\"12\",\"field_languages\":\"\",\"operator_moderators\":\"0\",\"operator_users\":\"12\",\"field_users\":\"\",\"operator\":\"0\",\"delimiter\":\"\",\"value\":\"\",\"value_translate\":\"0\",\"location_registration\":\"1\",\"location_profile_edit\":\"1\",\"location_profile_view\":\"1\",\"location_userlist_search\":\"0\",\"location_userlist_view\":\"1\"}]}],\"cbconditional_debug\":\"0\",\"ajax_template\":\"\",\"ajax_profile\":\"0\",\"ajax_profile_access\":\"2\",\"ajax_profile_output\":\"1\",\"ajax_list\":\"0\",\"ajax_list_access\":\"2\",\"ajax_list_output\":\"2\",\"ajax_placeholder\":\"\",\"ajax_update\":\"\",\"cbprivacy_display\":\"0\",\"cbprivacy_display_override\":\"\",\"cbprivacy_display_reg\":\"0\",\"cbprivacy_edit\":\"0\",\"cbprivacy_edit_access\":\"1\",\"cbprivacy_edit_group\":\"2\",\"cbprivacy_edit_override\":\"\",\"privacy_template\":\"-1\",\"privacy_layout\":\"-1\",\"privacy_ajax\":\"-1\",\"privacy_options_default\":\"-1\",\"privacy_options_visible\":\"-1\",\"privacy_options_users\":\"-1\",\"privacy_options_invisible\":\"-1\",\"privacy_options_conn\":\"-1\",\"privacy_options_connofconn\":\"-1\",\"privacy_options_conntype\":\"-1\",\"privacy_options_conntypes\":\"-1\",\"privacy_options_viewaccesslevel\":\"-1\",\"privacy_options_viewaccesslevels\":\"-1\",\"privacy_options_usergroup\":\"-1\",\"privacy_options_usergroups\":\"-1\",\"code_validate\":\"0\",\"code_validate_code\":\"\",\"code_validate_success\":\"\",\"code_validate_error\":\"Not a valid input.\",\"code_validate_ajax\":\"0\",\"qry_validate\":\"0\",\"qry_validate_query\":\"\",\"qry_validate_mode\":\"0\",\"qry_validate_host\":\"\",\"qry_validate_username\":\"\",\"qry_validate_password\":\"\",\"qry_validate_database\":\"\",\"qry_validate_charset\":\"\",\"qry_validate_prefix\":\"\",\"qry_validate_on\":\"0\",\"qry_validate_success\":\"\",\"qry_validate_error\":\"Not a valid input.\",\"qry_validate_ajax\":\"0\"}'),
(95, 'cb_phone_n', 'cb_phone_n', '#__comprofiler', 'PHONE_N', '', 'text', 0, 0, 0, 11, 45, NULL, NULL, NULL, '', 1, 1, 1, 1, 0, 0, 0, 0, 1, '', '{\"fieldLayout\":\"\",\"fieldLayoutEdit\":\"\",\"fieldLayoutList\":\"\",\"fieldLayoutSearch\":\"\",\"fieldLayoutRegister\":\"\",\"fieldLayoutContentPlugins\":\"0\",\"fieldLayoutIcons\":\"\",\"fieldPlaceholder\":\"\",\"fieldMinLength\":\"0\",\"fieldValidateExpression\":\"\",\"pregexp\":\"\\/^.*$\\/\",\"pregexperror\":\"Not a valid input\",\"fieldValidateForbiddenList_register\":\"http:,https:,mailto:,\\/\\/.[url],<a,<\\/a>,&#\",\"fieldValidateForbiddenList_edit\":\"\",\"cbconditional_conditioned\":\"0\",\"cbconditional_conditions\":[{\"condition\":[{\"field\":\"\",\"field_custom\":\"\",\"field_custom_translate\":\"0\",\"operator_viewaccesslevels\":\"0\",\"field_viewaccesslevels\":\"\",\"operator_usergroups\":\"0\",\"field_usergroups\":\"\",\"operator_languages\":\"12\",\"field_languages\":\"\",\"operator_moderators\":\"0\",\"operator_users\":\"12\",\"field_users\":\"\",\"operator\":\"0\",\"delimiter\":\"\",\"value\":\"\",\"value_translate\":\"0\",\"location_registration\":\"1\",\"location_profile_edit\":\"1\",\"location_profile_view\":\"1\",\"location_userlist_search\":\"0\",\"location_userlist_view\":\"1\"}]}],\"cbconditional_debug\":\"0\",\"ajax_template\":\"\",\"ajax_profile\":\"0\",\"ajax_profile_access\":\"2\",\"ajax_profile_output\":\"1\",\"ajax_list\":\"0\",\"ajax_list_access\":\"2\",\"ajax_list_output\":\"2\",\"ajax_placeholder\":\"\",\"ajax_update\":\"\",\"cbprivacy_display\":\"0\",\"cbprivacy_display_override\":\"\",\"cbprivacy_display_reg\":\"0\",\"cbprivacy_edit\":\"0\",\"cbprivacy_edit_access\":\"1\",\"cbprivacy_edit_group\":\"2\",\"cbprivacy_edit_override\":\"\",\"privacy_template\":\"-1\",\"privacy_layout\":\"-1\",\"privacy_ajax\":\"-1\",\"privacy_options_default\":\"-1\",\"privacy_options_visible\":\"-1\",\"privacy_options_users\":\"-1\",\"privacy_options_invisible\":\"-1\",\"privacy_options_conn\":\"-1\",\"privacy_options_connofconn\":\"-1\",\"privacy_options_conntype\":\"-1\",\"privacy_options_conntypes\":\"-1\",\"privacy_options_viewaccesslevel\":\"-1\",\"privacy_options_viewaccesslevels\":\"-1\",\"privacy_options_usergroup\":\"-1\",\"privacy_options_usergroups\":\"-1\",\"code_validate\":\"0\",\"code_validate_code\":\"\",\"code_validate_success\":\"\",\"code_validate_error\":\"Not a valid input.\",\"code_validate_ajax\":\"0\",\"qry_validate\":\"0\",\"qry_validate_query\":\"\",\"qry_validate_mode\":\"0\",\"qry_validate_host\":\"\",\"qry_validate_username\":\"\",\"qry_validate_password\":\"\",\"qry_validate_database\":\"\",\"qry_validate_charset\":\"\",\"qry_validate_prefix\":\"\",\"qry_validate_on\":\"0\",\"qry_validate_success\":\"\",\"qry_validate_error\":\"Not a valid input.\",\"qry_validate_ajax\":\"0\"}');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_field_values`
--

CREATE TABLE `orexv_comprofiler_field_values` (
  `fieldvalueid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(255) NOT NULL DEFAULT '',
  `fieldlabel` varchar(255) NOT NULL DEFAULT '',
  `fieldgroup` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_field_values`
--

INSERT INTO `orexv_comprofiler_field_values` (`fieldvalueid`, `fieldid`, `fieldtitle`, `fieldlabel`, `fieldgroup`, `ordering`, `sys`) VALUES
(16, 85, '_UE_YES', '', 0, 1, 0),
(17, 85, '_UE_NO', '', 0, 2, 0),
(18, 86, '_UE_YES', '', 0, 1, 0),
(19, 86, '_UE_NO', '', 0, 2, 0),
(20, 87, '_UE_YES', '', 0, 1, 0),
(21, 87, '_UE_NO', '', 0, 2, 0),
(22, 88, '_UE_YES', '', 0, 1, 0),
(23, 88, '_UE_NO', '', 0, 2, 0),
(24, 89, '_UE_YES', '', 0, 1, 0),
(25, 89, '_UE_NO', '', 0, 2, 0),
(26, 90, '_UE_YES', '', 0, 1, 0),
(27, 90, '_UE_NO', '', 0, 2, 0),
(28, 91, '_UE_YES', '', 0, 1, 0),
(29, 91, '_UE_NO', '', 0, 2, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_lists`
--

CREATE TABLE `orexv_comprofiler_lists` (
  `listid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `viewaccesslevel` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `usergroupids` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_members`
--

CREATE TABLE `orexv_comprofiler_members` (
  `referenceid` int(11) NOT NULL DEFAULT '0',
  `memberid` int(11) NOT NULL DEFAULT '0',
  `accepted` tinyint(1) NOT NULL DEFAULT '1',
  `pending` tinyint(1) NOT NULL DEFAULT '0',
  `membersince` date NOT NULL DEFAULT '0000-00-00',
  `reason` mediumtext,
  `description` varchar(255) DEFAULT NULL,
  `type` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin`
--

CREATE TABLE `orexv_comprofiler_plugin` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `element` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(100) DEFAULT '',
  `folder` varchar(100) DEFAULT '',
  `viewaccesslevel` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `backend_menu` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `iscore` tinyint(3) NOT NULL DEFAULT '0',
  `client_id` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_plugin`
--

INSERT INTO `orexv_comprofiler_plugin` (`id`, `name`, `element`, `type`, `folder`, `viewaccesslevel`, `backend_menu`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'CB Core', 'cb.core', 'user', 'plug_cbcore', 1, '', 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '{\"name_style\":\"1\",\"name_format\":\"3\",\"date_format\":\"m\\/d\\/Y\",\"time_format\":\"H:i:s\",\"calendar_type\":\"2\",\"allow_email_display\":\"3\",\"allow_email_public\":\"0\",\"allow_email_replyto\":\"1\",\"allow_email\":\"1\",\"allow_website\":\"1\",\"allow_onlinestatus\":\"1\",\"icons_display\":\"3\",\"login_type\":\"0\",\"reg_admin_allowcbregistration\":\"1\",\"emailpass\":\"0\",\"reg_admin_approval\":\"0\",\"reg_confirmation\":\"0\",\"reg_username_checker\":\"0\",\"reg_ipaddress\":\"1\",\"reg_show_login_on_page\":\"0\",\"reg_email_name\":\"REGISTRATION_EMAIL_FROM_NAME\",\"reg_email_from\":\"\",\"reg_email_replyto\":\"\",\"reg_email_html\":\"0\",\"reg_pend_appr_sub\":\"YOUR_REGISTRATION_IS_PENDING_APPROVAL_SUBJECT\",\"reg_pend_appr_msg\":\"YOUR_REGISTRATION_IS_PENDING_APPROVAL_MESSAGE\",\"reg_welcome_sub\":\"YOUR_REGISTRATION_IS_APPROVED_SUBJECT\",\"reg_welcome_msg\":\"YOUR_REGISTRATION_IS_APPROVED_MESSAGE\",\"reg_layout\":\"flat\",\"reg_show_icons_explain\":\"0\",\"reg_title_img\":\"general\",\"reg_intro_msg\":\"REGISTRATION_GREETING\",\"reg_conclusion_msg\":\"REGISTRATION_CONCLUSION\",\"reg_first_visit_url\":\"index.php?option=com_comprofiler&view=userprofile\",\"usernameedit\":\"0\",\"usernamefallback\":\"name\",\"adminrequiredfields\":\"1\",\"profile_viewaccesslevel\":\"2\",\"maxEmailsPerHr\":\"10\",\"profile_recordviews\":\"1\",\"minHitsInterval\":\"60\",\"templatedir\":\"default\",\"use_divs\":\"1\",\"left2colsWidth\":\"40\",\"left3colsWidth\":\"32\",\"right3colsWidth\":\"32\",\"showEmptyTabs\":\"1\",\"showEmptyFields\":\"1\",\"emptyFieldsText\":\"-\",\"frontend_userparams\":\"1\",\"profile_edit_layout\":\"tabbed\",\"profile_edit_show_icons_explain\":\"0\",\"html_filter_allowed_tags\":\"\",\"conversiontype\":\"0\",\"avatarResizeAlways\":\"0\",\"avatarHeight\":\"500\",\"avatarWidth\":\"200\",\"avatarSize\":\"2000\",\"thumbHeight\":\"86\",\"thumbWidth\":\"60\",\"avatarMaintainRatio\":\"1\",\"moderator_viewaccesslevel\":\"3\",\"allowModUserApproval\":\"0\",\"moderatorEmail\":\"0\",\"allowUserReports\":\"0\",\"avatarUploadApproval\":\"0\",\"allowModeratorsUserEdit\":\"0\",\"allowUserBanning\":\"1\",\"allowConnections\":\"1\",\"connectionDisplay\":\"0\",\"connectionPath\":\"1\",\"useMutualConnections\":\"1\",\"conNotifyType\":\"0\",\"autoAddConnections\":\"1\",\"connection_categories\":\"Friend\\r\\nCo Worker\\r\\nFamily\",\"translations_debug\":\"0\",\"enableSpoofCheck\":\"1\",\"noVersionCheck\":\"0\",\"pluginVersionCheck\":\"1\",\"installFromWeb\":\"1\",\"sendemails\":\"1\",\"templateBootstrap\":\"1\",\"templateFontawesme\":\"1\",\"jsJquery\":\"1\",\"jsJqueryMigrate\":\"1\",\"poweredBy\":\"1\",\"footerplugin\":\"1\"}'),
(2, 'CB Connections', 'cb.connections', 'user', 'plug_cbconnections', 1, '', 3, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(7, 'Default', 'default', 'templates', 'default', 1, '', 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(13, 'Default language (English)', 'default_language', 'language', 'default_language', 1, '', 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(14, 'CB Menu', 'cb.menu', 'user', 'plug_cbmenu', 1, '', 2, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(15, 'Private Message System', 'pms.mypmspro', 'user', 'plug_pms_mypmspro', 1, '', 8, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(17, 'CB Articles', 'cbarticles', 'user', 'plug_cbarticles', 1, '', 8, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(18, 'CB Forums', 'cbforums', 'user', 'plug_cbforums', 1, '', 9, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(19, 'CB Blogs', 'cbblogs', 'user', 'plug_cbblogs', 1, '', 10, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(500, 'CB Paid Subscriptions - en-GB', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-en-gb', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(501, 'CB Paid Subscriptions - en-US', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-en-us', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(502, 'en-GB', 'en-GB', 'language', 'en-gb', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(503, 'en-US', 'en-US', 'language', 'en-us', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(504, 'sq-AL', 'sq-AL', 'language', 'sq-al', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(505, 'az-AZ', 'az-AZ', 'language', 'az-az', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(506, 'zh-CN', 'zh-CN', 'language', 'zh-cn', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(507, 'zh-TW', 'zh-TW', 'language', 'zh-tw', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(508, 'hr-HR', 'hr-HR', 'language', 'hr-hr', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(509, 'CB Paid Subscriptions - da-DK', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-da-dk', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(510, 'da-DK', 'da-DK', 'language', 'da-dk', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(511, 'nl-BE', 'nl-BE', 'language', 'nl-be', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(512, 'CB Paid Subscriptions - nl-NL', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-nl-nl', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(513, 'nl-NL', 'nl-NL', 'language', 'nl-nl', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(514, 'et-EE', 'et-EE', 'language', 'et-ee', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(515, 'CB Paid Subscriptions - fr-FR', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-fr-fr', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(516, 'fr-FR', 'fr-FR', 'language', 'fr-fr', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(517, 'fi-FI', 'fi-FI', 'language', 'fi-fi', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(518, 'CB Paid Subscriptions - de-DE', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-de-de', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(519, 'de-DE', 'de-DE', 'language', 'de-de', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(520, 'CB Paid Subscriptions - el-GR', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-el-gr', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(521, 'el-GR', 'el-GR', 'language', 'el-gr', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(522, 'hi-IN', 'hi-IN', 'language', 'hi-in', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(523, 'hu-HU', 'hu-HU', 'language', 'hu-hu', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(524, 'id-ID', 'id-ID', 'language', 'id-id', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(525, 'CB Paid Subscriptions - it-IT', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-it-it', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(526, 'it-IT', 'it-IT', 'language', 'it-it', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(527, 'CB Paid Subscriptions - ja-JP', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-ja-jp', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(528, 'ja-JP', 'ja-JP', 'language', 'ja-jp', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(529, 'lv-LV', 'lv-LV', 'language', 'lv-lv', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(530, 'lt-LT', 'lt-LT', 'language', 'lt-lt', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(531, 'nb-NO', 'nb-NO', 'language', 'nb-no', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(532, 'fa-IR', 'fa-IR', 'language', 'fa-ir', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(533, 'pl-PL', 'pl-PL', 'language', 'pl-pl', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(534, 'pt-BR', 'pt-BR', 'language', 'pt-br', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(535, 'pt-PT', 'pt-PT', 'language', 'pt-pt', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(536, 'ro-RO', 'ro-RO', 'language', 'ro-ro', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(537, 'CB Paid Subscriptions - ru-RU', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-ru-ru', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(538, 'ru-RU', 'ru-RU', 'language', 'ru-ru', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(539, 'sk-SK', 'sk-SK', 'language', 'sk-sk', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(540, 'CB Paid Subscriptions - es-ES', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-es-es', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(541, 'es-ES', 'es-ES', 'language', 'es-es', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(542, 'CB Paid Subscriptions - sl_SI', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-sl_si', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(543, 'CB Paid Subscriptions - pl-PL', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-pl-pl', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(544, 'CB Paid Subscriptions - sv-SE', 'cbpaidsubscriptions_language', 'language', 'cbpaidsubscriptions-sv-se', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(545, 'sv-SE', 'sv-SE', 'language', 'sv-se', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(546, 'tr-TR', 'tr-TR', 'language', 'tr-tr', 1, '', 99, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(547, 'CB Activity', 'cbactivity', 'user', 'plug_cbactivity', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(548, 'CB AntiSpam', 'cbantispam', 'user', 'plug_cbantispam', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(549, 'CB Auto Actions', 'cbautoactions', 'user', 'plug_cbautoactions', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(550, 'CB Auto Welcome', 'cb.autowelcome', 'user', 'plug_cbautowelcome', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(551, 'CB Conditional', 'cbconditional', 'user', 'plug_cbconditional', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(552, 'CB Connect', 'cbconnect', 'user', 'plug_cbconnect', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(553, 'CB Core Fields Ajax', 'cbcorefieldsajax', 'user', 'plug_cbcorefieldsajax', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(554, 'CB Footer', 'cbfooter', 'user', 'plug_cbfooter', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(555, 'CB Gallery', 'cbgallery', 'user', 'plug_cbgallery', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(556, 'CB Google Translate', 'cbgoogletranslate', 'user', 'plug_cbgoogletranslate', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(557, 'CB Gravatar Field', 'cbgravatarfield', 'user', 'plug_cbgravatarfield', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(558, 'CB Invites', 'cbinvites', 'user', 'plug_cbinvites', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(559, 'CB Last Views Tab', 'cb.lastviews', 'user', 'plug_cblastviewstab', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(560, 'CB Privacy', 'cbprivacy', 'user', 'plug_cbprivacy', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(561, 'CB Profile Update Logger', 'cb.pulog', 'user', 'plug_cbprofileupdatelogger', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(562, 'CB ProfileBook', 'cb.profilebook', 'user', 'plug_cbprofilebook', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(563, 'CB Progress Field', 'cbprogressfield', 'user', 'plug_cbprogressfield', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(564, 'CB Code Field', 'cbcodefield', 'user', 'plug_cbcodefield', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(565, 'CB Query Field', 'cbqueryfield', 'user', 'plug_cbqueryfield', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(566, 'CB Reconfirm Email', 'cbreconfirmemail', 'user', 'plug_cbreconfirmemail', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(567, 'CB Template Changer', 'cbtemplatechanger', 'user', 'plug_cbtemplatechanger', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(568, 'CB GroupJive', 'cbgroupjive', 'user', 'plug_cbgroupjive', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(569, 'CB GroupJive About', 'cbgroupjiveabout', 'user/plug_cbgroupjive/plugins', 'cbgroupjiveabout', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(570, 'CB GroupJive Events', 'cbgroupjiveevents', 'user/plug_cbgroupjive/plugins', 'cbgroupjiveevents', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(571, 'CB GroupJive File', 'cbgroupjivefile', 'user/plug_cbgroupjive/plugins', 'cbgroupjivefile', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(572, 'CB GroupJive Forums', 'cbgroupjiveforums', 'user/plug_cbgroupjive/plugins', 'cbgroupjiveforums', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(573, 'CB GroupJive Photo', 'cbgroupjivephoto', 'user/plug_cbgroupjive/plugins', 'cbgroupjivephoto', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(574, 'CB GroupJive Video', 'cbgroupjivevideo', 'user/plug_cbgroupjive/plugins', 'cbgroupjivevideo', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(575, 'CB GroupJive Wall', 'cbgroupjivewall', 'user/plug_cbgroupjive/plugins', 'cbgroupjivewall', 1, '', 99, 0, 0, 0, 0, '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity`
--

CREATE TABLE `orexv_comprofiler_plugin_activity` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `title` text,
  `message` text,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_actions`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_actions` (
  `id` int(11) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '99999',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_plugin_activity_actions`
--

INSERT INTO `orexv_comprofiler_plugin_activity_actions` (`id`, `value`, `title`, `description`, `icon`, `class`, `published`, `ordering`, `params`) VALUES
(1, 'Feeling', 'feeling', 'How are you feeling?', NULL, 'fa fa-smile-o', 1, 1, NULL),
(2, 'Watching', 'watching', 'What are you watching?', NULL, 'fa fa-ticket', 1, 2, NULL),
(3, 'Reading', 'reading', 'What are you reading?', NULL, 'fa fa-book', 1, 3, NULL),
(4, 'Listening To', 'listening to', 'What are you listening to?', NULL, 'fa fa-headphones', 1, 4, NULL),
(5, 'Drinking', 'drinking', 'What are you drinking?', NULL, 'fa fa-coffee', 1, 5, NULL),
(6, 'Eating', 'eating', 'What are you eating?', NULL, 'fa fa-cutlery', 1, 6, NULL),
(7, 'Playing', 'playing', 'What are you playing?', NULL, 'fa fa-gamepad', 1, 7, NULL),
(8, 'Traveling To', 'traveling to', 'Where are you going?', NULL, 'fa fa-plane', 1, 8, NULL),
(9, 'Looking For', 'looking for', 'What are you looking for?', NULL, 'fa fa-search', 1, 9, NULL),
(10, 'Celebrating', 'celebrating', 'What are you celebrating?', NULL, 'fa fa-birthday-cake', 1, 10, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_comments`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `message` text,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_emotes`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_emotes` (
  `id` int(11) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '99999',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_plugin_activity_emotes`
--

INSERT INTO `orexv_comprofiler_plugin_activity_emotes` (`id`, `value`, `icon`, `class`, `published`, `ordering`, `params`) VALUES
(1, 'smile', NULL, 'twemoji twemoji-1f604', 1, 1, NULL),
(2, 'laughing', NULL, 'twemoji twemoji-1f606', 1, 2, NULL),
(3, 'blush', NULL, 'twemoji twemoji-1f60a', 1, 3, NULL),
(4, 'smiley', NULL, 'twemoji twemoji-1f603', 1, 4, NULL),
(5, 'relaxed', NULL, 'twemoji twemoji-263a', 1, 5, NULL),
(6, 'smirk', NULL, 'twemoji twemoji-1f60f', 1, 6, NULL),
(7, 'heart_eyes', NULL, 'twemoji twemoji-1f60d', 1, 7, NULL),
(8, 'kissing_heart', NULL, 'twemoji twemoji-1f618', 1, 8, NULL),
(9, 'kissing_closed_eyes', NULL, 'twemoji twemoji-1f61a', 1, 9, NULL),
(10, 'flushed', NULL, 'twemoji twemoji-1f633', 1, 10, NULL),
(11, 'relieved', NULL, 'twemoji twemoji-1f625', 1, 11, NULL),
(12, 'satisfied', NULL, 'twemoji twemoji-1f60c', 1, 12, NULL),
(13, 'grin', NULL, 'twemoji twemoji-1f601', 1, 13, NULL),
(14, 'wink', NULL, 'twemoji twemoji-1f609', 1, 14, NULL),
(15, 'stuck_out_tongue_winking_eye', NULL, 'twemoji twemoji-1f61c', 1, 15, NULL),
(16, 'stuck_out_tongue_closed_eyes', NULL, 'twemoji twemoji-1f61d', 1, 16, NULL),
(17, 'grinning', NULL, 'twemoji twemoji-1f600', 1, 17, NULL),
(18, 'kissing', NULL, 'twemoji twemoji-1f617', 1, 18, NULL),
(19, 'kissing_smiling_eyes', NULL, 'twemoji twemoji-1f619', 1, 19, NULL),
(20, 'stuck_out_tongue', NULL, 'twemoji twemoji-1f61b', 1, 20, NULL),
(21, 'sleeping', NULL, 'twemoji twemoji-1f634', 1, 21, NULL),
(22, 'worried', NULL, 'twemoji twemoji-1f61f', 1, 22, NULL),
(23, 'frowning', NULL, 'twemoji twemoji-1f626', 1, 23, NULL),
(24, 'anguished', NULL, 'twemoji twemoji-1f627', 1, 24, NULL),
(25, 'open_mouth', NULL, 'twemoji twemoji-1f62e', 1, 25, NULL),
(26, 'grimacing', NULL, 'twemoji twemoji-1f62c', 1, 26, NULL),
(27, 'confused', NULL, 'twemoji twemoji-1f615', 1, 27, NULL),
(28, 'hushed', NULL, 'twemoji twemoji-1f62f', 1, 28, NULL),
(29, 'expressionless', NULL, 'twemoji twemoji-1f611', 1, 29, NULL),
(30, 'unamused', NULL, 'twemoji twemoji-1f612', 1, 30, NULL),
(31, 'sweat_smile', NULL, 'twemoji twemoji-1f605', 1, 31, NULL),
(32, 'sweat', NULL, 'twemoji twemoji-1f613', 1, 32, NULL),
(33, 'weary', NULL, 'twemoji twemoji-1f629', 1, 33, NULL),
(34, 'pensive', NULL, 'twemoji twemoji-1f614', 1, 34, NULL),
(35, 'disappointed', NULL, 'twemoji twemoji-1f61e', 1, 35, NULL),
(36, 'confounded', NULL, 'twemoji twemoji-1f616', 1, 36, NULL),
(37, 'fearful', NULL, 'twemoji twemoji-1f628', 1, 37, NULL),
(38, 'cold_sweat', NULL, 'twemoji twemoji-1f630', 1, 38, NULL),
(39, 'persevere', NULL, 'twemoji twemoji-1f623', 1, 39, NULL),
(40, 'cry', NULL, 'twemoji twemoji-1f622', 1, 40, NULL),
(41, 'sob', NULL, 'twemoji twemoji-1f62d', 1, 41, NULL),
(42, 'joy', NULL, 'twemoji twemoji-1f602', 1, 42, NULL),
(43, 'astonished', NULL, 'twemoji twemoji-1f632', 1, 43, NULL),
(44, 'scream', NULL, 'twemoji twemoji-1f631', 1, 44, NULL),
(45, 'tired_face', NULL, 'twemoji twemoji-1f62b', 1, 45, NULL),
(46, 'angry', NULL, 'twemoji twemoji-1f620', 1, 46, NULL),
(47, 'rage', NULL, 'twemoji twemoji-1f621', 1, 47, NULL),
(48, 'triumph', NULL, 'twemoji twemoji-1f624', 1, 48, NULL),
(49, 'sleepy', NULL, 'twemoji twemoji-1f62a', 1, 49, NULL),
(50, 'yum', NULL, 'twemoji twemoji-1f60b', 1, 50, NULL),
(51, 'mask', NULL, 'twemoji twemoji-1f637', 1, 51, NULL),
(52, 'sunglasses', NULL, 'twemoji twemoji-1f60e', 1, 52, NULL),
(53, 'dizzy_face', NULL, 'twemoji twemoji-1f635', 1, 53, NULL),
(54, 'neutral_face', NULL, 'twemoji twemoji-1f610', 1, 54, NULL),
(55, 'no_mouth', NULL, 'twemoji twemoji-1f636', 1, 55, NULL),
(56, 'innocent', NULL, 'twemoji twemoji-1f607', 1, 56, NULL),
(57, 'beer', NULL, 'twemoji twemoji-1f37a', 1, 57, NULL),
(58, 'coffee', NULL, 'twemoji twemoji-2615', 1, 58, NULL),
(59, 'tada', NULL, 'twemoji twemoji-1f389', 1, 59, NULL),
(60, 'zzz', NULL, 'twemoji twemoji-1f4a4', 1, 60, NULL),
(61, 'fork_and_knife', NULL, 'twemoji twemoji-1f374', 1, 61, NULL),
(62, 'birthday', NULL, 'twemoji twemoji-1f382', 1, 62, NULL),
(63, 'heart', NULL, 'fa fa-lg fa-heart text-danger', 1, 63, NULL),
(64, 'thumbsup', NULL, 'fa fa-lg fa-thumbs-o-up text-primary', 1, 64, NULL),
(65, 'thumbsdown', NULL, 'fa fa-lg fa-thumbs-o-down text-primary', 1, 65, NULL),
(66, 'car', NULL, 'fa fa-lg fa-car text-info', 1, 66, NULL),
(67, 'bell', NULL, 'fa fa-lg fa-bell-o text-warning', 1, 67, NULL),
(68, 'bomb', NULL, 'fa fa-lg fa-bomb text-default', 1, 68, NULL),
(69, 'book', NULL, 'fa fa-lg fa-book text-default', 1, 69, NULL),
(70, 'bus', NULL, 'fa fa-lg fa-bus text-info', 1, 70, NULL),
(71, 'taxi', NULL, 'fa fa-lg fa-taxi text-warning', 1, 71, NULL),
(72, 'train', NULL, 'fa fa-lg fa-train text-info', 1, 72, NULL),
(73, 'plane', NULL, 'fa fa-lg fa-plane text-info', 1, 73, NULL),
(74, 'music', NULL, 'fa fa-lg fa-music text-primary', 1, 74, NULL),
(75, 'soccer', NULL, 'fa fa-lg fa-futbol-o text-default', 1, 75, NULL),
(76, 'snow', NULL, 'fa fa-lg fa-snowflake-o text-primary', 1, 76, NULL),
(77, 'paperclip', NULL, 'fa fa-lg fa-paperclip text-default', 1, 77, NULL),
(78, 'bicycle', NULL, 'fa fa-lg fa-bicycle text-info', 1, 78, NULL),
(79, 'motorcycle', NULL, 'fa fa-lg fa-motorcycle text-info', 1, 79, NULL),
(80, 'recycle', NULL, 'fa fa-lg fa-recycle text-primary', 1, 80, NULL),
(81, 'gift', NULL, 'fa fa-lg fa-gift text-danger', 1, 81, NULL),
(82, 'camera', NULL, 'fa fa-lg fa-camera-retro text-info', 1, 82, NULL),
(83, 'globe', NULL, 'fa fa-lg fa-globe text-primary', 1, 83, NULL),
(84, 'idea', NULL, 'fa fa-lg fa-lightbulb-o text-warning', 1, 84, NULL),
(85, 'paw', NULL, 'fa fa-lg fa-paw text-default', 1, 85, NULL),
(86, 'shopping', NULL, 'fa fa-lg fa-shopping-bag text-info', 1, 86, NULL),
(87, 'phone', NULL, 'fa fa-lg fa-phone text-success', 1, 87, NULL),
(88, 'tree', NULL, 'fa fa-lg fa-tree text-success', 1, 88, NULL),
(89, 'trophy', NULL, 'fa fa-lg fa-trophy text-warning', 1, 89, NULL),
(90, 'umbrella', NULL, 'fa fa-lg fa-umbrella text-info', 1, 90, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_following`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_following` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_hidden`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_hidden` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT '',
  `item` varchar(255) NOT NULL DEFAULT '',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_likes`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_likes` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_like_types`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_like_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '99999',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_plugin_activity_like_types`
--

INSERT INTO `orexv_comprofiler_plugin_activity_like_types` (`id`, `value`, `icon`, `class`, `published`, `ordering`, `params`) VALUES
(1, 'Like', NULL, 'fa fa-lg fa-thumbs-o-up text-primary', 1, 1, NULL),
(2, 'Love', NULL, 'fa fa-lg fa-heart text-danger', 1, 2, NULL),
(3, 'Funny', NULL, 'twemoji twemoji-1f606', 1, 3, NULL),
(4, 'Sad', NULL, 'twemoji twemoji-1f622', 1, 4, NULL),
(5, 'Angry', NULL, 'twemoji twemoji-1f621', 1, 5, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_locations`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_locations` (
  `id` int(11) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) DEFAULT NULL,
  `url` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '99999',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_plugin_activity_locations`
--

INSERT INTO `orexv_comprofiler_plugin_activity_locations` (`id`, `value`, `title`, `url`, `published`, `ordering`, `params`) VALUES
(1, 'At', 'at', NULL, 1, 1, NULL),
(2, 'In', 'in', NULL, 1, 2, NULL),
(3, 'Near', 'near', NULL, 1, 3, NULL),
(4, 'Going To', 'going to', NULL, 1, 4, NULL),
(5, 'Leaving From', 'leaving from', NULL, 1, 5, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_notifications`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `title` text,
  `message` text,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_read`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_read` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_activity_tags`
--

CREATE TABLE `orexv_comprofiler_plugin_activity_tags` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `tag` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_antispam_attempts`
--

CREATE TABLE `orexv_comprofiler_plugin_antispam_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_antispam_block`
--

CREATE TABLE `orexv_comprofiler_plugin_antispam_block` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `reason` text,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_antispam_log`
--

CREATE TABLE `orexv_comprofiler_plugin_antispam_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `ip_address` varchar(255) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_antispam_whitelist`
--

CREATE TABLE `orexv_comprofiler_plugin_antispam_whitelist` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `reason` text,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_autoactions`
--

CREATE TABLE `orexv_comprofiler_plugin_autoactions` (
  `id` int(11) UNSIGNED NOT NULL,
  `system` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `type` varchar(255) NOT NULL DEFAULT '',
  `trigger` text NOT NULL,
  `object` int(11) NOT NULL DEFAULT '0',
  `variable` int(11) NOT NULL DEFAULT '1',
  `access` text NOT NULL,
  `conditions` text,
  `published` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '99999',
  `params` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_plugin_autoactions`
--

INSERT INTO `orexv_comprofiler_plugin_autoactions` (`id`, `system`, `title`, `description`, `type`, `trigger`, `object`, `variable`, `access`, `conditions`, `published`, `ordering`, `params`) VALUES
(1, 'autologin', 'Auto Login', 'Automatically logs in a user after registration or confirmation (must be approved and confirmed).', 'loginlogout', 'onAfterUserConfirmation|*|onAfterSaveUserRegistration', 0, 1, '-1', '[{\"field\":\"[approved]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[confirmed]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[block]\",\"operator\":\"0\",\"value\":\"0\"}]', 0, 1, '{\"loginlogout\":{\"mode\":\"1\",\"method\":\"1\",\"username\":\"[username]\",\"redirect\":\"index.php\"}}'),
(2, 'activityprofilelogin', 'Profile - Logged In', 'Logs activity when a user logs in.', 'activity', 'onAfterLogin', 0, 1, '-1', '[]', 0, 2, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"profile.[user_id].login\",\"owner\":\"[user_id]\",\"date\":\"now\",\"create_by\":\"asset\"}]}'),
(3, 'activityprofilelogout', 'Profile - Logged Out', 'Logs activity when a user logs out.', 'activity', 'onAfterLogout', 0, 1, '-1', '[]', 0, 3, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"profile.[user_id].logout\",\"owner\":\"[user_id]\",\"date\":\"now\",\"create_by\":\"asset\"}]}'),
(4, 'activityprofileregister', 'Profile - Register', 'Logs activity when a new user registers.', 'activity', 'onAfterUserApproval|*|onAfterUserConfirm|*|onAfterUserRegistration', 0, 1, '-1', '[{\"field\":\"[approved]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[confirmed]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 4, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"profile.[user_id].registration\",\"owner\":\"[user_id]\",\"date\":\"now\",\"create_by\":\"asset\"}]}'),
(5, 'activityprofileupdate', 'Profile - Update', 'Logs activity for profile updates.', 'activity', 'onAfterUserUpdate', 0, 1, '-7', '[{\"field\":\"[approved]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[confirmed]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[block]\",\"operator\":\"0\",\"value\":\"0\"}]', 0, 5, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"profile.[user_id].update\",\"owner\":\"[user_id]\",\"date\":\"now\",\"create_by\":\"asset\"}]}'),
(6, 'activityprofileavatar', 'Profile - Avatar', 'Logs activity for avatar updates.', 'activity', 'onAfterUserUpdate', 0, 1, '-7', '[{\"field\":\"[approved]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[confirmed]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[block]\",\"operator\":\"0\",\"value\":\"0\"},{\"field\":\"[var1_avatarapproved]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[var1_avatar]\",\"operator\":\"1\",\"value\":\"[var3_avatar]\"},{\"field\":\"[var1_avatar]\",\"operator\":\"7\"}]', 1, 6, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"profile.[user_id].avatar\",\"owner\":\"[user_id]\",\"date\":\"now\",\"create_by\":\"asset\"}]}'),
(7, 'activityprofilecanvas', 'Profile - Canvas', 'Logs activity for canvas updates.', 'activity', 'onAfterUserUpdate', 0, 1, '-7', '[{\"field\":\"[approved]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[confirmed]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[block]\",\"operator\":\"0\",\"value\":\"0\"},{\"field\":\"[var1_canvasapproved]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[var1_canvas]\",\"operator\":\"1\",\"value\":\"[var3_canvas]\"},{\"field\":\"[var1_canvas]\",\"operator\":\"7\"}]', 1, 7, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"profile.[user_id].canvas\",\"owner\":\"[user_id]\",\"date\":\"now\",\"create_by\":\"asset\"}]}'),
(8, 'activityaddconn', 'CB Connections - Add', 'Logs activity when a new connection is added and accepted.', 'activity', 'onAfterAcceptConnection|*|onAfterAddConnection', 0, 1, '-1', '[{\"field\":\"[cb:if trigger=\\\"onAfterAcceptConnection\\\"]0[cb:else][var3][\\/cb:else][\\/cb:if]\",\"operator\":\"1\",\"value\":\"1\"}]', 1, 8, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"profile.[var2].connection\",\"owner\":\"[var1]\",\"date\":\"now\",\"create_by\":\"asset_owner\"}]}'),
(9, 'activityremoveconn', 'CB Connections - Remove', 'Deletes activity for removed connections.', 'activity', 'onAfterRemoveConnection', 0, 1, '-1', '[]', 1, 9, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"profile.[var2].connection\",\"owner\":\"[var1]\",\"delete_by\":\"asset_owner\"}]}'),
(10, 'activitycomment', 'CB Activity - Comment', 'Logs activity for activity comments.', 'activity', 'activity_onAfterCreateStreamComment|*|activity_onAfterUpdateStreamComment', 0, 1, '-1', '[{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var1_activity][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 10, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var3_published]\",\"asset\":\"activity.[var1_activity].comment\",\"owner\":\"[var3_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner\"}]}'),
(11, 'notificationcomment', 'CB Activity - Comment Notification', 'Logs notifications for activity comments.', 'activity', 'activity_onAfterCreateStreamComment', 0, 1, '-1', '[{\"field\":\"[var3_asset]\",\"operator\":\"11\",\"value\":\"\\/^gallery.\\/\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var1_activity][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 11, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"activity.[var1_activity].comment\",\"owner\":\"[var3_user_id]\",\"user\":\"[var2_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(12, 'activitytag', 'CB Activity - Tag', 'Logs activity for activity tags.', 'activity', 'activity_onAfterCreateStreamTag', 0, 1, '-1', '[{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var1_activity][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_tag][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_tag][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 12, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"activity.[var1_activity].tag\",\"owner\":\"[var3_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner\"}]}'),
(13, 'notificationtag', 'CB Activity - Tag Notification', 'Logs notifications for activity tags.', 'activity', 'activity_onAfterCreateStreamTag', 0, 1, '-1', '[{\"field\":\"[var3_asset]\",\"operator\":\"11\",\"value\":\"\\/^gallery.\\/\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var1_activity][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_tag][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_tag][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 13, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"activity.[var1_activity].tag\",\"owner\":\"[var3_user_id]\",\"user\":\"[var3_tag]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(14, 'activitylike', 'CB Activity - Like', 'Logs activity for activity likes.', 'activity', 'activity_onAfterLikeStream', 0, 1, '-1', '[{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var1_activity][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 14, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"activity.[var1_activity].like\",\"owner\":\"[var3_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner\"}]}'),
(15, 'notificationlike', 'CB Activity - Like Notification', 'Logs notifications for activity likes.', 'activity', 'activity_onAfterLikeStream', 0, 1, '-1', '[{\"field\":\"[var3_asset]\",\"operator\":\"11\",\"value\":\"\\/^gallery.\\/\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var1_activity][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 15, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"activity.[var1_activity].like\",\"owner\":\"[var3_user_id]\",\"user\":\"[var2_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(16, 'activityprivacynew', 'CB Activity - Privacy - New', 'Displays privacy selector for new activity.', 'privacy', 'activity_onDisplayStreamActivityNew', 2, 1, '-1', '[]', 1, 16, '{\"privacy\":[{\"mode\":\"privacy\",\"method\":\"display\",\"asset\":\"activity.0\",\"privacy_privacy\":{\"privacy_layout\":\"button\",\"privacy_ajax\":\"0\"}}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var1\'][\'right\'][] = \'<span class=\\\"streamInputSelect streamInputPrivacy\\\">\' . $content . \'<\\/span>\';\",\"display_method\":\"php\",\"references\":\"1\"}'),
(17, 'activityprivacyedit', 'CB Activity - Privacy - Edit', 'Displays privacy selector for activity edit.', 'privacy', 'activity_onDisplayStreamActivityEdit', 2, 1, '-1', '[]', 1, 17, '{\"privacy\":[{\"mode\":\"privacy\",\"method\":\"display\",\"asset\":\"activity.[var1_id]\",\"owner\":\"[var1_user_id]\",\"privacy_privacy\":{\"privacy_layout\":\"button\",\"privacy_ajax\":\"0\"}}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'][\'right\'][] = \'<span class=\\\"streamInputSelect streamInputPrivacy\\\">\' . $content . \'<\\/span>\';\",\"display_method\":\"php\",\"references\":\"2\"}'),
(18, 'activityprivacysave', 'CB Activity - Privacy - Save', 'Saves activity privacy rules.', 'privacy', 'activity_onAfterCreateStreamActivity|*|activity_onAfterUpdateStreamActivity', 2, 1, '-1', '[]', 1, 18, '{\"privacy\":[{\"mode\":\"privacy\",\"method\":\"save\",\"asset\":\"activity.[var3_id]\",\"owner\":\"[var3_user_id]\",\"privacy_privacy\":{\"privacy_layout\":\"button\",\"privacy_ajax\":\"0\"}}]}'),
(19, 'activityprivacyauthorized', 'CB Activity - Privacy - Authorized', 'Checks privacy rules on activity.', 'privacy', 'activity_onLoadActivityStream', 2, 1, '-4', '[{\"field\":\"[user_id]\",\"operator\":\"1\",\"value\":\"[loop_user_id]\"}]', 1, 19, '{\"privacy\":[{\"mode\":\"privacy\",\"method\":\"authorized\",\"asset\":\"activity.[loop_id]\",\"owner\":\"[loop_user_id]\",\"privacy_privacy\":{\"privacy_layout\":\"button\",\"privacy_ajax\":\"0\"}}],\"references\":\"1\",\"loop\":\"1\"}'),
(20, 'activitygallerycreate', 'CB Gallery - Create', 'Logs activity for uploaded and linked media.', 'activity', 'gallery_onAfterCreateItem|*|gallery_onAfterUpdateItem', 0, 1, '-1', '[{\"field\":\"[var1_asset]\",\"operator\":\"10\",\"value\":\"\\/^profile\\\\.\\/\"},{\"field\":\"[var1_asset]\",\"operator\":\"11\",\"value\":\"\\/\\\\.uploads$\\/\"}]', 1, 20, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"gallery.[var1_type].[var1_id]\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset\"}]}'),
(21, 'activitygallerydelete', 'CB Gallery - Delete', 'Deletes activity, notifications, comments, tags and likes for deleted media.', 'activity', 'gallery_onAfterDeleteItem', 0, 1, '-1', '[]', 1, 21, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"gallery.[var1_type].[var1_id],gallery.[var1_type].[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"gallery.[var1_type].[var1_id],gallery.[var1_type].[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"comment\",\"method\":\"delete\",\"asset\":\"gallery.[var1_type].[var1_id],gallery.[var1_type].[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"tag\",\"method\":\"delete\",\"asset\":\"gallery.[var1_type].[var1_id],gallery.[var1_type].[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"like\",\"method\":\"delete\",\"asset\":\"gallery.[var1_type].[var1_id],gallery.[var1_type].[var1_id].%\",\"delete_by\":\"asset\"}]}'),
(22, 'activitygallerylikes', 'CB Gallery - Likes', 'Adds likes to gallery media.', 'activity', 'gallery_onDisplayModal', 0, 1, '-1', '[{\"field\":\"[var1_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 22, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"like\",\"asset\":\"gallery.[var1_type].[var1_id]\",\"owner\":\"[var1_user_id]\",\"likes_stream\":{\"likes_layout\":\"extended\"}}],\"display\":\"return\",\"display_layout\":\"<div style=\\\"margin-bottom: 5px;\\\">[content]<\\/div>\"}'),
(23, 'activitygallerycomments', 'CB Gallery - Comments', 'Adds commenting to gallery media.', 'activity', 'gallery_onDisplayModal', 0, 1, '-1', '[{\"field\":\"[var1_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 1, 23, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"comments\",\"asset\":\"gallery.[var1_type].[var1_id]\",\"owner\":\"[var1_user_id]\",\"comments_stream\":{\"comments_direction\":\"down\",\"comments_pinned\":\"0\",\"comments_create\":\"1\",\"comments_create_access\":\"2\",\"comments_create_connected\":\"1\",\"comments_actions\":\"0\",\"comments_locations\":\"0\",\"comments_links\":\"0\",\"comments_tags\":\"0\",\"comments_likes\":\"0\",\"comments_replies\":\"0\",\"comments_gallery\":\"0\"}}],\"display\":\"return\"}'),
(24, 'notificationgallerycreateconn', 'CB Gallery - Create Connections Notification', 'Logs notifications to users connections for uploaded and linked media.', 'activity', 'gallery_onAfterCreateItem', 5, 1, '-1', '[{\"field\":\"[var1_asset]\",\"operator\":\"10\",\"value\":\"\\/^profile\\\\.\\/\"},{\"field\":\"[var1_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 24, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"gallery.[var1_type].[var1_id]\",\"owner\":\"[var1_user_id]\",\"user\":\"[action_user]\",\"date\":\"[var1_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(25, 'notificationgallerycomment', 'CB Gallery - Comment Notification', 'Logs notifications for gallery media comments.', 'activity', 'activity_onAfterCreateStreamComment', 0, 1, '-1', '[{\"field\":\"[var3_asset]\",\"operator\":\"10\",\"value\":\"\\/^gallery.\\/\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 25, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"[var3_asset].comment\",\"owner\":\"[var3_user_id]\",\"user\":\"[var2_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(26, 'notificationgallerytag', 'CB Gallery - Tag Notification', 'Logs notifications for gallery media tags.', 'activity', 'activity_onAfterCreateStreamTag', 0, 1, '-1', '[{\"field\":\"[var3_asset]\",\"operator\":\"10\",\"value\":\"\\/^gallery.\\/\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 26, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"[var3_asset].tag\",\"owner\":\"[var3_user_id]\",\"user\":\"[var2_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(27, 'notificationgallerylike', 'CB Gallery - Like Notification', 'Logs notifications for gallery media likes.', 'activity', 'activity_onAfterLikeStream', 0, 1, '-1', '[{\"field\":\"[var3_asset]\",\"operator\":\"10\",\"value\":\"\\/^gallery.\\/\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"operator\":\"2\",\"value\":\"0\",\"format\":\"1\"},{\"field\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var2_user_id][\\/cb:parse]\",\"operator\":\"1\",\"value\":\"[cb:parse function=\\\"clean\\\" method=\\\"int\\\"][var3_user_id][\\/cb:parse]\",\"format\":\"1\"}]', 0, 27, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"[var3_asset].like\",\"owner\":\"[var3_user_id]\",\"user\":\"[var2_user_id]\",\"date\":\"[var3_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(28, 'galleryprivacyedit', 'CB Gallery - Privacy - Edit', 'Displays privacy selector for gallery album and media edit.', 'privacy', 'gallery_onAfterFolderEdit|*|gallery_onAfterItemEdit|*|gallery_onAfterItemEditMini', 2, 1, '-1', '[]', 1, 28, '{\"privacy\":[{\"mode\":\"privacy\",\"method\":\"display\",\"asset\":\"gallery.[cb:if trigger=\\\"gallery_onAfterFolderEdit\\\"]album[cb:else][var1_type][\\/cb:else][\\/cb:if].[cb:if var1_id=\\\"\\\"]0[cb:else][var1_id][\\/cb:else][\\/cb:if]\",\"owner\":\"[cb:if var1_user_id=\\\"\\\"][user_id][cb:else][var1_user_id][\\/cb:else][\\/cb:if]\",\"privacy_privacy\":{\"privacy_layout\":\"button\",\"privacy_ajax\":\"0\"}}],\"display\":\"return\",\"display_layout\":\"return ( $variables[\'trigger\'] == \'gallery_onAfterItemEditMini\' ? \'<div class=\\\"row\\\"><div class=\\\"col-sm-12\\\">\' . $content . \'<\\/div><\\/div>\' : \'<div class=\\\"cbft_select cbtt_select form-group cb_form_line clearfix\\\"><label class=\\\"col-sm-3 control-label\\\">\' . CBTxt::T( \'Privacy\' ) . \'<\\/label><div class=\\\"cb_field col-sm-9\\\">\' . $content . \'<\\/div><\\/div>\' );\",\"display_method\":\"php\"}'),
(29, 'galleryprivacysave', 'CB Gallery - Privacy - Save', 'Saves gallery privacy rules.', 'privacy', 'gallery_onAfterUpdateGalleryItem|*|gallery_onAfterCreateGalleryItem|*|gallery_onAfterUpdateGalleryFolder|*|gallery_onAfterCreateGalleryFolder', 2, 1, '-1', '[]', 1, 29, '{\"privacy\":[{\"mode\":\"privacy\",\"method\":\"save\",\"asset\":\"gallery.[cb:if trigger=\\\"gallery_onAfterUpdateGalleryFolder\\\" or trigger=\\\"gallery_onAfterCreateGalleryFolder\\\"]album[cb:else][var3_type][\\/cb:else][\\/cb:if].[var3_id]\",\"owner\":\"[var3_user_id]\",\"privacy_privacy\":{\"privacy_layout\":\"button\",\"privacy_ajax\":\"0\"}}]}'),
(30, 'galleryprivacyauthorized', 'CB Gallery - Privacy - Authorized', 'Checks privacy rules on gallery.', 'privacy', 'gallery_onLoadFolders|*|gallery_onLoadItems', 2, 1, '-4', '[{\"field\":\"[user_id]\",\"operator\":\"1\",\"value\":\"[loop_user_id]\"}]', 1, 30, '{\"privacy\":[{\"mode\":\"privacy\",\"method\":\"authorized\",\"asset\":\"gallery.[cb:if trigger=\\\"gallery_onLoadFolders\\\"]album[cb:else][loop_type][\\/cb:else][\\/cb:if].[loop_id]\",\"owner\":\"[loop_user_id]\",\"privacy_privacy\":{\"privacy_layout\":\"button\",\"privacy_ajax\":\"0\"}}],\"references\":\"1\",\"loop\":\"1\"}'),
(31, 'activityblogcreate', 'CB Blogs - Create', 'Logs activity for newly created blog entries.', 'activity', 'cbblogs_onAfterUpdateBlog|*|cbblogs_onAfterCreateBlog', 0, 1, '-1', '[]', 1, 31, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"blog.[var2_id]\",\"owner\":\"[var1_user]\",\"date\":\"[var2_created]\",\"create_by\":\"asset\"}]}'),
(32, 'notificationblogcreateconn', 'CB Blogs - Create Connections Notification', 'Logs notifications to users connections for newly created blog entries.', 'activity', 'cbblogs_onAfterUpdateBlog|*|cbblogs_onAfterCreateBlog', 5, 1, '-1', '[]', 1, 32, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"blog.[var2_id]\",\"owner\":\"[var1_user]\",\"user\":\"[action_user]\",\"date\":\"[var2_created]\",\"create_by\":\"asset_owner_user\"}]}'),
(33, 'activityblogdelete', 'CB Blogs - Delete', 'Deletes activity, notifications, comments and likes for deleted blog entries.', 'activity', 'cbblogs_onAfterDeleteBlog', 0, 1, '-1', '[]', 1, 33, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"blog.[var2_id],blog.[var2_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"blog.[var2_id],blog.[var2_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"comment\",\"method\":\"delete\",\"asset\":\"blog.[var2_id],blog.[var2_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"like\",\"method\":\"delete\",\"asset\":\"blog.[var2_id],blog.[var2_id].%\",\"delete_by\":\"asset\"}]}'),
(34, 'activitykunenacreate', 'Kunena - Create', 'Logs activity for newly created discussions.', 'activity', 'kunenaIntegration', 0, 1, '-1', '[{\"field\":\"[var1]\",\"operator\":\"10\",\"value\":\"\\/onAfterPost|onAfterEdit|onAfterUndelete\\/\"},{\"field\":\"[var3_message_parent]\",\"operator\":\"0\",\"value\":\"0\"}]', 0, 34, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"kunena.[var3_message_id].create\",\"owner\":\"[var3_message_userid]\",\"date\":\"[var3_time]\",\"create_by\":\"asset\"}]}'),
(35, 'activitykunenareply', 'Kunena - Reply', 'Logs activity for discussion replies.', 'activity', 'kunenaIntegration', 0, 1, '-1', '[{\"field\":\"[var1]\",\"operator\":\"10\",\"value\":\"\\/onAfterReply|onAfterEdit|onAfterUndelete\\/\"},{\"field\":\"[var3_message_parent]\",\"operator\":\"1\",\"value\":\"0\"}]', 0, 35, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"asset\":\"kunena.[var3_message_id].reply\",\"owner\":\"[var3_message_userid]\",\"date\":\"[var3_time]\",\"create_by\":\"asset\"}]}'),
(36, 'activitykunenadelete', 'Kunena - Delete', 'Deletes activity for deleted discussions.', 'activity', 'kunenaIntegration', 0, 1, '-1', '[{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"onAfterDelete\"}]', 0, 36, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"kunena.[var3_message_id],kunena.[var3_message_id].%\",\"delete_by\":\"asset\"}]}'),
(37, 'activitygjgrpcreate', 'CB GroupJive - Group Create', 'Logs activity for newly created groups.', 'activity', 'gj_onAfterCreateGroup|*|gj_onAfterUpdateGroup', 0, 1, '-1', '[]', 1, 37, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_id].create\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset\"}]}'),
(38, 'notificationgjgrpcreateconn', 'CB GroupJive - Group Create Connections Notification', 'Logs notifications to users connections for newly created groups.', 'activity', 'gj_onAfterCreateGroup|*|gj_onAfterUpdateGroup', 5, 1, '-1', '[]', 0, 38, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_id].create\",\"owner\":\"[var1_user_id]\",\"user\":\"[action_user]\",\"date\":\"[var1_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(39, 'activitygjgrpdelete', 'CB GroupJive - Group Delete', 'Deletes activity, notifications, follows, tags and likes for deleted groups.', 'activity', 'gj_onAfterDeleteGroup', 0, 1, '-1', '[]', 1, 39, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_id],groupjive.group.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_id],groupjive.group.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"follow\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_id],groupjive.group.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"tag\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_id],groupjive.group.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"like\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_id],groupjive.group.[var1_id].%\",\"delete_by\":\"asset\"}]}'),
(40, 'activitygjgrpjoin', 'CB GroupJive - Group Join', 'Logs activity when joining a group.', 'activity', 'gj_onAfterJoinGroup|*|gj_onAfterUpdateUser', 0, 1, '-1', '[]', 1, 40, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[cb:if var1_status>\\\"0\\\"]1[cb:else]0[\\/cb:else][\\/cb:if]\",\"asset\":\"groupjive.group.[var1_group].join\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset_owner\"}]}'),
(41, 'notificationgjgrpjoin', 'CB GroupJive - Group Join Notification', 'Logs notifications when joining a group.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"user_join\"}]', 0, 41, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].join\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var7_id].leave\",\"owner\":\"[var3_id]\",\"create_by\":\"asset_owner\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(42, 'notificationgjgrpjoinconn', 'CB GroupJive - Group Join Connections Notification', 'Logs notifications to users connections when joining a group.', 'activity', 'gj_onAfterJoinGroup|*|gj_onAfterUpdateUser', 5, 1, '-1', '[]', 0, 42, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"published\":\"[cb:if var1_status>\\\"0\\\"]1[cb:else]0[\\/cb:else][\\/cb:if]\",\"asset\":\"groupjive.group.[var1_group].join\",\"owner\":\"[var1_user_id]\",\"user\":\"[action_user]\",\"date\":\"[var1_date]\",\"create_by\":\"asset_owner_user\"}]}'),
(43, 'activitygjgrpleave', 'CB GroupJive - Group Leave', 'Deletes group activity on group leave.', 'activity', 'gj_onAfterDeleteUser', 0, 1, '-1', '[]', 1, 43, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group],groupjive.group.[var1_group].%\",\"owner\":\"[var1_user_id]\",\"delete_by\":\"asset_owner\"}]}'),
(44, 'notificationgjgrpleave', 'CB GroupJive - Group Leave Notification', 'Logs notifications when leaving a group.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"user_leave\"}]', 0, 44, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].leave\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var7_id].join\",\"owner\":\"[var3_id]\",\"create_by\":\"asset_owner\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(45, 'notificationgjgrpinvite', 'CB GroupJive - Group Invite Notification', 'Logs notifications when invited to a group.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"user_invite\"},{\"field\":\"[var4]\",\"operator\":\"7\"}]', 0, 45, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].invite\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(46, 'notificationgjgrpmessage', 'CB GroupJive - Group Message Notification', 'Logs notifications when sending group messages.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"group_message\"}]', 0, 46, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].message\",\"message\":\"[var8_message]\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"none\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(47, 'activitygjgrpstream', 'CB Groupjive - Stream', 'Adds activity stream to groups.', 'activity', 'gj_onBeforeDisplayGroup', 2, 1, '-1', '[{\"field\":\"[var2_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 47, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"activity\",\"asset\":\"groupjive.group.[var2_id],groupjive.group.[var2_id].%\",\"moderators\":\"[var2_user_id]\",\"notify\":\"[var2_user_id]\",\"activity_stream\":{\"activity_pinned\":\"0\",\"activity_create_connected\":\"0\",\"activity_comments_pinned\":\"0\",\"activity_comments_create_connected\":\"0\",\"activity_comments_replies_pinned\":\"0\",\"activity_comments_replies_create_connected\":\"0\"}}],\"display\":\"return\",\"display_layout\":\"return array( \'id\' => \'activity\', \'title\' => CBTxt::T( \'Activity\' ), \'content\' => $content );\",\"display_method\":\"php\"}'),
(48, 'activitygjgrpfollow', 'CB Groupjive - Follow', 'Adds follow button to groups. Note only applies to Open groups.', 'activity', 'gj_onBeforeDisplayGroup', 2, 1, '-1', '[{\"field\":\"[var2_published]\",\"operator\":\"0\",\"value\":\"1\"},{\"field\":\"[var2_type]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 48, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"follow\",\"asset\":\"groupjive.group.[var2_id],groupjive.group.[var2_id].%\",\"following_stream\":{\"following_count\":\"0\",\"following_connected\":\"0\"}}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var6\'][] = $content;\",\"display_method\":\"php\",\"references\":\"6\"}'),
(49, 'activitygjgrplikes', 'CB Groupjive - Likes', 'Adds like button to groups.', 'activity', 'gj_onBeforeDisplayGroup', 2, 1, '-1', '[{\"field\":\"[var2_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 49, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"like\",\"asset\":\"groupjive.group.[var2_id]\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var6\'][] = $content;\",\"display_method\":\"php\",\"references\":\"6\"}'),
(50, 'gallerygjgrpgallery', 'CB Groupjive - Gallery', 'Adds media gallery to groups.', 'gallery', 'gj_onBeforeDisplayGroup', 2, 1, '-1', '[{\"field\":\"[var2_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 50, '{\"gallery\":[{\"mode\":\"gallery\",\"asset\":\"groupjive.group.[var2_id]\",\"moderators\":\"[var2_user_id]\",\"notify\":\"[var2_user_id]\"}],\"display\":\"return\",\"display_layout\":\"return array( \'id\' => \'gallery\', \'title\' => CBTxt::T( \'Gallery\' ), \'content\' => $content );\",\"display_method\":\"php\"}'),
(51, 'notificationgjgrpgeneral', 'CB GroupJive - General Notifications', 'Attempts to log all other notifications instead of sending private messages or emails. Note this will store the subject and message in the notification.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"1\",\"value\":\"\\/user_join|user_leave|user_invite|group_message|wall_reply|wall_new|photo_new|file_new|video_new|event_new\\/\"}]', 0, 51, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id]\",\"title\":\"[var5]\",\"message\":\"[var6]\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"none\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(52, 'activitygjwallcreate', 'CB GroupJive Wall - Create', 'Logs activity for newly created group wall posts.', 'activity', 'gj_onAfterCreateWall|*|gj_onAfterUpdateWall', 0, 1, '-1', '[{\"field\":\"[var1_reply]\",\"operator\":\"5\",\"value\":\"0\"}]', 1, 52, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_group].wall.[var1_id]\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset\"}]}'),
(53, 'activitygjwallreply', 'CB GroupJive Wall - Reply', 'Logs comments for newly created group wall post replies.', 'activity', 'gj_onAfterCreateWall|*|gj_onAfterUpdateWall', 0, 1, '-1', '[{\"field\":\"[var1_reply]\",\"operator\":\"2\",\"value\":\"0\"}]', 0, 53, '{\"activity\":[{\"mode\":\"comment\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_group].wall.[var1_reply].reply.[var1_id]\",\"message\":\"[var1_post]\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset_owner\"}]}'),
(54, 'notificationgjwallcreate', 'CB GroupJive Wall - Create Notification', 'Logs notifications for newly created group wall posts.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"10\",\"value\":\"\\/wall_reply|wall_new\\/\"}]', 0, 54, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].wall.[var8_wall_id]\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(55, 'activitygjwalldelete', 'CB GroupJive Wall - Delete', 'Deletes activity, notifications and comments for deleted group wall posts.', 'activity', 'gjint_onAfterDeleteWall', 0, 1, '-1', '[]', 1, 55, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].wall.[var1_id],groupjive.group.[var1_group].wall.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var7_id].wall.[var8_wall_id],groupjive.group.[var7_id].wall.[var8_wall_id].%\",\"create_by\":\"asset\"},{\"mode\":\"comment\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].wall.[var1_id],groupjive.group.[var1_group].wall.[var1_id].%\",\"delete_by\":\"asset\"}]}'),
(56, 'activitygjphotolikes', 'CB Groupjive Photo - Likes', 'Adds like button to group photos.', 'activity', 'gj_onDisplayPhoto', 2, 1, '-1', '[{\"field\":\"[var1_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 56, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"like\",\"asset\":\"groupjive.group.[var1_group].photo.[var1_id]\",\"likes_stream\":{\"likes_layout\":\"simple\",\"likes_count\":\"0\"}}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'][] = $content;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(57, 'activitygjphotocreate', 'CB GroupJive Photo - Create', 'Logs activity for newly uploaded group photos.', 'activity', 'gj_onAfterCreatePhoto|*|gj_onAfterUpdatePhoto', 0, 1, '-1', '[]', 1, 57, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_group].photo.[var1_id]\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset\"}]}'),
(58, 'notificationgjphotocreate', 'CB GroupJive Photo - Create Notification', 'Logs notifications for newly uploaded group photos.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"photo_new\"}]', 0, 58, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].photo.[var8_photo_id]\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(59, 'activitygjphotodelete', 'CB GroupJive Photo - Delete', 'Deletes activity, notifications and likes for deleted group photos.', 'activity', 'gj_onAfterDeletePhoto', 0, 1, '-1', '[]', 1, 59, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].photo.[var1_id],groupjive.group.[var1_group].photo.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].photo.[var1_id],groupjive.group.[var1_group].photo.[var1_id].%\",\"create_by\":\"asset\"},{\"mode\":\"like\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].photo.[var1_id],groupjive.group.[var1_group].photo.[var1_id].%\",\"create_by\":\"asset\"}]}'),
(60, 'activitygjfilecreate', 'CB GroupJive File - Create', 'Logs activity for newly uploaded group files.', 'activity', 'gj_onAfterCreateFile|*|gj_onAfterUpdateFile', 0, 1, '-1', '[]', 1, 60, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_group].file.[var1_id]\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset\"}]}'),
(61, 'notificationgjfilecreate', 'CB GroupJive File - Create Notification', 'Logs notifications for newly uploaded group files.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"file_new\"}]', 0, 61, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].file.[var8_file_id]\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(62, 'activitygjfiledelete', 'CB GroupJive File - Delete', 'Deletes activity, notifications and likes for deleted group files.', 'activity', 'gj_onAfterDeleteFile', 0, 1, '-1', '[]', 1, 62, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].file.[var1_id],groupjive.group.[var1_group].file.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].file.[var1_id],groupjive.group.[var1_group].file.[var1_id].%\",\"create_by\":\"asset\"},{\"mode\":\"like\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].file.[var1_id],groupjive.group.[var1_group].file.[var1_id].%\",\"create_by\":\"asset\"}]}'),
(63, 'activitygjvideolikes', 'CB Groupjive Video - Likes', 'Adds like button to group videos.', 'activity', 'gj_onDisplayVideo', 2, 1, '-1', '[{\"field\":\"[var1_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 63, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"like\",\"asset\":\"groupjive.group.[var1_group].video.[var1_id]\",\"likes_stream\":{\"likes_layout\":\"simple\",\"likes_count\":\"0\"}}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'][] = $content;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(64, 'activitygjvideocreate', 'CB GroupJive Video - Create', 'Logs activity for newly uploaded group videos.', 'activity', 'gj_onAfterCreateVideo|*|gj_onAfterUpdateVideo', 0, 1, '-1', '[]', 1, 64, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_group].video.[var1_id]\",\"owner\":\"[var1_user_id]\",\"date\":\"[var1_date]\",\"create_by\":\"asset\"}]}'),
(65, 'notificationgjvideocreate', 'CB GroupJive Video - Create Notification', 'Logs notifications for newly uploaded group videos.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"video_new\"}]', 0, 65, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].video.[var8_video_id]\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(66, 'activitygjvideodelete', 'CB GroupJive Video - Delete', 'Deletes activity, notifications and likes for deleted group videos.', 'activity', 'gj_onAfterDeleteVideo', 0, 1, '-1', '[]', 1, 66, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].video.[var1_id],groupjive.group.[var1_group].video.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].video.[var1_id],groupjive.group.[var1_group].video.[var1_id].%\",\"create_by\":\"asset\"},{\"mode\":\"like\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].video.[var1_id],groupjive.group.[var1_group].video.[var1_id].%\",\"create_by\":\"asset\"}]}'),
(67, 'activitygjeventslikes', 'CB Groupjive Events - Likes', 'Adds like button to group events.', 'activity', 'gj_onDisplayEvent', 2, 1, '-1', '[{\"field\":\"[var1_published]\",\"operator\":\"0\",\"value\":\"1\"}]', 0, 67, '{\"activity\":[{\"mode\":\"stream\",\"stream\":\"like\",\"asset\":\"groupjive.group.[var1_group].event.[var1_id]\",\"likes_stream\":{\"likes_layout\":\"extended\"}}],\"display\":\"return\"}'),
(68, 'activitygjeventscreate', 'CB GroupJive Events - Create', 'Logs activity for newly scheduled events.', 'activity', 'gj_onAfterCreateEvent|*|gj_onAfterUpdateEvent', 0, 1, '-1', '[]', 1, 68, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"create\",\"published\":\"[var1_published]\",\"asset\":\"groupjive.group.[var1_group].event.[var1_id]\",\"owner\":\"[var1_user_id]\",\"create_by\":\"asset\"}]}'),
(69, 'notificationgjeventscreate', 'CB GroupJive Events - Create Notification', 'Logs notifications for newly scheduled events.', 'activity', 'gj_onSendNotification', 0, 1, '-1', '[{\"field\":\"[var2]\",\"operator\":\"1\",\"value\":\"3\"},{\"field\":\"[var1]\",\"operator\":\"0\",\"value\":\"event_new\"}]', 0, 69, '{\"activity\":[{\"mode\":\"notification\",\"method\":\"create\",\"asset\":\"groupjive.group.[var7_id].event.[var8_event_id]\",\"owner\":\"[var3_id]\",\"user\":\"[var4_id]\",\"date\":\"now\",\"create_by\":\"asset_owner_user\"}],\"display\":\"silent\",\"display_layout\":\"$variables[\'var2\'] = 99;\",\"display_method\":\"php\",\"references\":\"2\"}'),
(70, 'activitygjeventsdelete', 'CB GroupJive Events - Delete', 'Deletes activity, notifications and likes for deleted group events.', 'activity', 'gj_onAfterDeleteEvent', 0, 1, '-1', '[]', 1, 70, '{\"activity\":[{\"mode\":\"activity\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].event.[var1_id],groupjive.group.[var1_group].event.[var1_id].%\",\"delete_by\":\"asset\"},{\"mode\":\"notification\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].event.[var1_id],groupjive.group.[var1_group].event.[var1_id].%\",\"create_by\":\"asset\"},{\"mode\":\"like\",\"method\":\"delete\",\"asset\":\"groupjive.group.[var1_group].event.[var1_id],groupjive.group.[var1_group].event.[var1_id].%\",\"create_by\":\"asset\"}]}');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_blogs`
--

CREATE TABLE `orexv_comprofiler_plugin_blogs` (
  `id` int(11) UNSIGNED NOT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `blog_intro` text,
  `blog_full` text,
  `category` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '99999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_emails`
--

CREATE TABLE `orexv_comprofiler_plugin_emails` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `from` varchar(255) NOT NULL DEFAULT '',
  `to` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(255) NOT NULL DEFAULT 'P'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_gallery_folders`
--

CREATE TABLE `orexv_comprofiler_plugin_gallery_folders` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `thumbnail` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_gallery_items`
--

CREATE TABLE `orexv_comprofiler_plugin_gallery_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `file` text,
  `folder` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_invites`
--

CREATE TABLE `orexv_comprofiler_plugin_invites` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `to` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `code` varchar(255) DEFAULT NULL,
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accepted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_privacy`
--

CREATE TABLE `orexv_comprofiler_plugin_privacy` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `asset` text NOT NULL,
  `rule` text NOT NULL,
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plugin_privacy_closed`
--

CREATE TABLE `orexv_comprofiler_plugin_privacy_closed` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `reason` text,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plug_profilebook`
--

CREATE TABLE `orexv_comprofiler_plug_profilebook` (
  `id` int(11) UNSIGNED NOT NULL,
  `mode` char(1) NOT NULL DEFAULT 'g',
  `posterid` int(11) UNSIGNED DEFAULT NULL,
  `posterip` varchar(255) NOT NULL DEFAULT '',
  `postername` varchar(255) DEFAULT '',
  `posteremail` varchar(255) DEFAULT NULL,
  `posterlocation` varchar(255) DEFAULT NULL,
  `posterurl` varchar(255) DEFAULT NULL,
  `postervote` int(11) UNSIGNED DEFAULT NULL,
  `postertitle` varchar(128) NOT NULL DEFAULT '',
  `postercomment` text NOT NULL,
  `date` datetime DEFAULT NULL,
  `userid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `feedback` text,
  `editdate` datetime DEFAULT NULL,
  `editedbyid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `editedbyname` varchar(255) DEFAULT NULL,
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_plug_pulogger`
--

CREATE TABLE `orexv_comprofiler_plug_pulogger` (
  `id` int(10) UNSIGNED NOT NULL,
  `changedate` datetime DEFAULT NULL,
  `profileid` int(11) DEFAULT NULL,
  `editedbyip` varchar(255) NOT NULL DEFAULT '',
  `editedbyid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `mode` tinyint(1) NOT NULL DEFAULT '0',
  `fieldname` varchar(50) NOT NULL DEFAULT '',
  `oldvalue` text NOT NULL,
  `newvalue` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_ratings`
--

CREATE TABLE `orexv_comprofiler_ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT 'field',
  `item` int(11) NOT NULL DEFAULT '0',
  `target` int(11) NOT NULL DEFAULT '0',
  `rating` float NOT NULL DEFAULT '0',
  `ip_address` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_sessions`
--

CREATE TABLE `orexv_comprofiler_sessions` (
  `username` varchar(50) NOT NULL DEFAULT '',
  `userid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `ui` tinyint(4) NOT NULL DEFAULT '0',
  `incoming_ip` varchar(39) NOT NULL DEFAULT '',
  `client_ip` varchar(39) NOT NULL DEFAULT '',
  `session_id` varchar(33) NOT NULL DEFAULT '',
  `session_data` mediumtext NOT NULL,
  `expiry_time` int(14) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_tabs`
--

CREATE TABLE `orexv_comprofiler_tabs` (
  `tabid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `ordering_edit` int(11) NOT NULL DEFAULT '10',
  `ordering_register` int(11) NOT NULL DEFAULT '10',
  `width` varchar(10) NOT NULL DEFAULT '.5',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `pluginclass` varchar(255) DEFAULT NULL,
  `pluginid` int(11) DEFAULT NULL,
  `fields` tinyint(1) NOT NULL DEFAULT '1',
  `params` mediumtext,
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  `displaytype` varchar(255) NOT NULL DEFAULT '',
  `position` varchar(255) NOT NULL DEFAULT '',
  `viewaccesslevel` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cssclass` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_comprofiler_tabs`
--

INSERT INTO `orexv_comprofiler_tabs` (`tabid`, `title`, `description`, `ordering`, `ordering_edit`, `ordering_register`, `width`, `enabled`, `pluginclass`, `pluginid`, `fields`, `params`, `sys`, `displaytype`, `position`, `viewaccesslevel`, `cssclass`) VALUES
(6, 'USER_STATISTICS_TITLE', '', 1, 10, 10, '.5', 1, 'getStatsTab', 1, 1, NULL, 1, 'html', 'canvas_stats', 1, NULL),
(7, 'USER_CANVAS_TITLE', '', 1, 11, 11, '1', 1, 'getCanvasTab', 1, 1, NULL, 1, 'html', 'canvas_background', 1, NULL),
(8, 'BLOGS_TITLE', '', 3, 10, 10, '1', 1, 'cbblogsTab', 19, 0, NULL, 1, 'menu', 'canvas_main_middle', 1, NULL),
(9, 'FORUMS_TITLE', '', 4, 10, 10, '1', 0, 'cbforumsTab', 18, 0, NULL, 1, 'menu', 'canvas_main_middle', 1, NULL),
(10, 'ARTICLES_TITLE', '', 2, 10, 10, '1', 1, 'cbarticlesTab', 17, 0, NULL, 1, 'menu', 'canvas_main_middle', 1, NULL),
(11, '_UE_CONTACT_INFO_HEADER', '', 1, 10, 10, '1', 1, 'getContactTab', 1, 1, NULL, 1, 'menu', 'canvas_main_middle', 1, NULL),
(15, '_UE_CONNECTION', '', 5, 10, 10, '1', 1, 'getConnectionTab', 2, 0, NULL, 1, 'menunested', 'canvas_main_middle', 1, NULL),
(17, '_UE_MENU', '', 1, 10, 10, '1', 1, 'getMenuTab', 14, 0, NULL, 1, 'html', 'canvas_menu', 1, NULL),
(18, '_UE_CONNECTIONPATHS', '', 1, 10, 10, '1', 1, 'getConnectionPathsTab', 2, 0, NULL, 1, 'html', 'cb_head', 1, NULL),
(19, '_UE_PROFILE_PAGE_TITLE', '', 1, 10, 10, '1', 1, 'getPageTitleTab', 1, 1, NULL, 1, 'html', 'canvas_title', 1, NULL),
(20, '_UE_PORTRAIT', '', 1, 11, 11, '1', 1, 'getPortraitTab', 1, 1, NULL, 1, 'html', 'canvas_photo', 1, NULL),
(21, '_UE_USER_STATUS', '', 1, 10, 10, '.5', 1, 'getStatusTab', 14, 1, NULL, 1, 'html', 'canvas_main_right', 1, NULL),
(22, '_UE_PMSTAB', '', 6, 10, 10, '.5', 0, 'getmypmsproTab', 15, 0, NULL, 1, 'menunested', 'canvas_main_middle', 1, NULL),
(23, 'Activity', '', 99, 12, 1, '.5', 1, 'cbactivityTab', 547, 0, NULL, 0, 'menunested', 'canvas_main_middle', 1, NULL),
(24, 'Blocks', '', 99, 13, 1, '.5', 1, 'cbantispamTabBlocks', 548, 0, NULL, 0, 'menunested', 'canvas_main_middle', 1, NULL),
(25, 'Whitelists', '', 99, 14, 1, '.5', 1, 'cbantispamTabWhitelists', 548, 0, NULL, 0, 'menunested', 'canvas_main_middle', 1, NULL),
(26, 'Attempts', '', 99, 15, 1, '.5', 1, 'cbantispamTabAttempts', 548, 0, NULL, 0, 'menunested', 'canvas_main_middle', 1, NULL),
(27, 'Log', '', 99, 16, 1, '.5', 1, 'cbantispamTabLog', 548, 0, NULL, 0, 'menunested', 'canvas_main_middle', 1, NULL),
(28, 'Gallery', '', 99, 17, 1, '.5', 1, 'cbgalleryTab', 555, 0, NULL, 0, 'menunested', 'canvas_main_middle', 1, NULL),
(29, 'Invites', '', 99, 18, 1, '.5', 1, 'cbinvitesTab', 558, 0, NULL, 0, 'tab', 'cb_tabmain', 1, NULL),
(30, 'Last Visitors', '', 99, 19, 1, '.5', 1, 'getLastViewsTab', 559, 0, NULL, 0, 'menunested', 'canvas_main_middle', 1, NULL),
(31, 'Update Log', 'This tab contains a log of profile updates made by user or moderators', 99, 20, 12, '.5', 1, 'getcbpuloggerTab', 561, 0, NULL, 0, 'tab', 'cb_tabmain', 1, NULL),
(32, 'ProfileBook', '', 99, 21, 13, '.5', 1, 'getprofilebookTab', 562, 1, NULL, 0, 'tab', 'cb_tabmain', 1, NULL),
(33, 'ProfileBlog', '', 99, 22, 14, '.5', 1, 'getprofilebookblogTab', 562, 1, NULL, 0, 'tab', 'cb_tabmain', 1, NULL),
(34, 'ProfileWall', '', 99, 23, 15, '.5', 1, 'getprofilebookwallTab', 562, 1, NULL, 0, 'tab', 'cb_tabmain', 1, NULL),
(35, 'Groups', '', 99, 24, 1, '.5', 1, 'cbgjTab', 568, 0, NULL, 0, 'tab', 'cb_tabmain', 1, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_userreports`
--

CREATE TABLE `orexv_comprofiler_userreports` (
  `reportid` int(11) NOT NULL,
  `reporteduser` int(11) NOT NULL DEFAULT '0',
  `reportedbyuser` int(11) NOT NULL DEFAULT '0',
  `reportedondate` date NOT NULL DEFAULT '0000-00-00',
  `reportexplaination` text NOT NULL,
  `reportedstatus` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_comprofiler_views`
--

CREATE TABLE `orexv_comprofiler_views` (
  `viewer_id` int(11) NOT NULL DEFAULT '0',
  `profile_id` int(11) NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  `lastview` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `viewscount` int(11) NOT NULL DEFAULT '0',
  `vote` tinyint(3) DEFAULT NULL,
  `lastvote` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_contact_details`
--

CREATE TABLE `orexv_contact_details` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `con_position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `suburb` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `misc` mediumtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_con` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `webpage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sortname1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sortname2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sortname3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadesc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `xreference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_content`
--

CREATE TABLE `orexv_content` (
  `id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `introtext` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `fulltext` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `urls` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribs` varchar(5120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadesc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `metadata` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The language code for the article.',
  `xreference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A reference to enable linkages to external data sets.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_content`
--

INSERT INTO `orexv_content` (`id`, `asset_id`, `title`, `alias`, `introtext`, `fulltext`, `state`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`, `featured`, `language`, `xreference`) VALUES
(1, 69, 'Харрис, Харрис: Цифровая схемотехника и архитектура компьютера. Дополнение по архитектуре ARM ', 'kharris-kharris-tsifrovaya-skhemotekhnika-i-arkhitektura-kompyutera-dopolnenie-po-arkhitekture-arm', '<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\">Автор:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/187439/\">Харрис Дэвид М.</a>,&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/187440/\">Харрис Сара Л.</a></div>\r\n<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\">Переводчик:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/63408/\">Слинкин А. А.</a></div>\r\n<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\">Редактор:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/205256/\">Косолобов Д. А.</a></div>\r\n<div class=\"publisher\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\">Издательство:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/pubhouse/1416/\">ДМК-Пресс</a>, 2019 г.</div>\r\n<div class=\"genre\" style=\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\">Жанр:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/genres/2575/\">Железо ПК</a>&nbsp;и др.</div>\r\n<div class=\"genre\" style=\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\">&nbsp;</div>\r\n<div class=\"genre\" style=\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\"><span id=\"ctrlcopy\" style=\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0; opacity: 0;\"><br style=\"margin: 0px; padding: 0px;\" />Подробнее:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/books/662585/\">https://www.labirint.ru/books/662585/</a></span></div>\r\n<p><span class=\"buying-pricenew-val-number\" style=\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\">342</span><span style=\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\">&nbsp;mdl</span><span class=\"buying-pricenew-val-currency\" style=\"margin: 0px; padding: 0px; font-size: 14px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; background-color: #f0eded;\">.</span></p>\r\n', '\r\n<p>&nbsp;</p>\r\n<p>Аннотация к книге \"Цифровая схемотехника и архитектура компьютера. Дополнение по архитектуре ARM\" Данное издание является дополнением к книге \"Цифровая схемотехника и архитектура компьютера\" с описанием отличий архитектуры ARM от MIPS, описанной в первой книге. Оно состоит из глав, посвященных архитектуре процессоров ARM, их микроархитектуре, описанию подсистемы памяти и системы ввода-вывода. Также в приложении приведена система команд ARM. Книгу рекомендуется использовать совместно с первым (основным) изданием по архитектуре MIPS. Издание будет полезно студентам, инженерам, а также широкому кругу читателей, интересующихся современной схемотехникой.&nbsp;</p>', 1, 2, '2018-09-27 14:44:36', 15, '', '2018-09-27 15:34:05', 15, 0, '0000-00-00 00:00:00', '2018-09-27 14:44:36', '0000-00-00 00:00:00', '{\"image_intro\":\"images\\/catalog\\/big1.jpg\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"images\\/catalog\\/big1.jpg\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}', '{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}', '{\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}', 7, 4, '', '', 1, 8, '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}', 0, '*', ''),
(2, 71, 'Александр Алексеев: Курсовое проектирование для криптографов. Учебное пособие ', 'aleksandr-alekseev-kursovoe-proektirovanie-dlya-kriptografov-uchebnoe-posobie', '<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Автор:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/98473/\">Алексеев Александр Петрович</a></div>\r\n<div class=\"publisher\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Издательство:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/pubhouse/1418/\">Солон-пресс</a>, 2018 г.</div>\r\n<div class=\"series\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Серия:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/series/26335/\">Библиотека студента</a>\r\n<div class=\"series\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">\r\n<div class=\"series\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\"><a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/series/26335/\"></a><span id=\"ctrlcopy\" style=\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\">Подробнее:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #1868a0;\" href=\"https://www.labirint.ru/books/655859/\">https://www.labirint.ru/books/655859/</a></span></div>\r\n</div>\r\n</div>\r\n<p><span class=\"buying-pricenew-val-number\" style=\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">1272</span><span style=\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\"></span><span class=\"buying-pricenew-val-currency\" style=\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">mdl.</span></p>\r\n', '\r\n<p>&nbsp;</p>\r\n<p>Аннотация к книге \"Курсовое проектирование для криптографов. Учебное пособие\" Учебное пособие содержит задание на курсовое проектирование и методические указания для выполнения задания. Описаны методы сжатия информации (Хаффмана, RLE), помехоустойчивого кодирования (коды Хэмминга и БЧХ), шифрования (аддитивный шифр и шифр с управляемыми операциями), стеганографического сокрытия информации (скрытая передача информации в графическом файле формата BMP, в звуковом файле формата WAV, на HTML-страницах), описан порядок моделирования цифровых устройств (систем шифрования, регистра сдвига и устройства деления полиномов). Данная работа является составной частью учебно-методического комплекса, подготовленного автором. Комплекс включает в себя лекции, методические указания на выполнении лабораторных работ в двух семестрах и сборник задач для практических занятий. Учебное пособие по дисциплине \"Информатика\" для студентов специальностей 10.03.01 и 10.05.02&nbsp;</p>', 1, 2, '2018-09-27 14:50:15', 15, '', '2018-09-27 15:33:38', 15, 0, '0000-00-00 00:00:00', '2018-09-27 14:50:15', '0000-00-00 00:00:00', '{\"image_intro\":\"images\\/catalog\\/big2.jpg\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"images\\/catalog\\/big2.jpg\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}', '{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}', '{\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}', 4, 3, '', '', 1, 4, '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}', 0, '*', ''),
(3, 72, 'Гулаков, Трубаков, Трубаков: Структуры и алгоритмы обработки многомерных данных', 'gulakov-trubakov-trubakov-struktury-i-algoritmy-obrabotki-mnogomernykh-dannykh', '<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Автор:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/204282/\">Гулаков Василий Константинович</a>,&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/204283/\">Трубаков Андрей Олегович</a>,&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/204284/\">Трубаков Евгений Олегович</a></div>\r\n<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Редактор:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/69777/\">Макаров С. В.</a></div>\r\n<div class=\"publisher\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Издательство:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/pubhouse/73/\">Лань</a>, 2018 г.</div>\r\n<div class=\"series\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Серия:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/series/30905/\">Учебники для вузов. Специальная литература<span id=\"ctrlcopy\" style=\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\"><br style=\"margin: 0px; padding: 0px;\" />Подробнее:&nbsp;</span></a><a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/books/653486/\"><br /></a></div>\r\n<p><span class=\"buying-pricenew-val-number\" style=\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">1981</span><span style=\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\">&nbsp;mdl</span><span class=\"buying-pricenew-val-currency\" style=\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">.</span></p>\r\n', '\r\n<p>&nbsp;</p>\r\n<p>Аннотация к книге \"Структуры и алгоритмы обработки многомерных данных\" Книга посвящена описанию структур и алгоритмов для индексирования и обработки многомерных данных. В ней систематизированы наиболее важные подходы, описаны их математические и логические принципы построения, проанализированы достоинства и недостатки. Содержится большое число примеров листинга, позволяющих более детально разобраться в представленных подходах. На различных примерах рассматриваются особенности проектирования и разработки приложений, обрабатывающих многомерные и многоатрибутные данные. Монография предназначена для бакалавров и магистров, обучающихся по направлениям «Информатика и вычислительная техника», «Программная инженерия», «Математическое обеспечение и администрирование информационных систем», а также по близким направлениям. Также она будет полезна научным работникам, преподавателям, специалистам, аспирантам, связанным с прикладной математикой и разработкой программного обеспечения. Можно использовать специалистам, занимающимся хранилищами данных, поиском информации... Подробнее: https://www.labirint.ru/books/653486/</p>', 1, 2, '2018-09-27 15:23:56', 15, '', '2018-09-27 15:33:10', 15, 0, '0000-00-00 00:00:00', '2018-09-27 15:23:56', '0000-00-00 00:00:00', '{\"image_intro\":\"images\\/catalog\\/big4.jpg\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"images\\/catalog\\/big4.jpg\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}', '{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}', '{\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}', 3, 2, '', '', 1, 0, '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}', 0, '*', ''),
(4, 73, 'Адам Гринфилд: Радикальные технологии: устройство повседневной жизни ', 'adam-grinfild-radikalnye-tekhnologii-ustrojstvo-povsednevnoj-zhizni', '<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Автор:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/203673/\">Гринфилд Адам</a></div>\r\n<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Переводчик:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/105260/\">Кушнарева Инна</a></div>\r\n<div class=\"authors\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Редактор:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/authors/49760/\">Попова Е. В.</a></div>\r\n<div class=\"publisher\" style=\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Издательство:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/pubhouse/2362/\">Дело</a>, 2018 г.</div>\r\n<div class=\"genre\" style=\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">Жанр:&nbsp;<a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/genres/2313/\">Информатика<span id=\"ctrlcopy\" style=\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\"><br style=\"margin: 0px; padding: 0px;\" />Подробнее:&nbsp;</span></a><a style=\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\" href=\"https://www.labirint.ru/books/651886/\"><br /><span class=\"buying-pricenew-val-number\" style=\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">480 mdl</span><span class=\"buying-pricenew-val-currency\" style=\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\">.</span><br /></a></div>\r\n', '\r\n<p>&nbsp;</p>\r\n<p>Аннотация к книге \"Радикальные технологии: устройство повседневной жизни\" Всюду, куда бы мы ни обратили свой взор, блистательные новые устройства обещают полностью преобразить нашу жизнь. Но какой ценой? В этой своевременной и откровенной книге, посвященной нашей информационной эпохе, ведущий технологический мыслитель Адам Гринфилд заставляет пересмотреть наши отношения с сетевыми объектами, сервисами и пространствами. Мы уже зависим от смартфона практически во всех аспектах своей жизни. Нам говорят, что инновации - от интерфейсов дополненной реальности и виртуальных помощников до автономных дронов-доставщиков и беспилотных автомобилей - упростят жизнь, сделают ее более удобной и продуктивной. 3D-печать сулит беспрецедентный контроль над формой и распределением материи, а блокчейн обещает произвести революцию во всем - от учета и обмена ценностями до самых прозаичных повседневных вещей. Тем временем невероятно сложные алгоритмические системы действуют незаметно, меняя экономику, трансформируя фундаментальные условия политики и даже предлагая новые... Подробнее: https://www.labirint.ru/books/651886/</p>', 1, 2, '2018-09-27 15:26:17', 15, '', '2018-09-27 15:37:40', 15, 0, '0000-00-00 00:00:00', '2018-09-27 15:26:17', '0000-00-00 00:00:00', '{\"image_intro\":\"images\\/catalog\\/big3.jpg\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"images\\/catalog\\/big3.jpg\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}', '{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}', '{\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}', 7, 1, '', '', 1, 0, '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}', 0, '*', ''),
(5, 75, 'Контакты', 'kontakty', '<p><strong>Найти нас можете по адресу:</strong></p>\r\n<p><img style=\"width: 100%;\" src=\"images/catalog/download.png\" alt=\"\" /></p>\r\n<p>Кишинев 60/2 Корп.4&nbsp;</p>\r\n<p>Номер телефона: 0694447712<br />Факс: 022 962 25 25&nbsp;</p>\r\n<p><strong>Рабочие дни Пн-Пт с 9:00 до 18:00</strong></p>\r\n<p>Все книги преобретаются пока что под заказ и только в нашем офисе.</p>', '', 1, 8, '2018-09-27 15:40:54', 15, '', '2018-09-27 15:44:25', 15, 0, '0000-00-00 00:00:00', '2018-09-27 15:40:54', '0000-00-00 00:00:00', '{\"image_intro\":\"\",\"float_intro\":\"\",\"image_intro_alt\":\"\",\"image_intro_caption\":\"\",\"image_fulltext\":\"\",\"float_fulltext\":\"\",\"image_fulltext_alt\":\"\",\"image_fulltext_caption\":\"\"}', '{\"urla\":false,\"urlatext\":\"\",\"targeta\":\"\",\"urlb\":false,\"urlbtext\":\"\",\"targetb\":\"\",\"urlc\":false,\"urlctext\":\"\",\"targetc\":\"\"}', '{\"show_title\":\"\",\"link_titles\":\"\",\"show_tags\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_vote\":\"\",\"show_hits\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"alternative_readmore\":\"\",\"article_layout\":\"\",\"show_publishing_options\":\"\",\"show_article_options\":\"\",\"show_urls_images_backend\":\"\",\"show_urls_images_frontend\":\"\"}', 4, 0, '', '', 1, 2, '{\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}', 0, '*', '');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_contentitem_tag_map`
--

CREATE TABLE `orexv_contentitem_tag_map` (
  `type_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `core_content_id` int(10) UNSIGNED NOT NULL COMMENT 'PK from the core content table',
  `content_item_id` int(11) NOT NULL COMMENT 'PK from the content type table',
  `tag_id` int(10) UNSIGNED NOT NULL COMMENT 'PK from the tag table',
  `tag_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date of most recent save for this tag-item',
  `type_id` mediumint(8) NOT NULL COMMENT 'PK from the content_type table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Maps items from content tables to tags';

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_content_frontpage`
--

CREATE TABLE `orexv_content_frontpage` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_content_rating`
--

CREATE TABLE `orexv_content_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `rating_sum` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `rating_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastip` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_content_types`
--

CREATE TABLE `orexv_content_types` (
  `type_id` int(10) UNSIGNED NOT NULL,
  `type_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type_alias` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `table` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_mappings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `router` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content_history_options` varchar(5120) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JSON string for com_contenthistory options'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_content_types`
--

INSERT INTO `orexv_content_types` (`type_id`, `type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
(1, 'Article', 'com_content.article', '{\"special\":{\"dbtable\":\"#__content\",\"key\":\"id\",\"type\":\"Content\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"state\",\"core_alias\":\"alias\",\"core_created_time\":\"created\",\"core_modified_time\":\"modified\",\"core_body\":\"introtext\", \"core_hits\":\"hits\",\"core_publish_up\":\"publish_up\",\"core_publish_down\":\"publish_down\",\"core_access\":\"access\", \"core_params\":\"attribs\", \"core_featured\":\"featured\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"images\", \"core_urls\":\"urls\", \"core_version\":\"version\", \"core_ordering\":\"ordering\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"catid\", \"core_xreference\":\"xreference\", \"asset_id\":\"asset_id\"}, \"special\":{\"fulltext\":\"fulltext\"}}', 'ContentHelperRoute::getArticleRoute', '{\"formFile\":\"administrator\\/components\\/com_content\\/models\\/forms\\/article.xml\", \"hideFields\":[\"asset_id\",\"checked_out\",\"checked_out_time\",\"version\"],\"ignoreChanges\":[\"modified_by\", \"modified\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\"],\"convertToInt\":[\"publish_up\", \"publish_down\", \"featured\", \"ordering\"],\"displayLookup\":[{\"sourceColumn\":\"catid\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"created_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"} ]}'),
(2, 'Contact', 'com_contact.contact', '{\"special\":{\"dbtable\":\"#__contact_details\",\"key\":\"id\",\"type\":\"Contact\",\"prefix\":\"ContactTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"name\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created\",\"core_modified_time\":\"modified\",\"core_body\":\"address\", \"core_hits\":\"hits\",\"core_publish_up\":\"publish_up\",\"core_publish_down\":\"publish_down\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"featured\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"image\", \"core_urls\":\"webpage\", \"core_version\":\"version\", \"core_ordering\":\"ordering\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"catid\", \"core_xreference\":\"xreference\", \"asset_id\":\"null\"}, \"special\":{\"con_position\":\"con_position\",\"suburb\":\"suburb\",\"state\":\"state\",\"country\":\"country\",\"postcode\":\"postcode\",\"telephone\":\"telephone\",\"fax\":\"fax\",\"misc\":\"misc\",\"email_to\":\"email_to\",\"default_con\":\"default_con\",\"user_id\":\"user_id\",\"mobile\":\"mobile\",\"sortname1\":\"sortname1\",\"sortname2\":\"sortname2\",\"sortname3\":\"sortname3\"}}', 'ContactHelperRoute::getContactRoute', '{\"formFile\":\"administrator\\/components\\/com_contact\\/models\\/forms\\/contact.xml\",\"hideFields\":[\"default_con\",\"checked_out\",\"checked_out_time\",\"version\",\"xreference\"],\"ignoreChanges\":[\"modified_by\", \"modified\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\"],\"convertToInt\":[\"publish_up\", \"publish_down\", \"featured\", \"ordering\"], \"displayLookup\":[ {\"sourceColumn\":\"created_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"catid\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"} ] }'),
(3, 'Newsfeed', 'com_newsfeeds.newsfeed', '{\"special\":{\"dbtable\":\"#__newsfeeds\",\"key\":\"id\",\"type\":\"Newsfeed\",\"prefix\":\"NewsfeedsTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"name\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created\",\"core_modified_time\":\"modified\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"publish_up\",\"core_publish_down\":\"publish_down\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"featured\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"images\", \"core_urls\":\"link\", \"core_version\":\"version\", \"core_ordering\":\"ordering\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"catid\", \"core_xreference\":\"xreference\", \"asset_id\":\"null\"}, \"special\":{\"numarticles\":\"numarticles\",\"cache_time\":\"cache_time\",\"rtl\":\"rtl\"}}', 'NewsfeedsHelperRoute::getNewsfeedRoute', '{\"formFile\":\"administrator\\/components\\/com_newsfeeds\\/models\\/forms\\/newsfeed.xml\",\"hideFields\":[\"asset_id\",\"checked_out\",\"checked_out_time\",\"version\"],\"ignoreChanges\":[\"modified_by\", \"modified\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\"],\"convertToInt\":[\"publish_up\", \"publish_down\", \"featured\", \"ordering\"],\"displayLookup\":[{\"sourceColumn\":\"catid\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"created_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"} ]}'),
(4, 'User', 'com_users.user', '{\"special\":{\"dbtable\":\"#__users\",\"key\":\"id\",\"type\":\"User\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"name\",\"core_state\":\"null\",\"core_alias\":\"username\",\"core_created_time\":\"registerdate\",\"core_modified_time\":\"lastvisitDate\",\"core_body\":\"null\", \"core_hits\":\"null\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"access\":\"null\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"null\", \"core_language\":\"null\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"null\", \"core_ordering\":\"null\", \"core_metakey\":\"null\", \"core_metadesc\":\"null\", \"core_catid\":\"null\", \"core_xreference\":\"null\", \"asset_id\":\"null\"}, \"special\":{}}', 'UsersHelperRoute::getUserRoute', ''),
(5, 'Article Category', 'com_content.category', '{\"special\":{\"dbtable\":\"#__categories\",\"key\":\"id\",\"type\":\"Category\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created_time\",\"core_modified_time\":\"modified_time\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"version\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"parent_id\", \"core_xreference\":\"null\", \"asset_id\":\"asset_id\"}, \"special\":{\"parent_id\":\"parent_id\",\"lft\":\"lft\",\"rgt\":\"rgt\",\"level\":\"level\",\"path\":\"path\",\"extension\":\"extension\",\"note\":\"note\"}}', 'ContentHelperRoute::getCategoryRoute', '{\"formFile\":\"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml\", \"hideFields\":[\"asset_id\",\"checked_out\",\"checked_out_time\",\"version\",\"lft\",\"rgt\",\"level\",\"path\",\"extension\"], \"ignoreChanges\":[\"modified_user_id\", \"modified_time\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\", \"path\"],\"convertToInt\":[\"publish_up\", \"publish_down\"], \"displayLookup\":[{\"sourceColumn\":\"created_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"parent_id\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}]}'),
(6, 'Contact Category', 'com_contact.category', '{\"special\":{\"dbtable\":\"#__categories\",\"key\":\"id\",\"type\":\"Category\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created_time\",\"core_modified_time\":\"modified_time\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"version\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"parent_id\", \"core_xreference\":\"null\", \"asset_id\":\"asset_id\"}, \"special\":{\"parent_id\":\"parent_id\",\"lft\":\"lft\",\"rgt\":\"rgt\",\"level\":\"level\",\"path\":\"path\",\"extension\":\"extension\",\"note\":\"note\"}}', 'ContactHelperRoute::getCategoryRoute', '{\"formFile\":\"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml\", \"hideFields\":[\"asset_id\",\"checked_out\",\"checked_out_time\",\"version\",\"lft\",\"rgt\",\"level\",\"path\",\"extension\"], \"ignoreChanges\":[\"modified_user_id\", \"modified_time\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\", \"path\"],\"convertToInt\":[\"publish_up\", \"publish_down\"], \"displayLookup\":[{\"sourceColumn\":\"created_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"parent_id\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}]}'),
(7, 'Newsfeeds Category', 'com_newsfeeds.category', '{\"special\":{\"dbtable\":\"#__categories\",\"key\":\"id\",\"type\":\"Category\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created_time\",\"core_modified_time\":\"modified_time\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"version\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"parent_id\", \"core_xreference\":\"null\", \"asset_id\":\"asset_id\"}, \"special\":{\"parent_id\":\"parent_id\",\"lft\":\"lft\",\"rgt\":\"rgt\",\"level\":\"level\",\"path\":\"path\",\"extension\":\"extension\",\"note\":\"note\"}}', 'NewsfeedsHelperRoute::getCategoryRoute', '{\"formFile\":\"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml\", \"hideFields\":[\"asset_id\",\"checked_out\",\"checked_out_time\",\"version\",\"lft\",\"rgt\",\"level\",\"path\",\"extension\"], \"ignoreChanges\":[\"modified_user_id\", \"modified_time\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\", \"path\"],\"convertToInt\":[\"publish_up\", \"publish_down\"], \"displayLookup\":[{\"sourceColumn\":\"created_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"parent_id\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}]}'),
(8, 'Tag', 'com_tags.tag', '{\"special\":{\"dbtable\":\"#__tags\",\"key\":\"tag_id\",\"type\":\"Tag\",\"prefix\":\"TagsTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created_time\",\"core_modified_time\":\"modified_time\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"featured\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"images\", \"core_urls\":\"urls\", \"core_version\":\"version\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"null\", \"core_xreference\":\"null\", \"asset_id\":\"null\"}, \"special\":{\"parent_id\":\"parent_id\",\"lft\":\"lft\",\"rgt\":\"rgt\",\"level\":\"level\",\"path\":\"path\"}}', 'TagsHelperRoute::getTagRoute', '{\"formFile\":\"administrator\\/components\\/com_tags\\/models\\/forms\\/tag.xml\", \"hideFields\":[\"checked_out\",\"checked_out_time\",\"version\", \"lft\", \"rgt\", \"level\", \"path\", \"urls\", \"publish_up\", \"publish_down\"],\"ignoreChanges\":[\"modified_user_id\", \"modified_time\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\", \"path\"],\"convertToInt\":[\"publish_up\", \"publish_down\"], \"displayLookup\":[{\"sourceColumn\":\"created_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}, {\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}, {\"sourceColumn\":\"modified_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}]}'),
(9, 'Banner', 'com_banners.banner', '{\"special\":{\"dbtable\":\"#__banners\",\"key\":\"id\",\"type\":\"Banner\",\"prefix\":\"BannersTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"name\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created\",\"core_modified_time\":\"modified\",\"core_body\":\"description\", \"core_hits\":\"null\",\"core_publish_up\":\"publish_up\",\"core_publish_down\":\"publish_down\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"images\", \"core_urls\":\"link\", \"core_version\":\"version\", \"core_ordering\":\"ordering\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"catid\", \"core_xreference\":\"null\", \"asset_id\":\"null\"}, \"special\":{\"imptotal\":\"imptotal\", \"impmade\":\"impmade\", \"clicks\":\"clicks\", \"clickurl\":\"clickurl\", \"custombannercode\":\"custombannercode\", \"cid\":\"cid\", \"purchase_type\":\"purchase_type\", \"track_impressions\":\"track_impressions\", \"track_clicks\":\"track_clicks\"}}', '', '{\"formFile\":\"administrator\\/components\\/com_banners\\/models\\/forms\\/banner.xml\", \"hideFields\":[\"checked_out\",\"checked_out_time\",\"version\", \"reset\"],\"ignoreChanges\":[\"modified_by\", \"modified\", \"checked_out\", \"checked_out_time\", \"version\", \"imptotal\", \"impmade\", \"reset\"], \"convertToInt\":[\"publish_up\", \"publish_down\", \"ordering\"], \"displayLookup\":[{\"sourceColumn\":\"catid\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}, {\"sourceColumn\":\"cid\",\"targetTable\":\"#__banner_clients\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}, {\"sourceColumn\":\"created_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"modified_by\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"} ]}'),
(10, 'Banners Category', 'com_banners.category', '{\"special\":{\"dbtable\":\"#__categories\",\"key\":\"id\",\"type\":\"Category\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created_time\",\"core_modified_time\":\"modified_time\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"version\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"parent_id\", \"core_xreference\":\"null\", \"asset_id\":\"asset_id\"}, \"special\": {\"parent_id\":\"parent_id\",\"lft\":\"lft\",\"rgt\":\"rgt\",\"level\":\"level\",\"path\":\"path\",\"extension\":\"extension\",\"note\":\"note\"}}', '', '{\"formFile\":\"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml\", \"hideFields\":[\"asset_id\",\"checked_out\",\"checked_out_time\",\"version\",\"lft\",\"rgt\",\"level\",\"path\",\"extension\"], \"ignoreChanges\":[\"modified_user_id\", \"modified_time\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\", \"path\"], \"convertToInt\":[\"publish_up\", \"publish_down\"], \"displayLookup\":[{\"sourceColumn\":\"created_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"parent_id\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}]}'),
(11, 'Banner Client', 'com_banners.client', '{\"special\":{\"dbtable\":\"#__banner_clients\",\"key\":\"id\",\"type\":\"Client\",\"prefix\":\"BannersTable\"}}', '', '', '', '{\"formFile\":\"administrator\\/components\\/com_banners\\/models\\/forms\\/client.xml\", \"hideFields\":[\"checked_out\",\"checked_out_time\"], \"ignoreChanges\":[\"checked_out\", \"checked_out_time\"], \"convertToInt\":[], \"displayLookup\":[]}'),
(12, 'User Notes', 'com_users.note', '{\"special\":{\"dbtable\":\"#__user_notes\",\"key\":\"id\",\"type\":\"Note\",\"prefix\":\"UsersTable\"}}', '', '', '', '{\"formFile\":\"administrator\\/components\\/com_users\\/models\\/forms\\/note.xml\", \"hideFields\":[\"checked_out\",\"checked_out_time\", \"publish_up\", \"publish_down\"],\"ignoreChanges\":[\"modified_user_id\", \"modified_time\", \"checked_out\", \"checked_out_time\"], \"convertToInt\":[\"publish_up\", \"publish_down\"],\"displayLookup\":[{\"sourceColumn\":\"catid\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}, {\"sourceColumn\":\"created_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}, {\"sourceColumn\":\"user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}, {\"sourceColumn\":\"modified_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}]}'),
(13, 'User Notes Category', 'com_users.category', '{\"special\":{\"dbtable\":\"#__categories\",\"key\":\"id\",\"type\":\"Category\",\"prefix\":\"JTable\",\"config\":\"array()\"},\"common\":{\"dbtable\":\"#__ucm_content\",\"key\":\"ucm_id\",\"type\":\"Corecontent\",\"prefix\":\"JTable\",\"config\":\"array()\"}}', '', '{\"common\":{\"core_content_item_id\":\"id\",\"core_title\":\"title\",\"core_state\":\"published\",\"core_alias\":\"alias\",\"core_created_time\":\"created_time\",\"core_modified_time\":\"modified_time\",\"core_body\":\"description\", \"core_hits\":\"hits\",\"core_publish_up\":\"null\",\"core_publish_down\":\"null\",\"core_access\":\"access\", \"core_params\":\"params\", \"core_featured\":\"null\", \"core_metadata\":\"metadata\", \"core_language\":\"language\", \"core_images\":\"null\", \"core_urls\":\"null\", \"core_version\":\"version\", \"core_ordering\":\"null\", \"core_metakey\":\"metakey\", \"core_metadesc\":\"metadesc\", \"core_catid\":\"parent_id\", \"core_xreference\":\"null\", \"asset_id\":\"asset_id\"}, \"special\":{\"parent_id\":\"parent_id\",\"lft\":\"lft\",\"rgt\":\"rgt\",\"level\":\"level\",\"path\":\"path\",\"extension\":\"extension\",\"note\":\"note\"}}', '', '{\"formFile\":\"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml\", \"hideFields\":[\"checked_out\",\"checked_out_time\",\"version\",\"lft\",\"rgt\",\"level\",\"path\",\"extension\"], \"ignoreChanges\":[\"modified_user_id\", \"modified_time\", \"checked_out\", \"checked_out_time\", \"version\", \"hits\", \"path\"], \"convertToInt\":[\"publish_up\", \"publish_down\"], \"displayLookup\":[{\"sourceColumn\":\"created_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"}, {\"sourceColumn\":\"access\",\"targetTable\":\"#__viewlevels\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"},{\"sourceColumn\":\"modified_user_id\",\"targetTable\":\"#__users\",\"targetColumn\":\"id\",\"displayColumn\":\"name\"},{\"sourceColumn\":\"parent_id\",\"targetTable\":\"#__categories\",\"targetColumn\":\"id\",\"displayColumn\":\"title\"}]}');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_core_log_searches`
--

CREATE TABLE `orexv_core_log_searches` (
  `search_term` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_extensions`
--

CREATE TABLE `orexv_extensions` (
  `extension_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `element` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `folder` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` tinyint(3) NOT NULL,
  `enabled` tinyint(3) NOT NULL DEFAULT '1',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `protected` tinyint(3) NOT NULL DEFAULT '0',
  `manifest_cache` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) DEFAULT '0',
  `state` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_extensions`
--

INSERT INTO `orexv_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(1, 'com_mailto', 'component', 'com_mailto', '', 0, 1, 1, 1, '{\"name\":\"com_mailto\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\\t\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_MAILTO_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mailto\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(2, 'com_wrapper', 'component', 'com_wrapper', '', 0, 1, 1, 1, '{\"name\":\"com_wrapper\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\\n\\t\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_WRAPPER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"wrapper\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(3, 'com_admin', 'component', 'com_admin', '', 1, 1, 1, 1, '{\"name\":\"com_admin\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_ADMIN_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(4, 'com_banners', 'component', 'com_banners', '', 1, 1, 1, 0, '{\"name\":\"com_banners\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_BANNERS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"banners\"}', '{\"purchase_type\":\"3\",\"track_impressions\":\"0\",\"track_clicks\":\"0\",\"metakey_prefix\":\"\",\"save_history\":\"1\",\"history_limit\":10}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(5, 'com_cache', 'component', 'com_cache', '', 1, 1, 1, 1, '{\"name\":\"com_cache\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_CACHE_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(6, 'com_categories', 'component', 'com_categories', '', 1, 1, 1, 1, '{\"name\":\"com_categories\",\"type\":\"component\",\"creationDate\":\"December 2007\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_CATEGORIES_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(7, 'com_checkin', 'component', 'com_checkin', '', 1, 1, 1, 1, '{\"name\":\"com_checkin\",\"type\":\"component\",\"creationDate\":\"Unknown\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_CHECKIN_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(8, 'com_contact', 'component', 'com_contact', '', 1, 1, 1, 0, '{\"name\":\"com_contact\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_CONTACT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"contact\"}', '{\"show_contact_category\":\"hide\",\"save_history\":\"1\",\"history_limit\":10,\"show_contact_list\":\"0\",\"presentation_style\":\"sliders\",\"show_name\":\"1\",\"show_position\":\"1\",\"show_email\":\"0\",\"show_street_address\":\"1\",\"show_suburb\":\"1\",\"show_state\":\"1\",\"show_postcode\":\"1\",\"show_country\":\"1\",\"show_telephone\":\"1\",\"show_mobile\":\"1\",\"show_fax\":\"1\",\"show_webpage\":\"1\",\"show_misc\":\"1\",\"show_image\":\"1\",\"image\":\"\",\"allow_vcard\":\"0\",\"show_articles\":\"0\",\"show_profile\":\"0\",\"show_links\":\"0\",\"linka_name\":\"\",\"linkb_name\":\"\",\"linkc_name\":\"\",\"linkd_name\":\"\",\"linke_name\":\"\",\"contact_icons\":\"0\",\"icon_address\":\"\",\"icon_email\":\"\",\"icon_telephone\":\"\",\"icon_mobile\":\"\",\"icon_fax\":\"\",\"icon_misc\":\"\",\"show_headings\":\"1\",\"show_position_headings\":\"1\",\"show_email_headings\":\"0\",\"show_telephone_headings\":\"1\",\"show_mobile_headings\":\"0\",\"show_fax_headings\":\"0\",\"allow_vcard_headings\":\"0\",\"show_suburb_headings\":\"1\",\"show_state_headings\":\"1\",\"show_country_headings\":\"1\",\"show_email_form\":\"1\",\"show_email_copy\":\"1\",\"banned_email\":\"\",\"banned_subject\":\"\",\"banned_text\":\"\",\"validate_session\":\"1\",\"custom_reply\":\"0\",\"redirect\":\"\",\"show_category_crumb\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"robots\":\"\",\"author\":\"\",\"rights\":\"\",\"xreference\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(9, 'com_cpanel', 'component', 'com_cpanel', '', 1, 1, 1, 1, '{\"name\":\"com_cpanel\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_CPANEL_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10, 'com_installer', 'component', 'com_installer', '', 1, 1, 1, 1, '{\"name\":\"com_installer\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_INSTALLER_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(11, 'com_languages', 'component', 'com_languages', '', 1, 1, 1, 1, '{\"name\":\"com_languages\",\"type\":\"component\",\"creationDate\":\"2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_LANGUAGES_XML_DESCRIPTION\",\"group\":\"\"}', '{\"administrator\":\"ru-RU\",\"site\":\"ru-RU\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(12, 'com_login', 'component', 'com_login', '', 1, 1, 1, 1, '{\"name\":\"com_login\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_LOGIN_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(13, 'com_media', 'component', 'com_media', '', 1, 1, 0, 1, '{\"name\":\"com_media\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_MEDIA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"media\"}', '{\"upload_extensions\":\"bmp,csv,doc,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\",\"upload_maxsize\":\"10\",\"file_path\":\"images\",\"image_path\":\"images\",\"restrict_uploads\":\"1\",\"allowed_media_usergroup\":\"3\",\"check_mime\":\"1\",\"image_extensions\":\"bmp,gif,jpg,png\",\"ignore_extensions\":\"\",\"upload_mime\":\"image\\/jpeg,image\\/gif,image\\/png,image\\/bmp,application\\/x-shockwave-flash,application\\/msword,application\\/excel,application\\/pdf,application\\/powerpoint,text\\/plain,application\\/x-zip\",\"upload_mime_illegal\":\"text\\/html\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(14, 'com_menus', 'component', 'com_menus', '', 1, 1, 1, 1, '{\"name\":\"com_menus\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_MENUS_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(15, 'com_messages', 'component', 'com_messages', '', 1, 1, 1, 1, '{\"name\":\"com_messages\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_MESSAGES_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(16, 'com_modules', 'component', 'com_modules', '', 1, 1, 1, 1, '{\"name\":\"com_modules\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_MODULES_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(17, 'com_newsfeeds', 'component', 'com_newsfeeds', '', 1, 1, 1, 0, '{\"name\":\"com_newsfeeds\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_NEWSFEEDS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"newsfeeds\"}', '{\"newsfeed_layout\":\"_:default\",\"save_history\":\"1\",\"history_limit\":5,\"show_feed_image\":\"1\",\"show_feed_description\":\"1\",\"show_item_description\":\"1\",\"feed_character_count\":\"0\",\"feed_display_order\":\"des\",\"float_first\":\"right\",\"float_second\":\"right\",\"show_tags\":\"1\",\"category_layout\":\"_:default\",\"show_category_title\":\"1\",\"show_description\":\"1\",\"show_description_image\":\"1\",\"maxLevel\":\"-1\",\"show_empty_categories\":\"0\",\"show_subcat_desc\":\"1\",\"show_cat_items\":\"1\",\"show_cat_tags\":\"1\",\"show_base_description\":\"1\",\"maxLevelcat\":\"-1\",\"show_empty_categories_cat\":\"0\",\"show_subcat_desc_cat\":\"1\",\"show_cat_items_cat\":\"1\",\"filter_field\":\"1\",\"show_pagination_limit\":\"1\",\"show_headings\":\"1\",\"show_articles\":\"0\",\"show_link\":\"1\",\"show_pagination\":\"1\",\"show_pagination_results\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(18, 'com_plugins', 'component', 'com_plugins', '', 1, 1, 1, 1, '{\"name\":\"com_plugins\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_PLUGINS_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(19, 'com_search', 'component', 'com_search', '', 1, 1, 1, 0, '{\"name\":\"com_search\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_SEARCH_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"search\"}', '{\"enabled\":\"0\",\"show_date\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(20, 'com_templates', 'component', 'com_templates', '', 1, 1, 1, 1, '{\"name\":\"com_templates\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_TEMPLATES_XML_DESCRIPTION\",\"group\":\"\"}', '{\"template_positions_display\":\"0\",\"upload_limit\":\"2\",\"image_formats\":\"gif,bmp,jpg,jpeg,png\",\"source_formats\":\"txt,less,ini,xml,js,php,css\",\"font_formats\":\"woff,ttf,otf\",\"compressed_formats\":\"zip\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(22, 'com_content', 'component', 'com_content', '', 1, 1, 0, 1, '{\"name\":\"com_content\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_CONTENT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"content\"}', '{\"article_layout\":\"_:default\",\"show_title\":\"1\",\"link_titles\":\"1\",\"show_intro\":\"1\",\"info_block_position\":\"0\",\"show_category\":\"1\",\"link_category\":\"0\",\"show_parent_category\":\"0\",\"link_parent_category\":\"0\",\"show_author\":\"0\",\"link_author\":\"0\",\"show_create_date\":\"0\",\"show_modify_date\":\"0\",\"show_publish_date\":\"1\",\"show_item_navigation\":\"0\",\"show_vote\":\"0\",\"show_readmore\":\"0\",\"show_readmore_title\":\"0\",\"readmore_limit\":\"30\",\"show_tags\":\"1\",\"show_icons\":\"0\",\"show_print_icon\":\"0\",\"show_email_icon\":\"0\",\"show_hits\":\"0\",\"show_noauth\":\"1\",\"urls_position\":\"0\",\"show_publishing_options\":\"1\",\"show_article_options\":\"1\",\"save_history\":\"1\",\"history_limit\":10,\"show_urls_images_frontend\":\"0\",\"show_urls_images_backend\":\"1\",\"targeta\":0,\"targetb\":0,\"targetc\":0,\"float_intro\":\"left\",\"float_fulltext\":\"left\",\"category_layout\":\"_:blog\",\"show_category_heading_title_text\":\"1\",\"show_category_title\":\"0\",\"show_description\":\"0\",\"show_description_image\":\"0\",\"maxLevel\":\"-1\",\"show_empty_categories\":\"0\",\"show_no_articles\":\"0\",\"show_subcat_desc\":\"0\",\"show_cat_num_articles\":\"0\",\"show_cat_tags\":\"0\",\"show_base_description\":\"1\",\"maxLevelcat\":\"-1\",\"show_empty_categories_cat\":\"0\",\"show_subcat_desc_cat\":\"0\",\"show_cat_num_articles_cat\":\"0\",\"num_leading_articles\":\"999\",\"num_intro_articles\":\"0\",\"num_columns\":\"0\",\"num_links\":\"0\",\"multi_column_order\":\"0\",\"show_subcategory_content\":\"0\",\"show_pagination_limit\":\"1\",\"filter_field\":\"hide\",\"show_headings\":\"1\",\"list_show_date\":\"0\",\"date_format\":\"\",\"list_show_hits\":\"1\",\"list_show_author\":\"1\",\"orderby_pri\":\"order\",\"orderby_sec\":\"rdate\",\"order_date\":\"published\",\"show_pagination\":\"2\",\"show_pagination_results\":\"1\",\"show_featured\":\"show\",\"show_feed_link\":\"1\",\"feed_summary\":\"0\",\"feed_show_readmore\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(23, 'com_config', 'component', 'com_config', '', 1, 1, 0, 1, '{\"name\":\"com_config\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_CONFIG_XML_DESCRIPTION\",\"group\":\"\"}', '{\"filters\":{\"1\":{\"filter_type\":\"NH\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"9\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"6\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"7\":{\"filter_type\":\"NONE\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"2\":{\"filter_type\":\"NH\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"3\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"4\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"5\":{\"filter_type\":\"BL\",\"filter_tags\":\"\",\"filter_attributes\":\"\"},\"8\":{\"filter_type\":\"NONE\",\"filter_tags\":\"\",\"filter_attributes\":\"\"}}}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(24, 'com_redirect', 'component', 'com_redirect', '', 1, 1, 0, 1, '{\"name\":\"com_redirect\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_REDIRECT_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(25, 'com_users', 'component', 'com_users', '', 1, 1, 0, 1, '{\"name\":\"com_users\",\"type\":\"component\",\"creationDate\":\"April 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_USERS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"users\"}', '{\"allowUserRegistration\":\"0\",\"new_usertype\":\"2\",\"guest_usergroup\":\"9\",\"sendpassword\":\"1\",\"useractivation\":\"1\",\"mail_to_admin\":\"0\",\"captcha\":\"\",\"frontend_userparams\":\"1\",\"site_language\":\"0\",\"change_login_name\":\"0\",\"reset_count\":\"10\",\"reset_time\":\"1\",\"minimum_length\":\"4\",\"minimum_integers\":\"0\",\"minimum_symbols\":\"0\",\"minimum_uppercase\":\"0\",\"save_history\":\"1\",\"history_limit\":5,\"mailSubjectPrefix\":\"\",\"mailBodySuffix\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(27, 'com_finder', 'component', 'com_finder', '', 1, 1, 0, 0, '{\"name\":\"com_finder\",\"type\":\"component\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_FINDER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"finder\"}', '{\"show_description\":\"1\",\"description_length\":255,\"allow_empty_query\":\"0\",\"show_url\":\"1\",\"show_advanced\":\"1\",\"expand_advanced\":\"0\",\"show_date_filters\":\"0\",\"highlight_terms\":\"1\",\"opensearch_name\":\"\",\"opensearch_description\":\"\",\"batch_size\":\"50\",\"memory_table_limit\":30000,\"title_multiplier\":\"1.7\",\"text_multiplier\":\"0.7\",\"meta_multiplier\":\"1.2\",\"path_multiplier\":\"2.0\",\"misc_multiplier\":\"0.3\",\"stemmer\":\"snowball\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(28, 'com_joomlaupdate', 'component', 'com_joomlaupdate', '', 1, 1, 0, 1, '{\"name\":\"com_joomlaupdate\",\"type\":\"component\",\"creationDate\":\"February 2012\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"COM_JOOMLAUPDATE_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(29, 'com_tags', 'component', 'com_tags', '', 1, 1, 1, 1, '{\"name\":\"com_tags\",\"type\":\"component\",\"creationDate\":\"December 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.1.0\",\"description\":\"COM_TAGS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"tags\"}', '{\"tag_layout\":\"_:default\",\"save_history\":\"1\",\"history_limit\":5,\"show_tag_title\":\"0\",\"tag_list_show_tag_image\":\"0\",\"tag_list_show_tag_description\":\"0\",\"tag_list_image\":\"\",\"show_tag_num_items\":\"0\",\"tag_list_orderby\":\"title\",\"tag_list_orderby_direction\":\"ASC\",\"show_headings\":\"0\",\"tag_list_show_date\":\"0\",\"tag_list_show_item_image\":\"0\",\"tag_list_show_item_description\":\"0\",\"tag_list_item_maximum_characters\":0,\"return_any_or_all\":\"1\",\"include_children\":\"0\",\"maximum\":200,\"tag_list_language_filter\":\"all\",\"tags_layout\":\"_:default\",\"all_tags_orderby\":\"title\",\"all_tags_orderby_direction\":\"ASC\",\"all_tags_show_tag_image\":\"0\",\"all_tags_show_tag_descripion\":\"0\",\"all_tags_tag_maximum_characters\":20,\"all_tags_show_tag_hits\":\"0\",\"filter_field\":\"1\",\"show_pagination_limit\":\"1\",\"show_pagination\":\"2\",\"show_pagination_results\":\"1\",\"tag_field_ajax_mode\":\"1\",\"show_feed_link\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(30, 'com_contenthistory', 'component', 'com_contenthistory', '', 1, 1, 1, 0, '{\"name\":\"com_contenthistory\",\"type\":\"component\",\"creationDate\":\"May 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.2.0\",\"description\":\"COM_CONTENTHISTORY_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"contenthistory\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(31, 'com_ajax', 'component', 'com_ajax', '', 1, 1, 1, 0, '{\"name\":\"com_ajax\",\"type\":\"component\",\"creationDate\":\"August 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.2.0\",\"description\":\"COM_AJAX_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"ajax\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(32, 'com_postinstall', 'component', 'com_postinstall', '', 1, 1, 1, 1, '{\"name\":\"com_postinstall\",\"type\":\"component\",\"creationDate\":\"September 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.2.0\",\"description\":\"COM_POSTINSTALL_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(101, 'SimplePie', 'library', 'simplepie', '', 0, 1, 1, 1, '{\"name\":\"SimplePie\",\"type\":\"library\",\"creationDate\":\"2004\",\"author\":\"SimplePie\",\"copyright\":\"Copyright (c) 2004-2009, Ryan Parman and Geoffrey Sneddon\",\"authorEmail\":\"\",\"authorUrl\":\"http:\\/\\/simplepie.org\\/\",\"version\":\"1.2\",\"description\":\"LIB_SIMPLEPIE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"simplepie\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(102, 'phputf8', 'library', 'phputf8', '', 0, 1, 1, 1, '{\"name\":\"phputf8\",\"type\":\"library\",\"creationDate\":\"2006\",\"author\":\"Harry Fuecks\",\"copyright\":\"Copyright various authors\",\"authorEmail\":\"hfuecks@gmail.com\",\"authorUrl\":\"http:\\/\\/sourceforge.net\\/projects\\/phputf8\",\"version\":\"0.5\",\"description\":\"LIB_PHPUTF8_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"phputf8\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(103, 'Joomla! Platform', 'library', 'joomla', '', 0, 1, 1, 1, '{\"name\":\"Joomla! Platform\",\"type\":\"library\",\"creationDate\":\"2008\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"https:\\/\\/www.joomla.org\",\"version\":\"13.1\",\"description\":\"LIB_JOOMLA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"joomla\"}', '{\"mediaversion\":\"0089499093447469634baa37e25c7564\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(104, 'IDNA Convert', 'library', 'idna_convert', '', 0, 1, 1, 1, '{\"name\":\"IDNA Convert\",\"type\":\"library\",\"creationDate\":\"2004\",\"author\":\"phlyLabs\",\"copyright\":\"2004-2011 phlyLabs Berlin, http:\\/\\/phlylabs.de\",\"authorEmail\":\"phlymail@phlylabs.de\",\"authorUrl\":\"http:\\/\\/phlylabs.de\",\"version\":\"0.8.0\",\"description\":\"LIB_IDNA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"idna_convert\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(105, 'FOF', 'library', 'fof', '', 0, 1, 1, 1, '{\"name\":\"FOF\",\"type\":\"library\",\"creationDate\":\"2015-04-22 13:15:32\",\"author\":\"Nicholas K. Dionysopoulos \\/ Akeeba Ltd\",\"copyright\":\"(C)2011-2015 Nicholas K. Dionysopoulos\",\"authorEmail\":\"nicholas@akeebabackup.com\",\"authorUrl\":\"https:\\/\\/www.akeebabackup.com\",\"version\":\"2.4.3\",\"description\":\"LIB_FOF_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"fof\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(106, 'PHPass', 'library', 'phpass', '', 0, 1, 1, 1, '{\"name\":\"PHPass\",\"type\":\"library\",\"creationDate\":\"2004-2006\",\"author\":\"Solar Designer\",\"copyright\":\"\",\"authorEmail\":\"solar@openwall.com\",\"authorUrl\":\"http:\\/\\/www.openwall.com\\/phpass\\/\",\"version\":\"0.3\",\"description\":\"LIB_PHPASS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"phpass\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(200, 'mod_articles_archive', 'module', 'mod_articles_archive', '', 0, 1, 1, 0, '{\"name\":\"mod_articles_archive\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_ARTICLES_ARCHIVE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_articles_archive\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(201, 'mod_articles_latest', 'module', 'mod_articles_latest', '', 0, 1, 1, 0, '{\"name\":\"mod_articles_latest\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_LATEST_NEWS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_articles_latest\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(202, 'mod_articles_popular', 'module', 'mod_articles_popular', '', 0, 1, 1, 0, '{\"name\":\"mod_articles_popular\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_POPULAR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_articles_popular\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(203, 'mod_banners', 'module', 'mod_banners', '', 0, 1, 1, 0, '{\"name\":\"mod_banners\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_BANNERS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_banners\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(204, 'mod_breadcrumbs', 'module', 'mod_breadcrumbs', '', 0, 1, 1, 1, '{\"name\":\"mod_breadcrumbs\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_BREADCRUMBS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_breadcrumbs\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(205, 'mod_custom', 'module', 'mod_custom', '', 0, 1, 1, 1, '{\"name\":\"mod_custom\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_CUSTOM_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_custom\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(206, 'mod_feed', 'module', 'mod_feed', '', 0, 1, 1, 0, '{\"name\":\"mod_feed\",\"type\":\"module\",\"creationDate\":\"July 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_FEED_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_feed\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(207, 'mod_footer', 'module', 'mod_footer', '', 0, 1, 1, 0, '{\"name\":\"mod_footer\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_FOOTER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_footer\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(208, 'mod_login', 'module', 'mod_login', '', 0, 1, 1, 1, '{\"name\":\"mod_login\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_LOGIN_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_login\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(209, 'mod_menu', 'module', 'mod_menu', '', 0, 1, 1, 1, '{\"name\":\"mod_menu\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_MENU_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_menu\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(210, 'mod_articles_news', 'module', 'mod_articles_news', '', 0, 1, 1, 0, '{\"name\":\"mod_articles_news\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_ARTICLES_NEWS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_articles_news\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(211, 'mod_random_image', 'module', 'mod_random_image', '', 0, 1, 1, 0, '{\"name\":\"mod_random_image\",\"type\":\"module\",\"creationDate\":\"July 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_RANDOM_IMAGE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_random_image\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(212, 'mod_related_items', 'module', 'mod_related_items', '', 0, 1, 1, 0, '{\"name\":\"mod_related_items\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_RELATED_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_related_items\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(213, 'mod_search', 'module', 'mod_search', '', 0, 1, 1, 0, '{\"name\":\"mod_search\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_SEARCH_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_search\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(214, 'mod_stats', 'module', 'mod_stats', '', 0, 1, 1, 0, '{\"name\":\"mod_stats\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_STATS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_stats\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(215, 'mod_syndicate', 'module', 'mod_syndicate', '', 0, 1, 1, 1, '{\"name\":\"mod_syndicate\",\"type\":\"module\",\"creationDate\":\"May 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_SYNDICATE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_syndicate\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(216, 'mod_users_latest', 'module', 'mod_users_latest', '', 0, 1, 1, 0, '{\"name\":\"mod_users_latest\",\"type\":\"module\",\"creationDate\":\"December 2009\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_USERS_LATEST_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_users_latest\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(218, 'mod_whosonline', 'module', 'mod_whosonline', '', 0, 1, 1, 0, '{\"name\":\"mod_whosonline\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_WHOSONLINE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_whosonline\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(219, 'mod_wrapper', 'module', 'mod_wrapper', '', 0, 1, 1, 0, '{\"name\":\"mod_wrapper\",\"type\":\"module\",\"creationDate\":\"October 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_WRAPPER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_wrapper\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(220, 'mod_articles_category', 'module', 'mod_articles_category', '', 0, 1, 1, 0, '{\"name\":\"mod_articles_category\",\"type\":\"module\",\"creationDate\":\"February 2010\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_ARTICLES_CATEGORY_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_articles_category\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(221, 'mod_articles_categories', 'module', 'mod_articles_categories', '', 0, 1, 1, 0, '{\"name\":\"mod_articles_categories\",\"type\":\"module\",\"creationDate\":\"February 2010\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_ARTICLES_CATEGORIES_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_articles_categories\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(222, 'mod_languages', 'module', 'mod_languages', '', 0, 1, 1, 1, '{\"name\":\"mod_languages\",\"type\":\"module\",\"creationDate\":\"February 2010\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_LANGUAGES_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_languages\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(223, 'mod_finder', 'module', 'mod_finder', '', 0, 1, 0, 0, '{\"name\":\"mod_finder\",\"type\":\"module\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_FINDER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_finder\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(300, 'mod_custom', 'module', 'mod_custom', '', 1, 1, 1, 1, '{\"name\":\"mod_custom\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_CUSTOM_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_custom\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(301, 'mod_feed', 'module', 'mod_feed', '', 1, 1, 1, 0, '{\"name\":\"mod_feed\",\"type\":\"module\",\"creationDate\":\"July 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_FEED_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_feed\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(302, 'mod_latest', 'module', 'mod_latest', '', 1, 1, 1, 0, '{\"name\":\"mod_latest\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_LATEST_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_latest\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(303, 'mod_logged', 'module', 'mod_logged', '', 1, 1, 1, 0, '{\"name\":\"mod_logged\",\"type\":\"module\",\"creationDate\":\"January 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_LOGGED_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_logged\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(304, 'mod_login', 'module', 'mod_login', '', 1, 1, 1, 1, '{\"name\":\"mod_login\",\"type\":\"module\",\"creationDate\":\"March 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_LOGIN_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_login\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(305, 'mod_menu', 'module', 'mod_menu', '', 1, 1, 1, 0, '{\"name\":\"mod_menu\",\"type\":\"module\",\"creationDate\":\"March 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_MENU_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_menu\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(307, 'mod_popular', 'module', 'mod_popular', '', 1, 1, 1, 0, '{\"name\":\"mod_popular\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_POPULAR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_popular\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(308, 'mod_quickicon', 'module', 'mod_quickicon', '', 1, 1, 1, 1, '{\"name\":\"mod_quickicon\",\"type\":\"module\",\"creationDate\":\"Nov 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_QUICKICON_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_quickicon\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(309, 'mod_status', 'module', 'mod_status', '', 1, 1, 1, 0, '{\"name\":\"mod_status\",\"type\":\"module\",\"creationDate\":\"Feb 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_STATUS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_status\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(310, 'mod_submenu', 'module', 'mod_submenu', '', 1, 1, 1, 0, '{\"name\":\"mod_submenu\",\"type\":\"module\",\"creationDate\":\"Feb 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_SUBMENU_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_submenu\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(311, 'mod_title', 'module', 'mod_title', '', 1, 1, 1, 0, '{\"name\":\"mod_title\",\"type\":\"module\",\"creationDate\":\"Nov 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_TITLE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_title\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(312, 'mod_toolbar', 'module', 'mod_toolbar', '', 1, 1, 1, 1, '{\"name\":\"mod_toolbar\",\"type\":\"module\",\"creationDate\":\"Nov 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_TOOLBAR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_toolbar\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(313, 'mod_multilangstatus', 'module', 'mod_multilangstatus', '', 1, 1, 1, 0, '{\"name\":\"mod_multilangstatus\",\"type\":\"module\",\"creationDate\":\"September 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_MULTILANGSTATUS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_multilangstatus\"}', '{\"cache\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(314, 'mod_version', 'module', 'mod_version', '', 1, 1, 1, 0, '{\"name\":\"mod_version\",\"type\":\"module\",\"creationDate\":\"January 2012\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_VERSION_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_version\"}', '{\"format\":\"short\",\"product\":\"1\",\"cache\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(315, 'mod_stats_admin', 'module', 'mod_stats_admin', '', 1, 1, 1, 0, '{\"name\":\"mod_stats_admin\",\"type\":\"module\",\"creationDate\":\"July 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"MOD_STATS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_stats_admin\"}', '{\"serverinfo\":\"0\",\"siteinfo\":\"0\",\"counter\":\"0\",\"increase\":\"0\",\"cache\":\"1\",\"cache_time\":\"900\",\"cachemode\":\"static\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(316, 'mod_tags_popular', 'module', 'mod_tags_popular', '', 0, 1, 1, 0, '{\"name\":\"mod_tags_popular\",\"type\":\"module\",\"creationDate\":\"January 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.1.0\",\"description\":\"MOD_TAGS_POPULAR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_tags_popular\"}', '{\"maximum\":\"5\",\"timeframe\":\"alltime\",\"owncache\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(317, 'mod_tags_similar', 'module', 'mod_tags_similar', '', 0, 1, 1, 0, '{\"name\":\"mod_tags_similar\",\"type\":\"module\",\"creationDate\":\"January 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.1.0\",\"description\":\"MOD_TAGS_SIMILAR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mod_tags_similar\"}', '{\"maximum\":\"5\",\"matchtype\":\"any\",\"owncache\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(400, 'plg_authentication_gmail', 'plugin', 'gmail', 'authentication', 0, 0, 1, 0, '{\"name\":\"plg_authentication_gmail\",\"type\":\"plugin\",\"creationDate\":\"February 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_GMAIL_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"gmail\"}', '{\"applysuffix\":\"0\",\"suffix\":\"\",\"verifypeer\":\"1\",\"user_blacklist\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(401, 'plg_authentication_joomla', 'plugin', 'joomla', 'authentication', 0, 1, 1, 1, '{\"name\":\"plg_authentication_joomla\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_AUTH_JOOMLA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"joomla\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(402, 'plg_authentication_ldap', 'plugin', 'ldap', 'authentication', 0, 0, 1, 0, '{\"name\":\"plg_authentication_ldap\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_LDAP_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"ldap\"}', '{\"host\":\"\",\"port\":\"389\",\"use_ldapV3\":\"0\",\"negotiate_tls\":\"0\",\"no_referrals\":\"0\",\"auth_method\":\"bind\",\"base_dn\":\"\",\"search_string\":\"\",\"users_dn\":\"\",\"username\":\"admin\",\"password\":\"bobby7\",\"ldap_fullname\":\"fullName\",\"ldap_email\":\"mail\",\"ldap_uid\":\"uid\"}', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(403, 'plg_content_contact', 'plugin', 'contact', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_contact\",\"type\":\"plugin\",\"creationDate\":\"January 2014\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.2.2\",\"description\":\"PLG_CONTENT_CONTACT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"contact\"}', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(404, 'plg_content_emailcloak', 'plugin', 'emailcloak', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_emailcloak\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_CONTENT_EMAILCLOAK_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"emailcloak\"}', '{\"mode\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(406, 'plg_content_loadmodule', 'plugin', 'loadmodule', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_loadmodule\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_LOADMODULE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"loadmodule\"}', '{\"style\":\"xhtml\"}', '', '', 0, '2011-09-18 15:22:50', 0, 0),
(407, 'plg_content_pagebreak', 'plugin', 'pagebreak', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_pagebreak\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_CONTENT_PAGEBREAK_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"pagebreak\"}', '{\"title\":\"1\",\"multipage_toc\":\"1\",\"showall\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 4, 0);
INSERT INTO `orexv_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(408, 'plg_content_pagenavigation', 'plugin', 'pagenavigation', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_pagenavigation\",\"type\":\"plugin\",\"creationDate\":\"January 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_PAGENAVIGATION_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"pagenavigation\"}', '{\"position\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 5, 0),
(409, 'plg_content_vote', 'plugin', 'vote', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_vote\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_VOTE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"vote\"}', '', '', '', 0, '0000-00-00 00:00:00', 6, 0),
(410, 'plg_editors_codemirror', 'plugin', 'codemirror', 'editors', 0, 1, 1, 1, '{\"name\":\"plg_editors_codemirror\",\"type\":\"plugin\",\"creationDate\":\"28 March 2011\",\"author\":\"Marijn Haverbeke\",\"copyright\":\"Copyright (C) 2014 by Marijn Haverbeke <marijnh@gmail.com> and others\",\"authorEmail\":\"marijnh@gmail.com\",\"authorUrl\":\"http:\\/\\/codemirror.net\\/\",\"version\":\"5.12\",\"description\":\"PLG_CODEMIRROR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"codemirror\"}', '{\"lineNumbers\":\"1\",\"lineWrapping\":\"1\",\"matchTags\":\"1\",\"matchBrackets\":\"1\",\"marker-gutter\":\"1\",\"autoCloseTags\":\"1\",\"autoCloseBrackets\":\"1\",\"autoFocus\":\"1\",\"theme\":\"default\",\"tabmode\":\"indent\"}', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(411, 'plg_editors_none', 'plugin', 'none', 'editors', 0, 1, 1, 1, '{\"name\":\"plg_editors_none\",\"type\":\"plugin\",\"creationDate\":\"September 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_NONE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"none\"}', '', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(412, 'plg_editors_tinymce', 'plugin', 'tinymce', 'editors', 0, 1, 1, 0, '{\"name\":\"plg_editors_tinymce\",\"type\":\"plugin\",\"creationDate\":\"2005-2016\",\"author\":\"Ephox Corporation\",\"copyright\":\"Ephox Corporation\",\"authorEmail\":\"N\\/A\",\"authorUrl\":\"http:\\/\\/www.tinymce.com\",\"version\":\"4.3.3\",\"description\":\"PLG_TINY_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"tinymce\"}', '{\"mode\":\"1\",\"skin\":\"0\",\"mobile\":\"0\",\"entity_encoding\":\"raw\",\"lang_mode\":\"1\",\"text_direction\":\"ltr\",\"content_css\":\"1\",\"content_css_custom\":\"\",\"relative_urls\":\"1\",\"newlines\":\"0\",\"invalid_elements\":\"script,applet,iframe\",\"extended_elements\":\"\",\"html_height\":\"550\",\"html_width\":\"750\",\"resizing\":\"1\",\"element_path\":\"1\",\"fonts\":\"1\",\"paste\":\"1\",\"searchreplace\":\"1\",\"insertdate\":\"1\",\"colors\":\"1\",\"table\":\"1\",\"smilies\":\"1\",\"hr\":\"1\",\"link\":\"1\",\"media\":\"1\",\"print\":\"1\",\"directionality\":\"1\",\"fullscreen\":\"1\",\"alignment\":\"1\",\"visualchars\":\"1\",\"visualblocks\":\"1\",\"nonbreaking\":\"1\",\"template\":\"1\",\"blockquote\":\"1\",\"wordcount\":\"1\",\"advlist\":\"1\",\"autosave\":\"1\",\"contextmenu\":\"1\",\"inlinepopups\":\"1\",\"custom_plugin\":\"\",\"custom_button\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(413, 'plg_editors-xtd_article', 'plugin', 'article', 'editors-xtd', 0, 1, 1, 0, '{\"name\":\"plg_editors-xtd_article\",\"type\":\"plugin\",\"creationDate\":\"October 2009\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_ARTICLE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"article\"}', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(414, 'plg_editors-xtd_image', 'plugin', 'image', 'editors-xtd', 0, 1, 1, 0, '{\"name\":\"plg_editors-xtd_image\",\"type\":\"plugin\",\"creationDate\":\"August 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_IMAGE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"image\"}', '', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(415, 'plg_editors-xtd_pagebreak', 'plugin', 'pagebreak', 'editors-xtd', 0, 1, 1, 0, '{\"name\":\"plg_editors-xtd_pagebreak\",\"type\":\"plugin\",\"creationDate\":\"August 2004\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_EDITORSXTD_PAGEBREAK_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"pagebreak\"}', '', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(416, 'plg_editors-xtd_readmore', 'plugin', 'readmore', 'editors-xtd', 0, 1, 1, 0, '{\"name\":\"plg_editors-xtd_readmore\",\"type\":\"plugin\",\"creationDate\":\"March 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_READMORE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"readmore\"}', '', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(417, 'plg_search_categories', 'plugin', 'categories', 'search', 0, 1, 1, 0, '{\"name\":\"plg_search_categories\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SEARCH_CATEGORIES_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"categories\"}', '{\"search_limit\":\"50\",\"search_content\":\"1\",\"search_archived\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(418, 'plg_search_contacts', 'plugin', 'contacts', 'search', 0, 1, 1, 0, '{\"name\":\"plg_search_contacts\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SEARCH_CONTACTS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"contacts\"}', '{\"search_limit\":\"50\",\"search_content\":\"1\",\"search_archived\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(419, 'plg_search_content', 'plugin', 'content', 'search', 0, 1, 1, 0, '{\"name\":\"plg_search_content\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SEARCH_CONTENT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"content\"}', '{\"search_limit\":\"50\",\"search_content\":\"1\",\"search_archived\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(420, 'plg_search_newsfeeds', 'plugin', 'newsfeeds', 'search', 0, 1, 1, 0, '{\"name\":\"plg_search_newsfeeds\",\"type\":\"plugin\",\"creationDate\":\"November 2005\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SEARCH_NEWSFEEDS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"newsfeeds\"}', '{\"search_limit\":\"50\",\"search_content\":\"1\",\"search_archived\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(422, 'plg_system_languagefilter', 'plugin', 'languagefilter', 'system', 0, 0, 1, 1, '{\"name\":\"plg_system_languagefilter\",\"type\":\"plugin\",\"creationDate\":\"July 2010\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SYSTEM_LANGUAGEFILTER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"languagefilter\"}', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(423, 'plg_system_p3p', 'plugin', 'p3p', 'system', 0, 0, 1, 0, '{\"name\":\"plg_system_p3p\",\"type\":\"plugin\",\"creationDate\":\"September 2010\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_P3P_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"p3p\"}', '{\"headers\":\"NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM\"}', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(424, 'plg_system_cache', 'plugin', 'cache', 'system', 0, 0, 1, 1, '{\"name\":\"plg_system_cache\",\"type\":\"plugin\",\"creationDate\":\"February 2007\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_CACHE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"cache\"}', '{\"browsercache\":\"0\",\"cachetime\":\"15\"}', '', '', 0, '0000-00-00 00:00:00', 9, 0),
(425, 'plg_system_debug', 'plugin', 'debug', 'system', 0, 1, 1, 0, '{\"name\":\"plg_system_debug\",\"type\":\"plugin\",\"creationDate\":\"December 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_DEBUG_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"debug\"}', '{\"profile\":\"1\",\"queries\":\"1\",\"memory\":\"1\",\"language_files\":\"1\",\"language_strings\":\"1\",\"strip-first\":\"1\",\"strip-prefix\":\"\",\"strip-suffix\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(426, 'plg_system_log', 'plugin', 'log', 'system', 0, 1, 1, 1, '{\"name\":\"plg_system_log\",\"type\":\"plugin\",\"creationDate\":\"April 2007\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_LOG_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"log\"}', '', '', '', 0, '0000-00-00 00:00:00', 5, 0),
(427, 'plg_system_redirect', 'plugin', 'redirect', 'system', 0, 0, 1, 1, '{\"name\":\"plg_system_redirect\",\"type\":\"plugin\",\"creationDate\":\"April 2009\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SYSTEM_REDIRECT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"redirect\"}', '', '', '', 0, '0000-00-00 00:00:00', 6, 0),
(428, 'plg_system_remember', 'plugin', 'remember', 'system', 0, 1, 1, 1, '{\"name\":\"plg_system_remember\",\"type\":\"plugin\",\"creationDate\":\"April 2007\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_REMEMBER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"remember\"}', '', '', '', 0, '0000-00-00 00:00:00', 7, 0),
(429, 'plg_system_sef', 'plugin', 'sef', 'system', 0, 1, 1, 0, '{\"name\":\"plg_system_sef\",\"type\":\"plugin\",\"creationDate\":\"December 2007\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SEF_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"sef\"}', '', '', '', 0, '0000-00-00 00:00:00', 8, 0),
(430, 'plg_system_logout', 'plugin', 'logout', 'system', 0, 1, 1, 1, '{\"name\":\"plg_system_logout\",\"type\":\"plugin\",\"creationDate\":\"April 2009\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SYSTEM_LOGOUT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"logout\"}', '', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(431, 'plg_user_contactcreator', 'plugin', 'contactcreator', 'user', 0, 0, 1, 0, '{\"name\":\"plg_user_contactcreator\",\"type\":\"plugin\",\"creationDate\":\"August 2009\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_CONTACTCREATOR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"contactcreator\"}', '{\"autowebpage\":\"\",\"category\":\"34\",\"autopublish\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(432, 'plg_user_joomla', 'plugin', 'joomla', 'user', 0, 1, 1, 0, '{\"name\":\"plg_user_joomla\",\"type\":\"plugin\",\"creationDate\":\"December 2006\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_USER_JOOMLA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"joomla\"}', '{\"autoregister\":\"1\",\"mail_to_user\":\"1\",\"forceLogout\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(433, 'plg_user_profile', 'plugin', 'profile', 'user', 0, 0, 1, 0, '{\"name\":\"plg_user_profile\",\"type\":\"plugin\",\"creationDate\":\"January 2008\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_USER_PROFILE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"profile\"}', '{\"register-require_address1\":\"1\",\"register-require_address2\":\"1\",\"register-require_city\":\"1\",\"register-require_region\":\"1\",\"register-require_country\":\"1\",\"register-require_postal_code\":\"1\",\"register-require_phone\":\"1\",\"register-require_website\":\"1\",\"register-require_favoritebook\":\"1\",\"register-require_aboutme\":\"1\",\"register-require_tos\":\"1\",\"register-require_dob\":\"1\",\"profile-require_address1\":\"1\",\"profile-require_address2\":\"1\",\"profile-require_city\":\"1\",\"profile-require_region\":\"1\",\"profile-require_country\":\"1\",\"profile-require_postal_code\":\"1\",\"profile-require_phone\":\"1\",\"profile-require_website\":\"1\",\"profile-require_favoritebook\":\"1\",\"profile-require_aboutme\":\"1\",\"profile-require_tos\":\"1\",\"profile-require_dob\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(434, 'plg_extension_joomla', 'plugin', 'joomla', 'extension', 0, 1, 1, 1, '{\"name\":\"plg_extension_joomla\",\"type\":\"plugin\",\"creationDate\":\"May 2010\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_EXTENSION_JOOMLA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"joomla\"}', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(435, 'plg_content_joomla', 'plugin', 'joomla', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_joomla\",\"type\":\"plugin\",\"creationDate\":\"November 2010\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_CONTENT_JOOMLA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"joomla\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(436, 'plg_system_languagecode', 'plugin', 'languagecode', 'system', 0, 0, 1, 0, '{\"name\":\"plg_system_languagecode\",\"type\":\"plugin\",\"creationDate\":\"November 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SYSTEM_LANGUAGECODE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"languagecode\"}', '', '', '', 0, '0000-00-00 00:00:00', 10, 0),
(437, 'plg_quickicon_joomlaupdate', 'plugin', 'joomlaupdate', 'quickicon', 0, 1, 1, 1, '{\"name\":\"plg_quickicon_joomlaupdate\",\"type\":\"plugin\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_QUICKICON_JOOMLAUPDATE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"joomlaupdate\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(438, 'plg_quickicon_extensionupdate', 'plugin', 'extensionupdate', 'quickicon', 0, 1, 1, 1, '{\"name\":\"plg_quickicon_extensionupdate\",\"type\":\"plugin\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_QUICKICON_EXTENSIONUPDATE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"extensionupdate\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(439, 'plg_captcha_recaptcha', 'plugin', 'recaptcha', 'captcha', 0, 0, 1, 0, '{\"name\":\"plg_captcha_recaptcha\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.4.0\",\"description\":\"PLG_CAPTCHA_RECAPTCHA_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"recaptcha\"}', '{\"public_key\":\"\",\"private_key\":\"\",\"theme\":\"clean\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(440, 'plg_system_highlight', 'plugin', 'highlight', 'system', 0, 1, 1, 0, '{\"name\":\"plg_system_highlight\",\"type\":\"plugin\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SYSTEM_HIGHLIGHT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"highlight\"}', '', '', '', 0, '0000-00-00 00:00:00', 7, 0),
(441, 'plg_content_finder', 'plugin', 'finder', 'content', 0, 0, 1, 0, '{\"name\":\"plg_content_finder\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_CONTENT_FINDER_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"finder\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(442, 'plg_finder_categories', 'plugin', 'categories', 'finder', 0, 1, 1, 0, '{\"name\":\"plg_finder_categories\",\"type\":\"plugin\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_FINDER_CATEGORIES_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"categories\"}', '', '', '', 0, '0000-00-00 00:00:00', 1, 0),
(443, 'plg_finder_contacts', 'plugin', 'contacts', 'finder', 0, 1, 1, 0, '{\"name\":\"plg_finder_contacts\",\"type\":\"plugin\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_FINDER_CONTACTS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"contacts\"}', '', '', '', 0, '0000-00-00 00:00:00', 2, 0),
(444, 'plg_finder_content', 'plugin', 'content', 'finder', 0, 1, 1, 0, '{\"name\":\"plg_finder_content\",\"type\":\"plugin\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_FINDER_CONTENT_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"content\"}', '', '', '', 0, '0000-00-00 00:00:00', 3, 0),
(445, 'plg_finder_newsfeeds', 'plugin', 'newsfeeds', 'finder', 0, 1, 1, 0, '{\"name\":\"plg_finder_newsfeeds\",\"type\":\"plugin\",\"creationDate\":\"August 2011\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_FINDER_NEWSFEEDS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"newsfeeds\"}', '', '', '', 0, '0000-00-00 00:00:00', 4, 0),
(447, 'plg_finder_tags', 'plugin', 'tags', 'finder', 0, 1, 1, 0, '{\"name\":\"plg_finder_tags\",\"type\":\"plugin\",\"creationDate\":\"February 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_FINDER_TAGS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"tags\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(448, 'plg_twofactorauth_totp', 'plugin', 'totp', 'twofactorauth', 0, 0, 1, 0, '{\"name\":\"plg_twofactorauth_totp\",\"type\":\"plugin\",\"creationDate\":\"August 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.2.0\",\"description\":\"PLG_TWOFACTORAUTH_TOTP_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"totp\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(449, 'plg_authentication_cookie', 'plugin', 'cookie', 'authentication', 0, 1, 1, 0, '{\"name\":\"plg_authentication_cookie\",\"type\":\"plugin\",\"creationDate\":\"July 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_AUTH_COOKIE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"cookie\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(450, 'plg_twofactorauth_yubikey', 'plugin', 'yubikey', 'twofactorauth', 0, 0, 1, 0, '{\"name\":\"plg_twofactorauth_yubikey\",\"type\":\"plugin\",\"creationDate\":\"September 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.2.0\",\"description\":\"PLG_TWOFACTORAUTH_YUBIKEY_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"yubikey\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(451, 'plg_search_tags', 'plugin', 'tags', 'search', 0, 1, 1, 0, '{\"name\":\"plg_search_tags\",\"type\":\"plugin\",\"creationDate\":\"March 2014\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.0.0\",\"description\":\"PLG_SEARCH_TAGS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"tags\"}', '{\"search_limit\":\"50\",\"show_tagged_items\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(452, 'plg_system_updatenotification', 'plugin', 'updatenotification', 'system', 0, 1, 1, 0, '{\"name\":\"plg_system_updatenotification\",\"type\":\"plugin\",\"creationDate\":\"May 2015\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.5.0\",\"description\":\"PLG_SYSTEM_UPDATENOTIFICATION_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"updatenotification\"}', '{\"lastrun\":1538055445}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(453, 'plg_editors-xtd_module', 'plugin', 'module', 'editors-xtd', 0, 1, 1, 0, '{\"name\":\"plg_editors-xtd_module\",\"type\":\"plugin\",\"creationDate\":\"October 2015\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.5.0\",\"description\":\"PLG_MODULE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"module\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(454, 'plg_system_stats', 'plugin', 'stats', 'system', 0, 1, 1, 0, '{\"name\":\"plg_system_stats\",\"type\":\"plugin\",\"creationDate\":\"November 2013\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.5.0\",\"description\":\"PLG_SYSTEM_STATS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"stats\"}', '{\"mode\":3,\"lastrun\":1538056474,\"unique_id\":\"0efe8d23067a1e0c9245357259ed46eb30cfc3c8\",\"interval\":12}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(503, 'beez3', 'template', 'beez3', '', 0, 1, 1, 0, '{\"name\":\"beez3\",\"type\":\"template\",\"creationDate\":\"25 November 2009\",\"author\":\"Angie Radtke\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.\",\"authorEmail\":\"a.radtke@derauftritt.de\",\"authorUrl\":\"http:\\/\\/www.der-auftritt.de\",\"version\":\"3.1.0\",\"description\":\"TPL_BEEZ3_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"templateDetails\"}', '{\"wrapperSmall\":\"53\",\"wrapperLarge\":\"72\",\"sitetitle\":\"\",\"sitedescription\":\"\",\"navposition\":\"center\",\"templatecolor\":\"nature\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(504, 'hathor', 'template', 'hathor', '', 1, 1, 1, 0, '{\"name\":\"hathor\",\"type\":\"template\",\"creationDate\":\"May 2010\",\"author\":\"Andrea Tarr\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.\",\"authorEmail\":\"hathor@tarrconsulting.com\",\"authorUrl\":\"http:\\/\\/www.tarrconsulting.com\",\"version\":\"3.0.0\",\"description\":\"TPL_HATHOR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"templateDetails\"}', '{\"showSiteName\":\"0\",\"colourChoice\":\"0\",\"boldText\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(506, 'protostar', 'template', 'protostar', '', 0, 1, 1, 0, '{\"name\":\"protostar\",\"type\":\"template\",\"creationDate\":\"4\\/30\\/2012\",\"author\":\"Kyle Ledbetter\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"\",\"version\":\"1.0\",\"description\":\"TPL_PROTOSTAR_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"templateDetails\"}', '{\"templateColor\":\"\",\"logoFile\":\"\",\"googleFont\":\"1\",\"googleFontName\":\"Open+Sans\",\"fluidContainer\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(507, 'isis', 'template', 'isis', '', 1, 1, 1, 0, '{\"name\":\"isis\",\"type\":\"template\",\"creationDate\":\"3\\/30\\/2012\",\"author\":\"Kyle Ledbetter\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"\",\"version\":\"1.0\",\"description\":\"TPL_ISIS_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"templateDetails\"}', '{\"templateColor\":\"\",\"logoFile\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(600, 'English (en-GB)', 'language', 'en-GB', '', 0, 1, 1, 1, '{\"name\":\"English (en-GB)\",\"type\":\"language\",\"creationDate\":\"November 2015\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.5.0\",\"description\":\"en-GB site language\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(601, 'English (en-GB)', 'language', 'en-GB', '', 1, 1, 1, 1, '{\"name\":\"English (en-GB)\",\"type\":\"language\",\"creationDate\":\"November 2015\",\"author\":\"Joomla! Project\",\"copyright\":\"Copyright (C) 2005 - 2016 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.5.0\",\"description\":\"en-GB administrator language\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(700, 'files_joomla', 'file', 'joomla', '', 0, 1, 1, 1, '{\"name\":\"files_joomla\",\"type\":\"file\",\"creationDate\":\"March 2016\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2016 Open Source Matters. All rights reserved\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"3.5.0\",\"description\":\"FILES_JOOMLA_XML_DESCRIPTION\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10000, 'Russian', 'language', 'ru-RU', '', 0, 1, 0, 0, '{\"name\":\"Russian\",\"type\":\"language\",\"creationDate\":\"2018-09-13\",\"author\":\"Russian Translation Team\",\"copyright\":\"Copyright (C) 2005 - 2017 Open Source Matters. All rights reserved.\",\"authorEmail\":\"smart@joomlaportal.ru\",\"authorUrl\":\"www.joomlaportal.ru\",\"version\":\"3.8.12.1\",\"description\":\"Russian language pack (site) for Joomla! 3.8.12\",\"group\":\"\",\"filename\":\"install\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10001, 'ru-RU', 'language', 'ru-RU', '', 1, 1, 0, 0, '{\"name\":\"\\u0420\\u0443\\u0441\\u0441\\u043a\\u0438\\u0439 (ru-RU)\",\"type\":\"language\",\"creationDate\":\"2018-09-13\",\"author\":\"Russian Translation Team\",\"copyright\":\"Copyright (C) 2005 - 2017 Open Source Matters. All rights reserved.\",\"authorEmail\":\"smart@joomlaportal.ru\",\"authorUrl\":\"www.joomlaportal.ru\",\"version\":\"3.8.12.1\",\"description\":\"Russian language pack (administrator) for Joomla! 3.8.12\",\"group\":\"\",\"filename\":\"install\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10002, 'Russian (ru-RU) Language Pack', 'package', 'pkg_ru-RU', '', 0, 1, 1, 0, '{\"name\":\"Russian (ru-RU) Language Pack\",\"type\":\"package\",\"creationDate\":\"2018-09-13\",\"author\":\"Russian Translation Team\",\"copyright\":\"Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.\",\"authorEmail\":\"smart@joomlaportal.ru\",\"authorUrl\":\"www.joomlaportal.ru\",\"version\":\"3.8.12.1\",\"description\":\"Joomla 3.8 Russian Language Package\",\"group\":\"\",\"filename\":\"pkg_ru-RU\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10005, 'Community Builder Library', 'library', 'CBLib', '', 0, 1, 1, 0, '{\"name\":\"Community Builder Library\",\"type\":\"library\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"(C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"description\":\"Community Builder Library\",\"group\":\"\",\"filename\":\"CBLib\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10006, 'Community Builder System plugin', 'plugin', 'communitybuilder', 'system', 0, 1, 1, 0, '{\"name\":\"Community Builder System plugin\",\"type\":\"plugin\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"Copyright (C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"description\":\"Community Builder System Plugin for Joomla!\",\"group\":\"\",\"filename\":\"communitybuilder\"}', '{\"redirect_urls\":\"1\",\"rewrite_urls\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10007, 'comprofiler', 'component', 'com_comprofiler', '', 1, 1, 0, 0, '{\"name\":\"comprofiler\",\"type\":\"component\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"Copyright (C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"description\":\"Community Builder for Joomla!\",\"group\":\"\",\"filename\":\"comprofiler\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10008, 'Community Builder Login module', 'module', 'mod_cblogin', '', 0, 1, 0, 0, '{\"name\":\"Community Builder Login module\",\"type\":\"module\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"(C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"description\":\"Login module to be used with Community Builder instead of the Login module.\",\"group\":\"\",\"filename\":\"mod_cblogin\"}', '{\"show_buttons_icons\":\"0\",\"https_post\":\"0\",\"cb_plugins\":\"0\",\"pretext\":\"\",\"posttext\":\"\",\"login\":\"\",\"name_label\":\"5\",\"name_length\":\"14\",\"pass_label\":\"5\",\"pass_length\":\"14\",\"key_label\":\"5\",\"key_length\":\"14\",\"remember_enabled\":\"1\",\"show_lostpass\":\"1\",\"show_newaccount\":\"1\",\"login_message\":\"0\",\"logoutpretext\":\"\",\"logoutposttext\":\"\",\"logout\":\"index.php\",\"greeting\":\"1\",\"show_avatar\":\"1\",\"text_show_profile\":\"\",\"icon_show_profile\":\"0\",\"text_edit_profile\":\"\",\"icon_edit_profile\":\"0\",\"show_pms\":\"0\",\"show_pms_icon\":\"0\",\"show_connection_notifications\":\"0\",\"show_connection_notifications_icon\":\"0\",\"logout_message\":\"0\",\"style_username_cssclass\":\"\",\"style_password_cssclass\":\"\",\"style_secretkey_cssclass\":\"\",\"style_login_cssclass\":\"\",\"style_logout_cssclass\":\"\",\"style_forgotlogin_cssclass\":\"\",\"style_register_cssclass\":\"\",\"style_profile_cssclass\":\"\",\"style_profileedit_cssclass\":\"\",\"style_connrequests_cssclass\":\"\",\"style_privatemsgs_cssclass\":\"\",\"layout\":\"_:bootstrap\",\"cache\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10009, 'Community Builder Online module', 'module', 'mod_comprofileronline', '', 0, 1, 0, 0, '{\"name\":\"Community Builder Online module\",\"type\":\"module\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"(C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"description\":\"Displays a list of users logged in with a link to their profile.\",\"group\":\"\",\"filename\":\"mod_comprofileronline\"}', '{\"mode\":\"1\",\"pretext\":\"\",\"posttext\":\"\",\"usertext\":\"\",\"limit\":\"30\",\"exclude\":\"\",\"cb_plugins\":\"0\",\"custom_field\":\"username\",\"custom_direction\":\"ASC\",\"label\":\"1\",\"separator\":\",\",\"layout\":\"_:default\",\"cache\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10010, 'Community Builder Workflows module', 'module', 'mod_comprofilermoderator', '', 0, 1, 0, 0, '{\"name\":\"Community Builder Workflows module\",\"type\":\"module\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"(C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"description\":\"Displays Notifications of pending actions for the moderator and connections functionality of Community Builder.\",\"group\":\"\",\"filename\":\"mod_comprofilermoderator\"}', '{\"pretext\":\"\",\"posttext\":\"\",\"show_banned\":\"1\",\"show_image_approval\":\"1\",\"show_user_reports\":\"1\",\"show_uban_requests\":\"1\",\"show_user_approval\":\"1\",\"show_pms\":\"1\",\"show_connections\":\"1\",\"cb_plugins\":\"0\",\"layout\":\"_:default\",\"cache\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10011, 'Community Builder Admin module', 'module', 'mod_cbadmin', '', 1, 1, 2, 0, '{\"name\":\"Community Builder Admin module\",\"type\":\"module\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"Copyright (C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\",\"version\":\"2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"description\":\"Community Builder Admin Module for Joomla!\",\"group\":\"\",\"filename\":\"mod_cbadmin\"}', '{\"mode\":\"1\",\"menu_cb\":\"1\",\"menu_cbsubs\":\"1\",\"menu_cbgj\":\"1\",\"menu_plugins\":\"0\",\"feed_entries\":\"5\",\"feed_duration\":\"12\",\"modal_display\":\"1\",\"modal_width\":\"90%\",\"modal_height\":\"90vh\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10012, 'Community Builder Package', 'package', 'pkg_communitybuilder', '', 0, 1, 1, 0, '{\"name\":\"Community Builder Package\",\"type\":\"package\",\"creationDate\":\"2018-01-01\",\"author\":\"CB Team\",\"copyright\":\"Copyright (C) 2004-2017 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"cbteam@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.1.3\",\"description\":\"Community Builder 2.1.3+build.2018.01.01.21.09.14.b5ac000a8\",\"group\":\"\",\"filename\":\"pkg_communitybuilder\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10013, 'System - Community Builder Redirect', 'plugin', 'cbredirectbot', 'system', 0, 0, 1, 0, '{\"name\":\"System - Community Builder Redirect\",\"type\":\"plugin\",\"creationDate\":\"2016-07-13\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"1.1.0\",\"description\":\"This plugin implements 404 redirect replacements with support for REGEXP replacements.\",\"group\":\"\",\"filename\":\"cbredirectbot\"}', '{\"redirects\":\"\",\"header\":\"301\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10014, 'Content - Community Builder Replacer', 'plugin', 'cbreplacerbot', 'content', 0, 0, 1, 0, '{\"name\":\"Content - Community Builder Replacer\",\"type\":\"plugin\",\"creationDate\":\"2016-04-30\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"1.2.0\",\"description\":\"This plugin allows customized replacers to replace a string to a different string. Optionally supports REGEXP replacers, translations, substitutions, and case sensitive replacements.\",\"group\":\"\",\"filename\":\"cbreplacerbot\"}', '{\"replacers\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10015, 'Content - Community Builder Author', 'plugin', 'cbauthorbot', 'content', 0, 0, 1, 0, '{\"name\":\"Content - Community Builder Author\",\"type\":\"plugin\",\"creationDate\":\"2015-11-17\",\"author\":\"Krileon\",\"copyright\":\"Copyright (C) 2004-2015 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.0.1\",\"description\":\"This plugin replaces the author with their Community Builder formatted name.\",\"group\":\"\",\"filename\":\"cbauthorbot\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10016, 'Content - Community Builder', 'plugin', 'cbcontentbot', 'content', 0, 0, 1, 0, '{\"name\":\"Content - Community Builder\",\"type\":\"plugin\",\"creationDate\":\"2016-06-08\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"4.0.1\",\"description\":\"This plugin allows tabs, fields, and more to be displayed within content items using Community Builder substitutions. Format: [substitution] (e.g. [username]). Optionally use [cb:ignore]STRING[\\/cb:ignore] to ignore parsing the specified string for substitutions.\",\"group\":\"\",\"filename\":\"cbcontentbot\"}', '{\"user\":\"0\",\"ignore_context\":\"\",\"css\":\"\",\"js\":\"\",\"jquery\":\"\",\"jquery_plgs\":\"\",\"load_tpl\":\"0\",\"load_js\":\"0\",\"load_tooltip\":\"0\",\"load_lang\":\"1\",\"load_plgs\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10017, 'Search - Community Builder', 'plugin', 'cbsearchbot', 'search', 0, 0, 1, 0, '{\"name\":\"Search - Community Builder\",\"type\":\"plugin\",\"creationDate\":\"2014-11-10\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2014 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.0.2\",\"description\":\"This plugin allows searching for Community Builder users with Joomla search.\",\"group\":\"\",\"filename\":\"cbsearchbot\"}', '{\"search_area\":\"Users\",\"search_fields\":\"41|*|42|*|46|*|47|*|48|*|50\",\"search_blocked\":\"0\",\"search_banned\":\"1\",\"search_unapproved\":\"0\",\"search_unconfirmed\":\"0\",\"search_exclude\":\"\",\"result_title\":\"[formatname]\",\"result_link\":\"0\",\"result_text\":\"[formatname]\'s profile page\",\"result_limit\":\"50\",\"ordering_alpha\":\"name\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10018, 'Search - CB GroupJive', 'plugin', 'gjsearchbot', 'search', 0, 0, 1, 0, '{\"name\":\"Search - CB GroupJive\",\"type\":\"plugin\",\"creationDate\":\"2016-12-29\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"3.1.0\",\"description\":\"This plugin allows searching for CB GroupJive with Joomla search.\",\"group\":\"\",\"filename\":\"gjsearchbot\"}', '{\"search_category_enable\":\"0\",\"search_category_area\":\"Categories\",\"search_category_exclude\":\"\",\"results_category_title\":\"[name]\",\"results_category_link\":\"0\",\"results_category_text\":\"[description]\",\"results_category_limit\":\"50\",\"search_group_enable\":\"1\",\"search_group_area\":\"Groups\",\"search_group_exclude\":\"\",\"results_group_title\":\"[name]\",\"results_group_link\":\"0\",\"results_group_text\":\"[description]\",\"results_group_limit\":\"50\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10019, 'Content - CB Gallery', 'plugin', 'cbgallerybot', 'content', 0, 0, 1, 0, '{\"name\":\"Content - CB Gallery\",\"type\":\"plugin\",\"creationDate\":\"2016-08-31\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.0.0\",\"description\":\"This plugin allows CB Gallery galleries to be displayed within content items using Community Builder substitutions. Format: [cbgallery:gallery \\/]. Additional formation options include asset=\\\"ASSET\\\", folders=\\\"TRUE_OR_FALSE\\\", folders_width=\\\"#\\\", folders_create=\\\"TRUE_OR_FALSE\\\", folders_create_access=\\\"VIEWACCESSLEVEL\\\", folders_create_limit=\\\"FIELD_NAME_OR_CUSTOM\\\", folders_create_limit_custom=\\\"#\\\", folders_create_approval=\\\"TRUE_OR_FALSE\\\", folders_create_approval_notify=\\\"TRUE_OR_FALSE\\\", folders_create_captcha=\\\"TRUE_OR_FALSE\\\", folders_paging=\\\"TRUE_OR_FALSE\\\", folders_paging_limit=\\\"#\\\", folders_search=\\\"TRUE_OR_FALSE\\\", folders_orderby=\\\"COLUMN_DESC_OR_ASC\\\", items_width=\\\"#\\\", items_create=\\\"TRUE_OR_FALSE\\\", items_create_captcha=\\\"TRUE_OR_FALSE\\\", items_create_approval_notify=\\\"TRUE_OR_FALSE\\\", items_paging=\\\"TRUE_OR_FALSE\\\", items_paging_limit=\\\"#\\\", items_search=\\\"TRUE_OR_FALSE\\\", items_orderby=\\\"COLUMN_DESC_OR_ASC\\\", photos=\\\"TRUE_OR_FALSE\\\", photos_download=\\\"TRUE_OR_FALSE\\\", photos_create=\\\"TRUE_OR_FALSE\\\", photos_create_access=\\\"VIEWACCESSLEVEL\\\", photos_create_limit=\\\"FIELD_NAME_OR_CUSTOM\\\", photos_create_limit_custom=\\\"#\\\", photos_upload=\\\"TRUE_OR_FALSE\\\", photos_link=\\\"TRUE_OR_FALSE\\\", photos_create_approval=\\\"TRUE_OR_FALSE\\\", photos_resample=\\\"TRUE_OR_FALSE\\\", photos_image_height=\\\"#\\\", photos_image_width=\\\"#\\\", photos_thumbnail_height=\\\"#\\\", photos_thumbnail_width=\\\"#\\\", photos_maintain_aspect_ratio=\\\"#\\\", photos_min_size=\\\"#\\\", photos_max_size=\\\"#\\\", videos=\\\"TRUE_OR_FALSE\\\", videos_download=\\\"TRUE_OR_FALSE\\\", videos_create=\\\"TRUE_OR_FALSE\\\", videos_create_access=\\\"VIEWACCESSLEVEL\\\", videos_create_limit=\\\"FIELD_NAME_OR_CUSTOM\\\", videos_create_limit_custom=\\\"#\\\", videos_upload=\\\"TRUE_OR_FALSE\\\", videos_link=\\\"TRUE_OR_FALSE\\\", videos_create_approval=\\\"TRUE_OR_FALSE\\\", videos_min_size=\\\"#\\\", videos_max_size=\\\"#\\\", files=\\\"TRUE_OR_FALSE\\\", files_create=\\\"TRUE_OR_FALSE\\\", files_create_access=\\\"VIEWACCESSLEVEL\\\", files_create_limit=\\\"FIELD_NAME_OR_CUSTOM\\\", files_create_limit_custom=\\\"#\\\", files_upload=\\\"TRUE_OR_FALSE\\\", files_link=\\\"TRUE_OR_FALSE\\\", files_create_approval=\\\"TRUE_OR_FALSE\\\", files_extensions=\\\"EXTENSIONS\\\", files_min_size=\\\"#\\\", files_max_size=\\\"#\\\", music=\\\"TRUE_OR_FALSE\\\", music_download=\\\"TRUE_OR_FALSE\\\", music_create=\\\"TRUE_OR_FALSE\\\", music_create_access=\\\"VIEWACCESSLEVEL\\\", music_create_limit=\\\"FIELD_NAME_OR_CUSTOM\\\", music_create_limit_custom=\\\"#\\\", music_upload=\\\"TRUE_OR_FALSE\\\", music_link=\\\"TRUE_OR_FALSE\\\", music_create_approval=\\\"TRUE_OR_FALSE\\\", music_min_size=\\\"#\\\", music_max_size=\\\"#\\\", thumbnails=\\\"TRUE_OR_FALSE\\\", thumbnails_upload=\\\"TRUE_OR_FALSE\\\", thumbnails_link=\\\"TRUE_OR_FALSE\\\", thumbnails_resample=\\\"TRUE_OR_FALSE\\\", thumbnails_image_height=\\\"#\\\", thumbnails_image_width=\\\"#\\\", thumbnails_maintain_aspect_ratio=\\\"#\\\", thumbnails_min_size=\\\"#\\\", and thumbnails_max_size=\\\"#\\\".\",\"group\":\"\",\"filename\":\"cbgallerybot\"}', '{\"ignore_context\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0);
INSERT INTO `orexv_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(10020, 'CB Gallery', 'module', 'mod_cbgallery', '', 0, 1, 0, 0, '{\"name\":\"CB Gallery\",\"type\":\"module\",\"creationDate\":\"2016-08-31\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.0.0\",\"description\":\"Displays CB Gallery as a module.\",\"group\":\"\",\"filename\":\"mod_cbgallery\"}', '{\"gallery_asset\":\"\",\"gallery_folder\":\"\",\"gallery_folders\":\"-1\",\"gallery_folders_width\":\"\",\"gallery_folders_create\":\"0\",\"gallery_folders_create_access\":\"-1\",\"gallery_folders_create_limit\":\"-1\",\"gallery_folders_create_limit_custom\":\"\",\"gallery_folders_create_approval\":\"-1\",\"gallery_folders_create_approval_notify\":\"-1\",\"gallery_folders_create_captcha\":\"-1\",\"gallery_folders_paging\":\"0\",\"gallery_folders_paging_limit\":\"5\",\"gallery_folders_search\":\"0\",\"gallery_folders_orderby\":\"-1\",\"gallery_items_width\":\"\",\"gallery_items_create\":\"0\",\"gallery_items_create_captcha\":\"-1\",\"gallery_items_create_approval_notify\":\"-1\",\"gallery_items_paging\":\"0\",\"gallery_items_paging_limit\":\"5\",\"gallery_items_search\":\"0\",\"gallery_items_orderby\":\"-1\",\"gallery_photos\":\"-1\",\"gallery_photos_download\":\"-1\",\"gallery_photos_create\":\"0\",\"gallery_photos_create_access\":\"-1\",\"gallery_photos_create_limit\":\"-1\",\"gallery_photos_create_limit_custom\":\"\",\"gallery_photos_upload\":\"-1\",\"gallery_photos_link\":\"-1\",\"gallery_photos_create_approval\":\"-1\",\"gallery_photos_resample\":\"-1\",\"gallery_photos_image_height\":\"\",\"gallery_photos_image_width\":\"\",\"gallery_photos_thumbnail_height\":\"\",\"gallery_photos_thumbnail_width\":\"\",\"gallery_photos_maintain_aspect_ratio\":\"-1\",\"gallery_photos_min_size\":\"\",\"gallery_photos_max_size\":\"\",\"gallery_videos\":\"-1\",\"gallery_videos_download\":\"-1\",\"gallery_videos_create\":\"0\",\"gallery_videos_create_access\":\"-1\",\"gallery_videos_create_limit\":\"-1\",\"gallery_videos_create_limit_custom\":\"\",\"gallery_videos_upload\":\"-1\",\"gallery_videos_link\":\"-1\",\"gallery_videos_create_approval\":\"-1\",\"gallery_videos_min_size\":\"\",\"gallery_videos_max_size\":\"\",\"gallery_files\":\"-1\",\"gallery_files_create\":\"0\",\"gallery_files_create_access\":\"-1\",\"gallery_files_create_limit\":\"-1\",\"gallery_files_create_limit_custom\":\"\",\"gallery_files_upload\":\"-1\",\"gallery_files_link\":\"-1\",\"gallery_files_create_approval\":\"-1\",\"gallery_files_extensions\":\"\",\"gallery_files_min_size\":\"\",\"gallery_files_max_size\":\"\",\"gallery_music\":\"-1\",\"gallery_music_download\":\"-1\",\"gallery_music_create\":\"0\",\"gallery_music_create_access\":\"-1\",\"gallery_music_create_limit\":\"-1\",\"gallery_music_create_limit_custom\":\"\",\"gallery_music_upload\":\"-1\",\"gallery_music_link\":\"-1\",\"gallery_music_create_approval\":\"-1\",\"gallery_music_min_size\":\"\",\"gallery_music_max_size\":\"\",\"gallery_thumbnails\":\"-1\",\"gallery_thumbnails_upload\":\"-1\",\"gallery_thumbnails_link\":\"-1\",\"gallery_thumbnails_resample\":\"-1\",\"gallery_thumbnails_image_height\":\"\",\"gallery_thumbnails_image_width\":\"\",\"gallery_thumbnails_maintain_aspect_ratio\":\"-1\",\"gallery_thumbnails_min_size\":\"\",\"gallery_thumbnails_max_size\":\"\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10021, 'CB Activity', 'module', 'mod_cbactivity', '', 0, 1, 0, 0, '{\"name\":\"CB Activity\",\"type\":\"module\",\"creationDate\":\"2015-11-13\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2015 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"3.0.1\",\"description\":\"Displays CB Activity as a module.\",\"group\":\"\",\"filename\":\"mod_cbactivity\"}', '{\"activity_create_access\":\"\",\"activity_message_limit\":\"\",\"activity_paging\":\"\",\"activity_limit\":\"10\",\"activity_actions\":\"\",\"activity_actions_message_limit\":\"\",\"activity_locations\":\"\",\"activity_links\":\"\",\"activity_links_link_limit\":\"\",\"activity_tags\":\"\",\"activity_comments\":\"\",\"activity_comments_create_access\":\"\",\"activity_comments_message_limit\":\"\",\"activity_comments_paging\":\"\",\"activity_comments_limit\":\"\",\"activity_comments_replies\":\"\",\"activity_comments_replies_create_access\":\"\",\"activity_comments_replies_message_limit\":\"\",\"activity_comments_replies_paging\":\"\",\"activity_comments_replies_limit\":\"\",\"cache\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10022, 'CB Content', 'module', 'mod_cbcontent', '', 0, 1, 0, 0, '{\"name\":\"CB Content\",\"type\":\"module\",\"creationDate\":\"2016-03-07\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"2.0.1\",\"description\":\"Allows to display user profile content.\",\"group\":\"\",\"filename\":\"mod_cbcontent\"}', '{\"maintext\":\"\",\"maincss\":\"\",\"mainjs\":\"\",\"mainjquery\":\"\",\"mainjquery_plgs\":\"\",\"prepare_content\":\"0\",\"maincbtpl\":\"0\",\"maincbjs\":\"0\",\"load_tooltip\":\"0\",\"load_lang\":\"1\",\"load_plgs\":\"0\",\"cache\":\"1\",\"cache_time\":\"900\",\"cachemode\":\"static\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10023, 'CB PB Latest', 'module', 'mod_cbpblatest', '', 0, 1, 0, 0, '{\"name\":\"CB PB Latest\",\"type\":\"module\",\"creationDate\":\"2015-01-20\",\"author\":\"CB Team\",\"copyright\":\"(C) 2004-2015 and Trademark of Lightning MultiCom SA, Switzerland - www.joomlapolis.com - and its licensors, all rights reserved\",\"authorEmail\":\"beat@joomlapolis.com\",\"authorUrl\":\"www.joomlapolis.com\",\"version\":\"2.0.2\",\"description\":\"CB ProfileBook (Latest Entries) Module\",\"group\":\"\",\"filename\":\"mod_cbpblatest\"}', '{\"moduleclass_sfx\":\"\",\"pblatest_mode\":\"b\",\"pblatest_connections\":\"0\",\"pblatest_limit\":\"5\",\"pblatest_include\":\"\",\"pblatest_exclude\":\"\",\"pblatest_substitutions\":\"\",\"pblatest_guestbook_user\":\"{p[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} added a new <a href=\\\"{e[url]}\\\">guestbook entry<\\/a> to {r[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} - {e[date]}\",\"pblatest_guestbook_self\":\"{p[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} added a new <a href=\\\"{e[url]}\\\">guestbook entry<\\/a> - {e[date]}\",\"pblatest_blog_user\":\"{p[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} wrote a new blog \\\"<a href=\\\"{e[url]}\\\">{e[title]}<\\/a>\\\" to {r[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} - {e[date]}\",\"pblatest_blog_self\":\"{p[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} wrote a new blog \\\"<a href=\\\"{e[url]}\\\">{e[title]}<\\/a>\\\" - {e[date]}\",\"pblatest_wall_user\":\"{p[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} added a new <a href=\\\"{e[url]}\\\">wall entry<\\/a> to {r[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} - {e[date]}\",\"pblatest_wall_self\":\"{p[cb:userfield field=\\\"formatname\\\" reason=\\\"list\\\" \\/]} added a new <a href=\\\"{e[url]}\\\">wall entry<\\/a> - {e[date]}\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10024, 'CB GroupJive', 'module', 'mod_cbgroupjive', '', 0, 1, 0, 0, '{\"name\":\"CB GroupJive\",\"type\":\"module\",\"creationDate\":\"2016-12-29\",\"author\":\"Krileon\",\"copyright\":\"(C) 2004-2016 www.joomlapolis.com \\/ Lightning MultiCom SA - and its licensors, all rights reserved2 License\",\"authorEmail\":\"krileon@joomlapolis.com\",\"authorUrl\":\"http:\\/\\/www.joomlapolis.com\\/\",\"version\":\"3.1.0\",\"description\":\"Multi-purpose module displaying various GroupJive content.\",\"group\":\"\",\"filename\":\"mod_cbgroupjive\"}', '{\"groupjive_mode\":\"latest_groups\",\"groupjive_limit\":\"10\",\"groupjive_exclude_categories\":\"\",\"groupjive_exclude_groups\":\"\",\"cache\":\"0\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10025, 'COM_JCE', 'component', 'com_jce', '', 1, 1, 0, 0, '{\"name\":\"COM_JCE\",\"type\":\"component\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"COM_JCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10026, 'plg_editors_jce', 'plugin', 'jce', 'editors', 0, 1, 1, 0, '{\"name\":\"plg_editors_jce\",\"type\":\"plugin\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"http:\\/\\/www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"WF_EDITOR_PLUGIN_DESC\",\"group\":\"\",\"filename\":\"jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10027, 'plg_content_jce', 'plugin', 'jce', 'content', 0, 1, 1, 0, '{\"name\":\"plg_content_jce\",\"type\":\"plugin\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"http:\\/\\/www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"PLG_CONTENT_JCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10028, 'plg_extension_jce', 'plugin', 'jce', 'extension', 0, 1, 1, 0, '{\"name\":\"plg_extension_jce\",\"type\":\"plugin\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"http:\\/\\/www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"PLG_EXTENSION_JCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10029, 'plg_fields_mediajce', 'plugin', 'mediajce', 'fields', 0, 1, 1, 0, '{\"name\":\"plg_fields_mediajce\",\"type\":\"plugin\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"https:\\/\\/www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"PLG_FIELDS_MEDIAJCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"mediajce\"}', '[]', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10030, 'plg_installer_jce', 'plugin', 'jce', 'installer', 0, 1, 1, 0, '{\"name\":\"plg_installer_jce\",\"type\":\"plugin\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"http:\\/\\/www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"PLG_INSTALLER_JCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10031, 'plg_quickicon_jce', 'plugin', 'jce', 'quickicon', 0, 1, 1, 0, '{\"name\":\"plg_quickicon_jce\",\"type\":\"plugin\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"http:\\/\\/www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"PLG_QUICKICON_JCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10032, 'plg_system_jce', 'plugin', 'jce', 'system', 0, 1, 1, 0, '{\"name\":\"plg_system_jce\",\"type\":\"plugin\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"Copyright (C) 2006 - 2018 Ryan Demmer. All rights reserved\",\"authorEmail\":\"info@joomlacontenteditor.net\",\"authorUrl\":\"http:\\/\\/www.joomlacontenteditor.net\",\"version\":\"2.6.29\",\"description\":\"PLG_SYSTEM_JCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
(10033, 'PKG_JCE', 'package', 'pkg_jce', '', 0, 1, 1, 0, '{\"name\":\"PKG_JCE\",\"type\":\"package\",\"creationDate\":\"03-05-2018\",\"author\":\"Ryan Demmer\",\"copyright\":\"\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"2.6.29\",\"description\":\"PKG_JCE_XML_DESCRIPTION\",\"group\":\"\",\"filename\":\"pkg_jce\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_filters`
--

CREATE TABLE `orexv_finder_filters` (
  `filter_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_by_alias` varchar(255) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `map_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `params` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links`
--

CREATE TABLE `orexv_finder_links` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL,
  `title` varchar(400) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `indexdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `md5sum` varchar(32) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `state` int(5) DEFAULT '1',
  `access` int(5) DEFAULT '0',
  `language` varchar(8) NOT NULL,
  `publish_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `list_price` double UNSIGNED NOT NULL DEFAULT '0',
  `sale_price` double UNSIGNED NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL,
  `object` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms0`
--

CREATE TABLE `orexv_finder_links_terms0` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms1`
--

CREATE TABLE `orexv_finder_links_terms1` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms2`
--

CREATE TABLE `orexv_finder_links_terms2` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms3`
--

CREATE TABLE `orexv_finder_links_terms3` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms4`
--

CREATE TABLE `orexv_finder_links_terms4` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms5`
--

CREATE TABLE `orexv_finder_links_terms5` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms6`
--

CREATE TABLE `orexv_finder_links_terms6` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms7`
--

CREATE TABLE `orexv_finder_links_terms7` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms8`
--

CREATE TABLE `orexv_finder_links_terms8` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_terms9`
--

CREATE TABLE `orexv_finder_links_terms9` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_termsa`
--

CREATE TABLE `orexv_finder_links_termsa` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_termsb`
--

CREATE TABLE `orexv_finder_links_termsb` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_termsc`
--

CREATE TABLE `orexv_finder_links_termsc` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_termsd`
--

CREATE TABLE `orexv_finder_links_termsd` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_termse`
--

CREATE TABLE `orexv_finder_links_termse` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_links_termsf`
--

CREATE TABLE `orexv_finder_links_termsf` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `weight` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_taxonomy`
--

CREATE TABLE `orexv_finder_taxonomy` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `access` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `orexv_finder_taxonomy`
--

INSERT INTO `orexv_finder_taxonomy` (`id`, `parent_id`, `title`, `state`, `access`, `ordering`) VALUES
(1, 0, 'ROOT', 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_taxonomy_map`
--

CREATE TABLE `orexv_finder_taxonomy_map` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `node_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_terms`
--

CREATE TABLE `orexv_finder_terms` (
  `term_id` int(10) UNSIGNED NOT NULL,
  `term` varchar(75) NOT NULL,
  `stem` varchar(75) NOT NULL,
  `common` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `phrase` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `weight` float UNSIGNED NOT NULL DEFAULT '0',
  `soundex` varchar(75) NOT NULL,
  `links` int(10) NOT NULL DEFAULT '0',
  `language` char(3) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_terms_common`
--

CREATE TABLE `orexv_finder_terms_common` (
  `term` varchar(75) NOT NULL,
  `language` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `orexv_finder_terms_common`
--

INSERT INTO `orexv_finder_terms_common` (`term`, `language`) VALUES
('a', 'en'),
('about', 'en'),
('after', 'en'),
('ago', 'en'),
('all', 'en'),
('am', 'en'),
('an', 'en'),
('and', 'en'),
('ani', 'en'),
('any', 'en'),
('are', 'en'),
('aren\'t', 'en'),
('as', 'en'),
('at', 'en'),
('be', 'en'),
('but', 'en'),
('by', 'en'),
('for', 'en'),
('from', 'en'),
('get', 'en'),
('go', 'en'),
('how', 'en'),
('if', 'en'),
('in', 'en'),
('into', 'en'),
('is', 'en'),
('isn\'t', 'en'),
('it', 'en'),
('its', 'en'),
('me', 'en'),
('more', 'en'),
('most', 'en'),
('must', 'en'),
('my', 'en'),
('new', 'en'),
('no', 'en'),
('none', 'en'),
('not', 'en'),
('noth', 'en'),
('nothing', 'en'),
('of', 'en'),
('off', 'en'),
('often', 'en'),
('old', 'en'),
('on', 'en'),
('onc', 'en'),
('once', 'en'),
('onli', 'en'),
('only', 'en'),
('or', 'en'),
('other', 'en'),
('our', 'en'),
('ours', 'en'),
('out', 'en'),
('over', 'en'),
('page', 'en'),
('she', 'en'),
('should', 'en'),
('small', 'en'),
('so', 'en'),
('some', 'en'),
('than', 'en'),
('thank', 'en'),
('that', 'en'),
('the', 'en'),
('their', 'en'),
('theirs', 'en'),
('them', 'en'),
('then', 'en'),
('there', 'en'),
('these', 'en'),
('they', 'en'),
('this', 'en'),
('those', 'en'),
('thus', 'en'),
('time', 'en'),
('times', 'en'),
('to', 'en'),
('too', 'en'),
('true', 'en'),
('under', 'en'),
('until', 'en'),
('up', 'en'),
('upon', 'en'),
('use', 'en'),
('user', 'en'),
('users', 'en'),
('veri', 'en'),
('version', 'en'),
('very', 'en'),
('via', 'en'),
('want', 'en'),
('was', 'en'),
('way', 'en'),
('were', 'en'),
('what', 'en'),
('when', 'en'),
('where', 'en'),
('whi', 'en'),
('which', 'en'),
('who', 'en'),
('whom', 'en'),
('whose', 'en'),
('why', 'en'),
('wide', 'en'),
('will', 'en'),
('with', 'en'),
('within', 'en'),
('without', 'en'),
('would', 'en'),
('yes', 'en'),
('yet', 'en'),
('you', 'en'),
('your', 'en'),
('yours', 'en');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_tokens`
--

CREATE TABLE `orexv_finder_tokens` (
  `term` varchar(75) NOT NULL,
  `stem` varchar(75) NOT NULL,
  `common` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `phrase` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `weight` float UNSIGNED NOT NULL DEFAULT '1',
  `context` tinyint(1) UNSIGNED NOT NULL DEFAULT '2',
  `language` char(3) NOT NULL DEFAULT ''
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_tokens_aggregate`
--

CREATE TABLE `orexv_finder_tokens_aggregate` (
  `term_id` int(10) UNSIGNED NOT NULL,
  `map_suffix` char(1) NOT NULL,
  `term` varchar(75) NOT NULL,
  `stem` varchar(75) NOT NULL,
  `common` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `phrase` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `term_weight` float UNSIGNED NOT NULL,
  `context` tinyint(1) UNSIGNED NOT NULL DEFAULT '2',
  `context_weight` float UNSIGNED NOT NULL,
  `total_weight` float UNSIGNED NOT NULL,
  `language` char(3) NOT NULL DEFAULT ''
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_finder_types`
--

CREATE TABLE `orexv_finder_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `mime` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_categories`
--

CREATE TABLE `orexv_groupjive_categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `description` text,
  `canvas` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `types` varchar(255) NOT NULL DEFAULT '',
  `access` int(11) NOT NULL DEFAULT '1',
  `create_access` int(11) NOT NULL DEFAULT '0',
  `css` text,
  `published` int(11) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '99999',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_groups`
--

CREATE TABLE `orexv_groupjive_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '0',
  `name` text NOT NULL,
  `description` text,
  `canvas` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `css` text,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '1',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_invites`
--

CREATE TABLE `orexv_groupjive_invites` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `message` text,
  `invited` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accepted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `code` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_notifications`
--

CREATE TABLE `orexv_groupjive_notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_plugin_events`
--

CREATE TABLE `orexv_groupjive_plugin_events` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `event` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `limit` int(11) NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_plugin_events_attendance`
--

CREATE TABLE `orexv_groupjive_plugin_events_attendance` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `event` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_plugin_file`
--

CREATE TABLE `orexv_groupjive_plugin_file` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `file` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) DEFAULT NULL,
  `description` text,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_plugin_photo`
--

CREATE TABLE `orexv_groupjive_plugin_photo` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) DEFAULT NULL,
  `description` text,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_plugin_video`
--

CREATE TABLE `orexv_groupjive_plugin_video` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `caption` text,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_plugin_wall`
--

CREATE TABLE `orexv_groupjive_plugin_wall` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `reply` int(11) NOT NULL DEFAULT '0',
  `post` text NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_groupjive_users`
--

CREATE TABLE `orexv_groupjive_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL DEFAULT '0',
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_languages`
--

CREATE TABLE `orexv_languages` (
  `lang_id` int(11) UNSIGNED NOT NULL,
  `lang_code` char(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_native` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sef` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadesc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sitename` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `published` int(11) NOT NULL DEFAULT '0',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_languages`
--

INSERT INTO `orexv_languages` (`lang_id`, `lang_code`, `title`, `title_native`, `sef`, `image`, `description`, `metakey`, `metadesc`, `sitename`, `published`, `access`, `ordering`) VALUES
(1, 'en-GB', 'English (UK)', 'English (UK)', 'en', 'en', '', '', '', '', 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_menu`
--

CREATE TABLE `orexv_menu` (
  `id` int(11) NOT NULL,
  `menutype` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The display title of the menu item.',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'The SEF alias of the menu item.',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `path` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The computed path of the menu item based on the alias field.',
  `link` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The actually link the menu item refers to.',
  `type` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The published state of the menu link.',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `level` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The relative level in the tree.',
  `component_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to #__extensions.id',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to #__users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The click behaviour of the link.',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.',
  `img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The image of the menu item.',
  `template_style_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON encoded data for the menu item.',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `home` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Indicates if this menu item is the home or default page.',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_id` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_menu`
--

INSERT INTO `orexv_menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES
(1, '', 'Menu_Item_Root', 'root', '', '', '', '', 1, 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, '', 0, '', 0, 71, 0, '*', 0),
(2, 'menu', 'com_banners', 'Banners', '', 'Banners', 'index.php?option=com_banners', 'component', 0, 1, 1, 4, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners', 0, '', 1, 10, 0, '*', 1),
(3, 'menu', 'com_banners', 'Banners', '', 'Banners/Banners', 'index.php?option=com_banners', 'component', 0, 2, 2, 4, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners', 0, '', 2, 3, 0, '*', 1),
(4, 'menu', 'com_banners_categories', 'Categories', '', 'Banners/Categories', 'index.php?option=com_categories&extension=com_banners', 'component', 0, 2, 2, 6, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners-cat', 0, '', 4, 5, 0, '*', 1),
(5, 'menu', 'com_banners_clients', 'Clients', '', 'Banners/Clients', 'index.php?option=com_banners&view=clients', 'component', 0, 2, 2, 4, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners-clients', 0, '', 6, 7, 0, '*', 1),
(6, 'menu', 'com_banners_tracks', 'Tracks', '', 'Banners/Tracks', 'index.php?option=com_banners&view=tracks', 'component', 0, 2, 2, 4, 0, '0000-00-00 00:00:00', 0, 0, 'class:banners-tracks', 0, '', 8, 9, 0, '*', 1),
(7, 'menu', 'com_contact', 'Contacts', '', 'Contacts', 'index.php?option=com_contact', 'component', 0, 1, 1, 8, 0, '0000-00-00 00:00:00', 0, 0, 'class:contact', 0, '', 11, 16, 0, '*', 1),
(8, 'menu', 'com_contact_contacts', 'Contacts', '', 'Contacts/Contacts', 'index.php?option=com_contact', 'component', 0, 7, 2, 8, 0, '0000-00-00 00:00:00', 0, 0, 'class:contact', 0, '', 12, 13, 0, '*', 1),
(9, 'menu', 'com_contact_categories', 'Categories', '', 'Contacts/Categories', 'index.php?option=com_categories&extension=com_contact', 'component', 0, 7, 2, 6, 0, '0000-00-00 00:00:00', 0, 0, 'class:contact-cat', 0, '', 14, 15, 0, '*', 1),
(10, 'menu', 'com_messages', 'Messaging', '', 'Messaging', 'index.php?option=com_messages', 'component', 0, 1, 1, 15, 0, '0000-00-00 00:00:00', 0, 0, 'class:messages', 0, '', 17, 22, 0, '*', 1),
(11, 'menu', 'com_messages_add', 'New Private Message', '', 'Messaging/New Private Message', 'index.php?option=com_messages&task=message.add', 'component', 0, 10, 2, 15, 0, '0000-00-00 00:00:00', 0, 0, 'class:messages-add', 0, '', 18, 19, 0, '*', 1),
(13, 'menu', 'com_newsfeeds', 'News Feeds', '', 'News Feeds', 'index.php?option=com_newsfeeds', 'component', 0, 1, 1, 17, 0, '0000-00-00 00:00:00', 0, 0, 'class:newsfeeds', 0, '', 23, 28, 0, '*', 1),
(14, 'menu', 'com_newsfeeds_feeds', 'Feeds', '', 'News Feeds/Feeds', 'index.php?option=com_newsfeeds', 'component', 0, 13, 2, 17, 0, '0000-00-00 00:00:00', 0, 0, 'class:newsfeeds', 0, '', 24, 25, 0, '*', 1),
(15, 'menu', 'com_newsfeeds_categories', 'Categories', '', 'News Feeds/Categories', 'index.php?option=com_categories&extension=com_newsfeeds', 'component', 0, 13, 2, 6, 0, '0000-00-00 00:00:00', 0, 0, 'class:newsfeeds-cat', 0, '', 26, 27, 0, '*', 1),
(16, 'menu', 'com_redirect', 'Redirect', '', 'Redirect', 'index.php?option=com_redirect', 'component', 0, 1, 1, 24, 0, '0000-00-00 00:00:00', 0, 0, 'class:redirect', 0, '', 29, 30, 0, '*', 1),
(17, 'menu', 'com_search', 'Basic Search', '', 'Basic Search', 'index.php?option=com_search', 'component', 0, 1, 1, 19, 0, '0000-00-00 00:00:00', 0, 0, 'class:search', 0, '', 31, 32, 0, '*', 1),
(18, 'menu', 'com_finder', 'Smart Search', '', 'Smart Search', 'index.php?option=com_finder', 'component', 0, 1, 1, 27, 0, '0000-00-00 00:00:00', 0, 0, 'class:finder', 0, '', 33, 34, 0, '*', 1),
(19, 'menu', 'com_joomlaupdate', 'Joomla! Update', '', 'Joomla! Update', 'index.php?option=com_joomlaupdate', 'component', 1, 1, 1, 28, 0, '0000-00-00 00:00:00', 0, 0, 'class:joomlaupdate', 0, '', 35, 36, 0, '*', 1),
(20, 'main', 'com_tags', 'Tags', '', 'Tags', 'index.php?option=com_tags', 'component', 0, 1, 1, 29, 0, '0000-00-00 00:00:00', 0, 1, 'class:tags', 0, '', 37, 38, 0, '', 1),
(21, 'main', 'com_postinstall', 'Post-installation messages', '', 'Post-installation messages', 'index.php?option=com_postinstall', 'component', 0, 1, 1, 32, 0, '0000-00-00 00:00:00', 0, 1, 'class:postinstall', 0, '', 39, 40, 0, '*', 1),
(101, 'mainmenu', 'Главная', 'home', '', 'home', 'index.php?option=com_content&view=category&layout=blog&id=2', 'component', 1, 1, 1, 22, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{\"layout_type\":\"blog\",\"show_category_heading_title_text\":\"\",\"show_category_title\":\"\",\"show_description\":\"\",\"show_description_image\":\"\",\"maxLevel\":\"\",\"show_empty_categories\":\"\",\"show_no_articles\":\"\",\"show_subcat_desc\":\"\",\"show_cat_num_articles\":\"\",\"show_cat_tags\":\"\",\"page_subheading\":\"\",\"num_leading_articles\":\"999\",\"num_intro_articles\":\"0\",\"num_columns\":\"0\",\"num_links\":\"0\",\"multi_column_order\":\"\",\"show_subcategory_content\":\"\",\"orderby_pri\":\"\",\"orderby_sec\":\"front\",\"order_date\":\"\",\"show_pagination\":\"2\",\"show_pagination_results\":\"1\",\"show_featured\":\"\",\"show_title\":\"\",\"link_titles\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_vote\":\"\",\"show_readmore\":\"\",\"show_readmore_title\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"show_tags\":\"\",\"show_noauth\":\"\",\"show_feed_link\":\"1\",\"feed_summary\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"menu_show\":1,\"page_title\":\"\",\"show_page_heading\":\"1\",\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 41, 42, 1, '*', 0),
(102, 'main', 'COM_COMPROFILER', 'com-comprofiler', '', 'com-comprofiler', 'index.php?option=com_comprofiler', 'component', 0, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, '../components/com_comprofiler/images/icon-16-cb.png', 0, '{}', 43, 60, 0, '', 1),
(103, 'main', 'COM_COMPROFILER_CONTROLPANEL', 'com-comprofiler-controlpanel', '', 'com-comprofiler/com-comprofiler-controlpanel', 'index.php?option=com_comprofiler', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:cpanel', 0, '{}', 44, 45, 0, '', 1),
(104, 'main', 'COM_COMPROFILER_SHOWUSERS', 'com-comprofiler-showusers', '', 'com-comprofiler/com-comprofiler-showusers', 'index.php?option=com_comprofiler&task=showusers&view=showusers', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:user', 0, '{}', 46, 47, 0, '', 1),
(105, 'main', 'COM_COMPROFILER_SHOWTAB', 'com-comprofiler-showtab', '', 'com-comprofiler/com-comprofiler-showtab', 'index.php?option=com_comprofiler&task=showTab&view=showTab', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:themes', 0, '{}', 48, 49, 0, '', 1),
(106, 'main', 'COM_COMPROFILER_SHOWFIELD', 'com-comprofiler-showfield', '', 'com-comprofiler/com-comprofiler-showfield', 'index.php?option=com_comprofiler&task=showField&view=showField', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:content', 0, '{}', 50, 51, 0, '', 1),
(107, 'main', 'COM_COMPROFILER_SHOWLISTS', 'com-comprofiler-showlists', '', 'com-comprofiler/com-comprofiler-showlists', 'index.php?option=com_comprofiler&task=showLists&view=showLists', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:search', 0, '{}', 52, 53, 0, '', 1),
(108, 'main', 'COM_COMPROFILER_SHOWPLUGINS', 'com-comprofiler-showplugins', '', 'com-comprofiler/com-comprofiler-showplugins', 'index.php?option=com_comprofiler&task=showPlugins&view=showPlugins', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:plugin', 0, '{}', 54, 55, 0, '', 1),
(109, 'main', 'COM_COMPROFILER_TOOLS', 'com-comprofiler-tools', '', 'com-comprofiler/com-comprofiler-tools', 'index.php?option=com_comprofiler&task=tools&view=tools', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:maintenance', 0, '{}', 56, 57, 0, '', 1),
(110, 'main', 'COM_COMPROFILER_SHOWCONFIG', 'com-comprofiler-showconfig', '', 'com-comprofiler/com-comprofiler-showconfig', 'index.php?option=com_comprofiler&task=showconfig&view=showconfig', 'component', 0, 102, 2, 10007, 0, '0000-00-00 00:00:00', 0, 1, 'class:config', 0, '{}', 58, 59, 0, '', 1),
(111, 'communitybuilder', 'CB Profile', 'cb-profile', '', 'cb-profile', 'index.php?option=com_comprofiler&view=userprofile', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 94, 95, 0, '*', 0),
(112, 'communitybuilder', 'CB Profile Edit', 'cb-profile-edit', '', 'cb-profile-edit', 'index.php?option=com_comprofiler&view=userdetails', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 92, 93, 0, '*', 0),
(113, 'mainmenu', 'Регистрация', 'cb-registration', '', 'cb-registration', 'index.php?option=com_comprofiler&view=registers', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"menu_show\":1,\"page_title\":\"\",\"show_page_heading\":\"\",\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 90, 91, 0, '*', 0),
(114, 'communitybuilder', 'CB Login', 'cb-login', '', 'cb-login', 'index.php?option=com_comprofiler&view=login', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 88, 89, 0, '*', 0),
(115, 'communitybuilder', 'CB Logout', 'cb-logout', '', 'cb-logout', 'index.php?option=com_comprofiler&view=logout', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 86, 87, 0, '*', 0),
(116, 'communitybuilder', 'CB Forgot Login', 'cb-forgot-login', '', 'cb-forgot-login', 'index.php?option=com_comprofiler&view=lostpassword', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 84, 85, 0, '*', 0),
(117, 'communitybuilder', 'CB Userlist', 'cb-userlist', '', 'cb-userlist', 'index.php?option=com_comprofiler&view=userslist', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 82, 83, 0, '*', 0),
(118, 'communitybuilder', 'CB Manage Connections', 'cb-manage-connections', '', 'cb-manage-connections', 'index.php?option=com_comprofiler&view=manageconnections', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 80, 81, 0, '*', 0),
(119, 'communitybuilder', 'CB Moderate Bans', 'cb-moderate-bans', '', 'cb-moderate-bans', 'index.php?option=com_comprofiler&view=moderatebans', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 78, 79, 0, '*', 0),
(120, 'communitybuilder', 'CB Moderate Images', 'cb-moderate-images', '', 'cb-moderate-images', 'index.php?option=com_comprofiler&view=moderateimages', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 76, 77, 0, '*', 0),
(121, 'communitybuilder', 'CB Moderate Reports', 'cb-moderate-reports', '', 'cb-moderate-reports', 'index.php?option=com_comprofiler&view=moderatereports', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 74, 75, 0, '*', 0),
(122, 'communitybuilder', 'CB Moderate User Approvals', 'cb-moderate-user-approvals', '', 'cb-moderate-user-approvals', 'index.php?option=com_comprofiler&view=pendingapprovaluser', 'component', 1, 1, 1, 10007, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{}', 72, 73, 0, '*', 0),
(123, 'main', 'COM_JCE', 'com-jce', '', 'com-jce', 'index.php?option=com_jce', 'component', 0, 1, 1, 10025, 0, '0000-00-00 00:00:00', 0, 1, 'components/com_jce/media/img/menu/logo.png', 0, '{}', 61, 68, 0, '', 1),
(124, 'main', 'COM_JCE_MENU_CPANEL', 'com-jce-menu-cpanel', '', 'com-jce/com-jce-menu-cpanel', 'index.php?option=com_jce', 'component', 0, 123, 2, 10025, 0, '0000-00-00 00:00:00', 0, 1, 'class:component', 0, '{}', 62, 63, 0, '', 1),
(125, 'main', 'COM_JCE_MENU_CONFIG', 'com-jce-menu-config', '', 'com-jce/com-jce-menu-config', 'index.php?option=com_jce&view=config', 'component', 0, 123, 2, 10025, 0, '0000-00-00 00:00:00', 0, 1, 'class:component', 0, '{}', 64, 65, 0, '', 1),
(126, 'main', 'COM_JCE_MENU_PROFILES', 'com-jce-menu-profiles', '', 'com-jce/com-jce-menu-profiles', 'index.php?option=com_jce&view=profiles', 'component', 0, 123, 2, 10025, 0, '0000-00-00 00:00:00', 0, 1, 'class:component', 0, '{}', 66, 67, 0, '', 1),
(127, 'mainmenu', 'Контакты', 'kontakty', '', 'kontakty', 'index.php?option=com_content&view=article&id=5', 'component', 1, 1, 1, 22, 0, '0000-00-00 00:00:00', 0, 1, ' ', 0, '{\"show_title\":\"\",\"link_titles\":\"\",\"show_intro\":\"\",\"info_block_position\":\"\",\"show_category\":\"\",\"link_category\":\"\",\"show_parent_category\":\"\",\"link_parent_category\":\"\",\"show_author\":\"\",\"link_author\":\"\",\"show_create_date\":\"\",\"show_modify_date\":\"\",\"show_publish_date\":\"\",\"show_item_navigation\":\"\",\"show_vote\":\"\",\"show_icons\":\"\",\"show_print_icon\":\"\",\"show_email_icon\":\"\",\"show_hits\":\"\",\"show_tags\":\"\",\"show_noauth\":\"\",\"urls_position\":\"\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"menu_show\":1,\"page_title\":\"\",\"show_page_heading\":\"\",\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 69, 70, 0, '*', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_menu_types`
--

CREATE TABLE `orexv_menu_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `menutype` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_menu_types`
--

INSERT INTO `orexv_menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(1, 'mainmenu', 'Main Menu', 'The main menu for the site'),
(2, 'communitybuilder', 'Community Builder', '');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_messages`
--

CREATE TABLE `orexv_messages` (
  `message_id` int(10) UNSIGNED NOT NULL,
  `user_id_from` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_id_to` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `folder_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_messages_cfg`
--

CREATE TABLE `orexv_messages_cfg` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cfg_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cfg_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_modules`
--

CREATE TABLE `orexv_modules` (
  `id` int(11) NOT NULL,
  `asset_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `position` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `module` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `showtitle` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_modules`
--

INSERT INTO `orexv_modules` (`id`, `asset_id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
(1, 39, 'Main Menu', '', '', 1, 'position-7', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_menu', 1, 1, '{\"menutype\":\"mainmenu\",\"startLevel\":\"0\",\"endLevel\":\"0\",\"showAllChildren\":\"0\",\"tag_id\":\"\",\"class_sfx\":\"\",\"window_open\":\"\",\"layout\":\"\",\"moduleclass_sfx\":\"_menu\",\"cache\":\"1\",\"cache_time\":\"900\",\"cachemode\":\"itemid\"}', 0, '*'),
(2, 40, 'Login', '', '', 1, 'login', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_login', 1, 1, '', 1, '*'),
(3, 41, 'Popular Articles', '', '', 3, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_popular', 3, 1, '{\"count\":\"5\",\"catid\":\"\",\"user_id\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 1, '*'),
(4, 42, 'Recently Added Articles', '', '', 4, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_latest', 3, 1, '{\"count\":\"5\",\"ordering\":\"c_dsc\",\"catid\":\"\",\"user_id\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 1, '*'),
(8, 43, 'Toolbar', '', '', 1, 'toolbar', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_toolbar', 3, 1, '', 1, '*'),
(9, 44, 'Quick Icons', '', '', 1, 'icon', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_quickicon', 3, 1, '', 1, '*'),
(10, 45, 'Logged-in Users', '', '', 2, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_logged', 3, 1, '{\"count\":\"5\",\"name\":\"1\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 1, '*'),
(12, 46, 'Admin Menu', '', '', 1, 'menu', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_menu', 3, 1, '{\"layout\":\"\",\"moduleclass_sfx\":\"\",\"shownew\":\"1\",\"showhelp\":\"1\",\"cache\":\"0\"}', 1, '*'),
(13, 47, 'Admin Submenu', '', '', 1, 'submenu', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_submenu', 3, 1, '', 1, '*'),
(14, 48, 'User Status', '', '', 2, 'status', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_status', 3, 1, '', 1, '*'),
(15, 49, 'Title', '', '', 1, 'title', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_title', 3, 1, '', 1, '*'),
(16, 50, 'Login Form', '', '', 7, 'position-7', 0, '0000-00-00 00:00:00', '2018-09-27 14:33:37', '0000-00-00 00:00:00', -2, 'mod_login', 1, 1, '{\"greeting\":\"1\",\"name\":\"0\"}', 0, '*'),
(17, 51, 'Breadcrumbs', '', '', 1, 'position-1', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_breadcrumbs', 1, 1, '{\"showHere\":\"1\",\"showHome\":\"1\",\"homeText\":\"\",\"showLast\":\"1\",\"separator\":\"\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"1\",\"cache_time\":\"900\",\"cachemode\":\"itemid\",\"module_tag\":\"div\",\"bootstrap_size\":\"0\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 0, '*'),
(79, 52, 'Multilanguage status', '', '', 1, 'status', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 'mod_multilangstatus', 3, 1, '{\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 1, '*'),
(86, 53, 'Joomla Version', '', '', 1, 'footer', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_version', 3, 1, '{\"format\":\"short\",\"product\":\"1\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 1, '*'),
(87, 57, 'Вход/Регистрация', '', '', 1, 'position-7', 15, '2018-09-27 14:34:44', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_cblogin', 1, 1, '{\"show_buttons_icons\":\"0\",\"https_post\":\"1\",\"cb_plugins\":\"1\",\"pretext\":\"\",\"posttext\":\"\",\"login\":\"\",\"name_label\":\"5\",\"name_length\":\"14\",\"pass_label\":\"5\",\"pass_length\":\"14\",\"key_label\":\"5\",\"key_length\":\"14\",\"remember_enabled\":\"1\",\"show_lostpass\":\"1\",\"show_newaccount\":\"1\",\"login_message\":\"0\",\"logoutpretext\":\"\",\"logoutposttext\":\"\",\"logout\":\"index.php\",\"greeting\":\"1\",\"show_avatar\":\"1\",\"text_show_profile\":\"\",\"icon_show_profile\":\"0\",\"text_edit_profile\":\"\",\"icon_edit_profile\":\"0\",\"show_pms\":\"0\",\"show_pms_icon\":\"0\",\"show_connection_notifications\":\"0\",\"show_connection_notifications_icon\":\"0\",\"logout_message\":\"0\",\"style_username_cssclass\":\"\",\"style_password_cssclass\":\"\",\"style_secretkey_cssclass\":\"\",\"style_login_cssclass\":\"\",\"style_logout_cssclass\":\"\",\"style_forgotlogin_cssclass\":\"\",\"style_register_cssclass\":\"\",\"style_profile_cssclass\":\"\",\"style_profileedit_cssclass\":\"\",\"style_connrequests_cssclass\":\"\",\"style_privatemsgs_cssclass\":\"\",\"layout\":\"_:bootstrap\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"module_tag\":\"div\",\"bootstrap_size\":\"0\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 0, '*'),
(88, 58, 'CB Online', '', '', 3, 'position-7', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 'mod_comprofileronline', 1, 1, '{\"pretext\":\"\",\"posttext\":\"\",\"cb_plugins\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"module_tag\":\"div\",\"bootstrap_size\":\"0\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 0, '*'),
(89, 59, 'CB Workflows', '', '', 2, 'position-7', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 'mod_comprofilermoderator', 2, 1, '{\"pretext\":\"\",\"posttext\":\"\",\"show_banned\":\"1\",\"show_image_approval\":\"1\",\"show_user_reports\":\"1\",\"show_uban_requests\":\"1\",\"show_user_approval\":\"1\",\"show_pms\":\"1\",\"show_connections\":\"1\",\"cb_plugins\":\"0\",\"layout\":\"_:default\",\"moduleclass_sfx\":\"\",\"cache\":\"0\",\"module_tag\":\"div\",\"bootstrap_size\":\"0\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 0, '*'),
(90, 60, 'CB Admin Dropdown Menu', '', '', 99, 'menu', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_cbadmin', 1, 0, '{\"mode\":\"1\",\"menu_cb\":\"1\",\"menu_cbsubs\":\"1\",\"menu_cbgj\":\"1\",\"menu_plugins\":\"0\",\"feed_entries\":\"5\",\"feed_duration\":\"12\",\"module_tag\":\"div\",\"bootstrap_size\":\"0\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 1, '*'),
(91, 61, 'Community Builder News', '', '', 99, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_cbadmin', 1, 1, '{\"mode\":\"3\",\"menu_cb\":\"1\",\"menu_cbsubs\":\"1\",\"menu_cbgj\":\"1\",\"menu_plugins\":\"0\",\"feed_entries\":\"5\",\"feed_duration\":\"12\",\"modal_display\":\"1\",\"modal_width\":\"800\",\"modal_height\":\"500\",\"module_tag\":\"div\",\"bootstrap_size\":\"6\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 1, '*'),
(92, 62, 'Community Builder Updates', '', '', 99, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_cbadmin', 1, 1, '{\"mode\":\"4\",\"menu_cb\":\"1\",\"menu_cbsubs\":\"1\",\"menu_cbgj\":\"1\",\"menu_plugins\":\"0\",\"feed_entries\":\"5\",\"feed_duration\":\"12\",\"modal_display\":\"1\",\"modal_width\":\"800\",\"modal_height\":\"500\",\"module_tag\":\"div\",\"bootstrap_size\":\"6\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 1, '*'),
(93, 63, 'CB Admin Version Checker', '', '', 99, 'cpanel', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_cbadmin', 1, 0, '{\"mode\":\"5\",\"menu_cb\":\"1\",\"menu_cbsubs\":\"1\",\"menu_cbgj\":\"1\",\"menu_plugins\":\"0\",\"feed_entries\":\"5\",\"feed_duration\":\"12\",\"modal_display\":\"1\",\"modal_width\":\"800\",\"modal_height\":\"500\",\"module_tag\":\"div\",\"bootstrap_size\":\"6\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}', 1, '*'),
(94, 64, 'CB Gallery', '', '', 0, '', 0, '0000-00-00 00:00:00', '2018-09-27 14:33:31', '0000-00-00 00:00:00', -2, 'mod_cbgallery', 1, 1, '', 0, '*'),
(95, 65, 'CB Activity', '', '', 0, '', 0, '0000-00-00 00:00:00', '2018-09-27 14:33:31', '0000-00-00 00:00:00', -2, 'mod_cbactivity', 1, 1, '', 0, '*'),
(96, 66, 'CB Content', '', '', 0, '', 0, '0000-00-00 00:00:00', '2018-09-27 14:33:31', '0000-00-00 00:00:00', -2, 'mod_cbcontent', 1, 1, '', 0, '*'),
(97, 67, 'CB PB Latest', '', '', 0, '', 0, '0000-00-00 00:00:00', '2018-09-27 14:33:31', '0000-00-00 00:00:00', -2, 'mod_cbpblatest', 1, 1, '', 0, '*'),
(98, 68, 'CB GroupJive', '', '', 0, '', 0, '0000-00-00 00:00:00', '2018-09-27 14:33:31', '0000-00-00 00:00:00', -2, 'mod_cbgroupjive', 1, 1, '', 0, '*');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_modules_menu`
--

CREATE TABLE `orexv_modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_modules_menu`
--

INSERT INTO `orexv_modules_menu` (`moduleid`, `menuid`) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0),
(6, 0),
(7, 0),
(8, 0),
(9, 0),
(10, 0),
(12, 0),
(13, 0),
(14, 0),
(15, 0),
(16, 0),
(17, 0),
(79, 0),
(86, 0),
(87, 0),
(88, 0),
(89, 0),
(90, 0),
(91, 0),
(92, 0),
(93, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_newsfeeds`
--

CREATE TABLE `orexv_newsfeeds` (
  `catid` int(11) NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `link` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `numarticles` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `cache_time` int(10) UNSIGNED NOT NULL DEFAULT '3600',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rtl` tinyint(4) NOT NULL DEFAULT '0',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadesc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `xreference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `images` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_overrider`
--

CREATE TABLE `orexv_overrider` (
  `id` int(10) NOT NULL COMMENT 'Primary Key',
  `constant` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `string` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_postinstall_messages`
--

CREATE TABLE `orexv_postinstall_messages` (
  `postinstall_message_id` bigint(20) UNSIGNED NOT NULL,
  `extension_id` bigint(20) NOT NULL DEFAULT '700' COMMENT 'FK to #__extensions',
  `title_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Lang key for the title',
  `description_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Lang key for description',
  `action_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `language_extension` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'com_postinstall' COMMENT 'Extension holding lang keys',
  `language_client_id` tinyint(3) NOT NULL DEFAULT '1',
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'link' COMMENT 'Message type - message, link, action',
  `action_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'RAD URI to the PHP file containing action method',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Action method name or URL',
  `condition_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'RAD URI to file holding display condition method',
  `condition_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Display condition method, must return boolean',
  `version_introduced` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '3.2.0' COMMENT 'Version when this message was introduced',
  `enabled` tinyint(3) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_postinstall_messages`
--

INSERT INTO `orexv_postinstall_messages` (`postinstall_message_id`, `extension_id`, `title_key`, `description_key`, `action_key`, `language_extension`, `language_client_id`, `type`, `action_file`, `action`, `condition_file`, `condition_method`, `version_introduced`, `enabled`) VALUES
(1, 700, 'PLG_TWOFACTORAUTH_TOTP_POSTINSTALL_TITLE', 'PLG_TWOFACTORAUTH_TOTP_POSTINSTALL_BODY', 'PLG_TWOFACTORAUTH_TOTP_POSTINSTALL_ACTION', 'plg_twofactorauth_totp', 1, 'action', 'site://plugins/twofactorauth/totp/postinstall/actions.php', 'twofactorauth_postinstall_action', 'site://plugins/twofactorauth/totp/postinstall/actions.php', 'twofactorauth_postinstall_condition', '3.2.0', 1),
(2, 700, 'COM_CPANEL_WELCOME_BEGINNERS_TITLE', 'COM_CPANEL_WELCOME_BEGINNERS_MESSAGE', '', 'com_cpanel', 1, 'message', '', '', '', '', '3.2.0', 1),
(3, 700, 'COM_CPANEL_MSG_STATS_COLLECTION_TITLE', 'COM_CPANEL_MSG_STATS_COLLECTION_BODY', '', 'com_cpanel', 1, 'message', '', '', 'admin://components/com_admin/postinstall/statscollection.php', 'admin_postinstall_statscollection_condition', '3.5.0', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_redirect_links`
--

CREATE TABLE `orexv_redirect_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `old_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referer` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `header` smallint(3) NOT NULL DEFAULT '301'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_schemas`
--

CREATE TABLE `orexv_schemas` (
  `extension_id` int(11) NOT NULL,
  `version_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_schemas`
--

INSERT INTO `orexv_schemas` (`extension_id`, `version_id`) VALUES
(700, '3.5.0-2016-03-01');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_session`
--

CREATE TABLE `orexv_session` (
  `session_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `guest` tinyint(4) UNSIGNED DEFAULT '1',
  `time` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `data` mediumtext COLLATE utf8mb4_unicode_ci,
  `userid` int(11) DEFAULT '0',
  `username` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_session`
--

INSERT INTO `orexv_session` (`session_id`, `client_id`, `guest`, `time`, `data`, `userid`, `username`) VALUES
('dh0mdrama6picm77ofq27bbtn2', 1, 0, '1538063066', 'joomla|s:8932:\"TzoyNDoiSm9vbWxhXFJlZ2lzdHJ5XFJlZ2lzdHJ5IjoyOntzOjc6IgAqAGRhdGEiO086ODoic3RkQ2xhc3MiOjE6e3M6OToiX19kZWZhdWx0IjtPOjg6InN0ZENsYXNzIjo1OntzOjc6InNlc3Npb24iO086ODoic3RkQ2xhc3MiOjM6e3M6NzoiY291bnRlciI7aTo0MTI7czo1OiJ0aW1lciI7Tzo4OiJzdGRDbGFzcyI6Mzp7czo1OiJzdGFydCI7aToxNTM4MDU2NDM2O3M6NDoibGFzdCI7aToxNTM4MDYzMDY0O3M6Mzoibm93IjtpOjE1MzgwNjMwNjU7fXM6NToidG9rZW4iO3M6MzI6Ikx4T0xhcmU2anZPNEFVZXJiVWRXTG82MG81MkJrT1c3Ijt9czo4OiJyZWdpc3RyeSI7TzoyNDoiSm9vbWxhXFJlZ2lzdHJ5XFJlZ2lzdHJ5IjoyOntzOjc6IgAqAGRhdGEiO086ODoic3RkQ2xhc3MiOjg6e3M6MTM6ImNvbV9pbnN0YWxsZXIiO086ODoic3RkQ2xhc3MiOjM6e3M6NzoibWVzc2FnZSI7czowOiIiO3M6MTc6ImV4dGVuc2lvbl9tZXNzYWdlIjtzOjA6IiI7czoxMjoicmVkaXJlY3RfdXJsIjtOO31zOjEwOiJjb21fY29uZmlnIjtPOjg6InN0ZENsYXNzIjoxOntzOjY6ImNvbmZpZyI7Tzo4OiJzdGRDbGFzcyI6MTp7czo2OiJnbG9iYWwiO086ODoic3RkQ2xhc3MiOjE6e3M6NDoiZGF0YSI7YTo5MTp7czo3OiJvZmZsaW5lIjtzOjE6IjAiO3M6MTU6Im9mZmxpbmVfbWVzc2FnZSI7czoxMjk6ItCh0LDQudGCINC30LDQutGA0YvRgiDQvdCwINGC0LXRhdC90LjRh9C10YHQutC+0LUg0L7QsdGB0LvRg9C20LjQstCw0L3QuNC1LjxiciAvPtCf0L7QttCw0LvRg9C50YHRgtCwLCDQt9Cw0LnQtNC40YLQtSDQv9C+0LfQttC1LiI7czoyMzoiZGlzcGxheV9vZmZsaW5lX21lc3NhZ2UiO3M6MToiMSI7czoxMzoib2ZmbGluZV9pbWFnZSI7czowOiIiO3M6ODoic2l0ZW5hbWUiO3M6NToiZWxjb20iO3M6NjoiZWRpdG9yIjtzOjM6ImpjZSI7czo3OiJjYXB0Y2hhIjtzOjE6IjAiO3M6MTA6Imxpc3RfbGltaXQiO3M6MjoiMjAiO3M6NjoiYWNjZXNzIjtzOjE6IjEiO3M6NToiZGVidWciO3M6MToiMCI7czoxMDoiZGVidWdfbGFuZyI7czoxOiIwIjtzOjY6ImRidHlwZSI7czo2OiJteXNxbGkiO3M6NDoiaG9zdCI7czo5OiJsb2NhbGhvc3QiO3M6NDoidXNlciI7czo0OiJyb290IjtzOjg6InBhc3N3b3JkIjtzOjA6IiI7czoyOiJkYiI7czo1OiJlbGNvbSI7czo4OiJkYnByZWZpeCI7czo2OiJvcmV4dl8iO3M6OToibGl2ZV9zaXRlIjtzOjA6IiI7czo2OiJzZWNyZXQiO3M6MTY6ImxXT1ozdUF3YU1CTEhBR2giO3M6NDoiZ3ppcCI7czoxOiIwIjtzOjE1OiJlcnJvcl9yZXBvcnRpbmciO3M6NzoiZGVmYXVsdCI7czo3OiJoZWxwdXJsIjtzOjkwOiJodHRwczovL2hlbHAuam9vbWxhLm9yZy9wcm94eS9pbmRleC5waHA/b3B0aW9uPWNvbV9oZWxwJmtleXJlZj1IZWxwe21ham9yfXttaW5vcn06e2tleXJlZn0iO3M6ODoiZnRwX2hvc3QiO3M6MDoiIjtzOjg6ImZ0cF9wb3J0IjtzOjA6IiI7czo4OiJmdHBfdXNlciI7czowOiIiO3M6ODoiZnRwX3Bhc3MiO3M6MDoiIjtzOjg6ImZ0cF9yb290IjtzOjA6IiI7czoxMDoiZnRwX2VuYWJsZSI7czoxOiIwIjtzOjY6Im9mZnNldCI7czozOiJVVEMiO3M6MTA6Im1haWxvbmxpbmUiO3M6MToiMSI7czo2OiJtYWlsZXIiO3M6NDoibWFpbCI7czo4OiJtYWlsZnJvbSI7czoxMzoiZWxjb21AbWFpbC5ydSI7czo4OiJmcm9tbmFtZSI7czo1OiJlbGNvbSI7czo4OiJzZW5kbWFpbCI7czoxODoiL3Vzci9zYmluL3NlbmRtYWlsIjtzOjg6InNtdHBhdXRoIjtzOjE6IjAiO3M6ODoic210cHVzZXIiO3M6MDoiIjtzOjg6InNtdHBwYXNzIjtzOjA6IiI7czo4OiJzbXRwaG9zdCI7czo5OiJsb2NhbGhvc3QiO3M6MTA6InNtdHBzZWN1cmUiO3M6NDoibm9uZSI7czo4OiJzbXRwcG9ydCI7czoyOiIyNSI7czo3OiJjYWNoaW5nIjtzOjE6IjAiO3M6MTM6ImNhY2hlX2hhbmRsZXIiO3M6NDoiZmlsZSI7czo5OiJjYWNoZXRpbWUiO3M6MjoiMTUiO3M6MjA6ImNhY2hlX3BsYXRmb3JtcHJlZml4IjtzOjE6IjAiO3M6ODoiTWV0YURlc2MiO3M6NToiZWxjb20iO3M6ODoiTWV0YUtleXMiO3M6MDoiIjtzOjk6Ik1ldGFUaXRsZSI7czoxOiIxIjtzOjEwOiJNZXRhQXV0aG9yIjtzOjE6IjEiO3M6MTE6Ik1ldGFWZXJzaW9uIjtzOjE6IjAiO3M6Njoicm9ib3RzIjtzOjA6IiI7czozOiJzZWYiO3M6MToiMSI7czoxMToic2VmX3Jld3JpdGUiO3M6MToiMCI7czoxMDoic2VmX3N1ZmZpeCI7czoxOiIwIjtzOjEyOiJ1bmljb2Rlc2x1Z3MiO3M6MToiMCI7czoxMDoiZmVlZF9saW1pdCI7czoyOiIxMCI7czoxMDoiZmVlZF9lbWFpbCI7czo0OiJub25lIjtzOjg6ImxvZ19wYXRoIjtzOjI2OiJDOlx4YW1wcFxodGRvY3NcZWxjb20vbG9ncyI7czo4OiJ0bXBfcGF0aCI7czoyNToiQzpceGFtcHBcaHRkb2NzXGVsY29tL3RtcCI7czo4OiJsaWZldGltZSI7czozOiI0NTAiO3M6MTU6InNlc3Npb25faGFuZGxlciI7czo4OiJkYXRhYmFzZSI7czoxNjoibWVtY2FjaGVfcGVyc2lzdCI7czoxOiIxIjtzOjE3OiJtZW1jYWNoZV9jb21wcmVzcyI7czoxOiIwIjtzOjIwOiJtZW1jYWNoZV9zZXJ2ZXJfaG9zdCI7czo5OiJsb2NhbGhvc3QiO3M6MjA6Im1lbWNhY2hlX3NlcnZlcl9wb3J0IjtzOjU6IjExMjExIjtzOjE3OiJtZW1jYWNoZWRfcGVyc2lzdCI7czoxOiIxIjtzOjE4OiJtZW1jYWNoZWRfY29tcHJlc3MiO3M6MToiMCI7czoyMToibWVtY2FjaGVkX3NlcnZlcl9ob3N0IjtzOjk6ImxvY2FsaG9zdCI7czoyMToibWVtY2FjaGVkX3NlcnZlcl9wb3J0IjtzOjU6IjExMjExIjtzOjEzOiJyZWRpc19wZXJzaXN0IjtzOjE6IjEiO3M6MTc6InJlZGlzX3NlcnZlcl9ob3N0IjtzOjk6ImxvY2FsaG9zdCI7czoxNzoicmVkaXNfc2VydmVyX3BvcnQiO3M6NDoiNjM3OSI7czoxNzoicmVkaXNfc2VydmVyX2F1dGgiO3M6MDoiIjtzOjE1OiJyZWRpc19zZXJ2ZXJfZGIiO3M6MToiMCI7czoxMjoicHJveHlfZW5hYmxlIjtzOjE6IjAiO3M6MTA6InByb3h5X2hvc3QiO3M6MDoiIjtzOjEwOiJwcm94eV9wb3J0IjtzOjA6IiI7czoxMDoicHJveHlfdXNlciI7czowOiIiO3M6MTA6InByb3h5X3Bhc3MiO3M6MDoiIjtzOjExOiJtYXNzbWFpbG9mZiI7czoxOiIwIjtzOjEwOiJNZXRhUmlnaHRzIjtzOjA6IiI7czoxOToic2l0ZW5hbWVfcGFnZXRpdGxlcyI7czoxOiIwIjtzOjk6ImZvcmNlX3NzbCI7czoxOiIwIjtzOjI4OiJzZXNzaW9uX21lbWNhY2hlX3NlcnZlcl9ob3N0IjtzOjk6ImxvY2FsaG9zdCI7czoyODoic2Vzc2lvbl9tZW1jYWNoZV9zZXJ2ZXJfcG9ydCI7czo1OiIxMTIxMSI7czoyOToic2Vzc2lvbl9tZW1jYWNoZWRfc2VydmVyX2hvc3QiO3M6OToibG9jYWxob3N0IjtzOjI5OiJzZXNzaW9uX21lbWNhY2hlZF9zZXJ2ZXJfcG9ydCI7czo1OiIxMTIxMSI7czoxMjoiZnJvbnRlZGl0aW5nIjtzOjE6IjEiO3M6MTM6ImNvb2tpZV9kb21haW4iO3M6MDoiIjtzOjExOiJjb29raWVfcGF0aCI7czowOiIiO3M6ODoiYXNzZXRfaWQiO2k6MTtzOjc6ImZpbHRlcnMiO2E6OTp7aToxO2E6Mzp7czoxMToiZmlsdGVyX3R5cGUiO3M6MjoiTkgiO3M6MTE6ImZpbHRlcl90YWdzIjtzOjA6IiI7czoxNzoiZmlsdGVyX2F0dHJpYnV0ZXMiO3M6MDoiIjt9aTo5O2E6Mzp7czoxMToiZmlsdGVyX3R5cGUiO3M6MjoiQkwiO3M6MTE6ImZpbHRlcl90YWdzIjtzOjA6IiI7czoxNzoiZmlsdGVyX2F0dHJpYnV0ZXMiO3M6MDoiIjt9aTo2O2E6Mzp7czoxMToiZmlsdGVyX3R5cGUiO3M6MjoiQkwiO3M6MTE6ImZpbHRlcl90YWdzIjtzOjA6IiI7czoxNzoiZmlsdGVyX2F0dHJpYnV0ZXMiO3M6MDoiIjt9aTo3O2E6Mzp7czoxMToiZmlsdGVyX3R5cGUiO3M6NDoiTk9ORSI7czoxMToiZmlsdGVyX3RhZ3MiO3M6MDoiIjtzOjE3OiJmaWx0ZXJfYXR0cmlidXRlcyI7czowOiIiO31pOjI7YTozOntzOjExOiJmaWx0ZXJfdHlwZSI7czoyOiJOSCI7czoxMToiZmlsdGVyX3RhZ3MiO3M6MDoiIjtzOjE3OiJmaWx0ZXJfYXR0cmlidXRlcyI7czowOiIiO31pOjM7YTozOntzOjExOiJmaWx0ZXJfdHlwZSI7czoyOiJCTCI7czoxMToiZmlsdGVyX3RhZ3MiO3M6MDoiIjtzOjE3OiJmaWx0ZXJfYXR0cmlidXRlcyI7czowOiIiO31pOjQ7YTozOntzOjExOiJmaWx0ZXJfdHlwZSI7czoyOiJCTCI7czoxMToiZmlsdGVyX3RhZ3MiO3M6MDoiIjtzOjE3OiJmaWx0ZXJfYXR0cmlidXRlcyI7czowOiIiO31pOjU7YTozOntzOjExOiJmaWx0ZXJfdHlwZSI7czoyOiJCTCI7czoxMToiZmlsdGVyX3RhZ3MiO3M6MDoiIjtzOjE3OiJmaWx0ZXJfYXR0cmlidXRlcyI7czowOiIiO31pOjg7YTozOntzOjExOiJmaWx0ZXJfdHlwZSI7czo0OiJOT05FIjtzOjExOiJmaWx0ZXJfdGFncyI7czowOiIiO3M6MTc6ImZpbHRlcl9hdHRyaWJ1dGVzIjtzOjA6IiI7fX19fX19czoyMzoiY29tX2NvbXByb2ZpbGVyX2luc3RhbGwiO3M6MDoiIjtzOjExOiJjb21fbW9kdWxlcyI7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJlZGl0IjtPOjg6InN0ZENsYXNzIjoxOntzOjY6Im1vZHVsZSI7Tzo4OiJzdGRDbGFzcyI6Mjp7czoyOiJpZCI7YToxOntpOjA7aTo4Nzt9czo0OiJkYXRhIjtOO319czozOiJhZGQiO086ODoic3RkQ2xhc3MiOjE6e3M6NjoibW9kdWxlIjtPOjg6InN0ZENsYXNzIjoyOntzOjEyOiJleHRlbnNpb25faWQiO047czo2OiJwYXJhbXMiO047fX19czo5OiJjb21fbWVudXMiO086ODoic3RkQ2xhc3MiOjI6e3M6NToiaXRlbXMiO086ODoic3RkQ2xhc3MiOjM6e3M6ODoibWVudXR5cGUiO3M6ODoibWFpbm1lbnUiO3M6MTA6ImxpbWl0c3RhcnQiO2k6MDtzOjQ6Imxpc3QiO2E6NDp7czo5OiJkaXJlY3Rpb24iO3M6MzoiYXNjIjtzOjU6ImxpbWl0IjtzOjI6IjIwIjtzOjg6Im9yZGVyaW5nIjtzOjU6ImEubGZ0IjtzOjU6InN0YXJ0IjtkOjA7fX1zOjQ6ImVkaXQiO086ODoic3RkQ2xhc3MiOjE6e3M6NDoiaXRlbSI7Tzo4OiJzdGRDbGFzcyI6NDp7czoyOiJpZCI7YTowOnt9czo0OiJkYXRhIjtOO3M6NDoidHlwZSI7TjtzOjQ6ImxpbmsiO047fX19czoxMToiY29tX2NvbnRlbnQiO086ODoic3RkQ2xhc3MiOjE6e3M6NDoiZWRpdCI7Tzo4OiJzdGRDbGFzcyI6MTp7czo3OiJhcnRpY2xlIjtPOjg6InN0ZENsYXNzIjoyOntzOjQ6ImRhdGEiO047czoyOiJpZCI7YTowOnt9fX19czoxNDoiY29tX2NhdGVnb3JpZXMiO086ODoic3RkQ2xhc3MiOjI6e3M6MTA6ImNhdGVnb3JpZXMiO086ODoic3RkQ2xhc3MiOjE6e3M6NjoiZmlsdGVyIjtPOjg6InN0ZENsYXNzIjoxOntzOjk6ImV4dGVuc2lvbiI7czoxMToiY29tX2NvbnRlbnQiO319czo0OiJlZGl0IjtPOjg6InN0ZENsYXNzIjoxOntzOjg6ImNhdGVnb3J5IjtPOjg6InN0ZENsYXNzIjoyOntzOjQ6ImRhdGEiO047czoyOiJpZCI7YTowOnt9fX19czo0OiJpdGVtIjtPOjg6InN0ZENsYXNzIjoxOntzOjY6ImZpbHRlciI7Tzo4OiJzdGRDbGFzcyI6MTp7czo4OiJtZW51dHlwZSI7czo4OiJtYWlubWVudSI7fX19czo5OiJzZXBhcmF0b3IiO3M6MToiLiI7fXM6NDoidXNlciI7Tzo1OiJKVXNlciI6Mjg6e3M6OToiACoAaXNSb290IjtiOjE7czoyOiJpZCI7czoyOiIxNSI7czo0OiJuYW1lIjtzOjEwOiJTdXBlciBVc2VyIjtzOjg6InVzZXJuYW1lIjtzOjU6ImVsY29tIjtzOjU6ImVtYWlsIjtzOjEzOiJlbGNvbUBtYWlsLnJ1IjtzOjg6InBhc3N3b3JkIjtzOjYwOiIkMnkkMTAkT2FPUlJMdEltUnBITFRxOFhSMEtUZS85QTFTYnZBdnozWXg5YmpuVjlMZ2xjWTc0TGx3Zk8iO3M6MTQ6InBhc3N3b3JkX2NsZWFyIjtzOjA6IiI7czo1OiJibG9jayI7czoxOiIwIjtzOjk6InNlbmRFbWFpbCI7czoxOiIxIjtzOjEyOiJyZWdpc3RlckRhdGUiO3M6MTk6IjIwMTgtMDktMjcgMTM6Mzc6MTkiO3M6MTM6Imxhc3R2aXNpdERhdGUiO3M6MTk6IjIwMTgtMDktMjcgMTM6Mzc6MzAiO3M6MTA6ImFjdGl2YXRpb24iO3M6MToiMCI7czo2OiJwYXJhbXMiO3M6MDoiIjtzOjY6Imdyb3VwcyI7YToxOntpOjg7czoxOiI4Ijt9czo1OiJndWVzdCI7aTowO3M6MTM6Imxhc3RSZXNldFRpbWUiO3M6MTk6IjAwMDAtMDAtMDAgMDA6MDA6MDAiO3M6MTA6InJlc2V0Q291bnQiO3M6MToiMCI7czoxMjoicmVxdWlyZVJlc2V0IjtzOjE6IjAiO3M6MTA6IgAqAF9wYXJhbXMiO086MjQ6Ikpvb21sYVxSZWdpc3RyeVxSZWdpc3RyeSI6Mjp7czo3OiIAKgBkYXRhIjtPOjg6InN0ZENsYXNzIjowOnt9czo5OiJzZXBhcmF0b3IiO3M6MToiLiI7fXM6MTQ6IgAqAF9hdXRoR3JvdXBzIjthOjI6e2k6MDtpOjE7aToxO2k6ODt9czoxNDoiACoAX2F1dGhMZXZlbHMiO2E6NTp7aTowO2k6MTtpOjE7aToxO2k6MjtpOjI7aTozO2k6MztpOjQ7aTo2O31zOjE1OiIAKgBfYXV0aEFjdGlvbnMiO047czoxMjoiACoAX2Vycm9yTXNnIjtOO3M6MTM6IgAqAHVzZXJIZWxwZXIiO086MTg6IkpVc2VyV3JhcHBlckhlbHBlciI6MDp7fXM6MTA6IgAqAF9lcnJvcnMiO2E6MDp7fXM6MzoiYWlkIjtpOjA7czo2OiJvdHBLZXkiO3M6MDoiIjtzOjQ6Im90ZXAiO3M6MDoiIjt9czoxMToiYXBwbGljYXRpb24iO086ODoic3RkQ2xhc3MiOjE6e3M6NToicXVldWUiO047fXM6OToiY29tX21lZGlhIjtPOjg6InN0ZENsYXNzIjoxOntzOjEwOiJyZXR1cm5fdXJsIjtzOjEwNjoiaW5kZXgucGhwP29wdGlvbj1jb21fbWVkaWEmdmlldz1pbWFnZXMmdG1wbD1jb21wb25lbnQmZmllbGRpZD0mZV9uYW1lPWpmb3JtX2FydGljbGV0ZXh0JmFzc2V0PTc1JmF1dGhvcj0xNSI7fX19czo5OiJzZXBhcmF0b3IiO3M6MToiLiI7fQ==\";cb|a:1:{s:5:\"state\";a:1:{s:13:\"fieldsbrowser\";a:21:{s:6:\"search\";s:0:\"\";s:7:\"orderby\";s:12:\"ordering_asc\";s:11:\"filter_type\";s:0:\"\";s:12:\"filter_tabid\";s:0:\"\";s:15:\"filter_required\";s:0:\"\";s:14:\"filter_profile\";s:0:\"\";s:19:\"filter_registration\";s:0:\"\";s:11:\"filter_edit\";s:0:\"\";s:17:\"filter_searchable\";s:0:\"\";s:16:\"filter_published\";s:0:\"\";s:10:\"batch_type\";s:0:\"\";s:11:\"batch_tabid\";s:0:\"\";s:14:\"batch_required\";s:0:\"\";s:13:\"batch_profile\";s:0:\"\";s:18:\"batch_registration\";s:0:\"\";s:10:\"batch_edit\";s:0:\"\";s:16:\"batch_searchable\";s:0:\"\";s:8:\"ordering\";a:20:{i:0;s:2:\"99\";i:1;s:2:\"99\";i:2;s:2:\"99\";i:3;s:2:\"99\";i:4;s:2:\"99\";i:5;s:2:\"99\";i:6;s:2:\"99\";i:7;s:2:\"99\";i:8;s:2:\"99\";i:9;s:2:\"99\";i:10;s:2:\"99\";i:11;s:2:\"99\";i:12;s:2:\"99\";i:13;s:2:\"99\";i:14;s:2:\"99\";i:15;s:2:\"99\";i:16;s:2:\"99\";i:17;s:2:\"99\";i:18;s:2:\"99\";i:19;s:2:\"99\";}s:5:\"limit\";i:20;s:10:\"limitstart\";i:0;s:7:\"subtask\";s:0:\"\";}}}', 15, 'elcom'),
('sl1iio3idcn4450uk50052puh1', 0, 1, '1538063056', 'joomla|s:2184:\"TzoyNDoiSm9vbWxhXFJlZ2lzdHJ5XFJlZ2lzdHJ5IjoyOntzOjc6IgAqAGRhdGEiO086ODoic3RkQ2xhc3MiOjE6e3M6OToiX19kZWZhdWx0IjtPOjg6InN0ZENsYXNzIjo0OntzOjc6InNlc3Npb24iO086ODoic3RkQ2xhc3MiOjI6e3M6NzoiY291bnRlciI7aTo1NDtzOjU6InRpbWVyIjtPOjg6InN0ZENsYXNzIjozOntzOjU6InN0YXJ0IjtpOjE1MzgwNTc3NTY7czo0OiJsYXN0IjtpOjE1MzgwNjMwNTE7czozOiJub3ciO2k6MTUzODA2MzA1Njt9fXM6ODoicmVnaXN0cnkiO086MjQ6Ikpvb21sYVxSZWdpc3RyeVxSZWdpc3RyeSI6Mjp7czo3OiIAKgBkYXRhIjtPOjg6InN0ZENsYXNzIjowOnt9czo5OiJzZXBhcmF0b3IiO3M6MToiLiI7fXM6NDoidXNlciI7Tzo1OiJKVXNlciI6MjY6e3M6OToiACoAaXNSb290IjtiOjA7czoyOiJpZCI7aTowO3M6NDoibmFtZSI7TjtzOjg6InVzZXJuYW1lIjtOO3M6NToiZW1haWwiO047czo4OiJwYXNzd29yZCI7TjtzOjE0OiJwYXNzd29yZF9jbGVhciI7czowOiIiO3M6NToiYmxvY2siO047czo5OiJzZW5kRW1haWwiO2k6MDtzOjEyOiJyZWdpc3RlckRhdGUiO047czoxMzoibGFzdHZpc2l0RGF0ZSI7TjtzOjEwOiJhY3RpdmF0aW9uIjtOO3M6NjoicGFyYW1zIjtOO3M6NjoiZ3JvdXBzIjthOjE6e2k6MDtzOjE6IjkiO31zOjU6Imd1ZXN0IjtpOjE7czoxMzoibGFzdFJlc2V0VGltZSI7TjtzOjEwOiJyZXNldENvdW50IjtOO3M6MTI6InJlcXVpcmVSZXNldCI7TjtzOjEwOiIAKgBfcGFyYW1zIjtPOjI0OiJKb29tbGFcUmVnaXN0cnlcUmVnaXN0cnkiOjI6e3M6NzoiACoAZGF0YSI7Tzo4OiJzdGRDbGFzcyI6MDp7fXM6OToic2VwYXJhdG9yIjtzOjE6Ii4iO31zOjE0OiIAKgBfYXV0aEdyb3VwcyI7YToyOntpOjA7aToxO2k6MTtpOjk7fXM6MTQ6IgAqAF9hdXRoTGV2ZWxzIjthOjM6e2k6MDtpOjE7aToxO2k6MTtpOjI7aTo1O31zOjE1OiIAKgBfYXV0aEFjdGlvbnMiO047czoxMjoiACoAX2Vycm9yTXNnIjtOO3M6MTM6IgAqAHVzZXJIZWxwZXIiO086MTg6IkpVc2VyV3JhcHBlckhlbHBlciI6MDp7fXM6MTA6IgAqAF9lcnJvcnMiO2E6MDp7fXM6MzoiYWlkIjtpOjA7fXM6MTA6ImNvbV9tYWlsdG8iO086ODoic3RkQ2xhc3MiOjE6e3M6NToibGlua3MiO2E6Mjp7czo0MDoiMTY3YmQwOTVkOWUzMDQ2YTdmY2E3MzY1MTRkZDc5MzdlZjVmOTg3NCI7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJsaW5rIjtzOjExMjoiaHR0cDovL2xvY2FsaG9zdC9lbGNvbS9pbmRleC5waHAvMi1hbGVrc2FuZHItYWxla3NlZXYta3Vyc292b2UtcHJvZWt0aXJvdmFuaWUtZGx5YS1rcmlwdG9ncmFmb3YtdWNoZWJub2UtcG9zb2JpZSI7czo2OiJleHBpcnkiO2k6MTUzODA1OTkxODt9czo0MDoiMzNkOWI5NDIwNTkyMDVlODQxNTE3ZDdmNWRhYTNmOWUyYjQxYzEwMCI7Tzo4OiJzdGRDbGFzcyI6Mjp7czo0OiJsaW5rIjtzOjEzMzoiaHR0cDovL2xvY2FsaG9zdC9lbGNvbS9pbmRleC5waHAvMS1raGFycmlzLWtoYXJyaXMtdHNpZnJvdmF5YS1za2hlbW90ZWtobmlrYS1pLWFya2hpdGVrdHVyYS1rb21weXV0ZXJhLWRvcG9sbmVuaWUtcG8tYXJraGl0ZWt0dXJlLWFybSI7czo2OiJleHBpcnkiO2k6MTUzODA1OTkxODt9fX19fXM6OToic2VwYXJhdG9yIjtzOjE6Ii4iO30=\";', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_tags`
--

CREATE TABLE `orexv_tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `path` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadesc` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modified_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `urls` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_tags`
--

INSERT INTO `orexv_tags` (`id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `created_by_alias`, `modified_user_id`, `modified_time`, `images`, `urls`, `hits`, `language`, `version`, `publish_up`, `publish_down`) VALUES
(1, 0, 0, 1, 0, '', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 42, '2011-01-01 00:00:01', '', 0, '0000-00-00 00:00:00', '', '', 0, '*', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_template_styles`
--

CREATE TABLE `orexv_template_styles` (
  `id` int(10) UNSIGNED NOT NULL,
  `template` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_id` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `home` char(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_template_styles`
--

INSERT INTO `orexv_template_styles` (`id`, `template`, `client_id`, `home`, `title`, `params`) VALUES
(4, 'beez3', 0, '0', 'Beez3 - Default', '{\"wrapperSmall\":\"53\",\"wrapperLarge\":\"72\",\"logo\":\"images\\/joomla_black.png\",\"sitetitle\":\"Joomla!\",\"sitedescription\":\"Open Source Content Management\",\"navposition\":\"left\",\"templatecolor\":\"personal\",\"html5\":\"0\"}'),
(5, 'hathor', 1, '0', 'Hathor - Default', '{\"showSiteName\":\"0\",\"colourChoice\":\"\",\"boldText\":\"0\"}'),
(7, 'protostar', 0, '1', 'protostar - Default', '{\"templateColor\":\"\",\"logoFile\":\"\",\"googleFont\":\"1\",\"googleFontName\":\"Open+Sans\",\"fluidContainer\":\"0\"}'),
(8, 'isis', 1, '1', 'isis - Default', '{\"templateColor\":\"\",\"logoFile\":\"\"}');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_ucm_base`
--

CREATE TABLE `orexv_ucm_base` (
  `ucm_id` int(10) UNSIGNED NOT NULL,
  `ucm_item_id` int(10) NOT NULL,
  `ucm_type_id` int(11) NOT NULL,
  `ucm_language_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_ucm_content`
--

CREATE TABLE `orexv_ucm_content` (
  `core_content_id` int(10) UNSIGNED NOT NULL,
  `core_type_alias` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'FK to the content types table',
  `core_title` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `core_body` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_state` tinyint(1) NOT NULL DEFAULT '0',
  `core_checked_out_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `core_checked_out_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `core_access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `core_params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_featured` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `core_metadata` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON encoded metadata properties.',
  `core_created_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `core_created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `core_created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `core_modified_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Most recent user that modified',
  `core_modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `core_language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_publish_up` datetime NOT NULL,
  `core_publish_down` datetime NOT NULL,
  `core_content_item_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID from the individual type table',
  `asset_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to the #__assets table.',
  `core_images` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_urls` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `core_version` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `core_ordering` int(11) NOT NULL DEFAULT '0',
  `core_metakey` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_metadesc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_catid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `core_xreference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `core_type_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contains core content data in name spaced fields';

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_ucm_history`
--

CREATE TABLE `orexv_ucm_history` (
  `version_id` int(10) UNSIGNED NOT NULL,
  `ucm_item_id` int(10) UNSIGNED NOT NULL,
  `ucm_type_id` int(10) UNSIGNED NOT NULL,
  `version_note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Optional version name',
  `save_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `character_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Number of characters in this version.',
  `sha1_hash` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SHA1 hash of the version_data column.',
  `version_data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'json-encoded string of version data',
  `keep_forever` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=auto delete; 1=keep'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_ucm_history`
--

INSERT INTO `orexv_ucm_history` (`version_id`, `ucm_item_id`, `ucm_type_id`, `version_note`, `save_date`, `editor_user_id`, `character_count`, `sha1_hash`, `version_data`, `keep_forever`) VALUES
(1, 1, 1, '', '2018-09-27 14:44:36', 15, 9492, '88d19222915114ae797e6ac37c63efc7cd19c74f', '{\"id\":1,\"asset_id\":69,\"title\":\"\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441, \\u0425\\u0430\\u0440\\u0440\\u0438\\u0441: \\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM \",\"alias\":\"kharris-kharris-tsifrovaya-skhemotekhnika-i-arkhitektura-kompyutera-dopolnenie-po-arkhitekture-arm\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:\\u00a0<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187439\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0414\\u044d\\u0432\\u0438\\u0434 \\u041c.<\\/a>,\\u00a0<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187440\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0421\\u0430\\u0440\\u0430 \\u041b.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:\\u00a0<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/63408\\/\\\">\\u0421\\u043b\\u0438\\u043d\\u043a\\u0438\\u043d \\u0410. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:\\u00a0<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/205256\\/\\\">\\u041a\\u043e\\u0441\\u043e\\u043b\\u043e\\u0431\\u043e\\u0432 \\u0414. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:\\u00a0<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1416\\/\\\">\\u0414\\u041c\\u041a-\\u041f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2019 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:\\u00a0<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2575\\/\\\">\\u0416\\u0435\\u043b\\u0435\\u0437\\u043e \\u041f\\u041a<\\/a>\\u00a0\\u0438 \\u0434\\u0440.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u00a0<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\"><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0; opacity: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:\\u00a0<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/662585\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/662585\\/<\\/a><\\/span><\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">342<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">\\u00a0mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; background-color: #f0eded;\\\">.<\\/span><\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM\\\" \\u0414\\u0430\\u043d\\u043d\\u043e\\u0435 \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435\\u043c \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430\\\" \\u0441 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0435\\u043c \\u043e\\u0442\\u043b\\u0438\\u0447\\u0438\\u0439 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u044b ARM \\u043e\\u0442 MIPS, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u043d\\u043e\\u0439 \\u0432 \\u043f\\u0435\\u0440\\u0432\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435. \\u041e\\u043d\\u043e \\u0441\\u043e\\u0441\\u0442\\u043e\\u0438\\u0442 \\u0438\\u0437 \\u0433\\u043b\\u0430\\u0432, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u044b\\u0445 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 \\u043f\\u0440\\u043e\\u0446\\u0435\\u0441\\u0441\\u043e\\u0440\\u043e\\u0432 ARM, \\u0438\\u0445 \\u043c\\u0438\\u043a\\u0440\\u043e\\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u044e \\u043f\\u043e\\u0434\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u043f\\u0430\\u043c\\u044f\\u0442\\u0438 \\u0438 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0432\\u0432\\u043e\\u0434\\u0430-\\u0432\\u044b\\u0432\\u043e\\u0434\\u0430. \\u0422\\u0430\\u043a\\u0436\\u0435 \\u0432 \\u043f\\u0440\\u0438\\u043b\\u043e\\u0436\\u0435\\u043d\\u0438\\u0438 \\u043f\\u0440\\u0438\\u0432\\u0435\\u0434\\u0435\\u043d\\u0430 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u0430 \\u043a\\u043e\\u043c\\u0430\\u043d\\u0434 ARM. \\u041a\\u043d\\u0438\\u0433\\u0443 \\u0440\\u0435\\u043a\\u043e\\u043c\\u0435\\u043d\\u0434\\u0443\\u0435\\u0442\\u0441\\u044f \\u0438\\u0441\\u043f\\u043e\\u043b\\u044c\\u0437\\u043e\\u0432\\u0430\\u0442\\u044c \\u0441\\u043e\\u0432\\u043c\\u0435\\u0441\\u0442\\u043d\\u043e \\u0441 \\u043f\\u0435\\u0440\\u0432\\u044b\\u043c (\\u043e\\u0441\\u043d\\u043e\\u0432\\u043d\\u044b\\u043c) \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435\\u043c \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 MIPS. \\u0418\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u0431\\u0443\\u0434\\u0435\\u0442 \\u043f\\u043e\\u043b\\u0435\\u0437\\u043d\\u043e \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430\\u043c, \\u0438\\u043d\\u0436\\u0435\\u043d\\u0435\\u0440\\u0430\\u043c, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u0448\\u0438\\u0440\\u043e\\u043a\\u043e\\u043c\\u0443 \\u043a\\u0440\\u0443\\u0433\\u0443 \\u0447\\u0438\\u0442\\u0430\\u0442\\u0435\\u043b\\u0435\\u0439, \\u0438\\u043d\\u0442\\u0435\\u0440\\u0435\\u0441\\u0443\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u0441\\u043e\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u043e\\u0439.\\u00a0<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:44:36\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 14:44:36\",\"modified_by\":null,\"checked_out\":null,\"checked_out_time\":null,\"publish_up\":\"2018-09-27 14:44:36\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":1,\"ordering\":null,\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":null,\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(2, 1, 1, '', '2018-09-27 14:45:58', 15, 9509, '82afced986ef0a6fd084e5c0e233435e8e24c20e', '{\"id\":1,\"asset_id\":\"69\",\"title\":\"\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441, \\u0425\\u0430\\u0440\\u0440\\u0438\\u0441: \\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM \",\"alias\":\"kharris-kharris-tsifrovaya-skhemotekhnika-i-arkhitektura-kompyutera-dopolnenie-po-arkhitekture-arm\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187439\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0414\\u044d\\u0432\\u0438\\u0434 \\u041c.<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187440\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0421\\u0430\\u0440\\u0430 \\u041b.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/63408\\/\\\">\\u0421\\u043b\\u0438\\u043d\\u043a\\u0438\\u043d \\u0410. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/205256\\/\\\">\\u041a\\u043e\\u0441\\u043e\\u043b\\u043e\\u0431\\u043e\\u0432 \\u0414. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1416\\/\\\">\\u0414\\u041c\\u041a-\\u041f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2019 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2575\\/\\\">\\u0416\\u0435\\u043b\\u0435\\u0437\\u043e \\u041f\\u041a<\\/a>&nbsp;\\u0438 \\u0434\\u0440.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">&nbsp;<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\"><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0; opacity: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/662585\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/662585\\/<\\/a><\\/span><\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">342<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">&nbsp;mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; background-color: #f0eded;\\\">.<\\/span><\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM\\\" \\u0414\\u0430\\u043d\\u043d\\u043e\\u0435 \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435\\u043c \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430\\\" \\u0441 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0435\\u043c \\u043e\\u0442\\u043b\\u0438\\u0447\\u0438\\u0439 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u044b ARM \\u043e\\u0442 MIPS, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u043d\\u043e\\u0439 \\u0432 \\u043f\\u0435\\u0440\\u0432\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435. \\u041e\\u043d\\u043e \\u0441\\u043e\\u0441\\u0442\\u043e\\u0438\\u0442 \\u0438\\u0437 \\u0433\\u043b\\u0430\\u0432, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u044b\\u0445 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 \\u043f\\u0440\\u043e\\u0446\\u0435\\u0441\\u0441\\u043e\\u0440\\u043e\\u0432 ARM, \\u0438\\u0445 \\u043c\\u0438\\u043a\\u0440\\u043e\\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u044e \\u043f\\u043e\\u0434\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u043f\\u0430\\u043c\\u044f\\u0442\\u0438 \\u0438 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0432\\u0432\\u043e\\u0434\\u0430-\\u0432\\u044b\\u0432\\u043e\\u0434\\u0430. \\u0422\\u0430\\u043a\\u0436\\u0435 \\u0432 \\u043f\\u0440\\u0438\\u043b\\u043e\\u0436\\u0435\\u043d\\u0438\\u0438 \\u043f\\u0440\\u0438\\u0432\\u0435\\u0434\\u0435\\u043d\\u0430 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u0430 \\u043a\\u043e\\u043c\\u0430\\u043d\\u0434 ARM. \\u041a\\u043d\\u0438\\u0433\\u0443 \\u0440\\u0435\\u043a\\u043e\\u043c\\u0435\\u043d\\u0434\\u0443\\u0435\\u0442\\u0441\\u044f \\u0438\\u0441\\u043f\\u043e\\u043b\\u044c\\u0437\\u043e\\u0432\\u0430\\u0442\\u044c \\u0441\\u043e\\u0432\\u043c\\u0435\\u0441\\u0442\\u043d\\u043e \\u0441 \\u043f\\u0435\\u0440\\u0432\\u044b\\u043c (\\u043e\\u0441\\u043d\\u043e\\u0432\\u043d\\u044b\\u043c) \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435\\u043c \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 MIPS. \\u0418\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u0431\\u0443\\u0434\\u0435\\u0442 \\u043f\\u043e\\u043b\\u0435\\u0437\\u043d\\u043e \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430\\u043c, \\u0438\\u043d\\u0436\\u0435\\u043d\\u0435\\u0440\\u0430\\u043c, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u0448\\u0438\\u0440\\u043e\\u043a\\u043e\\u043c\\u0443 \\u043a\\u0440\\u0443\\u0433\\u0443 \\u0447\\u0438\\u0442\\u0430\\u0442\\u0435\\u043b\\u0435\\u0439, \\u0438\\u043d\\u0442\\u0435\\u0440\\u0435\\u0441\\u0443\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u0441\\u043e\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u043e\\u0439.&nbsp;<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:44:36\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 14:45:58\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 14:45:38\",\"publish_up\":\"2018-09-27 14:44:36\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":2,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(3, 1, 1, '', '2018-09-27 14:48:08', 15, 9511, '0e7b5b7502b93954d16bf812fd7381563af0d290', '{\"id\":1,\"asset_id\":\"69\",\"title\":\"\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441, \\u0425\\u0430\\u0440\\u0440\\u0438\\u0441: \\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM \",\"alias\":\"kharris-kharris-tsifrovaya-skhemotekhnika-i-arkhitektura-kompyutera-dopolnenie-po-arkhitekture-arm\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187439\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0414\\u044d\\u0432\\u0438\\u0434 \\u041c.<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187440\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0421\\u0430\\u0440\\u0430 \\u041b.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/63408\\/\\\">\\u0421\\u043b\\u0438\\u043d\\u043a\\u0438\\u043d \\u0410. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/205256\\/\\\">\\u041a\\u043e\\u0441\\u043e\\u043b\\u043e\\u0431\\u043e\\u0432 \\u0414. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1416\\/\\\">\\u0414\\u041c\\u041a-\\u041f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2019 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2575\\/\\\">\\u0416\\u0435\\u043b\\u0435\\u0437\\u043e \\u041f\\u041a<\\/a>&nbsp;\\u0438 \\u0434\\u0440.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">&nbsp;<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\"><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0; opacity: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/662585\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/662585\\/<\\/a><\\/span><\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">342<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">&nbsp;mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; background-color: #f0eded;\\\">.<\\/span><\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM\\\" \\u0414\\u0430\\u043d\\u043d\\u043e\\u0435 \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435\\u043c \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430\\\" \\u0441 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0435\\u043c \\u043e\\u0442\\u043b\\u0438\\u0447\\u0438\\u0439 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u044b ARM \\u043e\\u0442 MIPS, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u043d\\u043e\\u0439 \\u0432 \\u043f\\u0435\\u0440\\u0432\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435. \\u041e\\u043d\\u043e \\u0441\\u043e\\u0441\\u0442\\u043e\\u0438\\u0442 \\u0438\\u0437 \\u0433\\u043b\\u0430\\u0432, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u044b\\u0445 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 \\u043f\\u0440\\u043e\\u0446\\u0435\\u0441\\u0441\\u043e\\u0440\\u043e\\u0432 ARM, \\u0438\\u0445 \\u043c\\u0438\\u043a\\u0440\\u043e\\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u044e \\u043f\\u043e\\u0434\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u043f\\u0430\\u043c\\u044f\\u0442\\u0438 \\u0438 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0432\\u0432\\u043e\\u0434\\u0430-\\u0432\\u044b\\u0432\\u043e\\u0434\\u0430. \\u0422\\u0430\\u043a\\u0436\\u0435 \\u0432 \\u043f\\u0440\\u0438\\u043b\\u043e\\u0436\\u0435\\u043d\\u0438\\u0438 \\u043f\\u0440\\u0438\\u0432\\u0435\\u0434\\u0435\\u043d\\u0430 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u0430 \\u043a\\u043e\\u043c\\u0430\\u043d\\u0434 ARM. \\u041a\\u043d\\u0438\\u0433\\u0443 \\u0440\\u0435\\u043a\\u043e\\u043c\\u0435\\u043d\\u0434\\u0443\\u0435\\u0442\\u0441\\u044f \\u0438\\u0441\\u043f\\u043e\\u043b\\u044c\\u0437\\u043e\\u0432\\u0430\\u0442\\u044c \\u0441\\u043e\\u0432\\u043c\\u0435\\u0441\\u0442\\u043d\\u043e \\u0441 \\u043f\\u0435\\u0440\\u0432\\u044b\\u043c (\\u043e\\u0441\\u043d\\u043e\\u0432\\u043d\\u044b\\u043c) \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435\\u043c \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 MIPS. \\u0418\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u0431\\u0443\\u0434\\u0435\\u0442 \\u043f\\u043e\\u043b\\u0435\\u0437\\u043d\\u043e \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430\\u043c, \\u0438\\u043d\\u0436\\u0435\\u043d\\u0435\\u0440\\u0430\\u043c, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u0448\\u0438\\u0440\\u043e\\u043a\\u043e\\u043c\\u0443 \\u043a\\u0440\\u0443\\u0433\\u0443 \\u0447\\u0438\\u0442\\u0430\\u0442\\u0435\\u043b\\u0435\\u0439, \\u0438\\u043d\\u0442\\u0435\\u0440\\u0435\\u0441\\u0443\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u0441\\u043e\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u043e\\u0439.&nbsp;<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:44:36\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 14:48:08\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 14:47:44\",\"publish_up\":\"2018-09-27 14:44:36\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big1.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big1.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":3,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(4, 2, 1, '', '2018-09-27 14:50:15', 15, 11611, 'f3866c9573a114ffee526a35520c045e146c5cef', '{\"id\":2,\"asset_id\":71,\"title\":\"\\u0410\\u043b\\u0435\\u043a\\u0441\\u0430\\u043d\\u0434\\u0440 \\u0410\\u043b\\u0435\\u043a\\u0441\\u0435\\u0435\\u0432: \\u041a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0434\\u043b\\u044f \\u043a\\u0440\\u0438\\u043f\\u0442\\u043e\\u0433\\u0440\\u0430\\u0444\\u043e\\u0432. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \",\"alias\":\"aleksandr-alekseev-kursovoe-proektirovanie-dlya-kriptografov-uchebnoe-posobie\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/98473\\/\\\">\\u0410\\u043b\\u0435\\u043a\\u0441\\u0435\\u0435\\u0432 \\u0410\\u043b\\u0435\\u043a\\u0441\\u0430\\u043d\\u0434\\u0440 \\u041f\\u0435\\u0442\\u0440\\u043e\\u0432\\u0438\\u0447<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1418\\/\\\">\\u0421\\u043e\\u043b\\u043e\\u043d-\\u043f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0421\\u0435\\u0440\\u0438\\u044f:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/26335\\/\\\">\\u0411\\u0438\\u0431\\u043b\\u0438\\u043e\\u0442\\u0435\\u043a\\u0430 \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430<\\/a>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\"><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/26335\\/\\\"><\\/a><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\">\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #1868a0;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/655859\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/655859\\/<\\/a><\\/span><\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">1272<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\\\"><\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">mdl.<\\/span><\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u041a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0434\\u043b\\u044f \\u043a\\u0440\\u0438\\u043f\\u0442\\u043e\\u0433\\u0440\\u0430\\u0444\\u043e\\u0432. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435\\\" \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \\u0441\\u043e\\u0434\\u0435\\u0440\\u0436\\u0438\\u0442 \\u0437\\u0430\\u0434\\u0430\\u043d\\u0438\\u0435 \\u043d\\u0430 \\u043a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0438 \\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0443\\u043a\\u0430\\u0437\\u0430\\u043d\\u0438\\u044f \\u0434\\u043b\\u044f \\u0432\\u044b\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u044f \\u0437\\u0430\\u0434\\u0430\\u043d\\u0438\\u044f. \\u041e\\u043f\\u0438\\u0441\\u0430\\u043d\\u044b \\u043c\\u0435\\u0442\\u043e\\u0434\\u044b \\u0441\\u0436\\u0430\\u0442\\u0438\\u044f \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 (\\u0425\\u0430\\u0444\\u0444\\u043c\\u0430\\u043d\\u0430, RLE), \\u043f\\u043e\\u043c\\u0435\\u0445\\u043e\\u0443\\u0441\\u0442\\u043e\\u0439\\u0447\\u0438\\u0432\\u043e\\u0433\\u043e \\u043a\\u043e\\u0434\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f (\\u043a\\u043e\\u0434\\u044b \\u0425\\u044d\\u043c\\u043c\\u0438\\u043d\\u0433\\u0430 \\u0438 \\u0411\\u0427\\u0425), \\u0448\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f (\\u0430\\u0434\\u0434\\u0438\\u0442\\u0438\\u0432\\u043d\\u044b\\u0439 \\u0448\\u0438\\u0444\\u0440 \\u0438 \\u0448\\u0438\\u0444\\u0440 \\u0441 \\u0443\\u043f\\u0440\\u0430\\u0432\\u043b\\u044f\\u0435\\u043c\\u044b\\u043c\\u0438 \\u043e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f\\u043c\\u0438), \\u0441\\u0442\\u0435\\u0433\\u0430\\u043d\\u043e\\u0433\\u0440\\u0430\\u0444\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e \\u0441\\u043e\\u043a\\u0440\\u044b\\u0442\\u0438\\u044f \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 (\\u0441\\u043a\\u0440\\u044b\\u0442\\u0430\\u044f \\u043f\\u0435\\u0440\\u0435\\u0434\\u0430\\u0447\\u0430 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 \\u0432 \\u0433\\u0440\\u0430\\u0444\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u043c \\u0444\\u0430\\u0439\\u043b\\u0435 \\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0430 BMP, \\u0432 \\u0437\\u0432\\u0443\\u043a\\u043e\\u0432\\u043e\\u043c \\u0444\\u0430\\u0439\\u043b\\u0435 \\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0430 WAV, \\u043d\\u0430 HTML-\\u0441\\u0442\\u0440\\u0430\\u043d\\u0438\\u0446\\u0430\\u0445), \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d \\u043f\\u043e\\u0440\\u044f\\u0434\\u043e\\u043a \\u043c\\u043e\\u0434\\u0435\\u043b\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f \\u0446\\u0438\\u0444\\u0440\\u043e\\u0432\\u044b\\u0445 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432 (\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c \\u0448\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f, \\u0440\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430 \\u0441\\u0434\\u0432\\u0438\\u0433\\u0430 \\u0438 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u0430 \\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u044f \\u043f\\u043e\\u043b\\u0438\\u043d\\u043e\\u043c\\u043e\\u0432). \\u0414\\u0430\\u043d\\u043d\\u0430\\u044f \\u0440\\u0430\\u0431\\u043e\\u0442\\u0430 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0441\\u043e\\u0441\\u0442\\u0430\\u0432\\u043d\\u043e\\u0439 \\u0447\\u0430\\u0441\\u0442\\u044c\\u044e \\u0443\\u0447\\u0435\\u0431\\u043d\\u043e-\\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e \\u043a\\u043e\\u043c\\u043f\\u043b\\u0435\\u043a\\u0441\\u0430, \\u043f\\u043e\\u0434\\u0433\\u043e\\u0442\\u043e\\u0432\\u043b\\u0435\\u043d\\u043d\\u043e\\u0433\\u043e \\u0430\\u0432\\u0442\\u043e\\u0440\\u043e\\u043c. \\u041a\\u043e\\u043c\\u043f\\u043b\\u0435\\u043a\\u0441 \\u0432\\u043a\\u043b\\u044e\\u0447\\u0430\\u0435\\u0442 \\u0432 \\u0441\\u0435\\u0431\\u044f \\u043b\\u0435\\u043a\\u0446\\u0438\\u0438, \\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0443\\u043a\\u0430\\u0437\\u0430\\u043d\\u0438\\u044f \\u043d\\u0430 \\u0432\\u044b\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0438 \\u043b\\u0430\\u0431\\u043e\\u0440\\u0430\\u0442\\u043e\\u0440\\u043d\\u044b\\u0445 \\u0440\\u0430\\u0431\\u043e\\u0442 \\u0432 \\u0434\\u0432\\u0443\\u0445 \\u0441\\u0435\\u043c\\u0435\\u0441\\u0442\\u0440\\u0430\\u0445 \\u0438 \\u0441\\u0431\\u043e\\u0440\\u043d\\u0438\\u043a \\u0437\\u0430\\u0434\\u0430\\u0447 \\u0434\\u043b\\u044f \\u043f\\u0440\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0445 \\u0437\\u0430\\u043d\\u044f\\u0442\\u0438\\u0439. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \\u043f\\u043e \\u0434\\u0438\\u0441\\u0446\\u0438\\u043f\\u043b\\u0438\\u043d\\u0435 \\\"\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430\\\" \\u0434\\u043b\\u044f \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u043e\\u0432 \\u0441\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0435\\u0439 10.03.01 \\u0438 10.05.02 \\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435: https:\\/\\/www.labirint.ru\\/books\\/655859\\/<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:50:15\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 14:50:15\",\"modified_by\":null,\"checked_out\":null,\"checked_out_time\":null,\"publish_up\":\"2018-09-27 14:50:15\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big2.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big2.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":1,\"ordering\":null,\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":null,\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0);
INSERT INTO `orexv_ucm_history` (`version_id`, `ucm_item_id`, `ucm_type_id`, `version_note`, `save_date`, `editor_user_id`, `character_count`, `sha1_hash`, `version_data`, `keep_forever`) VALUES
(5, 2, 1, '', '2018-09-27 15:00:55', 15, 11650, 'eabb45bc97152245c27fccd281a0ee99f2b2c3a0', '{\"id\":2,\"asset_id\":\"71\",\"title\":\"\\u0410\\u043b\\u0435\\u043a\\u0441\\u0430\\u043d\\u0434\\u0440 \\u0410\\u043b\\u0435\\u043a\\u0441\\u0435\\u0435\\u0432: \\u041a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0434\\u043b\\u044f \\u043a\\u0440\\u0438\\u043f\\u0442\\u043e\\u0433\\u0440\\u0430\\u0444\\u043e\\u0432. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \",\"alias\":\"aleksandr-alekseev-kursovoe-proektirovanie-dlya-kriptografov-uchebnoe-posobie\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/98473\\/\\\">\\u0410\\u043b\\u0435\\u043a\\u0441\\u0435\\u0435\\u0432 \\u0410\\u043b\\u0435\\u043a\\u0441\\u0430\\u043d\\u0434\\u0440 \\u041f\\u0435\\u0442\\u0440\\u043e\\u0432\\u0438\\u0447<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1418\\/\\\">\\u0421\\u043e\\u043b\\u043e\\u043d-\\u043f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0421\\u0435\\u0440\\u0438\\u044f:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/26335\\/\\\">\\u0411\\u0438\\u0431\\u043b\\u0438\\u043e\\u0442\\u0435\\u043a\\u0430 \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430<\\/a>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\"><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/26335\\/\\\"><\\/a><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\">\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #1868a0;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/655859\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/655859\\/<\\/a><\\/span><\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">1272<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\\\"><\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">mdl.<\\/span><\\/p>\\r\\n\",\"fulltext\":\"\\r\\n<p>&nbsp;<\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u041a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0434\\u043b\\u044f \\u043a\\u0440\\u0438\\u043f\\u0442\\u043e\\u0433\\u0440\\u0430\\u0444\\u043e\\u0432. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435\\\" \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \\u0441\\u043e\\u0434\\u0435\\u0440\\u0436\\u0438\\u0442 \\u0437\\u0430\\u0434\\u0430\\u043d\\u0438\\u0435 \\u043d\\u0430 \\u043a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0438 \\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0443\\u043a\\u0430\\u0437\\u0430\\u043d\\u0438\\u044f \\u0434\\u043b\\u044f \\u0432\\u044b\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u044f \\u0437\\u0430\\u0434\\u0430\\u043d\\u0438\\u044f. \\u041e\\u043f\\u0438\\u0441\\u0430\\u043d\\u044b \\u043c\\u0435\\u0442\\u043e\\u0434\\u044b \\u0441\\u0436\\u0430\\u0442\\u0438\\u044f \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 (\\u0425\\u0430\\u0444\\u0444\\u043c\\u0430\\u043d\\u0430, RLE), \\u043f\\u043e\\u043c\\u0435\\u0445\\u043e\\u0443\\u0441\\u0442\\u043e\\u0439\\u0447\\u0438\\u0432\\u043e\\u0433\\u043e \\u043a\\u043e\\u0434\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f (\\u043a\\u043e\\u0434\\u044b \\u0425\\u044d\\u043c\\u043c\\u0438\\u043d\\u0433\\u0430 \\u0438 \\u0411\\u0427\\u0425), \\u0448\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f (\\u0430\\u0434\\u0434\\u0438\\u0442\\u0438\\u0432\\u043d\\u044b\\u0439 \\u0448\\u0438\\u0444\\u0440 \\u0438 \\u0448\\u0438\\u0444\\u0440 \\u0441 \\u0443\\u043f\\u0440\\u0430\\u0432\\u043b\\u044f\\u0435\\u043c\\u044b\\u043c\\u0438 \\u043e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f\\u043c\\u0438), \\u0441\\u0442\\u0435\\u0433\\u0430\\u043d\\u043e\\u0433\\u0440\\u0430\\u0444\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e \\u0441\\u043e\\u043a\\u0440\\u044b\\u0442\\u0438\\u044f \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 (\\u0441\\u043a\\u0440\\u044b\\u0442\\u0430\\u044f \\u043f\\u0435\\u0440\\u0435\\u0434\\u0430\\u0447\\u0430 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 \\u0432 \\u0433\\u0440\\u0430\\u0444\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u043c \\u0444\\u0430\\u0439\\u043b\\u0435 \\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0430 BMP, \\u0432 \\u0437\\u0432\\u0443\\u043a\\u043e\\u0432\\u043e\\u043c \\u0444\\u0430\\u0439\\u043b\\u0435 \\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0430 WAV, \\u043d\\u0430 HTML-\\u0441\\u0442\\u0440\\u0430\\u043d\\u0438\\u0446\\u0430\\u0445), \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d \\u043f\\u043e\\u0440\\u044f\\u0434\\u043e\\u043a \\u043c\\u043e\\u0434\\u0435\\u043b\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f \\u0446\\u0438\\u0444\\u0440\\u043e\\u0432\\u044b\\u0445 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432 (\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c \\u0448\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f, \\u0440\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430 \\u0441\\u0434\\u0432\\u0438\\u0433\\u0430 \\u0438 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u0430 \\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u044f \\u043f\\u043e\\u043b\\u0438\\u043d\\u043e\\u043c\\u043e\\u0432). \\u0414\\u0430\\u043d\\u043d\\u0430\\u044f \\u0440\\u0430\\u0431\\u043e\\u0442\\u0430 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0441\\u043e\\u0441\\u0442\\u0430\\u0432\\u043d\\u043e\\u0439 \\u0447\\u0430\\u0441\\u0442\\u044c\\u044e \\u0443\\u0447\\u0435\\u0431\\u043d\\u043e-\\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e \\u043a\\u043e\\u043c\\u043f\\u043b\\u0435\\u043a\\u0441\\u0430, \\u043f\\u043e\\u0434\\u0433\\u043e\\u0442\\u043e\\u0432\\u043b\\u0435\\u043d\\u043d\\u043e\\u0433\\u043e \\u0430\\u0432\\u0442\\u043e\\u0440\\u043e\\u043c. \\u041a\\u043e\\u043c\\u043f\\u043b\\u0435\\u043a\\u0441 \\u0432\\u043a\\u043b\\u044e\\u0447\\u0430\\u0435\\u0442 \\u0432 \\u0441\\u0435\\u0431\\u044f \\u043b\\u0435\\u043a\\u0446\\u0438\\u0438, \\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0443\\u043a\\u0430\\u0437\\u0430\\u043d\\u0438\\u044f \\u043d\\u0430 \\u0432\\u044b\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0438 \\u043b\\u0430\\u0431\\u043e\\u0440\\u0430\\u0442\\u043e\\u0440\\u043d\\u044b\\u0445 \\u0440\\u0430\\u0431\\u043e\\u0442 \\u0432 \\u0434\\u0432\\u0443\\u0445 \\u0441\\u0435\\u043c\\u0435\\u0441\\u0442\\u0440\\u0430\\u0445 \\u0438 \\u0441\\u0431\\u043e\\u0440\\u043d\\u0438\\u043a \\u0437\\u0430\\u0434\\u0430\\u0447 \\u0434\\u043b\\u044f \\u043f\\u0440\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0445 \\u0437\\u0430\\u043d\\u044f\\u0442\\u0438\\u0439. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \\u043f\\u043e \\u0434\\u0438\\u0441\\u0446\\u0438\\u043f\\u043b\\u0438\\u043d\\u0435 \\\"\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430\\\" \\u0434\\u043b\\u044f \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u043e\\u0432 \\u0441\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0435\\u0439 10.03.01 \\u0438 10.05.02 \\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435: https:\\/\\/www.labirint.ru\\/books\\/655859\\/<\\/p>\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:50:15\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:00:55\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:00:36\",\"publish_up\":\"2018-09-27 14:50:15\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big2.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big2.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":2,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"2\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(6, 1, 1, '', '2018-09-27 15:01:17', 15, 9533, 'dc64c96d153ecc7cb1636ccdd98438481eb41c4b', '{\"id\":1,\"asset_id\":\"69\",\"title\":\"\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441, \\u0425\\u0430\\u0440\\u0440\\u0438\\u0441: \\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM \",\"alias\":\"kharris-kharris-tsifrovaya-skhemotekhnika-i-arkhitektura-kompyutera-dopolnenie-po-arkhitekture-arm\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187439\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0414\\u044d\\u0432\\u0438\\u0434 \\u041c.<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187440\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0421\\u0430\\u0440\\u0430 \\u041b.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/63408\\/\\\">\\u0421\\u043b\\u0438\\u043d\\u043a\\u0438\\u043d \\u0410. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/205256\\/\\\">\\u041a\\u043e\\u0441\\u043e\\u043b\\u043e\\u0431\\u043e\\u0432 \\u0414. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1416\\/\\\">\\u0414\\u041c\\u041a-\\u041f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2019 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2575\\/\\\">\\u0416\\u0435\\u043b\\u0435\\u0437\\u043e \\u041f\\u041a<\\/a>&nbsp;\\u0438 \\u0434\\u0440.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">&nbsp;<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\"><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0; opacity: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/662585\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/662585\\/<\\/a><\\/span><\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">342<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">&nbsp;mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; background-color: #f0eded;\\\">.<\\/span><\\/p>\\r\\n\",\"fulltext\":\"\\r\\n<p>&nbsp;<\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM\\\" \\u0414\\u0430\\u043d\\u043d\\u043e\\u0435 \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435\\u043c \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430\\\" \\u0441 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0435\\u043c \\u043e\\u0442\\u043b\\u0438\\u0447\\u0438\\u0439 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u044b ARM \\u043e\\u0442 MIPS, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u043d\\u043e\\u0439 \\u0432 \\u043f\\u0435\\u0440\\u0432\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435. \\u041e\\u043d\\u043e \\u0441\\u043e\\u0441\\u0442\\u043e\\u0438\\u0442 \\u0438\\u0437 \\u0433\\u043b\\u0430\\u0432, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u044b\\u0445 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 \\u043f\\u0440\\u043e\\u0446\\u0435\\u0441\\u0441\\u043e\\u0440\\u043e\\u0432 ARM, \\u0438\\u0445 \\u043c\\u0438\\u043a\\u0440\\u043e\\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u044e \\u043f\\u043e\\u0434\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u043f\\u0430\\u043c\\u044f\\u0442\\u0438 \\u0438 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0432\\u0432\\u043e\\u0434\\u0430-\\u0432\\u044b\\u0432\\u043e\\u0434\\u0430. \\u0422\\u0430\\u043a\\u0436\\u0435 \\u0432 \\u043f\\u0440\\u0438\\u043b\\u043e\\u0436\\u0435\\u043d\\u0438\\u0438 \\u043f\\u0440\\u0438\\u0432\\u0435\\u0434\\u0435\\u043d\\u0430 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u0430 \\u043a\\u043e\\u043c\\u0430\\u043d\\u0434 ARM. \\u041a\\u043d\\u0438\\u0433\\u0443 \\u0440\\u0435\\u043a\\u043e\\u043c\\u0435\\u043d\\u0434\\u0443\\u0435\\u0442\\u0441\\u044f \\u0438\\u0441\\u043f\\u043e\\u043b\\u044c\\u0437\\u043e\\u0432\\u0430\\u0442\\u044c \\u0441\\u043e\\u0432\\u043c\\u0435\\u0441\\u0442\\u043d\\u043e \\u0441 \\u043f\\u0435\\u0440\\u0432\\u044b\\u043c (\\u043e\\u0441\\u043d\\u043e\\u0432\\u043d\\u044b\\u043c) \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435\\u043c \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 MIPS. \\u0418\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u0431\\u0443\\u0434\\u0435\\u0442 \\u043f\\u043e\\u043b\\u0435\\u0437\\u043d\\u043e \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430\\u043c, \\u0438\\u043d\\u0436\\u0435\\u043d\\u0435\\u0440\\u0430\\u043c, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u0448\\u0438\\u0440\\u043e\\u043a\\u043e\\u043c\\u0443 \\u043a\\u0440\\u0443\\u0433\\u0443 \\u0447\\u0438\\u0442\\u0430\\u0442\\u0435\\u043b\\u0435\\u0439, \\u0438\\u043d\\u0442\\u0435\\u0440\\u0435\\u0441\\u0443\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u0441\\u043e\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u043e\\u0439.&nbsp;<\\/p>\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:44:36\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:01:17\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:01:06\",\"publish_up\":\"2018-09-27 14:44:36\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big1.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big1.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":4,\"ordering\":\"1\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(7, 3, 1, '', '2018-09-27 15:23:56', 15, 12904, '85b5709305dceaebd05ee45894b81cbb8fdea4ac', '{\"id\":3,\"asset_id\":72,\"title\":\"\\u0413\\u0443\\u043b\\u0430\\u043a\\u043e\\u0432, \\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432, \\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432: \\u0421\\u0442\\u0440\\u0443\\u043a\\u0442\\u0443\\u0440\\u044b \\u0438 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u044b \\u043e\\u0431\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0445 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445\",\"alias\":\"gulakov-trubakov-trubakov-struktury-i-algoritmy-obrabotki-mnogomernykh-dannykh\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/204282\\/\\\">\\u0413\\u0443\\u043b\\u0430\\u043a\\u043e\\u0432 \\u0412\\u0430\\u0441\\u0438\\u043b\\u0438\\u0439 \\u041a\\u043e\\u043d\\u0441\\u0442\\u0430\\u043d\\u0442\\u0438\\u043d\\u043e\\u0432\\u0438\\u0447<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/204283\\/\\\">\\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432 \\u0410\\u043d\\u0434\\u0440\\u0435\\u0439 \\u041e\\u043b\\u0435\\u0433\\u043e\\u0432\\u0438\\u0447<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/204284\\/\\\">\\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432 \\u0415\\u0432\\u0433\\u0435\\u043d\\u0438\\u0439 \\u041e\\u043b\\u0435\\u0433\\u043e\\u0432\\u0438\\u0447<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/69777\\/\\\">\\u041c\\u0430\\u043a\\u0430\\u0440\\u043e\\u0432 \\u0421. \\u0412.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/73\\/\\\">\\u041b\\u0430\\u043d\\u044c<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0421\\u0435\\u0440\\u0438\\u044f:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/30905\\/\\\">\\u0423\\u0447\\u0435\\u0431\\u043d\\u0438\\u043a\\u0438 \\u0434\\u043b\\u044f \\u0432\\u0443\\u0437\\u043e\\u0432. \\u0421\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u044c\\u043d\\u0430\\u044f \\u043b\\u0438\\u0442\\u0435\\u0440\\u0430\\u0442\\u0443\\u0440\\u0430<span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<\\/span><\\/a><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/653486\\/\\\"><br \\/><\\/a><\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">1981<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\\\">&nbsp;mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">.<\\/span><\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0421\\u0442\\u0440\\u0443\\u043a\\u0442\\u0443\\u0440\\u044b \\u0438 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u044b \\u043e\\u0431\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0445 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445\\\" \\u041a\\u043d\\u0438\\u0433\\u0430 \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u0430 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u044e \\u0441\\u0442\\u0440\\u0443\\u043a\\u0442\\u0443\\u0440 \\u0438 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u043e\\u0432 \\u0434\\u043b\\u044f \\u0438\\u043d\\u0434\\u0435\\u043a\\u0441\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f \\u0438 \\u043e\\u0431\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0445 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445. \\u0412 \\u043d\\u0435\\u0439 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u0437\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u044b \\u043d\\u0430\\u0438\\u0431\\u043e\\u043b\\u0435\\u0435 \\u0432\\u0430\\u0436\\u043d\\u044b\\u0435 \\u043f\\u043e\\u0434\\u0445\\u043e\\u0434\\u044b, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u044b \\u0438\\u0445 \\u043c\\u0430\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0438 \\u043b\\u043e\\u0433\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u043f\\u0440\\u0438\\u043d\\u0446\\u0438\\u043f\\u044b \\u043f\\u043e\\u0441\\u0442\\u0440\\u043e\\u0435\\u043d\\u0438\\u044f, \\u043f\\u0440\\u043e\\u0430\\u043d\\u0430\\u043b\\u0438\\u0437\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u044b \\u0434\\u043e\\u0441\\u0442\\u043e\\u0438\\u043d\\u0441\\u0442\\u0432\\u0430 \\u0438 \\u043d\\u0435\\u0434\\u043e\\u0441\\u0442\\u0430\\u0442\\u043a\\u0438. \\u0421\\u043e\\u0434\\u0435\\u0440\\u0436\\u0438\\u0442\\u0441\\u044f \\u0431\\u043e\\u043b\\u044c\\u0448\\u043e\\u0435 \\u0447\\u0438\\u0441\\u043b\\u043e \\u043f\\u0440\\u0438\\u043c\\u0435\\u0440\\u043e\\u0432 \\u043b\\u0438\\u0441\\u0442\\u0438\\u043d\\u0433\\u0430, \\u043f\\u043e\\u0437\\u0432\\u043e\\u043b\\u044f\\u044e\\u0449\\u0438\\u0445 \\u0431\\u043e\\u043b\\u0435\\u0435 \\u0434\\u0435\\u0442\\u0430\\u043b\\u044c\\u043d\\u043e \\u0440\\u0430\\u0437\\u043e\\u0431\\u0440\\u0430\\u0442\\u044c\\u0441\\u044f \\u0432 \\u043f\\u0440\\u0435\\u0434\\u0441\\u0442\\u0430\\u0432\\u043b\\u0435\\u043d\\u043d\\u044b\\u0445 \\u043f\\u043e\\u0434\\u0445\\u043e\\u0434\\u0430\\u0445. \\u041d\\u0430 \\u0440\\u0430\\u0437\\u043b\\u0438\\u0447\\u043d\\u044b\\u0445 \\u043f\\u0440\\u0438\\u043c\\u0435\\u0440\\u0430\\u0445 \\u0440\\u0430\\u0441\\u0441\\u043c\\u0430\\u0442\\u0440\\u0438\\u0432\\u0430\\u044e\\u0442\\u0441\\u044f \\u043e\\u0441\\u043e\\u0431\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f \\u0438 \\u0440\\u0430\\u0437\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043f\\u0440\\u0438\\u043b\\u043e\\u0436\\u0435\\u043d\\u0438\\u0439, \\u043e\\u0431\\u0440\\u0430\\u0431\\u0430\\u0442\\u044b\\u0432\\u0430\\u044e\\u0449\\u0438\\u0445 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0435 \\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u0430\\u0442\\u0440\\u0438\\u0431\\u0443\\u0442\\u043d\\u044b\\u0435 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0435. \\u041c\\u043e\\u043d\\u043e\\u0433\\u0440\\u0430\\u0444\\u0438\\u044f \\u043f\\u0440\\u0435\\u0434\\u043d\\u0430\\u0437\\u043d\\u0430\\u0447\\u0435\\u043d\\u0430 \\u0434\\u043b\\u044f \\u0431\\u0430\\u043a\\u0430\\u043b\\u0430\\u0432\\u0440\\u043e\\u0432 \\u0438 \\u043c\\u0430\\u0433\\u0438\\u0441\\u0442\\u0440\\u043e\\u0432, \\u043e\\u0431\\u0443\\u0447\\u0430\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u043f\\u043e \\u043d\\u0430\\u043f\\u0440\\u0430\\u0432\\u043b\\u0435\\u043d\\u0438\\u044f\\u043c \\u00ab\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430 \\u0438 \\u0432\\u044b\\u0447\\u0438\\u0441\\u043b\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u0430\\u044f \\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430\\u00bb, \\u00ab\\u041f\\u0440\\u043e\\u0433\\u0440\\u0430\\u043c\\u043c\\u043d\\u0430\\u044f \\u0438\\u043d\\u0436\\u0435\\u043d\\u0435\\u0440\\u0438\\u044f\\u00bb, \\u00ab\\u041c\\u0430\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0435 \\u043e\\u0431\\u0435\\u0441\\u043f\\u0435\\u0447\\u0435\\u043d\\u0438\\u0435 \\u0438 \\u0430\\u0434\\u043c\\u0438\\u043d\\u0438\\u0441\\u0442\\u0440\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u043e\\u043d\\u043d\\u044b\\u0445 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u00bb, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u043f\\u043e \\u0431\\u043b\\u0438\\u0437\\u043a\\u0438\\u043c \\u043d\\u0430\\u043f\\u0440\\u0430\\u0432\\u043b\\u0435\\u043d\\u0438\\u044f\\u043c. \\u0422\\u0430\\u043a\\u0436\\u0435 \\u043e\\u043d\\u0430 \\u0431\\u0443\\u0434\\u0435\\u0442 \\u043f\\u043e\\u043b\\u0435\\u0437\\u043d\\u0430 \\u043d\\u0430\\u0443\\u0447\\u043d\\u044b\\u043c \\u0440\\u0430\\u0431\\u043e\\u0442\\u043d\\u0438\\u043a\\u0430\\u043c, \\u043f\\u0440\\u0435\\u043f\\u043e\\u0434\\u0430\\u0432\\u0430\\u0442\\u0435\\u043b\\u044f\\u043c, \\u0441\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u0438\\u0441\\u0442\\u0430\\u043c, \\u0430\\u0441\\u043f\\u0438\\u0440\\u0430\\u043d\\u0442\\u0430\\u043c, \\u0441\\u0432\\u044f\\u0437\\u0430\\u043d\\u043d\\u044b\\u043c \\u0441 \\u043f\\u0440\\u0438\\u043a\\u043b\\u0430\\u0434\\u043d\\u043e\\u0439 \\u043c\\u0430\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u043a\\u043e\\u0439 \\u0438 \\u0440\\u0430\\u0437\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u043e\\u0439 \\u043f\\u0440\\u043e\\u0433\\u0440\\u0430\\u043c\\u043c\\u043d\\u043e\\u0433\\u043e \\u043e\\u0431\\u0435\\u0441\\u043f\\u0435\\u0447\\u0435\\u043d\\u0438\\u044f. \\u041c\\u043e\\u0436\\u043d\\u043e \\u0438\\u0441\\u043f\\u043e\\u043b\\u044c\\u0437\\u043e\\u0432\\u0430\\u0442\\u044c \\u0441\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u0438\\u0441\\u0442\\u0430\\u043c, \\u0437\\u0430\\u043d\\u0438\\u043c\\u0430\\u044e\\u0449\\u0438\\u043c\\u0441\\u044f \\u0445\\u0440\\u0430\\u043d\\u0438\\u043b\\u0438\\u0449\\u0430\\u043c\\u0438 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445, \\u043f\\u043e\\u0438\\u0441\\u043a\\u043e\\u043c \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438... \\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435: https:\\/\\/www.labirint.ru\\/books\\/653486\\/<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 15:23:56\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:23:56\",\"modified_by\":null,\"checked_out\":null,\"checked_out_time\":null,\"publish_up\":\"2018-09-27 15:23:56\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big4.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big4.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":1,\"ordering\":null,\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":null,\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0);
INSERT INTO `orexv_ucm_history` (`version_id`, `ucm_item_id`, `ucm_type_id`, `version_note`, `save_date`, `editor_user_id`, `character_count`, `sha1_hash`, `version_data`, `keep_forever`) VALUES
(8, 4, 1, '', '2018-09-27 15:26:17', 15, 14393, '0fdace39b604e6a0a1eba4047071629be1912906', '{\"id\":4,\"asset_id\":73,\"title\":\"\\u0410\\u0434\\u0430\\u043c \\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434: \\u0420\\u0430\\u0434\\u0438\\u043a\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0438: \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u043e \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u043e\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438 \",\"alias\":\"adam-grinfild-radikalnye-tekhnologii-ustrojstvo-povsednevnoj-zhizni\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/203673\\/\\\">\\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434 \\u0410\\u0434\\u0430\\u043c<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/105260\\/\\\">\\u041a\\u0443\\u0448\\u043d\\u0430\\u0440\\u0435\\u0432\\u0430 \\u0418\\u043d\\u043d\\u0430<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/49760\\/\\\">\\u041f\\u043e\\u043f\\u043e\\u0432\\u0430 \\u0415. \\u0412.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/2362\\/\\\">\\u0414\\u0435\\u043b\\u043e<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2313\\/\\\">\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430<span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<\\/span><\\/a><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/651886\\/\\\"><br \\/><\\/a><\\/div>\\r\\n<div><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">480<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\\\"><\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\"><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0440\\u0443\\u0431.<\\/span><\\/span>\",\"fulltext\":\"<span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\"><\\/span><\\/div>\\r\\n<div>\\r\\n<h2 style=\\\"margin: 0px 0px 10px; padding: 0px; font: bold 1em Arial, Helvetica, sans-serif; color: #000000; display: inline; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #ffffff;\\\">\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0420\\u0430\\u0434\\u0438\\u043a\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0438: \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u043e \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u043e\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438\\\"<\\/h2>\\r\\n<div style=\\\"margin: 0px; padding: 0px; color: #333333; font-family: Arial, sans-serif; font-size: 12px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #ffffff;\\\">\\r\\n<div id=\\\"smallannotation\\\" style=\\\"margin: 0px; padding: 0px;\\\">\\r\\n<p style=\\\"margin: 0px 0px 1em; padding: 0px;\\\">\\u0412\\u0441\\u044e\\u0434\\u0443, \\u043a\\u0443\\u0434\\u0430 \\u0431\\u044b \\u043c\\u044b \\u043d\\u0438 \\u043e\\u0431\\u0440\\u0430\\u0442\\u0438\\u043b\\u0438 \\u0441\\u0432\\u043e\\u0439 \\u0432\\u0437\\u043e\\u0440, \\u0431\\u043b\\u0438\\u0441\\u0442\\u0430\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0435 \\u043d\\u043e\\u0432\\u044b\\u0435 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u0430 \\u043e\\u0431\\u0435\\u0449\\u0430\\u044e\\u0442 \\u043f\\u043e\\u043b\\u043d\\u043e\\u0441\\u0442\\u044c\\u044e \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0430\\u0437\\u0438\\u0442\\u044c \\u043d\\u0430\\u0448\\u0443 \\u0436\\u0438\\u0437\\u043d\\u044c. \\u041d\\u043e \\u043a\\u0430\\u043a\\u043e\\u0439 \\u0446\\u0435\\u043d\\u043e\\u0439? \\u0412 \\u044d\\u0442\\u043e\\u0439 \\u0441\\u0432\\u043e\\u0435\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0438 \\u043e\\u0442\\u043a\\u0440\\u043e\\u0432\\u0435\\u043d\\u043d\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u043e\\u0439 \\u043d\\u0430\\u0448\\u0435\\u0439 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u043e\\u043d\\u043d\\u043e\\u0439 \\u044d\\u043f\\u043e\\u0445\\u0435, \\u0432\\u0435\\u0434\\u0443\\u0449\\u0438\\u0439 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0439 \\u043c\\u044b\\u0441\\u043b\\u0438\\u0442\\u0435\\u043b\\u044c \\u0410\\u0434\\u0430\\u043c \\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434 \\u0437\\u0430\\u0441\\u0442\\u0430\\u0432\\u043b\\u044f\\u0435\\u0442 \\u043f\\u0435\\u0440\\u0435\\u0441\\u043c\\u043e\\u0442\\u0440\\u0435\\u0442\\u044c \\u043d\\u0430\\u0448\\u0438 \\u043e\\u0442\\u043d\\u043e\\u0448\\u0435\\u043d\\u0438\\u044f \\u0441 \\u0441\\u0435\\u0442\\u0435\\u0432\\u044b\\u043c\\u0438 \\u043e\\u0431\\u044a\\u0435\\u043a\\u0442\\u0430\\u043c\\u0438, \\u0441\\u0435\\u0440\\u0432\\u0438\\u0441\\u0430\\u043c\\u0438 \\u0438 \\u043f\\u0440\\u043e\\u0441\\u0442\\u0440\\u0430\\u043d\\u0441\\u0442\\u0432\\u0430\\u043c\\u0438. \\u041c\\u044b \\u0443\\u0436\\u0435 \\u0437\\u0430\\u0432\\u0438\\u0441\\u0438\\u043c \\u043e\\u0442 \\u0441\\u043c\\u0430\\u0440\\u0442\\u0444\\u043e\\u043d\\u0430 \\u043f\\u0440\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438 \\u0432\\u043e \\u0432\\u0441\\u0435\\u0445 \\u0430\\u0441\\u043f\\u0435\\u043a\\u0442\\u0430\\u0445 \\u0441\\u0432\\u043e\\u0435\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438. \\u041d\\u0430\\u043c \\u0433\\u043e\\u0432\\u043e\\u0440\\u044f\\u0442, \\u0447\\u0442\\u043e \\u0438\\u043d\\u043d\\u043e\\u0432\\u0430\\u0446\\u0438\\u0438 - \\u043e\\u0442 \\u0438\\u043d\\u0442\\u0435\\u0440\\u0444\\u0435\\u0439\\u0441\\u043e\\u0432 \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0440\\u0435\\u0430\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0438 \\u0438 \\u0432\\u0438\\u0440\\u0442\\u0443\\u0430\\u043b\\u044c\\u043d\\u044b\\u0445 \\u043f\\u043e\\u043c\\u043e\\u0449\\u043d\\u0438\\u043a\\u043e\\u0432 \\u0434\\u043e \\u0430\\u0432\\u0442\\u043e\\u043d\\u043e\\u043c\\u043d\\u044b\\u0445 \\u0434\\u0440\\u043e\\u043d\\u043e\\u0432-\\u0434\\u043e\\u0441\\u0442\\u0430\\u0432\\u0449\\u0438\\u043a\\u043e\\u0432 \\u0438 \\u0431\\u0435\\u0441\\u043f\\u0438\\u043b\\u043e\\u0442\\u043d\\u044b\\u0445 \\u0430\\u0432\\u0442\\u043e\\u043c\\u043e\\u0431\\u0438\\u043b\\u0435\\u0439 - \\u0443\\u043f\\u0440\\u043e\\u0441\\u0442\\u044f\\u0442 \\u0436\\u0438\\u0437\\u043d\\u044c, \\u0441\\u0434\\u0435\\u043b\\u0430\\u044e\\u0442 \\u0435\\u0435 \\u0431\\u043e\\u043b\\u0435\\u0435 \\u0443\\u0434\\u043e\\u0431\\u043d\\u043e\\u0439 \\u0438 \\u043f\\u0440\\u043e\\u0434\\u0443\\u043a\\u0442\\u0438\\u0432\\u043d\\u043e\\u0439. 3D-\\u043f\\u0435\\u0447\\u0430\\u0442\\u044c \\u0441\\u0443\\u043b\\u0438\\u0442 \\u0431\\u0435\\u0441\\u043f\\u0440\\u0435\\u0446\\u0435\\u0434\\u0435\\u043d\\u0442\\u043d\\u044b\\u0439 \\u043a\\u043e\\u043d\\u0442\\u0440\\u043e\\u043b\\u044c \\u043d\\u0430\\u0434 \\u0444\\u043e\\u0440\\u043c\\u043e\\u0439 \\u0438 \\u0440\\u0430\\u0441\\u043f\\u0440\\u0435\\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u0435\\u043c \\u043c\\u0430\\u0442\\u0435\\u0440\\u0438\\u0438, \\u0430 \\u0431\\u043b\\u043e\\u043a\\u0447\\u0435\\u0439\\u043d \\u043e\\u0431\\u0435\\u0449\\u0430\\u0435\\u0442 \\u043f\\u0440\\u043e\\u0438\\u0437\\u0432\\u0435\\u0441\\u0442\\u0438 \\u0440\\u0435\\u0432\\u043e\\u043b\\u044e\\u0446\\u0438\\u044e \\u0432\\u043e \\u0432\\u0441\\u0435\\u043c - \\u043e\\u0442 \\u0443\\u0447\\u0435\\u0442\\u0430 \\u0438 \\u043e\\u0431\\u043c\\u0435\\u043d\\u0430 \\u0446\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u044f\\u043c\\u0438 \\u0434\\u043e \\u0441\\u0430\\u043c\\u044b\\u0445 \\u043f\\u0440\\u043e\\u0437\\u0430\\u0438\\u0447\\u043d\\u044b\\u0445 \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u044b\\u0445 \\u0432\\u0435\\u0449\\u0435\\u0439. \\u0422\\u0435\\u043c \\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u0435\\u043c \\u043d\\u0435\\u0432\\u0435\\u0440\\u043e\\u044f\\u0442\\u043d\\u043e \\u0441\\u043b\\u043e\\u0436\\u043d\\u044b\\u0435 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0434\\u0435\\u0439\\u0441\\u0442\\u0432\\u0443\\u044e\\u0442 \\u043d\\u0435\\u0437\\u0430\\u043c\\u0435\\u0442\\u043d\\u043e, \\u043c\\u0435\\u043d\\u044f\\u044f \\u044d\\u043a\\u043e\\u043d\\u043e\\u043c\\u0438\\u043a\\u0443, \\u0442\\u0440\\u0430\\u043d\\u0441\\u0444\\u043e\\u0440\\u043c\\u0438\\u0440\\u0443\\u044f \\u0444\\u0443\\u043d\\u0434\\u0430\\u043c\\u0435\\u043d\\u0442\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0443\\u0441\\u043b\\u043e\\u0432\\u0438\\u044f \\u043f\\u043e\\u043b\\u0438\\u0442\\u0438\\u043a\\u0438 \\u0438 \\u0434\\u0430\\u0436\\u0435 \\u043f\\u0440\\u0435\\u0434\\u043b\\u0430\\u0433\\u0430\\u044f \\u043d\\u043e\\u0432\\u044b\\u0435...<span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #1868a0;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/651886\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/651886\\/<\\/a><\\/span><\\/p>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 15:26:17\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:26:17\",\"modified_by\":null,\"checked_out\":null,\"checked_out_time\":null,\"publish_up\":\"2018-09-27 15:26:17\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":1,\"ordering\":null,\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":null,\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(9, 3, 1, '', '2018-09-27 15:27:23', 15, 12943, '19321a4c32c8ed9081d30c5bd9d7160a5e778f27', '{\"id\":3,\"asset_id\":\"72\",\"title\":\"\\u0413\\u0443\\u043b\\u0430\\u043a\\u043e\\u0432, \\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432, \\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432: \\u0421\\u0442\\u0440\\u0443\\u043a\\u0442\\u0443\\u0440\\u044b \\u0438 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u044b \\u043e\\u0431\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0445 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445\",\"alias\":\"gulakov-trubakov-trubakov-struktury-i-algoritmy-obrabotki-mnogomernykh-dannykh\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/204282\\/\\\">\\u0413\\u0443\\u043b\\u0430\\u043a\\u043e\\u0432 \\u0412\\u0430\\u0441\\u0438\\u043b\\u0438\\u0439 \\u041a\\u043e\\u043d\\u0441\\u0442\\u0430\\u043d\\u0442\\u0438\\u043d\\u043e\\u0432\\u0438\\u0447<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/204283\\/\\\">\\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432 \\u0410\\u043d\\u0434\\u0440\\u0435\\u0439 \\u041e\\u043b\\u0435\\u0433\\u043e\\u0432\\u0438\\u0447<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/204284\\/\\\">\\u0422\\u0440\\u0443\\u0431\\u0430\\u043a\\u043e\\u0432 \\u0415\\u0432\\u0433\\u0435\\u043d\\u0438\\u0439 \\u041e\\u043b\\u0435\\u0433\\u043e\\u0432\\u0438\\u0447<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/69777\\/\\\">\\u041c\\u0430\\u043a\\u0430\\u0440\\u043e\\u0432 \\u0421. \\u0412.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/73\\/\\\">\\u041b\\u0430\\u043d\\u044c<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0421\\u0435\\u0440\\u0438\\u044f:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/30905\\/\\\">\\u0423\\u0447\\u0435\\u0431\\u043d\\u0438\\u043a\\u0438 \\u0434\\u043b\\u044f \\u0432\\u0443\\u0437\\u043e\\u0432. \\u0421\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u044c\\u043d\\u0430\\u044f \\u043b\\u0438\\u0442\\u0435\\u0440\\u0430\\u0442\\u0443\\u0440\\u0430<span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<\\/span><\\/a><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/653486\\/\\\"><br \\/><\\/a><\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">1981<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\\\">&nbsp;mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">.<\\/span><\\/p>\\r\\n\",\"fulltext\":\"\\r\\n<p>&nbsp;<\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0421\\u0442\\u0440\\u0443\\u043a\\u0442\\u0443\\u0440\\u044b \\u0438 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u044b \\u043e\\u0431\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0445 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445\\\" \\u041a\\u043d\\u0438\\u0433\\u0430 \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u0430 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u044e \\u0441\\u0442\\u0440\\u0443\\u043a\\u0442\\u0443\\u0440 \\u0438 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u043e\\u0432 \\u0434\\u043b\\u044f \\u0438\\u043d\\u0434\\u0435\\u043a\\u0441\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f \\u0438 \\u043e\\u0431\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0445 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445. \\u0412 \\u043d\\u0435\\u0439 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u0437\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u044b \\u043d\\u0430\\u0438\\u0431\\u043e\\u043b\\u0435\\u0435 \\u0432\\u0430\\u0436\\u043d\\u044b\\u0435 \\u043f\\u043e\\u0434\\u0445\\u043e\\u0434\\u044b, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u044b \\u0438\\u0445 \\u043c\\u0430\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0438 \\u043b\\u043e\\u0433\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u043f\\u0440\\u0438\\u043d\\u0446\\u0438\\u043f\\u044b \\u043f\\u043e\\u0441\\u0442\\u0440\\u043e\\u0435\\u043d\\u0438\\u044f, \\u043f\\u0440\\u043e\\u0430\\u043d\\u0430\\u043b\\u0438\\u0437\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u044b \\u0434\\u043e\\u0441\\u0442\\u043e\\u0438\\u043d\\u0441\\u0442\\u0432\\u0430 \\u0438 \\u043d\\u0435\\u0434\\u043e\\u0441\\u0442\\u0430\\u0442\\u043a\\u0438. \\u0421\\u043e\\u0434\\u0435\\u0440\\u0436\\u0438\\u0442\\u0441\\u044f \\u0431\\u043e\\u043b\\u044c\\u0448\\u043e\\u0435 \\u0447\\u0438\\u0441\\u043b\\u043e \\u043f\\u0440\\u0438\\u043c\\u0435\\u0440\\u043e\\u0432 \\u043b\\u0438\\u0441\\u0442\\u0438\\u043d\\u0433\\u0430, \\u043f\\u043e\\u0437\\u0432\\u043e\\u043b\\u044f\\u044e\\u0449\\u0438\\u0445 \\u0431\\u043e\\u043b\\u0435\\u0435 \\u0434\\u0435\\u0442\\u0430\\u043b\\u044c\\u043d\\u043e \\u0440\\u0430\\u0437\\u043e\\u0431\\u0440\\u0430\\u0442\\u044c\\u0441\\u044f \\u0432 \\u043f\\u0440\\u0435\\u0434\\u0441\\u0442\\u0430\\u0432\\u043b\\u0435\\u043d\\u043d\\u044b\\u0445 \\u043f\\u043e\\u0434\\u0445\\u043e\\u0434\\u0430\\u0445. \\u041d\\u0430 \\u0440\\u0430\\u0437\\u043b\\u0438\\u0447\\u043d\\u044b\\u0445 \\u043f\\u0440\\u0438\\u043c\\u0435\\u0440\\u0430\\u0445 \\u0440\\u0430\\u0441\\u0441\\u043c\\u0430\\u0442\\u0440\\u0438\\u0432\\u0430\\u044e\\u0442\\u0441\\u044f \\u043e\\u0441\\u043e\\u0431\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f \\u0438 \\u0440\\u0430\\u0437\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u0438 \\u043f\\u0440\\u0438\\u043b\\u043e\\u0436\\u0435\\u043d\\u0438\\u0439, \\u043e\\u0431\\u0440\\u0430\\u0431\\u0430\\u0442\\u044b\\u0432\\u0430\\u044e\\u0449\\u0438\\u0445 \\u043c\\u043d\\u043e\\u0433\\u043e\\u043c\\u0435\\u0440\\u043d\\u044b\\u0435 \\u0438 \\u043c\\u043d\\u043e\\u0433\\u043e\\u0430\\u0442\\u0440\\u0438\\u0431\\u0443\\u0442\\u043d\\u044b\\u0435 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0435. \\u041c\\u043e\\u043d\\u043e\\u0433\\u0440\\u0430\\u0444\\u0438\\u044f \\u043f\\u0440\\u0435\\u0434\\u043d\\u0430\\u0437\\u043d\\u0430\\u0447\\u0435\\u043d\\u0430 \\u0434\\u043b\\u044f \\u0431\\u0430\\u043a\\u0430\\u043b\\u0430\\u0432\\u0440\\u043e\\u0432 \\u0438 \\u043c\\u0430\\u0433\\u0438\\u0441\\u0442\\u0440\\u043e\\u0432, \\u043e\\u0431\\u0443\\u0447\\u0430\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u043f\\u043e \\u043d\\u0430\\u043f\\u0440\\u0430\\u0432\\u043b\\u0435\\u043d\\u0438\\u044f\\u043c \\u00ab\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430 \\u0438 \\u0432\\u044b\\u0447\\u0438\\u0441\\u043b\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u0430\\u044f \\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430\\u00bb, \\u00ab\\u041f\\u0440\\u043e\\u0433\\u0440\\u0430\\u043c\\u043c\\u043d\\u0430\\u044f \\u0438\\u043d\\u0436\\u0435\\u043d\\u0435\\u0440\\u0438\\u044f\\u00bb, \\u00ab\\u041c\\u0430\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0435 \\u043e\\u0431\\u0435\\u0441\\u043f\\u0435\\u0447\\u0435\\u043d\\u0438\\u0435 \\u0438 \\u0430\\u0434\\u043c\\u0438\\u043d\\u0438\\u0441\\u0442\\u0440\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u043e\\u043d\\u043d\\u044b\\u0445 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u00bb, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u043f\\u043e \\u0431\\u043b\\u0438\\u0437\\u043a\\u0438\\u043c \\u043d\\u0430\\u043f\\u0440\\u0430\\u0432\\u043b\\u0435\\u043d\\u0438\\u044f\\u043c. \\u0422\\u0430\\u043a\\u0436\\u0435 \\u043e\\u043d\\u0430 \\u0431\\u0443\\u0434\\u0435\\u0442 \\u043f\\u043e\\u043b\\u0435\\u0437\\u043d\\u0430 \\u043d\\u0430\\u0443\\u0447\\u043d\\u044b\\u043c \\u0440\\u0430\\u0431\\u043e\\u0442\\u043d\\u0438\\u043a\\u0430\\u043c, \\u043f\\u0440\\u0435\\u043f\\u043e\\u0434\\u0430\\u0432\\u0430\\u0442\\u0435\\u043b\\u044f\\u043c, \\u0441\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u0438\\u0441\\u0442\\u0430\\u043c, \\u0430\\u0441\\u043f\\u0438\\u0440\\u0430\\u043d\\u0442\\u0430\\u043c, \\u0441\\u0432\\u044f\\u0437\\u0430\\u043d\\u043d\\u044b\\u043c \\u0441 \\u043f\\u0440\\u0438\\u043a\\u043b\\u0430\\u0434\\u043d\\u043e\\u0439 \\u043c\\u0430\\u0442\\u0435\\u043c\\u0430\\u0442\\u0438\\u043a\\u043e\\u0439 \\u0438 \\u0440\\u0430\\u0437\\u0440\\u0430\\u0431\\u043e\\u0442\\u043a\\u043e\\u0439 \\u043f\\u0440\\u043e\\u0433\\u0440\\u0430\\u043c\\u043c\\u043d\\u043e\\u0433\\u043e \\u043e\\u0431\\u0435\\u0441\\u043f\\u0435\\u0447\\u0435\\u043d\\u0438\\u044f. \\u041c\\u043e\\u0436\\u043d\\u043e \\u0438\\u0441\\u043f\\u043e\\u043b\\u044c\\u0437\\u043e\\u0432\\u0430\\u0442\\u044c \\u0441\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u0438\\u0441\\u0442\\u0430\\u043c, \\u0437\\u0430\\u043d\\u0438\\u043c\\u0430\\u044e\\u0449\\u0438\\u043c\\u0441\\u044f \\u0445\\u0440\\u0430\\u043d\\u0438\\u043b\\u0438\\u0449\\u0430\\u043c\\u0438 \\u0434\\u0430\\u043d\\u043d\\u044b\\u0445, \\u043f\\u043e\\u0438\\u0441\\u043a\\u043e\\u043c \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438... \\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435: https:\\/\\/www.labirint.ru\\/books\\/653486\\/<\\/p>\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 15:23:56\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:27:23\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:27:12\",\"publish_up\":\"2018-09-27 15:23:56\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big4.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big4.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":2,\"ordering\":\"1\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(10, 2, 1, '', '2018-09-27 15:27:36', 15, 11557, '9fac858aedd7620602ebd91f1bad51c069ea2b03', '{\"id\":2,\"asset_id\":\"71\",\"title\":\"\\u0410\\u043b\\u0435\\u043a\\u0441\\u0430\\u043d\\u0434\\u0440 \\u0410\\u043b\\u0435\\u043a\\u0441\\u0435\\u0435\\u0432: \\u041a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0434\\u043b\\u044f \\u043a\\u0440\\u0438\\u043f\\u0442\\u043e\\u0433\\u0440\\u0430\\u0444\\u043e\\u0432. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \",\"alias\":\"aleksandr-alekseev-kursovoe-proektirovanie-dlya-kriptografov-uchebnoe-posobie\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/98473\\/\\\">\\u0410\\u043b\\u0435\\u043a\\u0441\\u0435\\u0435\\u0432 \\u0410\\u043b\\u0435\\u043a\\u0441\\u0430\\u043d\\u0434\\u0440 \\u041f\\u0435\\u0442\\u0440\\u043e\\u0432\\u0438\\u0447<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1418\\/\\\">\\u0421\\u043e\\u043b\\u043e\\u043d-\\u043f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0421\\u0435\\u0440\\u0438\\u044f:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/26335\\/\\\">\\u0411\\u0438\\u0431\\u043b\\u0438\\u043e\\u0442\\u0435\\u043a\\u0430 \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430<\\/a>\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\r\\n<div class=\\\"series\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\"><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/series\\/26335\\/\\\"><\\/a><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\">\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #1868a0;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/655859\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/655859\\/<\\/a><\\/span><\\/div>\\r\\n<\\/div>\\r\\n<\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">1272<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded; float: none;\\\"><\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">mdl.<\\/span><\\/p>\\r\\n\",\"fulltext\":\"\\r\\n<p>&nbsp;<\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u041a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0434\\u043b\\u044f \\u043a\\u0440\\u0438\\u043f\\u0442\\u043e\\u0433\\u0440\\u0430\\u0444\\u043e\\u0432. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435\\\" \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \\u0441\\u043e\\u0434\\u0435\\u0440\\u0436\\u0438\\u0442 \\u0437\\u0430\\u0434\\u0430\\u043d\\u0438\\u0435 \\u043d\\u0430 \\u043a\\u0443\\u0440\\u0441\\u043e\\u0432\\u043e\\u0435 \\u043f\\u0440\\u043e\\u0435\\u043a\\u0442\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435 \\u0438 \\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0443\\u043a\\u0430\\u0437\\u0430\\u043d\\u0438\\u044f \\u0434\\u043b\\u044f \\u0432\\u044b\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u044f \\u0437\\u0430\\u0434\\u0430\\u043d\\u0438\\u044f. \\u041e\\u043f\\u0438\\u0441\\u0430\\u043d\\u044b \\u043c\\u0435\\u0442\\u043e\\u0434\\u044b \\u0441\\u0436\\u0430\\u0442\\u0438\\u044f \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 (\\u0425\\u0430\\u0444\\u0444\\u043c\\u0430\\u043d\\u0430, RLE), \\u043f\\u043e\\u043c\\u0435\\u0445\\u043e\\u0443\\u0441\\u0442\\u043e\\u0439\\u0447\\u0438\\u0432\\u043e\\u0433\\u043e \\u043a\\u043e\\u0434\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f (\\u043a\\u043e\\u0434\\u044b \\u0425\\u044d\\u043c\\u043c\\u0438\\u043d\\u0433\\u0430 \\u0438 \\u0411\\u0427\\u0425), \\u0448\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f (\\u0430\\u0434\\u0434\\u0438\\u0442\\u0438\\u0432\\u043d\\u044b\\u0439 \\u0448\\u0438\\u0444\\u0440 \\u0438 \\u0448\\u0438\\u0444\\u0440 \\u0441 \\u0443\\u043f\\u0440\\u0430\\u0432\\u043b\\u044f\\u0435\\u043c\\u044b\\u043c\\u0438 \\u043e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f\\u043c\\u0438), \\u0441\\u0442\\u0435\\u0433\\u0430\\u043d\\u043e\\u0433\\u0440\\u0430\\u0444\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e \\u0441\\u043e\\u043a\\u0440\\u044b\\u0442\\u0438\\u044f \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 (\\u0441\\u043a\\u0440\\u044b\\u0442\\u0430\\u044f \\u043f\\u0435\\u0440\\u0435\\u0434\\u0430\\u0447\\u0430 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u0438 \\u0432 \\u0433\\u0440\\u0430\\u0444\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u043c \\u0444\\u0430\\u0439\\u043b\\u0435 \\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0430 BMP, \\u0432 \\u0437\\u0432\\u0443\\u043a\\u043e\\u0432\\u043e\\u043c \\u0444\\u0430\\u0439\\u043b\\u0435 \\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0430 WAV, \\u043d\\u0430 HTML-\\u0441\\u0442\\u0440\\u0430\\u043d\\u0438\\u0446\\u0430\\u0445), \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d \\u043f\\u043e\\u0440\\u044f\\u0434\\u043e\\u043a \\u043c\\u043e\\u0434\\u0435\\u043b\\u0438\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f \\u0446\\u0438\\u0444\\u0440\\u043e\\u0432\\u044b\\u0445 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432 (\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c \\u0448\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u043d\\u0438\\u044f, \\u0440\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430 \\u0441\\u0434\\u0432\\u0438\\u0433\\u0430 \\u0438 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u0430 \\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u044f \\u043f\\u043e\\u043b\\u0438\\u043d\\u043e\\u043c\\u043e\\u0432). \\u0414\\u0430\\u043d\\u043d\\u0430\\u044f \\u0440\\u0430\\u0431\\u043e\\u0442\\u0430 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0441\\u043e\\u0441\\u0442\\u0430\\u0432\\u043d\\u043e\\u0439 \\u0447\\u0430\\u0441\\u0442\\u044c\\u044e \\u0443\\u0447\\u0435\\u0431\\u043d\\u043e-\\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e \\u043a\\u043e\\u043c\\u043f\\u043b\\u0435\\u043a\\u0441\\u0430, \\u043f\\u043e\\u0434\\u0433\\u043e\\u0442\\u043e\\u0432\\u043b\\u0435\\u043d\\u043d\\u043e\\u0433\\u043e \\u0430\\u0432\\u0442\\u043e\\u0440\\u043e\\u043c. \\u041a\\u043e\\u043c\\u043f\\u043b\\u0435\\u043a\\u0441 \\u0432\\u043a\\u043b\\u044e\\u0447\\u0430\\u0435\\u0442 \\u0432 \\u0441\\u0435\\u0431\\u044f \\u043b\\u0435\\u043a\\u0446\\u0438\\u0438, \\u043c\\u0435\\u0442\\u043e\\u0434\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0443\\u043a\\u0430\\u0437\\u0430\\u043d\\u0438\\u044f \\u043d\\u0430 \\u0432\\u044b\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0438 \\u043b\\u0430\\u0431\\u043e\\u0440\\u0430\\u0442\\u043e\\u0440\\u043d\\u044b\\u0445 \\u0440\\u0430\\u0431\\u043e\\u0442 \\u0432 \\u0434\\u0432\\u0443\\u0445 \\u0441\\u0435\\u043c\\u0435\\u0441\\u0442\\u0440\\u0430\\u0445 \\u0438 \\u0441\\u0431\\u043e\\u0440\\u043d\\u0438\\u043a \\u0437\\u0430\\u0434\\u0430\\u0447 \\u0434\\u043b\\u044f \\u043f\\u0440\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0445 \\u0437\\u0430\\u043d\\u044f\\u0442\\u0438\\u0439. \\u0423\\u0447\\u0435\\u0431\\u043d\\u043e\\u0435 \\u043f\\u043e\\u0441\\u043e\\u0431\\u0438\\u0435 \\u043f\\u043e \\u0434\\u0438\\u0441\\u0446\\u0438\\u043f\\u043b\\u0438\\u043d\\u0435 \\\"\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430\\\" \\u0434\\u043b\\u044f \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u043e\\u0432 \\u0441\\u043f\\u0435\\u0446\\u0438\\u0430\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0435\\u0439 10.03.01 \\u0438 10.05.02&nbsp;<\\/p>\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:50:15\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:27:36\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:27:26\",\"publish_up\":\"2018-09-27 14:50:15\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big2.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big2.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":3,\"ordering\":\"2\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"4\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0);
INSERT INTO `orexv_ucm_history` (`version_id`, `ucm_item_id`, `ucm_type_id`, `version_note`, `save_date`, `editor_user_id`, `character_count`, `sha1_hash`, `version_data`, `keep_forever`) VALUES
(11, 1, 1, '', '2018-09-27 15:28:06', 15, 9533, 'd65235d0a2f309b21a27bbd92f3d798c09dd9277', '{\"id\":1,\"asset_id\":\"69\",\"title\":\"\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441, \\u0425\\u0430\\u0440\\u0440\\u0438\\u0441: \\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM \",\"alias\":\"kharris-kharris-tsifrovaya-skhemotekhnika-i-arkhitektura-kompyutera-dopolnenie-po-arkhitekture-arm\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187439\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0414\\u044d\\u0432\\u0438\\u0434 \\u041c.<\\/a>,&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/187440\\/\\\">\\u0425\\u0430\\u0440\\u0440\\u0438\\u0441 \\u0421\\u0430\\u0440\\u0430 \\u041b.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/63408\\/\\\">\\u0421\\u043b\\u0438\\u043d\\u043a\\u0438\\u043d \\u0410. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/205256\\/\\\">\\u041a\\u043e\\u0441\\u043e\\u043b\\u043e\\u0431\\u043e\\u0432 \\u0414. \\u0410.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/1416\\/\\\">\\u0414\\u041c\\u041a-\\u041f\\u0440\\u0435\\u0441\\u0441<\\/a>, 2019 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2575\\/\\\">\\u0416\\u0435\\u043b\\u0435\\u0437\\u043e \\u041f\\u041a<\\/a>&nbsp;\\u0438 \\u0434\\u0440.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\">&nbsp;<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; background-color: #f0eded;\\\"><span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0; opacity: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/662585\\/\\\">https:\\/\\/www.labirint.ru\\/books\\/662585\\/<\\/a><\\/span><\\/div>\\r\\n<p><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">342<\\/span><span style=\\\"color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; background-color: #f0eded;\\\">&nbsp;mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; background-color: #f0eded;\\\">.<\\/span><\\/p>\\r\\n\",\"fulltext\":\"\\r\\n<p>&nbsp;<\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430. \\u0414\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435 \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 ARM\\\" \\u0414\\u0430\\u043d\\u043d\\u043e\\u0435 \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u0438\\u0435\\u043c \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0426\\u0438\\u0444\\u0440\\u043e\\u0432\\u0430\\u044f \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u0430 \\u0438 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0430 \\u043a\\u043e\\u043c\\u043f\\u044c\\u044e\\u0442\\u0435\\u0440\\u0430\\\" \\u0441 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0435\\u043c \\u043e\\u0442\\u043b\\u0438\\u0447\\u0438\\u0439 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u044b ARM \\u043e\\u0442 MIPS, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u043d\\u043e\\u0439 \\u0432 \\u043f\\u0435\\u0440\\u0432\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435. \\u041e\\u043d\\u043e \\u0441\\u043e\\u0441\\u0442\\u043e\\u0438\\u0442 \\u0438\\u0437 \\u0433\\u043b\\u0430\\u0432, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u044b\\u0445 \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 \\u043f\\u0440\\u043e\\u0446\\u0435\\u0441\\u0441\\u043e\\u0440\\u043e\\u0432 ARM, \\u0438\\u0445 \\u043c\\u0438\\u043a\\u0440\\u043e\\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435, \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u044e \\u043f\\u043e\\u0434\\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u043f\\u0430\\u043c\\u044f\\u0442\\u0438 \\u0438 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0432\\u0432\\u043e\\u0434\\u0430-\\u0432\\u044b\\u0432\\u043e\\u0434\\u0430. \\u0422\\u0430\\u043a\\u0436\\u0435 \\u0432 \\u043f\\u0440\\u0438\\u043b\\u043e\\u0436\\u0435\\u043d\\u0438\\u0438 \\u043f\\u0440\\u0438\\u0432\\u0435\\u0434\\u0435\\u043d\\u0430 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u0430 \\u043a\\u043e\\u043c\\u0430\\u043d\\u0434 ARM. \\u041a\\u043d\\u0438\\u0433\\u0443 \\u0440\\u0435\\u043a\\u043e\\u043c\\u0435\\u043d\\u0434\\u0443\\u0435\\u0442\\u0441\\u044f \\u0438\\u0441\\u043f\\u043e\\u043b\\u044c\\u0437\\u043e\\u0432\\u0430\\u0442\\u044c \\u0441\\u043e\\u0432\\u043c\\u0435\\u0441\\u0442\\u043d\\u043e \\u0441 \\u043f\\u0435\\u0440\\u0432\\u044b\\u043c (\\u043e\\u0441\\u043d\\u043e\\u0432\\u043d\\u044b\\u043c) \\u0438\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435\\u043c \\u043f\\u043e \\u0430\\u0440\\u0445\\u0438\\u0442\\u0435\\u043a\\u0442\\u0443\\u0440\\u0435 MIPS. \\u0418\\u0437\\u0434\\u0430\\u043d\\u0438\\u0435 \\u0431\\u0443\\u0434\\u0435\\u0442 \\u043f\\u043e\\u043b\\u0435\\u0437\\u043d\\u043e \\u0441\\u0442\\u0443\\u0434\\u0435\\u043d\\u0442\\u0430\\u043c, \\u0438\\u043d\\u0436\\u0435\\u043d\\u0435\\u0440\\u0430\\u043c, \\u0430 \\u0442\\u0430\\u043a\\u0436\\u0435 \\u0448\\u0438\\u0440\\u043e\\u043a\\u043e\\u043c\\u0443 \\u043a\\u0440\\u0443\\u0433\\u0443 \\u0447\\u0438\\u0442\\u0430\\u0442\\u0435\\u043b\\u0435\\u0439, \\u0438\\u043d\\u0442\\u0435\\u0440\\u0435\\u0441\\u0443\\u044e\\u0449\\u0438\\u0445\\u0441\\u044f \\u0441\\u043e\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0441\\u0445\\u0435\\u043c\\u043e\\u0442\\u0435\\u0445\\u043d\\u0438\\u043a\\u043e\\u0439.&nbsp;<\\/p>\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 14:44:36\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:28:06\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:27:57\",\"publish_up\":\"2018-09-27 14:44:36\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big1.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big1.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":5,\"ordering\":\"3\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"7\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(12, 4, 1, '', '2018-09-27 15:35:11', 15, 2164, '05001904dfbb18b587459541e3a060eebbc079d0', '{\"id\":4,\"asset_id\":\"73\",\"title\":\"\\u0410\\u0434\\u0430\\u043c \\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434: \\u0420\\u0430\\u0434\\u0438\\u043a\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0438: \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u043e \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u043e\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438 \",\"alias\":\"adam-grinfild-radikalnye-tekhnologii-ustrojstvo-povsednevnoj-zhizni\",\"introtext\":\"<p>\\u044b<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 15:26:17\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:35:11\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:34:30\",\"publish_up\":\"2018-09-27 15:26:17\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":4,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(13, 4, 1, '', '2018-09-27 15:36:21', 15, 12163, '284ea2dd34fc0112c7b54b58a47a834113994479', '{\"id\":4,\"asset_id\":\"73\",\"title\":\"\\u0410\\u0434\\u0430\\u043c \\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434: \\u0420\\u0430\\u0434\\u0438\\u043a\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0438: \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u043e \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u043e\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438 \",\"alias\":\"adam-grinfild-radikalnye-tekhnologii-ustrojstvo-povsednevnoj-zhizni\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/203673\\/\\\">\\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434 \\u0410\\u0434\\u0430\\u043c<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/105260\\/\\\">\\u041a\\u0443\\u0448\\u043d\\u0430\\u0440\\u0435\\u0432\\u0430 \\u0418\\u043d\\u043d\\u0430<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/49760\\/\\\">\\u041f\\u043e\\u043f\\u043e\\u0432\\u0430 \\u0415. \\u0412.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/2362\\/\\\">\\u0414\\u0435\\u043b\\u043e<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2313\\/\\\">\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430<span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<\\/span><\\/a><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/651886\\/\\\"><br \\/><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">480 mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">.<\\/span><br \\/><\\/a><\\/div>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0420\\u0430\\u0434\\u0438\\u043a\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0438: \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u043e \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u043e\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438\\\" \\u0412\\u0441\\u044e\\u0434\\u0443, \\u043a\\u0443\\u0434\\u0430 \\u0431\\u044b \\u043c\\u044b \\u043d\\u0438 \\u043e\\u0431\\u0440\\u0430\\u0442\\u0438\\u043b\\u0438 \\u0441\\u0432\\u043e\\u0439 \\u0432\\u0437\\u043e\\u0440, \\u0431\\u043b\\u0438\\u0441\\u0442\\u0430\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0435 \\u043d\\u043e\\u0432\\u044b\\u0435 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u0430 \\u043e\\u0431\\u0435\\u0449\\u0430\\u044e\\u0442 \\u043f\\u043e\\u043b\\u043d\\u043e\\u0441\\u0442\\u044c\\u044e \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0430\\u0437\\u0438\\u0442\\u044c \\u043d\\u0430\\u0448\\u0443 \\u0436\\u0438\\u0437\\u043d\\u044c. \\u041d\\u043e \\u043a\\u0430\\u043a\\u043e\\u0439 \\u0446\\u0435\\u043d\\u043e\\u0439? \\u0412 \\u044d\\u0442\\u043e\\u0439 \\u0441\\u0432\\u043e\\u0435\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0438 \\u043e\\u0442\\u043a\\u0440\\u043e\\u0432\\u0435\\u043d\\u043d\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u043e\\u0439 \\u043d\\u0430\\u0448\\u0435\\u0439 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u043e\\u043d\\u043d\\u043e\\u0439 \\u044d\\u043f\\u043e\\u0445\\u0435, \\u0432\\u0435\\u0434\\u0443\\u0449\\u0438\\u0439 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0439 \\u043c\\u044b\\u0441\\u043b\\u0438\\u0442\\u0435\\u043b\\u044c \\u0410\\u0434\\u0430\\u043c \\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434 \\u0437\\u0430\\u0441\\u0442\\u0430\\u0432\\u043b\\u044f\\u0435\\u0442 \\u043f\\u0435\\u0440\\u0435\\u0441\\u043c\\u043e\\u0442\\u0440\\u0435\\u0442\\u044c \\u043d\\u0430\\u0448\\u0438 \\u043e\\u0442\\u043d\\u043e\\u0448\\u0435\\u043d\\u0438\\u044f \\u0441 \\u0441\\u0435\\u0442\\u0435\\u0432\\u044b\\u043c\\u0438 \\u043e\\u0431\\u044a\\u0435\\u043a\\u0442\\u0430\\u043c\\u0438, \\u0441\\u0435\\u0440\\u0432\\u0438\\u0441\\u0430\\u043c\\u0438 \\u0438 \\u043f\\u0440\\u043e\\u0441\\u0442\\u0440\\u0430\\u043d\\u0441\\u0442\\u0432\\u0430\\u043c\\u0438. \\u041c\\u044b \\u0443\\u0436\\u0435 \\u0437\\u0430\\u0432\\u0438\\u0441\\u0438\\u043c \\u043e\\u0442 \\u0441\\u043c\\u0430\\u0440\\u0442\\u0444\\u043e\\u043d\\u0430 \\u043f\\u0440\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438 \\u0432\\u043e \\u0432\\u0441\\u0435\\u0445 \\u0430\\u0441\\u043f\\u0435\\u043a\\u0442\\u0430\\u0445 \\u0441\\u0432\\u043e\\u0435\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438. \\u041d\\u0430\\u043c \\u0433\\u043e\\u0432\\u043e\\u0440\\u044f\\u0442, \\u0447\\u0442\\u043e \\u0438\\u043d\\u043d\\u043e\\u0432\\u0430\\u0446\\u0438\\u0438 - \\u043e\\u0442 \\u0438\\u043d\\u0442\\u0435\\u0440\\u0444\\u0435\\u0439\\u0441\\u043e\\u0432 \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0440\\u0435\\u0430\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0438 \\u0438 \\u0432\\u0438\\u0440\\u0442\\u0443\\u0430\\u043b\\u044c\\u043d\\u044b\\u0445 \\u043f\\u043e\\u043c\\u043e\\u0449\\u043d\\u0438\\u043a\\u043e\\u0432 \\u0434\\u043e \\u0430\\u0432\\u0442\\u043e\\u043d\\u043e\\u043c\\u043d\\u044b\\u0445 \\u0434\\u0440\\u043e\\u043d\\u043e\\u0432-\\u0434\\u043e\\u0441\\u0442\\u0430\\u0432\\u0449\\u0438\\u043a\\u043e\\u0432 \\u0438 \\u0431\\u0435\\u0441\\u043f\\u0438\\u043b\\u043e\\u0442\\u043d\\u044b\\u0445 \\u0430\\u0432\\u0442\\u043e\\u043c\\u043e\\u0431\\u0438\\u043b\\u0435\\u0439 - \\u0443\\u043f\\u0440\\u043e\\u0441\\u0442\\u044f\\u0442 \\u0436\\u0438\\u0437\\u043d\\u044c, \\u0441\\u0434\\u0435\\u043b\\u0430\\u044e\\u0442 \\u0435\\u0435 \\u0431\\u043e\\u043b\\u0435\\u0435 \\u0443\\u0434\\u043e\\u0431\\u043d\\u043e\\u0439 \\u0438 \\u043f\\u0440\\u043e\\u0434\\u0443\\u043a\\u0442\\u0438\\u0432\\u043d\\u043e\\u0439. 3D-\\u043f\\u0435\\u0447\\u0430\\u0442\\u044c \\u0441\\u0443\\u043b\\u0438\\u0442 \\u0431\\u0435\\u0441\\u043f\\u0440\\u0435\\u0446\\u0435\\u0434\\u0435\\u043d\\u0442\\u043d\\u044b\\u0439 \\u043a\\u043e\\u043d\\u0442\\u0440\\u043e\\u043b\\u044c \\u043d\\u0430\\u0434 \\u0444\\u043e\\u0440\\u043c\\u043e\\u0439 \\u0438 \\u0440\\u0430\\u0441\\u043f\\u0440\\u0435\\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u0435\\u043c \\u043c\\u0430\\u0442\\u0435\\u0440\\u0438\\u0438, \\u0430 \\u0431\\u043b\\u043e\\u043a\\u0447\\u0435\\u0439\\u043d \\u043e\\u0431\\u0435\\u0449\\u0430\\u0435\\u0442 \\u043f\\u0440\\u043e\\u0438\\u0437\\u0432\\u0435\\u0441\\u0442\\u0438 \\u0440\\u0435\\u0432\\u043e\\u043b\\u044e\\u0446\\u0438\\u044e \\u0432\\u043e \\u0432\\u0441\\u0435\\u043c - \\u043e\\u0442 \\u0443\\u0447\\u0435\\u0442\\u0430 \\u0438 \\u043e\\u0431\\u043c\\u0435\\u043d\\u0430 \\u0446\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u044f\\u043c\\u0438 \\u0434\\u043e \\u0441\\u0430\\u043c\\u044b\\u0445 \\u043f\\u0440\\u043e\\u0437\\u0430\\u0438\\u0447\\u043d\\u044b\\u0445 \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u044b\\u0445 \\u0432\\u0435\\u0449\\u0435\\u0439. \\u0422\\u0435\\u043c \\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u0435\\u043c \\u043d\\u0435\\u0432\\u0435\\u0440\\u043e\\u044f\\u0442\\u043d\\u043e \\u0441\\u043b\\u043e\\u0436\\u043d\\u044b\\u0435 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0434\\u0435\\u0439\\u0441\\u0442\\u0432\\u0443\\u044e\\u0442 \\u043d\\u0435\\u0437\\u0430\\u043c\\u0435\\u0442\\u043d\\u043e, \\u043c\\u0435\\u043d\\u044f\\u044f \\u044d\\u043a\\u043e\\u043d\\u043e\\u043c\\u0438\\u043a\\u0443, \\u0442\\u0440\\u0430\\u043d\\u0441\\u0444\\u043e\\u0440\\u043c\\u0438\\u0440\\u0443\\u044f \\u0444\\u0443\\u043d\\u0434\\u0430\\u043c\\u0435\\u043d\\u0442\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0443\\u0441\\u043b\\u043e\\u0432\\u0438\\u044f \\u043f\\u043e\\u043b\\u0438\\u0442\\u0438\\u043a\\u0438 \\u0438 \\u0434\\u0430\\u0436\\u0435 \\u043f\\u0440\\u0435\\u0434\\u043b\\u0430\\u0433\\u0430\\u044f \\u043d\\u043e\\u0432\\u044b\\u0435... \\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435: https:\\/\\/www.labirint.ru\\/books\\/651886\\/<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 15:26:17\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:36:21\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:35:11\",\"publish_up\":\"2018-09-27 15:26:17\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":5,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(14, 4, 1, '', '2018-09-27 15:36:44', 15, 12185, '53184a0cdcc5c010e9be78b1c7b0ac7c9dcfe5bf', '{\"id\":4,\"asset_id\":\"73\",\"title\":\"\\u0410\\u0434\\u0430\\u043c \\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434: \\u0420\\u0430\\u0434\\u0438\\u043a\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0438: \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u043e \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u043e\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438 \",\"alias\":\"adam-grinfild-radikalnye-tekhnologii-ustrojstvo-povsednevnoj-zhizni\",\"introtext\":\"<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0410\\u0432\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/203673\\/\\\">\\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434 \\u0410\\u0434\\u0430\\u043c<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u041f\\u0435\\u0440\\u0435\\u0432\\u043e\\u0434\\u0447\\u0438\\u043a:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/105260\\/\\\">\\u041a\\u0443\\u0448\\u043d\\u0430\\u0440\\u0435\\u0432\\u0430 \\u0418\\u043d\\u043d\\u0430<\\/a><\\/div>\\r\\n<div class=\\\"authors\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0420\\u0435\\u0434\\u0430\\u043a\\u0442\\u043e\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/authors\\/49760\\/\\\">\\u041f\\u043e\\u043f\\u043e\\u0432\\u0430 \\u0415. \\u0412.<\\/a><\\/div>\\r\\n<div class=\\\"publisher\\\" style=\\\"margin: 0.3em 0px 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0418\\u0437\\u0434\\u0430\\u0442\\u0435\\u043b\\u044c\\u0441\\u0442\\u0432\\u043e:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/pubhouse\\/2362\\/\\\">\\u0414\\u0435\\u043b\\u043e<\\/a>, 2018 \\u0433.<\\/div>\\r\\n<div class=\\\"genre\\\" style=\\\"margin: 0px; padding: 0px; color: #000000; font-family: Tahoma, Helvetica, sans-serif; font-size: 11px; font-style: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">\\u0416\\u0430\\u043d\\u0440:&nbsp;<a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/genres\\/2313\\/\\\">\\u0418\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0442\\u0438\\u043a\\u0430<span id=\\\"ctrlcopy\\\" style=\\\"margin: 5px 0px 0px -1px; padding: 0px; height: 1px; overflow: hidden; position: absolute; width: 1px; line-height: 0;\\\"><br style=\\\"margin: 0px; padding: 0px;\\\" \\/>\\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435:&nbsp;<\\/span><\\/a><a style=\\\"margin: 0px; padding: 0px; background-color: transparent; color: #2f2f2f;\\\" href=\\\"https:\\/\\/www.labirint.ru\\/books\\/651886\\/\\\"><br \\/><span class=\\\"buying-pricenew-val-number\\\" style=\\\"margin: 0px; padding: 0px; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-style: normal; font-weight: bold; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">480 mdl<\\/span><span class=\\\"buying-pricenew-val-currency\\\" style=\\\"margin: 0px; padding: 0px; font-size: 14px; font-weight: normal; color: #1868a0; font-family: Arial, Helvetica, sans-serif; font-style: normal; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; background-color: #f0eded;\\\">.<\\/span><br \\/><\\/a><\\/div>\\r\\n\",\"fulltext\":\"\\r\\n<p>&nbsp;<\\/p>\\r\\n<p>\\u0410\\u043d\\u043d\\u043e\\u0442\\u0430\\u0446\\u0438\\u044f \\u043a \\u043a\\u043d\\u0438\\u0433\\u0435 \\\"\\u0420\\u0430\\u0434\\u0438\\u043a\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0438: \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u043e \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u043e\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438\\\" \\u0412\\u0441\\u044e\\u0434\\u0443, \\u043a\\u0443\\u0434\\u0430 \\u0431\\u044b \\u043c\\u044b \\u043d\\u0438 \\u043e\\u0431\\u0440\\u0430\\u0442\\u0438\\u043b\\u0438 \\u0441\\u0432\\u043e\\u0439 \\u0432\\u0437\\u043e\\u0440, \\u0431\\u043b\\u0438\\u0441\\u0442\\u0430\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0435 \\u043d\\u043e\\u0432\\u044b\\u0435 \\u0443\\u0441\\u0442\\u0440\\u043e\\u0439\\u0441\\u0442\\u0432\\u0430 \\u043e\\u0431\\u0435\\u0449\\u0430\\u044e\\u0442 \\u043f\\u043e\\u043b\\u043d\\u043e\\u0441\\u0442\\u044c\\u044e \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0430\\u0437\\u0438\\u0442\\u044c \\u043d\\u0430\\u0448\\u0443 \\u0436\\u0438\\u0437\\u043d\\u044c. \\u041d\\u043e \\u043a\\u0430\\u043a\\u043e\\u0439 \\u0446\\u0435\\u043d\\u043e\\u0439? \\u0412 \\u044d\\u0442\\u043e\\u0439 \\u0441\\u0432\\u043e\\u0435\\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0438 \\u043e\\u0442\\u043a\\u0440\\u043e\\u0432\\u0435\\u043d\\u043d\\u043e\\u0439 \\u043a\\u043d\\u0438\\u0433\\u0435, \\u043f\\u043e\\u0441\\u0432\\u044f\\u0449\\u0435\\u043d\\u043d\\u043e\\u0439 \\u043d\\u0430\\u0448\\u0435\\u0439 \\u0438\\u043d\\u0444\\u043e\\u0440\\u043c\\u0430\\u0446\\u0438\\u043e\\u043d\\u043d\\u043e\\u0439 \\u044d\\u043f\\u043e\\u0445\\u0435, \\u0432\\u0435\\u0434\\u0443\\u0449\\u0438\\u0439 \\u0442\\u0435\\u0445\\u043d\\u043e\\u043b\\u043e\\u0433\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0439 \\u043c\\u044b\\u0441\\u043b\\u0438\\u0442\\u0435\\u043b\\u044c \\u0410\\u0434\\u0430\\u043c \\u0413\\u0440\\u0438\\u043d\\u0444\\u0438\\u043b\\u0434 \\u0437\\u0430\\u0441\\u0442\\u0430\\u0432\\u043b\\u044f\\u0435\\u0442 \\u043f\\u0435\\u0440\\u0435\\u0441\\u043c\\u043e\\u0442\\u0440\\u0435\\u0442\\u044c \\u043d\\u0430\\u0448\\u0438 \\u043e\\u0442\\u043d\\u043e\\u0448\\u0435\\u043d\\u0438\\u044f \\u0441 \\u0441\\u0435\\u0442\\u0435\\u0432\\u044b\\u043c\\u0438 \\u043e\\u0431\\u044a\\u0435\\u043a\\u0442\\u0430\\u043c\\u0438, \\u0441\\u0435\\u0440\\u0432\\u0438\\u0441\\u0430\\u043c\\u0438 \\u0438 \\u043f\\u0440\\u043e\\u0441\\u0442\\u0440\\u0430\\u043d\\u0441\\u0442\\u0432\\u0430\\u043c\\u0438. \\u041c\\u044b \\u0443\\u0436\\u0435 \\u0437\\u0430\\u0432\\u0438\\u0441\\u0438\\u043c \\u043e\\u0442 \\u0441\\u043c\\u0430\\u0440\\u0442\\u0444\\u043e\\u043d\\u0430 \\u043f\\u0440\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438 \\u0432\\u043e \\u0432\\u0441\\u0435\\u0445 \\u0430\\u0441\\u043f\\u0435\\u043a\\u0442\\u0430\\u0445 \\u0441\\u0432\\u043e\\u0435\\u0439 \\u0436\\u0438\\u0437\\u043d\\u0438. \\u041d\\u0430\\u043c \\u0433\\u043e\\u0432\\u043e\\u0440\\u044f\\u0442, \\u0447\\u0442\\u043e \\u0438\\u043d\\u043d\\u043e\\u0432\\u0430\\u0446\\u0438\\u0438 - \\u043e\\u0442 \\u0438\\u043d\\u0442\\u0435\\u0440\\u0444\\u0435\\u0439\\u0441\\u043e\\u0432 \\u0434\\u043e\\u043f\\u043e\\u043b\\u043d\\u0435\\u043d\\u043d\\u043e\\u0439 \\u0440\\u0435\\u0430\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0438 \\u0438 \\u0432\\u0438\\u0440\\u0442\\u0443\\u0430\\u043b\\u044c\\u043d\\u044b\\u0445 \\u043f\\u043e\\u043c\\u043e\\u0449\\u043d\\u0438\\u043a\\u043e\\u0432 \\u0434\\u043e \\u0430\\u0432\\u0442\\u043e\\u043d\\u043e\\u043c\\u043d\\u044b\\u0445 \\u0434\\u0440\\u043e\\u043d\\u043e\\u0432-\\u0434\\u043e\\u0441\\u0442\\u0430\\u0432\\u0449\\u0438\\u043a\\u043e\\u0432 \\u0438 \\u0431\\u0435\\u0441\\u043f\\u0438\\u043b\\u043e\\u0442\\u043d\\u044b\\u0445 \\u0430\\u0432\\u0442\\u043e\\u043c\\u043e\\u0431\\u0438\\u043b\\u0435\\u0439 - \\u0443\\u043f\\u0440\\u043e\\u0441\\u0442\\u044f\\u0442 \\u0436\\u0438\\u0437\\u043d\\u044c, \\u0441\\u0434\\u0435\\u043b\\u0430\\u044e\\u0442 \\u0435\\u0435 \\u0431\\u043e\\u043b\\u0435\\u0435 \\u0443\\u0434\\u043e\\u0431\\u043d\\u043e\\u0439 \\u0438 \\u043f\\u0440\\u043e\\u0434\\u0443\\u043a\\u0442\\u0438\\u0432\\u043d\\u043e\\u0439. 3D-\\u043f\\u0435\\u0447\\u0430\\u0442\\u044c \\u0441\\u0443\\u043b\\u0438\\u0442 \\u0431\\u0435\\u0441\\u043f\\u0440\\u0435\\u0446\\u0435\\u0434\\u0435\\u043d\\u0442\\u043d\\u044b\\u0439 \\u043a\\u043e\\u043d\\u0442\\u0440\\u043e\\u043b\\u044c \\u043d\\u0430\\u0434 \\u0444\\u043e\\u0440\\u043c\\u043e\\u0439 \\u0438 \\u0440\\u0430\\u0441\\u043f\\u0440\\u0435\\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u0435\\u043c \\u043c\\u0430\\u0442\\u0435\\u0440\\u0438\\u0438, \\u0430 \\u0431\\u043b\\u043e\\u043a\\u0447\\u0435\\u0439\\u043d \\u043e\\u0431\\u0435\\u0449\\u0430\\u0435\\u0442 \\u043f\\u0440\\u043e\\u0438\\u0437\\u0432\\u0435\\u0441\\u0442\\u0438 \\u0440\\u0435\\u0432\\u043e\\u043b\\u044e\\u0446\\u0438\\u044e \\u0432\\u043e \\u0432\\u0441\\u0435\\u043c - \\u043e\\u0442 \\u0443\\u0447\\u0435\\u0442\\u0430 \\u0438 \\u043e\\u0431\\u043c\\u0435\\u043d\\u0430 \\u0446\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u044f\\u043c\\u0438 \\u0434\\u043e \\u0441\\u0430\\u043c\\u044b\\u0445 \\u043f\\u0440\\u043e\\u0437\\u0430\\u0438\\u0447\\u043d\\u044b\\u0445 \\u043f\\u043e\\u0432\\u0441\\u0435\\u0434\\u043d\\u0435\\u0432\\u043d\\u044b\\u0445 \\u0432\\u0435\\u0449\\u0435\\u0439. \\u0422\\u0435\\u043c \\u0432\\u0440\\u0435\\u043c\\u0435\\u043d\\u0435\\u043c \\u043d\\u0435\\u0432\\u0435\\u0440\\u043e\\u044f\\u0442\\u043d\\u043e \\u0441\\u043b\\u043e\\u0436\\u043d\\u044b\\u0435 \\u0430\\u043b\\u0433\\u043e\\u0440\\u0438\\u0442\\u043c\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0435 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b \\u0434\\u0435\\u0439\\u0441\\u0442\\u0432\\u0443\\u044e\\u0442 \\u043d\\u0435\\u0437\\u0430\\u043c\\u0435\\u0442\\u043d\\u043e, \\u043c\\u0435\\u043d\\u044f\\u044f \\u044d\\u043a\\u043e\\u043d\\u043e\\u043c\\u0438\\u043a\\u0443, \\u0442\\u0440\\u0430\\u043d\\u0441\\u0444\\u043e\\u0440\\u043c\\u0438\\u0440\\u0443\\u044f \\u0444\\u0443\\u043d\\u0434\\u0430\\u043c\\u0435\\u043d\\u0442\\u0430\\u043b\\u044c\\u043d\\u044b\\u0435 \\u0443\\u0441\\u043b\\u043e\\u0432\\u0438\\u044f \\u043f\\u043e\\u043b\\u0438\\u0442\\u0438\\u043a\\u0438 \\u0438 \\u0434\\u0430\\u0436\\u0435 \\u043f\\u0440\\u0435\\u0434\\u043b\\u0430\\u0433\\u0430\\u044f \\u043d\\u043e\\u0432\\u044b\\u0435... \\u041f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u0435\\u0435: https:\\/\\/www.labirint.ru\\/books\\/651886\\/<\\/p>\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 15:26:17\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:36:44\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:36:21\",\"publish_up\":\"2018-09-27 15:26:17\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"images\\\\\\/catalog\\\\\\/big3.jpg\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":6,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(15, 8, 5, '', '2018-09-27 15:40:28', 15, 1499, 'df85998e2507079b476c81878f926cd15c5c6823', '{\"id\":8,\"asset_id\":74,\"parent_id\":\"1\",\"lft\":\"11\",\"rgt\":12,\"level\":1,\"path\":null,\"extension\":\"com_content\",\"title\":\"\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u044b\",\"alias\":\"kontakty\",\"note\":\"\",\"description\":\"<p><strong>\\u041d\\u0430\\u0439\\u0442\\u0438 \\u043d\\u0430\\u0441 \\u043c\\u043e\\u0436\\u0435\\u0442\\u0435 \\u043f\\u043e \\u0430\\u0434\\u0440\\u0435\\u0441\\u0443:<\\/strong><\\/p>\\r\\n<p>\\u041a\\u0438\\u0448\\u0438\\u043d\\u0435\\u0432 60\\/2 \\u041a\\u043e\\u0440\\u043f.4&nbsp;<\\/p>\\r\\n<p>\\u041d\\u043e\\u043c\\u0435\\u0440 \\u0442\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u0430: 0694447712<br \\/>\\u0424\\u0430\\u043a\\u0441: 022 962 25 25&nbsp;<\\/p>\\r\\n<p><strong>\\u0420\\u0430\\u0431\\u043e\\u0447\\u0438\\u0435 \\u0434\\u043d\\u0438 \\u041f\\u043d-\\u041f\\u0442 \\u0441 9:00 \\u0434\\u043e 18:00<\\/strong><\\/p>\\r\\n<p>\\u0412\\u0441\\u0435 \\u043a\\u043d\\u0438\\u0433\\u0438 \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0435\\u0442\\u0430\\u044e\\u0442\\u0441\\u044f \\u043f\\u043e\\u043a\\u0430 \\u0447\\u0442\\u043e \\u043f\\u043e\\u0434 \\u0437\\u0430\\u043a\\u0430\\u0437 \\u0438 \\u0442\\u043e\\u043b\\u044c\\u043a\\u043e \\u0432 \\u043d\\u0430\\u0448\\u0435\\u043c \\u043e\\u0444\\u0438\\u0441\\u0435.<\\/p>\",\"published\":\"1\",\"checked_out\":null,\"checked_out_time\":null,\"access\":\"1\",\"params\":\"{\\\"category_layout\\\":\\\"\\\",\\\"image\\\":\\\"\\\",\\\"image_alt\\\":\\\"\\\"}\",\"metadesc\":\"\",\"metakey\":\"\",\"metadata\":\"{\\\"author\\\":\\\"\\\",\\\"robots\\\":\\\"\\\"}\",\"created_user_id\":\"15\",\"created_time\":\"2018-09-27 15:40:28\",\"modified_user_id\":null,\"modified_time\":\"2018-09-27 15:40:28\",\"hits\":\"0\",\"language\":\"*\",\"version\":null}', 0),
(16, 8, 5, '', '2018-09-27 15:40:41', 15, 620, '844684f15db79a4d8fc9ad166f9701b24a43b4fa', '{\"id\":8,\"asset_id\":\"74\",\"parent_id\":\"1\",\"lft\":\"11\",\"rgt\":\"12\",\"level\":\"1\",\"path\":\"kontakty\",\"extension\":\"com_content\",\"title\":\"\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u044b\",\"alias\":\"kontakty\",\"note\":\"\",\"description\":\"\",\"published\":\"1\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:40:34\",\"access\":\"1\",\"params\":\"{\\\"category_layout\\\":\\\"\\\",\\\"image\\\":\\\"\\\",\\\"image_alt\\\":\\\"\\\"}\",\"metadesc\":\"\",\"metakey\":\"\",\"metadata\":\"{\\\"author\\\":\\\"\\\",\\\"robots\\\":\\\"\\\"}\",\"created_user_id\":\"15\",\"created_time\":\"2018-09-27 15:40:28\",\"modified_user_id\":\"15\",\"modified_time\":\"2018-09-27 15:40:41\",\"hits\":\"0\",\"language\":\"*\",\"version\":\"1\"}', 0),
(17, 5, 1, '', '2018-09-27 15:40:54', 15, 2602, '5d1448d7004de37ae08367ac1f28a3e8f72c1ef5', '{\"id\":5,\"asset_id\":75,\"title\":\"\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u044b\",\"alias\":\"kontakty\",\"introtext\":\"<p><strong>\\u041d\\u0430\\u0439\\u0442\\u0438 \\u043d\\u0430\\u0441 \\u043c\\u043e\\u0436\\u0435\\u0442\\u0435 \\u043f\\u043e \\u0430\\u0434\\u0440\\u0435\\u0441\\u0443:<\\/strong><\\/p>\\r\\n<p>\\u041a\\u0438\\u0448\\u0438\\u043d\\u0435\\u0432 60\\/2 \\u041a\\u043e\\u0440\\u043f.4&nbsp;<\\/p>\\r\\n<p>\\u041d\\u043e\\u043c\\u0435\\u0440 \\u0442\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u0430: 0694447712<br \\/>\\u0424\\u0430\\u043a\\u0441: 022 962 25 25&nbsp;<\\/p>\\r\\n<p><strong>\\u0420\\u0430\\u0431\\u043e\\u0447\\u0438\\u0435 \\u0434\\u043d\\u0438 \\u041f\\u043d-\\u041f\\u0442 \\u0441 9:00 \\u0434\\u043e 18:00<\\/strong><\\/p>\\r\\n<p>\\u0412\\u0441\\u0435 \\u043a\\u043d\\u0438\\u0433\\u0438 \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0435\\u0442\\u0430\\u044e\\u0442\\u0441\\u044f \\u043f\\u043e\\u043a\\u0430 \\u0447\\u0442\\u043e \\u043f\\u043e\\u0434 \\u0437\\u0430\\u043a\\u0430\\u0437 \\u0438 \\u0442\\u043e\\u043b\\u044c\\u043a\\u043e \\u0432 \\u043d\\u0430\\u0448\\u0435\\u043c \\u043e\\u0444\\u0438\\u0441\\u0435.<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"2\",\"created\":\"2018-09-27 15:40:54\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:40:54\",\"modified_by\":null,\"checked_out\":null,\"checked_out_time\":null,\"publish_up\":\"2018-09-27 15:40:54\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":1,\"ordering\":null,\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":null,\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0);
INSERT INTO `orexv_ucm_history` (`version_id`, `ucm_item_id`, `ucm_type_id`, `version_note`, `save_date`, `editor_user_id`, `character_count`, `sha1_hash`, `version_data`, `keep_forever`) VALUES
(18, 5, 1, '', '2018-09-27 15:41:04', 15, 2619, 'f4960300a2c48a8ee265c051b89071f55a7b7eea', '{\"id\":5,\"asset_id\":\"75\",\"title\":\"\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u044b\",\"alias\":\"kontakty\",\"introtext\":\"<p><strong>\\u041d\\u0430\\u0439\\u0442\\u0438 \\u043d\\u0430\\u0441 \\u043c\\u043e\\u0436\\u0435\\u0442\\u0435 \\u043f\\u043e \\u0430\\u0434\\u0440\\u0435\\u0441\\u0443:<\\/strong><\\/p>\\r\\n<p>\\u041a\\u0438\\u0448\\u0438\\u043d\\u0435\\u0432 60\\/2 \\u041a\\u043e\\u0440\\u043f.4&nbsp;<\\/p>\\r\\n<p>\\u041d\\u043e\\u043c\\u0435\\u0440 \\u0442\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u0430: 0694447712<br \\/>\\u0424\\u0430\\u043a\\u0441: 022 962 25 25&nbsp;<\\/p>\\r\\n<p><strong>\\u0420\\u0430\\u0431\\u043e\\u0447\\u0438\\u0435 \\u0434\\u043d\\u0438 \\u041f\\u043d-\\u041f\\u0442 \\u0441 9:00 \\u0434\\u043e 18:00<\\/strong><\\/p>\\r\\n<p>\\u0412\\u0441\\u0435 \\u043a\\u043d\\u0438\\u0433\\u0438 \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0435\\u0442\\u0430\\u044e\\u0442\\u0441\\u044f \\u043f\\u043e\\u043a\\u0430 \\u0447\\u0442\\u043e \\u043f\\u043e\\u0434 \\u0437\\u0430\\u043a\\u0430\\u0437 \\u0438 \\u0442\\u043e\\u043b\\u044c\\u043a\\u043e \\u0432 \\u043d\\u0430\\u0448\\u0435\\u043c \\u043e\\u0444\\u0438\\u0441\\u0435.<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"8\",\"created\":\"2018-09-27 15:40:54\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:41:04\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:40:57\",\"publish_up\":\"2018-09-27 15:40:54\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":2,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"0\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(19, 5, 1, '', '2018-09-27 15:43:57', 15, 2707, '2d45bc70b7e2262b243f3912bb242172b86574cc', '{\"id\":5,\"asset_id\":\"75\",\"title\":\"\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u044b\",\"alias\":\"kontakty\",\"introtext\":\"<p><strong>\\u041d\\u0430\\u0439\\u0442\\u0438 \\u043d\\u0430\\u0441 \\u043c\\u043e\\u0436\\u0435\\u0442\\u0435 \\u043f\\u043e \\u0430\\u0434\\u0440\\u0435\\u0441\\u0443:<\\/strong><\\/p>\\r\\n<p><img src=\\\"images\\/catalog\\/download.png\\\" alt=\\\"\\\" style=\\\"width:100%\\\" \\/><\\/p>\\r\\n<p>\\u041a\\u0438\\u0448\\u0438\\u043d\\u0435\\u0432 60\\/2 \\u041a\\u043e\\u0440\\u043f.4&nbsp;<\\/p>\\r\\n<p>\\u041d\\u043e\\u043c\\u0435\\u0440 \\u0442\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u0430: 0694447712<br \\/>\\u0424\\u0430\\u043a\\u0441: 022 962 25 25&nbsp;<\\/p>\\r\\n<p><strong>\\u0420\\u0430\\u0431\\u043e\\u0447\\u0438\\u0435 \\u0434\\u043d\\u0438 \\u041f\\u043d-\\u041f\\u0442 \\u0441 9:00 \\u0434\\u043e 18:00<\\/strong><\\/p>\\r\\n<p>\\u0412\\u0441\\u0435 \\u043a\\u043d\\u0438\\u0433\\u0438 \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0435\\u0442\\u0430\\u044e\\u0442\\u0441\\u044f \\u043f\\u043e\\u043a\\u0430 \\u0447\\u0442\\u043e \\u043f\\u043e\\u0434 \\u0437\\u0430\\u043a\\u0430\\u0437 \\u0438 \\u0442\\u043e\\u043b\\u044c\\u043a\\u043e \\u0432 \\u043d\\u0430\\u0448\\u0435\\u043c \\u043e\\u0444\\u0438\\u0441\\u0435.<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"8\",\"created\":\"2018-09-27 15:40:54\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:43:57\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:43:16\",\"publish_up\":\"2018-09-27 15:40:54\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":3,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"1\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0),
(20, 5, 1, '', '2018-09-27 15:44:25', 15, 2709, '914f614cad8c6b1f124a1ac1c137a0f4b95cee9f', '{\"id\":5,\"asset_id\":\"75\",\"title\":\"\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u044b\",\"alias\":\"kontakty\",\"introtext\":\"<p><strong>\\u041d\\u0430\\u0439\\u0442\\u0438 \\u043d\\u0430\\u0441 \\u043c\\u043e\\u0436\\u0435\\u0442\\u0435 \\u043f\\u043e \\u0430\\u0434\\u0440\\u0435\\u0441\\u0443:<\\/strong><\\/p>\\r\\n<p><img style=\\\"width: 100%;\\\" src=\\\"images\\/catalog\\/download.png\\\" alt=\\\"\\\" \\/><\\/p>\\r\\n<p>\\u041a\\u0438\\u0448\\u0438\\u043d\\u0435\\u0432 60\\/2 \\u041a\\u043e\\u0440\\u043f.4&nbsp;<\\/p>\\r\\n<p>\\u041d\\u043e\\u043c\\u0435\\u0440 \\u0442\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u0430: 0694447712<br \\/>\\u0424\\u0430\\u043a\\u0441: 022 962 25 25&nbsp;<\\/p>\\r\\n<p><strong>\\u0420\\u0430\\u0431\\u043e\\u0447\\u0438\\u0435 \\u0434\\u043d\\u0438 \\u041f\\u043d-\\u041f\\u0442 \\u0441 9:00 \\u0434\\u043e 18:00<\\/strong><\\/p>\\r\\n<p>\\u0412\\u0441\\u0435 \\u043a\\u043d\\u0438\\u0433\\u0438 \\u043f\\u0440\\u0435\\u043e\\u0431\\u0440\\u0435\\u0442\\u0430\\u044e\\u0442\\u0441\\u044f \\u043f\\u043e\\u043a\\u0430 \\u0447\\u0442\\u043e \\u043f\\u043e\\u0434 \\u0437\\u0430\\u043a\\u0430\\u0437 \\u0438 \\u0442\\u043e\\u043b\\u044c\\u043a\\u043e \\u0432 \\u043d\\u0430\\u0448\\u0435\\u043c \\u043e\\u0444\\u0438\\u0441\\u0435.<\\/p>\",\"fulltext\":\"\",\"state\":1,\"catid\":\"8\",\"created\":\"2018-09-27 15:40:54\",\"created_by\":\"15\",\"created_by_alias\":\"\",\"modified\":\"2018-09-27 15:44:25\",\"modified_by\":\"15\",\"checked_out\":\"15\",\"checked_out_time\":\"2018-09-27 15:43:57\",\"publish_up\":\"2018-09-27 15:40:54\",\"publish_down\":\"0000-00-00 00:00:00\",\"images\":\"{\\\"image_intro\\\":\\\"\\\",\\\"float_intro\\\":\\\"\\\",\\\"image_intro_alt\\\":\\\"\\\",\\\"image_intro_caption\\\":\\\"\\\",\\\"image_fulltext\\\":\\\"\\\",\\\"float_fulltext\\\":\\\"\\\",\\\"image_fulltext_alt\\\":\\\"\\\",\\\"image_fulltext_caption\\\":\\\"\\\"}\",\"urls\":\"{\\\"urla\\\":false,\\\"urlatext\\\":\\\"\\\",\\\"targeta\\\":\\\"\\\",\\\"urlb\\\":false,\\\"urlbtext\\\":\\\"\\\",\\\"targetb\\\":\\\"\\\",\\\"urlc\\\":false,\\\"urlctext\\\":\\\"\\\",\\\"targetc\\\":\\\"\\\"}\",\"attribs\":\"{\\\"show_title\\\":\\\"\\\",\\\"link_titles\\\":\\\"\\\",\\\"show_tags\\\":\\\"\\\",\\\"show_intro\\\":\\\"\\\",\\\"info_block_position\\\":\\\"\\\",\\\"show_category\\\":\\\"\\\",\\\"link_category\\\":\\\"\\\",\\\"show_parent_category\\\":\\\"\\\",\\\"link_parent_category\\\":\\\"\\\",\\\"show_author\\\":\\\"\\\",\\\"link_author\\\":\\\"\\\",\\\"show_create_date\\\":\\\"\\\",\\\"show_modify_date\\\":\\\"\\\",\\\"show_publish_date\\\":\\\"\\\",\\\"show_item_navigation\\\":\\\"\\\",\\\"show_icons\\\":\\\"\\\",\\\"show_print_icon\\\":\\\"\\\",\\\"show_email_icon\\\":\\\"\\\",\\\"show_vote\\\":\\\"\\\",\\\"show_hits\\\":\\\"\\\",\\\"show_noauth\\\":\\\"\\\",\\\"urls_position\\\":\\\"\\\",\\\"alternative_readmore\\\":\\\"\\\",\\\"article_layout\\\":\\\"\\\",\\\"show_publishing_options\\\":\\\"\\\",\\\"show_article_options\\\":\\\"\\\",\\\"show_urls_images_backend\\\":\\\"\\\",\\\"show_urls_images_frontend\\\":\\\"\\\"}\",\"version\":4,\"ordering\":\"0\",\"metakey\":\"\",\"metadesc\":\"\",\"access\":\"1\",\"hits\":\"2\",\"metadata\":\"{\\\"robots\\\":\\\"\\\",\\\"author\\\":\\\"\\\",\\\"rights\\\":\\\"\\\",\\\"xreference\\\":\\\"\\\"}\",\"featured\":\"0\",\"language\":\"*\",\"xreference\":\"\"}', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_updates`
--

CREATE TABLE `orexv_updates` (
  `update_id` int(11) NOT NULL,
  `update_site_id` int(11) DEFAULT '0',
  `extension_id` int(11) DEFAULT '0',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `element` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `folder` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_id` tinyint(3) DEFAULT '0',
  `version` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `detailsurl` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `infourl` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra_query` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available Updates';

--
-- Дамп данных таблицы `orexv_updates`
--

INSERT INTO `orexv_updates` (`update_id`, `update_site_id`, `extension_id`, `name`, `description`, `element`, `type`, `folder`, `client_id`, `version`, `data`, `detailsurl`, `infourl`, `extra_query`) VALUES
(1, 3, 0, 'Greek', '', 'pkg_el-GR', 'package', '', 0, '3.8.11.1', '', 'https://update.joomla.org/language/details3/el-GR_details.xml', '', ''),
(2, 3, 0, 'Japanese', '', 'pkg_ja-JP', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/ja-JP_details.xml', '', ''),
(3, 3, 0, 'Hebrew', '', 'pkg_he-IL', 'package', '', 0, '3.1.1.2', '', 'https://update.joomla.org/language/details3/he-IL_details.xml', '', ''),
(4, 3, 0, 'Bengali', '', 'pkg_bn-BD', 'package', '', 0, '3.8.10.1', '', 'https://update.joomla.org/language/details3/bn-BD_details.xml', '', ''),
(5, 3, 0, 'Hungarian', '', 'pkg_hu-HU', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/hu-HU_details.xml', '', ''),
(6, 3, 0, 'Afrikaans', '', 'pkg_af-ZA', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/af-ZA_details.xml', '', ''),
(7, 3, 0, 'Arabic Unitag', '', 'pkg_ar-AA', 'package', '', 0, '3.7.5.1', '', 'https://update.joomla.org/language/details3/ar-AA_details.xml', '', ''),
(8, 3, 0, 'Belarusian', '', 'pkg_be-BY', 'package', '', 0, '3.2.1.2', '', 'https://update.joomla.org/language/details3/be-BY_details.xml', '', ''),
(9, 1, 700, 'Joomla', '', 'joomla', 'file', '', 0, '3.6.5', '', 'https://update.joomla.org/core/sts/extension_sts.xml', '', ''),
(10, 3, 0, 'Bulgarian', '', 'pkg_bg-BG', 'package', '', 0, '3.6.5.2', '', 'https://update.joomla.org/language/details3/bg-BG_details.xml', '', ''),
(11, 3, 0, 'Catalan', '', 'pkg_ca-ES', 'package', '', 0, '3.8.11.1', '', 'https://update.joomla.org/language/details3/ca-ES_details.xml', '', ''),
(12, 3, 0, 'Chinese Simplified', '', 'pkg_zh-CN', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/zh-CN_details.xml', '', ''),
(13, 3, 0, 'Croatian', '', 'pkg_hr-HR', 'package', '', 0, '3.8.5.1', '', 'https://update.joomla.org/language/details3/hr-HR_details.xml', '', ''),
(14, 3, 0, 'Czech', '', 'pkg_cs-CZ', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/cs-CZ_details.xml', '', ''),
(15, 3, 0, 'Danish', '', 'pkg_da-DK', 'package', '', 0, '3.8.11.1', '', 'https://update.joomla.org/language/details3/da-DK_details.xml', '', ''),
(16, 3, 0, 'Dutch', '', 'pkg_nl-NL', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/nl-NL_details.xml', '', ''),
(17, 3, 0, 'Esperanto', '', 'pkg_eo-XX', 'package', '', 0, '3.8.11.1', '', 'https://update.joomla.org/language/details3/eo-XX_details.xml', '', ''),
(18, 3, 0, 'Estonian', '', 'pkg_et-EE', 'package', '', 0, '3.8.10.1', '', 'https://update.joomla.org/language/details3/et-EE_details.xml', '', ''),
(19, 3, 0, 'Italian', '', 'pkg_it-IT', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/it-IT_details.xml', '', ''),
(20, 3, 0, 'Khmer', '', 'pkg_km-KH', 'package', '', 0, '3.4.5.1', '', 'https://update.joomla.org/language/details3/km-KH_details.xml', '', ''),
(21, 3, 0, 'Korean', '', 'pkg_ko-KR', 'package', '', 0, '3.8.9.1', '', 'https://update.joomla.org/language/details3/ko-KR_details.xml', '', ''),
(22, 3, 0, 'Latvian', '', 'pkg_lv-LV', 'package', '', 0, '3.7.3.1', '', 'https://update.joomla.org/language/details3/lv-LV_details.xml', '', ''),
(23, 3, 0, 'Lithuanian', '', 'pkg_lt-LT', 'package', '', 0, '3.8.10.1', '', 'https://update.joomla.org/language/details3/lt-LT_details.xml', '', ''),
(24, 3, 0, 'Macedonian', '', 'pkg_mk-MK', 'package', '', 0, '3.6.5.1', '', 'https://update.joomla.org/language/details3/mk-MK_details.xml', '', ''),
(25, 3, 0, 'Norwegian Bokmal', '', 'pkg_nb-NO', 'package', '', 0, '3.8.11.1', '', 'https://update.joomla.org/language/details3/nb-NO_details.xml', '', ''),
(26, 3, 0, 'Norwegian Nynorsk', '', 'pkg_nn-NO', 'package', '', 0, '3.4.2.1', '', 'https://update.joomla.org/language/details3/nn-NO_details.xml', '', ''),
(27, 3, 0, 'Persian', '', 'pkg_fa-IR', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/fa-IR_details.xml', '', ''),
(28, 3, 0, 'Polish', '', 'pkg_pl-PL', 'package', '', 0, '3.8.11.2', '', 'https://update.joomla.org/language/details3/pl-PL_details.xml', '', ''),
(29, 3, 0, 'Portuguese', '', 'pkg_pt-PT', 'package', '', 0, '3.8.11.1', '', 'https://update.joomla.org/language/details3/pt-PT_details.xml', '', ''),
(30, 3, 0, 'English AU', '', 'pkg_en-AU', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/en-AU_details.xml', '', ''),
(31, 3, 0, 'Slovak', '', 'pkg_sk-SK', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/sk-SK_details.xml', '', ''),
(32, 3, 0, 'English US', '', 'pkg_en-US', 'package', '', 0, '3.8.12.2', '', 'https://update.joomla.org/language/details3/en-US_details.xml', '', ''),
(33, 3, 0, 'Swedish', '', 'pkg_sv-SE', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/sv-SE_details.xml', '', ''),
(34, 3, 0, 'Syriac', '', 'pkg_sy-IQ', 'package', '', 0, '3.4.5.1', '', 'https://update.joomla.org/language/details3/sy-IQ_details.xml', '', ''),
(35, 3, 0, 'Tamil', '', 'pkg_ta-IN', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/ta-IN_details.xml', '', ''),
(36, 3, 0, 'Thai', '', 'pkg_th-TH', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/th-TH_details.xml', '', ''),
(37, 3, 0, 'Turkish', '', 'pkg_tr-TR', 'package', '', 0, '3.8.2.1', '', 'https://update.joomla.org/language/details3/tr-TR_details.xml', '', ''),
(38, 3, 0, 'Ukrainian', '', 'pkg_uk-UA', 'package', '', 0, '3.7.1.1', '', 'https://update.joomla.org/language/details3/uk-UA_details.xml', '', ''),
(39, 3, 0, 'Uyghur', '', 'pkg_ug-CN', 'package', '', 0, '3.7.5.1', '', 'https://update.joomla.org/language/details3/ug-CN_details.xml', '', ''),
(40, 3, 0, 'Albanian', '', 'pkg_sq-AL', 'package', '', 0, '3.1.1.2', '', 'https://update.joomla.org/language/details3/sq-AL_details.xml', '', ''),
(41, 3, 0, 'Basque', '', 'pkg_eu-ES', 'package', '', 0, '3.7.5.1', '', 'https://update.joomla.org/language/details3/eu-ES_details.xml', '', ''),
(42, 3, 0, 'Hindi', '', 'pkg_hi-IN', 'package', '', 0, '3.3.6.2', '', 'https://update.joomla.org/language/details3/hi-IN_details.xml', '', ''),
(43, 3, 0, 'German DE', '', 'pkg_de-DE', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/de-DE_details.xml', '', ''),
(44, 3, 0, 'Portuguese Brazil', '', 'pkg_pt-BR', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/pt-BR_details.xml', '', ''),
(45, 3, 0, 'Serbian Latin', '', 'pkg_sr-YU', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/sr-YU_details.xml', '', ''),
(46, 3, 0, 'Spanish', '', 'pkg_es-ES', 'package', '', 0, '3.8.12.2', '', 'https://update.joomla.org/language/details3/es-ES_details.xml', '', ''),
(47, 3, 0, 'Bosnian', '', 'pkg_bs-BA', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/bs-BA_details.xml', '', ''),
(48, 3, 0, 'Serbian Cyrillic', '', 'pkg_sr-RS', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/sr-RS_details.xml', '', ''),
(49, 3, 0, 'Vietnamese', '', 'pkg_vi-VN', 'package', '', 0, '3.2.1.2', '', 'https://update.joomla.org/language/details3/vi-VN_details.xml', '', ''),
(50, 3, 0, 'Bahasa Indonesia', '', 'pkg_id-ID', 'package', '', 0, '3.6.2.1', '', 'https://update.joomla.org/language/details3/id-ID_details.xml', '', ''),
(51, 3, 0, 'Finnish', '', 'pkg_fi-FI', 'package', '', 0, '3.8.1.1', '', 'https://update.joomla.org/language/details3/fi-FI_details.xml', '', ''),
(52, 3, 0, 'Swahili', '', 'pkg_sw-KE', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/sw-KE_details.xml', '', ''),
(53, 3, 0, 'Montenegrin', '', 'pkg_srp-ME', 'package', '', 0, '3.3.1.2', '', 'https://update.joomla.org/language/details3/srp-ME_details.xml', '', ''),
(54, 3, 0, 'English CA', '', 'pkg_en-CA', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/en-CA_details.xml', '', ''),
(55, 3, 0, 'French CA', '', 'pkg_fr-CA', 'package', '', 0, '3.6.5.1', '', 'https://update.joomla.org/language/details3/fr-CA_details.xml', '', ''),
(56, 3, 0, 'Welsh', '', 'pkg_cy-GB', 'package', '', 0, '3.8.5.1', '', 'https://update.joomla.org/language/details3/cy-GB_details.xml', '', ''),
(57, 3, 0, 'Sinhala', '', 'pkg_si-LK', 'package', '', 0, '3.3.1.2', '', 'https://update.joomla.org/language/details3/si-LK_details.xml', '', ''),
(58, 3, 0, 'Dari Persian', '', 'pkg_prs-AF', 'package', '', 0, '3.4.4.2', '', 'https://update.joomla.org/language/details3/prs-AF_details.xml', '', ''),
(59, 3, 0, 'Turkmen', '', 'pkg_tk-TM', 'package', '', 0, '3.5.0.2', '', 'https://update.joomla.org/language/details3/tk-TM_details.xml', '', ''),
(60, 3, 0, 'Irish', '', 'pkg_ga-IE', 'package', '', 0, '3.8.7.1', '', 'https://update.joomla.org/language/details3/ga-IE_details.xml', '', ''),
(61, 3, 0, 'Dzongkha', '', 'pkg_dz-BT', 'package', '', 0, '3.6.2.1', '', 'https://update.joomla.org/language/details3/dz-BT_details.xml', '', ''),
(62, 3, 0, 'Slovenian', '', 'pkg_sl-SI', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/sl-SI_details.xml', '', ''),
(63, 3, 0, 'Spanish CO', '', 'pkg_es-CO', 'package', '', 0, '3.8.11.1', '', 'https://update.joomla.org/language/details3/es-CO_details.xml', '', ''),
(64, 3, 0, 'German CH', '', 'pkg_de-CH', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/de-CH_details.xml', '', ''),
(65, 3, 0, 'German AT', '', 'pkg_de-AT', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/de-AT_details.xml', '', ''),
(66, 3, 0, 'German LI', '', 'pkg_de-LI', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/de-LI_details.xml', '', ''),
(67, 3, 0, 'German LU', '', 'pkg_de-LU', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/de-LU_details.xml', '', ''),
(68, 3, 0, 'English NZ', '', 'pkg_en-NZ', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/en-NZ_details.xml', '', ''),
(69, 5, 0, 'Armenian', '', 'pkg_hy-AM', 'package', '', 0, '3.4.4.1', '', 'https://update.joomla.org/language/details3/hy-AM_details.xml', '', ''),
(70, 5, 0, 'Malay', '', 'pkg_ms-MY', 'package', '', 0, '3.4.1.2', '', 'https://update.joomla.org/language/details3/ms-MY_details.xml', '', ''),
(71, 5, 0, 'Romanian', '', 'pkg_ro-RO', 'package', '', 0, '3.7.3.1', '', 'https://update.joomla.org/language/details3/ro-RO_details.xml', '', ''),
(72, 5, 0, 'Flemish', '', 'pkg_nl-BE', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/nl-BE_details.xml', '', ''),
(73, 5, 0, 'Chinese Traditional', '', 'pkg_zh-TW', 'package', '', 0, '3.8.0.1', '', 'https://update.joomla.org/language/details3/zh-TW_details.xml', '', ''),
(74, 5, 0, 'French', '', 'pkg_fr-FR', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/fr-FR_details.xml', '', ''),
(75, 5, 0, 'Galician', '', 'pkg_gl-ES', 'package', '', 0, '3.3.1.2', '', 'https://update.joomla.org/language/details3/gl-ES_details.xml', '', ''),
(76, 5, 0, 'Georgian', '', 'pkg_ka-GE', 'package', '', 0, '3.8.12.1', '', 'https://update.joomla.org/language/details3/ka-GE_details.xml', '', ''),
(77, 6, 10012, 'Community Builder', 'Community Builder package', 'pkg_communitybuilder', 'package', '', 0, '2.2.1', '', 'https://update.joomlapolis.net/versions/pkg_communitybuilder.xml', '', ''),
(78, 7, 10033, 'JCE Editor Core', '', 'pkg_jce', 'package', '', 0, '2.6.32', '', 'https://www.joomlacontenteditor.net/index.php?option=com_updates&view=update&format=xml&file=pkg_jce.xml', 'https://www.joomlacontenteditor.net/news/jce-pro-2-6-32-released', '');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_update_sites`
--

CREATE TABLE `orexv_update_sites` (
  `update_site_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` int(11) DEFAULT '0',
  `last_check_timestamp` bigint(20) DEFAULT '0',
  `extra_query` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Update Sites';

--
-- Дамп данных таблицы `orexv_update_sites`
--

INSERT INTO `orexv_update_sites` (`update_site_id`, `name`, `type`, `location`, `enabled`, `last_check_timestamp`, `extra_query`) VALUES
(1, 'Joomla! Core', 'collection', 'http://update.joomla.org/core/list.xml', 1, 1538059533, ''),
(2, 'Joomla! Extension Directory', 'collection', 'http://update.joomla.org/jed/list.xml', 1, 1538059533, ''),
(3, 'Accredited Joomla! Translations', 'collection', 'http://update.joomla.org/language/translationlist_3.xml', 1, 1538059531, ''),
(4, 'Joomla! Update Component Update Site', 'extension', 'http://update.joomla.org/core/extensions/com_joomlaupdate.xml', 1, 1538059531, ''),
(5, 'Accredited Joomla! Translations', 'collection', 'https://update.joomla.org/language/translationlist_3.xml', 1, 1538059531, ''),
(6, 'Community Builder Package Update Site', 'collection', 'https://update.joomlapolis.net/versions/pkg-communitybuilder-list.xml', 1, 1538059531, ''),
(7, 'JCE Editor Package', 'collection', 'https://cdn.joomlacontenteditor.net/updates/xml/pkg_jce.xml', 1, 1538059531, '');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_update_sites_extensions`
--

CREATE TABLE `orexv_update_sites_extensions` (
  `update_site_id` int(11) NOT NULL DEFAULT '0',
  `extension_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Links extensions to update sites';

--
-- Дамп данных таблицы `orexv_update_sites_extensions`
--

INSERT INTO `orexv_update_sites_extensions` (`update_site_id`, `extension_id`) VALUES
(1, 700),
(2, 700),
(3, 600),
(4, 28),
(5, 10002),
(6, 10012),
(7, 10033);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_usergroups`
--

CREATE TABLE `orexv_usergroups` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Primary Key',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Adjacency List Reference Id',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_usergroups`
--

INSERT INTO `orexv_usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES
(1, 0, 1, 18, 'Public'),
(2, 1, 8, 15, 'Registered'),
(3, 2, 9, 14, 'Author'),
(4, 3, 10, 13, 'Editor'),
(5, 4, 11, 12, 'Publisher'),
(6, 1, 4, 7, 'Manager'),
(7, 6, 5, 6, 'Administrator'),
(8, 1, 16, 17, 'Super Users'),
(9, 1, 2, 3, 'Guest');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_users`
--

CREATE TABLE `orexv_users` (
  `id` int(11) NOT NULL,
  `name` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `username` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastResetTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date of last password reset',
  `resetCount` int(11) NOT NULL DEFAULT '0' COMMENT 'Count of password resets since lastResetTime',
  `otpKey` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Two factor authentication encrypted keys',
  `otep` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'One time emergency passwords',
  `requireReset` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Require user to reset password on next login'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_users`
--

INSERT INTO `orexv_users` (`id`, `name`, `username`, `email`, `password`, `block`, `sendEmail`, `registerDate`, `lastvisitDate`, `activation`, `params`, `lastResetTime`, `resetCount`, `otpKey`, `otep`, `requireReset`) VALUES
(15, 'Super User', 'elcom', 'elcom@mail.ru', '$2y$10$OaORRLtImRpHLTq8XR0KTe/9A1SbvAvz3Yx9bjnV9LglcY74LlwfO', 0, 1, '2018-09-27 13:37:19', '2018-09-27 13:54:02', '0', '', '0000-00-00 00:00:00', 0, '', '', 0),
(16, 'Jhon2', 'Jhon2', 'Jhon2@mail.ru', '$2y$10$UAEOZ8VBFGm6NS241lUN1u.W/L9TzpCe4.LkzhLOZWBzUy7MfPb6W', 0, 0, '2018-09-27 14:11:39', '2018-09-27 14:15:55', '', '{}', '0000-00-00 00:00:00', 0, '', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_user_keys`
--

CREATE TABLE `orexv_user_keys` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `series` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invalid` tinyint(4) NOT NULL,
  `time` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uastring` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_user_notes`
--

CREATE TABLE `orexv_user_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `catid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) UNSIGNED NOT NULL,
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_user_profiles`
--

CREATE TABLE `orexv_user_profiles` (
  `user_id` int(11) NOT NULL,
  `profile_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Simple user profile storage table';

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_user_usergroup_map`
--

CREATE TABLE `orexv_user_usergroup_map` (
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__usergroups.id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_user_usergroup_map`
--

INSERT INTO `orexv_user_usergroup_map` (`user_id`, `group_id`) VALUES
(15, 8),
(16, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_utf8_conversion`
--

CREATE TABLE `orexv_utf8_conversion` (
  `converted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_utf8_conversion`
--

INSERT INTO `orexv_utf8_conversion` (`converted`) VALUES
(2);

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_viewlevels`
--

CREATE TABLE `orexv_viewlevels` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Primary Key',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rules` varchar(5120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON encoded access control.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orexv_viewlevels`
--

INSERT INTO `orexv_viewlevels` (`id`, `title`, `ordering`, `rules`) VALUES
(1, 'Public', 0, '[1]'),
(2, 'Registered', 2, '[6,2,8]'),
(3, 'Special', 3, '[6,3,8]'),
(5, 'Guest', 1, '[9]'),
(6, 'Super Users', 4, '[8]');

-- --------------------------------------------------------

--
-- Структура таблицы `orexv_wf_profiles`
--

CREATE TABLE `orexv_wf_profiles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `users` text NOT NULL,
  `types` text NOT NULL,
  `components` text NOT NULL,
  `area` tinyint(3) NOT NULL,
  `device` varchar(255) NOT NULL,
  `rows` text NOT NULL,
  `plugins` text NOT NULL,
  `published` tinyint(3) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` tinyint(3) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orexv_wf_profiles`
--

INSERT INTO `orexv_wf_profiles` (`id`, `name`, `description`, `users`, `types`, `components`, `area`, `device`, `rows`, `plugins`, `published`, `ordering`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'Default', 'Default Profile for all users', '', '3,4,5,6,8,7', '', 0, 'desktop,tablet,phone', 'help,newdocument,undo,redo,spacer,bold,italic,underline,strikethrough,justifyfull,justifycenter,justifyleft,justifyright,spacer,blockquote,formatselect,styleselect,removeformat,cleanup;fontselect,fontsizeselect,fontcolor,spacer,clipboard,indent,outdent,lists,sub,sup,textcase,charmap,hr;directionality,fullscreen,print,searchreplace,spacer,table,style,xhtmlxtras;visualaid,visualchars,visualblocks,nonbreaking,anchor,unlink,link,imgmanager,spellchecker,article', 'formatselect,styleselect,cleanup,fontselect,fontsizeselect,fontcolor,clipboard,lists,textcase,charmap,hr,directionality,fullscreen,print,searchreplace,table,style,xhtmlxtras,visualchars,visualblocks,nonbreaking,anchor,link,imgmanager,spellchecker,article,spellchecker,article,browser,contextmenu,inlinepopups,media,preview,source', 1, 1, 0, '0000-00-00 00:00:00', ''),
(2, 'Front End', 'Sample Front-end Profile', '', '3,4,5', '', 1, 'desktop,tablet,phone', 'help,newdocument,undo,redo,spacer,bold,italic,underline,strikethrough,justifyfull,justifycenter,justifyleft,justifyright,spacer,formatselect,styleselect;clipboard,searchreplace,indent,outdent,lists,cleanup,charmap,removeformat,hr,sub,sup,textcase,nonbreaking,visualchars,visualblocks;fullscreen,print,visualaid,style,xhtmlxtras,anchor,unlink,link,imgmanager,spellchecker,article', 'charmap,contextmenu,inlinepopups,help,clipboard,searchreplace,fullscreen,preview,print,style,textcase,nonbreaking,visualchars,visualblocks,xhtmlxtras,imgmanager,anchor,link,spellchecker,article,lists,formatselect,styleselect,hr', 0, 2, 0, '0000-00-00 00:00:00', ''),
(3, 'Blogger', 'Simple Blogging Profile', '', '3,4,5,6,8,7', '', 0, 'desktop,tablet,phone', 'bold,italic,strikethrough,lists,blockquote,spacer,justifyleft,justifycenter,justifyright,spacer,link,unlink,imgmanager,article,spellchecker,fullscreen,kitchensink;formatselect,styleselect,underline,justifyfull,clipboard,removeformat,charmap,indent,outdent,undo,redo,help', 'link,imgmanager,article,spellchecker,fullscreen,kitchensink,clipboard,contextmenu,inlinepopups,lists,formatselect,styleselect,textpattern', 0, 3, 0, '0000-00-00 00:00:00', '{\"editor\":{\"toggle\":\"0\"}}'),
(4, 'Mobile', 'Sample Mobile Profile', '', '3,4,5,6,8,7', '', 0, 'tablet,phone', 'undo,redo,spacer,bold,italic,underline,formatselect,spacer,justifyleft,justifycenter,justifyfull,justifyright,spacer,fullscreen,kitchensink;styleselect,lists,spellchecker,article,link,unlink', 'fullscreen,kitchensink,spellchecker,article,link,inlinepopups,lists,formatselect,styleselect,textpattern', 0, 4, 0, '0000-00-00 00:00:00', '{\"editor\":{\"toolbar_theme\":\"mobile\",\"resizing\":\"0\",\"resize_horizontal\":\"0\",\"resizing_use_cookie\":\"0\",\"toggle\":\"0\",\"links\":{\"popups\":{\"default\":\"\",\"jcemediabox\":{\"enable\":\"0\"},\"window\":{\"enable\":\"0\"}}}}}'),
(5, 'Markdown', 'Sample Markdown Profile', '', '6,7,3,4,5,8', '', 0, 'desktop,tablet,phone', 'fullscreen,justifyleft,justifycenter,justifyfull,justifyright,link,unlink,imgmanager,styleselect', 'fullscreen,link,imgmanager,styleselect,inlinepopups,media,textpattern', 0, 5, 0, '0000-00-00 00:00:00', '{\"editor\":{\"toolbar_theme\":\"mobile\"}}');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `orexv_assets`
--
ALTER TABLE `orexv_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_asset_name` (`name`),
  ADD KEY `idx_lft_rgt` (`lft`,`rgt`),
  ADD KEY `idx_parent_id` (`parent_id`);

--
-- Индексы таблицы `orexv_associations`
--
ALTER TABLE `orexv_associations`
  ADD PRIMARY KEY (`context`,`id`),
  ADD KEY `idx_key` (`key`);

--
-- Индексы таблицы `orexv_banners`
--
ALTER TABLE `orexv_banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_state` (`state`),
  ADD KEY `idx_own_prefix` (`own_prefix`),
  ADD KEY `idx_metakey_prefix` (`metakey_prefix`(100)),
  ADD KEY `idx_banner_catid` (`catid`),
  ADD KEY `idx_language` (`language`);

--
-- Индексы таблицы `orexv_banner_clients`
--
ALTER TABLE `orexv_banner_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_own_prefix` (`own_prefix`),
  ADD KEY `idx_metakey_prefix` (`metakey_prefix`(100));

--
-- Индексы таблицы `orexv_banner_tracks`
--
ALTER TABLE `orexv_banner_tracks`
  ADD PRIMARY KEY (`track_date`,`track_type`,`banner_id`),
  ADD KEY `idx_track_date` (`track_date`),
  ADD KEY `idx_track_type` (`track_type`),
  ADD KEY `idx_banner_id` (`banner_id`);

--
-- Индексы таблицы `orexv_categories`
--
ALTER TABLE `orexv_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cat_idx` (`extension`,`published`,`access`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_path` (`path`(100)),
  ADD KEY `idx_left_right` (`lft`,`rgt`),
  ADD KEY `idx_alias` (`alias`(100)),
  ADD KEY `idx_language` (`language`);

--
-- Индексы таблицы `orexv_comprofiler`
--
ALTER TABLE `orexv_comprofiler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `alias` (`alias`),
  ADD KEY `apprconfbanid` (`approved`,`confirmed`,`banned`,`id`),
  ADD KEY `avatappr_apr_conf_ban_avatar` (`avatarapproved`,`approved`,`confirmed`,`banned`,`avatar`(48)),
  ADD KEY `lastupdatedate` (`lastupdatedate`);

--
-- Индексы таблицы `orexv_comprofiler_fields`
--
ALTER TABLE `orexv_comprofiler_fields`
  ADD PRIMARY KEY (`fieldid`),
  ADD KEY `tabid_pub_prof_order` (`tabid`,`published`,`profile`,`ordering`),
  ADD KEY `readonly_published_tabid` (`readonly`,`published`,`tabid`),
  ADD KEY `registration_published_order` (`registration`,`published`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_field_values`
--
ALTER TABLE `orexv_comprofiler_field_values`
  ADD PRIMARY KEY (`fieldvalueid`),
  ADD KEY `fieldid_ordering` (`fieldid`,`ordering`),
  ADD KEY `fieldtitle_id` (`fieldtitle`,`fieldid`),
  ADD KEY `fieldlabel_id` (`fieldlabel`,`fieldid`);

--
-- Индексы таблицы `orexv_comprofiler_lists`
--
ALTER TABLE `orexv_comprofiler_lists`
  ADD PRIMARY KEY (`listid`),
  ADD KEY `pub_ordering` (`published`,`ordering`),
  ADD KEY `default_published` (`default`,`published`);

--
-- Индексы таблицы `orexv_comprofiler_members`
--
ALTER TABLE `orexv_comprofiler_members`
  ADD PRIMARY KEY (`referenceid`,`memberid`),
  ADD KEY `pamr` (`pending`,`accepted`,`memberid`,`referenceid`),
  ADD KEY `aprm` (`accepted`,`pending`,`referenceid`,`memberid`),
  ADD KEY `membrefid` (`memberid`,`referenceid`);

--
-- Индексы таблицы `orexv_comprofiler_plugin`
--
ALTER TABLE `orexv_comprofiler_plugin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `element` (`element`),
  ADD KEY `folder` (`folder`),
  ADD KEY `idx_folder` (`published`,`client_id`,`viewaccesslevel`,`folder`),
  ADD KEY `type_pub_order` (`type`,`published`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity`
--
ALTER TABLE `orexv_comprofiler_plugin_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset` (`asset`(30)),
  ADD KEY `activity` (`asset`(30),`published`,`pinned`,`date`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_actions`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_actions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `value` (`value`(30)),
  ADD KEY `actions` (`published`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_comments`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset` (`asset`(30)),
  ADD KEY `comments` (`asset`(30),`published`,`pinned`,`date`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_emotes`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_emotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `value` (`value`(30)),
  ADD KEY `emotes` (`published`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_following`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_following`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `asset` (`asset`(30));

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_hidden`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_hidden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `types` (`type`(30),`item`(30)),
  ADD KEY `hidden` (`user_id`,`type`(30),`item`(30));

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_likes`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `asset` (`asset`(30));

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_like_types`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_like_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `types` (`published`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_locations`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `value` (`value`(30)),
  ADD KEY `locations` (`published`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_notifications`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `asset` (`asset`(30)),
  ADD KEY `notifications` (`user`,`asset`(30),`published`,`pinned`,`date`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_read`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_read`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset` (`asset`(30)),
  ADD KEY `read` (`user_id`,`asset`(30),`date`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_activity_tags`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset` (`asset`(30)),
  ADD KEY `tag` (`tag`(30));

--
-- Индексы таблицы `orexv_comprofiler_plugin_antispam_attempts`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_antispam_block`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_block`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_value` (`type`(30),`value`(30));

--
-- Индексы таблицы `orexv_comprofiler_plugin_antispam_log`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_antispam_whitelist`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_whitelist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_value` (`type`(30),`value`(30));

--
-- Индексы таблицы `orexv_comprofiler_plugin_autoactions`
--
ALTER TABLE `orexv_comprofiler_plugin_autoactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `published` (`published`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_blogs`
--
ALTER TABLE `orexv_comprofiler_plugin_blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `published` (`published`),
  ADD KEY `user` (`user`),
  ADD KEY `access` (`access`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_emails`
--
ALTER TABLE `orexv_comprofiler_plugin_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `from` (`from`),
  ADD KEY `to` (`to`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_gallery_folders`
--
ALTER TABLE `orexv_comprofiler_plugin_gallery_folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_published_date` (`asset`(30),`published`,`date`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_gallery_items`
--
ALTER TABLE `orexv_comprofiler_plugin_gallery_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_folder_published_date` (`asset`(30),`folder`,`published`,`date`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_invites`
--
ALTER TABLE `orexv_comprofiler_plugin_invites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `orexv_comprofiler_plugin_privacy`
--
ALTER TABLE `orexv_comprofiler_plugin_privacy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `asset` (`asset`(30)),
  ADD KEY `user_asset` (`user_id`,`asset`(30));

--
-- Индексы таблицы `orexv_comprofiler_plugin_privacy_closed`
--
ALTER TABLE `orexv_comprofiler_plugin_privacy_closed`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orexv_comprofiler_plug_profilebook`
--
ALTER TABLE `orexv_comprofiler_plug_profilebook`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_mode_date` (`userid`,`mode`,`date`),
  ADD KEY `pub_user_mode_date` (`published`,`userid`,`mode`,`date`),
  ADD KEY `mode_pub_date` (`mode`,`published`,`date`),
  ADD KEY `status_user_mode` (`status`,`userid`,`mode`),
  ADD KEY `poster_mode_pub_date` (`posterid`,`mode`,`published`,`date`);

--
-- Индексы таблицы `orexv_comprofiler_plug_pulogger`
--
ALTER TABLE `orexv_comprofiler_plug_pulogger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_change_date` (`profileid`,`changedate`);

--
-- Индексы таблицы `orexv_comprofiler_ratings`
--
ALTER TABLE `orexv_comprofiler_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orexv_comprofiler_sessions`
--
ALTER TABLE `orexv_comprofiler_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `expiry_time` (`expiry_time`),
  ADD KEY `userid` (`userid`);

--
-- Индексы таблицы `orexv_comprofiler_tabs`
--
ALTER TABLE `orexv_comprofiler_tabs`
  ADD PRIMARY KEY (`tabid`),
  ADD KEY `pluginclass` (`pluginclass`),
  ADD KEY `enabled_position_ordering` (`enabled`,`position`,`ordering`),
  ADD KEY `orderedit_enabled_pos_order` (`enabled`,`ordering_edit`,`position`,`ordering`),
  ADD KEY `orderreg_enabled_pos_order` (`enabled`,`ordering_register`,`position`,`ordering`);

--
-- Индексы таблицы `orexv_comprofiler_userreports`
--
ALTER TABLE `orexv_comprofiler_userreports`
  ADD PRIMARY KEY (`reportid`),
  ADD KEY `status_user_date` (`reportedstatus`,`reporteduser`,`reportedondate`),
  ADD KEY `reportedbyuser_ondate` (`reportedbyuser`,`reportedondate`);

--
-- Индексы таблицы `orexv_comprofiler_views`
--
ALTER TABLE `orexv_comprofiler_views`
  ADD PRIMARY KEY (`viewer_id`,`profile_id`,`lastip`),
  ADD KEY `lastview` (`lastview`),
  ADD KEY `profile_id_lastview` (`profile_id`,`lastview`,`viewer_id`);

--
-- Индексы таблицы `orexv_contact_details`
--
ALTER TABLE `orexv_contact_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_state` (`published`),
  ADD KEY `idx_catid` (`catid`),
  ADD KEY `idx_createdby` (`created_by`),
  ADD KEY `idx_featured_catid` (`featured`,`catid`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_xreference` (`xreference`);

--
-- Индексы таблицы `orexv_content`
--
ALTER TABLE `orexv_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_state` (`state`),
  ADD KEY `idx_catid` (`catid`),
  ADD KEY `idx_createdby` (`created_by`),
  ADD KEY `idx_featured_catid` (`featured`,`catid`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_xreference` (`xreference`);

--
-- Индексы таблицы `orexv_contentitem_tag_map`
--
ALTER TABLE `orexv_contentitem_tag_map`
  ADD UNIQUE KEY `uc_ItemnameTagid` (`type_id`,`content_item_id`,`tag_id`),
  ADD KEY `idx_tag_type` (`tag_id`,`type_id`),
  ADD KEY `idx_date_id` (`tag_date`,`tag_id`),
  ADD KEY `idx_core_content_id` (`core_content_id`);

--
-- Индексы таблицы `orexv_content_frontpage`
--
ALTER TABLE `orexv_content_frontpage`
  ADD PRIMARY KEY (`content_id`);

--
-- Индексы таблицы `orexv_content_rating`
--
ALTER TABLE `orexv_content_rating`
  ADD PRIMARY KEY (`content_id`);

--
-- Индексы таблицы `orexv_content_types`
--
ALTER TABLE `orexv_content_types`
  ADD PRIMARY KEY (`type_id`),
  ADD KEY `idx_alias` (`type_alias`(100));

--
-- Индексы таблицы `orexv_extensions`
--
ALTER TABLE `orexv_extensions`
  ADD PRIMARY KEY (`extension_id`),
  ADD KEY `element_clientid` (`element`,`client_id`),
  ADD KEY `element_folder_clientid` (`element`,`folder`,`client_id`),
  ADD KEY `extension` (`type`,`element`,`folder`,`client_id`);

--
-- Индексы таблицы `orexv_finder_filters`
--
ALTER TABLE `orexv_finder_filters`
  ADD PRIMARY KEY (`filter_id`);

--
-- Индексы таблицы `orexv_finder_links`
--
ALTER TABLE `orexv_finder_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `idx_type` (`type_id`),
  ADD KEY `idx_title` (`title`(100)),
  ADD KEY `idx_md5` (`md5sum`),
  ADD KEY `idx_url` (`url`(75)),
  ADD KEY `idx_published_list` (`published`,`state`,`access`,`publish_start_date`,`publish_end_date`,`list_price`),
  ADD KEY `idx_published_sale` (`published`,`state`,`access`,`publish_start_date`,`publish_end_date`,`sale_price`);

--
-- Индексы таблицы `orexv_finder_links_terms0`
--
ALTER TABLE `orexv_finder_links_terms0`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms1`
--
ALTER TABLE `orexv_finder_links_terms1`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms2`
--
ALTER TABLE `orexv_finder_links_terms2`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms3`
--
ALTER TABLE `orexv_finder_links_terms3`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms4`
--
ALTER TABLE `orexv_finder_links_terms4`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms5`
--
ALTER TABLE `orexv_finder_links_terms5`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms6`
--
ALTER TABLE `orexv_finder_links_terms6`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms7`
--
ALTER TABLE `orexv_finder_links_terms7`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms8`
--
ALTER TABLE `orexv_finder_links_terms8`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_terms9`
--
ALTER TABLE `orexv_finder_links_terms9`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_termsa`
--
ALTER TABLE `orexv_finder_links_termsa`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_termsb`
--
ALTER TABLE `orexv_finder_links_termsb`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_termsc`
--
ALTER TABLE `orexv_finder_links_termsc`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_termsd`
--
ALTER TABLE `orexv_finder_links_termsd`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_termse`
--
ALTER TABLE `orexv_finder_links_termse`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_links_termsf`
--
ALTER TABLE `orexv_finder_links_termsf`
  ADD PRIMARY KEY (`link_id`,`term_id`),
  ADD KEY `idx_term_weight` (`term_id`,`weight`),
  ADD KEY `idx_link_term_weight` (`link_id`,`term_id`,`weight`);

--
-- Индексы таблицы `orexv_finder_taxonomy`
--
ALTER TABLE `orexv_finder_taxonomy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `state` (`state`),
  ADD KEY `ordering` (`ordering`),
  ADD KEY `access` (`access`),
  ADD KEY `idx_parent_published` (`parent_id`,`state`,`access`);

--
-- Индексы таблицы `orexv_finder_taxonomy_map`
--
ALTER TABLE `orexv_finder_taxonomy_map`
  ADD PRIMARY KEY (`link_id`,`node_id`),
  ADD KEY `link_id` (`link_id`),
  ADD KEY `node_id` (`node_id`);

--
-- Индексы таблицы `orexv_finder_terms`
--
ALTER TABLE `orexv_finder_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD UNIQUE KEY `idx_term` (`term`),
  ADD KEY `idx_term_phrase` (`term`,`phrase`),
  ADD KEY `idx_stem_phrase` (`stem`,`phrase`),
  ADD KEY `idx_soundex_phrase` (`soundex`,`phrase`);

--
-- Индексы таблицы `orexv_finder_terms_common`
--
ALTER TABLE `orexv_finder_terms_common`
  ADD KEY `idx_word_lang` (`term`,`language`),
  ADD KEY `idx_lang` (`language`);

--
-- Индексы таблицы `orexv_finder_tokens`
--
ALTER TABLE `orexv_finder_tokens`
  ADD KEY `idx_word` (`term`),
  ADD KEY `idx_context` (`context`);

--
-- Индексы таблицы `orexv_finder_tokens_aggregate`
--
ALTER TABLE `orexv_finder_tokens_aggregate`
  ADD KEY `token` (`term`),
  ADD KEY `keyword_id` (`term_id`);

--
-- Индексы таблицы `orexv_finder_types`
--
ALTER TABLE `orexv_finder_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Индексы таблицы `orexv_groupjive_categories`
--
ALTER TABLE `orexv_groupjive_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `published_access` (`published`,`access`);

--
-- Индексы таблицы `orexv_groupjive_groups`
--
ALTER TABLE `orexv_groupjive_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`);

--
-- Индексы таблицы `orexv_groupjive_invites`
--
ALTER TABLE `orexv_groupjive_invites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_group` (`user_id`,`group`),
  ADD KEY `group_accepted_email_user` (`group`,`accepted`,`email`(30),`user`);

--
-- Индексы таблицы `orexv_groupjive_notifications`
--
ALTER TABLE `orexv_groupjive_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_group` (`user_id`,`group`);

--
-- Индексы таблицы `orexv_groupjive_plugin_events`
--
ALTER TABLE `orexv_groupjive_plugin_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_user_id_published` (`group`,`user_id`,`published`);

--
-- Индексы таблицы `orexv_groupjive_plugin_events_attendance`
--
ALTER TABLE `orexv_groupjive_plugin_events_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_event` (`user_id`,`event`);

--
-- Индексы таблицы `orexv_groupjive_plugin_file`
--
ALTER TABLE `orexv_groupjive_plugin_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_user_id_published` (`group`,`user_id`,`published`);

--
-- Индексы таблицы `orexv_groupjive_plugin_photo`
--
ALTER TABLE `orexv_groupjive_plugin_photo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_user_id_published` (`group`,`user_id`,`published`);

--
-- Индексы таблицы `orexv_groupjive_plugin_video`
--
ALTER TABLE `orexv_groupjive_plugin_video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_user_id_published` (`group`,`user_id`,`published`);

--
-- Индексы таблицы `orexv_groupjive_plugin_wall`
--
ALTER TABLE `orexv_groupjive_plugin_wall`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_user_id_published` (`group`,`user_id`,`published`);

--
-- Индексы таблицы `orexv_groupjive_users`
--
ALTER TABLE `orexv_groupjive_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_group_status` (`user_id`,`group`,`status`);

--
-- Индексы таблицы `orexv_languages`
--
ALTER TABLE `orexv_languages`
  ADD PRIMARY KEY (`lang_id`),
  ADD UNIQUE KEY `idx_sef` (`sef`),
  ADD UNIQUE KEY `idx_image` (`image`),
  ADD UNIQUE KEY `idx_langcode` (`lang_code`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_ordering` (`ordering`);

--
-- Индексы таблицы `orexv_menu`
--
ALTER TABLE `orexv_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_client_id_parent_id_alias_language` (`client_id`,`parent_id`,`alias`(100),`language`),
  ADD KEY `idx_componentid` (`component_id`,`menutype`,`published`,`access`),
  ADD KEY `idx_menutype` (`menutype`),
  ADD KEY `idx_left_right` (`lft`,`rgt`),
  ADD KEY `idx_alias` (`alias`(100)),
  ADD KEY `idx_path` (`path`(100)),
  ADD KEY `idx_language` (`language`);

--
-- Индексы таблицы `orexv_menu_types`
--
ALTER TABLE `orexv_menu_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_menutype` (`menutype`);

--
-- Индексы таблицы `orexv_messages`
--
ALTER TABLE `orexv_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `useridto_state` (`user_id_to`,`state`);

--
-- Индексы таблицы `orexv_messages_cfg`
--
ALTER TABLE `orexv_messages_cfg`
  ADD UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`);

--
-- Индексы таблицы `orexv_modules`
--
ALTER TABLE `orexv_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `published` (`published`,`access`),
  ADD KEY `newsfeeds` (`module`,`published`),
  ADD KEY `idx_language` (`language`);

--
-- Индексы таблицы `orexv_modules_menu`
--
ALTER TABLE `orexv_modules_menu`
  ADD PRIMARY KEY (`moduleid`,`menuid`);

--
-- Индексы таблицы `orexv_newsfeeds`
--
ALTER TABLE `orexv_newsfeeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_state` (`published`),
  ADD KEY `idx_catid` (`catid`),
  ADD KEY `idx_createdby` (`created_by`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_xreference` (`xreference`);

--
-- Индексы таблицы `orexv_overrider`
--
ALTER TABLE `orexv_overrider`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `orexv_postinstall_messages`
--
ALTER TABLE `orexv_postinstall_messages`
  ADD PRIMARY KEY (`postinstall_message_id`);

--
-- Индексы таблицы `orexv_redirect_links`
--
ALTER TABLE `orexv_redirect_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_old_url` (`old_url`(100)),
  ADD KEY `idx_link_modifed` (`modified_date`);

--
-- Индексы таблицы `orexv_schemas`
--
ALTER TABLE `orexv_schemas`
  ADD PRIMARY KEY (`extension_id`,`version_id`);

--
-- Индексы таблицы `orexv_session`
--
ALTER TABLE `orexv_session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `time` (`time`);

--
-- Индексы таблицы `orexv_tags`
--
ALTER TABLE `orexv_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tag_idx` (`published`,`access`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_path` (`path`(100)),
  ADD KEY `idx_left_right` (`lft`,`rgt`),
  ADD KEY `idx_alias` (`alias`(100)),
  ADD KEY `idx_language` (`language`);

--
-- Индексы таблицы `orexv_template_styles`
--
ALTER TABLE `orexv_template_styles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_template` (`template`),
  ADD KEY `idx_home` (`home`);

--
-- Индексы таблицы `orexv_ucm_base`
--
ALTER TABLE `orexv_ucm_base`
  ADD PRIMARY KEY (`ucm_id`),
  ADD KEY `idx_ucm_item_id` (`ucm_item_id`),
  ADD KEY `idx_ucm_type_id` (`ucm_type_id`),
  ADD KEY `idx_ucm_language_id` (`ucm_language_id`);

--
-- Индексы таблицы `orexv_ucm_content`
--
ALTER TABLE `orexv_ucm_content`
  ADD PRIMARY KEY (`core_content_id`),
  ADD KEY `tag_idx` (`core_state`,`core_access`),
  ADD KEY `idx_access` (`core_access`),
  ADD KEY `idx_alias` (`core_alias`(100)),
  ADD KEY `idx_language` (`core_language`),
  ADD KEY `idx_title` (`core_title`(100)),
  ADD KEY `idx_modified_time` (`core_modified_time`),
  ADD KEY `idx_created_time` (`core_created_time`),
  ADD KEY `idx_content_type` (`core_type_alias`(100)),
  ADD KEY `idx_core_modified_user_id` (`core_modified_user_id`),
  ADD KEY `idx_core_checked_out_user_id` (`core_checked_out_user_id`),
  ADD KEY `idx_core_created_user_id` (`core_created_user_id`),
  ADD KEY `idx_core_type_id` (`core_type_id`);

--
-- Индексы таблицы `orexv_ucm_history`
--
ALTER TABLE `orexv_ucm_history`
  ADD PRIMARY KEY (`version_id`),
  ADD KEY `idx_ucm_item_id` (`ucm_type_id`,`ucm_item_id`),
  ADD KEY `idx_save_date` (`save_date`);

--
-- Индексы таблицы `orexv_updates`
--
ALTER TABLE `orexv_updates`
  ADD PRIMARY KEY (`update_id`);

--
-- Индексы таблицы `orexv_update_sites`
--
ALTER TABLE `orexv_update_sites`
  ADD PRIMARY KEY (`update_site_id`);

--
-- Индексы таблицы `orexv_update_sites_extensions`
--
ALTER TABLE `orexv_update_sites_extensions`
  ADD PRIMARY KEY (`update_site_id`,`extension_id`);

--
-- Индексы таблицы `orexv_usergroups`
--
ALTER TABLE `orexv_usergroups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usergroup_parent_title_lookup` (`parent_id`,`title`),
  ADD KEY `idx_usergroup_title_lookup` (`title`),
  ADD KEY `idx_usergroup_adjacency_lookup` (`parent_id`),
  ADD KEY `idx_usergroup_nested_set_lookup` (`lft`,`rgt`) USING BTREE;

--
-- Индексы таблицы `orexv_users`
--
ALTER TABLE `orexv_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`(100)),
  ADD KEY `idx_block` (`block`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`);

--
-- Индексы таблицы `orexv_user_keys`
--
ALTER TABLE `orexv_user_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `series` (`series`),
  ADD UNIQUE KEY `series_2` (`series`),
  ADD UNIQUE KEY `series_3` (`series`),
  ADD KEY `user_id` (`user_id`(100));

--
-- Индексы таблицы `orexv_user_notes`
--
ALTER TABLE `orexv_user_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_category_id` (`catid`);

--
-- Индексы таблицы `orexv_user_profiles`
--
ALTER TABLE `orexv_user_profiles`
  ADD UNIQUE KEY `idx_user_id_profile_key` (`user_id`,`profile_key`);

--
-- Индексы таблицы `orexv_user_usergroup_map`
--
ALTER TABLE `orexv_user_usergroup_map`
  ADD PRIMARY KEY (`user_id`,`group_id`);

--
-- Индексы таблицы `orexv_viewlevels`
--
ALTER TABLE `orexv_viewlevels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_assetgroup_title_lookup` (`title`);

--
-- Индексы таблицы `orexv_wf_profiles`
--
ALTER TABLE `orexv_wf_profiles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `orexv_assets`
--
ALTER TABLE `orexv_assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT для таблицы `orexv_banners`
--
ALTER TABLE `orexv_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_banner_clients`
--
ALTER TABLE `orexv_banner_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_categories`
--
ALTER TABLE `orexv_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_fields`
--
ALTER TABLE `orexv_comprofiler_fields`
  MODIFY `fieldid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_field_values`
--
ALTER TABLE `orexv_comprofiler_field_values`
  MODIFY `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_lists`
--
ALTER TABLE `orexv_comprofiler_lists`
  MODIFY `listid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin`
--
ALTER TABLE `orexv_comprofiler_plugin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=576;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity`
--
ALTER TABLE `orexv_comprofiler_plugin_activity`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_actions`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_actions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=500;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_comments`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_emotes`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_emotes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=500;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_following`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_following`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_hidden`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_hidden`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_likes`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_likes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_like_types`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_like_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=500;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_locations`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_locations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=500;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_notifications`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_read`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_read`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_activity_tags`
--
ALTER TABLE `orexv_comprofiler_plugin_activity_tags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_antispam_attempts`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_antispam_block`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_block`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_antispam_log`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_antispam_whitelist`
--
ALTER TABLE `orexv_comprofiler_plugin_antispam_whitelist`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_autoactions`
--
ALTER TABLE `orexv_comprofiler_plugin_autoactions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_blogs`
--
ALTER TABLE `orexv_comprofiler_plugin_blogs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_emails`
--
ALTER TABLE `orexv_comprofiler_plugin_emails`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_gallery_folders`
--
ALTER TABLE `orexv_comprofiler_plugin_gallery_folders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_gallery_items`
--
ALTER TABLE `orexv_comprofiler_plugin_gallery_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_invites`
--
ALTER TABLE `orexv_comprofiler_plugin_invites`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_privacy`
--
ALTER TABLE `orexv_comprofiler_plugin_privacy`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plugin_privacy_closed`
--
ALTER TABLE `orexv_comprofiler_plugin_privacy_closed`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plug_profilebook`
--
ALTER TABLE `orexv_comprofiler_plug_profilebook`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_plug_pulogger`
--
ALTER TABLE `orexv_comprofiler_plug_pulogger`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_ratings`
--
ALTER TABLE `orexv_comprofiler_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_tabs`
--
ALTER TABLE `orexv_comprofiler_tabs`
  MODIFY `tabid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT для таблицы `orexv_comprofiler_userreports`
--
ALTER TABLE `orexv_comprofiler_userreports`
  MODIFY `reportid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `orexv_contact_details`
--
ALTER TABLE `orexv_contact_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_content`
--
ALTER TABLE `orexv_content`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `orexv_content_types`
--
ALTER TABLE `orexv_content_types`
  MODIFY `type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;

--
-- AUTO_INCREMENT для таблицы `orexv_extensions`
--
ALTER TABLE `orexv_extensions`
  MODIFY `extension_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10034;

--
-- AUTO_INCREMENT для таблицы `orexv_finder_filters`
--
ALTER TABLE `orexv_finder_filters`
  MODIFY `filter_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_finder_links`
--
ALTER TABLE `orexv_finder_links`
  MODIFY `link_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_finder_taxonomy`
--
ALTER TABLE `orexv_finder_taxonomy`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `orexv_finder_terms`
--
ALTER TABLE `orexv_finder_terms`
  MODIFY `term_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_finder_types`
--
ALTER TABLE `orexv_finder_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_categories`
--
ALTER TABLE `orexv_groupjive_categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_groups`
--
ALTER TABLE `orexv_groupjive_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_invites`
--
ALTER TABLE `orexv_groupjive_invites`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_notifications`
--
ALTER TABLE `orexv_groupjive_notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_plugin_events`
--
ALTER TABLE `orexv_groupjive_plugin_events`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_plugin_events_attendance`
--
ALTER TABLE `orexv_groupjive_plugin_events_attendance`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_plugin_file`
--
ALTER TABLE `orexv_groupjive_plugin_file`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_plugin_photo`
--
ALTER TABLE `orexv_groupjive_plugin_photo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_plugin_video`
--
ALTER TABLE `orexv_groupjive_plugin_video`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_plugin_wall`
--
ALTER TABLE `orexv_groupjive_plugin_wall`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_groupjive_users`
--
ALTER TABLE `orexv_groupjive_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_languages`
--
ALTER TABLE `orexv_languages`
  MODIFY `lang_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `orexv_menu`
--
ALTER TABLE `orexv_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT для таблицы `orexv_menu_types`
--
ALTER TABLE `orexv_menu_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `orexv_messages`
--
ALTER TABLE `orexv_messages`
  MODIFY `message_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_modules`
--
ALTER TABLE `orexv_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT для таблицы `orexv_newsfeeds`
--
ALTER TABLE `orexv_newsfeeds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_overrider`
--
ALTER TABLE `orexv_overrider`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- AUTO_INCREMENT для таблицы `orexv_postinstall_messages`
--
ALTER TABLE `orexv_postinstall_messages`
  MODIFY `postinstall_message_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `orexv_redirect_links`
--
ALTER TABLE `orexv_redirect_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_tags`
--
ALTER TABLE `orexv_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `orexv_template_styles`
--
ALTER TABLE `orexv_template_styles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `orexv_ucm_content`
--
ALTER TABLE `orexv_ucm_content`
  MODIFY `core_content_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_ucm_history`
--
ALTER TABLE `orexv_ucm_history`
  MODIFY `version_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `orexv_updates`
--
ALTER TABLE `orexv_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT для таблицы `orexv_update_sites`
--
ALTER TABLE `orexv_update_sites`
  MODIFY `update_site_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `orexv_usergroups`
--
ALTER TABLE `orexv_usergroups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `orexv_users`
--
ALTER TABLE `orexv_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `orexv_user_keys`
--
ALTER TABLE `orexv_user_keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_user_notes`
--
ALTER TABLE `orexv_user_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orexv_viewlevels`
--
ALTER TABLE `orexv_viewlevels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `orexv_wf_profiles`
--
ALTER TABLE `orexv_wf_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
