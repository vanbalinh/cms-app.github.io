<?php
include_once __DIR__ . './../../vendor/autoload.php';
$fb = new \Facebook\Facebook([
    'app_id' => '157397817246073', // Replace {app-id} with your app id
    'app_secret' => '690c91db9f371c83918c6cccc24eb2b7',
    'default_graph_version' => 'v2.10',
]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://localhost/system-manager/test/fb-callback.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
