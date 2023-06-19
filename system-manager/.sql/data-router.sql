INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        0,
        'V1_Auth',
        'apiLogin',
        'POST',
        '/api/v1/auth/login'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'Router',
        'apiFetchAll',
        'GET',
        '/api/system-router'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'Router',
        'apiFindById',
        'GET',
        '/api/system-router/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'Router',
        'apiCreate',
        'POST',
        '/api/system-router'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'Router',
        'apiUpdate',
        'PUT',
        '/api/system-router'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'Router',
        'apiDelete',
        'DELETE',
        '/api/system-router'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'AppPusher',
        'apiSend',
        'POST',
        '/api/pusher/:chanel/:event'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'AppMail',
        'apiSend',
        'POST',
        '/api/mail/send'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'SystemConfig',
        'apiFetchAll',
        'GET',
        '/api/system-config'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'SystemConfig',
        'apiFindById',
        'GET',
        '/api/system-config/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'SystemConfig',
        'apiFindByName',
        'GET',
        '/api/system-config/:name'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'SystemConfig',
        'apiCreate',
        'POST',
        '/api/system-config'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'SystemConfig',
        'apiUpdate',
        'PUT',
        '/api/system-config'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'SystemConfig',
        'apiDelete',
        'DELETE',
        '/api/system-config'
    );

-- Language
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Language',
        'apiFetchAll',
        'GET',
        '/api/v1/language'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Language',
        'apiFindById',
        'GET',
        '/api/v1/language/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Language',
        'apiFindByLocale',
        'GET',
        '/api/v1/language/:locale'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Language',
        'apiCreate',
        'POST',
        '/api/v1/language'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Language',
        'apiUpdate',
        'PUT',
        '/api/v1/language'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Language',
        'apiUpdateDefault',
        'PUT',
        '/api/v1/language/update-default'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Language',
        'apiDelete',
        'DELETE',
        '/api/v1/language'
    );

-- Auth
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Auth',
        'apiCheck',
        'GET',
        '/api/v1/auth/check'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Auth',
        'apiLogout',
        'POST',
        '/api/v1/auth/logout'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Auth',
        'apiLogoutAll',
        'POST',
        '/api/v1/auth/logout-all'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        0,
        'V1_Auth_AuthGoogle',
        'apiLogin',
        'POST',
        '/api/v1/auth/login/google'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        0,
        'V1_Auth_AuthFacebook',
        'apiLogin',
        'POST',
        '/api/v1/auth/login/facebook'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Auth',
        'apiGetMyMenu',
        'GET',
        '/api/v1/auth/my-menu'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Auth',
        'apiGetMyPermission',
        'GET',
        '/api/v1/auth/my-permission'
    );

-- Account
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Account',
        'apiFetchAll',
        'GET',
        '/api/v1/account'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Account',
        'apiFindById',
        'GET',
        '/api/v1/account/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Account',
        'apiFindByUsername',
        'GET',
        '/api/v1/account/:username'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Account',
        'apiCreate',
        'POST',
        '/api/v1/account'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Account',
        'apiUpdate',
        'PUT',
        '/api/v1/account'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Account',
        'apiDelete',
        'DELETE',
        '/api/v1/account'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Account',
        'apiUpdateProfile',
        'PUT',
        '/api/v1/profile'
    );

-- Folder
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Folder',
        'apiFetchAll',
        'GET',
        '/api/v1/folder'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Folder',
        'apiFetchAllTree',
        'GET',
        '/api/v1/folder/tree'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Folder',
        'apiFindById',
        'GET',
        '/api/v1/folder/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Folder',
        'apiCreate',
        'POST',
        '/api/v1/folder'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Folder',
        'apiUpdate',
        'PUT',
        '/api/v1/folder'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Folder',
        'apiDelete',
        'DELETE',
        '/api/v1/folder'
    );

-- Permission
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Permission',
        'apiFetchAll',
        'GET',
        '/api/v1/permission'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Permission',
        'apiFetchAllTree',
        'GET',
        '/api/v1/permission/tree'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Permission',
        'apiFindById',
        'GET',
        '/api/v1/permission/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Permission',
        'apiCreate',
        'POST',
        '/api/v1/permission'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Permission',
        'apiUpdate',
        'PUT',
        '/api/v1/permission'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Permission',
        'apiDelete',
        'DELETE',
        '/api/v1/permission'
    );

-- Menu
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Menu',
        'apiFetchAll',
        'GET',
        '/api/v1/menu'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Menu',
        'apiFetchAllTree',
        'GET',
        '/api/v1/menu/tree'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Menu',
        'apiFindById',
        'GET',
        '/api/v1/menu/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Menu',
        'apiCreate',
        'POST',
        '/api/v1/menu'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Menu',
        'apiUpdate',
        'PUT',
        '/api/v1/menu'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Menu',
        'apiDelete',
        'DELETE',
        '/api/v1/menu'
    );

-- Translate
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Translate',
        'apiFetchAll',
        'GET',
        '/api/v1/translate'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Translate',
        'apiFindById',
        'GET',
        '/api/v1/translate/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Translate',
        'apiFindByCode',
        'GET',
        '/api/v1/translate/:code'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Translate',
        'apiCreate',
        'POST',
        '/api/v1/translate'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Translate',
        'apiUpdate',
        'PUT',
        '/api/v1/translate'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Translate',
        'apiDelete',
        'DELETE',
        '/api/v1/translate'
    );

