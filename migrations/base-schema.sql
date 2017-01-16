SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `block`;

CREATE TABLE `block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version_of_id` int(10) unsigned DEFAULT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `live_from` datetime DEFAULT NULL,
  `expires_end` datetime DEFAULT NULL,
  `workflow_stage` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `authored_time` datetime DEFAULT NULL,
  `last_modified_time` datetime DEFAULT NULL,
  `published_time` datetime DEFAULT NULL,
  `rollback_stop_point` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `version_of_id` (`version_of_id`),
  KEY `type_id` (`type_id`),
  KEY `author_id` (`author_id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `block_ibfk_1` FOREIGN KEY (`version_of_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `block_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `block_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `block_ibfk_3` FOREIGN KEY (`template_id`) REFERENCES `block_template` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `block_ibfk_4` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `block_field`;

CREATE TABLE `block_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `item_id` (`block_id`),
  CONSTRAINT `block_field_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `block_note`;

CREATE TABLE `block_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_id` int(10) unsigned NOT NULL,
  `text` text,
  `user_id` int(10) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`block_id`),
  KEY `item_note_ibfk_2` (`user_id`),
  CONSTRAINT `block_note_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `block_note_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `block_template`;

CREATE TABLE `block_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_type_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `view_script` varchar(50) NOT NULL,
  `description` varchar(180) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `for_type_id` (`for_type_id`),
  CONSTRAINT `block_template_ibfk_1` FOREIGN KEY (`for_type_id`) REFERENCES `block_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `block_type`;

CREATE TABLE `block_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container`;

CREATE TABLE `container` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version_of_id` int(10) unsigned DEFAULT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `route_id` int(10) unsigned DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `teaser_template_id` int(10) unsigned DEFAULT NULL,
  `colour_scheme` varchar(20) DEFAULT NULL,
  `nav_text` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `provisional_location_id` int(10) unsigned DEFAULT NULL,
  `published_to` varchar(10) DEFAULT NULL,
  `live_from` datetime DEFAULT NULL,
  `expires_end` datetime DEFAULT NULL,
  `workflow_stage` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `authored_time` datetime DEFAULT NULL,
  `last_modified_time` datetime DEFAULT NULL,
  `published_time` datetime DEFAULT NULL,
  `rollback_stop_point` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_id` (`route_id`),
  KEY `version_of_id` (`version_of_id`),
  KEY `type_id` (`type_id`),
  KEY `author_id` (`author_id`),
  KEY `provisional_location_id` (`provisional_location_id`),
  KEY `template_id` (`template_id`),
  KEY `teaser_template_id` (`teaser_template_id`),
  CONSTRAINT `container_ibfk_1` FOREIGN KEY (`version_of_id`) REFERENCES `container` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `container_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `container_ibfk_4` FOREIGN KEY (`provisional_location_id`) REFERENCES `provisional_location` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `container_ibfk_5` FOREIGN KEY (`route_id`) REFERENCES `route` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `container_ibfk_6` FOREIGN KEY (`template_id`) REFERENCES `container_template` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `container_ibfk_7` FOREIGN KEY (`teaser_template_id`) REFERENCES `container_teaser_template` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_block_sequence`;

CREATE TABLE `container_block_sequence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `container_id` int(10) unsigned NOT NULL,
  `template_block_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`container_id`),
  KEY `template_block_id` (`template_block_id`),
  CONSTRAINT `container_block_sequence_ibfk_1` FOREIGN KEY (`container_id`) REFERENCES `container` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_block_sequence_ibfk_2` FOREIGN KEY (`template_block_id`) REFERENCES `container_template_block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_block_sequence_block`;

CREATE TABLE `container_block_sequence_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_sequence_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  `order_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `block_sequence_id` (`block_sequence_id`),
  KEY `block_id` (`block_id`),
  CONSTRAINT `container_block_sequence_block_ibfk_1` FOREIGN KEY (`block_sequence_id`) REFERENCES `container_block_sequence` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_block_sequence_block_ibfk_2` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_field`;

CREATE TABLE `container_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `container_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `page_id` (`container_id`),
  CONSTRAINT `container_field_ibfk_1` FOREIGN KEY (`container_id`) REFERENCES `container` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_free_block`;

