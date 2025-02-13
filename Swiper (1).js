var swiper = new Swiper(".latest-attendance", {
    loop: true,
    centeredSlides: true, // نمایش اسلایدها به صورت مرکزی
    slidesPerView: 3, // نمایش سه اسلاید همزمان
    spaceBetween: 20, // فاصله بین اسلایدها
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    autoplay: {
        delay: 5000, // نمایش هر اسلاید به مدت ۵ ثانیه
    },
    breakpoints: {
        320: { // برای دستگاه‌های کوچک
            slidesPerView: 1,
        },
        768: { // برای تبلت‌ها
            slidesPerView: 2,
        },
        1024: { // برای دسکتاپ
            slidesPerView: 3,
        }
    }
});