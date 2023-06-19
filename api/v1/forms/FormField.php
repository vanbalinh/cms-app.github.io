<?php
namespace V1\Form;

use \Config\Connect;
use \V1\Account;
use \V1\Auth;
use \V1\Form;
use \V1\Form\Data;
use \V1\Form\PermissionField;

class FormField
{
    // Connection
    private $conn;
    private $connect;

    // Table
    private $db_table = "tbl_forms_fields";

    // Columns
    public $id;
    public $form_id;
    public $name;
    public $key;
    public $type;
    public $description;
    public $sort;
    public $required;
    public $unique;
    public $multiple;
    public $max_length;
    public $max;
    public $min;
    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;

    public function __construct()
    {
        $this->connect = new Connect();
        $this->conn = $this->connect->conn;
    }

    private function getOptionRefrence($referenceId, $labelKey = null)
    {
        $data = new Data;
        $res = (object) $data->fetchAll($referenceId);
        $result = array();
        foreach ($res->data as $d) {
            $d = (object) $d;
            $d->value = $d->id;
            if (!is_null($labelKey)) {
                $d->label = $d->$labelKey;
            }
            unset($d->createdBy);
            unset($d->updatedBy);
            unset($d->createdAt);
            unset($d->updatedAt);
            array_push($result, $d);
        }
        return $result;
    }

    private function getDetailInfo($field, $permission = false)
    {
        $acc = new Account();
        $form = new Form();
        $formFieldPermission = new PermissionField;
        $res = (object) [];

        $type = isset($field["type"]) ? $field["type"] : null;

        $res->id = isset($field["id"]) && is_numeric($field["id"]) ? (int) $field["id"] : null;
        $res->name = isset($field["name"]) ? $field["name"] : null;
        $res->key = isset($field["api_key"]) ? $field["api_key"] : null;
        $res->type = $type;
        $res->description = isset($field["description"]) ? $field["description"] : null;
        $res->sort = isset($field["sort"]) && is_numeric($field["sort"]) ? (int) $field["sort"] : 0;
        $res->required = isset($field["is_required"]) && $field["is_required"] == 1 ? true : false;
        $res->unique = isset($field["is_unique"]) && $field["is_unique"] == 1 ? true : false;

        switch ($type) {
            case "int":
            case "float":
                $res->max = isset($field["num_max"]) && is_numeric($field["num_max"]) ? (int) $field["num_max"] : null;
                $res->min = isset($field["num_min"]) && is_numeric($field["num_min"]) ? (int) $field["num_min"] : null;
                break;
            case "upload":
                $res->multiple = isset($field["is_multiple"]) && $field["is_multiple"] == 1 ? true : false;
                break;
            case "reference":
                $res->multiple = isset($field["is_multiple"]) && $field["is_multiple"] == 1 ? true : false;
                $res->referenceId = isset($field["reference_id"]) && is_numeric($field["reference_id"]) ? (int) $field["reference_id"] : null;
                $res->options = $this->getOptionRefrence($res->referenceId);
                break;
            case "select":
                $res->multiple = isset($field["is_multiple"]) && $field["is_multiple"] == 1 ? true : false;
                $res->referenceId = isset($field["reference_id"]) && is_numeric($field["reference_id"]) ? (int) $field["reference_id"] : null;
                $res->labelKey = isset($field["label_key"]) ? $field["label_key"] : null;
                $res->options = $this->getOptionRefrence($res->referenceId, $res->labelKey);
                break;
            default:
                $res->maxLength = isset($field["max_length"]) && is_numeric($field["max_length"]) ? (int) $field["max_length"] : null;
                break;
        }
        $res->permission = $formFieldPermission->getPermission($field["id"]);
        return $res;
    }

    public function getAllFieldName($formId, $idNotIn = array())
    {
        $res = array();
        $sqlQuery = "SELECT
            id,
            form_id,
            api_key
        FROM " . $this->db_table . " WHERE deleted = 0 AND form_id = " . $formId . " AND id not in (" . implode(", ", ($idNotIn)) . ")  ORDER BY sort ASC ";
        $stmt = $this->conn->query($sqlQuery);
        while ($row = $stmt->fetch_assoc()) {
            $intId = (int) $row["id"];
            array_push($res, (object) [
                "id" => $intId,
                "apiKey" => $row["api_key"],
            ]);
        }
        return $res;
    }

    public function getAllFieldUpdate($formId, $fieldsUpdateId = array())
    {
        $res = array();
        $sqlQuery = "SELECT
            id,
            form_id,
            api_key
        FROM " . $this->db_table . " WHERE deleted = 0 AND form_id = " . $formId . " AND id in (" . implode(", ", ($fieldsUpdateId)) . ")  ORDER BY sort ASC ";
        $stmt = $this->conn->query($sqlQuery);
        while ($row = $stmt->fetch_assoc()) {
            $intId = (int) $row["id"];
            array_push($res, (object) [
                "id" => $intId,
                "apiKey" => $row["api_key"],
            ]);
        }
        return $res;
    }

