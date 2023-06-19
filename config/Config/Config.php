<?php
namespace Config;

class SystemConfig extends \Controller

{

    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_system_config",
            (object) [
                "name" => (object) [
                    "required" => true,
                    "unique" => true,
                ],
                "value" => (object) [
                    "required" => true,
                ],
                "description" => (object) [],
            ]
        );
    }

    public function find($name = null)
    {
        if (is_string($name)) {
            $res = $this->_find((object) ["name" => $name], ["name"]);
            if ($res->error === 0) {
                return $res->data->data;
            }
        }
        return null;
    }

    public function findValue($name)
    {
        $systemConfig = $this->getSystemConfig();
        return isset($systemConfig->$name) ? $systemConfig->$name : null;
    }

    public function findSSOConfig($type)
    {
        $systemConfig = $this->getSystemConfig();
        if (isset($type)) {
            switch ($type) {
                case "FACEBOOK":
                    $FACEBOOK_APP_ID = isset($systemConfig->FACEBOOK_APP_ID) ? $systemConfig->FACEBOOK_APP_ID : null;
                    $FACEBOOK_APP_SECRET = isset($systemConfig->FACEBOOK_APP_SECRET) ? $systemConfig->FACEBOOK_APP_SECRET : null;
                    $FACEBOOK_APP_CALLBACK = isset($systemConfig->FACEBOOK_APP_CALLBACK) ? $systemConfig->FACEBOOK_APP_CALLBACK : null;
                    $FACEBOOK_APP_DEFAULT_GRAPH_VERSION = isset($systemConfig->FACEBOOK_APP_DEFAULT_GRAPH_VERSION) ? $systemConfig->FACEBOOK_APP_DEFAULT_GRAPH_VERSION : null;

                    return (object) [
                        "FACEBOOK_APP_ID" => $FACEBOOK_APP_ID,
                        "FACEBOOK_APP_SECRET" => $FACEBOOK_APP_SECRET,
                        "FACEBOOK_APP_CALLBACK" => $FACEBOOK_APP_CALLBACK,
                        "FACEBOOK_APP_DEFAULT_GRAPH_VERSION" => $FACEBOOK_APP_DEFAULT_GRAPH_VERSION,
                    ];
                case "GOOGLE":
                    $GOOGLE_APP_ID = isset($systemConfig->GOOGLE_APP_ID) ? $systemConfig->GOOGLE_APP_ID : null;
                    $GOOGLE_APP_SECRET = isset($systemConfig->GOOGLE_APP_SECRET) ? $systemConfig->GOOGLE_APP_SECRET : null;
                    $GOOGLE_APP_CALLBACK = isset($systemConfig->GOOGLE_APP_CALLBACK) ? $systemConfig->GOOGLE_APP_CALLBACK : null;
                    return (object) [
                        "GOOGLE_APP_ID" => $GOOGLE_APP_ID,
                        "GOOGLE_APP_SECRET" => $GOOGLE_APP_SECRET,
                        "GOOGLE_APP_CALLBACK" => $GOOGLE_APP_CALLBACK,
                    ];
                case "ZALO":
                    $ZALO_APP_ID = isset($systemConfig->ZALO_APP_ID) ? $systemConfig->ZALO_APP_ID : null;
                    $ZALO_APP_SECRET = isset($systemConfig->ZALO_APP_SECRET) ? $systemConfig->ZALO_APP_SECRET : null;
                    $ZALO_APP_CALLBACK = isset($systemConfig->ZALO_APP_CALLBACK) ? $systemConfig->ZALO_APP_CALLBACK : null;
                    return (object) [
                        "ZALO_APP_ID" => $ZALO_APP_ID,
                        "ZALO_APP_SECRET" => $ZALO_APP_SECRET,
                        "ZALO_APP_CALLBACK" => $ZALO_APP_CALLBACK,
                    ];
                default:
                    return null;
            }
        }
        return null;
    }

    public function apiFetchAll($pathData, $getData, $bodyData)
    {
        return $this->_fetch($getData);
    }

    public function apiFindById($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData);
    }

    public function apiFindByName($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData, ["name"]);
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
