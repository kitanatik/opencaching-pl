SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_index`;
CREATE TABLE IF NOT EXISTS `search_index` (
  `object_type` tinyint(4) NOT NULL,
  `cache_id` int(11) NOT NULL,
  `hash` int(10) unsigned NOT NULL,
  `count` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_type`,`cache_id`,`hash`),
  KEY `object_type` (`object_type`,`hash`,`cache_id`,`count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



