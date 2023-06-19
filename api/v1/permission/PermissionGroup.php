<?php
namespace V1;

class PermissionGroup extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_permission_group",
            (object) [
                "permission_id" => (object) ["type" => "int"],
                "group_id" => (object) ["type" => "int"],
            ]
        );
    }

    public function findGroupsByPermissionId($permissionId)
    {
        $groupIds = array();
        $res = $this->_fetch((object) ["permission_id" => $permissionId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $pg) {
                array_push($groupIds, $pg->group_id);
            }
        }
        return $groupIds;
    }

    public function findPermissionsByGroupId($groupId)
    {
        $permissionIds = array();
        $res = $this->_fetch((object) ["group_id" => $groupId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $pg) {
                array_push($permissionIds, $pg->permission_id);
            }
        }
        return $permissionIds;
    }

    public function findPermissionsByGroupIds($groupIds)
    {
        $permissionIds = array();
        if (is_array($groupIds) && count($groupIds) > 0) {
            $res = $this->_fetch((object) ["group_id_in" => implode(",", $groupIds)]);
            if ($res->error === 0) {
                foreach ($res->data->data as $pg) {
                    array_push($permissionIds, $pg->permission_id);
                }
            }
        }
        return $permissionIds;
    }
}
