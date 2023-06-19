<?php
namespace V1\Form;

use \Config\Connect;
use \Config\SystemConfig;
use \V1\Auth;

class PermissionField
{
    // Connection
    private $conn;
    private $connect;

    // Table
    private $db_table_permission_account = "tbl_permission_form_field_account";
    private $db_table_permission_group = "tbl_permission_form_field_group";
    private $db_table_permission_role = "tbl_permission_form_field_role";

    public function __construct()
    {
        $this->connect = new Connect;
        $this->conn = $this->connect->conn;
    }
    // create
    public function create($data)
    {
        $success = true;
        $formId = $data->formId;
        $formFieldId = $data->formFieldId;
        $accounts = $data->permissionAccounts;
        $groups = $data->permissionGroups;
        $roles = $data->permissionRoles;
        $sqlInsertAccount = "";
        $sqlInsertGroup = "";
        $sqlInsertRole = "";

        if (!$this->delete($formId, array($formFieldId))) {
            $success = false;
            goto end;
        }
        foreach ($accounts as $item) {
            $res = $this->conn->query(" INSERT INTO `" . $this->db_table_permission_account . "` (`form_id`, `form_field_id`, `account_id`, `view`, `create`, `update` ) "
                . " VALUES (" . $formId . ", " . $formFieldId . ", " . $item->id . ", " . ($item->view ? 1 : 0) . ", " . ($item->create ? 1 : 0) . ", " . ($item->update ? 1 : 0) . "); ");
            if (!$res) {
                $success = false;
                goto end;
            }
        }
        foreach ($groups as $item) {
            $res = $this->conn->query(" INSERT INTO `" . $this->db_table_permission_group . "` (`form_id`, `form_field_id`, `group_id`, `view`, `create`, `update` ) "
                . " VALUES (" . $formId . ", " . $formFieldId . ", " . $item->id . ", " . ($item->view ? 1 : 0) . ", " . ($item->create ? 1 : 0) . ", " . ($item->update ? 1 : 0) . "); ");
            if (!$res) {
                $success = false;
                goto end;
            }
        }
        foreach ($roles as $item) {
            $res = $this->conn->query(" INSERT INTO `" . $this->db_table_permission_role . "` (`form_id`, `form_field_id`, `role_id`, `view`, `create`, `update` ) "
                . " VALUES (" . $formId . ", " . $formFieldId . ", " . $item->id . ", " . ($item->view ? 1 : 0) . ", " . ($item->create ? 1 : 0) . ", " . ($item->update ? 1 : 0) . "); ");
            if (!$res) {
                $success = false;
                goto end;
            }
        }
        end:
        return $success;
    }

