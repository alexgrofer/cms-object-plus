--
CREATE TABLE `setcms_uclasses` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`codename` varchar(30) NOT NULL,
	`description` varchar(255) NOT NULL,
	`tablespace` smallint(5) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`codename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_uclasses_association` (
	`from_uclasses_id` int(11) NOT NULL,
	`to_uclasses_id` int(11) NOT NULL,
	PRIMARY KEY (`from_uclasses_id`,`to_uclasses_id`),
	FOREIGN KEY (`to_uclasses_id`) REFERENCES `setcms_uclasses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`from_uclasses_id`) REFERENCES `setcms_uclasses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_objproperties` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`codename` varchar(30) NOT NULL,
	`description` varchar(255) NOT NULL,
	`myfield` smallint(5) unsigned NOT NULL,
	`minfield` varchar(4) NOT NULL,
	`maxfield` varchar(4) NOT NULL,
	`required` tinyint(1) NOT NULL,
	`udefault` varchar(255) NOT NULL,
	`setcsv` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`codename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_uclasses_objproperties` (
	`from_uclasses_id` int(11) NOT NULL,
	`to_objproperties_id` int(11) NOT NULL,
	PRIMARY KEY (`from_uclasses_id`,`to_objproperties_id`),
	FOREIGN KEY (`from_uclasses_id`) REFERENCES `setcms_uclasses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_systemobjheaders` (
	-- required
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uclass_id` int(11) NOT NULL,
	-- more options
	`name` varchar(255) NOT NULL,
	`content` longtext NOT NULL,
	`sort` int(11) DEFAULT NULL,
	`vp1` varchar(255) NULL,
	`vp2` varchar(255) NULL,
	`vp3` varchar(255) NULL,
	`bp1` tinyint(1) NULL,
	-- end
	PRIMARY KEY (`id`),
	FOREIGN KEY (`uclass_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_templatesystemobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`desc` varchar(255) NOT NULL DEFAULT '',
	`path` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_viewsystemobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`desc` varchar(255) NOT NULL DEFAULT '',
	`path` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_navigatesystemobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`codename` varchar(255) NULL DEFAULT NULL,
	`action_name` varchar(255) NULL DEFAULT NULL,
	`sort` int(11) NOT NULL DEFAULT 0,
	`parent_id` int(11) NULL DEFAULT NULL,
	`show` tinyint(1) NOT NULL DEFAULT 0,
	-- keys
	`template_default_id` int(11) DEFAULT NULL,
	--
	PRIMARY KEY (`id`),
	UNIQUE KEY (`codename`,`action_name`),
	FOREIGN KEY (`template_default_id`) REFERENCES `setcms_templatesystemobjheaders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_handlesystemobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	-- keys
	`view_id` int(11) NOT NULL,
	`template_id` int(11) NOT NULL,
	--
	PRIMARY KEY (`id`),
	FOREIGN KEY (`view_id`) REFERENCES `setcms_viewsystemobjheaders` (`id`) ON UPDATE CASCADE,
	FOREIGN KEY (`template_id`) REFERENCES `setcms_templatesystemobjheaders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_linessystemobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`property_id` int(11) NOT NULL,
	`header_id` int(11) NOT NULL,
	`uptextfield` longtext DEFAULT NULL,
	`upcharfield` varchar(255) DEFAULT NULL,
	`updatetimefield` datetime DEFAULT NULL,
	`upintegerfield` int(11) DEFAULT NULL,
	`upfloatfield` double DEFAULT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`property_id`) REFERENCES `setcms_objproperties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`header_id`) REFERENCES `setcms_systemobjheaders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_myobjheaders` (
	-- required
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uclass_id` int(11) NOT NULL,
	-- more options
	`name` varchar(255) NOT NULL,
	`content` longtext NOT NULL,
	`sort` int(11) DEFAULT NULL,
	`bpublic` tinyint(1) NOT NULL,
	-- end
	PRIMARY KEY (`id`),
	FOREIGN KEY (`uclass_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_linesmyobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`property_id` int(11) NOT NULL,
	`header_id` int(11) NOT NULL,
	`uptextfield` longtext DEFAULT NULL,
	`upcharfield` varchar(255) DEFAULT NULL,
	`updatetimefield` datetime DEFAULT NULL,
	`upintegerfield` int(11) DEFAULT NULL,
	`upfloatfield` double DEFAULT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`property_id`) REFERENCES `setcms_objproperties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`header_id`) REFERENCES `setcms_myobjheaders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_linksobjectsallmy` (
	`from_obj_id` int(11) NOT NULL,
	`from_class_id` int(11) NOT NULL,
	`to_obj_id` int(11) NOT NULL,
	`to_class_id` int(11) NOT NULL,
	PRIMARY KEY (`from_obj_id`,`from_class_id`,`to_obj_id`,`to_class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_linksobjectsallsystem` (
	`from_obj_id` int(11) NOT NULL,
	`from_class_id` int(11) NOT NULL,
	`to_obj_id` int(11) NOT NULL,
	`to_class_id` int(11) NOT NULL,
	PRIMARY KEY (`from_obj_id`,`from_class_id`,`to_obj_id`,`to_class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
CREATE TABLE `setcms_user` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`login` VARCHAR(255) NOT NULL,
	`password` VARCHAR(32) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_ugroup` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`guid` VARCHAR(36) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `setcms_user_ugroup` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`group_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`user_id`,`group_id`),
	FOREIGN KEY (`user_id`) REFERENCES `setcms_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`group_id`) REFERENCES `setcms_ugroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_userpasport` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`firstname` varchar(255) NOT NULL,
	`lastname` varchar(255) NOT NULL,
	`user_id` int(11) NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`user_id`) REFERENCES `setcms_user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
