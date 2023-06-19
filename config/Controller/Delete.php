<?php
namespace Controller;

class ControllerDelete extends ControllerCore
{
    public function __construct()
    {
        parent::__construct();
    }

    public function delete($data)
    {
        $validate = $this->validate("delete", (object) ["data" => $data]);
        $error = 0;
        if ($validate === false) {
            return (object) [
                "error" => 400,
            ];
        } else {
            # Hàm xử lý ở đây
            if (count($data) > 0) {
                $updated_by = $this->getAuthId();
                $sql = "UPDATE `$this->table_name` SET deleted = 1,  updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id in(" . implode(", ", $data) . ")";
                $stmt = $this->prepare($sql);
                $stmt->bind_param("i", $updated_by);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $error = 0;
                } else {
                    $error = 4;
                }
            }
            # Xoá thành công
            if ($error === 0) {
                return (object) [
                    "error" => 0,
                ];
            }
            # Xoá thất bại
            else {
                return (object) [
                    "error" => $error,
                ];
            }
        }
    }
}
