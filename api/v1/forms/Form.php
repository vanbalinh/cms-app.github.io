<?php
namespace V1;

use \Common\Format;
use \Common\Pagination;
use \Config\Connect;
use \V1\Auth;
use \V1\Folder;
use \V1\Form\FormField;
use \V1\Form\Permission;
use \V1\Form\PermissionField;

class Form
{
    // Connection
    private $conn;
    private $connect;

    // Table
    private $db_table = "tbl_forms";

    // Columns
    public $id;
    public $folder_id;
    public $name;
    public $config;
    public $construct;
    public $fields;
    public $description;
    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;

    private $params;

    public function __construct()
    {
        $parameters = func_get_args();
        if (isset($parameters[0])) {
            $this->construct1($parameters[0]);
        }
        $this->connect = new Connect();
        $this->conn = $this->connect->conn;
    }

    private function construct1($form)
    {
        $format = new Format;
        $acc = new Account();
        $fields = null;
        try {
            $fields = json_decode(isset($form["fields"]) ? $form["fields"] : "[]");
        } catch (Exception $e) {}
        $this->id = isset($form["id"]) && is_numeric($form["id"]) ? (int) $form["id"] : null;
        $this->folder_id = isset($form["folder_id"]) && is_numeric($form["folder_id"]) ? (int) $form["folder_id"] : null;
        $this->name = isset($form["name"]) ? $form["name"] : null;
        $this->code = isset($form["code"]) ? $form["code"] : null;
        $this->fields = $fields;
        $this->description = isset($form["description"]) ? $form["description"] : null;
        $this->created_at = $format->DateTime($form["created_at"]);
        $this->created_by = $acc->by($form["created_by"]);
        $this->updated_at = $format->DateTime($form["updated_at"]);
        $this->updated_by = $acc->by($form["updated_by"]);
    }

    private function getDetailInfo($form)
    {
        $format = new Format;
        $acc = new Account();
        $folder = new Folder();
        $res = (object) [];
        $res->id = isset($form["id"]) && is_numeric($form["id"]) ? (int) $form["id"] : null;
        $res->folder = isset($form["folder_id"]) && is_numeric($form["folder_id"]) ? $folder->findById((int) $form["folder_id"]) : null;
        $res->name = isset($form["name"]) ? $form["name"] : null;
        $res->code = isset($form["code"]) ? $form["code"] : null;
        $res->checkAccess = isset($form["check_access"]) && $form["check_access"] == 1 ? true : false;
        $res->showView = isset($form["show_view"]) && $form["show_view"] == 1 ? true : false;
        $res->fields = isset($form["fields"]) ? $form["fields"] : array();
        $res->description = isset($form["description"]) ? $form["description"] : null;
        $res->createdAt = $format->DateTimeConvert($form["created_at"]);
        $res->createdBy = $acc->by($form["created_by"]);
        $res->updatedAt = $format->DateTimeConvert($form["updated_at"]);
        $res->updatedBy = $acc->by($form["updated_by"]);
        return $res;
    }

    private function where($folderId = null)
    {
        $where = "deleted = 0 ";
        if (isset($this->params->q)) {
            $where .= " AND ((name is not null AND name like '%" . $this->params->q . "%')";
            $where .= " OR ((description is not null AND description like '%" . $this->params->q . "%')";
        }
        /**
         *  Name
         */
        if (isset($this->params->name)) {
            $where .= " AND name = '" . $this->params->name . "'";
        }

        /**
         *  End Name
         */

        /**
         *  Description
         */
        if (isset($this->params->description)) {
            $where .= " AND description = '" . $this->params->description . "'";
        }

        /**
         *  End Description
         */

        /**
         *  parent_id
         */
        if (is_numeric($folderId)) {
            $where .= " AND folder_id = " . (int) $folderId;
        }

        /**
         *  End parent_id
         */
        return $where;
    }

    private function count($folderId)
    {
        $sqlCount = "SELECT count(*) FROM " . $this->db_table . " WHERE " . $this->where($folderId);
        $stmtCount = $this->conn->query($sqlCount);
        $row = $stmtCount->fetch_row();
        return (int) $row[0];
    }

