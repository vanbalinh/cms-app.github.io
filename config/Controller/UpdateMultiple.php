<?php
namespace Controller;

class ControllerUpdateMultiple extends ControllerFind
{
    public function __construct()
    {
        parent::__construct();
    }

    public function update($data)
    {
        if (isset($data->id) && $this->count((object) ["id" => $data->id]) !== 1) {
            return (object) [
                "error" => 1,
            ];
        }

        $validate = $this->validate("update", $data);
        if ($validate === false) {
            return (object) [
                "error" => 400,
                "error_description" => $this->error_description,
            ];
        }

        $item = null;
        $error = 0;
        # Xử lý ở đây
        $_fieldSQL = "";
        foreach ($this->field as $key => $value) {
            $fieldName = isset($value->name) ? $value->name : $key;
            if (array_key_exists($fieldName, (array) $data) && (!isset($value->system) || !$value->system)) {
                $value = $this->convertData($key, $data->$fieldName);
                $_fieldSQL .= "`$key`" . " =  " . (is_null($value) ? "null" : "'" . $value . "'") . ",";
            }
        }
        $updated_by = $this->getAuthId();
        $sql = "UPDATE `$this->table_name` SET " . $_fieldSQL . " updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = $data->id";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $updated_by);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
            $_item = $this->find($data);
            if ($_item->error === 0) {
                $item = $_item->data;
                $error = 0;
            } else {
                $error = 3;
            }
        } else {
            $error = 3;
        }
        # Cập nhật thành công
        if ($error === 0) {
            return (object) [
                "error" => 0,
                "data" => (object) $item,
            ];
        }
        # Cập nhật thất bại
        else {
            return (object) [
                "error" => $error,
            ];
        }
    }
}
