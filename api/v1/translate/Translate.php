<?php
namespace V1;

class Translate extends \Controller

{
    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_translate",
            (object) [
                "code" => (object) [
                    "required" => true,
                ],
                "locale" => (object) [
                    "name" => "locale",
                ],
                "translate" => (object) [],
                "description" => (object) [],
            ],
            (object) [
                "unique" => ["code", "locale"],
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

    public function apiFindByCode($pathData, $getData, $bodyData)
    {
        return $this->_find($pathData, ["code"]);
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

    public function translate($code, $locale)
    {
        $language = new \V1\Language;
        $_locale = null;

        if (is_null($locale) || !$language->checkLanguageCode($locale)) {

            $localeDefault = $language->findLanguageDefault();
            if (!is_null($localeDefault)) {
                $_locale = $localeDefault->locale;
            }
        } else {
            $_locale = $locale;
        }
        if (!is_null($_locale)) {
            $res = $this->_find((object) ["code" => $code, "locale" => $_locale], ["code", "locale"]);
            if ($res->error === 0) {
                return $res->data->data->translate;
            }
        }
        return $code;
    }
}
