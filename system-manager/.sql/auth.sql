CREATE TABLE `tbl_auth` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `token` text COLLATE utf8_vietnamese_ci NOT NULL,
  `user_agent` text COLLATE utf8_vietnamese_ci NOT NULL,
  `device_ip` varchar(16) COLLATE utf8_vietnamese_ci NULL,
  `device_name` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `device_version` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `device_platform` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `device_system` text COLLATE utf8_vietnamese_ci NULL,
  `device_id` text COLLATE utf8_vietnamese_ci NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_vietnamese_ci;

ALTER TABLE
  `tbl_auth`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_auth`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;

ALTER TABLE
  `tbl_auth`
ADD
  CONSTRAINT `tbl_auth_accounts` FOREIGN KEY (`account_id`) REFERENCES `tbl_accounts` (`id`);