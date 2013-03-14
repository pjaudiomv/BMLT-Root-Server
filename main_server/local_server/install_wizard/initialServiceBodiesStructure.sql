CREATE TABLE `%%PREFIX%%_comdef_service_bodies` (
  `id_bigint` bigint(20) unsigned NOT NULL auto_increment,
  `name_string` tinytext NOT NULL,
  `description_string` text NOT NULL,
  `lang_enum` varchar(7) NOT NULL default 'en',
  `worldid_mixed` varchar(255) default NULL,
  `kml_file_uri_string` varchar(255) default NULL,
  `principal_user_bigint` bigint(20) unsigned default NULL,
  `editors_string` varchar(255) default NULL,
  `uri_string` varchar(255) default NULL,
  `sb_type` varchar(32) default NULL,
  `sb_owner` bigint(20) unsigned default NULL,
  `sb_owner_2` bigint(20) unsigned default NULL,
  `sb_meeting_email` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_bigint`),
  KEY `worldid_mixed` (`worldid_mixed`),
  KEY `kml_file_uri_string` (`kml_file_uri_string`),
  KEY `principal_user_bigint` (`principal_user_bigint`),
  KEY `editors_string` (`editors_string`),
  KEY `lang_enum` (`lang_enum`),
  KEY `uri_string` (`uri_string`),
  KEY `sb_type` (`sb_type`),
  KEY `sb_owner` (`sb_owner`),
  KEY `sb_owner_2` (`sb_owner_2`),
  KEY `sb_meeting_email` (`sb_meeting_email`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
