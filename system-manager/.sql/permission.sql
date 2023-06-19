CREATE TABLE `tbl_permission` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `icon_class` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `description` TEXT COLLATE utf8_vietnamese_ci NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `controller_method` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `sub_title` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `hide_sub_title` tinyint(1) NOT NULL DEFAULT 0,
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
  `tbl_permission`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_permission`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;