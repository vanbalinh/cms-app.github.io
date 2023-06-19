<?php
namespace Controller;

class ControllerFind extends ControllerCore
{
    public function __construct()
    {
        parent::__construct();
    }

    public function find($data, $fieldNames = ["id"], $showItemInfo = true)
    {
        $validate = $this->validate("find", (object) ["data" => $data, "fieldNames" => $fieldNames]);
        if ($validate === false) {
            return (object) [
                "error" => 400,
                "error_description" => $this->error_description,
            ];
        }

        $item = null;
        # Xử lý ở đây
        $_fields = array();
        $_fieldSQL = "";
        foreach ($fieldNames as $_fieldName) {
            $_fieldKey = $this->findFieldKeyByFieldName($_fieldName);
            $_fieldSQL .= "`$_fieldKey`" . " =  '" . $data->$_fieldName . "' AND ";
        }
        foreach ($this->field as $key => $value) {
            array_push($_fields, "`$key`");
        }
        $sql = "SELECT " . implode(", ", $_fields) . " FROM  `$this->table_name` WHERE " . $_fieldSQL . " deleted = 0  LIMIT 0,1";
        $stmt = $this->query($sql);
        if ($stmt->num_rows > 0) {
            $item = (object) $stmt->fetch_assoc();
        }
        # Không tìm thấy dữ liệu
        if (is_null($item)) {
            return (object) [
                "error" => 1,
            ];
        }
        # Có dữ liệu
        else {
            $arrShowFields = isset($data->fields) && is_string($data->fields) && strlen(trim($data->fields)) > 0 && count(explode(",", $data->fields)) > 0 ? explode(",", $data->fields) : array();
            $showFields = array();
            foreach ($this->field as $key => $f) {
                $fieldName = isset($f->name) ? $f->name : $key;
                if ((!isset($f->system) || !$f->system) && in_array($fieldName, $arrShowFields)) {
                    array_push($showFields, $fieldName);
                }
            }
            return (object) [
                "error" => 0,
                "data" => (object) [
                    "data" => $this->item(
                        $item,
                        count($showFields) > 0 ? $showFields : null,
                        !$showItemInfo
                    ),
                ],
            ];
        }
    }
}
