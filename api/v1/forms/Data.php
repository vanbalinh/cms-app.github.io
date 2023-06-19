<?php
namespace V1\Form;

use \Common\Format;
use \Common\Pagination;
use \Config\Connect;
use \V1\Account;
use \V1\Account\GroupData;
use \V1\Account\GroupDataShare;
use \V1\Auth;
use \V1\Form;
use \V1\Form\FormField;
use \V1\Form\Permission;
use \V1\Form\PermissionField;

class Data
{
    private $conn;
    private $connect;

    private $db_table_forms = "tbl_forms";
    private $db_table_forms_fields = "tbl_forms_fields";
    private $db_table_form_data = "tbl_form_data";
    private $db_table_form_data_field = "tbl_form_data_field";

    private $params;
    public function __construct()
    {
        $this->connect = new Connect();
        $this->conn = $this->connect->conn;
    }

    private function checkForm($form_id)
    {
        $sql = "SELECT id, check_access FROM " . $this->db_table_forms . " WHERE  id = " . $form_id . " AND deleted = 0 LIMIT 0,1";
        $stmt = $this->conn->query($sql);
        $result = $this->conn->query("SHOW TABLES LIKE 'tbl_data_form" . $form_id . "'");
        if ($stmt->num_rows == 0 || $result->num_rows == 0) {
            return -1;
        }
        $dataRow = $stmt->fetch_assoc();
        if ($dataRow["check_access"] == "1") {
            return 1;
        } else {
            return 0;
        }
    }

    private function validateForm($formId, $permission)
    {
        $vF = $this->checkForm($formId);
        if ($vF == -1) {
            return $this->connect->sendResponse(200, (object) [
                "status" => false,
                "msg" => "Form không tồn tại",
            ]);
        }
        $auth = new Auth();
        if ($vF === 1) {
            $auth->check();
        }
        $p = new Permission;
        if (!$p->getPermission($formId)->$permission) {
            return $this->connect->sendResponse(403);
        }
    }

    private function getFields($form_id)
    {
        $formFields = new FormField();
        return $formFields->fetchAll($form_id);
    }

    private function getFieldKeys($fields)
    {
        $res = array();
        foreach ($fields as $f) {
            array_push($res, $f->key);
        }
        return $res;
    }

    private function checkExistData($formId, $key, $data, $dataId)
    {
        $sql = "SELECT
            fdt.id
        FROM tbl_data_form" . $formId . " fdt , " . $this->db_table_forms . " f
        WHERE  " . (!is_null($dataId) ? "fdt.id <> " . $dataId . " AND " : "") . " fdt." . $key . " = '" . $data . "' AND fdt.deleted = 0 AND f.deleted = 0 AND f.id = " . $formId;
        $stmt = $this->conn->query($sql);
        if ($stmt->num_rows == 0) {
            return false;
        }
        return true;
    }
    private function checkInvalid($field, $data, $value, $validate, $formId, $dataId)
    {
        // check required
        if ($field->required && is_null($data)) {
            $validate[$field->key] = "required";
            return [
                "value" => $value,
                "validate" => $validate,
            ];
        }
        if ($field->unique && $this->checkExistData($formId, $field->key, $data, $dataId)) {
            $validate[$field->key] = "unique";
            return [
                "value" => $value,
                "validate" => $validate,
            ];
        }
        switch ($field->type) {
            case "int":
                if (is_numeric($data) && $data === (int) $data) {
                    $value[$field->key] = $data;
                } else {
                    $validate[$field->key] = "incorrect_data_type";
                }
                break;
            case "float":
                if (is_numeric($data) && $data === (float) $data) {
                    $value[$field->key] = $data;
                } else {
                    $validate[$field->key] = "incorrect_data_type";
                }
                break;
            case "reference":
                if (!isset($data) || (isset($data->id) && is_numeric($data->id))) {
                    $value[$field->key] = $data;
                } else {
                    $validate[$field->key] = "incorrect_data_type";
                }
                break;
            case "select":
                if ($field->multiple) {
                    if (!isset($data) || (is_array($data))) {
                        $error = false;
                        foreach ($data as $d) {
                            if (!isset($d->id) || !is_numeric($d->id)) {
                                $error = true;
                                break;
                            }
                        }
                        if (!$error) {
                            $value[$field->key] = $data;
                        } else {
                            $validate[$field->key] = "incorrect_data_type";
                        }
                    } else {
                        $validate[$field->key] = "incorrect_data_type";
                    }
                } else {
                    if (!isset($data) || (isset($data->id) && is_numeric($data->id))) {
                        $value[$field->key] = $data;
                    } else {
                        $validate[$field->key] = "incorrect_data_type";
                    }
                }
                break;
            case "text":
                $value[$field->key] = (string) $data;
            default:
                break;
        }
        return [
            "value" => $value,
            "validate" => $validate,
        ];
    }

