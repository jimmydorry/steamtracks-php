--
-- Database: `dota2_sigs`
--
CREATE DATABASE `dota2_sigs` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `dota2_sigs`;

--
-- Table structure for table `mmr`
--

CREATE TABLE IF NOT EXISTS `mmr` (
  `steam_id` bigint(255) NOT NULL,
  `steam_name` varchar(40) NOT NULL,
  `dota_level` int(10) NOT NULL,
  `dota_wins` int(10) NOT NULL,
  `rank_solo` int(10) NOT NULL,
  `rank_team` int(10) NOT NULL,
  `commends_forgiving` int(10) NOT NULL,
  `commends_friendly` int(10) NOT NULL,
  `commends_leadership` int(10) NOT NULL,
  `commends_teaching` int(10) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`steam_id`),
  KEY `rank_solo_index` (`rank_solo`),
  KEY `rank_team_index` (`rank_team`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
