<?php

// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');

class AppRouter
{
    private $path;
    private $response;
    private $method;
    private $auth;
    private $roles = null;
    private $groups = null;
    private $conn;
    private $translate;
    private $languageCode = "vi";

    private $authId;
    private $authLocale;
    public function __construct()
    {

        $this->response = new \Config\Response;
        $_authClass = "\\V1\\Auth";
        // $this->translate = new \V1\Translate;
        if (isset($_GET["_____version"])) {
            $v = $_GET["_____version"];
            $authClass = "\\V" . $v . "\\Auth";
            $translateClass = "\\V" . $v . "\\Translate";
            define("API_VERSION", $v);

            if (class_exists($authClass)) {
                // $this->auth = new $authClass();
                $_authClass = $authClass;
            }
            if (class_exists($translateClass)) {
                $this->translate = new $translateClass();
            }
            unset($_GET["_____version"]);
        } else {
            define("API_VERSION", 1);
            $this->translate = new \V1\Translate;
        }
        $this->auth = new $_authClass();
        if (isset($_REQUEST["_____version"])) {
            unset($_REQUEST["_____version"]);
        }
        $this->method = $_SERVER['REQUEST_METHOD'];
        if (isset($_GET["_____path"])) {
            $this->path = $_GET["_____path"];
            unset($_GET["_____path"]);
        }
        if (isset($_REQUEST["_____path"])) {
            unset($_REQUEST["_____path"]);
        }

        $connect = new \Config\Connect;
        $this->conn = $connect->conn;
        $route = $this->getRoute();

        $config = new \Config\SystemConfig;
        $showMessage = false;
        $isShowMsg = $config->findValue("SHOW_MESSAGE_WHEN_401");
        if ($isShowMsg == "true") {
            $showMessage = true;
        }

        if ($this->auth->check()) {
            $jwt = $this->auth->jwt;
            $this->authId = $jwt->user->id;
            $this->authLocale = $jwt->user->locale;
        } else {
            if ($route->auth == 1) {
                $this->response->sendResponse(401, array(
                    "status" => false,
                    "message" => $this->translate->translate("api_msg_invalid_token", $this->authLocale),
                    "showMessage" => $showMessage,
                ));
                exit;
            }
        }
        try {

            $res = $this->run($route->path, $route->method, str_replace('_', '\\', $route->namespace), $route->function_name);
            $body = isset($res->data) ? (object) $res->data : (object) [];
            $statusCode = 200;
            /**
             *  -1- Lỗi không xác đinh
             *  0 - Thành công
             *  1 - Không tìm thấy
             *  2 - Thêm mới thất bại
             *  3 - Cập nhật thất bại
             *  4 - Xoá thất bại
             *  10 - Upload thất bại
             *  11 - Upload - max file size
             *  12 - Không tồn tại file với version đã chọn
             *
             *  20 - Đăng nhập thành công
             *  21 - Đăng nhập thất bại
             *  22 - Đăng xuất thành công
             *  23 - Đăng xuất thất bại
             *  24 - Tài khoản không tồn tại
             *  25 - Sai mật khẩu
             *  26 - Tài khoản chưa được kích hoạt
             *  27 - Tài khoản đang bị khoá
             */
            $this->auth = new $_authClass();

            // if ($this->auth->check()) {
            //     $jwt = $this->auth->jwt;
            //     $this->authLocale = $jwt->user->locale;
            // }
            $sql = "UPDATE tbl_routers SET count_call = count_call + 1 WHERE id = " . $route->id;
            $this->conn->query($sql);
            switch ($res->error) {
                case -1:
                    $statusCode = 500;
                    $body->status = false;
                    $body->message = $this->translate->translate("api_msg_unknown_error", $this->authLocale);
                    break;
                case 0:
                    $statusCode = 200;
                    $body->status = true;
                    $body->message = $this->translate->translate(is_null($route->msg_success) ? "api_msg_success" : $route->msg_success, $this->authLocale);
                    break;
                case 1:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_notfound" : $route->msg_error, $this->authLocale);
                    break;
                case 2:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_create_fail" : $route->msg_error, $this->authLocale);
                    break;
                case 3:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_update_fail" : $route->msg_error, $this->authLocale);
                    break;

                case 20:
                    $statusCode = 200;
                    $body->status = true;
                    $body->message = $this->translate->translate(is_null($route->msg_success) ? "api_msg_login_success" : $route->msg_success, $this->authLocale);
                    break;
                case 21:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_login_fail" : $route->msg_error, $this->authLocale);
                    break;
                case 24:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_login_not_found_account" : $route->msg_error, $this->authLocale);
                    break;
                case 25:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_login_password_not_match" : $route->msg_error, $this->authLocale);
                    break;
                case 26:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_login_account_active" : $route->msg_error, $this->authLocale);
                    break;
                case 27:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_login_account_lock" : $route->msg_error, $this->authLocale);
                    break;

                case 401:
                    $statusCode = 401;
                    $body->status = false;
                    $body->message = $this->translate->translate("api_msg_invalid_token", $this->authLocale);
                    $body->showMessage = $showMessage;
                    break;
                case 400:
                    $statusCode = 400;
                    $body->status = false;
                    $body->message = $this->translate->translate("api_msg_bad_request", $this->authLocale);
                    break;
                case 403:
                    $statusCode = 403;
                    $body->status = false;
                    $body->message = $this->translate->translate("api_msg_forbidden", $this->authLocale);
                    break;

                case 10:
                    $statusCode = 200;
                    $body->status = false;
                    $body->message = $this->translate->translate(is_null($route->msg_error) ? "api_msg_upload_fail" : $route->msg_error, $this->authLocale);
                    break;
                default:
                    break;
            }
            if (isset($res->error_description) && is_array($res->error_description)) {
                $errorDescription = array();
                foreach ($res->error_description as $key => $e) {
                    if (isset($e->message)) {
                        $e->message = $this->translate->translate($e->message, $this->authLocale);
                    }
                    array_push($errorDescription, $e);
                }
                $body->errorDescription = $errorDescription;
            }
            if ($route->realtime) {
                $pusher = new \AppPusher;
                $pusher->send($route->chanel, $route->event, $body);
            }
            $this->response->sendResponse($statusCode, $body);
        } catch (Exception $e) {
            $this->response->sendResponse(500, (object) ["error" => (string) $e]);
        }
    }

