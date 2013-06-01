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
);
-- (SELF) TABLE uclasses_association
CREATE TABLE `setcms_uclasses_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uclasses_id` int(11) NOT NULL,
  `to_uclasses_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_uclasses_id` (`from_uclasses_id`,`to_uclasses_id`)
);
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
);
-- uclasses (Django models.ManyToManyField) objproperties (relation)
CREATE TABLE `setcms_uclasses_objproperties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uclasses_id` int(11) NOT NULL,
  `to_objproperties_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_uclasses_id` (`from_uclasses_id`,`to_objproperties_id`)
);
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
);
-- setcms_myobj_lines
CREATE TABLE `setcms_systemobjlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL, -- models.ForeignKey(objProperties)
  `uptextfield` longtext NOT NULL,
  `upcharfield` varchar(255) NOT NULL,
  `updatetimefield` datetime DEFAULT NULL,
  `upintegerfield` int(11) DEFAULT NULL,
  `upfloatfield` double DEFAULT NULL,
  PRIMARY KEY (`id`)
);
-- systemobjheaders (Django models.ManyToManyField) systemobjlines (relation)
CREATE TABLE `setcms_systemobjheaders_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_headers_id` int(11) NOT NULL,
  `to_lines_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_headers_id` (`from_headers_id`,`to_lines_id`)
);
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
  PRIMARY KEY (`id`)
);
-- setcms_myobj_lines
CREATE TABLE `setcms_myobjlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `uptextfield` longtext NOT NULL,
  `upcharfield` varchar(255) NOT NULL,
  `updatetimefield` datetime DEFAULT NULL,
  `upintegerfield` int(11) DEFAULT NULL,
  `upfloatfield` double DEFAULT NULL,
  PRIMARY KEY (`id`)
);
-- myobjheaders (Django models.ManyToManyField) myobjlines (relation)
CREATE TABLE `setcms_myobjheaders_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_headers_id` int(11) NOT NULL,
  `to_lines_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_headers_id` (`from_headers_id`,`to_lines_id`)
);
-- -------------------------------------------------- end my objects

-- -------------------------------------------------- links my
-- linksobjectsallmy
CREATE TABLE `setcms_linksobjectsallmy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idobj` int(11) NOT NULL,
  `uclass_id` int(11) NOT NULL, -- models.ForeignKey(uClasses)
  -- (Django) links = models.ManyToManyField("self",blank=True)
  PRIMARY KEY (`id`)
);
-- (SELF) TABLE linksobjectssystem_links
CREATE TABLE `setcms_linksobjectsallmy_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_self_id` int(11) NOT NULL,
  `to_self_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_self_id` (`from_self_id`,`to_self_id`)
);
-- NOR RELATION linksobjectsallmy
-- -------------------------------------------------- ens links all my

-- -------------------------------------------------- links system
-- linksobjectsallsystem
CREATE TABLE `setcms_linksobjectsallsystem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idobj` int(11) NOT NULL,
  `uclass_id` int(11) NOT NULL, -- models.ForeignKey(uClasses)
  -- (Django) links = models.ManyToManyField("self",blank=True)
  PRIMARY KEY (`id`)
);
-- (SELF) TABLE linksobjectssystem_links
CREATE TABLE `setcms_linksobjectsallsystem_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_self_id` int(11) NOT NULL,
  `to_self_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_self_id` (`from_self_id`,`to_self_id`)
);
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
);

