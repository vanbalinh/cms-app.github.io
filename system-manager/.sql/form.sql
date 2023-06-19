CREATE TABLE `tbl_forms` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NULL,
  `name` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_vietnamese_ci NULL DEFAULT NULL,
  `description` text COLLATE utf8_vietnamese_ci NULL,
  `check_access` tinyint(1) NOT NULL DEFAULT 1,
  `show_view` tinyint(1) NOT NULL DEFAULT 0,
  `config` text COLLATE utf8_vietnamese_ci NULL,
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
  `tbl_forms`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_forms`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;