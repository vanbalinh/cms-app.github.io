<?php
namespace V1;

class PermissionRole extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_permission_role",
            (object) [
                "permission_id" => (object) ["type" => "int"],
                "role_id" => (object) ["type" => "int"],
            ]
        );
    }

    public function findRolesByPermissionId($permissionId)
    {
        $roleIds = array();
        $res = $this->_fetch((object) ["permission_id" => $permissionId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $pr) {
                array_push($roleIds, $pr->role_id);
            }
        }
        return $roleIds;
    }

    public function findPermissionsByRoleId($roleId)
    {
        $permissionIds = array();
        $res = $this->_fetch((object) ["role_id" => $roleId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $pr) {
                array_push($permissionIds, $pr->permission_id);
            }
        }
        return $permissionIds;
    }
}
