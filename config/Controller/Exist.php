<?php
namespace Controller;

class ControllerExist extends ControllerCore
{
    public function __construct()
    {
        parent::__construct();
    }

    public function exist($data, $fieldNames = ["id"])
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
        $_fieldSQL = "";
        foreach ($fieldNames as $_fieldName) {
            $_fieldKey = $this->findFieldKeyByFieldName($_fieldName);
            $_fieldSQL .= $_fieldKey . " =  '" . $data->$_fieldName . "' AND ";
        }
        $sql = "SELECT count(id) FROM  `$this->table_name` WHERE " . $_fieldSQL . " deleted = 0  LIMIT 0,1";
        $stmt = $this->query($sql);
        $row = $stmt->fetch_row();
        # Có dữ liệu
        if ((int) $row[0] === 1) {
            return (object) [
                "error" => 0,
            ];
        }
        # Không Có dữ liệu
        else {
            return (object) [
                "error" => 1,
            ];
        }
    }
}
