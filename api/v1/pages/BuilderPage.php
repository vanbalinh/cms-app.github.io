<?php
namespace V1\Builder;

class Page extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_page",
            (object) [
                "name" => (object) [
                    "required" => true,
                    "name" => "title",
                ],
                "url" => (object) [
                    "required" => true,
                    "unique" => true,
                ],
                "is_home" => (object) [
                    "name" => "isHome",
                    "type" => "boolean",
                    "uniqueValue" => true,
                ],
                "modules" => (object) [
                    "type" => "json",
                ],
                "properties" => (object) [
                    "name" => "propertiesModules",
                    "type" => "json",
                ],
                "publish" => (object) [
                    "type" => "boolean",
                ],
                "config" => (object) [
                    "type" => "json",
                ],
                "description" => (object) [],
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

    public function apiUpdateHomePage($pathData, $getData, $bodyData)
    {
        if (!isset($bodyData->id)) {
            return (object) [
                "error" => 400,
                "error_description" => [
                    (object) [
                        "field" => "id",
                        "message" => "api_400_required",
                    ],
                ],
            ];
        } else if ($this->_exist($bodyData, ["id"])->error !== 0) {
            return (object) [
                "error" => 400,
                "error_description" => [
                    (object) [
                        "field" => "id",
                        "message" => "api_msg_notfound",
                    ],
                ],
            ];
        }
        $stmt = $this->_run("UPDATE tbl_page SET is_home = 0 WHERE is_home = 1");
        if ($stmt) {
            $stmt = $this->_run("UPDATE tbl_page SET is_home = 1 WHERE id = '{$bodyData->id}'");
            if ($stmt) {
                return (object) [
                    "error" => 0,
                ];
            }
        }
        return (object) [
            "error" => -1,
        ];

    }
}