    // fetch all
    private function fetchAll($folderId = null)
    {
        $res = array();
        $res["data"] = array();
        $sqlQuery = "SELECT
            id,
            folder_id,
            name,
            code,
            check_access,
            show_view,
            description,
            created_at,
            updated_at,
            created_by,
            updated_by
        FROM " . $this->db_table . " WHERE " . $this->where($folderId);
        $page = isset($this->params->page) && is_numeric($this->params->page) ? (int) $this->params->page : null;
        $pageSize = isset($this->params->pageSize) && is_numeric($this->params->pageSize) ? (int) $this->params->pageSize : null;

        $pg = new Pagination($page, $pageSize, $this->count($folderId));
        if ($pg->check()) {
            $sqlQuery .= $pg->getSql();
            $res["pagination"] = $pg;
        }
        $stmt = $this->conn->query($sqlQuery);

        while ($row = $stmt->fetch_assoc()) {
            array_push($res["data"], $this->getDetailInfo($row));
        }

        $res["status"] = true;
        $res["msg"] = MSG_FETCH_SUCCESSFUL;
        return $res;
    }

    private function checkExistCode($code, $id = null)
    {
        if (is_null($code)) {
            return false;
        }
        $sql = "SELECT
            id,
            code
        FROM " . $this->db_table . "
        WHERE  " . (!is_null($id) ? "id <> " . $id . " AND " : "") . " code = '" . $code . "' AND deleted = 0";
        $stmt = $this->conn->query($sql);
        if ($stmt->num_rows == 0) {
            return false;
        }
        return true;
    }

    // Find by id
    public function findById($id, $hasFields = true)
    {
        $fields = new FormField();
        $sql = "SELECT
            id,
            folder_id,
            name,
            code,
            check_access,
            show_view,
            description,
            created_at,
            created_by,
            updated_at,
            updated_by
        FROM " . $this->db_table . "
        WHERE  id = " . $id . " AND deleted = 0 LIMIT 0,1";
        $stmt = $this->conn->query($sql);

        if ($stmt->num_rows == 0) {
            return null;
        }
        $dataRow = $stmt->fetch_assoc();
        $dataRow["fields"] = array();
        if ($hasFields) {
            $dataRow["fields"] = $fields->fetchAll($id, true);
        }
        return $this->getDetailInfo($dataRow);
    }

    public function findIdByCode($code)
    {
        $fields = new FormField();
        $sql = "SELECT
            id
        FROM " . $this->db_table . "
        WHERE  code = '" . $code . "' AND deleted = 0 LIMIT 0,1";
        $stmt = $this->conn->query($sql);

        if ($stmt->num_rows == 0) {
            return null;
        }
        $dataRow = $stmt->fetch_assoc();
        return $dataRow["id"];
    }
    private function formatDataInput($f)
    {
        $required = isset($f->required) && $f->required ? true : false;

        $types = ['text', 'int', 'float', 'date', 'date-time', 'upload', 'select', 'reference'];
        $type = isset($f->type) && in_array($f->type, $types) ? $f->type : "text";
        $description = isset($f->description) ? $f->description : null;
        $sort = isset($f->sort) && is_numeric($f->sort) ? (int) $f->sort : 0;
        $required = isset($f->required) && $f->required == true ? 1 : 0;
        $unique = isset($f->unique) && $f->unique == true ? 1 : 0;
        $multiple = isset($f->multiple) && $f->multiple == true ? 1 : 0;

        $max_length = isset($f->max_length) && is_numeric($f->max_length) ? (int) $f->max_length : null;
        $max = isset($f->max) && is_numeric($f->max) ? (int) $f->max : null;
        $min = isset($f->min) && is_numeric($f->min) ? (int) $f->min : null;
        $reference_id = isset($f->referenceId) && is_numeric($f->referenceId) ? $f->referenceId : null;
        $label_key = isset($f->labelKey) ? $f->labelKey : null;
        $sql_where = isset($f->sqlWhere) ? $f->sqlWhere : null;
        $min_date = isset($f->minDate) ? $f->minDate : null;
        $max_date = isset($f->maxDate) ? $f->maxDate : null;
        $min_datetime = isset($f->minDateTime) ? $f->minDateTime : null;
        $max_datetime = isset($f->maxDateTime) ? $f->maxDateTime : null;

        $d = [
            "name" => $f->name,
            "api_key" => $f->key,
            "type" => $type,
            "description" => $description,
            "sort" => $sort,
            "is_required" => $required,
            "is_unique" => $unique,
            "is_multiple" => $multiple,
            "max_length" => $max_length,
            "num_max" => $max,
            "num_min" => $min,
            "reference_id" => $reference_id,
            "label_key" => $label_key,
            "sql_where" => $sql_where,
            "min_date" => $min_date,
            "max_date" => $max_date,
            "min_datetime" => $min_datetime,
            "max_datetime" => $max_datetime,
        ];
        return $d;
    }

