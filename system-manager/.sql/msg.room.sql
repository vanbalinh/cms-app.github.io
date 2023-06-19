CREATE TABLE `tbl_msg_room` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `type` enum('GROUP', 'INDIVIDUAL') COLLATE utf8_vietnamese_ci NOT NULL DEFAULT 'INDIVIDUAL',
  `topic` varchar(255) COLLATE utf8_vietnamese_ci NULL,
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
  `tbl_msg_room`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_msg_room`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;