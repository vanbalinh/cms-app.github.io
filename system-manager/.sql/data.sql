-- Tbl Role
-- 1000000000
INSERT INTO
    tbl_role(code, name)
VALUES
    ('SYSTEM_ADMINISTRATOR', 'Quản trị hệ thống');

-- 1000000001
INSERT INTO
    tbl_role(code, name)
VALUES
    ('ADMINISTRATOR', 'Quản trị viên');

-- 1000000002
INSERT INTO
    tbl_role(code, name)
VALUES
    ('USER', 'Người dùng');

-- 1000000003
INSERT INTO
    tbl_role(code, name)
VALUES
    ('GUEST', 'Khách');

-- Tbl Account
INSERT INTO
    tbl_accounts(
        username,
        password,
        first_name,
        last_name,
        email,
        role_id
    )
VALUES
    (
        'administrator',
        '10b8e822d03fb4fd946188e852a4c3e2',
        'Admin',
        'System',
        'vanbalinh95@gmail.com',
        1000000000
    );

INSERT INTO
    tbl_accounts(
        username,
        password,
        first_name,
        last_name,
        email,
        role_id
    )
VALUES
    (
        'vbl1',
        '10b8e822d03fb4fd946188e852a4c3e2',
        'Văn',
        'Bá Linh1',
        'vbl1@gmail.com',
        1000000003
    );

-- Permission
INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        null,
        "LAYOUT",
        "Giao diện"
    );

INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        1000000000,
        "SYSTEM",
        "Hệ thống"
    );

INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        1000000001,
        "NGUOIDUNG",
        "Người dùng"
    );

INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        1000000001,
        "NHOMNGUOIDUNG",
        "Nhóm người dùng"
    );

INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        1000000001,
        "QUYEN",
        "Quyền"
    );

INSERT INTO
    tbl_permission(parent_id, code, name, icon_class, sort)
VALUES
    (
        1000000004,
        "QUYEN_THEMMOI",
        "Thêm mới",
        "fa fa-plus",
        1
    );

INSERT INTO
    tbl_permission(parent_id, code, name, icon_class, sort)
VALUES
    (
        1000000004,
        "QUYEN_CAPNHAT",
        "Cập nhật",
        "fa fa-pencil-square-o",
        2
    );

INSERT INTO
    tbl_permission(parent_id, code, name, icon_class, sort)
VALUES
    (
        1000000004,
        "QUYEN_XOA",
        "Xoá",
        "fa fa-trash",
        3
    );

INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        1000000001,
        "MENU",
        "Menu"
    );

INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        1000000001,
        "VAITRO",
        "Vai trò"
    );

INSERT INTO
    tbl_permission(parent_id, code, name)
VALUES
    (
        null,
        "API",
        "API"
    );

-- Menu
INSERT INTO
    tbl_menu(
        parent_id,
        name,
        url,
        sort,
        permission_id,
        page_title
    )
VALUES
    (
        null,
        "Hệ thống",
        "/he-thong.html",
        0,
        1000000000,
        "Hệ thống"
    );

INSERT INTO
    tbl_menu(
        parent_id,
        name,
        url,
        sort,
        permission_id,
        page_title
    )
VALUES
    (
        1000000000,
        "Người dùng",
        "/he-thong/nguoi-dung.html",
        1,
        1000000001,
        "Quản lý người dùng"
    );

INSERT INTO
    tbl_menu(
        parent_id,
        name,
        url,
        sort,
        permission_id,
        page_title
    )
VALUES
    (
        1000000000,
        "Nhóm người dùng",
        "/he-thong/nhom-nguoi-dung.html",
        2,
        1000000002,
        "Quản lý nhóm người dùng"
    );

INSERT INTO
    tbl_menu(
        parent_id,
        name,
        url,
        sort,
        permission_id,
        page_title
    )
VALUES
    (
        1000000000,
        "Quyền",
        "/he-thong/quyen.html",
        3,
        1000000003,
        "Quản lý quyền"
    );

INSERT INTO
    tbl_menu(
        parent_id,
        name,
        url,
        sort,
        permission_id,
        page_title
    )
