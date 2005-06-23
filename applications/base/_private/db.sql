DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default NULL,
  `name` text,
  `label` text,
  `position` int(11) default NULL,
  `visible` tinyint(4) default '1',
  `access_level` text,
  `action` text,
  `param1` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `menu` VALUES (1,NULL,'admin','Admin',1,1,'10',NULL,NULL),(2,1,'p4a_users','Users',1,1,'10','openMask',NULL),(3,1,'p4a_menu_mask','Menu',1,1,'10','openMask',NULL);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `user` text,
  `pass` text,
  `level` int(11) default NULL,
  `default_mask` text,
  `name` text,
  `surname` text,
  `country` text,
  `address` text,
  `city` text,
  `tel1` text,
  `tel2` text,
  `fax` text,
  `mobile` text,
  `email` text,
  `note` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `users` VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3',10,'p4a_menu_mask',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);