    public function fetchAll($form_id = null, $permission = false)
    {
        $res = array();
        $sqlQuery = "SELECT
            id,
            form_id,
            name,
            api_key,
            type,
            description,
            sort,
            is_required,
            is_unique,
            is_multiple,
            max_length,
            num_max,
            num_min,
            reference_id,
            label_key,
            created_by,
            updated_by
        FROM " . $this->db_table . " WHERE deleted = 0 AND form_id = " . $form_id . "  ORDER BY sort ASC, created_at ASC";
        $stmt = $this->conn->query($sqlQuery);

        while ($row = $stmt->fetch_assoc()) {
            array_push($res, $this->getDetailInfo($row, $permission));
        }
        return $res;
    }

    public function findById($id)
    {
        $sql = "SELECT
            id,
            form_id,
            name,
            api_key,
            type,
            description,
            sort,
            is_required,
            is_unique,
            is_multiple,
            max_length,
            num_max,
            num_min,
            reference_id,
            label_key,
            created_by,
            updated_by
        FROM " . $this->db_table . "
        WHERE  id = " . $id . " AND deleted = 0 LIMIT 0,1";
        $stmt = $this->conn->query($sql);

        if ($stmt->num_rows == 0) {
            return null;
        }
        $dataRow = $stmt->fetch_assoc();
        return $this->getDetailInfo($dataRow);
    }

    // create
    public function create($data, $form_id)
    {
        $auth = new Auth();

        $created_by = null;
        $updated_by = null;

        if ($auth->check()) {
            $jwt = $auth->jwt;
            $created_by = (int) $jwt->user->id;
            $updated_by = (int) $jwt->user->id;
        }
        try {
            $sqlQuery = "INSERT INTO " . $this->db_table . " (
                form_id,
                name,
                api_key,
                type,
                description,
                sort,
                is_required,
                is_unique,
                is_multiple,
                max_length,
                num_max,
                num_min,
                reference_id,
                label_key,
                sql_where,
                min_date,
                max_date,
                min_datetime,
                max_datetime,
                created_by,
                updated_by
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bind_param("sssssssssssssssssssii",
                $form_id,
                $data["name"],
                $data["api_key"],
                $data["type"],
                $data["description"],
                $data["sort"],
                $data["is_required"],
                $data["is_unique"],
                $data["is_multiple"],
                $data["max_length"],
                $data["num_max"],
                $data["num_min"],
                $data["reference_id"],
                $data["label_key"],
                $data["sql_where"],
                $data["min_date"],
                $data["max_date"],
                $data["min_datetime"],
                $data["max_datetime"],
                $created_by,
                $updated_by);

            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $newId = $this->conn->insert_id;
                return $newId;
            }
        } catch (Exception $e) {
            echo $e;
        }
        return false;
    }

    // create
    public function update($data, $form_id)
    {
        $auth = new Auth();

        $created_by = null;
        $updated_by = null;

        if ($auth->check()) {
            $jwt = $auth->jwt;
            $created_by = (int) $jwt->user->id;
            $updated_by = (int) $jwt->user->id;
        }
        try {
            $sqlQuery = "UPDATE " . $this->db_table . "  SET
                form_id = ?,
                name = ?,
                api_key = ?,
                type = ?,
                description = ?,
                sort = ?,
                is_required = ?,
                is_unique = ?,
                is_multiple = ?,
                max_length = ?,
                num_max = ?,
                num_min = ?,
                reference_id = ?,
                label_key = ?,
                sql_where = ?,
                min_date = ?,
                max_date = ?,
                min_datetime = ?,
                max_datetime = ?,
                updated_by = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bind_param("ssssssssssssssssssiis",
                $form_id,
                $data["name"],
                $data["api_key"],
                $data["type"],
                $data["description"],
                $data["sort"],
                $data["is_required"],
                $data["is_unique"],
                $data["is_multiple"],
                $data["max_length"],
                $data["num_max"],
                $data["num_min"],
                $data["reference_id"],
                $data["label_key"],
                $data["sql_where"],
                $data["min_date"],
                $data["max_date"],
                $data["min_datetime"],
                $data["max_datetime"],
                $updated_by,
                $data["id"]
            );

            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                return (int) $data["id"];
            }
        } catch (Exception $e) {
            echo $e;
        }
        return false;
    }

    // DELETE
    public function delete($_form_ids, $fieldIdNotIns = array())
    {
        $form_ids = is_array($_form_ids) ? $_form_ids : array();
        $formFieldPermission = new PermissionField;
        $auth = new Auth();
        $updated_by = null;
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $updated_by = (int) $jwt->user->id;
        }
        try {
            $formFieldPermission->deleteByFormIds((array) $form_ids, $fieldIdNotIns);
            $sql = "DELETE from " . $this->db_table . " WHERE form_id in (" . implode(", ", ($form_ids)) . ") AND id not in (" . implode(", ", ($fieldIdNotIns)) . ")";
            $stmt = $this->conn->query($sql);
            if ($stmt) {
                return true;
            }
        } catch (Exception $e) {
        }
        return false;
    }

    public function invalidMethod()
    {
        $this->connect->sendResponse(500, "INVALID METHOD");
    }
}
