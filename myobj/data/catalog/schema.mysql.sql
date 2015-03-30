CREATE TABLE `cmsplus_catalog_category` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`desc` varchar(255) NOT NULL,
	`codename` varchar(255) NOT NULL DEFAULT '',

	`parent_id` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`codename`),
	KEY `parent_id` (`parent_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_catalog_option` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`type` tinyint(1) NOT NULL,
	`conf` varchar(255) NOT NULL DEFAULT '',

	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_catalog_option_faq` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`text` text NOT NULL,

	PRIMARY KEY (`id`),
	`option_id` int(11) NOT NULL,
	FOREIGN KEY (`option_id`) REFERENCES `cmsplus_catalog_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_catalog_category_to_option` (
	`category_id` int(11) NOT NULL,
	`option_id` int(11) NOT NULL,
	PRIMARY KEY (`category_id`,`option_id`),
	FOREIGN KEY (`category_id`) REFERENCES `cmsplus_catalog_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`option_id`) REFERENCES `cmsplus_catalog_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_catalog_option_param` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`val` varchar(255) NOT NULL,

	`id_option` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`id_option`) REFERENCES `cmsplus_catalog_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;