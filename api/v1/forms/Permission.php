<?php
namespace V1\Form;

use \Config\Connect;
use \V1\Auth;

class Permission
{
    // Connection
    private $conn;
    private $connect;

    // Table
    private $db_table_permission_account = "tbl_permission_form_account";
    private $db_table_permission_group = "tbl_permission_form_group";
    private $db_table_permission_role = "tbl_permission_form_role";

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
        $accounts = $data->permissionAccounts;
        $groups = $data->permissionGroups;
        $roles = $data->permissionRoles;
        $sqlInsertAccount = "";
        $sqlInsertGroup = "";
        $sqlInsertRole = "";

        if (!$this->delete($formId)) {
            $success = false;
            goto end;
        }
        foreach ($accounts as $item) {
            $res = $this->conn->query(" INSERT INTO `" . $this->db_table_permission_account . "` (`form_id`, `account_id`, `view`, `create`, `update`, `delete` ) "
                . " VALUES (" . $formId . ", " . $item->id . ", " . ($item->view ? 1 : 0) . ", " . ($item->create ? 1 : 0) . ", " . ($item->update ? 1 : 0) . ", " . ($item->delete ? 1 : 0) . "); ");
            if (!$res) {
                $success = false;
                goto end;
            }
        }
        foreach ($groups as $item) {
            $res = $this->conn->query(" INSERT INTO `" . $this->db_table_permission_group . "` (`form_id`, `group_id`, `view`, `create`, `update`, `delete` ) "
                . " VALUES (" . $formId . ", " . $item->id . ", " . ($item->view ? 1 : 0) . ", " . ($item->create ? 1 : 0) . ", " . ($item->update ? 1 : 0) . ", " . ($item->delete ? 1 : 0) . "); ");
            if (!$res) {
                $success = false;
                goto end;
            }
        }
        foreach ($roles as $item) {
            $res = $this->conn->query(" INSERT INTO `" . $this->db_table_permission_role . "` (`form_id`, `role_id`, `view`, `create`, `update`, `delete` ) "
                . " VALUES (" . $formId . ", " . $item->id . ", " . ($item->view ? 1 : 0) . ", " . ($item->create ? 1 : 0) . ", " . ($item->update ? 1 : 0) . ", " . ($item->delete ? 1 : 0) . "); ");
            if (!$res) {
                $success = false;
                goto end;
            }
        }
        end:
        return $success;
    }

    // DELETE
    public function delete($formId)
    {
        $res1 = $this->conn->query("DELETE from `" . $this->db_table_permission_account . "` WHERE `form_id` = " . $formId);
        $res2 = $this->conn->query("DELETE from `" . $this->db_table_permission_group . "` WHERE `form_id` = " . $formId);
        $res3 = $this->conn->query("DELETE from `" . $this->db_table_permission_role . "` WHERE `form_id` = " . $formId);
        if ($res1 && $res2 && $res3) {
            return true;
        } else {
            return false;
        }
    }

    public function getPermission($formId)
    {
        $auth = new Auth();
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $accountId = $jwt->user->id;
        } else {
            $accountId = null;
        }
        $view = false;
        $create = false;
        $update = false;
        $delete = false;
        if (!is_null($accountId)) {
            $sql1 = "SELECT
                    p_a.view,
                    p_a.create,
                    p_a.update,
                    p_a.delete
                FROM
                    tbl_forms as f,
                    tbl_permission_form_account as p_a,

                    tbl_accounts as a
                WHERE
                    f.deleted = 0
                    AND p_a.deleted = 0
                    AND a.deleted = 0
                    AND p_a.account_id = a.id
                    AND p_a.form_id = f.id
                    AND p_a.form_id = " . $formId . "
                    " . (is_null($accountId) ? " AND p_a.account_id is null" : " AND p_a.account_id = " . $accountId) . "
            ";
            $stmt = $this->conn->query($sql1);
            while ($dataRow = $stmt->fetch_assoc()) {
                if ($dataRow["view"] === "1") {
                    $view = true;
                }
                if ($dataRow["create"] === "1") {
                    $create = true;
                }
                if ($dataRow["update"] === "1") {
                    $update = true;
                }
                if ($dataRow["delete"] === "1") {
                    $delete = true;
                }
            }
            $sql3 = "SELECT
                    p_r.view,
                    p_r.create,
                    p_r.update,
                    p_r.delete

                FROM
                    tbl_forms as f,
                    tbl_permission_form_role as p_r,

                    tbl_accounts as a,
                    tbl_role as r
                WHERE
                    f.deleted = 0
                    AND p_r.deleted = 0
                    AND a.deleted = 0
                    AND r.deleted = 0
                    AND p_r.role_id = r.id
                    AND a.role_id = r.id
                    AND p_r.form_id = f.id
                    AND p_r.form_id = " . $formId . "
                    " . (is_null($accountId) ? " AND a.id is null" : " AND a.id = " . $accountId) . "
            ";
            $stmt = $this->conn->query($sql3);
            while ($dataRow = $stmt->fetch_assoc()) {
                if ($dataRow["view"] === "1") {
                    $view = true;
                }
                if ($dataRow["create"] === "1") {
                    $create = true;
                }
                if ($dataRow["update"] === "1") {
                    $update = true;
                }
                if ($dataRow["delete"] === "1") {
                    $delete = true;
                }
            }
        }
        if (!is_null($accountId)) {
            $sql2 = "SELECT
                    p_g.view,
                    p_g.create,
                    p_g.update,
                    p_g.delete

                FROM
                    tbl_forms as f,
                    tbl_permission_form_group as p_g,

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
                    AND p_g.form_id = f.id
                    AND p_g.form_id = " . $formId . "
                    AND g_a.account_id = " . $accountId . "
            ";
        } else {
            $sql2 = "SELECT
                    p_g.view,
                    p_g.create,
                    p_g.update,
                    p_g.delete

                FROM
                    tbl_forms as f,
                    tbl_permission_form_group as p_g,

                    tbl_group as g
                WHERE
                    f.deleted = 0
                    AND p_g.deleted = 0
                    AND g.deleted = 0
                    AND p_g.group_id = g.id
                    AND p_g.form_id = f.id
                    AND p_g.form_id = " . $formId . "
                    AND g.code = '" . GROUP_GUEST . "'
            ";
        }
        $stmt = $this->conn->query($sql2);
        while ($dataRow = $stmt->fetch_assoc()) {
            if ($dataRow["view"] === "1") {
                $view = true;
            }
            if ($dataRow["create"] === "1") {
                $create = true;
            }
            if ($dataRow["update"] === "1") {
                $update = true;
            }
            if ($dataRow["delete"] === "1") {
                $delete = true;
            }
        }

        return (object) [
            "view" => $view,
            "create" => $create,
            "update" => $update,
            "delete" => $delete,
        ];
    }
}
