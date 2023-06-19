CREATE TABLE `tbl_room_message` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
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
  `tbl_room_message`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_room_message`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;

ALTER TABLE
  `tbl_room_message`
ADD
  CONSTRAINT `tbl_room_message_message` FOREIGN KEY (`message_id`) REFERENCES `tbl_messages` (`id`);

ALTER TABLE
  `tbl_room_message`
ADD
  CONSTRAINT `tbl_room_message_room` FOREIGN KEY (`room_id`) REFERENCES `tbl_rooms` (`id`);

ALTER TABLE
  `tbl_room_message`
ADD
  UNIQUE `unique_index`(`room_id`, `message_id`);