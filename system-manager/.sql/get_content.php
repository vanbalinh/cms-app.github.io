<?php
function getContent()
{
    $files = array(
        "role",
        "config",
        "config-role",

        "group-data",

        "system-config",
        "account",
        "group",
        "group-account",
        "permission",
        "menu",

        "language",
        "translate",

        "auth",
        "mobile-device",
        "files",
        "folder",
        "form",
        "form-fields",

        "group-data-share",

        "permission-role",
        "permission-account",
        "permission-group",

        "permission-form-account",
        "permission-form-group",
        "permission-form-role",

        "permission-form-field-account",
        "permission-form-field-group",
        "permission-form-field-role",

        "qrcodelogin",
        "verification",

        "routers",
        "router-account",
        "router-role",
        "router-group",

        // "notification-content",
        // "notification-manager",
        // "notification",

        // "message",
        // "room",
        // "room.account",
        // "room.message",
        // "room.message.account",

        "msg.room",
        "msg.room.account",
        "msg.message",
        "msg.room.message.account",

        "page",

        "trigger.config",
        "trigger.permission",
        "data",
        "data-router",
    );
    $l = 1;
    $line = "";
    $value = "";
    $res = array();
    foreach ($files as $f) {
        $_fileContent = (object) [];
        $_fileContentData = array();
        $file = fopen(__DIR__ . "./" . $f . ".sql", "r");
        $_fileContent->name = $f;
        while (!feof($file)) {
            array_push($_fileContentData, fgets($file));
        }
        $_fileContent->data = $_fileContentData;
        array_push($res, $_fileContent);
        fclose($file);
    }
    return $res;
}
