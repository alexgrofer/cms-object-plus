CREATE TABLE `cmsplus_store_file_image` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`code_name` varchar(255) NOT NULL,
	`folder` varchar(25) NOT NULL,
	`size` int(11) NOT NULL,
	`date_time` datetime NOT NULL,
	`parent_id` int(11) NULL,

	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;