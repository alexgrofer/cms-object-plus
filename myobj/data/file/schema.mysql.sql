CREATE TABLE `cmsplus_store_file_image` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`code_name` varchar(255) NOT NULL,
	`folder` varchar(25) NOT NULL,
	`size` int(11) NOT NULL,
	`date_time` datetime NOT NULL,

	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;