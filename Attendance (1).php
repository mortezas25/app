<?php
/*
Plugin Name: Attendance Data Importer
Description: دریافت و ذخیره داده‌های حضور و غیاب از طریق API
Version: 2.1
Author: شما
*/

// ایجاد جدول دیتابیس هنگام فعال‌سازی پلاگین
register_activation_hook(__FILE__, 'create_attendance_table');
function create_attendance_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'attendance_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        persian_name VARCHAR(255) NOT NULL,
        user_class VARCHAR(255) NOT NULL,
        attendance_status VARCHAR(50) NOT NULL,
        timecart TIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// ثبت endpoint API
add_action('rest_api_init', function () {
    register_rest_route('attendance/v1', '/upload', [
        'methods' => 'POST',
        'callback' => 'handle_attendance_upload',
        'permission_callback' => '__return_true'
    ]);
});

// مدیریت آپلود داده‌ها
function handle_attendance_upload(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'attendance_data';
    $data = $request->get_json_params();

    // اعتبارسنجی داده‌ها
    if (empty($data) || !is_array($data)) {
        return new WP_Error('invalid_data', 'داده نامعتبر است', ['status' => 400]);
    }

    // حذف داده‌های قدیمی
    $wpdb->query("TRUNCATE TABLE $table_name");

    // درج داده‌های جدید
    foreach ($data as $record) {
        $wpdb->insert($table_name, [
            'user_id' => sanitize_text_field($record['user_id']),
            'persian_name' => sanitize_text_field($record['persian_name']),
            'user_class' => sanitize_text_field($record['user_class']),
            'attendance_status' => sanitize_text_field($record['attendance_status']),
            'timecart' => sanitize_text_field($record['timecart'])
        ], ['%d', '%s', '%s', '%s', '%s']);
    }

    return ['message' => 'داده با موفقیت ذخیره شد', 'count' => count($data)];
}


// افزودن منوی مدیریت
add_action('admin_menu', 'register_attendance_admin_page');
function register_attendance_admin_page() {
    add_menu_page(
        'مدیریت حضور و غیاب',
        'حضور و غیاب',
        'manage_options',
        'attendance-dashboard',
        'render_attendance_admin_page',
        'dashicons-clipboard',
        30
    );
}

// اتصال فایل admin-dashboard.php
require_once plugin_dir_path(__FILE__) . 'admin-dashboard.php';