    private function formatFieldData($field, $data)
    {

        switch ($field->type) {
            case "int":
                if (is_numeric($data)) {return (int) $data;}
                return null;
            case "float":
                if (is_numeric($data)) {return (float) $data;}
                return null;
            case "reference":
                if (is_numeric($data) && is_numeric($field->referenceId)) {
                    return $this->findById((int) $field->referenceId, (int) $data, false, $field->referenceId);
                }
                break;
            case "select":
                if ($field->multiple) {
                    if (is_string($data)) {
                        $dataArr = array();
                        foreach (explode(",", $data) as $d) {
                            if (is_numeric($d) && is_numeric($field->referenceId)) {
                                array_push($dataArr, $this->findById($field->referenceId, $d, false, $field->referenceId));
                            }
                        }
                        return $dataArr;

                    }
                    break;
                } else {
                    if (is_numeric($data) && is_numeric($field->referenceId)) {
                        return $this->findById((int) $field->referenceId, (int) $data, false, $field->referenceId);
                    }
                    break;
                }
            default:
                return $data;
        }
    }
    private function formatDataSave($field, $data)
    {
        if (is_null($data)) {
            return null;
        }
        switch ($field->type) {
            case "int":
                if (is_numeric($data)) {return (int) $data;}
                return null;
            case "float":
                if (is_numeric($data)) {return (float) $data;}
                return null;
            case "reference":
                if (isset($data->id)) {
                    return $data->id;
                }
                break;
            case "select":
                if ($field->multiple) {
                    if (is_array($data)) {
                        $dataArr = array();
                        foreach ($data as $d) {
                            if (isset($d->id)) {
                                array_push($dataArr, $d->id);
                            }
                        }
                        return implode(",", $dataArr);
                    }
                    break;
                } else {
                    if (isset($data->id)) {
                        return $data->id;
                    }
                    break;
                }
            default:
                return $data;
        }
    }

    private function formatData($item, $formId, $showUser = true)
    {
        $format = new Format;
        $form = new Form();
        $formFieldPermission = new PermissionField;
        $_form = $form->findById($formId);
        $acc = new Account();
        $fields = $this->getFields($formId);
        $result = array();
        $result["id"] = (int) $item["id"];

        foreach ($fields as $f) {
            $p = $formFieldPermission->getPermission($f->id);
            if ($p->view) {
                $result[$f->key] = $this->formatFieldData($f, isset($item[$f->key]) ? $item[$f->key] : null);
            }
        }
        if ($_form->showView) {
            $result["view"] = (int) $item["view"];
        }

        $result["createdAt"] = $format->DateTime($item["created_at"]);
        $result["createdBy"] = $showUser ? $acc->by((int) $item["created_by"]) : null;
        $result["updatedAt"] = $format->DateTime($item["updated_at"]);
        $result["updatedBy"] = $showUser ? $acc->by((int) $item["updated_by"]) : null;
        return $result;
    }

    private function findById($formId, $dataId, $showUser = true, $referenceId = null)
    {
        $sql = "SELECT
            fdt.*
        FROM tbl_data_form" . $formId . " fdt , " . $this->db_table_forms . " f
        WHERE  fdt.id = " . $dataId . "  AND fdt.deleted = 0 AND f.deleted = 0 AND f.id = " . $formId;
        $stmt = $this->conn->query($sql);
        if ($stmt->num_rows == 0) {
            return null;
        }
        if ($dataRow = $stmt->fetch_assoc()) {
            $sqlUpdateView = "UPDATE tbl_data_form" . $formId . " SET view = " . ($dataRow["view"] + 1) . " WHERE id = " . $dataRow["id"];
            $this->conn->query($sqlUpdateView);
            return $this->formatData($dataRow, $formId, $showUser);
        }
        return null;
    }

