<?php
namespace V1;

class Test extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_routers",
            (object) [
                "path" => (object) [],
                "auth" => (object) [
                    "type" => "boolean",
                ],
                "method" => (object) [],
                "namespace" => (object) [],
                "function_name" => (object) [
                    "name" => "functionName",
                ],
                "msg_success" => (object) [
                    "name" => "messageSuccess",
                ],
                "msg_error" => (object) [
                    "name" => "messageError",
                ],
            ]
        );
    }

    public function apiFetchAll($pathData, $getData, $postData)
    {
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $postData)
    {
        return $this->_find($pathData);
    }

    public function apiCreate($pathData, $getData, $postData)
    {
        return $this->_create($postData);
    }

    public function apiUpdate($pathData, $getData, $postData)
    {
        return $this->_update($postData);
    }

    public function apiDelete($pathData, $getData, $postData)
    {
        return $this->_delete($postData);
    }

    public function postTest($data, $getData, $bodyData)
    {
        return $this->apiCreate($data, $getData, $bodyData);
    }
}
