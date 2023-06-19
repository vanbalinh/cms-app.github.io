<?php
namespace V1;

class Permission extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_permission",
            (object) [
                "parent_id" => (object) [
                    "name" => "parent",
                ],
                "code" => (object) [],
                "name" => (object) [],
                "url" => (object) [],
                "icon_class" => (object) [
                    "name" => "iconClass",
                ],
                "description" => (object) [],
                "sort" => (object) [
                    "type" => "int",
                ],
                // "controller_method" => (object) [
                //     "name" => "controllerMethod",
                // ],
                // "sub_title" => (object) [
                //     "name" => "subTitle",
                // ],
                // "hide_sub_title" => (object) [
                //     "name" => "hiddenSubTitle",
                //     "type" => "boolean",
                // ],
            ],
            (object) [
                "selfKey" => "parent",
                "selfFields" => ["code", "name"],
            ]
        );
    }

    public function findIdsByControllerMethod($controllerMethod)
    {
        $this->init("tbl_permission", (object) ["controller_method" => (object) ["name" => "controllerMethod"]], (object) ["hiddenItemInfo" => true]);
        $res = $this->_fetch((object) ["controllerMethod" => $controllerMethod]);
        $ids = array();
        if ($res->error === 0) {
            foreach ($res->data->data as $p) {
                array_push($ids, $p->id);
            }
        }
        return $ids;
    }

    /**
     *  FNC API
     */
    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFetchAllTree($pathData, $getData, $bodyData)
    {
        return $this->_tree($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        $res = $this->_find($pathData);
        // Code
        if ($res->error === 0) {
            $res->data->accounts = [];
            $res->data->groups = [];
            $res->data->roles = [];

            $pId = $res->data->data->id;
            $pA = new PermissionAccount;
            $pG = new PermissionGroup;
            $pR = new PermissionRole;
            $account = new Account;
            $group = new Group;
            $role = new Role;

            $accountIds = $pA->findAccountsByPermissionId($pId);
            $groupIds = $pG->findGroupsByPermissionId($pId);
            $roleIds = $pR->findRolesByPermissionId($pId);
            if (count($accountIds) > 0) {
                $resAccounts = $account->apiFetchAll(
                    (object) [],
                    (object) [
                        "id_in" => implode(",", $accountIds),
                        "fields" => "code,name",
                        "_hiddenItemInfo" => true,
                    ],
                    (object) []
                );
                if ($resAccounts->error === 0) {
                    $res->data->accounts = $resAccounts->data->data;
                }
            }
            if (count($groupIds) > 0) {
                $resGroups = $group->apiFetchAll(
                    (object) [],
                    (object) [
                        "id_in" => implode(",", $groupIds),
                        "fields" => "code,name",
                        "_hiddenItemInfo" => true,
                    ],
                    (object) []
                );
                if ($resGroups->error === 0) {
                    $res->data->groups = $resGroups->data->data;
                }
            }
            if (count($roleIds) > 0) {
                $resRoles = $role->apiFetchAll(
                    (object) [],
                    (object) [
                        "id_in" => implode(",", $roleIds),
                        "fields" => "code,name",
                        "_hiddenItemInfo" => true,
                    ],
                    (object) []
                );
                if ($resRoles->error === 0) {
                    $res->data->roles = $resRoles->data->data;
                }
            }
        }
        return $res;
    }

    public function apiCreate($pathData, $getData, $bodyData)
    {
        return $this->_create($bodyData);
    }

    public function apiUpdate($pathData, $getData, $bodyData)
    {
        return $this->_update($bodyData);
    }

    public function apiDelete($pathData, $getData, $bodyData)
    {
        return $this->_delete($bodyData);
    }

}