    private function runCreateTable($data, $id)
    {
        $fieldsSql = "";
        if (is_array($data->fields)) {
            foreach ($data->fields as $f) {
                $required = isset($f->required) && $f->required ? true : false;
                $sqlF = ",
                    " . $f->key . " text " . " COLLATE utf8_vietnamese_ci " . ($required ? " NOT NULL " : " NULL ");
                $fieldsSql .= $sqlF;
            }
        }

        $tbl_name = "tbl_data_form" . $id;
        $result = $this->conn->query("SHOW TABLES LIKE '" . $tbl_name . "'");
        if ($result->num_rows == 1) {
            $result1 = $this->conn->query("DROP TABLE " . $tbl_name);
        }
        $sqlCreateTable = "CREATE TABLE " . $tbl_name . " (
            `id` int(11) NOT NULL " . $fieldsSql . ",
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
            $this->conn->query($sqlCreateTable)
            && $this->conn->query($sqlCreateTable1)
            && $this->conn->query($sqlCreateTable2)
            && $this->conn->query($sqlCreateTable3)
        ) {
            return true;
        } else {
            return false;
        }
    }

    // create
    public function create($data)
    {
        $auth = new Auth();

        $created_by = null;
        $updated_by = null;
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $created_by = (int) $jwt->user->id;
            $updated_by = (int) $jwt->user->id;
        }
        if ($this->checkExistCode(isset($data->code) ? $data->code : null, null)) {
            return (object) [
                "msg" => MSG_INVALID_DATA,
                "status" => false,
            ];
        }
        try {
            $folderId = isset($data->folder) && isset($data->folder->id) && is_numeric($data->folder->id) ? $data->folder->id : null;
            $check_access = isset($data->checkAccess) && $data->checkAccess == false ? 0 : 1;
            $show_view = isset($data->showView) && $data->showView == true ? 1 : 0;
            $sqlQuery = "INSERT INTO " . $this->db_table . " (folder_id,name,code,check_access,show_view,description,created_by,updated_by) VALUES (?,?,?,?,?,?,?,?)";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param("ssssssii", $folderId, $data->name, $data->code, $check_access, $show_view, $description, $created_by, $updated_by);
            $stmt->execute();

            if ($stmt->affected_rows == 1) {
                $newId = $this->conn->insert_id;
                if (is_array($data->fields)) {
                    $insertFieldSuccess = true;
                    foreach ($data->fields as $f) {
                        if (isset($f->name) && isset($f->key)) {
                            $formFields = new FormField();
                            $d = $this->formatDataInput($f);
                            $formFieldId = $formFields->create($d, $newId);
                            if (!$formFieldId) {
                                $insertFieldSuccess = false;
                                break;
                            }
                        }
                    }
                }
                // $fieldsSql = "";
                // if (is_array($data->fields)) {
                //     foreach ($data->fields as $f) {
                //         $required = isset($f->required) && $f->required ? true : false;
                //         $sqlF = ",
                //             " . $f->key . " text " . " COLLATE utf8_vietnamese_ci " . ($required ? " NOT NULL " : " NULL ");
                //         $fieldsSql .= $sqlF;
                //     }
                // }

                // $tbl_name = "tbl_data_form" . $newId;
                // $sqlCreateTable = "CREATE TABLE " . $tbl_name . " (
                //     `id` int(11) NOT NULL " . $fieldsSql . ",
                //     `group_data_id` int(11) NULL,
                //     `view` int(11) NOT NULL DEFAULT 0,
                //     `deleted` tinyint(1) NOT NULL DEFAULT 0,
                //     `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                //     `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                //     `created_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL,
                //     `updated_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL
                // ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_vietnamese_ci;";
                // $sqlCreateTable1 = "ALTER TABLE `" . $tbl_name . "` ADD PRIMARY KEY (`id`);";
                // $sqlCreateTable2 = "ALTER TABLE `" . $tbl_name . "` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
                // $sqlCreateTable3 = "ALTER TABLE `" . $tbl_name . "` AUTO_INCREMENT = 1000000000;";

                if ($this->runCreateTable($data, $newId)) {
                    return (object) [
                        "data" => $this->findById($newId),
                        "msg" => MSG_CREATE_SUCCESSFUL,
                        "status" => true,
                    ];
                }
                // if (
                //     $this->conn->query($sqlCreateTable)
                //     && $this->conn->query($sqlCreateTable1)
                //     && $this->conn->query($sqlCreateTable2)
                //     && $this->conn->query($sqlCreateTable3)
                // ) {
                //     if ($insertFieldSuccess) {
                //         return (object) [
                //             "data" => $this->findById($newId),
                //             "msg" => MSG_CREATE_SUCCESSFUL,
                //             "status" => true,
                //         ];
                //     }
                // }
            }
        } catch (Exception $e) {}
        return (object) [
            "msg" => MSG_CREATE_FAILED,
            "status" => false,
        ];
    }

    // UPDATE
    private function update()
    {
        $auth = new Auth();
        $config = null;
        $construct = null;
        $fields = null;
        $updated_by = null;
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $updated_by = (int) $jwt->user->id;
        }
        $data = json_decode(file_get_contents("php://input"));
        if ($this->checkExistCode(isset($data->code) ? $data->code : null, $data->id)) {
            return (object) [
                "msg" => MSG_INVALID_DATA,
                "status" => false,
            ];
        }
        try {
            $folderId = isset($data->folder) && isset($data->folder->id) && is_numeric($data->folder->id) ? $data->folder->id : null;
            $check_access = isset($data->checkAccess) && $data->checkAccess == false ? 0 : 1;
            $showView = isset($data->showView) && $data->showView == true ? 1 : 0;
            $sql = "UPDATE " . $this->db_table . " SET folder_id = ?, name = ?, code = ?, check_access = ?,show_view = ?, description = ?, updated_at = CURRENT_TIMESTAMP, updated_by=? WHERE  id = " . $data->id;
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssssi", $folderId, $data->name, $data->code, $check_access, $showView, $data->description, $updated_by);
            $stmt->execute();
            if ($stmt->affected_rows == 1) {
                $formFields = new FormField();
                if (is_array($data->fields)) {

                    $fieldsNotIn = array();
                    foreach ($data->fields as $f) {
                        if (isset($f->id)) {
                            array_push($fieldsNotIn, $f->id);
                        }
                    }
                    $formFields->delete(array($data->id), $fieldsNotIn);
                    $insertFieldSuccess = true;
                    foreach ($data->fields as $f) {
                        if (isset($f->name) && isset($f->key)) {
                            $d = $this->formatDataInput($f);
                            if (isset($f->id)) {
                                $d["id"] = $f->id;
                                $formFieldId = $formFields->update($d, $data->id);
                            } else {
                                $formFieldId = $formFields->create($d, $data->id);
                                $f->id = $formFieldId;
                            }
                            if (!$formFieldId) {
                                $insertFieldSuccess = false;
                                break;
                            }
                        }
                    }
                }
                // $fieldsSql = "";
                // foreach ($data->fields as $f) {
                //     $required = isset($f->required) && $f->required ? true : false;
                //     $sqlF = ",
                //     " . $f->key . " varchar(255) " . " COLLATE utf8_vietnamese_ci " . ($required ? " NOT NULL " : " NULL ");
                //     $fieldsSql .= $sqlF;
                // }
                // $tbl_name = "tbl_data_form" . $data->id;

                // Kiểm tra tbl tồn tại
                // $result = $this->conn->query("SHOW TABLES LIKE '" . $tbl_name . "'");
                // if ($result->num_rows == 1) {
                //     $result1 = $this->conn->query("DROP TABLE " . $tbl_name);
                // }
                // $sqlCreateTable = "CREATE TABLE " . $tbl_name . " (
                //     `id` int(11) NOT NULL " . $fieldsSql . ",
                //     `group_data_id` int(11) NULL,
                //     `view` int(11) NOT NULL DEFAULT 0,
                //     `deleted` tinyint(1) NOT NULL DEFAULT 0,
                //     `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                //     `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
                //     `created_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL,
                //     `updated_by` int(11) COLLATE utf8_vietnamese_ci DEFAULT NULL
                // ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_vietnamese_ci;";
                // $sqlCreateTable1 = "ALTER TABLE `" . $tbl_name . "` ADD PRIMARY KEY (`id`);";
                // $sqlCreateTable2 = "ALTER TABLE `" . $tbl_name . "` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
                // $sqlCreateTable3 = "ALTER TABLE `" . $tbl_name . "` AUTO_INCREMENT = 1000000000;";

                // if (
                //     $this->conn->query($sqlCreateTable)
                //     && $this->conn->query($sqlCreateTable1)
                //     && $this->conn->query($sqlCreateTable2)
                //     && $this->conn->query($sqlCreateTable3)
                // ) {
                //     if ($insertFieldSuccess) {
                //         return (object) [
                //             "data" => $this->findById($data->id),
                //             "msg" => MSG_UPDATE_SUCCESSFUL,
                //             "status" => true,
                //         ];
                //     }
                // }
                if ($this->runCreateTable($data, $data->id)) {
                    return (object) [
                        "data" => $this->findById($data->id),
                        "msg" => MSG_UPDATE_SUCCESSFUL,
                        "status" => true,
                    ];
                }
            }
        } catch (Exception $e) {}
        return (object) [
            "msg" => MSG_UPDATE_FAILED,
            "status" => false,
        ];
    }

    // DELETE
    private function delete()
    {
        $auth = new Auth();
        $updated_by = null;
        if ($auth->check()) {
            $jwt = $auth->jwt;
            $updated_by = (int) $jwt->user->id;
        }
        try {
            $data = json_decode(file_get_contents("php://input"));
            $ids = isset($data->ids) && is_array($data->ids) ? $data->ids : array();
            $sql = "UPDATE " . $this->db_table . " SET deleted = 1, updated_at = CURRENT_TIMESTAMP, updated_by=? WHERE id in (" . implode(", ", $ids) . ")";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $updated_by);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $formFields = new FormField();
                $formFields->delete($data->ids);
                if (
                    isset($this->params->pageSize)
                    && is_numeric($this->params->pageSize)
                    && isset($this->params->pageSize)
                    && is_numeric($this->params->pageSize)
                ) {
                    $res = $this->fetchAll();
                } else {
                    $data = array("ids" => $ids);
                    $res = array("data" => $data);
                }
                return $res;
            }
        } catch (Exception $e) {
        }
        return false;
    }

    private function updateFormPermission($data)
    {
        $e400 = false;
        $formPermission = new Permission;
        $p_account = array();
        $p_group = array();
        $p_role = array();
        if (isset($data->formId) && isset($data->permission)) {
            $formId = $data->formId;
            $permission = $data->permission;
        } else {
            $e400 = true;
            goto end;
        }

        if (isset($permission->account) && is_array($permission->account)) {
            foreach ($permission->account as $p) {
                if (isset($p->id) && is_numeric($p->id)) {
                    array_push($p_account, (object) [
                        "id" => (int) $p->id,
                        "view" => isset($p->view) && is_bool($p->view) && $p->view ? true : false,
                        "create" => isset($p->create) && is_bool($p->create) && $p->create ? true : false,
                        "update" => isset($p->update) && is_bool($p->update) && $p->update ? true : false,
                        "delete" => isset($p->delete) && is_bool($p->delete) && $p->delete ? true : false,
                    ]);
                } else {
                    $e400 = true;
                    goto end;
                }
            }
        }
        if (isset($permission->group) && is_array($permission->group)) {
            foreach ($permission->group as $p) {
                if (isset($p->id) && is_numeric($p->id)) {
                    array_push($p_group, (object) [
                        "id" => (int) $p->id,
                        "view" => isset($p->view) && is_bool($p->view) && $p->view ? true : false,
                        "create" => isset($p->create) && is_bool($p->create) && $p->create ? true : false,
                        "update" => isset($p->update) && is_bool($p->update) && $p->update ? true : false,
                        "delete" => isset($p->delete) && is_bool($p->delete) && $p->delete ? true : false,
                    ]);
                } else {
                    $e400 = true;
                    goto end;
                }
            }
        }
        if (isset($permission->role) && is_array($permission->role)) {
            foreach ($permission->role as $p) {
                if (isset($p->id) && is_numeric($p->id)) {
                    array_push($p_role, (object) [
                        "id" => (int) $p->id,
                        "view" => isset($p->view) && is_bool($p->view) && $p->view ? true : false,
                        "create" => isset($p->create) && is_bool($p->create) && $p->create ? true : false,
                        "update" => isset($p->update) && is_bool($p->update) && $p->update ? true : false,
                        "delete" => isset($p->delete) && is_bool($p->delete) && $p->delete ? true : false,
                    ]);
                } else {
                    $e400 = true;
                    goto end;
                }
            }
        }
        $formPermissonData = (object) [
            "formId" => $formId,
            "permissionAccounts" => $p_account,
            "permissionGroups" => $p_group,
            "permissionRoles" => $p_role,
        ];
        end:
        return $e400 ? 400 : ($formPermission->create($formPermissonData) ? 0 : 1);
    }

    private function updateFormFieldPermission($data)
    {
        $p_account = array();
        $p_group = array();
        $p_role = array();
        $e400 = false;
        $formFieldPermission = new PermissionField;
        if (isset($data->formId) && isset($data->formFieldId) && isset($data->permission)) {
            $formId = $data->formId;
            $permission = $data->permission;
            $formFieldId = $data->formFieldId;
        } else {
            $e400 = true;
            goto end;
        }

        if (isset($permission->account) && is_array($permission->account)) {
            foreach ($permission->account as $p) {
                if (isset($p->id) && is_numeric($p->id)) {
                    array_push($p_account, (object) [
                        "id" => (int) $p->id,
                        "view" => isset($p->view) && is_bool($p->view) && $p->view ? true : false,
                        "create" => isset($p->create) && is_bool($p->create) && $p->create ? true : false,
                        "update" => isset($p->update) && is_bool($p->update) && $p->update ? true : false,
                    ]);
                } else {
                    $e400 = true;
                    goto end;
                }
            }
        }
        if (isset($permission->group) && is_array($permission->group)) {
            foreach ($permission->group as $p) {
                if (isset($p->id) && is_numeric($p->id)) {
                    array_push($p_group, (object) [
                        "id" => (int) $p->id,
                        "view" => isset($p->view) && is_bool($p->view) && $p->view ? true : false,
                        "create" => isset($p->create) && is_bool($p->create) && $p->create ? true : false,
                        "update" => isset($p->update) && is_bool($p->update) && $p->update ? true : false,
                    ]);
                } else {
                    $e400 = true;
                    goto end;
                }
            }
        }
        if (isset($permission->role) && is_array($permission->role)) {
            foreach ($permission->role as $p) {
                if (isset($p->id) && is_numeric($p->id)) {
                    array_push($p_role, (object) [
                        "id" => (int) $p->id,
                        "view" => isset($p->view) && is_bool($p->view) && $p->view ? true : false,
                        "create" => isset($p->create) && is_bool($p->create) && $p->create ? true : false,
                        "update" => isset($p->update) && is_bool($p->update) && $p->update ? true : false,
                    ]);
                } else {
                    $e400 = true;
                    goto end;
                }
            }
        }
        $formFieldPermissonData = (object) [
            "formId" => $formId,
            "formFieldId" => $formFieldId,
            "permissionAccounts" => $p_account,
            "permissionGroups" => $p_group,
            "permissionRoles" => $p_role,
        ];
        end:
        return $e400 ? 400 : ($formFieldPermission->create($formFieldPermissonData) ? 0 : 1);
    }

    /**
     *  Public
     */
    public function apiFetchAll($data, $params)
    {
        $this->params = $params;
        $folderId = isset($data->folderId) ? $data->folderId : null;
        $res = $this->fetchAll($folderId);
        return $this->connect->sendResponse(200, $res);
    }

    public function apiFindById($data)
    {
        $id = $data->formId;
        $res = $this->findById($id, true);
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
    }

    public function apiCheckPermissionById($data)
    {
        $id = $data->formId;
        $permission = new Permission;
        $res = $permission->getPermission($id);
        return $this->connect->sendResponse(200, array(
            "data" => (object) [
                "permission" => $res,
            ],
            "status" => true,
            "msg" => MSG_FETCH_SUCCESSFUL,
        ));
    }

    public function apiCreate($data)
    {
        $_data = json_decode(file_get_contents("php://input"));
        $res = $this->create($_data);
        $this->connect->sendResponse(200, $res);
    }

    public function apiUpdate()
    {
        $res = $this->update();
        $this->connect->sendResponse(200, $res);
    }

    public function apiDelete($data, $params)
    {
        $this->params = $params;
        $res = $this->delete();
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

    public function apiFetchAllFieldType()
    {
        $data = [
            ["value" => "text", "label" => "text", "fields" => []],
            ["value" => "int", "label" => "int", "fields" => ["min", "max"]],
            ["value" => "float", "label" => "float", "fields" => ["min", "max"]],
            ["value" => "date", "label" => "date", "fields" => []],
            ["value" => "date-time", "label" => "date-time", "fields" => []],
            ["value" => "upload", "label" => "upload", "fields" => ["multiple"]],
            ["value" => "select", "label" => "select", "fields" => ["referenceId", "multiple", "labelKey"]],
            ["value" => "reference", "label" => "reference", "fields" => ["referenceId"]],
        ];
        $this->connect->sendResponse(200, array(
            "data" => $data,
            "status" => true,
            "msg" => MSG_FETCH_SUCCESSFUL,
        ));
    }

    public function apiUpdateFormPermission()
    {
        $_data = json_decode(file_get_contents("php://input"));
        $error = $this->updateFormPermission($_data);
        switch ($error) {
            case 0:
                return $this->connect->sendResponse(200, array(
                    "status" => true,
                    "msg" => MSG_UPDATE_SUCCESSFUL,
                ));
            case 400:
                return $this->connect->sendResponse(400);
            default:
                return $this->connect->sendResponse(200, array(
                    "status" => false,
                    "msg" => MSG_UPDATE_FAILED,
                ));
        }
    }

    public function apiUpdateFormFieldPermission()
    {
        $_data = json_decode(file_get_contents("php://input"));
        $error = $this->updateFormFieldPermission($_data);
        switch ($error) {
            case 0:
                return $this->connect->sendResponse(200, array(
                    "status" => true,
                    "msg" => MSG_UPDATE_SUCCESSFUL,
                ));
            case 400:
                return $this->connect->sendResponse(400);
            default:
                return $this->connect->sendResponse(200, array(
                    "status" => false,
                    "msg" => MSG_UPDATE_FAILED,
                ));
        }
    }

    public function invalidMethod()
    {
        $this->connect->sendResponse(500, "INVALID METHOD");
    }
}
