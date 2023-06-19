CREATE TABLE `tbl_mobile_device` (
    `id` int(11) NOT NULL,
    `fcm_token` TEXT COLLATE utf8_vietnamese_ci NOT NULL,
    `os` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
    `auth_id` int(11) DEFAULT NULL,
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
    `tbl_mobile_device`
ADD
    PRIMARY KEY (`id`);

ALTER TABLE
    `tbl_mobile_device`
MODIFY
    `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 1000000000;

ALTER TABLE
    `tbl_mobile_device`
ADD
    CONSTRAINT `tbl_mobile_device_auth` FOREIGN KEY (`auth_id`) REFERENCES `tbl_auth` (`id`);