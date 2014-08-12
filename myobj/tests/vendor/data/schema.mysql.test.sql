CREATE TABLE `setcms_testabsbaseobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uclass_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`uclass_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_linestestabsbaseobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`property_id` int(11) NOT NULL,
	`header_id` int(11) NOT NULL,
	`uptextfield` longtext DEFAULT NULL,
	`upcharfield` varchar(255) DEFAULT NULL,
	`updatetimefield` datetime DEFAULT NULL,
	`upintegerfield` int(11) DEFAULT NULL,
	`upfloatfield` double DEFAULT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`property_id`) REFERENCES `setcms_objproperties` (`id`) ON UPDATE CASCADE,
	FOREIGN KEY (`header_id`) REFERENCES `setcms_testabsbaseobjheaders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_linksobjectsalltestabsbase` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`idobj` int(11) NOT NULL,
	`uclass_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`idobj`,`uclass_id`),
	FOREIGN KEY (`uclass_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_linksobjectsalltestabsbase_links` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`from_self_id` int(11) NOT NULL,
	`to_self_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`from_self_id`,`to_self_id`),
	FOREIGN KEY (`to_self_id`) REFERENCES `setcms_linksobjectsalltestabsbase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`from_self_id`) REFERENCES `setcms_linksobjectsalltestabsbase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_testabsbasemodel` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`)
);

-- для объектов также возможно цеплять таблицы, пример с HAS_MANY
CREATE TABLE `setcms_testtablehm` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`text` varchar(255) NOT NULL,
	`obj_id` int(11) NULL,
	PRIMARY KEY (`id`),
	KEY (`obj_id`)
);