<?php
namespace Controller;

class ControllerCreate extends ControllerFind
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create($data)
    {
        $validate = $this->validate("create", $data);
        $item = null;
        $error = 0;
        if ($validate === false) {
            return (object) [
                "error" => 400,
                "error_description" => $this->error_description,
            ];
        } else {
            $authId = $this->getAuthId();
            # Hàm xử lý ở đây
            $_fieldNameStr = "";
            $_fieldValueStr = "";
            foreach ($this->field as $key => $value) {
                $fieldName = isset($value->name) ? $value->name : $key;
                if (array_key_exists($fieldName, (array) $data) && (!isset($value->system) || !$value->system)) {
                    $value = $this->convertData($key, $data->$fieldName);
                    $_fieldNameStr .= "`$key`" . ", ";
                    $_fieldValueStr .= (is_null($value) ? "null" : "'" . $value . "'") . ", ";
                }
            }
            $created_by = $authId;
            $updated_by = $authId;
            $sql = "INSERT INTO `$this->table_name` (" . $_fieldNameStr . " created_at, updated_at, created_by, updated_by)
            VALUES (" . $_fieldValueStr . " CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ii", $created_by, $updated_by);
            $stmt->execute();
            $newId = null;
            if ($stmt->affected_rows == 1) {
                $_item = $this->find((object) ["id" => $this->conn->insert_id]);
                if ($_item->error === 0) {
                    $item = $_item->data;
                    $error = 0;
                } else {
                    $error = 2;
                }
            } else {
                $error = 2;
            }
            # Thêm mới thành công
            if ($error === 0) {
                return (object) [
                    "error" => 0,
                    "data" => (object) $item,
                ];
            }
            # Thêm mới thất bại
            else {
                return (object) [
                    "error" => $error,
                ];
            }
        }
    }
}
