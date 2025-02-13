<?php
// تابع fetch_latest_attendance
function fetch_latest_attendance() {
    global $wpdb;
    $table = $wpdb->prefix . 'attendance_data';
    
    // ✅ تغییر کوئری برای دریافت ۱۰ رکورد آخر
    $results = $wpdb->get_results("
        SELECT * 
        FROM $table 
        ORDER BY id DESC 
        LIMIT 10
    ", ARRAY_A);

    if(empty($results)) return '<p class="no-data">داده‌ای موجود نیست</p>';

    $html = '<div class="attendance-slider">
        <h3>۱۰ حضور اخیر</h3> <!-- ✅ تغییر عنوان -->
        <div class="swiper latest-attendance">
            <div class="swiper-wrapper">';

    foreach($results as $row) {
        $status = ($row['attendance_status'] == 'حاضر') ? 'present' : 'absent';
        $time = $row['timecart'] ?? '---';
        $html .= <<<HTML
        <div class="swiper-slide $status">
            <div class="attendance-card">
                <p><span>نام:</span> {$row['persian_name']}</p>
                <p><span>کلاس:</span> {$row['user_class']}</p>
                <p><span>وضعیت:</span> {$row['attendance_status']}</p>
                <p><span>زمان:</span> $time</p>
            </div>
        </div>
HTML;
    }

    $html .= '</div>
        <div class="swiper-pagination"></div>
        </div>
        </div>';
        
    return $html;
}

// محاسبه آمار
function calculate_stats() {
    global $wpdb;
    $table = $wpdb->prefix . 'attendance_data';
    
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $present = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE attendance_status = 'حاضر'");
    $absent = $total - $present;

    return <<<HTML
    <div class="stats-box">
        <h3>آمار کلی</h3>
        <div class="stats-grid">
            <div class="stat-item total">
                <span>کل دانش‌آموزان</span>
                <strong>$total</strong>
            </div>
            <div class="stat-item present">
                <span>حاضرین</span>
                <strong>$present</strong>
            </div>
            <div class="stat-item absent">
                <span>غایبین</span>
                <strong>$absent</strong>
            </div>
        </div>
    </div>
HTML;
}

// نمایش جدول کلاس‌ها
function fetch_class_data() {
    global $wpdb;
    $table = $wpdb->prefix . 'attendance_data';
    $results = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);

    if(empty($results)) return '<p class="no-data">داده‌ای موجود نیست</p>';

    $grouped = [];
    foreach($results as $row) {
        $grouped[$row['user_class']][] = $row;
    }

    $html = '<div class="class-accordion">';
    foreach($grouped as $class => $records) {
        $html .= <<<HTML
        <details class="accordion-item">
            <summary class="accordion-header">
                {$class}
                <span class="toggle-icon">▼</span>
            </summary>
            <div class="accordion-content">
                <table>
                    <tr><th>نام</th><th>وضعیت</th><th>زمان</th></tr>
HTML;

        foreach($records as $row) {
            $status = ($row['attendance_status'] == 'حاضر') ? 'present' : 'absent';
            $time = $row['timecart'] ?? '---';
            $html .= "<tr class='$status'>
                <td>{$row['persian_name']}</td>
                <td>{$row['attendance_status']}</td>
                <td>$time</td>
            </tr>";
        }
        
        $html .= '</table></div></details>';
    }
    $html .= '</div>';
    
    return $html;
}

// شورت‌کد اصلی
function attendance_shortcode() {
    ob_start();
    echo fetch_latest_attendance();
    echo calculate_stats();
        echo fetch_class_summary(); // افزودن این خط
    echo fetch_class_data();
    return ob_get_clean();
}
add_shortcode('attendance_table', 'attendance_shortcode');


// تابع خلاصه کلاس‌ها با اسلایدشو
function fetch_class_summary() {
    global $wpdb;
    $table = $wpdb->prefix . 'attendance_data';
    
    $results = $wpdb->get_results("
        SELECT user_class,
               COUNT(*) AS total,
               SUM(CASE WHEN attendance_status = 'حاضر' THEN 1 ELSE 0 END) AS present,
               SUM(CASE WHEN attendance_status = 'غیاب' THEN 1 ELSE 0 END) AS absent
        FROM $table
        GROUP BY user_class
    ", ARRAY_A);

    if(empty($results)) return '<p class="no-data">داده‌ای موجود نیست</p>';

    $html = '<div class="class-summary">
        <h3>خلاصه وضعیت کلاس‌ها</h3>
        <div class="swiper summary-slider">
            <div class="swiper-wrapper">';

    foreach($results as $class) {
        $html .= <<<HTML
        <div class="swiper-slide">
            <div class="summary-card">
                <h4>کلاس {$class['user_class']}</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <span>کل دانش‌آموزان</span>
                        <strong>{$class['total']}</strong>
                    </div>
                    <div class="stat-item present">
                        <span>حاضرین</span>
                        <strong>{$class['present']}</strong>
                    </div>
                    <div class="stat-item absent">
                        <span>غایبین</span>
                        <strong>{$class['absent']}</strong>
                    </div>
                </div>
            </div>
        </div>
HTML;
    }

    $html .= '</div>
            <div class="swiper-pagination"></div>
        </div>
    </div>';
    
    return $html;
}