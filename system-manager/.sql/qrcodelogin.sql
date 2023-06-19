CREATE TABLE `tbl_qrcode_login` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `code` text COLLATE utf8_vietnamese_ci NOT NULL,
  `client_id` text COLLATE utf8_vietnamese_ci NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_vietnamese_ci;

ALTER TABLE
  `tbl_qrcode_login`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_qrcode_login`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;