    private function getRoute()
    {

        $routers = array();
        if (isset($this->path)) {
            $sql = "SELECT * FROM tbl_routers WHERE  deleted = 0";
            $stmt = $this->conn->query($sql);
            while ($dataRow = $stmt->fetch_assoc()) {
                $route = (object) $dataRow;
                if ($this->isMatchPath($route->path)) {
                    array_push($routers, $route);
                }
            }
        }

        if (count($routers) === 0) {
            $this->response->sendResponse(404);
        } else {
            $index = array_search($this->method, array_column($routers, 'method'));
            if ($index === false) {
                $this->response->sendResponse(405);
            }
            return $routers[$index];
        }
    }

    private function isMatchPath($path)
    {
        $valid = false;
        $apiPathArr = explode("/", $path);
        $pathArr = explode("/", $this->path);
        if (strpos($this->path, ":")) {
            goto end;
        }
        if ("" . $this->path === "" . $path || (count($apiPathArr) == count($pathArr) && (strpos($path, "/:") !== false || strpos($path, "/i:") !== false))) {
            foreach ($pathArr as $key => $value) {
                if (strpos($apiPathArr[$key], "i:") === 0) {
                    if ("" . ((int) $value) != "" . $value || !is_numeric($value)) {
                        goto end;
                    }
                } else if (strpos($apiPathArr[$key], ":") === 0) {
                    if ("" . ((string) $value) != "" . $value || !is_string($value)) {
                        goto end;
                    }
                } else if ("" . $apiPathArr[$key] != "" . $value) {
                    goto end;
                }
            }
            $valid = true;
        }

        end:
        return $valid;
    }

    private function getPathData($path)
    {
        $params = array();
        $apiPathArr = explode("/", $path);
        $pathArr = explode("/", $this->path);
        if ($this->isMatchPath($path)) {
            foreach ($pathArr as $key => $value) {
                if (strpos($apiPathArr[$key], "i:") === 0) {
                    if ("" . ((int) $value) == "" . $value && is_numeric($value)) {
                        $params[substr($apiPathArr[$key], 2)] = (int) $value;
                    }
                } else if (strpos($apiPathArr[$key], ":") === 0) {
                    $params[substr($apiPathArr[$key], 1)] = $value;
                }
            }
        }
        return (object) $params;
    }

    private function validate($controllerMethod)
    {
        $class = "\V" . API_VERSION . "\Auth\AuthPermission";
        if (class_exists($class)) {
            $stdClass = new $class();
            if (method_exists($stdClass, "allowAccess") && is_callable($stdClass::class, "allowAccess")) {
                if (!$stdClass->allowAccess($controllerMethod, $this->roles, $this->groups)) {
                    $this->response->sendResponse(403);
                    exit;
                }
            } else {
                $this->response->sendResponse(500, (object) [
                    "status" => false,
                    "message" => $this->translate->translate("api_msg_unknown_error", $this->authLocale),
                ]);
            }
        } else {
            $this->response->sendResponse(500, (object) [
                "status" => false,
                "message" => $this->translate->translate("api_msg_unknown_error", $this->authLocale),
            ]);
        }

    }

    private function run($path, $method, $namespace, $functionName)
    {
        $data = null;
        $this->validate($namespace . "@" . $functionName);
        $class = "\\" . $namespace;
        if (class_exists($class)) {
            $stdClass = new $class();

            $pathData = $this->getPathData($path);
            $getData = (object) $_REQUEST;
            $bodyData = json_decode(file_get_contents("php://input"));
            if (method_exists($stdClass, $functionName) && is_callable($stdClass::class, $functionName)) {
                $data = $stdClass->$functionName($pathData, $getData, $bodyData);
                return $data;
            }
        }
        return (object) [
            "error" => -1,
        ];
    }
}
