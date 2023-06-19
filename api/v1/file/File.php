<?php
namespace V1;

class File extends \Controller

{
    // Connection

    private $maxSize = 80000000;
    private $allowtypes = array('jpg', 'png', 'jpeg', 'gif', 'rar', 'zip');

    public function __construct()
    {
        parent::__construct();
        $this->init(
            "tbl_files",
            (object) [
                "code" => (object) [],
                "name" => (object) [],
                "type" => (object) [],
                "size" => (object) [
                    "type" => "int",
                ],
                "public" => (object) [
                    "type" => "boolean",
                ],
                "path" => (object) [],
                "version" => (object) [
                    "type" => "int",
                ],
                "description" => (object) [],
            ]
        );
    }

    private function getMaxVersion($code)
    {
        $res = $this->_fetch((object) ["code" => $code]);
        if ($res->error === 0 && count($res->data->data) > 0) {
            $versions = array();
            foreach ($res->data->data as $value) {
                array_push($versions, $value->version);
            }
            return max($versions) + 1;
        }
        return 1;
    }

    private function checkCodeExist($code)
    {
        $res = $this->_find((object) ["code" => $code], ["code"]);
        return $res->error === 0;
    }

    public function apiUpload($pathData, $getData, $bodyData)
    {
        $data = (object) $_REQUEST;
        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
            if (isset($data->code)) {
                $codeExist = $this->checkCodeExist($data->code);
                if ($codeExist) {
                    $currentFolder = $data->code;
                    $version = $this->getMaxVersion($currentFolder);
                } else {
                    return (object) [
                        "error" => 400,
                    ];
                }
            } else {
                $currentFolder = time();
                $version = 1;
            }
            $data->name = $_FILES["file"]["name"];
            $data->type = $_FILES["file"]["type"];
            $data->size = $_FILES["file"]["size"];
            $target_dir = __DIR__ . "./../../../files/" . $currentFolder . "/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir);
            }
            $target_file = $target_dir . "ver-$version";
            $data->path = "/document/v1/" . $data->name . "?code=" . $currentFolder . "&version=$version";
            $data->version = $version;
            $data->code = (string) $currentFolder;
            if ($data->size > $this->maxSize) {
                return (object) [
                    "error" => 11,
                    "description" => "Max file size " . $this->maxSize,
                ];
            }
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $res = $this->_create($data);
                return $res;
            } else {
                return (object) [
                    "error" => 10,
                ];
            }
        } else {
            return (object) [
                "error" => 400,
            ];
        }
    }

    public function apiDownload($data, $getData, $postData, $is_preview = false)
    {
        $data->version = isset($getData->version) ? $getData->version : 1;
        $res = $this->_find($data, ["code", "version"]);
        if ($res->error === 0) {
            $f = $res->data->data;
            $file = __DIR__ . "./../../../files/" . $f->code . "/ver-" . $f->version;
            header('Content-Type:' . $f->type);
            header('Content-Length: ' . $f->size);
            if ($is_preview) {
                header("Content-Disposition: ; filename=" . $f->name);
            } else {
                header("Content-Disposition: attachment; filename=" . $f->name);
            }
            readfile($file);
            exit;
        } else {
            return (object) [
                "error" => 1,
            ];
        }
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
