<?php
namespace Common;

class Format
{
    private $conn;
    private $connect;
    public $config;
    public function __construct()
    {

        $this->connect = new \Config\Connect;
        $this->conn = $this->connect->conn;
        $this->config = (object) [];
        $this->fetchAllConfig();
    }

    private function fetchAllConfig()
    {
        $sql = "SELECT name, value FROM `tbl_system_config` WHERE deleted = 0";
        $stmt = $this->conn->query($sql);
        while ($item = $stmt->fetch_assoc()) {
            $formatName = $item["name"];
            $this->config->$formatName = $item["value"];
        }
    }

    private function getFormat($format)
    {
        switch ($format) {
            case "DD/MM/YYYY":
                return "d/m/Y";
            case "DD-MM-YYYY":
                return "d-m-Y";
            case "DD/MM/YYYY HH:mm:ss":
                return "d/m/Y H:i:s";
            case "DD-MM-YYYY HH:mm:ss":
                return "d-m-Y H:i:s";
            case "HH:mm:ss DD-MM-YYYY":
                return "H:i:s d-m-Y";
            case "HH:mm:ss DD/MM/YYYY":
                return "H:i:s d/m/Y";
            default:
                return $format;
        }
    }
    // convert from backend to frontend
    public function DateConvert($date)
    {
        if (!isset($date) || is_null($date)) {
            return null;
        }

        $dateFormat = isset($this->config->DATE_FORMAT) ? $this->config->DATE_FORMAT : "DD/MM/YYYY";
        $dateFormat = $this->getFormat($dateFormat);
        $d = \DateTime::createFromFormat("Y-m-d", $date);
        return $d ? $d->format($dateFormat) : null;
    }

    // convert from backend to frontend
    public function DateTimeConvert($dateTime)
    {
        if (!isset($dateTime) || is_null($dateTime)) {
            return null;
        }
        $dateTimeFormat = isset($this->config->DATE_TIME_FORMAT) ? $this->config->DATE_TIME_FORMAT : "HH:mm:ss DD/MM/YYYY";
        $dateTimeFormat = $this->getFormat($dateTimeFormat);

        $d = \DateTime::createFromFormat("Y-m-d H:i:s", $dateTime);
        return $d ? $d->format($dateTimeFormat) : null;
    }

    // convert from frontend to backend
    public function DateReConvert($date)
    {
        if (!isset($date) || is_null($date)) {
            return null;
        }

        $dateFormat = isset($this->config->DATE_FORMAT) ? $this->config->DATE_FORMAT : "DD/MM/YYYY";
        $dateFormat = $this->getFormat($dateFormat);

        $d = \DateTime::createFromFormat($dateFormat, $date);
        return $d ? $d->format("y-m-d") : null;
    }

    // convert from frontend to backend
    public function DateTimeReConvert($dateTime)
    {
        if (!isset($dateTime) || is_null($dateTime)) {
            return null;
        }

        $dateTimeFormat = isset($this->config->DATE_TIME_FORMAT) ? $this->config->DATE_TIME_FORMAT : "HH:mm:ss DD/MM/YYYY";
        $dateTimeFormat = $this->getFormat($dateTimeFormat);

        $d = \DateTime::createFromFormat($dateTimeFormat, $dateTime);
        return $d ? $d->format("Y-m-d H:i:s") : null;
    }
}
