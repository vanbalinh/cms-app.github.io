<?php
namespace V1;

class Auth
{
    // Connection
    private $conn;
    private $connect;
    private $db_table = "tbl_auth";

    public $jwt = null;
    private $token = null;

    private $maxTime = "+1 minutes";
    private $secretKey = 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=';
    private $alg = "HS512";
    public function __construct()
    {
        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
        $this->initJWT();
    }

    private function initJWT()
    {
        $header = getallheaders();
        if (
            (!isset($header["authorization"])
                || !preg_match('/Bearer\s(\S+)/', $header["authorization"], $matches))
            && (!isset($header["Authorization"])
                || !preg_match('/Bearer\s(\S+)/', $header["Authorization"], $matches))
        ) {

            goto end;
        }
        if (!isset($matches[1])) {
            goto end;
        }
        $token = $matches[1];
        $this->token = $token;

        \Firebase\JWT\JWT::$leeway += 60;
        $now = new \DateTimeImmutable ();
        $jwt = null;
        try {
            $jwt = \Firebase\JWT\JWT::decode($token, $this->secretKey, [$this->alg]);
        } catch (\Exception $e) {
        }
        if (
            !is_null($jwt) &&
            $jwt->nbf <= $now->getTimestamp() &&
            ($jwt->exp == 0 || $jwt->exp >= $now->getTimestamp())
        ) {
            $this->jwt = $jwt;
        }
        end:
    }

    public function check()
    {
        if (is_null($this->jwt)) {
            return false;
        } else {
            $sql = "SELECT account.id, account.email, account.role_id, account.group_data_id, account.locale
            FROM `tbl_auth` as auth, `tbl_accounts` as account
            WHERE
                auth.account_id = account.id
                AND account.deleted = 0
                AND account.id = '{$this->jwt->user->id}'
                AND auth.token = '$this->token' LIMIT 0,1";
            $stmt = $this->conn->query($sql);
            if ($stmt->num_rows == 1) {
                $dataRow = $stmt->fetch_assoc();
                $err = 0;
                $this->jwt->user = (object) $dataRow;
                $this->jwt->user->id = (int) $dataRow["id"];
                return true;
            }
            $sql = "DELETE FROM `tbl_auth` WHERE token = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $this->token);
            $stmt->execute();
            return false;
        }
    }

    private function createToken($user, $remember = false)
    {
        $issuedAt = new \DateTimeImmutable ();
        $expire = $issuedAt->modify($remember ? "+1 years" : "+1 days")->getTimestamp();

        $data = [
            'nbf' => $issuedAt->getTimestamp(),
            'exp' => $expire,
            'user' => $user,
        ];

        $token = \Firebase\JWT\JWT::encode(
            $data,
            $this->secretKey,
            $this->alg
        );
        return $token;
    }

    private function getClientIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    private function getInfoLogin($loginData = null)
    {
        $res = (object) getBrowser();
        $res->device_ip = $this->getClientIp();
        if (!is_null($loginData) && isset($loginData->device)) {
            if (isset($loginData->device->name)) {
                $res->device_name = $loginData->device->name;
            }
            if (isset($loginData->device->platform)) {
                $res->device_platform = $loginData->device->platform;
            }
            if (isset($loginData->device->version)) {
                $res->device_version = $loginData->device->version;
            }
        }
        return $res;
    }

    private function login($loginData)
    {
        $remember = isset($loginData->remember) && $loginData->remember ? true : false;
        if (isset($loginData->username) && isset($loginData->password)) {
            $account = new \V1\Account;
            $res = $account->apiLogin($loginData->username, $loginData->password);
            $user = (object) [];
            if (is_integer($res) && in_array($res, [24, 25, 26, 27])) {
                return $res;
            }
            if ($res) {
                $user->id = $res->id;
                $user->email = $res->email;
                $user->group_data_id = $res->groupDataId;
                $user->role_id = $res->role->id;
                $user->locale = $res->locale;

                $token = $this->createToken($user, $remember);
                $info = $this->getInfoLogin($loginData);
                $sql = "INSERT INTO " . $this->db_table . " (token,account_id,user_agent,device_ip,device_name,device_version,device_platform) VALUES (?,?,?,?,?,?,?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("sisssss", $token, $res->id, $info->user_agent, $info->device_ip, $info->device_name, $info->device_version, $info->device_platform);
                $stmt->execute();
                $groups = $account->findGroups($user->id);
                return [
                    "token" => $token,
                    "account" => $res,
                    "groups" => $groups,
                ];
            }
        }
        return null;
    }

