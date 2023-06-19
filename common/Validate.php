<?php
namespace Common;

class Validate
{
    public function __construct()
    {
        $this->connect = new \Connect\Connect;
        $this->conn = $this->connect->getConnection();
    }

    private function DateFeToBeFormat($format)
    {
        $format = str_replace("DD", "d", $format);
        $format = str_replace("MM", "m", $format);
        $format = str_replace("YYYY", "Y", $format);
        $format = str_replace("YY", "y", $format);

        $format = str_replace("HH", "H", $format);
        $format = str_replace("mm", "i", $format);
        $format = str_replace("ss", "s", $format);
        return $format;
    }

    private function DateBeToFeFormat($format)
    {
        $format = str_replace("d", "DD", $format);
        $format = str_replace("m", "MM", $format);
        $format = str_replace("Y", "YYYY", $format);
        $format = str_replace("YY", "y", $format);

        $format = str_replace("H", "HH", $format);
        $format = str_replace("i", "mm", $format);
        $format = str_replace("s", "ss", $format);
        return $format;
    }

    public function Date($date)
    {
        $config = new \Config\Config;
        $configDateFormat = $config->find("DATE_FORMAT_BE");
        $dateFormat = "d/m/Y";
        if ($configDateFormat) {
            $dateFormat = $configDateFormat->value;
        }
        $d = DateTime::createFromFormat($dateFormat, $date);
        return $d && $d->format($dateFormat) === $date;
    }

    public function DateTime($dateTime)
    {
        $config = new \Config\Config;
        $configDateTimeFormat = $config->find("DATE_TIME_FORMAT_BE");
        $dateTimeFormat = "d/m/Y H:i:s";
        if ($configDateTimeFormat) {
            $dateTimeFormat = $configDateTimeFormat->value;
        }
        $d = DateTime::createFromFormat($dateTimeFormat, $dateTime);
        return $d && $d->format($dateTimeFormat) === $dateTime;
    }
}