    private function where($formId)
    {
        $auth = new Auth;
        $where = "";
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $groupData = new GroupData;
            $gds = new GroupDataShare;
            $groupDataShare = $gds->getAllGroupIdsAndDataIds($jwt->user->group_data_id, $formId);
            $groupsIds = $groupData->getAllIds($jwt->user->group_data_id);
            if (!$groupsIds->error) {
                $where .= " ( fdt.group_data_id in (" . implode(", ", ($groupsIds->data)) . ")";
                if (count($groupDataShare->groupDataIds) > 0) {
                    $where .= " OR fdt.group_data_id in (" . implode(", ", ($groupDataShare->groupDataIds)) . ")";
                }
                if (count($groupDataShare->dataIds) > 0) {
                    $where .= " OR fdt.id in (" . implode(", ", ($groupDataShare->dataIds)) . ")";
                }
                $where .= " ) AND ";
            }
        }
        $where .= " fdt.deleted = 0 AND f.deleted = 0 AND f.id = " . $formId;
        $fields = $this->getFields($formId);
        $orderBy = null;
        $keys = array();
        foreach ($fields as $f) {
            array_push($keys, $f->key);
        }
        if (isset($this->params->q)) {
            $where .= " AND (";
            foreach ($keys as $key) {
                $where .= "(" . $key . " is not null AND " . $key . " like '%" . $this->params->q . "%') OR ";
            }

            $where = substr($where, 0, strlen($where) - 3);
            $where .= " ) ";
        }