VALUES
    (
        1000000000,
        "Menu",
        "/he-thong/menu.html",
        4,
        1000000007,
        "Quản lý menu"
    );

INSERT INTO
    tbl_menu(
        parent_id,
        name,
        url,
        sort,
        permission_id,
        page_title
    )
VALUES
    (
        1000000000,
        "Vai trò",
        "/he-thong/vai-tro.html",
        4,
        1000000008,
        "Quản lý vai trò"
    );

-- Tbl Config
INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('DATE_FORMAT', 'DD/MM/YYYY');

INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('DATE_TIME_FORMAT', 'DD/MM/YYYY HH:mm:ss');

INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('FACEBOOK_APP_ID', '5994268787310848');

INSERT INTO
    tbl_system_config(name, value)
VALUES
    (
        'FACEBOOK_APP_SECRET',
        'a035877b82e6def4d980750a3421648b'
    );

INSERT INTO
    tbl_system_config(name, value)
VALUES
    (
        'FACEBOOK_APP_CALLBACK',
        'http://localhost/api/auth/login/facebook'
    );

INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('FACEBOOK_APP_DEFAULT_GRAPH_VERSION', 'v2.9');

INSERT INTO
    tbl_system_config(name, value)
VALUES
    (
        'GOOGLE_APP_ID',
        '371205064724-nakqskace828vuk31hc9ebo2ospk2ir0.apps.googleusercontent.com'
    );

INSERT INTO
    tbl_system_config(name, value)
VALUES
    (
        'GOOGLE_APP_SECRET',
        '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'
    );

INSERT INTO
    tbl_system_config(name, value)
VALUES
    (
        'GOOGLE_APP_CALLBACK',
        'http://localhost/oauth/sso/google'
    );

INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('ZALO_APP_ID', '2333623311556925320');

INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('ZALO_APP_SECRET', 'ZTBjUB1FiCJs1FraKFD0');

INSERT INTO
    tbl_system_config(name, value)
VALUES
    (
        'ZALO_APP_CALLBACK',
        'http://localhost/oauth/sso/zalo'
    );

INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('ADMINISTRATIVE_UNITS', '1669185733');

INSERT INTO
    tbl_system_config(name, value, description)
VALUES
    (
        'PERMISSION_FORM_FIELD_DEFAULT_ALLOW_ACCESS',
        '1',
        'Mặc định cho phép cấp quyền truy cập cho form field'
    );

INSERT INTO
    tbl_system_config(name, value, description)
VALUES
    (
        'DEFAULT_AVATAR',
        '/document/v1/avatar-default.png?code=1685331383&version=1',
        'ĐƯờng dẫn avatar mặc định'
    );

INSERT INTO
    tbl_system_config(name, value)
VALUES
    ('ROLE_GUEST_ID', '1000000003');

-- Translate
INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_unknown_error',
        'vi',
        'Lỗi không xác định'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_unknown_error',
        'en',
        'Unknown error'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_success',
        'vi',
        'Thành công'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_success',
        'en',
        'Success'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_notfound',
        'vi',
        'Không tìm thấy'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_success',
        'en',
        'Not Found'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_create_fail',
        'vi',
        'Thêm mới lỗi'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_create_fail',
        'en',
        'Create fail'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_update_fail',
        'vi',
        'Cập nhật lỗi'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_update_fail',
        'en',
        'Update fail'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_login_success',
        'vi',
        'Đăng nhập thành công'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_login_success',
        'en',
        'Login success'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_login_fail',
        'vi',
        'Đăng nhập thất bại'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_msg_login_fail',
        'en',
        'Login fail'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_400_invalid_data',
        'vi',
        'Dữ liệu không hợp lệ'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_400_invalid_data',
        'en',
        'Invalid data'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_400_unique',
        'vi',
        'Trường dữ liệu duy nhất'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_400_unique',
        'en',
        'Field Unique'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_400_required',
        'vi',
        'Trường dữ liệu bắt buộc'
    );

INSERT INTO
    tbl_translate(code, locale, translate)
VALUES
    (
        'api_400_required',
        'en',
        'Field required'
    );