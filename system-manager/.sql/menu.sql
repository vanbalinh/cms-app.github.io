CREATE TABLE `tbl_menu` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `page_title` TEXT COLLATE utf8_vietnamese_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT 0,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `icon_class` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `permission_id` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `description` TEXT COLLATE utf8_vietnamese_ci NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL,
  `updated_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_vietnamese_ci;

--
--
--  After table
--
--
ALTER TABLE
  `tbl_menu`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_menu`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;

ALTER TABLE
  `tbl_menu`
ADD
  CONSTRAINT `tbl_menu_permission` FOREIGN KEY (`permission_id`) REFERENCES `tbl_permission` (`id`);