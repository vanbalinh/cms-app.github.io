INSERT INTO
    `tbl_forms`(id, name, code)
VALUES
    (
        1000000000,
        "Nội dung",
        "noi-dung"
    );

INSERT INTO
    `tbl_forms_fields`(
        form_id,
        name,
        api_key
    )
VALUES
    (
        1000000000,
        "Nội dung",
        "noiDung"
    );

CREATE TABLE `tbl_data_form1000000000` (
    `id` int(11) NOT NULL,
    `noiDung` text COLLATE utf8_vietnamese_ci NULL,
    `group_data_id` int(11) DEFAULT NULL,
    `view` int(11) NOT NULL DEFAULT 0,
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
    `tbl_data_form1000000000`
ADD
    PRIMARY KEY (`id`);

ALTER TABLE
    `tbl_data_form1000000000`
MODIFY
    `id` int(11) NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 1000000000;

INSERT INTO
    `tbl_data_form1000000000`(noiDung, group_data_id)
VALUES
    (
        "Nội dung Trưởng phòng N1",
        1000000000
    );

INSERT INTO
    `tbl_data_form1000000000`(noiDung, group_data_id)
VALUES
    (
        "Nội dung Phó Trưởng phòng N1",
        1000000001
    );
INSERT INTO
    `tbl_data_form1000000000`(noiDung, group_data_id)
VALUES
    (
        "Nội dung Chuyên viên N1",
        1000000002
    );