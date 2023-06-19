<?php
namespace V1;

use \Config\Connect;
use \V1\File;

class Common extends \Controller

{
    // Connection
    private $conn;
    private $connect;
    private $code;

    public function __construct()
    {
        $this->connect = new Connect;
        $this->conn = $this->connect->conn;
    }

    public function apiGetAllControllerMethodName($data, $getData)
    {
        $file = fopen(__DIR__ . "./../../../router/route.php", "r");
        $arr = array();
        while (!feof($file)) {
            $content = fgets($file);
            if (substr($content, 0, 8) === '$route->') {
                $a = explode(",", $content);
                if (count($a) >= 2) {
                    $b = $a[1];
                    $c = strpos($b, '")');
                    $d = substr($b, 0, $c);
                    $e = strpos($d, '"');
                    $f = substr($d, $e + 1);
                    $g = str_replace('\\', '_', $f);

                    $h = explode("@", (string) $g);

                    $namespace = $h[0];
                    $function_name = $h[1];

                    $n = explode('("', $content);
                    $m = strpos($n[1], '",');
                    $path = substr($n[1], 0, $m);

                    $method = strpos($content, "->get(") ? "get" :
                    (strpos($content, "->post(") ? "post" :
                        (strpos($content, "->put(") ? "put" :
                            (strpos($content, "->del(") ? "del" : null)));
                    $auth = strpos($content, "->auth(") !== false;
                    array_push($arr, (object) [
                        "auth" => $auth ? 1 : 0,
                        "namespace" => $namespace,
                        "function_name" => $function_name,
                        "method" => $method,
                        "path" => $path,
                    ]);

                    echo "INSERT INTO tbl_routers(auth, namespace, function_name, method, path) VALUES ( " . ($auth ? 1 : 0) . ", '" . $namespace . "', '" . $function_name . "', '" . ($method === "del" ? "DELETE" : strtoupper($method)) . "', '" . $path . "'); \n";
                }
            }
        }
        fclose($file);
        exit;
        // $this->connect->sendResponse(200, (object) ["data" => $arr]);

    }

    public function apiGetImageLoading()
    {
        $systemConfig = $this->getSystemConfig("IMAGE_LOADING");
        $code = isset($systemConfig->IMAGE_LOADING) ? $systemConfig->IMAGE_LOADING : null;
        $file = new File();
        return $file->apiDownload((object) ["code" => $code], (object) [], (object) []);
    }

    public function apiGetImageLogoHeader()
    {
        $systemConfig = $this->getSystemConfig("IMAGE_LOGO_HEADER");
        $code = isset($systemConfig->IMAGE_LOGO_HEADER) ? $systemConfig->IMAGE_LOGO_HEADER : null;
        $file = new File();
        return $file->apiDownload((object) ["code" => $code], (object) [], (object) []);
    }

    public function apiGetImageShortcutIcon()
    {
        $systemConfig = $this->getSystemConfig("IMAGE_SHORTCUT_ICON");
        $code = isset($systemConfig->IMAGE_SHORTCUT_ICON) ? $systemConfig->IMAGE_SHORTCUT_ICON : null;
        $file = new File();
        return $file->apiDownload((object) ["code" => $code], (object) [], (object) []);
    }

    public function apiGetAdministrativeUnits()
    {
        $systemConfig = $this->getSystemConfig("ADMINISTRATIVE_UNITS");
        $code = isset($systemConfig->ADMINISTRATIVE_UNITS) ? $systemConfig->ADMINISTRATIVE_UNITS : null;
        $file = new File();
        return $file->apiDownload((object) ["code" => $code], (object) [], (object) []);
    }

    public function postTest($data, $getData, $bodyData)
    {
        return (object) [
            "error" => 0,
            "data" => (object) [
                "data" => $bodyData,
            ],
        ];
    }
}
