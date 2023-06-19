<?php
namespace V1;

class GroupAccount extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_group_account",
            (object) [
                "group_id" => (object) ["type" => "int"],
                "account_id" => (object) ["type" => "int"],
            ]
        );
    }

    public function findAccountsByGroupId($groupId)
    {
        $accountIds = array();
        $res = $this->_fetch((object) ["group_id" => $groupId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $gr) {
                array_push($accountIds, $gr->account_id);
            }
        }
        return $accountIds;
    }

    public function findGroupsByAccountId($accountId)
    {
        $groupIds = array();
        $res = $this->_fetch((object) ["account_id" => $accountId]);
        if ($res->error === 0) {
            foreach ($res->data->data as $gr) {
                array_push($groupIds, $gr->group_id);
            }
        }
        return $groupIds;
    }
}
