DROP TABLE IF EXISTS `nxc_world_maps`;
CREATE TABLE `nxc_world_maps` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `object_attribute_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` decimal(20,15) NOT NULL,
  `longitude` decimal(20,15) NOT NULL,
  `zoom_level` float(4,2) unsigned NOT NULL,
  `map_type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `nxc_world_map_points`;
CREATE TABLE `nxc_world_map_points` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `world_map_id` int(11) unsigned NOT NULL,
  `related_object_id` int(11) unsigned NOT NULL,
  `related_object_class_id` int(11) unsigned NOT NULL,
  `title` VARCHAR( 255 ) NULL DEFAULT NULL, 
  `latitude` decimal(20,15) NOT NULL,
  `longitude` decimal(20,15) NOT NULL,
  `extra_data_string` TEXT NULL DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;