CREATE TABLE `container_free_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `container_id` int(10) unsigned NOT NULL,
  `template_block_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`container_id`),
  KEY `template_block_id` (`template_block_id`),
  KEY `block_id` (`block_id`),
  CONSTRAINT `container_free_block_ibfk_1` FOREIGN KEY (`container_id`) REFERENCES `container` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_free_block_ibfk_2` FOREIGN KEY (`template_block_id`) REFERENCES `container_template_block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_free_block_ibfk_3` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_item`;

CREATE TABLE `container_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `container_id` int(10) unsigned NOT NULL,
  `module_id` int(10) unsigned NOT NULL,
  `identifier` int(10) unsigned NOT NULL,
  `primary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `container_id` (`container_id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `container_item_ibfk_1` FOREIGN KEY (`container_id`) REFERENCES `container` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_item_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_note`;

CREATE TABLE `container_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `container_id` int(10) unsigned NOT NULL,
  `text` text,
  `user_id` int(10) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_note_ibfk_2` (`user_id`),
  KEY `container_id` (`container_id`),
  CONSTRAINT `container_note_ibfk_1` FOREIGN KEY (`container_id`) REFERENCES `container` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `container_note_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_teaser_template`;

CREATE TABLE `container_teaser_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_type_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `view_script` varchar(50) NOT NULL,
  `description` varchar(180) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `for_type_id` (`for_type_id`),
  CONSTRAINT `container_teaser_template_ibfk_1` FOREIGN KEY (`for_type_id`) REFERENCES `container_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_template`;

CREATE TABLE `container_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_type_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `view_script` varchar(50) NOT NULL,
  `description` varchar(180) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `for_type_id` (`for_type_id`),
  CONSTRAINT `container_template_ibfk_1` FOREIGN KEY (`for_type_id`) REFERENCES `container_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_template_block`;

CREATE TABLE `container_template_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `admin_label` varchar(50) NOT NULL,
  `sequence` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `container_template_block_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `container_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `container_type`;

CREATE TABLE `container_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `course`;

CREATE TABLE `course` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `day` varchar(10) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `num_weeks` int(11) DEFAULT NULL,
  `hours_per_week` int(11) DEFAULT NULL,
  `venue` varchar(50) DEFAULT NULL,
  `fee` float DEFAULT NULL,
  `concession` float DEFAULT NULL,
  `band` varchar(25) DEFAULT NULL,
  `day_time` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `digital_gallery_category`;

CREATE TABLE `digital_gallery_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `digital_gallery_image`;

CREATE TABLE `digital_gallery_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `image_no` varchar(50) DEFAULT NULL,
  `credit` varchar(100) DEFAULT NULL,
  `copyright` varchar(100) DEFAULT NULL,
  `price` float(6,2) DEFAULT NULL,
  `image_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `digital_gallery_image_category`;

CREATE TABLE `digital_gallery_image_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `digital_gallery_image_category_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `digital_gallery_image` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `digital_gallery_image_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `digital_gallery_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `digital_gallery_order`;

CREATE TABLE `digital_gallery_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `day_phone` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `message` text,
  `created_time` datetime NOT NULL,
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `digital_gallery_order_item`;

CREATE TABLE `digital_gallery_order_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `digital_gallery_order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `digital_gallery_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `digital_gallery_order_item_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `digital_gallery_image` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `helpdesk_ticket`;

CREATE TABLE `helpdesk_ticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(25) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `issue` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  CONSTRAINT `helpdesk_ticket_ibfk_1` FOREIGN KEY (`status`) REFERENCES `helpdesk_ticket_status` (`text`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `helpdesk_ticket_attachment`;

CREATE TABLE `helpdesk_ticket_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `helpdesk_ticket_attachment_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `helpdesk_ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `helpdesk_ticket_attachment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `helpdesk_ticket_comment`;

CREATE TABLE `helpdesk_ticket_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `helpdesk_ticket_comment_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `helpdesk_ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `helpdesk_ticket_comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `helpdesk_ticket_status`;

CREATE TABLE `helpdesk_ticket_status` (
  `text` varchar(25) NOT NULL,
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item`;

CREATE TABLE `item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version_of_id` int(10) unsigned DEFAULT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `route_id` int(10) unsigned DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `teaser_template_id` int(10) unsigned DEFAULT NULL,
  `colour_scheme` varchar(20) DEFAULT NULL,
  `nav_text` varchar(255) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `provisional_location_id` int(10) unsigned DEFAULT NULL,
  `published_to` varchar(10) DEFAULT NULL,
  `live_from` datetime DEFAULT NULL,
  `expires_end` datetime DEFAULT NULL,
  `workflow_stage` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `authored_time` datetime DEFAULT NULL,
  `last_modified_time` datetime DEFAULT NULL,
  `published_time` datetime DEFAULT NULL,
  `rollback_stop_point` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_id` (`route_id`),
  KEY `version_of_id` (`version_of_id`),
  KEY `type_id` (`type_id`),
  KEY `author_id` (`author_id`),
  KEY `provisional_location_id` (`provisional_location_id`),
  KEY `template_id` (`template_id`),
  KEY `teaser_template_id` (`teaser_template_id`),
  KEY `status` (`status`),
  KEY `workflow_stage` (`workflow_stage`),
  CONSTRAINT `item_ibfk_1` FOREIGN KEY (`version_of_id`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `item_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_4` FOREIGN KEY (`provisional_location_id`) REFERENCES `provisional_location` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_5` FOREIGN KEY (`route_id`) REFERENCES `route` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_6` FOREIGN KEY (`template_id`) REFERENCES `item_template` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_7` FOREIGN KEY (`teaser_template_id`) REFERENCES `item_teaser_template` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_8` FOREIGN KEY (`status`) REFERENCES `status` (`text`) ON UPDATE CASCADE,
  CONSTRAINT `item_ibfk_9` FOREIGN KEY (`workflow_stage`) REFERENCES `workflow_stage` (`text`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_block_sequence`;

CREATE TABLE `item_block_sequence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `template_block_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `template_block_id` (`template_block_id`),
  CONSTRAINT `item_block_sequence_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_block_sequence_ibfk_2` FOREIGN KEY (`template_block_id`) REFERENCES `item_template_block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_block_sequence_block`;

CREATE TABLE `item_block_sequence_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_sequence_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  `order_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `block_sequence_id` (`block_sequence_id`),
  KEY `block_id` (`block_id`),
  CONSTRAINT `item_block_sequence_block_ibfk_1` FOREIGN KEY (`block_sequence_id`) REFERENCES `item_block_sequence` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_block_sequence_block_ibfk_2` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_field`;

CREATE TABLE `item_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `item_field_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_free_block`;

CREATE TABLE `item_free_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `template_block_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `template_block_id` (`template_block_id`),
  KEY `block_id` (`block_id`),
  CONSTRAINT `item_free_block_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_free_block_ibfk_2` FOREIGN KEY (`template_block_id`) REFERENCES `item_template_block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_free_block_ibfk_3` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_note`;

CREATE TABLE `item_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `text` text,
  `user_id` int(10) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `item_note_ibfk_2` (`user_id`),
  CONSTRAINT `item_note_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `item_note_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_part`;

CREATE TABLE `item_part` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `order_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `item_part_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_part_field`;

CREATE TABLE `item_part_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `part_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` longtext,
  PRIMARY KEY (`id`),
  KEY `part_id` (`part_id`),
  CONSTRAINT `item_part_field_ibfk_1` FOREIGN KEY (`part_id`) REFERENCES `item_part` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_teaser_template`;

CREATE TABLE `item_teaser_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_type_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `view_script` varchar(50) NOT NULL,
  `description` varchar(180) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `for_type_id` (`for_type_id`),
  CONSTRAINT `item_teaser_template_ibfk_1` FOREIGN KEY (`for_type_id`) REFERENCES `item_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_template`;

CREATE TABLE `item_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_type_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `view_script` varchar(50) NOT NULL,
  `description` varchar(180) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `for_type_id` (`for_type_id`),
  CONSTRAINT `item_template_ibfk_1` FOREIGN KEY (`for_type_id`) REFERENCES `item_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_template_block`;

CREATE TABLE `item_template_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `admin_label` varchar(50) NOT NULL,
  `sequence` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `item_template_block_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `item_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `item_type`;

CREATE TABLE `item_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `multiple_parts` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `primary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu_item`;

CREATE TABLE `menu_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned NOT NULL,
  `parent_menu_item_id` int(10) unsigned DEFAULT NULL,
  `order_by` int(10) unsigned NOT NULL,
  `route_id` int(10) unsigned DEFAULT NULL,
  `nav_text` varchar(255) DEFAULT NULL,
  `external` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_menu_item_id` (`parent_menu_item_id`),
  KEY `route_id` (`route_id`),
  CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_item_ibfk_2` FOREIGN KEY (`parent_menu_item_id`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `menu_item_ibfk_3` FOREIGN KEY (`route_id`) REFERENCES `route` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `route_controller` varchar(25) DEFAULT NULL,
  `route_action` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `module_page`;

CREATE TABLE `module_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `module_page_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `module_page_block`;

CREATE TABLE `module_page_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_page_id` int(10) unsigned NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `admin_label` varchar(150) DEFAULT NULL,
  `sequence` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_page_id` (`module_page_id`),
  CONSTRAINT `module_page_block_ibfk_1` FOREIGN KEY (`module_page_id`) REFERENCES `module_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `module_page_block_sequence`;

CREATE TABLE `module_page_block_sequence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_page_id` int(10) unsigned NOT NULL,
  `module_page_block_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_page_id` (`module_page_id`),
  KEY `module_page_block_id` (`module_page_block_id`),
  CONSTRAINT `module_page_block_sequence_ibfk_1` FOREIGN KEY (`module_page_id`) REFERENCES `module_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `module_page_block_sequence_ibfk_2` FOREIGN KEY (`module_page_block_id`) REFERENCES `module_page_block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `module_page_block_sequence_block`;

CREATE TABLE `module_page_block_sequence_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_sequence_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  `order_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `block_sequence_id` (`block_sequence_id`),
  KEY `block_id` (`block_id`),
  CONSTRAINT `module_page_block_sequence_block_ibfk_1` FOREIGN KEY (`block_sequence_id`) REFERENCES `module_page_block_sequence` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `module_page_block_sequence_block_ibfk_2` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `module_page_free_block`;

CREATE TABLE `module_page_free_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_page_id` int(10) unsigned NOT NULL,
  `module_page_block_id` int(10) unsigned NOT NULL,
  `block_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_page_id` (`module_page_id`),
  KEY `module_page_block_id` (`module_page_block_id`),
  KEY `block_id` (`block_id`),
  CONSTRAINT `module_page_free_block_ibfk_1` FOREIGN KEY (`module_page_id`) REFERENCES `module_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `module_page_free_block_ibfk_2` FOREIGN KEY (`module_page_block_id`) REFERENCES `module_page_block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `module_page_free_block_ibfk_3` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `provisional_location`;

CREATE TABLE `provisional_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to` varchar(25) NOT NULL,
  `beneath_menu_item_id` int(10) unsigned DEFAULT NULL,
  `container_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `route`;

CREATE TABLE `route` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `module_id` int(10) unsigned NOT NULL,
  `identifier` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `route_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `status`;

CREATE TABLE `status` (
  `text` varchar(20) NOT NULL,
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `whats_on`;

CREATE TABLE `whats_on` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) DEFAULT NULL,
  `activity` varchar(100) DEFAULT NULL,
  `day_time` varchar(25) DEFAULT NULL,
  `venue` varchar(50) DEFAULT NULL,
  `age` varchar(25) DEFAULT NULL,
  `description` text,
  `specific_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `workflow_stage`;

CREATE TABLE `workflow_stage` (
  `text` varchar(20) NOT NULL,
  PRIMARY KEY (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
