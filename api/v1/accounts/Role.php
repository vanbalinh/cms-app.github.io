<?php
namespace V1;

class Role extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_role",
            (object) [
                "code" => (object) [
                    "required" => true,
                    "unique" => true,
                ],
                "name" => (object) [
                    "required" => true,
                ],
                "description" => (object) [],
            ]
        );
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData, $showItemInfo = true)
    {
        echo $showItemInfo;
        return $this->_find($pathData, null, $showItemInfo);
    }

    public function apiFindByCode($pathData, $getData, $bodyData, $showItemInfo = true)
    {
        return $this->_find($pathData, ["code"], $showItemInfo);
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
