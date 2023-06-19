<?php
namespace Controller;

class ControllerFetch extends ControllerCore
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Lấy danh sách
     */

    public function fetch($data = [])
    {
        $validate = $this->validate("fetch_all", (object) $data);
        if ($validate === false) {
            return (object) [
                "error" => 400,
                "error_description" => $this->error_description,
            ];
        }
        $res = (object) [
            "data" => array(),
        ];
        # Xử lý ở đây
        $_fields = array();
        foreach ($this->field as $key => $value) {
            array_push($_fields, "`" . $key . "`");
        }
        $sql = "SELECT " . implode(", ", $_fields) . " FROM `$this->table_name` WHERE " . $this->where($data);
        $page = isset($data->page) && is_numeric($data->page) ? (int) $data->page : null;
        $pageSize = isset($data->pageSize) && is_numeric($data->pageSize) ? (int) $data->pageSize : null;

        $pg = new \Common\Pagination ($page, $pageSize, $this->count($data));
        if ($pg->check()) {
            $sql .= $pg->getSql();
            $res->pagination = $pg;
        }
        $stmt = $this->query($sql);

        $arrShowFields = isset($data->fields) && is_string($data->fields) && strlen(trim($data->fields)) > 0 && count(explode(",", $data->fields)) > 0 ? explode(",", $data->fields) : array();
        $showFields = array();
        foreach ($this->field as $key => $f) {
            $fieldName = isset($f->name) ? $f->name : $key;
            if ((!isset($f->system) || !$f->system) && in_array($fieldName, $arrShowFields)) {
                array_push($showFields, $fieldName);
            }
        }
        while ($row = $stmt->fetch_assoc()) {
            array_push($res->data, $this->item(
                (object) $row,
                count($showFields) > 0 ? $showFields : null,
                isset($data->_hiddenItemInfo) && is_bool($data->_hiddenItemInfo) ? $data->_hiddenItemInfo : false
            ));
        }
        return (object) [
            "error" => 0,
            "data" => $res,
        ];

    }
}
