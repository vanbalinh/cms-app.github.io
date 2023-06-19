<?php
namespace Config;

use \Config\Response;

class Connect
{
    private $host = "localhost";
    private $database_name = "cms";
    private $username = "root";
    private $password = "";

    public $conn;

    public function __construct()
    {
        $this->conn = new \mysqli ($this->host, $this->username, $this->password, $this->database_name);
        if ($this->conn->connect_errno) {
            $this->conn = null;
        }
    }

    public function __destruct()
    {
        if (isset($this->conn) && !is_null($this->conn)) {
            $this->conn->close();
        }
    }

    public function sendResponse($status = 200, $body = null)
    {
        $response = new Response;
        return $response->sendResponse($status, $body);
    }
}
