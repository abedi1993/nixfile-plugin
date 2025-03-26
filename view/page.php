<?php $token = get_option( "nixfile_uploader_token", "" ) ?>
<div class="nixfile-container">
    <div class="nixfile-media-header">
        <h1>
            کتابخانه رسانه نیکس فایل
        </h1>
        <button id="nixfile-uploader-opener">
            افزودن رسانه جدید
        </button>
        <button id="nixfile-folder-opener">
            افزودن پوشه جدید
        </button>
    </div>
    <label id="nixfile-uploader" class="nixfile-uploader">
        <span type="button" id="nixfile-close-btn"></span>
        <div class="nixfile-uploader-content">
            <h1>
                برای بارگذاری، پرونده‌ها را بکشید
            </h1>
            <p>یا</p>
            <span class="nixfile-button">
                گزینش پرونده‌ها
            </span>
            <input type="file">
            <p>
                حداکثر اندازه پرونده برای بارگذاری: 8 مگابایت.
            </p>
        </div>
    </label>
    <div class="nixfile-filter-bar">
        <div class="nixfile-media-tools">
            <select name="type" id="nixfile-file-type"></select>
            <select name="date" id="nixfile-file-date"></select>
            <select name="mims" id="nixfile-file-mims"></select>
            <button>انتخاب دسته‌جمعی</button>
            <button id="nixfile-setting-toggler">تنظیمات</button>
            <button id="nixfile-buy">خرید و تمدید سرویس</button>
        </div>
        <div class="nixfile-search-box">
            <label>
                <span>جستجوی رسانه</span>
                <input id="nixfile-search-input" type="text">
            </label>
        </div>
    </div>
    <div class="nixfile-setting">
        <div class="nixfile-statistic">
            <div class="nixfile-capacity">
                <div></div>
                <p>حجم استفاده شده</p>
            </div>
            <div class="nixfile-expired">
                <div></div>
                <p>زمان باقی مانده</p>
            </div>
        </div>
        <div class="nixfile-token">
            <label>
                <button id="nixfile_store_token">ثبت</button>
                <input placeholder="توکن" value="<?php echo $token ?>" name="nixfile_store_token" type="text">
            </label>
            <label>
                <button id="nixfile_store_email">ثبت</button>
                <input placeholder="email" name="nixfile_store_email" type="text">
            </label>
        </div>
        <div class="nixfile-option">
            <p>
                <span>بکاپ گیری کامل سایت (روزانه)</span>
                <button>به زودی</button>
            </p>
            <p>
                <span>نمایش مانیتورینگ در نوار وردپرس </span>
                <button>غیر فعال</button>
            </p>
        </div>
        <div class="nixfile-option">
            <p>
                <span>زمان آپلود تصاویر فشرده شود؟</span>
                <button>غیر فعال</button>
            </p>
            <p>
                <span>زمان آپلود فرمت WEBP شود؟</span>
                <button>غیر فعال</button>
            </p>
        </div>
    </div>
    <div class="nixfile-media-section">
    </div>
    <div class="nixfile-errors-box"></div>
</div>