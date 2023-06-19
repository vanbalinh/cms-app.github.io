<?php

/**
 *  ACCOUNT
 *  username vbl.api.sso@gmail.com
 *  password VbL@041295
 */

class AppFirebase
{
    public function __construct()
    {
    }

    public function apiSend()
    {
        // FCM API Url
        $url = 'https://fcm.googleapis.com/fcm/send';

        // Put your Server Response Key here
        $apiKey = "YOUR SERVER RESPONSE KEY HERE";

        // Compile headers in one variable
        $headers = array(
            'Authorization:key=' . $apiKey,
            'Content-Type:application/json',
        );

        // Add notification content to a variable for easy reference
        $notifData = [
            'title' => "Test Title",
            'body' => "Test notification body",
            'click_action' => "android.intent.action.MAIN",
        ];

        // Create the api body
        $apiBody = [
            'notification' => $notifData,
            'data' => $notifData,
            "time_to_live" => "600", // Optional
            'to' => '/topics/mytargettopic', // Replace 'mytargettopic' with your intended notification audience
        ];

        // Initialize curl with the prepared headers and body
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiBody));

        // Execute call and save result
        $result = curl_exec($ch);

        // Close curl after call
        curl_close($ch);

        return $result;
    }
}
