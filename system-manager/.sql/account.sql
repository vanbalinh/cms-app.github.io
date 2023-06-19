CREATE TABLE `tbl_accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `first_name` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `last_name` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `email` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `birthday` date DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `group_data_id` int(11) DEFAULT NULL,
  `fb_id` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `gg_id` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `zl_id` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `allow_update_username` tinyint(1) NOT NULL DEFAULT 0,
  `locale` varchar(10) COLLATE utf8_vietnamese_ci NOT NULL,
  `avatar_url` TEXT COLLATE utf8_vietnamese_ci DEFAULT 'DEFAULT_AVATAR',
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
  `tbl_accounts`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_accounts`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;

ALTER TABLE
  `tbl_accounts`
ADD
  CONSTRAINT `tbl_accounts_role` FOREIGN KEY (`role_id`) REFERENCES `tbl_role` (`id`);

ALTER TABLE
  `tbl_accounts`
ADD
  CONSTRAINT `tbl_accounts_group_data` FOREIGN KEY (`group_data_id`) REFERENCES `tbl_group_data` (`id`);