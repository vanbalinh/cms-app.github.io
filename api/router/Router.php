<?php
class Router extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_routers",
            (object) [
                "path" => (object) [
                    "required" => true,
                ],
                "auth" => (object) [
                    "type" => "boolean",
                ],
                "method" => (object) [],
                "namespace" => (object) [],
                "function_name" => (object) [
                    "name" => "functionName",
                ],
                "count_call" => (object) [
                    "name" => "countCall",
                    "type" => "int",
                ],
                "msg_success" => (object) [
                    "name" => "messageSuccess",
                ],
                "msg_error" => (object) [
                    "name" => "messageError",
                ],
                "realtime" => (object) [
                    "type" => "boolean",
                ],
                "chanel" => (object) [
                ],
                "event" => (object) [
                ],
            ],
            (object) [
                "unique" => ["path", "method"],
            ]
        );
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
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
