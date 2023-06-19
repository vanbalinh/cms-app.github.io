CREATE TABLE `tbl_page` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NULL,
  `name` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `modules` text COLLATE utf8_vietnamese_ci '[]',
  `properties` text COLLATE utf8_vietnamese_ci '[]',
  `is_home` tinyint(1) NOT NULL DEFAULT 0,
  `publish` tinyint(1) NOT NULL DEFAULT 0,
  `config` text COLLATE utf8_vietnamese_ci '{}',
  `description` text COLLATE utf8_vietnamese_ci NULL,
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
  `tbl_page`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_page`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;