<?php
include_once __DIR__ . './Router.php';
$route = new Router();

/**
 *  Test
 */
$route->get("/api/v1/test/i:groupId", "V1\Account\GroupData@getAllIds");
$route->auth()->post("/api/v1/send-mail", "Mail\Mail@test");

/**
 *  Auth
 */
$route->post("/api/v1/auth/login", "V1\Auth@apiLogin");
$route->auth()->post("/api/v1/auth/logout", "V1\Auth@apiLogout");
$route->auth()->post("/api/v1/auth/logout-all", "V1\Auth@apiLogoutAll");
$route->post("/api/v1/auth/login/facebook", "V1\Auth\AuthFacebook@apiLogin");
$route->post("/api/v1/auth/login/google", "V1\Auth\AuthGoogle@apiLogin");
$route->post("/api/v1/auth/login/zalo", "V1\Auth\AuthZalo@apiLogin");

$route->auth()->get("/api/v1/auth/check", "V1\Auth@apiCheck");
$route->post("/api/v1/auth/change-password", "V1\Auth@apiChangePassword");
$route->post("/api/v1/auth/verification", "V1\Auth\Verification@apiCreateVeriricationCode");

$route->auth()->get("/api/v1/auth/permission", "V1\Auth\AuthPermission@getPermission");
$route->auth()->get("/api/v1/auth/permission/tree", "V1\Auth\AuthPermission@getPermissionTree");

/**
 *  End Auth
 *  ==========================================================================================================
 */

/**
 *
 */
$route->get("/api/v1/firebase", "V1\Firebase@apiSend");

$route->get("/api/v1/image-loading", "V1\Common@apiGetImageLoading");
$route->get("/api/v1/image-logo-header", "V1\Common@apiGetImageLogoHeader");
$route->get("/api/v1/image-shortcut-icon", "V1\Common@apiGetImageShortcutIcon");

$route->auth()->get("/api/v1/administrative-units", "V1\Common@apiGetAdministrativeUnits");

$route->auth([ROLE_SYSTEM_ADMINISTRATOR])->get("/api/v1/get-all-permission-key", "V1\Common@apiGetAllControllerMethodName");

/**
 *  Account
 */

$route->auth()->get("/api/v1/account", "V1\Account@apiFetchAll");
$route->auth()->get("/api/v1/account/i:id", "V1\Account@apiFindById");
$route->auth()->get("/api/v1/account/:username", "V1\Account@apiFindByUsername");
$route->auth()->post("/api/v1/account", "V1\Account@apiCreate");
$route->auth()->put("/api/v1/account", "V1\Account@apiUpdate");
$route->auth()->put("/api/v1/account/profile", "V1\Account@apiUpdateProfile");
$route->auth()->del("/api/v1/account", "V1\Account@apiDelete");
$route->post("/api/v1/account/register", "V1\Account@apiRegister");

/**
 *  End account
 *  ==========================================================================================================
 */

/**
 *  Permission
 */
$route->auth()->get("/api/v1/permission", "V1\Permission@apiFetchAll");
$route->auth()->get("/api/v1/permission/tree", "V1\Permission@apiFetchAllTree");
$route->auth()->get("/api/v1/permission/i:id", "V1\Permission@apiFindById");
$route->auth()->post("/api/v1/permission", "V1\Permission@apiCreate");
$route->auth()->put("/api/v1/permission", "V1\Permission@apiUpdate");
$route->auth()->put("/api/v1/permission/move", "V1\Permission@apiUpdateList");
$route->auth()->del("/api/v1/permission", "V1\Permission@apiDelete");
/**
 *  End Permission
 *  ==========================================================================================================
 */

/**
 *  Menu
 */
$route->auth()->get("/api/v1/menu", "V1\Menu@apiFetchAll");
$route->auth()->get("/api/v1/menu/tree", "V1\Menu@apiFetchAllTree");
$route->auth()->get("/api/v1/menu/i:id", "V1\Menu@apiFindById");
$route->auth()->post("/api/v1/menu", "V1\Menu@apiCreate");
$route->auth()->put("/api/v1/menu", "V1\Menu@apiUpdate");
$route->auth()->put("/api/v1/menu/move", "V1\Menu@apiUpdateList");
$route->auth()->del("/api/v1/menu", "V1\Menu@apiDelete");
/**
 *  End Menu
 *  ==========================================================================================================
 */

/**
 *  Upload
 */
$route->auth()->post("/api/v1/upload", "V1\Upload@upload");
$route->auth()->get("/api/v1/download/:code", "V1\Download@apiDownload");
/**
 *  End Upload
 *  ==========================================================================================================
 */

/**
 *  Folder
 */
$route->auth()->get("/api/v1/folder", "V1\Folder@apiFetchAll");
$route->auth()->get("/api/v1/folder/tree", "V1\Folder@apiFetchAllTree");
$route->auth()->get("/api/v1/folder/i:id", "V1\Folder@apiFindById");
$route->auth()->post("/api/v1/folder", "V1\Folder@apiCreate");
$route->auth()->del("/api/v1/folder", "V1\Folder@apiDelete");
/**
 *  End Folder
 *  ==========================================================================================================
 */

/**
 *  Form
 */
$route->auth()->get("/api/v1/form/field-type", "V1\Form@apiFetchAllFieldType");
$route->auth()->get("/api/v1/form", "V1\Form@apiFetchAll");
$route->auth()->get("/api/v1/form/folder/i:folderId", "V1\Form@apiFetchAll");
$route->get("/api/v1/form/i:formId", "V1\Form@apiFindById");
// $route->auth()->get("/api/v1/form/i:formId", "V1\Form@apiFindById");
$route->auth()->post("/api/v1/form", "V1\Form@apiCreate");
$route->auth()->put("/api/v1/form", "V1\Form@apiUpdate");
$route->auth()->del("/api/v1/form", "V1\Form@apiDelete");

$route->get("/api/v1/form/permission/i:formId", "V1\Form@apiCheckPermissionById");
$route->auth()->put("/api/v1/form/permission-form", "V1\Form@apiUpdateFormPermission");
$route->auth()->put("/api/v1/form/permission-field", "V1\Form@apiUpdateFormFieldPermission");

$route->get("/api/v1/form/i:formId/data", "V1\Form\Data@apiFetchAll");
$route->get("/api/v1/form/i:formId/data/i:id", "V1\Form\Data@apiFindById");
$route->post("/api/v1/form/i:formId/data", "V1\Form\Data@apiCreate");
$route->put("/api/v1/form/i:formId/data", "V1\Form\Data@apiUpdate");
$route->del("/api/v1/form/i:formId/data", "V1\Form\Data@apiDelete");

$route->get("/api/v1/form/:formCode/data", "V1\Form\Data@apiCodeFetchAll");
$route->get("/api/v1/form/:formCode/data/i:id", "V1\Form\Data@apiCodeFindById");
$route->post("/api/v1/form/:formCode/data", "V1\Form\Data@apiCodeCreate");
$route->put("/api/v1/form/:formCode/data", "V1\Form\Data@apiCodeUpdate");
$route->del("/api/v1/form/:formCode/data", "V1\Form\Data@apiCodeDelete");
/**
 *  End Form
 *  ==========================================================================================================
 */
// 404
$route->fallback();
