<?php
namespace V1;

class Account extends \Controller

{
    private $_by;
    public function __construct()
    {
        parent::__construct();
        $this->_by = (object) [];
        $systemConfig = $this->getSystemConfig("DEFAULT_AVATAR");
        $avatarUrlConfig = (object) [];
        $avatarUrlConfig->name = "avatarUrl";
        if (isset($systemConfig->DEFAULT_AVATAR)) {
            $avatarUrlConfig->replace_value = ["DEFAULT_AVATAR" => $systemConfig->DEFAULT_AVATAR];
        }
        $this->init(
            "tbl_accounts",
            (object) [
                "username" => (object) [
                    "required" => true,
                    "unique" => true,
                ],
                "password" => (object) [
                    "public" => false,
                ],
                "first_name" => (object) [
                    "required" => true,
                    "name" => "firstName",
                ],
                "last_name" => (object) [
                    "required" => true,
                    "name" => "lastName",
                ],
                "phone" => (object) [],
                "email" => (object) [
                    "required" => true,
                    "unique" => true,
                ],
                "birthday" => (object) [
                    "type" => "date",
                ],
                "role_id" => (object) [
                    "required" => true,
                    "name" => "role",
                    "type" => "object",
                    "object_name" => "V1\\Role",
                    "object_fields" => ["code", "name"],
                ],
                "group_data_id" => (object) [
                    "name" => "groupDataId",
                    "type" => "int",
                ],
                "fb_id" => (object) [
                    "name" => "fbId",
                    "public" => false,
                ],
                "gg_id" => (object) [
                    "name" => "ggId",
                    "public" => false,
                ],
                "zl_id" => (object) [
                    "name" => "zlId",
                    "public" => false,
                ],
                "allow_update_username" => (object) [
                    "name" => "allowUpdateUsername",
                    "type" => "boolean",
                ],
                "locale" => (object) [],
                "avatar_url" => $avatarUrlConfig,
            ]
        );
    }

    public function findById($id = null)
    {
        if (is_numeric($id)) {
            $res = $this->_find((object) ["id" => $id]);
            if ($res->error === 0) {
                return $res->data->data;
            }
        }
        return null;
    }

    public function findGroups($accountId = null)
    {
        $groups = array();
        if (is_numeric($accountId)) {
            $gr = new Group;
            $groupAccount = new GroupAccount;
            $accountIds = $groupAccount->findGroupsByAccountId($accountId);
            if (count($accountIds) > 0) {
                $resGroup = $gr->apiFetchAll(
                    (object) [],
                    (object) ["id_in" => implode(",", $accountIds), "fields" => "id,code,name"],
                    (object) []
                );
                if ($resGroup->error === 0 && count($resGroup->data->data) > 0) {
                    $groups = $resGroup->data->data;
                }
            }
        }
        return $groups;
    }

    public function findByEmail($email)
    {
        $res = $this->_find((object) ["email" => $email], ["email"]);
        if ($res->error === 0) {
            return $res->data->data;
        }
        return null;
    }

    public function emailExist($email)
    {
        $res = $this->_exist((object) ["email" => $email], ["email"]);
        if ($res->error === 0) {
            return true;
        }
        return false;
    }

    public function zaloIdExist($zlId)
    {
        $res = $this->_exist((object) ["zlId" => $zlId], ["zlId"]);
        if ($res->error === 0) {
            return true;
        }
        return false;
    }

    public function by($id = null)
    {
        if (is_numeric($id)) {
            if (isset($this->_by->$id)) {
                return $this->_by->$id;
            }
            $avatarUrlConfig = (object) [];
            $avatarUrlConfig->name = "avatarUrl";
            if (isset($systemConfig->DEFAULT_AVATAR)) {
                $avatarUrlConfig->replace_value = ["DEFAULT_AVATAR" => $systemConfig->DEFAULT_AVATAR];
            }

            $this->init(
                "tbl_accounts",
                (object) [
                    "first_name" => (object) [],
                    "last_name" => (object) [],
                    "avatar_url" => $avatarUrlConfig,
                ],
                (object) [
                    "hiddenItemInfo" => true,
                ]
            );
            $res = $this->_find((object) ["id" => $id]);

            if ($res->error === 0) {
                $acc = $res->data->data;
                $this->_by->$id = (object) [
                    "id" => $acc->id,
                    "name" => $acc->first_name . " " . $acc->last_name,
                ];
                return $this->_by->$id;
            }
        }
        return null;
    }

    #API
    public function apiLogin($username, $password)
    {
        $res = $this->_find((object) ["username" => $username, "password" => md5($password)], ["username", "password"]);
        if ($res->error === 0) {
            return $res->data->data;
        }
        if ($this->_find((object) ["username" => $username], ["username"])->error !== 0) {
            return 24;
        } else {
            return 25;
        }
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        if (isset($getData->fields)) {
            $pathData->fields = $getData->fields;
        }
        $res = $this->_find($pathData);
        if ($res->error === 0) {
            $groups = $this->findGroups($res->data->data->id);
            $res->data->groups = $groups;
        }
        return $res;

    }

    public function apiFindByUsername($pathData, $getData, $bodyData)
    {
        if (isset($getData->fields)) {
            $pathData->fields = $getData->fields;
        }
        $res = $this->_find($pathData, ["username"]);
        if ($res->error === 0) {
            $groups = $this->findGroups($res->data->data->id);
            $res->data->groups = $groups;
        }
        return $res;

    }

    public function apiCreate($pathData, $getData, $bodyData)
    {
        if (isset($bodyData->password)) {
            $bodyData->password = md5($bodyData->password);
        }
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

    public function apiUpdateProfile($pathData, $getData, $bodyData)
    {
        $auth = new \V1\Auth;
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $accountId = $jwt->user->id;
            $bodyData->id = $accountId;
            $this->initField((object) [
                "password" => (object) [
                    "public" => false,
                    "allow_update" => false,
                ],
            ]);
            return $this->_update($bodyData);
        } else {
            return (object) [
                "error" => 401,
            ];
        }

    }

}