CREATE TABLE `setcms_ugroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `guid` VARCHAR(36) NOT NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `setcms_user_ugroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`)
);

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
    -- example
    (7,'news','news','',1),
    (8,'news section','news_section','',1);
INSERT INTO `setcms_uclasses_association` (`id`,`from_uclasses_id`,`to_uclasses_id`) VALUES
    (1,2,1), -- [views_sys]<>-----groups_sys
    (2,5,2), -- [navigation_sys]<>-----views_sys
    (3,5,3), -- [navigation_sys]<>-----templates_sys
    (4,5,4), -- [navigation_sys]<>-----handle_sys
    (5,5,6), -- [navigation_sys]<>-----param_sys
    -- example classes_association
    (6,7,8); -- [news]<>-----news_section
INSERT INTO `setcms_objproperties` (`id`,`name`,`codename`,`description`,`myfield`,`minfield`,`maxfield`,`required`,`udefault`,`setcsv`) VALUES
    -- example
    (1,'Annotation news','annotation_news','',3,'','',0,'',''),
    (2,'Text news','text_news','',3,'','',0,'',''),
    (3,'Codename news section','codename_news_section','',1,'','',0,'','type\ntype=>string');
INSERT INTO `setcms_uclasses_objproperties` (`from_uclasses_id`,`to_objproperties_id`) VALUES
    -- example
    (7,1), -- news -> annotation_news
    (7,2), -- news -> text_news
    (8,3); -- news_section -> codename_news_section
INSERT INTO `setcms_systemobjheaders` (`id`,`uclass_id`,`name`,`content`,`sort`,`vp1`,`vp2`,`bp1`) VALUES -- objects system
-- Class (setcms_uclasses) id = 1
(1,1,'Admin CMS','',0,'CC99CD08-A1BF-461A-B1FE-3182B24D2812','admincms',0), -- guid outside-id or guid group user
(2,1,'guest','',0,'guestsys','guestsys',0),
(3,1,'authorized','',0,'authorizedsys','authorizedsys',0),
-- example
-- navigation - Class (navigation_sys) id = 5
(4,5,'index','',0,'0','index',1),
(5,5,'news list','',0,'0','news',1),
(6,5,'news object','',0,'0','news object',1),
-- templates - Class (templates_sys) id = 3
(7,3,'example index','',0,'example/index','',0),
-- views - Class (views_sys) id = 2
(8,2,'example list news','',0,'example/listnews','',0),
(9,2,'example object news','',0,'example/getobjnews','',0);
-- Object Links (—сылки дл€ возможности прив€зок, создаютс€ автоматически при создании объекта если стоит настройка автомотического создани€ объекта ссылки)
INSERT INTO `setcms_linksobjectsallsystem` (`id`,`idobj`,`uclass_id`) VALUES
-- example setcms_systemobjheaders links
(1,1,1),
(2,2,1),
(3,3,1),
(4,4,5),
(5,5,5),
(6,6,5),
(7,7,3),
(8,8,2),
(9,9,2);

-- User
INSERT INTO `setcms_userpasport` (`id`,`firstname`,`lastname`) VALUES (1,'alex','ivanov');
INSERT INTO `setcms_user` (`id`,`login`,`password`,`email`,`userpasport_id`) VALUES (1,'admin','21232f297a57a5a743894a0e4a801fc3','admin@admin.com',1);
-- Groups
INSERT INTO `setcms_ugroup` (`id`,`name`,`guid`) VALUES (1,'admin','CC99CD08-A1BF-461A-B1FE-3182B24D2812');
-- (One to M) User -> Groups
INSERT INTO `setcms_user_ugroup` (`user_id`,`group_id`) VALUES (1,1);
-- ------- STORE
CREATE TABLE `setcms_dep_cat_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `left_key` int(11) NOT NULL DEFAULT 0,
  `right_key` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 0, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `left_key` (`left_key`,`right_key`,`level`)
);

CREATE TABLE `setcms_dep_cat_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `type` tinyint(4) NOT NULL, -- (логическое(да,нет(неважно это значит не учитывать в шаблоне('не важно'))),число(диапазон ставить в exp,выбор нескольких(чекбоксы мн. выбора))
  `range` varchar(255) DEFAULT NULL, -- диапазон '20-400'
  PRIMARY KEY (`id`)
);

CREATE TABLE `setcms_dep_cat_category_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`option_id`)
);

CREATE TABLE `setcms_dep_cat_option_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `val` varchar(255) NOT NULL,
  `id_option` int(11) NOT NULL,
   PRIMARY KEY (`id`) 
);
