<?php
namespace V1\Auth;

class AuthGoogle
{
    private $client;
    private $connect;
    private $conn;
    public function __construct()
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;

        $systemConfig = new \Config\SystemConfig;
        $googleConfig = $systemConfig->findSSOConfig("GOOGLE");

        $this->client = new \Google_Client();
        $this->client->setClientId($googleConfig->GOOGLE_APP_ID);
        $this->client->setClientSecret($googleConfig->GOOGLE_APP_SECRET);
        $this->client->setRedirectUri($googleConfig->GOOGLE_APP_CALLBACK);

        $this->client->addScope("email");
        $this->client->addScope("profile");
    }

    public function apiLogin()
    {
        $callbackUrl = $this->client->createAuthUrl();
        if (isset($_GET["code"])) {

            $token = $this->client->fetchAccessTokenWithAuthCode($_GET["code"]);
            if (isset($token['access_token'])) {
                $this->client->setAccessToken($token['access_token']);

                $google_oauth = new \Google\Service\Oauth2($this->client);
                $google_account_info = $google_oauth->userinfo->get();

                $email = $google_account_info->email;
                $first_name = $google_account_info->familyName;
                $last_name = $google_account_info->givenName;
                $gg_id = $google_account_info->id;
                $avatar_url = $google_account_info->picture;
                $locale = $google_account_info->locale;
                // {
                //     "email": "vanbalinh95@gmail.com",
                //     "familyName": "Văn Bá",
                //     "gender": null,
                //     "givenName": "Linh",
                //     "hd": null,
                //     "id": "116198879939918848308",
                //     "link": null,
                //     "locale": "vi",
                //     "name": "Linh Văn Bá",
                //     "picture": "https://lh3.googleusercontent.com/a/AGNmyxbc1TaT1SQGkSCu-o1moxSfE80LKpB8T5QvyxaW8A=s96-c",
                //     "verifiedEmail": true
                // }
                $account = new \V1\Account;
                if ($account->emailExist($email)) {
                    $auth = new \V1\Auth;
                    $login = $auth->loginByEmail($email);
                    if ($login) {
                        $login["status"] = true;
                        $login["msg"] = "Đăng nhập thành công";
                        $this->connect->sendResponse(200, $login);
                        return (object) [
                            "error" => 20,
                            "data" => (object) [
                                "data" => $login,
                            ],
                        ];
                    }
                } else {
                    $res = $account->_create((object) [
                        "username" => md5($gg_id),
                        "email" => $email,
                        "ggId" => $gg_id,
                        "password" => null,
                        "firstName" => $first_name,
                        "lastName" => $last_name,
                        "avatarUrl" => $avatar_url,
                        "locale" => $locale,
                        "roleId" => ROLE_USER_ID,
                    ]);
                    if ($res->error === 0) {
                        $auth = new \V1\Auth;
                        $login = $auth->loginByEmail($email);
                        if ($login) {
                            return (object) [
                                "error" => 20,
                                "data" => (object) [
                                    "data" => $login,
                                ],
                            ];
                        }
                    }
                }
            }
            return (object) [
                "error" => 21,
                "data" => (object) [
                    "data" => (object) [
                        "callbackUrl" => $callbackUrl,
                    ],
                ],
            ];
        }
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
    }
}