    // DELETE
    public function delete($formId, $formFieldIds = array())
    {
        $res1 = $this->conn->query("DELETE from `" . $this->db_table_permission_account . "` WHERE `form_id` = " . $formId . " AND `form_field_id` in (" . implode(", ", ($formFieldIds)) . ")");
        $res2 = $this->conn->query("DELETE from `" . $this->db_table_permission_group . "` WHERE `form_id` = " . $formId . " AND `form_field_id` in (" . implode(", ", ($formFieldIds)) . ")");
        $res3 = $this->conn->query("DELETE from `" . $this->db_table_permission_role . "` WHERE `form_id` = " . $formId . " AND `form_field_id` in (" . implode(", ", ($formFieldIds)) . ")");
        if ($res1 && $res2 && $res3) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteByFormIds($formIds, $deleteByFormIds = array())
    {
        $res1 = $this->conn->query("DELETE from `" . $this->db_table_permission_account . "` WHERE `form_id` in (" . implode(", ", $formIds) . ") AND `form_field_id` not in (" . implode(", ", $deleteByFormIds) . ")");
        $res2 = $this->conn->query("DELETE from `" . $this->db_table_permission_group . "` WHERE `form_id` in (" . implode(", ", $formIds) . ") AND `form_field_id` not in (" . implode(", ", $deleteByFormIds) . ")");
        $res3 = $this->conn->query("DELETE from `" . $this->db_table_permission_role . "` WHERE `form_id` in (" . implode(", ", $formIds) . ") AND `form_field_id` not in (" . implode(", ", $deleteByFormIds) . ")");
        if ($res1 && $res2 && $res3) {
            return true;
        } else {
            return false;
        }
    }

    public function getPermission($fieldId)
    {
        $config = new SystemConfig;
        $vDefault = $config->find("PERMISSION_FORM_FIELD_DEFAULT_ALLOW_ACCESS");
        $allowDefault = is_null($vDefault) ? false : ($vDefault->value === "1" ? true : false);
        $auth = new Auth();
        $accountId = null;
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $accountId = (int) $jwt->user->id;
        }

        $viewTrue = false;
        $createTrue = false;
        $updateTrue = false;

        $viewFalse = false;
        $createFalse = false;
        $updateFalse = false;
        if (!is_null($accountId)) {
            $sql1 = "SELECT
                    p_a.view,
                    p_a.create,
                    p_a.update
                FROM
                    tbl_forms_fields as f,
                    tbl_permission_form_field_account as p_a,

                    tbl_accounts as a
                WHERE
                    f.deleted = 0
                    AND p_a.deleted = 0
                    AND a.deleted = 0
                    AND p_a.account_id = a.id
                    AND p_a.form_field_id = f.id
                    AND p_a.form_field_id = " . $fieldId . "
                    " . (is_null($accountId) ? " AND p_a.account_id is null" : " AND p_a.account_id = " . $accountId) . "
            ";
            $stmt = $this->conn->query($sql1);
            while ($dataRow = $stmt->fetch_assoc()) {
                if ($dataRow["view"] === "1") {
                    $viewTrue = true;
                } else {
                    $viewFalse = true;
                }
                if ($dataRow["create"] === "1") {
                    $createTrue = true;
                } else {
                    $createFalse = true;
                }
                if ($dataRow["update"] === "1") {
                    $updateTrue = true;
                } else {
                    $updateFalse = true;
                }
            }
            $sql3 = "SELECT
                    p_r.view,
                    p_r.create,
                    p_r.update

                FROM
                    tbl_forms_fields as f,
                    tbl_permission_form_field_role as p_r,

                    tbl_accounts as a,
                    tbl_role as r
                WHERE
                    f.deleted = 0
                    AND p_r.deleted = 0
                    AND a.deleted = 0
                    AND r.deleted = 0
                    AND p_r.role_id = r.id
                    AND a.role_id = r.id
                    AND p_r.form_field_id = f.id
                    AND p_r.form_field_id = " . $fieldId . "
                    " . (is_null($accountId) ? " AND a.id is null" : " AND a.id = " . $accountId) . "
            ";
            $stmt = $this->conn->query($sql3);
            while ($dataRow = $stmt->fetch_assoc()) {
                if ($dataRow["view"] === "1") {
                    $viewTrue = true;
                } else {
                    $viewFalse = true;
                }
                if ($dataRow["create"] === "1") {
                    $createTrue = true;
                } else {
                    $createFalse = true;
                }
                if ($dataRow["update"] === "1") {
                    $updateTrue = true;
                } else {
                    $updateFalse = true;
                }
            }
        }
        $sql2 = "SELECT
                    p_g.view,
                    p_g.create,
                    p_g.update

                FROM
                    tbl_forms_fields as f,
                    tbl_permission_form_field_group as p_g,

                    tbl_accounts as a,
                    tbl_group as g,
                    tbl_group_account as g_a
                WHERE
                    f.deleted = 0
                    AND p_g.deleted = 0
                    AND a.deleted = 0
                    AND g.deleted = 0
                    AND g_a.deleted = 0
                    AND p_g.group_id = g.id
                    AND p_g.form_field_id = f.id
                    AND p_g.form_field_id = " . $fieldId . "
                    " . (is_null($accountId) ? " AND g.code = ' " . GROUP_GUEST . " '" : " AND g_a.account_id = " . $accountId) . "
            ";
        $stmt = $this->conn->query($sql2);
        while ($dataRow = $stmt->fetch_assoc()) {
            if ($dataRow["view"] === "1") {
                $viewTrue = true;
            } else {
                $viewFalse = true;
            }
            if ($dataRow["create"] === "1") {
                $createTrue = true;
            } else {
                $createFalse = true;
            }
            if ($dataRow["update"] === "1") {
                $updateTrue = true;
            } else {
                $updateFalse = true;
            }
        }

        $view = $viewTrue ? true : ($viewFalse ? false : $allowDefault);
        $create = $createTrue ? true : ($createFalse ? false : $allowDefault);
        $update = $updateTrue ? true : ($updateFalse ? false : $allowDefault);

        return (object) [
            "view" => $view,
            "create" => $create,
            "update" => $update,
        ];
    }
}