    public function loginByEmail($email)
    {
        if (isset($email)) {
            $account = new \V1\Account;
            $res = $account->findByEmail($email);
            $user = (object) [];
            if ($res) {
                $user->id = $res->id;
                $user->email = $res->email;
                $user->group_data_id = $res->groupDataId;
                $user->role_id = $res->roleId;
                $user->locale = $res->locale;

                $token = $this->createToken($user);
                $info = $this->getInfoLogin();
                $sql = "INSERT INTO " . $this->db_table . " (token,account_id,user_agent,device_ip,device_name,device_version,device_platform) VALUES (?,?,?,?,?,?,?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("sisssss", $token, $res->id, $info->user_agent, $info->device_ip, $info->device_name, $info->device_version, $info->device_platform);
                $stmt->execute();
                $groups = $account->findGroups($this->jwt->user->id);

                return [
                    "token" => $token,
                    "account" => $res,
                    "groups" => $groups,
                ];
            }
        }
        return null;
    }

    private function logout($logoutAll)
    {
        if ($logoutAll) {
            $sql = "DELETE FROM " . $this->db_table . " WHERE account_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $this->jwt->user->id);
        } else {
            $sql = "DELETE FROM " . $this->db_table . " WHERE account_id = ? AND token = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $this->jwt->user->id, $this->token);
        }
        if ($stmt->execute()) {
            return true;
        };
        return false;
    }

    public function apiLogin($pathData, $getData, $bodyData)
    {
        $res = $this->login($bodyData);
        if (is_integer($res) && in_array($res, [24, 25, 26, 27])) {
            return (object) [
                "error" => $res,
            ];
        }
        if ($res) {
            return (object) [
                "error" => 20,
                "data" => (object) [
                    "data" => $res,
                ],
            ];
        } else {
            return (object) [
                "error" => 21,
            ];
        }
    }

    public function apiCheck()
    {
        if ($this->check()) {
            $account = new \V1\Account;
            $res = $account->findById($this->jwt->user->id);
            if ($res) {
                $accountId = $this->jwt->user->id;
                $roleId = $this->jwt->user->role_id;
                $groups = $account->findGroups($this->jwt->user->id);
                $authPermission = new Auth\AuthPermission;
                $permissions = $authPermission->fetchAllPermission($accountId, $roleId);
                return (object) [
                    "error" => 0,
                    "data" => (object) [
                        "data" => (object) [
                            "account" => $res,
                            "groups" => $groups,
                            "permissions" => $permissions,
                        ],
                    ],
                ];
            }
        }
        return (object) [
            "error" => 401,
        ];
    }

    public function apiLogout()
    {
        if ($this->logout(false)) {
            $this->connect->sendResponse(200, array(
                "status" => true,
                "msg" => "Đăng xuất thành công",
            ));
        } else {
            $this->connect->sendResponse(200, array(
                "status" => false,
                "msg" => "Đăng xuất thất bại",
            ));
        }
    }

    public function apiLogoutAll()
    {
        if ($this->logout(true)) {
            $this->connect->sendResponse(200, array(
                "status" => true,
                "msg" => "Đăng xuất tất cả thành công",
            ));
        } else {
            $this->connect->sendResponse(200, array(
                "status" => false,
                "msg" => "Đăng xuất thất bại",
            ));
        }
    }

    public function apiGetMyMenu()
    {
        if ($this->check()) {
            $accountId = $this->jwt->user->id;
            $roleId = $this->jwt->user->role_id;
            $authPermission = new \V1\Auth\AuthPermission;
            $data = $authPermission->fetchAllMenu($accountId, $roleId);
            return (object) [
                "error" => 0,
                "data" => (object) [
                    "data" => $data,
                ],
            ];
        } else {
            return (object) [
                "error" => 401,
            ];
        }
    }

    public function apiGetMyPermission()
    {
        if ($this->check()) {
            $accountId = $this->jwt->user->id;
            $roleId = $this->jwt->user->role_id;
            $authPermission = new \V1\Auth\AuthPermission;
            $data = $authPermission->fetchAllPermission($accountId, $roleId);
            return (object) [
                "error" => 0,
                "data" => (object) [
                    "data" => $data,
                ],
            ];
        } else {
            return (object) [
                "error" => 401,
            ];
        }
    }

    public function apiRegister($pathData, $getData, $bodyData)
    {

    }
}
