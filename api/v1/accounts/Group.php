<?php
namespace V1;

class Group extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_group",
            (object) [
                "parent_id" => (object) [
                    "name" => "parent",
                ],
                "code" => (object) [
                    "required" => true,
                    "unique" => true,
                ],
                "name" => (object) [
                    "required" => true,
                ],
                "description" => (object) [],
                "sort" => (object) [
                    "type" => "int",
                ],
            ],
            (object) [
                "selfKey" => "parent",
            ]
        );
    }

    public function apiTree($pathData, $getData, $bodyData)
    {
        return $this->_tree($getData);
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData, $showItemInfo = true)
    {
        $res = $this->_find($pathData);
        if ($res->error === 0) {
            $acc = new Account;
            $auth = new Auth;
            $groupAccount = new GroupAccount;
            $accountIds = $groupAccount->findAccountsByGroupId($res->data->data->id);
            $res->data->accounts = [];
            $res->data->permissions = [];

            if (count($accountIds) > 0) {
                $resAccount = $acc->apiFetchAll(
                    (object) [],
                    (object) ["id_in" => implode(",", $accountIds), "fields" => "id,username,firstName,lastName"],
                    (object) []
                );

                if ($resAccount->error === 0 && count($resAccount->data->data) > 0) {
                    $res->data->accounts = $resAccount->data->data;
                }
            }
            if (isset($this->auth->jwt->user->id) && isset($this->auth->jwt->user->role_id)) {
                $res->data->permissions = $auth->fetchAllPermission($this->auth->jwt->user->id, $this->auth->jwt->user->role_id);
            }
        }
        return $res;
    }

    public function apiFindByCode($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData, ["code"]);
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
