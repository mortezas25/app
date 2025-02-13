<?php
/*
Plugin Name: Attendance Table Plugin
Description: نمایش داده‌های حضور و غیاب به صورت جدول‌های گروه‌بندی‌شده بر اساس کلاس و جدیدترین حضورها به صورت اسلایدشو.
Version: 2.0
Author: شما
*/

// بارگذاری Swiper.js و استایل‌ها
function enqueue_attendance_assets() {
    // Swiper CSS
    wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper@8/swiper-bundle.min.css');
        wp_enqueue_script('jquery');

    // Swiper JS
    wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper@8/swiper-bundle.min.js', array(), '8.4.5', true);
    
    // استایل‌های سفارشی
    wp_enqueue_style('attendance-styles', plugins_url('attendance-styles.css', __FILE__));
    
    // فعال‌سازی Swiper
wp_add_inline_script('swiper-js', '
    document.addEventListener("DOMContentLoaded", function() {
        // فعال‌سازی اسلایدشو خلاصه کلاس‌ها
        new Swiper(".summary-slider", {
            loop: true,
            slidesPerView: "auto",
            centeredSlides: true,
            spaceBetween: 20,
            pagination: { el: ".summary-slider .swiper-pagination", clickable: true },
            autoplay: { delay: 5000 },
            breakpoints: {
                320: { slidesPerView: 1 },
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });
    });
');

wp_add_inline_script('swiper-js', '
    document.addEventListener("DOMContentLoaded", function() {
        // ✅ تنظیمات بهبود یافته برای نمایش همزمان چند اسلاید
        new Swiper(".latest-attendance", {
            loop: true,
            slidesPerView: "auto", // نمایش خودکار بر اساس فضای موجود
            centeredSlides: true, // متمرکز کردن اسلاید فعال
            spaceBetween: 20,
            pagination: { 
                el: ".latest-attendance .swiper-pagination",
                clickable: true 
            },
            autoplay: { 
                delay: 5000,
                disableOnInteraction: false 
            },
            breakpoints: {
                320: { slidesPerView: 1 }, // موبایل: ۱ اسلاید
                768: { slidesPerView: 3 }, // تبلت: ۳ اسلاید
                1024: { slidesPerView: 4 } // دسکتاپ: ۴ اسلاید
            }
        });
    });
');


}
add_action('wp_enqueue_scripts', 'enqueue_attendance_assets');

// توابع اصلی
require_once plugin_dir_path(__FILE__) . 'attendance-functions.php';



wp_add_inline_script('swiper-js', '
    jQuery(document).ready(function($) {
        $(".class-accordion").on("click", ".accordion-toggle", function() {
            $(this).closest(".accordion-item").toggleClass("active");
            $(this).find(".toggle-icon").toggleClass("rotated");
        });
    });
');