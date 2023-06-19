<?php
namespace V1\Builder\Form;

class Data extends \Controller

{
    private $field_fields;

    public function __construct()
    {
        parent::__construct();
        $field_fields = "name,apiKey,type,referenceId,labelKey";

    }

    private function field($field)
    {
        $res = (object) [
            "name" => $field->apiKey,
            "required" => $field->isRequired,
            "unique" => $field->isUnique,
        ];
        switch ($field->type) {
            case "boolean":
                $res->type = "boolean";
                break;
            case "int":
                $res->type = "int";
                break;
            case "float":
                $res->type = "float";
                break;
            case "date":
                $res->type = "date";
                break;
            case "date-time":
                $res->type = "date-time";
                break;
            case "select":
            case "reference":
                $data = isset($field->referenceId) && is_int($field->referenceId) ? (object) ["form_id" => $field->referenceId] : (object) [];
                if (is_string($field->labelKey)) {
                    $data->fields = $field->labelKey;
                }

                $res->type = "object";
                $res->object_name = "V1\\Builder\\Form\\Data";
                $res->method = "apiFindById";
                $res->data = $data;
                if (isset($field->isMultiple) && $field->isMultiple) {
                    $res->method = "apiFindByIds";
                    $res->multiple = true;
                }
                break;
            default:
                $res->type = "text";
                break;
        }
        return $res;
    }
    private function initialization($formParam, $fieldSelects = null, $showItemInfo = true)
    {
        $_form = new \V1\Builder\Form;
        $form = (object) ["error" => -1];
        if (isset($formParam->form_id)) {
            $form = $_form->apiFindById((object) ["id" => $formParam->form_id], (object) [], (object) []);
        }
        if (isset($formParam->code)) {
            $form = $_form->apiFindByCode((object) ["code" => $formParam->code], (object) [], (object) []);
        }

        if ($form->error === 0) {
            $config = (object) [];

            $data = (object) ["form_id" => $form->data->data->id];
            if ($form->data->data->checkAccess && is_null($this->getJWT())) {
                return (object) ["error" => 401];
            }
            $_field = new \V1\Builder\Form\Field;
            // $data->fields = $this->field_fields;
            $fields = $_field->apiFetchAll($data, $data, (object) [], false);
            if ($fields->error === 0) {
                $fields = $fields->data->data;
            }
            $_fields = (object) [];
            foreach ($fields as $f) {
                # code...
                // if (is_null($fieldSelects) || (is_array($fieldSelects) && in_array($f->apiKey, $fieldSelects))) {
                $fieldKey = (string) $f->id;
                $_fields->$fieldKey = $this->field($f);

                if ($f->isParent) {
                    $config->selfKey = $f->apiKey;
                }
                // }
            }
            if (!$showItemInfo) {
                $config->hideParentSelf = true;
                $config->hiddenItemInfo = true;
            }
            $this->init(
                "tbl_data_form_" . $data->form_id,
                (object) $_fields,
                $config
            );
            return true;
        } else {
            return (object) [
                "error" => 1,
                "error_description" => [
                    (object) [
                        "field" => "form",
                        "message" => "api_msg_notfound",
                    ],
                ],
            ];
        }

    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        $fieldSelects = isset($getData->fields) ? explode(",", $getData->fields) : null;
        $res = $this->initialization($pathData, $fieldSelects);
        if ($res === true) {
            return $this->_fetch($getData);
        } else {
            return $res;
        }
    }

    public function apiTree($pathData, $getData, $bodyData)
    {
        $fieldSelects = isset($getData->fields) ? explode(",", $getData->fields) : null;
        $res = $this->initialization($pathData, $fieldSelects);
        if ($res === true) {
            return $this->_tree($getData);
        } else {
            return $res;
        }
    }

    public function apiFindById($pathData, $getData, $bodyData, $showItemInfo = true)
    {
        $fieldSelects = isset($getData->fields) ? explode(",", $getData->fields) : null;
        $res = $this->initialization($pathData, $fieldSelects, $showItemInfo);
        if ($res === true) {
            return $this->_find($pathData);
        } else {
            return $res;
        }
    }

    public function apiFindByIds($pathData, $getData, $bodyData, $showItemInfo = true)
    {
        $fieldSelects = isset($getData->fields) ? explode(",", $getData->fields) : null;
        $res = $this->initialization($pathData, $fieldSelects, $showItemInfo);
        if (isset($pathData->id) && is_string($pathData->id)) {
            $pathData->id_in = $pathData->id;
            unset($pathData->id);
        }
        if ($res === true) {
            return $this->_fetch($pathData);
        } else {
            return $res;
        }
    }

    public function apiCreate($pathData, $getData, $bodyData)
    {
        $res = $this->initialization($pathData);
        if ($res === true) {
            return $this->_create($bodyData);
        } else {
            return $res;
        }
    }

    public function apiUpdate($pathData, $getData, $bodyData)
    {
        $fieldSelects = isset($getData->fields) ? explode(",", $getData->fields) : null;
        $res = $this->initialization($pathData, $fieldSelects, true);
        if ($res === true) {
            return $this->_update($bodyData);
        } else {
            return $res;
        }
    }

    public function apiDelete($pathData, $getData, $bodyData)
    {
        $res = $this->initialization($pathData);
        if ($res === true) {
            return $this->_delete($bodyData);
        } else {
            return $res;
        }
    }
}
