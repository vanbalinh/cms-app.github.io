<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");

// Define Group
define("GROUP_GUEST", "GUEST");
define("GROUP_GUEST_ID", 1000000000);

// Define Role
define("ROLE_SYSTEM_ADMINISTRATOR", "SYSTEM_ADMINISTRATOR");
define("ROLE_ADMINISTRATOR", "ADMINISTRATOR");
define("ROLE_USER", "USER");
define("ROLE_GUEST", "GUEST");

define("ROLE_SYSTEM_ADMINISTRATOR_ID", 1000000000);
define("ROLE_ADMINISTRATOR_ID", 1000000001);
define("ROLE_USER_ID", 1000000002);
define("ROLE_GUEST_ID", 1000000003);

// Status message
define("MSG_CREATE_SUCCESSFUL", "Thêm mới thành công!");
define("MSG_UPDATE_SUCCESSFUL", "Cập nhật thành công!");
define("MSG_DELETE_SUCCESSFUL", "Xoá thành công!");
define("MSG_FETCH_SUCCESSFUL", "Lấy dữ liệu thành công!");
define("MSG_REGISTER_SUCCESSFUL", "Đăng ký thành công!");
define("MSG_LOGIN_SUCCESSFUL", "Đăng nhập thành công!");

define("MSG_CREATE_FAILED", "Thêm mới thất bại!");
define("MSG_UPDATE_FAILED", "Cập nhật thất bại!");
define("MSG_DELETE_FAILED", "Xoá thất bại!");
define("MSG_FETCH_FAILED", "Lấy dữ liệu thất bại!");
define("MSG_REGISTER_FAILED", "Đăng ký thất bại!");

define("MSG_DATA_NOTFOUND", "Không tìm thấy dữ liệu!");

define("MSG_INVALID_DATA", "Dữ liệu không hợp lệ!");

define("MSG_API_FIELD_UNIQUE", "api_400_field_unique");

// define("API_VERSION", 1);

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";
    $ub = "";

    if (
        preg_match('/cms-android/i', $u_agent)
        || preg_match('/cms-ios/i', $u_agent)
        || preg_match('/cms-windows/i', $u_agent)
        || preg_match('/cms-macos/i', $u_agent)
        || preg_match('/cms-web/i', $u_agent)
    ) {
        $data = json_decode($u_agent);
        return array(
            'user_agent' => $u_agent,
            'device_name' => $data->name,
            'device_version' => $data->version,
            'device_platform' => $data->platform,
        );
    }

    //First get the platform?
    if (preg_match('/android/i', $u_agent)) {
        $platform = 'android';
    } elseif (preg_match('/iphone/i', $u_agent)) {
        $platform = 'iphone';
    } elseif (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/OPR/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
        if (preg_match('/iphone/i', $u_agent)) {
            $bname = 'Iphone Safari';
        } else {
            $bname = 'Apple Safari';
        }
        $ub = "Safari";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } elseif (preg_match('/Edge/i', $u_agent)) {
        $bname = 'Edge';
        $ub = "Edge";
    } elseif (preg_match('/Trident/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Postman/i', $u_agent)) {
        $bname = 'Postman';
    }

    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    $i = count($matches['browser']);
    if ($i != 1) {
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }

    return array(
        'user_agent' => $u_agent,
        'device_name' => $bname,
        'device_version' => $version,
        'device_platform' => $platform,
    );
}
