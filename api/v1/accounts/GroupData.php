<?php
namespace V1\Account;

class GroupData
{
    // Connection
    private $conn;
    private $connect;

    // Table
    private $db_table = "tbl_group_data";

    private $params;

    public function __construct()
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
    }

    public function getChildren($groupId, $res)
    {
        $query = "SELECT id FROM `" . $this->db_table . "` WHERE deleted = 0 AND parent_id = " . $groupId;
        $result = $this->conn->query($query);
        while ($row = $result->fetch_assoc()) {
            array_push($res, (int) $row['id']);
            $res = $this->getChildren($row['id'], $res);
        }
        return $res;
    }
    private function checkValidGroupId($id)
    {
        if (is_numeric($id)) {
            $query = "SELECT id FROM `" . $this->db_table . "` WHERE deleted = 0 AND id = " . $id . " LIMIT 0,1";
            $result = $this->conn->query($query);
            return $result->num_rows === 1;
        }
        return false;
    }

    public function getAllIds($groupId)
    {
        $res = array();
        if ($this->checkValidGroupId($groupId)) {
            array_push($res, $groupId);
            $res = $this->getChildren($groupId, $res);
            return (object) [
                "error" => false,
                "data" => $res,
            ];
        }
        return (object) [
            "error" => true,
        ];
    }
}
