<?php

/**
 *  ACCOUNT
 *  username vbl.api.sso@gmail.com
 *  password VbL@041295
 */

class AppPusher
{
    public $pusher;
    public function __construct()
    {
        $options = array(
            'cluster' => 'eu',
            'useTLS' => true,
        );
        $this->pusher = new \Pusher\Pusher (
            '495b1d657873f2ea1300',
            '9d0dc313ccdcaa80498b',
            '1332718',
            $options
        );
    }

    public function send($chanel, $event, $data)
    {
        $this->pusher->trigger($chanel, $event, $data);
    }

    public function apiSend($pathData, $getData, $bodyData)
    {
        $res = $this->pusher->trigger($pathData->chanel, $pathData->event, $bodyData->data);
        return (object) [
            "error" => 0,
        ];
    }
}
