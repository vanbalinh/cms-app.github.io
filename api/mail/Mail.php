<?php
use \Config\Connect;
use \PHPMailer\PHPMailer\PHPMailer;

class AppMail
{
    private $connect;

    public function __construct()
    {
        $this->connect = new Connect;
    }

    /**
     *  to = array email []
     */
    public function send($to, $subject, $body)
    {

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->CharSet = "UTF-8";
        $mail->Username = 'vbl.cms.noreply@gmail.com';
        $mail->Password = 'zzompnwcpfrasflr';
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->From = "cms.noreply@gmail.com";
        $mail->FromName = "No Reply";
        foreach ($to as $key => $value) {
            $mail->addAddress($value);
        }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $sendMailSuccess = $mail->send();
        $mail->smtpClose();
        return $sendMailSuccess;
    }

    public function apiSend()
    {
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->to) && is_array($data->to) && isset($data->body) && isset($data->subject)) {
            $sendMailSuccess = $this->send($data->to, $data->subject, $data->body);
            if ($sendMailSuccess) {
                return (object) [
                    "error" => 0,
                ];
            }
            return (object) [
                "error" => -1,
            ];
        } else {
            return (object) [
                "error" => 400,
            ];
        }
    }
}
