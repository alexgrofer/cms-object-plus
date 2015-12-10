CREATE TABLE `cmsplus_soc_likes_image` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_image` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`id_image`) REFERENCES `cmsplus_store_file_image` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`user_id`) REFERENCES `cmsplus_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_soc_comments_image` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`text` varchar(3000) NOT NULL,
	`id_image` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`id_image`) REFERENCES `cmsplus_store_file_image` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_soc_group` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`type` tinyint(1) NOT NULL,
	`category` tinyint(1) NOT NULL,
	`user_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`user_id`) REFERENCES `cmsplus_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_soc_names_group` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`codename` varchar(255) NOT NULL,
	`group_id` int(11),
	PRIMARY KEY (`id`),
	FOREIGN KEY (`group_id`) REFERENCES `cmsplus_soc_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_soc_wall` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`text` varchar(3000) NOT NULL,
	`create_date_time` datetime NOT NULL,
	`edit_date_time` datetime NULL,
	`group_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`group_id`) REFERENCES `cmsplus_soc_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`user_id`) REFERENCES `cmsplus_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_soc_tags_wall` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`wall_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`wall_id`) REFERENCES `cmsplus_soc_wall` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_soc_likes_wall` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`type` tinyint(1) NOT NULL,
	`wall_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`wall_id`) REFERENCES `cmsplus_soc_wall` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`user_id`) REFERENCES `cmsplus_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cmsplus_soc_comments_wall` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`text` varchar(3000) NOT NULL,
	`wall_id` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`wall_id`) REFERENCES `cmsplus_soc_wall` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

