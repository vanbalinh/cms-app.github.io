CREATE TABLE `tbl_routers` (
    `id` int(11) NOT NULL,
    `path` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
    `auth` tinyint(1) NOT NULL DEFAULT 0,
    `method` enum(
        'GET',
        'POST',
        'PUT',
        'DELETE'
    ) COLLATE utf8_vietnamese_ci NOT NULL DEFAULT 'GET',
    `namespace` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
    `function_name` varchar(255) COLLATE utf8_vietnamese_ci NOT NULL,
    `msg_success` text COLLATE utf8_vietnamese_ci NULL,
    `msg_error` text COLLATE utf8_vietnamese_ci NULL,
    `count_call` int(11) NOT NULL DEFAULT 0,
    `realtime` tinyint(1) NOT NULL DEFAULT 0,
    `chanel` varchar(255) COLLATE utf8_vietnamese_ci NULL,
    `event` varchar(255) COLLATE utf8_vietnamese_ci NULL,
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
    `tbl_routers`
ADD
    PRIMARY KEY (`id`);

ALTER TABLE
    `tbl_routers`
MODIFY
    `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 1000000000;