<?php
include_once __DIR__ . './Core.php';
include_once __DIR__ . './Config.php';
include_once __DIR__ . './Format.php';
include_once __DIR__ . './Fetch.php';
include_once __DIR__ . './Tree.php';
include_once __DIR__ . './Find.php';
include_once __DIR__ . './Exist.php';
include_once __DIR__ . './Create.php';
include_once __DIR__ . './Update.php';
include_once __DIR__ . './Delete.php';

class Controller
{
    private $controllerFetch;
    private $controllerFind;
    private $controllerCreate;
    private $controllerUpdate;
    private $controllerDelete;
    private $controllerTree;
    private $config = null;

    private $table_name;
    private $fields = array();
    private $field;

    public function __construct()
    {
        $this->field = (object) [];
    }

    public function getSystemConfig($configName = null)
    {
        $controllerConfig = new \Controller\ControllerConfig ($configName);
        return $controllerConfig->config;
    }

    public function getSystemFormat()
    {
        return new \Controller\ControllerFormat;
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case "init":
                if (isset($args[0])) {
                    $this->table_name = $args[0];
                    if (isset($args[1])) {
                        $this->field = (object) $args[1];
                    }
                    if (isset($args[2])) {
                        $this->config = (object) $args[2];
                    }
                }
                break;
            case "initField":
                if (isset($args[0])) {
                    foreach ((object) $args[0] as $key => $value) {
                        $this->field->$key = $value;
                    }
                }
                break;
            case "_rollback":
                $connect = new \Config\Connect;
                $conn = $connect->conn;
                $conn->rollback();
                break;
            case "_run":
                if (isset($args[0])) {
                    $sql = $args[0];
                    $connect = new \Config\Connect;
                    $conn = $connect->conn;
                    $conn->autocommit(false);
                    $query = $conn->query($sql);
                    $conn->commit();
                    return $query;
                }
                break;
            case "_query":
                if (isset($args[0])) {
                    $sql = $args[0];
                    $connect = new \Config\Connect;
                    $conn = $connect->conn;
                    $query = $conn->query($sql);
                    $conn->commit();

                    return $query;
                }
                break;
            case "_prepare":
                if (isset($args[0])) {
                    $sql = $args[0];
                    $connect = new \Config\Connect;
                    $conn = $connect->conn;
                    $query = $conn->prepare($sql);
                    return $query;
                }
                break;
            case "_fetch":
                $this->controllerFetch = new Controller\ControllerFetch;
                $this->controllerFetch->init($this->table_name, $this->field, $this->config);
                $fetchInput = (object) [];
                if (isset($args[0])) {
                    $fetchInput = (object) $args[0];
                }
                return $this->controllerFetch->fetch($fetchInput);
            case "_tree":
                $this->controllerTree = new Controller\ControllerTree;
                $this->controllerTree->init($this->table_name, $this->field, $this->config);
                return $this->controllerTree->tree(isset($args[0]) ? $args[0] : (object) []);

            case "_find":
                $this->controllerFind = new Controller\ControllerFind;
                $this->controllerFind->init($this->table_name, $this->field, $this->config);
                $data = $args[0];
                $fields = (isset($args[1]) && is_array($args[1]) ? $args[1] : ["id"]);
                $showItemInfo = (isset($args[2]) && is_bool($args[2]) ? $args[2] : true);
                return $this->controllerFind->find($data, $fields, $showItemInfo);

            case "_exist":
                $this->controllerExist = new Controller\ControllerExist;
                $this->controllerExist->init($this->table_name, $this->field, $this->config);
                $data = $args[0];
                $fields = (isset($args[1]) && is_array($args[1]) ? $args[1] : ["id"]);
                return $this->controllerExist->exist($data, $fields);

            case "_create":
                $this->controllerCreate = new Controller\ControllerCreate;
                $this->controllerCreate->init($this->table_name, $this->field, $this->config);
                return $this->controllerCreate->create($args[0]);

            case "_update":
                $this->controllerUpdate = new Controller\ControllerUpdate;
                $this->controllerUpdate->init($this->table_name, $this->field, $this->config);
                return $this->controllerUpdate->update($args[0]);

            case "_delete":
                $this->controllerDelete = new Controller\ControllerDelete;
                $this->controllerDelete->init($this->table_name, $this->field, $this->config);
                return $this->controllerDelete->delete($args[0]);
            case "getJWT":
                $controllerCore = new Controller\ControllerCore;
                return $controllerCore->getJWT();
            default:
                return (object) [
                    "error" => -1,
                ];
        }
    }
}
