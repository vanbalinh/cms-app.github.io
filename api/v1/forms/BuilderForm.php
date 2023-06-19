<?php
namespace V1\Builder;

class Form extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_forms",
            (object) [
                "folder_id" => (object) [
                    "name" => "folder",
                    "type" => "object",
                    "object_name" => "V1\\Folder",
                    "object_fields" => ["name"],
                ],
                "name" => (object) [
                    "unique" => true,
                    "required" => true,
                ],
                "code" => (object) [
                    "unique" => true,
                ],
                "description" => (object) [],
                "check_access" => (object) [
                    "type" => "boolean",
                    "name" => "checkAccess",
                ],
                "show_view" => (object) [
                    "type" => "boolean",
                    "name" => "showView",
                ],
                "config" => (object) [
                    "type" => "json",
                ],
            ]
        );
    }

    private function createTable($id)
    {
        $tbl_name = "tbl_data_form_" . $id;
        $result = $this->_run("SHOW TABLES LIKE '" . $tbl_name . "'");
        if ($result->num_rows == 0) {
            $sqlCreateTable = "CREATE TABLE " . $tbl_name . " (
                `id` int(11) NOT NULL,
                `group_data_id` int(11) NULL,
                `view` int(11) NOT NULL DEFAULT 0,
                `deleted` tinyint(1) NOT NULL DEFAULT 0,
                `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                `created_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL,
                `updated_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL
            ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_vietnamese_ci;";
            $sqlCreateTable1 = "ALTER TABLE `" . $tbl_name . "` ADD PRIMARY KEY (`id`);";
            $sqlCreateTable2 = "ALTER TABLE `" . $tbl_name . "` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
            $sqlCreateTable3 = "ALTER TABLE `" . $tbl_name . "` AUTO_INCREMENT = 1000000000;";

            if (
                $this->_run($sqlCreateTable)
                && $this->_run($sqlCreateTable1)
                && $this->_run($sqlCreateTable2)
                && $this->_run($sqlCreateTable3)
            ) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData);
    }

    public function apiFindByCode($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData, ["code"]);
    }

    public function apiCreate($pathData, $getData, $bodyData)
    {
        $res = $this->_create($bodyData);
        if ($res->error === 0) {
            $this->createTable($res->data->data->id);
        }
        return $res;
    }

    public function apiUpdate($pathData, $getData, $bodyData)
    {
        $res = $this->_update($bodyData);
        if ($res->error === 0) {
            $this->createTable($res->data->data->id);
        }
        return $res;
    }

    public function apiDelete($pathData, $getData, $bodyData)
    {
        return $this->_delete($bodyData);
    }
}
