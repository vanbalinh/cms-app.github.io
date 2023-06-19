<?php
namespace Controller;

class ControllerConfig
{
    private $conn;
    private $connect;
    public $config;
    public function __construct()
    {

        $configName = null;
        $parameters = func_get_args();
        if (isset($parameters[0]) && is_string($parameters[0])) {
            $configName = (string) $parameters[0];
        }

        $this->config = (object) [];
        $this->fetchAllConfig($configName);
    }

    private function fetchAllConfig($configName = null)
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
        $sql = "SELECT name, value FROM `tbl_system_config` WHERE deleted = 0";
        if (is_string($configName)) {
            $sql .= " AND name = '{$configName}'";
        }
        $stmt = $this->conn->query($sql);
        while ($item = $stmt->fetch_assoc()) {
            $formatName = $item["name"];
            $this->config->$formatName = $item["value"];
        }
    }
}
