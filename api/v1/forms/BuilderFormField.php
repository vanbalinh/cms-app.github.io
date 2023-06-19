<?php
namespace V1\Builder\Form;

class Field extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init_table = "tbl_forms_fields";
        $this->init_fields = (object) [
            "form_id" => (object) [
                "required" => true,
                "name" => "form",
                "type" => "object",
                "object_name" => "V1\\Builder\\Form",
                "object_fields" => ["name"],
            ],
            "name" => (object) [
                "required" => true,
            ],
            "api_key" => (object) [
                "required" => true,
                "name" => "apiKey",
            ],
            "type" => (object) [
                "required" => true,
            ],
            "sort" => (object) [
                "type" => "int",
            ],
            "description" => (object) [],
            "is_required" => (object) [
                "name" => "isRequired",
                "type" => "boolean",
            ],
            "is_unique" => (object) [
                "name" => "isUnique",
                "type" => "boolean",
            ],
            "is_parent" => (object) [
                "name" => "isParent",
                "type" => "boolean",
                "uniqueValue" => true,
            ],
            "is_multiple" => (object) [
                "name" => "isMultiple",
                "type" => "boolean",
            ],
            "min" => (object) [
            ],
            "max" => (object) [
            ],
            "display_on_list" => (object) [
                "name" => "displayOnList",
                "type" => "boolean",
            ],
            "display_on_list_default" => (object) [
                "name" => "displayOnListDefault",
                "type" => "boolean",
            ],
            "form_col" => (object) [
                "name" => "formCol",
                "type" => "int",
            ],
            "form_hidden" => (object) [
                "name" => "formHidden",
                "type" => "boolean",
            ],
            "default_value" => (object) [
                "name" => "defaultValue",
            ],
            "reference_id" => (object) [
                "name" => "referenceId",
                "type" => "int",
            ],
            "label_key" => (object) [
                "name" => "labelKey",
            ],
            "relationship" => (object) [
            ],
            "sql_where" => (object) [
                "name" => "sqlWhere",
            ],
        ];
        $this->init_config = (object) [
            "unique" => ["form", "apiKey"],
        ];
        $this->init(
            $this->init_table,
            $this->init_fields,
            $this->init_config
        );
    }

    private function addColumn($form_id, $field_id)
    {
        $tbl_name = "tbl_data_form_" . $form_id;
        return $this->_run("ALTER TABLE `$tbl_name` ADD `$field_id` TEXT COLLATE utf8_vietnamese_ci NULL AFTER `id`");
    }

    public function apiFetchAll($pathData, $getData, $bodyData, $showItemInfo = true)
    {
        if ($showItemInfo === false) {
            $this->init_config->hiddenItemInfo = true;
            $this->init(
                $this->init_table,
                $this->init_fields,
                $this->init_config
            );

        }
        $getData->form = (object) ["id" => $pathData->form_id];
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData);
    }

    public function apiCreate($pathData, $getData, $bodyData)
    {
        $bodyData->form = (object) ["id" => $pathData->form_id];
        $res = $this->_create($bodyData);
        if ($res->error === 0) {
            $this->addColumn($pathData->form_id, $res->data->data->id);
        }
        return $res;
    }

    public function apiUpdate($pathData, $getData, $bodyData)
    {
        $bodyData->form = (object) ["id" => $pathData->form_id];
        $res = $this->_update($bodyData);
        return $res;
    }

    public function apiDelete($pathData, $getData, $bodyData)
    {
        return $this->_delete($bodyData);
    }
}
