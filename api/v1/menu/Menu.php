<?php
namespace V1;

class Menu extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_menu",
            (object) [
                "parent_id" => (object) [
                    "name" => "parent",
                ],
                "name" => (object) [],
                "page_title" => (object) [
                    "name" => "pageTitle",
                ],
                "url" => (object) [],
                "hide" => (object) [
                    "type" => "boolean",
                ],
                "is_group" => (object) [
                    "name" => "isGroup",
                    "type" => "boolean",
                ],
                "icon_class" => (object) [
                    "name" => "iconClass",
                ],
                "description" => (object) [],
                "sort" => (object) [
                    "type" => "int",
                ],
                "permission_id" => (object) [
                    "name" => "permission",
                    "type" => "number",
                ],
            ],
            (object) [
                "selfKey" => "parent",
            ]
        );
    }

    public function findMenuTreeByPermissionIds($permissionIds = array())
    {
        $this->initField(
            (object) [
                "permission_id" => (object) [
                    "type" => "number",
                    "public" => false,
                ],
            ]
        );
        return $this->_tree((object) ["permission_id_in" => implode(",", $permissionIds)]);
    }

    /**
     *  FNC API
     */
    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFetchAllTree($pathData, $getData, $bodyData)
    {
        return $this->_tree($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData);
    }

    public function apiCreate($pathData, $getData, $bodyData)
    {
        return $this->_create($bodyData);
    }

    public function apiUpdate($pathData, $getData, $bodyData)
    {
        return $this->_update($bodyData);
    }

    public function apiDelete($pathData, $getData, $bodyData)
    {
        return $this->_delete($bodyData);
    }

}
