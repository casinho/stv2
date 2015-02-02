-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 02. Feb 2015 um 16:37
-- Server Version: 5.6.14
-- PHP-Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `santitan`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `authassignment`
--

CREATE TABLE IF NOT EXISTS `authassignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `authassignmentlog`
--

CREATE TABLE IF NOT EXISTS `authassignmentlog` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemname` varchar(100) NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `bizrule` text,
  `data` text,
  `user_id_bearbeiter` int(10) unsigned NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=531 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `authitem`
--

CREATE TABLE IF NOT EXISTS `authitem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `authitemchild`
--

CREATE TABLE IF NOT EXISTS `authitemchild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clans`
--

CREATE TABLE IF NOT EXISTS `clans` (
  `clan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clan` varchar(255) NOT NULL,
  `tag` varchar(40) NOT NULL,
  `image` varchar(255) NOT NULL,
  `claninfo` text NOT NULL,
  `homepage` varchar(255) NOT NULL,
  `homepage_flag` tinyint(1) unsigned NOT NULL,
  `channel` varchar(255) NOT NULL,
  `land_id` smallint(3) unsigned NOT NULL,
  `ctf_flag` tinyint(1) unsigned NOT NULL,
  `tdm_flag` tinyint(1) unsigned NOT NULL,
  `as_flag` tinyint(1) unsigned NOT NULL,
  `soccer_flag` tinyint(1) unsigned NOT NULL,
  `insta_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`clan_id`),
  KEY `land_id` (`land_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=163 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clanwars`
--

CREATE TABLE IF NOT EXISTS `clanwars` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `gametype` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `squad_id` tinyint(2) unsigned NOT NULL,
  `datum` date NOT NULL,
  `spielerzahl` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `enemy_id` int(11) NOT NULL,
  `enemy_spieler` text COLLATE latin1_general_ci NOT NULL,
  `liga_id` smallint(3) unsigned NOT NULL,
  `servername` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `scorelimit` smallint(2) unsigned NOT NULL,
  `timelimit` smallint(2) unsigned NOT NULL,
  `sonstiges` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `anzahl_maps` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `ringer1` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `ringer2` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `spieler` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `report` text COLLATE latin1_general_ci NOT NULL,
  `wertung` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `endscore` smallint(4) NOT NULL,
  `geg_endscore` smallint(4) NOT NULL,
  `poster_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `hits` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `fazit` text COLLATE latin1_general_ci NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1204 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cronjob`
--

CREATE TABLE IF NOT EXISTS `cronjob` (
  `cronjob_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cronjob` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `start` datetime NOT NULL,
  `ende` datetime NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1 = OK, 2 = Fehler',
  `info` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cronjob_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=510 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `coment` text COLLATE latin1_general_ci NOT NULL,
  `size` varchar(12) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `hits` int(5) NOT NULL DEFAULT '1',
  `kat` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `bild` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `typ` char(3) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `poster_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `date` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `show_it` char(2) COLLATE latin1_general_ci NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=218 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_hash` varchar(255) NOT NULL,
  `image_flag` varchar(10) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `image_flag` (`image_flag`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `file_typ`
--

CREATE TABLE IF NOT EXISTS `file_typ` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `tag` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flaggen`
--

CREATE TABLE IF NOT EXISTS `flaggen` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flaggenname` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `nationalname` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum`
--

CREATE TABLE IF NOT EXISTS `forum` (
  `forum_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_titel` varchar(255) NOT NULL,
  `forum_icon` varchar(100) NOT NULL,
  `parent_id` tinyint(2) unsigned NOT NULL,
  `child_id` tinyint(2) unsigned NOT NULL,
  `beschreibung` text NOT NULL,
  `zugriffs_flag` tinyint(1) unsigned NOT NULL,
  `online_flag` tinyint(1) unsigned NOT NULL,
  `nummer` tinyint(2) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `thread_titel` varchar(255) NOT NULL,
  `thread_user_id` int(10) unsigned NOT NULL,
  `thread_user_nick` varchar(100) NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `post_user_id` int(10) unsigned NOT NULL,
  `post_user_nick` varchar(100) NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_antwort` datetime NOT NULL,
  `anz_threads` smallint(4) unsigned NOT NULL,
  `anz_posts` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum2thread2abo`
--

CREATE TABLE IF NOT EXISTS `forum2thread2abo` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_2_post`
--

CREATE TABLE IF NOT EXISTS `forum_2_post` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_bearbeitet` datetime NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `post_flag` tinyint(3) unsigned NOT NULL,
  `post_option` tinyint(3) unsigned NOT NULL,
  `startbeitrag_flag` tinyint(1) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=98 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_2_thread`
--

CREATE TABLE IF NOT EXISTS `forum_2_thread` (
  `thread_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_titel` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `closed_flag` tinyint(1) unsigned NOT NULL,
  `sticky_flag` tinyint(1) unsigned NOT NULL,
  `poll_flag` tinyint(1) unsigned NOT NULL,
  `poll_end_datum` datetime NOT NULL,
  `moved_forum_id` int(10) unsigned NOT NULL,
  `moved_thread_id` int(10) unsigned NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_antwort` datetime NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `post_user_id` int(10) unsigned NOT NULL,
  `post_user_nick` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `anz_posts` int(10) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_3_post`
--

CREATE TABLE IF NOT EXISTS `forum_3_post` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_bearbeitet` datetime NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `post_flag` tinyint(3) unsigned NOT NULL,
  `post_option` tinyint(3) unsigned NOT NULL,
  `startbeitrag_flag` tinyint(1) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_3_thread`
--

CREATE TABLE IF NOT EXISTS `forum_3_thread` (
  `thread_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_titel` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `closed_flag` tinyint(1) unsigned NOT NULL,
  `sticky_flag` tinyint(1) unsigned NOT NULL,
  `poll_flag` tinyint(1) unsigned NOT NULL,
  `poll_end_datum` datetime NOT NULL,
  `moved_forum_id` int(10) unsigned NOT NULL,
  `moved_thread_id` int(10) unsigned NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_antwort` datetime NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `post_user_id` int(10) unsigned NOT NULL,
  `post_user_nick` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `anz_posts` int(10) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_5_post`
--

CREATE TABLE IF NOT EXISTS `forum_5_post` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_bearbeitet` datetime NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `post_flag` tinyint(3) unsigned NOT NULL,
  `post_option` tinyint(3) unsigned NOT NULL,
  `startbeitrag_flag` tinyint(1) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_5_thread`
--

CREATE TABLE IF NOT EXISTS `forum_5_thread` (
  `thread_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_titel` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `closed_flag` tinyint(1) unsigned NOT NULL,
  `sticky_flag` tinyint(1) unsigned NOT NULL,
  `poll_flag` tinyint(1) unsigned NOT NULL,
  `poll_end_datum` datetime NOT NULL,
  `moved_forum_id` int(10) unsigned NOT NULL,
  `moved_thread_id` int(10) unsigned NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_antwort` datetime NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `post_user_id` int(10) unsigned NOT NULL,
  `post_user_nick` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `anz_posts` int(10) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_6_post`
--

CREATE TABLE IF NOT EXISTS `forum_6_post` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_bearbeitet` datetime NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `post_flag` tinyint(3) unsigned NOT NULL,
  `post_option` tinyint(3) unsigned NOT NULL,
  `startbeitrag_flag` tinyint(1) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_6_thread`
--

CREATE TABLE IF NOT EXISTS `forum_6_thread` (
  `thread_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_titel` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(100) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `closed_flag` tinyint(1) unsigned NOT NULL,
  `sticky_flag` tinyint(1) unsigned NOT NULL,
  `poll_flag` tinyint(1) unsigned NOT NULL,
  `poll_end_datum` datetime NOT NULL,
  `moved_forum_id` int(10) unsigned NOT NULL,
  `moved_thread_id` int(10) unsigned NOT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `edit_user_nick` varchar(100) NOT NULL,
  `edit_user_ip` varchar(100) NOT NULL,
  `datum_erstellt` datetime NOT NULL,
  `datum_antwort` datetime NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `post_user_id` int(10) unsigned NOT NULL,
  `post_user_nick` varchar(100) NOT NULL,
  `sprache` char(4) NOT NULL,
  `anz_posts` int(10) unsigned NOT NULL,
  `delete_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`thread_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kategorie`
--

CREATE TABLE IF NOT EXISTS `kategorie` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `tag` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `pic` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `url` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `status` char(2) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `history` text COLLATE latin1_general_ci NOT NULL,
  `try` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `trytext` text COLLATE latin1_general_ci NOT NULL,
  `warscript` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `newsscript` tinyint(4) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kommentarzuweisung`
--

CREATE TABLE IF NOT EXISTS `kommentarzuweisung` (
  `kommentar_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kommentar` text NOT NULL,
  `fremd_id` int(10) unsigned NOT NULL,
  `zuweisung` varchar(40) NOT NULL,
  `poster_id` int(10) unsigned NOT NULL,
  `poster_ip` varchar(40) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `datum` datetime DEFAULT NULL,
  `url` varchar(150) NOT NULL,
  `irc` varchar(50) NOT NULL,
  PRIMARY KEY (`kommentar_id`),
  UNIQUE KEY `kommentar_id` (`kommentar_id`),
  KEY `fremd_id` (`fremd_id`),
  KEY `zuweisung` (`zuweisung`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4329 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `link` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `text` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `beschreibung` text COLLATE latin1_general_ci NOT NULL,
  `bild` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `typ` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `channel` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `tag` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `land_id` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `hits` int(5) NOT NULL DEFAULT '0',
  `joker` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `poster_id` varchar(4) COLLATE latin1_general_ci NOT NULL DEFAULT '1',
  `date` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=262 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `linkzuweisung`
--

CREATE TABLE IF NOT EXISTS `linkzuweisung` (
  `link_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fremd_id` int(11) unsigned NOT NULL,
  `link` varchar(255) NOT NULL,
  `link_text` varchar(255) NOT NULL,
  `zuweisung` varchar(50) NOT NULL,
  PRIMARY KEY (`link_id`),
  KEY `zuweisung` (`zuweisung`),
  KEY `fremd_id` (`fremd_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=323 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `link_typ`
--

CREATE TABLE IF NOT EXISTS `link_typ` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `tag` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `map2clanwar`
--

CREATE TABLE IF NOT EXISTS `map2clanwar` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clanwar_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `map_nr` smallint(2) unsigned NOT NULL,
  `score_st` smallint(3) unsigned NOT NULL,
  `score_enemy` smallint(3) unsigned NOT NULL,
  `enemy_id` int(10) unsigned NOT NULL,
  `wertung` smallint(3) unsigned NOT NULL,
  `report` text NOT NULL,
  PRIMARY KEY (`auto_id`),
  KEY `clanwar_id` (`clanwar_id`),
  KEY `enemy_id` (`enemy_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=583 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `member`
--

CREATE TABLE IF NOT EXISTS `member` (
  `user_id` int(5) NOT NULL AUTO_INCREMENT,
  `nick` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `realname` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `wohnort` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `wohnort_link` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `bundesland` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `flaggen` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `birthday` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `icq` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `status` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `aktiv` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `aufgaben` text COLLATE latin1_general_ci NOT NULL,
  `position` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `member_since` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `laston` varchar(40) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `fav_maps` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `hate_maps` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `fav_weapons` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `other_clans1` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `other_clans_link1` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `other_clans2` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `other_clans_link2` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `clanhistory` text COLLATE latin1_general_ci NOT NULL,
  `hobbies` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `fav_musik` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `fav_filme` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `web_tip1` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `web_tip_link1` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `web_tip2` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `web_tip_link2` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `web_tip3` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `web_tip_link3` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `idle_tip1` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `idle_tip_link1` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `idle_tip2` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `idle_tip_link2` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `idle_tip3` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `idle_tip_link3` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `cpu` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `ram` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `graka` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `soka` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `maus` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `moni` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `provi` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `urlprovi` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `konn` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `beruf` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `telefon` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `strasse` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `postleitzahl` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `content` text COLLATE latin1_general_ci NOT NULL,
  `membertype` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `admin` int(2) NOT NULL DEFAULT '0',
  `pass` varchar(30) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `avatar` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `nt_job` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `contest_n4su` int(3) NOT NULL DEFAULT '0',
  `freigeschaltet_flag` smallint(1) unsigned NOT NULL,
  `sperr_flag` smallint(1) unsigned NOT NULL,
  `datum_registriert` date NOT NULL,
  `datum_validiert` date NOT NULL,
  `sprache` char(4) COLLATE latin1_general_ci NOT NULL,
  `letzte_ip` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `letzter_login` date NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `nick` (`nick`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=15338 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `titel` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `text` text COLLATE latin1_general_ci NOT NULL,
  `slidertext` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `slidertextposition` tinyint(1) unsigned NOT NULL,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `datum` datetime NOT NULL,
  `kategorie_id` int(5) NOT NULL DEFAULT '0',
  `poster_id` int(5) NOT NULL DEFAULT '0',
  `wichtig` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `image` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `alt` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `image_id` smallint(5) unsigned NOT NULL,
  `big_image_id` smallint(5) unsigned NOT NULL,
  `datum_only` date NOT NULL,
  `zeit_only` time NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=294 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `newszuweisung`
--

CREATE TABLE IF NOT EXISTS `newszuweisung` (
  `zuweisung_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(11) unsigned NOT NULL,
  `fremd_id` int(11) unsigned NOT NULL,
  `zuweisung` varchar(50) NOT NULL,
  PRIMARY KEY (`zuweisung_id`),
  UNIQUE KEY `news_id_2` (`news_id`,`fremd_id`,`zuweisung`),
  KEY `zuweisung` (`zuweisung`),
  KEY `fremd_id` (`fremd_id`),
  KEY `news_id` (`news_id`),
  KEY `fremd_id_2` (`fremd_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `open_sessions`
--

CREATE TABLE IF NOT EXISTS `open_sessions` (
  `id` int(200) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `ip` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2391464 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pn_alarm`
--

CREATE TABLE IF NOT EXISTS `pn_alarm` (
  `alarm_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `alarm_tld` char(5) COLLATE utf8_unicode_ci NOT NULL,
  `meldung` text COLLATE utf8_unicode_ci NOT NULL,
  `alarm_datum` datetime DEFAULT NULL,
  PRIMARY KEY (`alarm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pn_archiv`
--

CREATE TABLE IF NOT EXISTS `pn_archiv` (
  `pn_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titel` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `nachricht_id` int(11) NOT NULL,
  `pn_datum` datetime NOT NULL,
  `absender_id` int(6) unsigned NOT NULL,
  `empfaenger_id` int(6) unsigned NOT NULL,
  `beantwortet_flag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `weitergeleitet_flag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `update_user_id` int(11) DEFAULT NULL,
  `update_datum` datetime DEFAULT NULL,
  PRIMARY KEY (`pn_id`),
  KEY `absender_id` (`absender_id`),
  KEY `empfaenger_id` (`empfaenger_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=106174 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pn_ausgang`
--

CREATE TABLE IF NOT EXISTS `pn_ausgang` (
  `pn_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titel` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `nachricht_id` int(11) NOT NULL,
  `pn_datum` datetime NOT NULL,
  `absender_id` int(6) unsigned NOT NULL,
  `empfaenger_id` int(6) unsigned NOT NULL,
  `empfaenger_multi` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `anz_empfaenger` tinyint(1) unsigned NOT NULL,
  `gelesen` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`pn_id`,`absender_id`),
  KEY `absender_id` (`absender_id`),
  KEY `empfaenger_id` (`empfaenger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pn_eingang`
--

CREATE TABLE IF NOT EXISTS `pn_eingang` (
  `pn_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titel` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `nachricht_id` int(11) NOT NULL,
  `pn_datum` datetime NOT NULL,
  `absender_id` int(6) unsigned NOT NULL,
  `empfaenger_id` int(6) unsigned NOT NULL,
  `weitergeleitet_flag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gelesen` tinyint(1) unsigned NOT NULL,
  `update_datum` datetime NOT NULL,
  `gelesen_datum` datetime NOT NULL,
  `alarm_id` int(10) unsigned DEFAULT NULL,
  `alarm_erledigt` tinyint(1) NOT NULL,
  `update_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`pn_id`,`empfaenger_id`),
  KEY `absender_id` (`absender_id`),
  KEY `empfaenger_id` (`empfaenger_id`),
  KEY `counter` (`empfaenger_id`,`gelesen`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pn_nachricht`
--

CREATE TABLE IF NOT EXISTS `pn_nachricht` (
  `nachricht_id` int(11) NOT NULL AUTO_INCREMENT,
  `erste_nachricht_id` int(11) DEFAULT NULL,
  `absender_id` int(11) DEFAULT NULL,
  `nachricht` mediumtext CHARACTER SET utf8 NOT NULL,
  `pn_datum` datetime DEFAULT NULL,
  PRIMARY KEY (`nachricht_id`),
  KEY `erste_nachricht_id` (`erste_nachricht_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pn_queue`
--

CREATE TABLE IF NOT EXISTS `pn_queue` (
  `queue_id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `msg` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  `empfaenger_rollen` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_empfaenger_id` int(11) DEFAULT NULL COMMENT 'user_id der zuletzt verschickten PN',
  `user_id` int(11) NOT NULL COMMENT 'user_id des echten Absenders',
  `absender_id` int(11) NOT NULL COMMENT 'user_id, welche als Absender angezeigt wird.',
  PRIMARY KEY (`queue_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `polls`
--

CREATE TABLE IF NOT EXISTS `polls` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` smallint(4) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `option` varchar(255) NOT NULL,
  `count_votes` int(10) unsigned NOT NULL,
  `sort` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=78 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `post_log`
--

CREATE TABLE IF NOT EXISTS `post_log` (
  `auto_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(6) unsigned NOT NULL,
  `forum_id` smallint(4) unsigned NOT NULL,
  `thread_id` mediumint(6) unsigned NOT NULL,
  `post_id` mediumint(6) unsigned NOT NULL,
  `datum_zeit` datetime NOT NULL,
  PRIMARY KEY (`auto_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=145 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `potm`
--

CREATE TABLE IF NOT EXISTS `potm` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `image_id` int(10) unsigned NOT NULL,
  `url` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `name` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `text` text COLLATE latin1_general_ci NOT NULL,
  `aktiv` int(1) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  `datum` date DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `squads`
--

CREATE TABLE IF NOT EXISTS `squads` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `s1_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `s2_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `s3_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `s4_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `s5_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `member_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=15346 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `statistik_ga`
--

CREATE TABLE IF NOT EXISTS `statistik_ga` (
  `pageviews` int(11) NOT NULL,
  `unique_pageviews` int(11) NOT NULL,
  `visits` int(11) NOT NULL,
  `exitrate` float NOT NULL,
  `avgtimeonpage` float NOT NULL,
  `entrancebouncerate` float NOT NULL,
  `day` tinyint(2) NOT NULL,
  `week` tinyint(2) NOT NULL,
  `month` tinyint(2) NOT NULL,
  `year` year(4) NOT NULL,
  `datum` date NOT NULL,
  PRIMARY KEY (`datum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_user_id` int(10) unsigned NOT NULL,
  `user_nick` varchar(80) NOT NULL,
  `first_letter` char(1) NOT NULL,
  `user_pwd` varchar(80) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `str` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `plz` varchar(10) NOT NULL,
  `handy` varchar(40) NOT NULL,
  `flaggen_id` smallint(4) unsigned NOT NULL,
  `geburtsdatum` varchar(20) NOT NULL,
  `geburtstag` tinyint(2) NOT NULL,
  `geburtmonat` tinyint(2) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` varchar(150) NOT NULL,
  `avatar` varchar(150) NOT NULL,
  `skin` varchar(150) NOT NULL,
  `aufgaben` text NOT NULL,
  `member_since` date DEFAULT NULL,
  `fav_maps` text NOT NULL,
  `hate_maps` text NOT NULL,
  `fav_weapons` text NOT NULL,
  `clanhistory` text NOT NULL,
  `hobbies` text NOT NULL,
  `fav_musik` text NOT NULL,
  `fav_filme` text NOT NULL,
  `motto` text NOT NULL,
  `membertype` text NOT NULL,
  `member_flag` tinyint(1) unsigned NOT NULL,
  `admin_flag` smallint(3) unsigned NOT NULL,
  `freigeschaltet_flag` smallint(1) unsigned NOT NULL,
  `sperr_flag` tinyint(1) unsigned NOT NULL,
  `datum_registriert` date NOT NULL,
  `datum_validiert` date NOT NULL,
  `sprache` char(4) NOT NULL,
  `datenschutzerklaerung_flag` tinyint(1) unsigned NOT NULL,
  `nutzungsbedingungen_flag` tinyint(1) unsigned NOT NULL,
  `letzte_ip` varchar(50) NOT NULL,
  `letzter_login` date NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `first_letter` (`first_letter`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=273 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user2clanwar`
--

CREATE TABLE IF NOT EXISTS `user2clanwar` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clanwar_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`auto_id`),
  KEY `clanwar_id` (`clanwar_id`),
  KEY `member_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=670 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user2count`
--

CREATE TABLE IF NOT EXISTS `user2count` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `zuweisung` varchar(50) NOT NULL,
  `freigeschaltet_flag` tinyint(3) unsigned NOT NULL,
  `anzahl` int(10) unsigned NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user2forum`
--

CREATE TABLE IF NOT EXISTS `user2forum` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `forum_id` int(10) unsigned NOT NULL,
  `haupt_flag` tinyint(3) unsigned NOT NULL,
  `co_flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user2poll`
--

CREATE TABLE IF NOT EXISTS `user2poll` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user2squad`
--

CREATE TABLE IF NOT EXISTS `user2squad` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `squad_id` int(10) unsigned NOT NULL,
  `leader_flag` tinyint(1) unsigned NOT NULL,
  `orga_flag` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=320 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user2validierung`
--

CREATE TABLE IF NOT EXISTS `user2validierung` (
  `auto_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_mail` varchar(100) NOT NULL,
  `daten` varchar(40) NOT NULL,
  `datum_angefordert` datetime NOT NULL,
  `user_id_angefordert` int(11) unsigned NOT NULL,
  `user_ip_angefordert` varchar(40) NOT NULL,
  `validierungs_typ` varchar(30) NOT NULL,
  `validierungs_schluessel` varchar(30) NOT NULL,
  `validiert_datum` datetime DEFAULT NULL,
  `validiert_flag` tinyint(1) unsigned NOT NULL,
  `datum_erinnert` datetime DEFAULT NULL,
  `anzahl_erinnerungen` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`auto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `utserver`
--

CREATE TABLE IF NOT EXISTS `utserver` (
  `serverid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `categoryid` smallint(5) NOT NULL DEFAULT '0',
  `ip` varchar(15) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `port` varchar(10) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `quick` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `poster_id` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`serverid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ut_character_class`
--

CREATE TABLE IF NOT EXISTS `ut_character_class` (
  `class_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(40) NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ut_character_face`
--

CREATE TABLE IF NOT EXISTS `ut_character_face` (
  `face_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `face` varchar(40) NOT NULL,
  `image` varchar(255) NOT NULL,
  `skin_id` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`face_id`),
  KEY `skin_id` (`skin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ut_character_skin`
--

CREATE TABLE IF NOT EXISTS `ut_character_skin` (
  `skin_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `skin` varchar(40) NOT NULL,
  `class_id` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`skin_id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `yiichat_post`
--

CREATE TABLE IF NOT EXISTS `yiichat_post` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` char(40) NOT NULL DEFAULT '',
  `chat_id` char(40) DEFAULT NULL,
  `post_identity` char(40) DEFAULT NULL,
  `owner` char(20) DEFAULT NULL,
  `created` bigint(30) DEFAULT NULL,
  `text` blob,
  `data` blob,
  PRIMARY KEY (`auto_id`),
  KEY `yiichat_chat_id` (`chat_id`),
  KEY `yiichat_chat_id_identity` (`chat_id`,`post_identity`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=79 ;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `authassignment`
--
ALTER TABLE `authassignment`
  ADD CONSTRAINT `authassignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `authitemchild`
--
ALTER TABLE `authitemchild`
  ADD CONSTRAINT `authitemchild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `authitemchild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
