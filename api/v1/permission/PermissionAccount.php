<?php
namespace V1;

class PermissionAccount extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_permission_account",
            (object) [
                "permission_id" => (object) ["type" => "int"],
                "account_id" => (object) ["type" => "int"],
            ]
        );
    }

    public function findAccountsByPermissionId($permissionId)
    {
        $accountIds = array();
        $res = $this->_fetch((object) ["permission_id" => $permissionId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $pa) {
                array_push($accountIds, $pa->account_id);
            }
        }
        return $accountIds;
    }

    public function findPermissionsByAccountId($accountId)
    {
        $permissionIds = array();
        $res = $this->_fetch((object) ["account_id" => $accountId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $pa) {
                array_push($permissionIds, $pa->permission_id);
            }
        }
        return $permissionIds;
    }
}
