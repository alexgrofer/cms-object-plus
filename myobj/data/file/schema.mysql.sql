CREATE TABLE `cmsplus_store_file_image` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`cod_name` varchar(255) NOT NULL,
	`folder` varchar(255) NOT NULL,
	`size` int(11) NOT NULL,
	`date_time` datetime NOT NULL,

	`description` varchar(255) NOT NULL,
	`album_id` int(11) NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`album_id`) REFERENCES `cmsplus_image_album` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;