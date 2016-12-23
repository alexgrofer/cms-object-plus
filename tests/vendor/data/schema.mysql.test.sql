-- AbsBaseObjHeadersTest

CREATE TABLE `cmsplus_testabsbaseobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uclass_id` int(11) NOT NULL,
	`param1` varchar(255) NULL,
	`param2` varchar(255) NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`uclass_id`) REFERENCES `cmsplus_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_linestestabsbaseobjheaders` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`property_id` int(11) NOT NULL,
	`header_id` int(11) NOT NULL,
	`uptextfield` longtext NULL,
	`upcharfield` varchar(255) NULL,
	`updatetimefield` datetime NULL,
	`upintegerfield` int(11) NULL,
	`upfloatfield` double NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`property_id`) REFERENCES `cmsplus_objproperties` (`id`) ON DELETE CASCADE,
	FOREIGN KEY (`header_id`) REFERENCES `cmsplus_testabsbaseobjheaders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_linksobjectstestabsbase` (
	`from_obj_id` int(11) NOT NULL,
	`from_class_id` int(11) NOT NULL,
	`to_obj_id` int(11) NOT NULL,
	`to_class_id` int(11) NOT NULL,
	PRIMARY KEY (`from_obj_id`,`from_class_id`,`to_obj_id`,`to_class_id`),
	FOREIGN KEY (`from_class_id`) REFERENCES `cmsplus_uclasses` (`id`) ON UPDATE CASCADE,
	FOREIGN KEY (`to_class_id`) REFERENCES `cmsplus_uclasses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- AbsBaseModelTest

CREATE TABLE `cmsplus_testabsbasemodel` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`param1` varchar(255) NULL,
	`param2` varchar(255) NULL,
	`content_e_array_1` text NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;