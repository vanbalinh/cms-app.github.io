<?php
namespace V1\Account;

class GroupDataShare
{
    // Connection
    private $conn;
    private $connect;

    // Table
    private $db_table = "tbl_group_data_share";

    private $params;

    public function __construct()
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
    }

    private function getAllIds($groupId, $formId)
    {
        $res = array();
        if (is_numeric($groupId) && is_numeric($formId)) {
            $groupData = new \V1\Account\GroupData;
            $groupIdRes = $groupData->getAllIds($groupId);
            $groupIds = !$groupIdRes->error ? $groupIdRes->data : array();
            $query = "SELECT id FROM `" . $this->db_table . "` WHERE deleted = 0 AND form_id = " . $formId . " AND group_data_id in (" . implode(", ", ($groupIds)) . ")";
            $result = $this->conn->query($query);

            while ($r = $result->fetch_assoc()) {
                array_push($res, (int) $r["id"]);
            }
        }
        return $res;
    }

    public function getAllGroupIdsAndDataIds($groupId, $formId)
    {
        $groupData = new \V1\Account\GroupData;
        $ids = $this->getAllIds($groupId, $formId);
        $groupDataIds = array();
        $dataIds = array();
        if (count($ids) > 0) {
            $query = "SELECT group_data_id, data_id, group_data_shared_id FROM `" . $this->db_table . "` WHERE deleted = 0 AND id in (" . implode(", ", ($ids)) . ")";
            $result = $this->conn->query($query);
            $res = array();
            while ($r = $result->fetch_assoc()) {
                if (!is_null($r["group_data_id"])) {
                    array_push($groupDataIds, (int) $r["group_data_id"]);
                }
                if (!is_null($r["group_data_shared_id"])) {
                    $groupIdRes = $groupData->getAllIds((int) $r["group_data_shared_id"]);
                    $groupIds = !$groupIdRes->error ? $groupIdRes->data : array();
                    if (count($groupIds) > 0) {
                        $groupDataIds = array_merge($groupDataIds, $groupIds);
                    }
                }
                if (!is_null($r["data_id"])) {
                    array_push($dataIds, (int) $r["data_id"]);
                }
            }
        }
        return (object) [
            "groupDataIds" => array_unique($groupDataIds),
            "dataIds" => array_unique($dataIds),
        ];
    }
}
