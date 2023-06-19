<?php
namespace V1\Auth;

use \Config\Connect;
use \V1\Account;
use \V1\Auth;

class AuthFacebook
{
    private $connect;
    private $conn;
    private $facebook;
    private $helper;
    public function __construct()
    {
        $this->connect = new Connect();
        $this->conn = $this->connect->conn;

        $systemConfig = new \Config\SystemConfig();
        $facebookConfig = $systemConfig->findSSOConfig("FACEBOOK");

        define("FACEBOOK_APP_ID", $facebookConfig->FACEBOOK_APP_ID);
        define("FACEBOOK_APP_SECRET", $facebookConfig->FACEBOOK_APP_SECRET);
        define("FACEBOOK_APP_CALLBACK", $facebookConfig->FACEBOOK_APP_CALLBACK);
        define("FACEBOOK_APP_DEFAULT_GRAPH_VERSION", $facebookConfig->FACEBOOK_APP_DEFAULT_GRAPH_VERSION);

        $this->facebook = new \Facebook\Facebook([
            'app_id' => FACEBOOK_APP_ID,
            'app_secret' => FACEBOOK_APP_SECRET,
            'default_graph_version' => FACEBOOK_APP_DEFAULT_GRAPH_VERSION,
        ]);
        $this->helper = $this->facebook->getRedirectLoginHelper();
    }

    public function apiLogin()
    {
        $permissions = ['email'];
        $callbackUrl = $this->helper->getLoginUrl(FACEBOOK_APP_CALLBACK, $permissions);
        return (object) [
            "error" => 400,
            "data" => (object) [
                "data" => (object) [
                    "callbackUrl" => $callbackUrl,
                ],
            ],
            "error_description" => [
                (object) [
                    "field" => "code",
                    "message" => "api_400_required",
                ],
            ],
        ];
        try {
            $accessToken = $this->helper->getAccessToken();
            echo $accessToken;
            $response = $this->facebook->get('/me?fields=id,first_name,last_name,email', $accessToken);
        } catch (\Facebook\Exceptions\facebookResponseException$e) {
            echo (string) $e;
        }
        exit;
        if (!isset($accessToken)) {
            echo 123;
            $permissions = ['email'];
            $loginUrl = $this->helper->getLoginUrl(FACEBOOK_APP_CALLBACK, $permissions);
            $this->connect->sendResponse(200, [
                "status" => false,
                "url" => $loginUrl,
            ]);
        } else {
            echo 123;
            $me = $response->getGraphUser();

            $email = $me->getEmail();
            $first_name = $me->getFirstName();
            $last_name = $me->getLastName();
            $fb_id = $me->getId();
            $account = new Account();

            if ($account->findByFbId($fb_id)) {
                $auth = new Auth();
                $login = $auth->loginByFbId($fb_id);
                if ($login) {
                    $login["status"] = true;
                    $login["msg"] = "Đăng nhập thành công";
                    $this->connect->sendResponse(200, $login);
                    exit;
                }
            } else {
                $res = $account->create((object) [
                    "username" => md5($fb_id),
                    "fbId" => $fb_id,
                    "password" => null,
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                ]);
                if ($res->status && $res->data) {
                    $auth = new Auth();
                    $login = $auth->loginByFbId($fb_id);
                    if ($login) {
                        $login["status"] = true;
                        $login["msg"] = "Đăng nhập thành công";
                        $this->connect->sendResponse(200, $login);
                        exit;
                    }
                }
            }
        }
    }
}
