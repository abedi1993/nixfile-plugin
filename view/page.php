<?php $token = get_option("nixfile_uploader_token", "") ?>
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
            <button id="nixfile-buy">
                <a target="_blank" href="https://nixfile.com">خرید و تمدید سرویس</a>
            </button>
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

    <div id="breadcrumb"></div>

    <div class="nixfile-media-section"></div>

    <div class="nixfile-errors-box"></div>

    <div class="nixfile-folder-form-container">
        <form class="nixfile-folder-form" method="POST">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <path fill="currentColor"
                    d="M20 5h-9.586L8.707 3.293A1 1 0 0 0 8 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V7c0-1.103-.897-2-2-2m-4 9h-3v3h-2v-3H8v-2h3V9h2v3h3z" />
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

    <div class="nixfile-folder-contextmenu">
        <ul>
            <li id="nixfile-edit-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                        d="m10 21l4-4h8v4H10Zm-6-2h1.4l8.625-8.625l-1.4-1.4L4 17.6V19ZM18.3 8.925l-4.25-4.2l1.4-1.4q.575-.575 1.413-.575t1.412.575l1.4 1.4q.575.575.6 1.388t-.55 1.387L18.3 8.925ZM16.85 10.4L6.25 21H2v-4.25l10.6-10.6l4.25 4.25Zm-3.525-.725l-.7-.7l1.4 1.4l-.7-.7Z" />
                </svg>
                <span>ویرایش نام</span>
            </li>
            <li id="nixfile-delete-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                        d="M7.616 20q-.672 0-1.144-.472T6 18.385V6H5V5h4v-.77h6V5h4v1h-1v12.385q0 .69-.462 1.153T16.384 20zM17 6H7v12.385q0 .269.173.442t.443.173h8.769q.23 0 .423-.192t.192-.424zM9.808 17h1V8h-1zm3.384 0h1V8h-1zM7 6v13z" />
                </svg>
                <span>حـذف پوشه</span>
            </li>
            <li id="nixfile-move-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                        d="m7 21l-1.4-1.4l1.575-1.65q-2.65-.3-4.413-2.287T1 11q0-2.925 2.038-4.962T8 4h3v2H8Q5.925 6 4.463 7.463T3 11q0 1.8 1.15 3.175T7.075 15.9L5.6 14.425L7 13l4 4zm6-1v-7h9v7zm0-9V4h9v7zm2-2h5V6h-5z" />
                </svg>
                <span>انتقال پوشه</span>
            </li>
            <li id="nixfile-detail-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                        d="M8 12v-2h8v2zm0-4V6h8v2zm-2 6h8.975L18 17.95V4H6zm0 6h11.05L14 16H6zm14 2H4V2h16zM6 20V4zm0-4v-2z" />
                </svg>
                <span>جزپیات پوشه</span>
            </li>
        </ul>
    </div>

    <div id="nixfile-delete-folder-form-container">
        <form id="nixfile-delete-folder-form">
            <h1>آیا از حذف شدن این فایل اطمینان دارید ؟</h1>
            <label>
                <input type="text" name="id">
            </label>
            <div>
                <input type="submit" , value="حذف">
                <button type="button">انصراف</button>
            </div>
        </form>
    </div>

    <div id="nixfile-folder-move-container">
        <div class="nixfile-folder-move-content">


            <div class="nixfile-folder-mov-header">
                <img class="nixfile-size-12" src="<?php echo plugin_dir_url(__DIR__) . 'assets/images/transfer.svg' ?>"
                    alt="alt">
            </div>


            <div>
                <div class="nixfile-button-modal">ساخت پوشه جدید</div>

                <div class="nixfile-folder-move-folder-name">نام پوشه</div>

                <div class="nixfile-divider">
                    <div class="nixfile-folder-item-dropdown-container">
                        <div class="nixfile-dropdown-item">
                            <div class="nixfile-flex nixfile-items-center nixfile-gap-4">
                                <div class="nixfile-folder-move-icon"></div>
                                <span>نام پوشه تستی 1</span>
                            </div>

                            <button class="nixfile-close-button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="nixfile-size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div class="nixfile-content" style="display: none;">s</div>
                    </div>


                    <div class="nixfile-folder-item">
                        <div class="nixfile-folder-move-icon"></div>
                        <span>نام پوشه تستی 1</span>
                    </div>

                </div>
            </div>

            <div class="nixfile-footer">
                <button class="nixfile-blue-button">انتقال پوشه</button>
                <button class="nixfile-cancel-button">انصراف</button>
            </div>
        </div>
    </div>




</div>