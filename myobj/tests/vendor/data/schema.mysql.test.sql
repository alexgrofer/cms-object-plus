-- AbsBaseObjHeadersTest

CREATE TABLE `setcms_testabsbaseobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uclass_id` int(11) NOT NULL,
	`param1` varchar(255) DEFAULT NULL,
	`param2` varchar(255) DEFAULT NULL,
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
	FOREIGN KEY (`property_id`) REFERENCES `setcms_objproperties` (`id`) ON DELETE CASCADE,
	FOREIGN KEY (`header_id`) REFERENCES `setcms_testabsbaseobjheaders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `setcms_linksobjectstestabsbase` (
	`from_obj_id` int(11) NOT NULL,
	`from_class_id` int(11) NOT NULL,
	`to_obj_id` int(11) NOT NULL,
	`to_class_id` int(11) NOT NULL,
	PRIMARY KEY (`from_obj_id`,`from_class_id`,`to_obj_id`,`to_class_id`),
	FOREIGN KEY (`from_class_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE,
	FOREIGN KEY (`to_class_id`) REFERENCES `setcms_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- AbsBaseModelTest

CREATE TABLE `setcms_testabsbasemodel` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`param1` varchar(255) NULL,
	`param2` varchar(255) NULL,
	`content_e_array_1` text NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;