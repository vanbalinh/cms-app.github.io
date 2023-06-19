<?php
namespace V1\Auth;

class AuthPermission
{
    private $conn;
    private $connect;
    public function __construct()
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
    }

    private function getIdPermission($accountId, $roleId)
    {
        // Account
        $sql1 = "SELECT
            DISTINCT p.id
        FROM tbl_permission as p,
            tbl_accounts as t_a,
            tbl_permission_account as t_p_a

        WHERE
            p.deleted = 0
            AND t_a.deleted = 0
            AND t_p_a.deleted = 0
            AND t_p_a.permission_id = p.id
            ANd t_a.id = t_p_a.account_id
            AND t_p_a.account_id = " . $accountId . "
        ";
        // Role
        $sql2 = "SELECT
            DISTINCT p.id
        FROM tbl_permission as p,

            tbl_role as t_r,
            tbl_accounts as t_a,
            tbl_permission_role as t_p_r
        WHERE
            p.deleted = 0
            AND t_p_r.role_id = t_r.id
            AND t_p_r.permission_id = p.id

            AND t_r.deleted = 0
            AND t_p_r.deleted = 0
            AND t_a.deleted = 0
            AND t_a.role_id = t_r.id
            AND t_a.id = " . $accountId . "
            AND t_r.id = " . $roleId . "
        ";
        // group
        $sql3 = "SELECT
            DISTINCT p.id
        FROM tbl_permission as p,
            tbl_group as t_g,
            tbl_group_account as t_g_a,
            tbl_accounts as t_a_p_g,
            tbl_permission_group as t_p_g
        WHERE
            p.deleted = 0
            AND t_g.deleted = 0
            AND t_g_a.deleted = 0
            AND t_p_g.deleted = 0
            AND t_a_p_g.deleted = 0
            AND t_g.id = t_g_a.group_id
            AND t_g_a.account_id = t_a_p_g.id
            AND t_p_g.permission_id = p.id
            AND t_p_g.group_id = t_g.id
            AND t_g_a.account_id = " . $accountId . "
            ";
        $result = array();
        $resultIds = array(0);
        $stmt1 = $this->conn->query($sql1);
        $stmt2 = $this->conn->query($sql2);
        $stmt3 = $this->conn->query($sql3);
        while ($row = $stmt1->fetch_assoc()) {
            if (!in_array((int) $row["id"], $resultIds)) {
                array_push($resultIds, (int) $row["id"]);
            }
        }
        while ($row = $stmt2->fetch_assoc()) {
            if (!in_array((int) $row["id"], $resultIds)) {
                array_push($resultIds, (int) $row["id"]);
            }
        }
        while ($row = $stmt3->fetch_assoc()) {
            if (!in_array((int) $row["id"], $resultIds)) {
                array_push($resultIds, (int) $row["id"]);
            }
        }
        return $resultIds;
    }

    public function fetchAllPermission($accountId, $roleId)
    {
        $ids = $this->getIdPermission($accountId, $roleId);
        if (count($ids) > 0) {
            $permission = new \V1\Permission;
            $res = $permission->_fetch((object) ["id_in" => implode(",", $ids)]);
            if ($res->error === 0) {
                return $res->data->data;
            }
        }
        return [];
    }

    public function fetchAllMenu($accountId, $roleId)
    {
        $ids = $this->getIdPermission($accountId, $roleId);
        if (count($ids) > 0) {
            $menu = new \V1\Menu;
            $res = $menu->findMenuTreeByPermissionIds($ids);
            if ($res->error === 0) {
                return $res->data->data;
            }
        }
        return [];
    }

    public function allowAccess($controllerMethod, $roles = null, $groups = null)
    {
        $res = false;
        $permission = new \V1\Permission;
        $ids = $permission->findIdsByControllerMethod(str_replace('\\', '_', $controllerMethod));
        $ids = array();
        if (count($ids) === 0) {
            return true;
        } else {
            // Account
            $auth = new \V1\Auth;
            if ($auth->check()) {
                $jwt = $auth->jwt;
                $accountId = $jwt->user->id;
            }
            $sql1 = "SELECT
                DISTINCT p.id
            FROM tbl_permission as p,
                tbl_accounts as t_a,
                tbl_permission_account as t_p_a

            WHERE
                p.deleted = 0
                AND p.id in (" . implode(", ", $ids) . ")
                AND p.controller_method is not null
                AND t_p_a.permission_id = p.id
                ANd t_a.id = t_p_a.account_id
                AND t_a.deleted = 0
                AND t_p_a.deleted = 0
                " . (isset($accountId) ? " AND t_p_a.account_id = " . $accountId : "") . "
            ";
            // Role
            $sql2 = "SELECT
                DISTINCT p.id
            FROM tbl_permission as p,

                tbl_role as t_r,
                tbl_accounts as t_a_p_r,
                tbl_permission_role as t_p_r
            WHERE
                p.deleted = 0
                AND p.id in (" . implode(", ", $ids) . ")
                AND p.controller_method is not null
                AND t_p_r.role_id = t_r.id
                AND t_p_r.permission_id = p.id

                AND t_r.deleted = 0
                AND t_p_r.deleted = 0
                " . (isset($accountId) ? " AND t_a_p_r.role_id = t_r.id AND t_a_p_r.id = " . $accountId : " AND t_r.code = '" . ROLE_GUEST . "'") . "
                " . (!is_null($roles) && is_array($roles) ? "AND t_r.code in (" . implode(", ", $roles) . ")" : "") . "
            ";
            // group
            $sql3 = "SELECT
                DISTINCT p.id
            FROM tbl_permission as p,
                tbl_group as t_g,
                tbl_group_account as t_g_a,
                tbl_accounts as t_a_p_g,
                tbl_permission_group as t_p_g
            WHERE
                p.deleted = 0
                AND p.id in (" . implode(", ", $ids) . ")
                AND p.controller_method is not null
                AND t_g.id = t_g_a.group_id
                AND t_g_a.account_id = t_a_p_g.id
                AND t_p_g.permission_id = p.id
                AND t_p_g.group_id = t_g.id
                AND t_g.deleted = 0
                AND t_g_a.deleted = 0
                AND t_p_g.deleted = 0
                AND t_a_p_g.deleted = 0
                " . (!is_null($groups) && is_string($groups) ? "AND t_r.code in (" . implode(", ", $groups) . ")" : "") . "
                " . (isset($accountId) ? " AND t_g_a.account_id = " . $accountId : " ") . "
                ";
            if ($this->conn->query($sql1)->num_rows === 0 && $this->conn->query($sql2)->num_rows === 0 && $this->conn->query($sql3)->num_rows === 0) {
                return false;
            }
            return true;
        }
    }
}
