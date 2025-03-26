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
    <p>
        خانه
    </p>
    <div class="nixfile-media-section">
    </div>
    <div class="nixfile-errors-box">
    </div>

    <div class="nixfile-folder-form-container">
        <form class="nixfile-folder-form" method="POST">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <path fill="currentColor"
                      d="M20 5h-9.586L8.707 3.293A1 1 0 0 0 8 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V7c0-1.103-.897-2-2-2m-4 9h-3v3h-2v-3H8v-2h3V9h2v3h3z"/>
            </svg>
            <label>
                <span>
                    نام پوشه
                </span>
                <input placeholder="مثلا: نمونه کار" type="text" name="title">
            </label>
            <button>ثبت</button>
        </form>
    </div>
</div>