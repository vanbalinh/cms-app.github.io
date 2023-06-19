<?php
namespace V1;

class Folder extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_folders",
            (object) [
                "parent_id" => (object) [
                    "name" => "parent",
                ],
                "name" => (object) [
                    "unique" => true,
                    "required" => true,
                ],
                "description" => (object) [],
                "sort" => (object) [
                    "type" => "int",
                    "required" => true,
                ],
            ],
            (object) [
                "selfKey" => "parent",
            ]
        );
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFetchAllTree($pathData, $getData, $bodyData)
    {
        return $this->_tree();
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
