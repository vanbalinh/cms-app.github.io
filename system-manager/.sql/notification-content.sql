CREATE TABLE `tbl_notification_content` (
    `id` int(11) NOT NULL,
    `content` text COLLATE utf8_vietnamese_ci NULL,
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
    `tbl_notification_content`
ADD
    PRIMARY KEY (`id`);

ALTER TABLE
    `tbl_notification_content`
MODIFY
    `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 1000000000;