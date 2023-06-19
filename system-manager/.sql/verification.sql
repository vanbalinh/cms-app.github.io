CREATE TABLE `tbl_verification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `code` text COLLATE utf8_vietnamese_ci NOT NULL,
  `action` enum('CREATE_PASSWORD', 'CHANGE_PASSWORD', 'REGISTER') COLLATE utf8_vietnamese_ci NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_vietnamese_ci;

ALTER TABLE
  `tbl_verification`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_verification`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;