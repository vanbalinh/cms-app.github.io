CREATE TABLE `tbl_group_data_share` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `data_id` int(11) NULL,
  `group_data_id` int(11) NOT NULL,
  `group_data_shared_id` int(11) NULL,
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
  `tbl_group_data_share`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_group_data_share`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;

ALTER TABLE
  `tbl_group_data_share`
ADD
  CONSTRAINT `tbl_group_data_share_form` FOREIGN KEY (`form_id`) REFERENCES `tbl_forms` (`id`);

ALTER TABLE
  `tbl_group_data_share`
ADD
  CONSTRAINT `tbl_group_data_share_group_data` FOREIGN KEY (`group_data_id`) REFERENCES `tbl_group_data` (`id`);
  
ALTER TABLE
  `tbl_group_data_share`
ADD
  CONSTRAINT `tbl_group_data_share_group_data_shared` FOREIGN KEY (`group_data_shared_id`) REFERENCES `tbl_group_data` (`id`);