-- Stotage files
CREATE TABLE `setcms_filesstorage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namefile` varchar(60) NOT NULL  DEFAULT '',
  `descr` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL, -- /doun/doc.zip or http://site.ru/image.jpg
  `w_img` varchar(10) NOT NULL DEFAULT '',
  `h_img` varchar(10) NOT NULL DEFAULT '',
  `sort` smallint(5) NOT NULL DEFAULT 0,
  `classprocdownload` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;