-- File
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_File',
        'apiUpload',
        'GET',
        '/api/v1/file/upload'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_File',
        'apiDownload',
        'POST',
        '/api/v1/file/download/:code'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_File',
        'apiFetchAll',
        'GET',
        '/api/v1/file'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_File',
        'apiFindById',
        'GET',
        '/api/v1/file/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_File',
        'apiCreate',
        'POST',
        '/api/v1/file'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_File',
        'apiUpdate',
        'PUT',
        '/api/v1/file'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_File',
        'apiDelete',
        'DELETE',
        '/api/v1/file'
    );

-- Page builder
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        0,
        'V1_Builder_Page',
        'apiFetchAll',
        'GET',
        '/api/v1/builder/page'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Page',
        'apiFindById',
        'GET',
        '/api/v1/builder/page/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Page',
        'apiCreate',
        'POST',
        '/api/v1/builder/page'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Page',
        'apiUpdate',
        'PUT',
        '/api/v1/builder/page'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Page',
        'apiDelete',
        'DELETE',
        '/api/v1/builder/page'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Page',
        'apiUpdateHomePage',
        'PUT',
        '/api/v1/builder/page/update-home-page'
    );

-- Form builder
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form',
        'apiFetchAll',
        'GET',
        '/api/v1/builder/form'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form',
        'apiFindById',
        'GET',
        '/api/v1/builder/form/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form',
        'apiCreate',
        'POST',
        '/api/v1/builder/form'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form',
        'apiUpdate',
        'PUT',
        '/api/v1/builder/form'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form',
        'apiDelete',
        'DELETE',
        '/api/v1/builder/form'
    );

-- Form Field builder
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Field',
        'apiFetchAll',
        'GET',
        '/api/v1/builder/form/i:form_id/field'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Field',
        'apiFindById',
        'GET',
        '/api/v1/builder/form/i:form_id/field/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Field',
        'apiCreate',
        'POST',
        '/api/v1/builder/form/i:form_id/field'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Field',
        'apiUpdate',
        'PUT',
        '/api/v1/builder/form/i:form_id/field'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Field',
        'apiDelete',
        'DELETE',
        '/api/v1/builder/form/i:form_id/field'
    );

-- Form builder data
-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiFetchAll',
        'GET',
        '/api/v1/builder/form/i:form_id/data'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiTree',
        'GET',
        '/api/v1/builder/form/i:form_id/data/tree'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiFindById',
        'GET',
        '/api/v1/builder/form/i:form_id/data/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiCreate',
        'POST',
        '/api/v1/builder/form/i:form_id/data'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiUpdate',
        'PUT',
        '/api/v1/builder/form/i:form_id/data'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiDelete',
        'DELETE',
        '/api/v1/builder/form/i:form_id/data'
    );

-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiFetchAll',
        'GET',
        '/api/v1/builder/form/:code/data'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiFindById',
        'GET',
        '/api/v1/builder/form/:code/data/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiCreate',
        'POST',
        '/api/v1/builder/form/:code/data'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiUpdate',
        'PUT',
        '/api/v1/builder/form/:code/data'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Builder_Form_Data',
        'apiDelete',
        'DELETE',
        '/api/v1/builder/form/:code/data'
    );

-- Chat
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Chat_Room',
        'apiFindById',
        'GET',
        '/api/v1/chat/room/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Chat_SendMessage',
        'apiSend',
        'POST',
        '/api/v1/chat/message/send'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Chat_GetMessage',
        'apiFetchAllMessageByRoom',
        'GET',
        '/api/v1/chat/message/i:roomId'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Chat_GetMessage',
        'apiFetchAllMessageTo',
        'GET',
        '/api/v1/chat/message/i:roomId/to/i:messageId'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Chat_GetMessage',
        'apiFetchAllMessageFrom',
        'GET',
        '/api/v1/chat/message/i:roomId/from/i:messageId'
    );

-- -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Role',
        'apiFetchAll',
        'GET',
        '/api/v1/role'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Role',
        'apiFindById',
        'GET',
        '/api/v1/role/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Role',
        'apiCreate',
        'POST',
        '/api/v1/role'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Role',
        'apiUpdate',
        'PUT',
        '/api/v1/role'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Role',
        'apiDelete',
        'DELETE',
        '/api/v1/role'
    );

-- GROUP -----------------------------------------------------------------------------------------------
INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Group',
        'apiFetchAll',
        'GET',
        '/api/v1/group'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Group',
        'apiTree',
        'GET',
        '/api/v1/group/tree'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Group',
        'apiFindById',
        'GET',
        '/api/v1/group/i:id'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Group',
        'apiCreate',
        'POST',
        '/api/v1/group'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Group',
        'apiUpdate',
        'PUT',
        '/api/v1/group'
    );

INSERT INTO
    tbl_routers(auth, namespace, function_name, method, path)
VALUES
    (
        1,
        'V1_Group',
        'apiDelete',
        'DELETE',
        '/api/v1/group'
    );