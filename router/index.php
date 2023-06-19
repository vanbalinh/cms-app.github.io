<?php
include_once __DIR__ . './../vendor/autoload.php';

function includeDir($paths)
{
    foreach ($paths as $path) {
        if (is_file(__DIR__ . $path)) {
            include_once __DIR__ . $path;
        } else {
            $dir = new RecursiveDirectoryIterator($path);
            $iterator = new RecursiveIteratorIterator($dir);
            foreach ($iterator as $file) {
                $fname = $file->getFilename();
                if (preg_match('%\.php$%', $fname)) {
                    $fpath = $file->getPathname();
                    if (is_file(__DIR__ . $file->getPathname())) {
                        include_once __DIR__ . $file->getPathname();
                    }
                }
            }
        }
    }
}
includeDir([
    "./../config/Connect",
    "./../config/Controller",
    "./../config",
    "./../common",
    "./../api",
    "./Router.php",
]);

if (isset($_GET["_____action"]) && $_GET["_____action"] === "file") {
    $error = null;
    if (isset($_GET["ver"]) && isset($_GET["code"]) && isset($_GET["name"])) {
        define("API_VERSION", $_GET["ver"]);
        $class = "\\V" . $_GET["ver"] . "\\File";
        if (class_exists($class)) {
            $stdClass = new $class();
            if (is_callable($stdClass::class, "apiDownload")) {
                $version = isset($_GET["version"]) && is_numeric($_GET["version"]) ? (int) $_GET["version"] : 1;
                $res = $stdClass->apiDownload((object) ["code" => $_GET["code"]], (object) ["version" => $version], null, true);
                if (isset($res->error) && $res->error === 1) {
                    $error = "Không tìm thấy tài liệu có mã = '" . $_GET["code"] . "' và version = '" . $version . "'";
                }
            } else {
                $error = "Lỗi1";
            }
        } else {
            $error = "Lỗi2";
        }
    } else {
        $error = "Đường dẫn không hợp lệ";
    }
    echo $error;
} else {
    new AppRouter();
}
