<?php
namespace Controller;

class ControllerCore
{
    public $conn;
    public $connect;
    public $table_name;
    public $column;
    public $hiddenItemInfo = false;
    public $auth;
    public $error_description = array();

    public $authId = null;
    public $field;

    public $account;
    private $by;
    private $format;
    public $config;

    private $initData;
    private $cacheData;
    public function __construct()
    {
        $this->cacheData = (object) [];
        $this->account = (object) [];
        $this->field = (object) [];
        $this->config = (object) [];

        $_auth = "\\V" . API_VERSION . "\\Auth";
        if (class_exists($_auth)) {
            $this->auth = new $_auth();

        }
        $this->format = new \Controller\ControllerFormat;
    }

    public function getJWT()
    {
        if ($this->auth->check()) {
            return $this->auth->jwt;
        }
        return null;
    }

    public function getAuthId()
    {
        if ($this->auth->check()) {
            $jwt = $this->auth->jwt;
            return $jwt->user->id;
        }
        return null;
    }

    public function query($sql)
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
        $query = $this->conn->query($sql);
        return $query;
    }

    public function prepare($sql)
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
        $prepare = $this->conn->prepare($sql);
        return $prepare;
    }
    public function init($table_name, $field = null, $config = null)
    {
        $this->initData = (object) [
            "table_name" => $table_name,
            "field" => (object) $field,
            "config" => (object) $config,
        ];
        $this->table($table_name);
        $this->initField($field);
        $this->setConfig($config);
    }

    private function table($table_name)
    {
        $allColumns = (object) [];
        if (is_string($table_name)) {
            $tableExist = $this->query("SHOW TABLES LIKE '" . $table_name . "'");
            if ($tableExist->num_rows === 1) {
                $this->table_name = $table_name;

                $sttmColumns = $this->query("SHOW COLUMNS FROM " . $table_name);
                if ($sttmColumns->num_rows > 0) {
                    while ($col = $sttmColumns->fetch_assoc()) {
                        $colName = $col["Field"];
                        $allColumns->$colName = (object) [
                            "field" => $col["Field"],
                            "type" => $col["Type"],
                            "null" => $col["Null"] === "YES" ? true : false,
                            "key" => $col["Key"],
                            "default" => $col["Default"],
                            "extra" => $col["Extra"],
                            "system" => in_array($col["Field"], ["id", "deleted", "created_at", "updated_at", "created_by", "updated_by"]) ? true : false,
                        ];
                    }
                }

                $sttmColumns = $this->query("SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE table_name='" . $table_name . "'");
                if ($sttmColumns->num_rows > 0) {
                    while ($col = $sttmColumns->fetch_assoc()) {
                        $colName = $col["COLUMN_NAME"];
                        $allColumns->$colName->props = (object) $col;
                    }
                }
                $this->column = $allColumns;
            }
        }
    }

    private function initField($field = null)
    {
        $this->field->id = (object) [];
        if (is_array($field) || is_object($field)) {
            foreach ($field as $fieldName => $f) {
                if (is_string($fieldName) && isset($this->column->$fieldName)) {
                    $this->field->$fieldName = (object) [];
                    $this->field->$fieldName->system = false;
                    $this->field->$fieldName->public = true;
                    $this->field->$fieldName->key = $fieldName;
                    $this->field->$fieldName->name = $fieldName;
                    $this->field->$fieldName->type = "string";
                    $this->field->$fieldName->required = false;
                    $this->field->$fieldName->unique = false;

                    $this->field->$fieldName->name = (isset($f->name) ? $f->name : $fieldName);
                    $this->field->$fieldName->type = (isset($f->type) ? $f->type : "string");
                    $this->field->$fieldName->required = (isset($f->required) && is_bool($f->required) ? $f->required : false);
                    $this->field->$fieldName->public = (isset($f->public) && is_bool($f->public) ? $f->public : true);
                    $this->field->$fieldName->unique = (isset($f->unique) && is_bool($f->unique) ? $f->unique : false);
                    $this->field->$fieldName->uniqueValue = (isset($f->uniqueValue) ? $f->uniqueValue : null);
                    $this->field->$fieldName->allow_update = (isset($f->allow_update) && is_bool($f->allow_update) ? $f->allow_update : true);
                    $this->field->$fieldName->replace_value = (isset($f->replace_value) && is_array($f->replace_value) ? $f->replace_value : null);

                    if ($this->field->$fieldName->type === "object") {
                        if (isset($f->object_name) && class_exists($f->object_name)) {
                            $this->field->$fieldName->object_class = new $f->object_name;
                            $this->field->$fieldName->object_fields = (isset($f->object_fields) && is_array($f->object_fields) ? $f->object_fields : []);
                            $this->field->$fieldName->method = isset($f->method) ? $f->method : "apiFindById";
                            $this->field->$fieldName->data = isset($f->data) && is_object($f->data) ? $f->data : (object) [];
                            $this->field->$fieldName->multiple = isset($f->multiple) && is_bool($f->multiple) ? $f->multiple : false;

                        } else {
                            $this->field->$fieldName->type = "string";
                        }
                    }

                }
            }
        }

        /**
         *  Systemm config
         */
        foreach (["deleted", "created_at", "updated_at", "created_by", "updated_by"] as $fieldName) {
            $this->field->$fieldName = (object) [];
            $this->field->$fieldName->system = true;
            $this->field->$fieldName->system = $fieldName;
            $this->field->$fieldName->public = true;
        }
        $this->field->id->system = true;
        $this->field->id->public = true;
        $this->field->id->key = "id";
        $this->field->id->name = "id";
        $this->field->id->type = "int";

        $this->field->created_at->name = "createdAt";
        $this->field->updated_at->name = "updatedAt";
        $this->field->created_by->name = "createdBy";
        $this->field->updated_by->name = "updatedBy";
        // echo json_encode($this->field);
        // exit();
    }

    private function setConfig($config = null)
    {
        $this->config->hideParentSelf = false;
        if (is_object($config)) {
            if (isset($config->unique) && is_array($config->unique)) {
                $this->config->unique = $config->unique;
            }
            if (isset($config->selfKey) && is_string($config->selfKey) && !is_null($this->findFieldKeyByFieldName($config->selfKey))) {
                $this->config->selfKey = $config->selfKey;

                if (isset($config->hideParentSelf) && $config->hideParentSelf) {
                    $this->config->hideParentSelf = true;
                }
                if (isset($config->selfFields) && is_array($config->selfFields)) {
                    $this->config->selfFields = $config->selfFields;
                }

            }
            if (isset($config->hiddenItemInfo) && is_bool($config->hiddenItemInfo)) {
                $this->hiddenItemInfo = $config->hiddenItemInfo;
            }
        }
    }

    public function setHiddenItemInfo($hidden)
    {
        if (isset($hidden) && is_bool($hidden)) {
            $this->hiddenItemInfo = $hidden;
        }
    }

    public function findFieldKeyByFieldName($fieldName)
    {
        $fieldKey = null;
        if (isset($fieldName) && is_string($fieldName)) {
            foreach ($this->field as $key => $f) {
                if (isset($f->name) && $f->name === $fieldName) {
                    $fieldKey = $key;
                    break;
                }
            }
        }
        return $fieldKey;
    }

    public function findFieldByFieldName($fieldName)
    {
        $fieldKey = $this->findFieldKeyByFieldName($fieldName);
        if (!is_null($fieldKey) && isset($this->field->$fieldKey)) {
            return $this->field->$fieldKey;
        }
        return null;
    }

    /**
     *  Kiểm tra dữ liệu người dùng truyền lên
     *  Hợp lệ = false
     *  Không hợp lệ = true
     */
    private function checkInvalidData($type, $data)
    {
        if (is_null($data)) {
            return false;
        }

        if (isset($this->config->selfKey)) {

        }
        switch ($type) {
            case "boolean":
                return !is_bool($data) && $data !== "true" && $data !== "false";
            case "int":
                return !is_numeric($data);
            case "date":
                return !is_null($data) && is_null($this->format->DateReConvert($data)) ? true : false;
            case "date-time":
                return !is_null($data) && is_null($this->format->DateTimeReConvert($data)) ? true : false;
            case "object":
                return is_null($data) || !isset($data->id) || is_numeric($data->id) ? false : true;
            default:
                return false;
        }
    }

    private function isFieldUnique($key, $data)
    {
        if (isset($this->field->$key)) {
            $value = $this->convertData($key, $data->data);
            $sqlCount = "SELECT count(id) FROM `$this->table_name` WHERE deleted = 0 AND " . (is_null($value) ? "`$key` is null" : "`$key` = '$value' ") . " " . (is_null($data->id) ? "" : " AND `id` <> " . $data->id);

            $stmtCount = $this->conn->query($sqlCount);
            $row = $stmtCount->fetch_row();

            return (int) $row[0] === 0;
        }
        return true;
    }

    private function isItemUnique($data)
    {
        if (isset($this->config->unique) && is_array($this->config->unique)) {
            $sqlDataArr = array();
            foreach ($this->config->unique as $value) {
                $fieldKey = $this->findFieldKeyByFieldName($value);
                if (!is_null($fieldKey)) {
                    $value = $this->convertData($fieldKey, isset($data->$value) ? $data->$value : null);
                    array_push($sqlDataArr, (!is_null($value) ? "`$fieldKey` = '" . $value . "' " : "`$fieldKey` is null"));
                }
            }
            if (count($sqlDataArr) > 0) {
                $sqlCount = "SELECT count(id) FROM `$this->table_name` WHERE deleted = 0 AND " . (implode(" AND ", $sqlDataArr)) . " " . (isset($data->id) ? " AND `id` <> " . $data->id : "");
                $stmtCount = $this->conn->query($sqlCount);
                $row = $stmtCount->fetch_row();
                return (int) $row[0] === 0;
            }
        }
        return true;
    }

    # Convert từ data truyền lên -> lưu vào db
    public function convertData($fieldKey, $data)
    {
        $field = $this->field->$fieldKey;
        $fieldName = isset($field->name) ? $field->name : null;
        $type = isset($field->type) ? $field->type : "string";
        if (is_null($data)) {
            return $data;
        }

        if (is_string($fieldName) && isset($this->config->selfKey) && $this->config->selfKey === $fieldName) {
            if (is_object($data) && isset($data->id) && is_integer($data->id)) {
                return (int) $data->id;
            } else {
                return null;
            }
        } else {
            switch ($type) {
                case "json":
                    return json_encode($data, JSON_UNESCAPED_UNICODE);
                case "boolean":
                    return $data == "true" ? 1 : 0;
                case "int":
                    return is_numeric($data) ? (int) $data : null;
                case "date":
                    return $this->format->DateReConvert($data);
                case "date-time":
                    return $this->format->DateTimeReConvert($data);
                case "object":

                    // if (!isset($this->cacheData->$fieldKey)) {
                    //     $this->cacheData->$fieldKey = (object) [];
                    // }
                    // if (!isset($this->cacheData->$fieldKey->$dataId)) {
                    //     $res = $stdClass->$method($_data, $_data, $_data, false);
                    //     if ($res->error === 0) {
                    //         $this->cacheData->$fieldKey->$dataId = (int) $data->id;
                    //     }
                    // }
                    // return $this->cacheData->$fieldKey->$dataId;

                    $stdClass = $field->object_class;
                    $method = $field->method;
                    $_data = $field->data;
                    if ($field->multiple) {
                        if (method_exists($stdClass, $method) && is_callable($stdClass::class, $method) && is_array($data)) {
                            $ids = array();
                            foreach ($data as $d) {
                                if (isset($d->id)) {
                                    array_push($ids, $d->id);
                                }
                            }
                            $_data->id = implode(",", $ids);
                            $res = $stdClass->$method($_data, $_data, $_data, false);
                            if ($res->error === 0) {
                                return implode(",", $ids);
                            }
                        }

                    } else {
                        if (method_exists($stdClass, $method) && is_callable($stdClass::class, $method) && isset($data->id)) {
                            $_data->id = $data->id;

                            $res = $stdClass->$method($_data, $_data, $_data, false);
                            if ($res->error === 0) {
                                return (int) $_data->id;
                            }
                        }
                    }
                    return null;
                default:
                    return is_string($data) ? $data : null;
            }
        }
    }

    # Convert từ trong db -> trả về response
    public function reConvertData($fieldKey, $data)
    {
        $field = $this->field->$fieldKey;
        $fieldName = isset($field->name) ? $field->name : null;
        $type = isset($field->type) ? $field->type : "string";
        if (is_null($data)) {
            return $data;
        }
        if (is_string($fieldName) && isset($this->config->selfKey) && $this->config->selfKey === $fieldName) {
            $result = null;
            if (is_numeric($data) && !$this->config->hideParentSelf) {
                $self = new \Controller;
                $selfConfig = $this->initData->config;
                $selfConfig->hideParentSelf = true;
                $selfConfig->hiddenItemInfo = true;
                $self->init($this->initData->table_name, $this->initData->field, $selfConfig);
                $_self = $self->_find((object) ["id" => (int) $data]);
                if ($_self->error === 0) {
                    $result = $_self->data->data;
                    unset($result->$fieldName);
                }
            }
            return $result;
        }
        if (isset($field->replace_value) && is_array($field->replace_value)) {
            foreach ($field->replace_value as $key => $value) {
                if ($data === $key) {
                    return $value;
                }
            }
        }
        switch ($type) {
            case "json":
                return json_decode($data);
            case "boolean":
                return $data == 1 ? true : false;
            case "int":
                return is_numeric($data) ? (int) $data : null;
            case "date":
                return $this->format->DateConvert($data);
            case "date-time":
                return $this->format->DateTimeConvert($data);
            case "object":
                $stdClass = $field->object_class;
                $method = $field->method;
                $_data = $field->data;
                $_data->id = $data;
                $dataId = $_data->id;

                if (method_exists($stdClass, $method) && is_callable($stdClass::class, $method)) {
                    // if (!isset($this->cacheData->$fieldKey)) {
                    //     $this->cacheData->$fieldKey = (object) [];
                    // }
                    // if (!isset($this->cacheData->$fieldKey->$dataId)) {
                    //     $res = $stdClass->$method($_data, $_data, $_data, false);
                    //     if ($res->error === 0) {
                    //         $this->cacheData->$fieldKey->$dataId = $res->data->data;
                    //     }
                    // }
                    // return $this->cacheData->$fieldKey->$dataId;

                    $res = $stdClass->$method($_data, $_data, $_data, false);
                    if ($res->error === 0) {
                        return $res->data->data;
                    }
                }
                return null;
            default:
                return $data;
        }
    }

    public function validate($action, $data)
    {
        $this->error_description = array();
        switch ($action) {
            case "fetch_all":
                $fetchAllValidate = true;

                # Check Invalid data
                if ($this->checkInvalidData("int", isset($data->page) ? $data->page : null)) {
                    $fetchAllValidate = false;
                    array_push($this->error_description, (object) [
                        "field" => "page",
                        "message" => "api_400_invalid_data",
                    ]);
                }
                if ($this->checkInvalidData("int", isset($data->pageSize) ? $data->pageSize : null)) {
                    $fetchAllValidate = false;
                    array_push($this->error_description, (object) [
                        "field" => "pageSize",
                        "message" => "api_400_invalid_data",
                    ]);
                }

                foreach ($this->field as $key => $field) {
                    $_fieldName = isset($field->name) ? $field->name : $key;
                    # Check Invalid data
                    if ($this->checkInvalidData(isset($field->type) ? $field->type : "string", isset($data->$_fieldName) ? $data->$_fieldName : null)) {
                        $fetchAllValidate = false;
                        array_push($this->error_description, (object) [
                            "field" => $_fieldName,
                            "message" => "api_400_invalid_data",
                        ]);
                    }
                }
                return $fetchAllValidate;

            case "find":
                $findValidate = true;
                foreach ($data->fieldNames as $_fieldName) {
                    $field = $this->findFieldByFieldName($_fieldName);
                    if (is_null($field) || !isset($data->data->$_fieldName)) {
                        $findValidate = false;
                    } else if (!is_null($field)) {
                        # Check Invalid data
                        if ($this->checkInvalidData(isset($field->type) ? $field->type : "string", isset($data->data->$_fieldName) ? $data->data->$_fieldName : null)) {
                            $createUpdateValidate = false;
                            array_push($this->error_description, (object) [
                                "field" => $_fieldName,
                                "message" => "api_400_invalid_data",
                            ]);
                        }
                    }
                }
                return $findValidate;
            case "create":
            case "update":
                $createUpdateValidate = true;
                if (!$this->isItemUnique($data)) {
                    $createUpdateValidate = false;
                    array_push($this->error_description, (object) [
                        "fields" => $this->config->unique,
                        "message" => "api_400_field_unique",
                    ]);
                    return $createUpdateValidate;
                }
                if ($action === "update" && (!isset($data->id) || !is_int($data->id))) {
                    $createUpdateValidate = false;
                    array_push($this->error_description, (object) [
                        "field" => "id",
                        "message" => "api_400_required",
                    ]);
                    return $createUpdateValidate;
                }
                foreach ($this->field as $key => $field) {
                    $_fieldName = isset($field->name) ? $field->name : $key;
                    # check required
                    if ((isset($field->required) && $field->required && (!isset($data->$_fieldName) || empty($data->$_fieldName)))) {
                        $createUpdateValidate = false;
                        array_push($this->error_description, (object) [
                            "field" => $_fieldName,
                            "message" => "api_400_required",
                        ]);
                    }
                    # Check Invalid data
                    else if ($this->checkInvalidData(isset($field->type) ? $field->type : "string", isset($data->$_fieldName) ? $data->$_fieldName : null)) {
                        $createUpdateValidate = false;
                        array_push($this->error_description, (object) [
                            "field" => $_fieldName,
                            "message" => "api_400_invalid_data",
                        ]);

                    } else if (isset($this->config->selfKey) && $this->config->selfKey === $_fieldName && isset($data->$_fieldName)) {
                        if (!isset($data->$_fieldName->id) || !is_integer($data->$_fieldName->id)) {
                            $createUpdateValidate = false;
                            array_push($this->error_description, (object) [
                                "field" => $_fieldName,
                                "message" => "api_400_invalid_data",
                            ]);
                        } else {
                            if ($action === "update" && (int) $data->id === (int) $data->$_fieldName->id) {
                                $createUpdateValidate = false;
                                array_push($this->error_description, (object) [
                                    "field" => $_fieldName,
                                    "message" => "api_400_invalid_data",
                                ]);
                            } else {
                                $ctlExist = new ControllerExist;
                                $ctlExist->init($this->initData->table_name, $this->initData->field, $this->initData->config);
                                $resExist = $ctlExist->exist((object) ["id" => (int) $data->$_fieldName->id]);
                                if ($resExist->error !== 0) {
                                    $createUpdateValidate = false;
                                    array_push($this->error_description, (object) [
                                        "field" => $_fieldName,
                                        "message" => "api_400_invalid_data",
                                    ]);
                                }
                            }
                        }
                    }
                    # Check Unique data
                    else if (isset($field->unique) && $field->unique && !$this->isFieldUnique($key, (object) [
                        "id" => isset($data->id) && is_int($data->id) ? (int) $data->id : null,
                        "data" => isset($data->$_fieldName) ? $data->$_fieldName : null,
                    ])) {
                        $createUpdateValidate = false;
                        array_push($this->error_description, (object) [
                            "field" => $_fieldName,
                            "message" => "api_400_field_unique",
                        ]);
                    } else if (isset($field->uniqueValue) && $field->uniqueValue === (isset($data->$_fieldName) ? $data->$_fieldName : null) && !$this->isFieldUnique($key, (object) [
                        "id" => isset($data->id) && is_int($data->id) ? (int) $data->id : null,
                        "data" => isset($data->$_fieldName) ? $data->$_fieldName : null,
                    ])) {
                        $createUpdateValidate = false;
                        array_push($this->error_description, (object) [
                            "field" => $_fieldName,
                            "message" => "api_400_field_unique",
                        ]);
                    }
                }
                return $createUpdateValidate;
            case "delete":
                if (isset($data) && isset($data->data) && is_array($data->data)) {
                    return true;
                }
                return false;
            default:
                return false;
        }
    }

    private function by($accountId = null)
    {
        if (!is_null($accountId)) {
            if (isset($this->account->$accountId)) {
                return $this->account->$accountId;
            } else {
                $systemConfig = new \Controller\ControllerConfig ("DEFAULT_AVATAR");

                $sql = "SELECT first_name, last_name, avatar_url from `tbl_accounts` WHERE id = '{$accountId}' LIMIT 0,1";
                $stmt = $this->query($sql);
                if ($stmt->num_rows == 1) {
                    $row = $stmt->fetch_assoc();
                    $avatarUrl = isset($row["avatar_url"]) && $row["avatar_url"] !== "DEFAULT_AVATAR" ? $row["avatar_url"] : (isset($systemConfig->config->DEFAULT_AVATAR) ? $systemConfig->config->DEFAULT_AVATAR : null);
                    $account = (object) [
                        "id" => (int) $accountId,
                        "name" => $row["first_name"] . " " . $row["last_name"],
                        "avatarUrl" => $avatarUrl,
                    ];
                    $this->account->$accountId = $account;
                    return $account;
                }
            }
        }
        return null;
    }

    public function item($data, $showFields = null, $hiddenItemInfo = false)
    {
        $item = (object) [];
        $item->id = $this->reConvertData("id", $data->id);
        foreach ($this->field as $key => $f) {
            if (
                (is_null($showFields) || (is_array($showFields) && isset($f->name) && in_array($f->name, $showFields)))
                && isset($f->name)
                && !$f->system
                && $f->public
            ) {
                $fieldName = $f->name;
                $item->$fieldName = $this->reConvertData($key, isset($data->$key) ? $data->$key : null);
            }
        }

        /**
         *  System field
         */

        if (!$this->hiddenItemInfo && !$hiddenItemInfo) {
            $item->createdAt = $this->format->DateTimeConvert($data->created_at);
            $item->updatedAt = $this->format->DateTimeConvert($data->updated_at);
            $item->createdBy = $this->by($data->created_by);
            $item->updatedBy = $this->by($data->updated_by);
        }
        /**
         *  End system field
         */
        return (object) $item;
    }

    private function convertSort($sort)
    {
        if (is_string($sort)) {
            $res = array();
            $sortArray = explode(",", $sort);
            foreach ($sortArray as $key) {
                $_sort = explode(" ", trim($key));
                if (isset($_sort[0]) && isset($_sort[1])) {
                    $fieldName = $_sort[0];
                    $order = $_sort[1];
                    $fieldKey = $this->findFieldKeyByFieldName($fieldName);
                    if (!is_null($fieldKey)) {
                        array_push($res, " `{$fieldKey}` {$order}");
                    }
                }
            }
            return count($res) > 0 ? implode(",", $res) : null;
        }
        return null;
    }
    public function where($data)
    {
        $where = " deleted = 0 ";
        if (isset($data->q)) {
            $qWhere = array();
            foreach ($this->field as $key => $value) {
                array_push($qWhere, " (`$key` is not null AND $key like '%$data->q%') ");
            }
            if (count($qWhere) > 0) {
                $where .= " AND (" . implode(" OR ", $qWhere) . ") ";
            }
        } else {
            foreach ($this->field as $key => $_field) {
                if (isset($_field->name)) {
                    $_fieldName = $_field->name;
                    $_fieldNameLike = $_field->name . "_like";
                    $_fieldNameLikes = $_field->name . "_likes";
                    $_fieldNameIn = $_field->name . "_in";
                    if (isset($data->$_fieldName)) {
                        $value = $this->convertData($key, $data->$_fieldName);
                        $where .= " AND `$key` = '" . $value . "'";
                    }

                    if (isset($data->$_fieldNameLike)) {
                        $where .= " AND `$key` like '%" . $data->$_fieldNameLike . "%'";
                    }
                    if (isset($data->$_fieldNameIn) && is_string($data->$_fieldNameIn) && strlen(trim($data->$_fieldNameIn)) > 0 && count(explode(",", $data->$_fieldNameIn)) > 0) {
                        $where .= " AND `$key` in ('" . join("', '", explode(",", $data->$_fieldNameIn)) . "')";
                    }

                    if (isset($data->$_fieldNameLikes)) {
                        if (count(explode(",", $data->$_fieldNameLikes)) > 0) {
                            $where .= "AND (`$key` like '%" . join("%' OR $key like '%", explode(",", $data->$_fieldNameLikes)) . "%')";
                        }
                    }
                }
            }
        }

        if (isset($data->_sort) && !is_null($this->convertSort($data->_sort))) {
            $where .= " ORDER BY " . urldecode($this->convertSort($data->_sort));
        }
        return $where;
    }

    public function count($data)
    {
        $sqlCount = "SELECT count(id) FROM `$this->table_name` WHERE " . $this->where($data);
        $stmtCount = $this->query($sqlCount);
        $row = $stmtCount->fetch_row();
        return (int) $row[0];
    }
}
