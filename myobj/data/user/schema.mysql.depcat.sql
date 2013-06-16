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