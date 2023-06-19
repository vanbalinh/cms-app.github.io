<?php
namespace V1;

class Language extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_language",
            (object) [
                "locale" => (object) [
                    "required" => true,
                    "unique" => true,
                ],
                "name" => (object) [],
                "description" => (object) [],
                "sort" => (object) [
                    "type" => "int",
                ],
                "is_default" => (object) [
                    "name" => "isDefault",
                    "type" => "boolean",
                    "uniqueValue" => true,
                ],
            ]
        );
    }

    public function findLanguageDefault()
    {
        $res = $this->_find((object) ["isDefault" => true], ["isDefault"]);
        if ($res->error === 0) {
            return $res->data->data;
        }
        return null;
    }

    public function checkLanguageCode($locale)
    {
        $res = $this->_exist((object) ["isDefault" => true], ["isDefault"]);
        if ($res->error === 0) {
            return true;
        }
        return false;
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        $this->run("");
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData);
    }

    public function apiFindByLocale($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData, ["locale"]);
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

    public function apiUpdateDefault($pathData, $getData, $bodyData)
    {
        if (!isset($bodyData->locale)) {
            return (object) [
                "error" => 400,
                "error_description" => [
                    (object) [
                        "field" => "locale",
                        "message" => "api_400_required",
                    ],
                ],
            ];
        } else if ($this->_exist($bodyData, ["locale"])->error !== 0) {
            return (object) [
                "error" => 400,
                "error_description" => [
                    (object) [
                        "field" => "locale",
                        "message" => "api_msg_notfound",
                    ],
                ],
            ];
        }
        $stmt = $this->_run("UPDATE tbl_language SET is_default = 0 WHERE is_default = 1");
        if ($stmt) {
            $stmt = $this->_run("UPDATE tbl_language SET is_default = 1 WHERE locale = '{$bodyData->locale}'");
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
