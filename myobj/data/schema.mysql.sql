-- uclasses system
CREATE TABLE `setcms_uclasses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `codename` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  `tablespace` smallint(5) unsigned NOT NULL,
  -- (Django) properties = models.ManyToManyField(objProperties,blank=True)
  -- (Django) association = models.ManyToManyField("self",blank=True)
  PRIMARY KEY (`id`),
  UNIQUE KEY `codename` (`codename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- (SELF) TABLE uclasses_association
CREATE TABLE `setcms_uclasses_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uclasses_id` int(11) NOT NULL,
  `to_uclasses_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_uclasses_id` (`from_uclasses_id`,`to_uclasses_id`),
  CONSTRAINT `setcms_uclasses_association_ibfk_to_uclasses_id` FOREIGN KEY (`to_uclasses_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `setcms_uclasses_association_ibfk_from_uclasses_id` FOREIGN KEY (`from_uclasses_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- objproperties
CREATE TABLE `setcms_objproperties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `codename` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  `myfield` smallint(5) unsigned NOT NULL, -- types field
  `minfield` varchar(4) NOT NULL,
  `maxfield` varchar(4) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `udefault` varchar(255) NOT NULL,
  `setcsv` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codename` (`codename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- uclasses (Django models.ManyToManyField) objproperties (relation)
CREATE TABLE `setcms_uclasses_objproperties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uclasses_id` int(11) NOT NULL,
  `to_objproperties_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_uclasses_id` (`from_uclasses_id`,`to_objproperties_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- -------------------------------------------------- system objects
-- system_obj_headers
CREATE TABLE `setcms_systemobjheaders` (
   -- required
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uclass_id` int(11) NOT NULL, -- models.ForeignKey(uClasses)
  -- more options
  `name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `vp1` varchar(255) NULL,
  `vp2` varchar(255) NULL,
  `bp1` tinyint(1) NULL,
  -- (Django) lines = models.ManyToManyField(systemObjLines,blank=True)
  -- end
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- setcms_myobj_lines
CREATE TABLE `setcms_systemobjlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL, -- models.ForeignKey(objProperties)
  `uptextfield` longtext NOT NULL,
  `upcharfield` varchar(255) NOT NULL,
  `updatetimefield` datetime DEFAULT NULL,
  `upintegerfield` int(11) DEFAULT NULL,
  `upfloatfield` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `setcms_systemobjlines_ibfk_property_id` FOREIGN KEY (`property_id`) REFERENCES `setcms_objproperties` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- systemobjheaders (Django models.ManyToManyField) systemobjlines (relation)
CREATE TABLE `setcms_systemobjheaders_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_headers_id` int(11) NOT NULL,
  `to_lines_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_headers_id` (`from_headers_id`,`to_lines_id`),
  CONSTRAINT `setcms_systemobjheaders_lines_ibfk_from_headers_id` FOREIGN KEY (`from_headers_id`) REFERENCES `setcms_systemobjheaders` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `setcms_systemobjheaders_lines_ibfk_to_lines_id` FOREIGN KEY (`to_lines_id`) REFERENCES `setcms_systemobjlines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- -------------------------------------------------- end system objects

-- -------------------------------------------------- my objects
-- my_obj_headers
CREATE TABLE `setcms_myobjheaders` (
   -- required
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uclass_id` int(11) NOT NULL,
  -- more options
  `name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `bpublic` tinyint(1) NOT NULL,
  -- (Django) lines = models.ManyToManyField(myObjLines,blank=True)
  -- end
  PRIMARY KEY (`id`),
  KEY `uclass_id` (`uclass_id`),
  CONSTRAINT `setcms_myobjheaders_ibfk_uclass_id` FOREIGN KEY (`uclass_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- setcms_myobj_lines
CREATE TABLE `setcms_myobjlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `uptextfield` longtext NOT NULL,
  `upcharfield` varchar(255) NOT NULL,
  `updatetimefield` datetime DEFAULT NULL,
  `upintegerfield` int(11) DEFAULT NULL,
  `upfloatfield` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`),
  CONSTRAINT `setcms_myobjlines_ibfk_property_id` FOREIGN KEY (`property_id`) REFERENCES `setcms_objproperties` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- myobjheaders (Django models.ManyToManyField) myobjlines (relation)
CREATE TABLE `setcms_myobjheaders_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_headers_id` int(11) NOT NULL,
  `to_lines_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_headers_id` (`from_headers_id`,`to_lines_id`),
  CONSTRAINT `setcms_myobjheaders_lines_ibfk_from_headers_id` FOREIGN KEY (`from_headers_id`) REFERENCES `setcms_myobjheaders` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `setcms_myobjheaders_lines_ibfk_to_lines_id` FOREIGN KEY (`to_lines_id`) REFERENCES `setcms_myobjlines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- -------------------------------------------------- end my objects

-- -------------------------------------------------- links my
-- linksobjectsallmy
CREATE TABLE `setcms_linksobjectsallmy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idobj` int(11) NOT NULL,
  `uclass_id` int(11) NOT NULL, -- models.ForeignKey(uClasses)
  -- (Django) links = models.ManyToManyField("self",blank=True)
  PRIMARY KEY (`id`),
  UNIQUE KEY `idobj` (`idobj`,`uclass_id`),
  CONSTRAINT `setcms_linksobjectsallmy_ibfk_uclass_id` FOREIGN KEY (`uclass_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- (SELF) TABLE linksobjectssystem_links
CREATE TABLE `setcms_linksobjectsallmy_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_self_id` int(11) NOT NULL,
  `to_self_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_self_id` (`from_self_id`,`to_self_id`),
  CONSTRAINT `setcms_linksobjectsallmy_links_ibfk_to_self_id` FOREIGN KEY (`to_self_id`) REFERENCES `setcms_linksobjectsallmy` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `setcms_linksobjectsallmy_links_ibfk_from_self_id` FOREIGN KEY (`from_self_id`) REFERENCES `setcms_linksobjectsallmy` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- NOR RELATION linksobjectsallmy
-- -------------------------------------------------- ens links all my

-- -------------------------------------------------- links system
-- linksobjectsallsystem
CREATE TABLE `setcms_linksobjectsallsystem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idobj` int(11) NOT NULL,
  `uclass_id` int(11) NOT NULL, -- models.ForeignKey(uClasses)
  -- (Django) links = models.ManyToManyField("self",blank=True)
  PRIMARY KEY (`id`),
  UNIQUE KEY `idobj` (`idobj`,`uclass_id`),
  CONSTRAINT `setcms_linksobjectsallsystem_ibfk_uclass_id` FOREIGN KEY (`uclass_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- (SELF) TABLE linksobjectssystem_links
CREATE TABLE `setcms_linksobjectsallsystem_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_self_id` int(11) NOT NULL,
  `to_self_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_self_id` (`from_self_id`,`to_self_id`),
  CONSTRAINT `setcms_linksobjectsallsystem_links_ibfk_to_self_id` FOREIGN KEY (`to_self_id`) REFERENCES `setcms_linksobjectsallsystem` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `setcms_linksobjectsallsystem_links_ibfk_from_self_id` FOREIGN KEY (`from_self_id`) REFERENCES `setcms_linksobjectsallsystem` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- NOR RELATION linksobjectsallsystem
-- -------------------------------------------------- ens links all system

-- User
CREATE TABLE `setcms_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(255) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `userpasport_id` int(11) NULL,
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
  UNIQUE KEY `user_id` (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_userpasport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);
-- -------------------------------------------------- end User
-- assotiation
INSERT INTO `setcms_uclasses` (`id`,`name`,`codename`,`description`,`tablespace`) VALUES
	-- SYS
	(1,'groups_sys','groups_sys','',2),
	(2,'views_sys','views_sys','',2),
	(3,'templates_sys','templates_sys','',2),
	(4,'handle_sys','handle_sys','',2),
	(5,'navigation_sys','navigation_sys','',2),
	(6,'param_sys','param_sys','',2),
	(9,'controllersnav_sys','controllersnav_sys','',2),
	(10,'db_dump_sys','db_dump_sys','',2),
	-- example
	(17,'news example','news_example','',1),
	(18,'news section example','news_section_example','',1);
INSERT INTO `setcms_uclasses_association` (`from_uclasses_id`,`to_uclasses_id`) VALUES
	(2,1), -- [views_sys]<>-----groups_sys
	(5,2), -- [navigation_sys]<>-----views_sys
	(5,3), -- [navigation_sys]<>-----templates_sys
	(5,4), -- [navigation_sys]<>-----handle_sys
	(5,6), -- [navigation_sys]<>-----param_sys
	(5,9), -- [navigation_sys]<>-----controllersnav_sys
	-- example classes_association
	(17,18); -- [news_example]<>-----news_section_example
INSERT INTO `setcms_objproperties` (`id`,`name`,`codename`,`description`,`myfield`,`minfield`,`maxfield`,`required`,`udefault`,`setcsv`) VALUES
	-- example
	(1,'Annotation news example','annotation_news_example','',3,'','',0,'',''),
	(2,'Text news example','text_news_example','',3,'','',0,'',''),
	(3,'Codename news section example','codename_news_section_example','',1,'','',0,'','type\ntype=>string');
INSERT INTO `setcms_uclasses_objproperties` (`from_uclasses_id`,`to_objproperties_id`) VALUES
	-- example
	(17,1), -- news_example -> annotation_news_example
	(17,2), -- news_example -> text_news_example
	(18,3); -- news_example -> codename_news_section_example
INSERT INTO `setcms_systemobjheaders` (`id`,`uclass_id`,`name`,`content`,`sort`,`vp1`,`vp2`,`bp1`) VALUES -- objects system
-- Class (setcms_uclasses) id = 1
(1,1,'Admin CMS','',0,'CC99CD08-A1BF-461A-B1FE-3182B24D2812','admincms',0), -- guid outside-id or guid group user
(2,1,'guest','',0,'guestsys','guestsys',0),
(3,1,'authorized','',0,'authorizedsys','authorizedsys',0),
-- Controller def
(4,9,'default','default controller',null,'default','',null),
-- example
-- navigation - Class (navigation_sys) id = 5
(14,5,'index','',0,'0','index',1),
(15,5,'example news list','',0,'0','news_list_example',1),
(16,5,'example news object','',0,'0','news_object_example',1),
-- templates - Class (templates_sys) id = 3
(17,3,'example index','',0,'example/index','',0),
-- views - Class (views_sys) id = 2
(18,2,'example list news','',0,'example/listnews','',0),
(19,2,'example object news','',0,'example/getobjnews','',0);
-- Object Links
INSERT INTO `setcms_linksobjectsallsystem` (`idobj`,`uclass_id`) VALUES
-- example setcms_systemobjheaders links
-- добавить все из setcms_systemobjheaders id, class по порядку
(1,1),
(2,1),
(3,1),
(4,9),
(14,5),
(15,5),
(16,5),
(17,3),
(18,2),
(19,5);

-- User
INSERT INTO `setcms_userpasport` (`id`,`firstname`,`lastname`) VALUES (1,'alex','ivanov');
INSERT INTO `setcms_user` (`id`,`login`,`password`,`email`,`userpasport_id`) VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','admin@admin.com',1);
-- Groups
INSERT INTO `setcms_ugroup` (`id`,`name`,`guid`) VALUES (1,'admin','CC99CD08-A1BF-461A-B1FE-3182B24D2812');
-- (One to M) User -> Groups
INSERT INTO `setcms_user_ugroup` (`user_id`,`group_id`) VALUES (1,1);