INSERT INTO `setcms_uclasses` (`id`,`name`,`codename`,`description`,`tablespace`) VALUES
	(1,'groups_sys','groups_sys','',2),
	(2,'views_sys','views_sys','',2),
	(3,'templates_sys','templates_sys','',2),
	(4,'handle_sys','handle_sys','',2),
	(5,'navigation_sys','navigation_sys','',2),
	(6,'param_sys','param_sys','',2),
	(10,'db_dump_sys','db_dump_sys','',2);
INSERT INTO `setcms_uclasses_association` (`from_uclasses_id`,`to_uclasses_id`) VALUES
	(2,1), -- views_sys <> groups_sys
	(5,3), -- navigation_sys <> templates_sys
	(5,4), -- navigation_sys <> handle_sys
	(5,6); -- navigation_sys <> param_sys

-- INSERT INTO `setcms_objproperties` (`id`,`name`,`codename`,`description`,`myfield`,`minfield`,`maxfield`,`required`,`udefault`,`setcsv`) VALUES
-- INSERT INTO `setcms_uclasses_objproperties` (`from_uclasses_id`,`to_objproperties_id`) VALUES

INSERT INTO `setcms_systemobjheaders` (`id`,`uclass_id`,`name`,`content`,`sort`,`vp1`,`vp2`,`vp3`,`bp1`) VALUES
(1,1,'Admin CMS','',0,'CC99CD08-A1BF-461A-B1FE-3182B24D2812','admincms',NULL,0), -- guid outside-id or guid group user
(2,1,'guest','',0,'guestsys','guestsys',NULL,0),
(3,1,'authorized','',0,'authorizedsys','authorizedsys',NULL,0);

--
INSERT INTO `setcms_user` (`id`,`login`,`password`,`email`) VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','admin@admin.com');
INSERT INTO `setcms_userpasport` (`id`,`firstname`,`lastname`,`user_id`) VALUES (1,'alex','',1);
--
INSERT INTO `setcms_ugroup` (`id`,`name`,`guid`) VALUES (1,'admin','CC99CD08-A1BF-461A-B1FE-3182B24D2812');
--
INSERT INTO `setcms_user_ugroup` (`user_id`,`group_id`) VALUES (1,1);