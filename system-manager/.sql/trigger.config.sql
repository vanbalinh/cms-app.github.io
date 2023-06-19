--
--
--  Trigger
--
--
DROP TRIGGER IF EXISTS auto_insert_config_for_system_administrator;
DELIMITER $$ 
CREATE TRIGGER auto_insert_config_for_system_administrator
AFTER
INSERT
    ON tbl_config FOR EACH ROW BEGIN
INSERT INTO
    tbl_config_role(`role_id`, `config_id`)
VALUES
    (`1000000000`, new.id);

END $$ 
DELIMITER ;