        foreach ($keys as $key) {
            if (isset($this->params->$key)) {
                $where .= " AND " . $fkey . " = '" . $this->params->$key . "'";
            }

        }
        return $where;
    }

    private function order($formId)
    {
        $fields = $this->getFields($formId);
        $orderBy = null;
        $keys = array();
        foreach ($fields as $f) {
            array_push($keys, $f->key);
        }
        if (isset($this->params->orderBy) && is_string($this->params->orderBy)) {
            $orderByString = $this->params->orderBy;
            $orderByArray = explode(',', $orderByString);
            foreach ($orderByArray as $order) {
                $_o = explode(' ', trim($order));
                if (count($_o) === 2) {
                    $key = trim($_o[0]);
                    $sort = trim($_o[1]);
                    $s = array_search($key, $keys, true);
                    if (($sort === "asc" || $sort === "desc")) {
                        if ($s !== false) {
                            if (is_null($orderBy)) {
                                $orderBy = "";
                            }

                            $orderBy .= $_o[0] . " " . $_o[1] . ", ";
                        }
                        if ($key === "createdAt") {
                            if (is_null($orderBy)) {
                                $orderBy = "";
                            }

                            $orderBy .= "fdt.created_at" . " " . $_o[1] . ", ";
                        }
                        if ($key === "updatedAt") {
                            if (is_null($orderBy)) {
                                $orderBy = "";
                            }

                            $orderBy .= "fdt.updated_at" . " " . $_o[1] . ", ";
                        }
                    }
                }
            }

        }
        if (!is_null($orderBy)) {
            $orderBy = substr($orderBy, 0, strlen($orderBy) - 2);
        }
        return (!is_null($orderBy) ? (" ORDER BY " . $orderBy) : "");
    }

    private function count($formId)
    {
        $sqlCount = "SELECT
            count(*)
        FROM tbl_data_form" . $formId . " fdt , " . $this->db_table_forms . " f
        WHERE " . $this->where($formId);
        $stmtCount = $this->conn->query($sqlCount);
        $row = $stmtCount->fetch_row();
        return (int) $row[0];
    }

    public function fetchAll($formId)
    {
        $validateForm = $this->checkForm($formId);
        $res = array();
        $res["data"] = array();

        $sql = "SELECT
            fdt.*
        FROM tbl_data_form" . $formId . " fdt , " . $this->db_table_forms . " f
        WHERE " . $this->where($formId);
        $sql .= $this->order($formId);
        $page = isset($this->params->page) && is_numeric($this->params->page) ? (int) $this->params->page : null;
        $pageSize = isset($this->params->pageSize) && is_numeric($this->params->pageSize) ? (int) $this->params->pageSize : null;

        $pg = new Pagination($page, $pageSize, $this->count($formId));
        if ($pg->check()) {
            $sql .= $pg->getSql();
            $res["pagination"] = $pg;
        }
        $stmt = $this->conn->query($sql);
        while ($dataRow = $stmt->fetch_assoc()) {
            array_push($res["data"], $this->formatData($dataRow, $formId, true));
        }
        return $res;
    }

    private function create($data, $formId)
    {
        $auth = new Auth();
        $validateForm = $this->checkForm($formId);
        if ($validateForm == -1) {
            return (object) [
                "status" => false,
                "msg" => "Form không tồn tại",
            ];
        }
        if ($validateForm === 1) {
            $auth->check();
        }
        $created_by = null;
        $updated_by = null;
        $groupDataId = null;
        $value = array();
        $validate = array();
        $fields = $this->getFields($formId);
        $fieldsArray = (array) $fields;
        foreach ($fields as $f) {
            $res = $this->checkInvalid($f, isset($data[$f->key]) ? $data[$f->key] : null, $value, $validate, $formId, null);
            $value = $res["value"];
            $validate = $res["validate"];
        }
        $keys = (array) $this->getFieldKeys($fields);
        if (count($validate) == 0) {
            if ($auth->check()) {
                $jwt = $auth->jwt;
                $created_by = (int) $jwt->user->id;
                $updated_by = (int) $jwt->user->id;
                if (isset($jwt->user->group_data_id)) {
                    $groupDataId = $jwt->user->group_data_id;
                }
            }
            $fieldString = "";
            $valueString = "";
            foreach ($data as $key => $item) {
                $stringKey = (string) $key;
                $field = array_column($fieldsArray, null, "key")[$key] ?? null;
                if (!is_null($field)) {
                    $fieldString .= "'$key' , ";
                    $valueString .= "'" . $this->formatDataSave((object) $field, $item) . "',";
                }
            }
            $sqlQuery = "INSERT INTO tbl_data_form" . $formId . " (" . $fieldString . "group_data_id,created_by,updated_by) VALUES (" . $valueString . " ?,?,?)";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param("iii", $groupDataId, $created_by, $updated_by);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $newId = $this->conn->insert_id;
                $item = $this->findById($formId, $newId, true, null);
                return (object) [
                    "data" => (object) $item,
                    "validate" => (object) $validate,
                    "status" => true,
                    "msg" => MSG_CREATE_SUCCESSFUL,
                ];
            } else {
                return (object) [
                    "status" => false,
                    "msg" => MSG_CREATE_FAILED,
                ];
            }
        } else {
            return (object) [
                "data" => null,
                "validate" => (object) $validate,
                "status" => false,
                "msg" => MSG_INVALID_DATA,
            ];
        }
    }

    private function update($data, $formId)
    {
        $validateForm = $this->checkForm($formId);
        if ($validateForm == -1) {
            return (object) [
                "status" => false,
                "msg" => "Form không tồn tại",
            ];
        }
        $created_by = null;
        $updated_by = null;
        $groupDataId = null;
        $value = array();
        $validate = array();
        $fields = $this->getFields($formId);
        $fieldsArray = (array) $fields;
        foreach ($fields as $f) {
            $res = $this->checkInvalid($f, isset($data[$f->key]) ? $data[$f->key] : null, $value, $validate, $formId, $data["id"]);
            $value = $res["value"];
            $validate = $res["validate"];
        }
        $keys = (array) $this->getFieldKeys($fields);
        $auth = new Auth();
        if ($validateForm === 1) {
            $auth->check();
        }
        if (count($validate) == 0) {
            if ($auth->check()) {
                $jwt = $auth->jwt;
                $created_by = (int) $jwt->user->id;
                $updated_by = (int) $jwt->user->id;
                if (isset($jwt->user->group_data_id)) {
                    $groupDataId = $jwt->user->group_data_id;
                }
            }
            $fieldString = "";
            $valueString = "";
            $fieldValueString = "";
            foreach ($data as $key => $item) {
                $field = array_column($fieldsArray, null, "key")[$key] ?? null;
                if (!is_null($field)) {
                    $fieldValueString .= " " . $key . " = '" . $this->formatDataSave((object) $field, $item) . "' , ";
                }
            }
            $sqlQuery = "UPDATE tbl_data_form" . $formId . " SET " . $fieldValueString . "  group_data_id, updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE id = " . $data["id"];
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param("ii", $groupDataId, $updated_by);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $item = $this->findById($formId, $data["id"], true, null);
                return (object) [
                    "data" => (object) $item,
                    "validate" => (object) $validate,
                    "status" => true,
                    "msg" => MSG_UPDATE_SUCCESSFUL,
                ];
            } else {
                return (object) [
                    "status" => false,
                    "msg" => MSG_UPDATE_FAILED,
                ];
            }
        } else {
            return (object) [
                "data" => null,
                "validate" => (object) $validate,
                "status" => false,
                "msg" => MSG_INVALID_DATA,
            ];
        }
    }

    // DELETE
    private function delete($formId)
    {
        $validateForm = $this->checkForm($formId);
        if ($validateForm == -1) {
            return (object) [
                "status" => false,
                "msg" => "Form không tồn tại",
            ];
        }
        $auth = new Auth();
        if ($validateForm === 1) {
            $auth->check();
        }
        $updated_by = null;
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $updated_by = (int) $jwt->user->id;
        }
        try {
            $data = json_decode(file_get_contents("php://input"));
            $ids = isset($data->ids) && is_array($data->ids) ? $data->ids : array();
            $sql = "UPDATE tbl_data_form" . $formId . " SET deleted = 1, updated_at = CURRENT_TIMESTAMP, updated_by=? WHERE id in (" . implode(", ", $ids) . ")";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $updated_by);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $data = array("ids" => $ids);
                $res = array("data" => $data);
                return $res;
            }
        } catch (Exception $e) {
        }
        return false;
    }

    // api public
    public function apiCreate($data, $params)
    {
        $this->params = $params;
        $formId = $data->formId;
        $this->validateForm($formId, "create");
        $_data = (array) json_decode(file_get_contents("php://input"));
        $res = $this->create($_data, $formId);
        $this->connect->sendResponse(200, $res);
    }

    public function apiCodeCreate($data, $params)
    {
        $formCode = $data->formCode;
        $form = new Form;
        $formId = $form->findIdByCode($formCode);
        if (!is_null($formId)) {
            $data->formId = $formId;
            $this->apiCreate($data, $params);
        } else {
            $this->connect->sendResponse(404);
        }
    }

    // api public
    public function apiFindById($data, $params)
    {
        $this->params = $params;
        $id = isset($data->id) ? $data->id : null;
        $formId = isset($data->formId) ? $data->formId : null;
        $this->validateForm($formId, "view");
        if (!is_null($id) && !is_null($formId)) {
            $res = $this->findById($formId, $id, true, null);
            if ($res) {
                $this->connect->sendResponse(200, array(
                    "data" => $res,
                    "status" => true,
                    "msg" => MSG_FETCH_SUCCESSFUL,
                ));
            } else {
                $this->connect->sendResponse(200, array(
                    "data" => null,
                    "status" => false,
                    "msg" => MSG_DATA_NOTFOUND,
                ));
            }
        } else {
            $msg = "";
            if (is_null($id)) {
                $msg = "id is required";
            } else if (is_null($formId)) {
                $msg = "id is required";
            }
            $this->connect->sendResponse(400, array(
                "data" => null,
                "status" => false,
                "msg" => MSG_DATA_NOTFOUND,
            ));
        }
    }

    public function apiCodeFindById($data, $params)
    {
        $formCode = $data->formCode;
        $form = new Form;
        $formId = $form->findIdByCode($formCode);
        $this->validateForm($formId, "view");
        if (!is_null($formId)) {
            $data->formId = $formId;
            $this->apiFindById($data, $params);
        } else {
            $this->connect->sendResponse(404);
        }
    }

    public function apiUpdate($data, $params)
    {
        $this->params = $params;
        $formId = $data->formId;
        $this->validateForm($formId, "update");
        $data = (array) json_decode(file_get_contents("php://input"));
        $res = $this->update($data, $formId);
        $this->connect->sendResponse(200, $res);
    }

    public function apiCodeUpdate($data, $params)
    {
        $formCode = $data->formCode;
        $form = new Form;
        $formId = $form->findIdByCode($formCode);
        $this->validateForm($formId, "update");
        if (!is_null($formId)) {
            $data->formId = $formId;
            $this->apiUpdate($data, $params);
        } else {
            $this->connect->sendResponse(404);
        }
    }

    public function apiDelete($data, $params)
    {
        $this->params = $params;
        $formId = $data->formId;
        $this->validateForm($formId, "delete");
        $res = $this->delete($formId);
        if ($res) {
            $res["status"] = true;
            $res["msg"] = MSG_DELETE_SUCCESSFUL;
            $this->connect->sendResponse(200, $res);
        } else {
            $this->connect->sendResponse(202, array(
                "data" => null,
                "status" => false,
                "msg" => MSG_DELETE_FAILED,
            ));
        }
    }
    public function apiCodeDelete($data, $params)
    {
        $formCode = $data->formCode;
        $form = new Form;
        $formId = $form->findIdByCode($formCode);
        if (!is_null($formId)) {
            $data->formId = $formId;
            $this->apiDelete($data, $params);
        } else {
            $this->connect->sendResponse(404);
        }
    }

    public function apiFetchAll($data, $params)
    {
        $this->params = $params;
        $formId = $data->formId;
        $this->validateForm($formId, "view");
        $res = (object) $this->fetchAll($formId);
        $res->status = true;
        $res->msg = MSG_FETCH_SUCCESSFUL;
        return $this->connect->sendResponse(200, $res);
    }
    public function apiCodeFetchAll($data, $params)
    {
        $formCode = $data->formCode;
        $form = new Form;
        $formId = $form->findIdByCode($formCode);
        if (!is_null($formId)) {
            $data->formId = $formId;
            $this->apiFetchAll($data, $params);
        } else {
            $this->connect->sendResponse(404);
        }
    }
}
