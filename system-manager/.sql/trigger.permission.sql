--
--
--  Trigger
--
--
DROP TRIGGER IF EXISTS auto_insert_permission_for_system_administrator;
DELIMITER $$ 
CREATE TRIGGER auto_insert_permission_for_system_administrator
AFTER
INSERT
    ON tbl_permission FOR EACH ROW BEGIN
INSERT INTO
    tbl_permission_role(role_id, permission_id)
VALUES
    (1000000000, new.id);

END $$ 
DELIMITER ;

--
--  After table
--
DROP TRIGGER IF EXISTS auto_update_permission_for_system_administrator;
DELIMITER $$ 
CREATE TRIGGER auto_update_permission_for_system_administrator
AFTER
UPDATE ON `tbl_permission` 
    FOR EACH ROW BEGIN 
        IF(old.deleted <> new.deleted) THEN
            UPDATE tbl_permission_role
            SET deleted = new.deleted
            WHERE role_id = 1000000000  AND permission_id = new.id;
        END IF;

END $$ 
DELIMITER ;

DROP TRIGGER IF EXISTS check_sub_title_permission;
DELIMITER $$ 
CREATE TRIGGER check_sub_title_permission
AFTER
INSERT
    ON tbl_permission FOR EACH ROW BEGIN
    DECLARE sub_title varchar(255) DEFAULT new.name;
    IF (ISNULL(new.sub_title)) THEN
    UPDATE tbl_permission
            SET sub_title = new.name
            WHERE id = new.id;
    END IF;
END $$ 
DELIMITER ;