CREATE TABLE `tbl_forms_fields` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NULL,
  `name` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
  `type` enum(
    'text',
    'int',
    'float',
    'date',
    'date-time',
    'boolean',
    'upload',
    'reference'
  ) COLLATE utf8_vietnamese_ci NOT NULL DEFAULT 'text',
  `sort` int(11) NOT NULL DEFAULT 0,
  `description` text COLLATE utf8_vietnamese_ci NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_unique ` tinyint(1) NOT NULL DEFAULT 0,
  `is_parent` tinyint(1) NOT NULL DEFAULT 0,
  `is_multiple` tinyint(1) NOT NULL DEFAULT 0,
  `default_value` varchar(255) DEFAULT NULL,
  `min` text COLLATE utf8_vietnamese_ci DEFAULT NULL,
  `max` text COLLATE utf8_vietnamese_ci DEFAULT NULL,
  `display_on_list` tinyint(1) NOT NULL DEFAULT 1,
  `display_on_list_default` tinyint(1) NOT NULL DEFAULT 1,
  `form_col` enum(
    '1',
    '2',
    '3',
    '4',
    '5',
    '6',
    '7',
    '8',
    '9',
    '10',
    '11',
    '12'
  ) NOT NULL DEFAULT '12',
  `form_hidden` tinyint(1) NOT NULL DEFAULT 0,
  `reference_id` int(11) DEFAULT NULL,
  -- Sử dụng cho reference
  `label_key` varchar(255) COLLATE utf8_vietnamese_ci NULL,
  `relationship` enum(
    'self',
    '1-1',
    '1-n',
    'n-1',
    'n-n'
  ) COLLATE utf8_vietnamese_ci NOT NULL DEFAULT '1-n',
  -- Sử dụng cho select
  `sql_where` text COLLATE utf8_vietnamese_ci NULL,
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
  `tbl_forms_fields`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tbl_forms_fields`
MODIFY
  `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 1000000000;

ALTER TABLE
  `tbl_forms_fields`
ADD
  CONSTRAINT `tbl_forms_fields` FOREIGN KEY (`form_id`) REFERENCES `tbl_forms` (`id`);