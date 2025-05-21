<?php $token = get_option( "nixfile_uploader_token", "" ) ?>
<div class="nixfile-container">
    <div id="nixfile-loader">
        <div class="loader">
            <div class="wrapper">
                <div class="catContainer">
                    <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 733 673"
                            class="catbody"
                    >
                        <path
                                fill="#212121"
                                d="M111.002 139.5C270.502 -24.5001 471.503 2.4997 621.002 139.5C770.501 276.5 768.504 627.5 621.002 649.5C473.5 671.5 246 687.5 111.002 649.5C-23.9964 611.5 -48.4982 303.5 111.002 139.5Z"
                        ></path>
                        <path fill="#212121" d="M184 9L270.603 159H97.3975L184 9Z"></path>
                        <path fill="#212121" d="M541 0L627.603 150H454.397L541 0Z"></path>
                    </svg>
                    <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 158 564"
                            class="tail"
                    >
                        <path
                                fill="#191919"
                                d="M5.97602 76.066C-11.1099 41.6747 12.9018 0 51.3036 0V0C71.5336 0 89.8636 12.2558 97.2565 31.0866C173.697 225.792 180.478 345.852 97.0691 536.666C89.7636 553.378 73.0672 564 54.8273 564V564C16.9427 564 -5.4224 521.149 13.0712 488.085C90.2225 350.15 87.9612 241.089 5.97602 76.066Z"
                        ></path>
                    </svg>
                    <div class="text">
                        <span class="bigzzz">Z</span>
                        <span class="zzz">Z</span>
                    </div>
                </div>
                <div class="wallContainer">
                    <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 500 126"
                            class="wall"
                    >
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="3"
                                x2="450"
                                y1="3"
                                x1="50"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="85"
                                x2="400"
                                y1="85"
                                x1="100"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="122"
                                x2="375"
                                y1="122"
                                x1="125"
                        ></line>
                        <line stroke-width="6" stroke="#7C7C7C" y2="43" x2="500" y1="43"></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="1.99391"
                                x2="115.5"
                                y1="43.0061"
                                x1="115.5"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="2.00002"
                                x2="189"
                                y1="43.0122"
                                x1="189"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="2.00612"
                                x2="262.5"
                                y1="43.0183"
                                x1="262.5"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="2.01222"
                                x2="336"
                                y1="43.0244"
                                x1="336"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="2.01833"
                                x2="409.5"
                                y1="43.0305"
                                x1="409.5"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="43"
                                x2="153"
                                y1="84.0122"
                                x1="153"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="43"
                                x2="228"
                                y1="84.0122"
                                x1="228"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="43"
                                x2="303"
                                y1="84.0122"
                                x1="303"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="43"
                                x2="378"
                                y1="84.0122"
                                x1="378"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="84"
                                x2="192"
                                y1="125.012"
                                x1="192"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="84"
                                x2="267"
                                y1="125.012"
                                x1="267"
                        ></line>
                        <line
                                stroke-width="6"
                                stroke="#7C7C7C"
                                y2="84"
                                x2="342"
                                y1="125.012"
                                x1="342"
                        ></line>
                    </svg>
                </div>
            </div>
        </div>
    </div>

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
            <input type="file" multiple>
            <p>
                حداکثر اندازه پرونده برای بارگذاری: 8 مگابایت.
            </p>
        </div>
    </label>

    <div class="nixfile-filter-bar">
        <div class="nixfile-media-tools">
            <select name="type" id="nixfile-file-type">
                <option value="null">همه موارد رسانه ای</option>
            </select>
            <select name="date" id="nixfile-file-date">
                <option value="null">همه تاریخ ها</option>
            </select>
            <!--<select name="mims" id="nixfile-file-mims"></select>-->
            <button id="nixfile-multi-select">انتخاب دسته‌جمعی</button>
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
        <div class="nixfile-multi-select-tools">
            <button id="nixfile-multi-select-delete" class="disabled">حذف برای همیشه</button>
            <button id="nixfile-multi-select-cancel">لغو</button>
        </div>
    </div>

    <div class="nixfile-setting">
        <div class="nixfile-statistic">
            <div class="nixfile-capacity">
                <div>
                    <p>در حال بارگذاری...</p>
                </div>
                <p>حجم استفاده شده</p>
            </div>
            <div class="nixfile-expired">
                <div>
                    <p>در حال بارگذاری...</p>
                </div>
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

    <div class="nixfile-media-section-container">
        <div class="nixfile-media-section"></div>
    </div>

    <div class="nixfile-errors-box"></div>

    <div class="nixfile-folder-form-container">
        <form class="nixfile-folder-form" method="POST">
            <img src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/images/add.svg' ?>" alt="">
            <label>
                <span>
                    نام پوشه
                </span>
                <input placeholder="مثلا: نمونه کار" type="text" name="title">
            </label>
            <button type="submit">ثبت</button>
        </form>
    </div>

    <div class="nixfile-folder-contextmenu">
        <ul>
            <li id="nixfile-edit-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                          d="m10 21l4-4h8v4H10Zm-6-2h1.4l8.625-8.625l-1.4-1.4L4 17.6V19ZM18.3 8.925l-4.25-4.2l1.4-1.4q.575-.575 1.413-.575t1.412.575l1.4 1.4q.575.575.6 1.388t-.55 1.387L18.3 8.925ZM16.85 10.4L6.25 21H2v-4.25l10.6-10.6l4.25 4.25Zm-3.525-.725l-.7-.7l1.4 1.4l-.7-.7Z"/>
                </svg>
                <span>ویرایش نام</span>
            </li>
            <li id="nixfile-delete-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                          d="M7.616 20q-.672 0-1.144-.472T6 18.385V6H5V5h4v-.77h6V5h4v1h-1v12.385q0 .69-.462 1.153T16.384 20zM17 6H7v12.385q0 .269.173.442t.443.173h8.769q.23 0 .423-.192t.192-.424zM9.808 17h1V8h-1zm3.384 0h1V8h-1zM7 6v13z"/>
                </svg>
                <span>حـذف پوشه</span>
            </li>
            <li id="nixfile-move-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                          d="m7 21l-1.4-1.4l1.575-1.65q-2.65-.3-4.413-2.287T1 11q0-2.925 2.038-4.962T8 4h3v2H8Q5.925 6 4.463 7.463T3 11q0 1.8 1.15 3.175T7.075 15.9L5.6 14.425L7 13l4 4zm6-1v-7h9v7zm0-9V4h9v7zm2-2h5V6h-5z"/>
                </svg>
                <span>انتقال پوشه</span>
            </li>
            <li id="nixfile-detail-folder">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                          d="M8 12v-2h8v2zm0-4V6h8v2zm-2 6h8.975L18 17.95V4H6zm0 6h11.05L14 16H6zm14 2H4V2h16zM6 20V4zm0-4v-2z"/>
                </svg>
                <span>جزپیات پوشه</span>
            </li>
        </ul>
    </div>

    <div class="nixfile-file-contextmenu">
        <ul>
            <li id="nixfile-copy-file">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                          d="M9 18q-.825 0-1.412-.587T7 16V4q0-.825.588-1.412T9 2h9q.825 0 1.413.588T20 4v12q0 .825-.587 1.413T18 18zm0-2h9V4H9zm-4 6q-.825 0-1.412-.587T3 20V6h2v14h11v2zm4-6V4z"/>
                </svg>
                <span>کپی لینک</span>
            </li>
            <li id="nixfile-edit-file-name">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                          d="M5 19h1.425L16.2 9.225L14.775 7.8L5 17.575zm-2 2v-4.25L16.2 3.575q.3-.275.663-.425t.762-.15t.775.15t.65.45L20.425 5q.3.275.438.65T21 6.4q0 .4-.137.763t-.438.662L7.25 21zM19 6.4L17.6 5zm-3.525 2.125l-.7-.725L16.2 9.225z"/>
                </svg>
                <span>ویرایش نام</span>
            </li>
            <li id="nixfile-delete-file">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888" d="M5 21V6H4V4h5V3h6v1h5v2h-1v15zm2-2h10V6H7zm2-2h2V8H9zm4 0h2V8h-2zM7 6v13z"/>
                </svg>
                <span>حذف فایل</span>
            </li>
            <li id="nixfile-move-file">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="#888888"
                          d="m12.2 14l-1.625 1.625l1.4 1.4L16 13l-4.025-4.025l-1.4 1.4L12.2 12H8v2zM4 20q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4h6l2 2h8q.825 0 1.413.588T22 8v10q0 .825-.587 1.413T20 20zm0-2h16V8h-8.825l-2-2H4zm0 0V6z"/>
                </svg>
                <span>انتقال فایل</span>
            </li>
            <li id="nixfile-replace-file">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <g fill="none" stroke="#888888" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="M14 4a2 2 0 0 1 2-2m0 8a2 2 0 0 1-2-2m6-6a2 2 0 0 1 2 2m0 4a2 2 0 0 1-2 2M3 7l3 3l3-3"/>
                        <path d="M6 10V5a3 3 0 0 1 3-3h1"/>
                        <rect width="8" height="8" x="2" y="14" rx="2"/>
                    </g>
                </svg>
                <span>جایگزین فایل</span>
            </li>
            <li id="nixfile-detail-file">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 36 36">
                    <path fill="#888888"
                          d="M25.39 25.45a1 1 0 0 0-1.38.29c-1.41 2.16-4 4.81-6.31 5.7s-4.12.57-4.84 0c-.31-.27-1.12-1-.43-3.49c.46-1.66 3.32-9.48 4-11.38l-2.18.28c-.69 1.86-3.29 8.84-3.76 10.58c-.68 2.49-.34 4.3 1.09 5.56A5.6 5.6 0 0 0 15 34a9.5 9.5 0 0 0 3.45-.7c2.79-1.09 5.72-4.12 7.26-6.47a1 1 0 0 0-.32-1.38"
                          class="clr-i-outline clr-i-outline-path-1"/>
                    <path fill="#888888"
                          d="M19.3 11a4.5 4.5 0 1 0-4.5-4.5a4.5 4.5 0 0 0 4.5 4.5m0-7a2.5 2.5 0 1 1-2.5 2.5A2.5 2.5 0 0 1 19.3 4"
                          class="clr-i-outline clr-i-outline-path-2"/>
                    <path fill="#888888"
                          d="M11.81 15c.06 0 6.27-.82 7.73-1c.65-.1 1.14 0 1.3.15s.21.8-.07 1.68c-.61 1.86-3.69 11-4.59 13.71a8 8 0 0 0 1.29-.38a7.3 7.3 0 0 0 1.15-.6c1.23-3.56 3.53-10.46 4.05-12.04s.39-2.78-.3-3.6a3.16 3.16 0 0 0-3.08-.83c-1.43.15-7.47.94-7.73 1a1 1 0 0 0 .26 2Z"
                          class="clr-i-outline clr-i-outline-path-3"/>
                    <path fill="none" d="M0 0h36v36H0z"/>
                </svg>
                <span>جزئیات فایل</span>
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
                <img class="nixfile-size-12"
                     src="<?php echo plugin_dir_url( __DIR__ ) . 'assets/images/transfer.svg' ?>"
                     alt="alt">
            </div>
            <button class="nixfile-create-new-folder">ساخت پوشه جدید</button>
            <form class="nixfile-create-new-folder-form">
                <label>
                    <input type="text" placeholder="مثلا: نمونه کار">
                </label>
                <button type="submit"> ایجاد</button>
            </form>
            <div class="nixfile-folder-move-folder-name">نام پوشه</div>
            <div class="nixfile-divider"></div>
            <div class="nixfile-footer">
                <button id="nixfile-submit-move-folder" class="nixfile-blue-button">انتقال پوشه</button>
                <button class="nixfile-cancel-button">انصراف</button>
            </div>
        </div>
    </div>
    <div class="nixfile-file-edit-name-form-container">
        <form class="nixfile-file-edit-form" method="POST">
            <label>
                <span>نام فایل</span>
                <input name="title" id="nixfile-file-edit-form-input" type="text" placeholder="نام فایل">
            </label>
            <button type="submit">ویرایش</button>
        </form>
    </div>

    <div id="nixfile-delete-file-form-container">
        <form id="nixfile-delete-file-form">
            <h1>آیا از حذف شدن این فایل اطمینان دارید ؟</h1>
            <label>
                <input type="text" name="id">
            </label>
            <div>
                <input type="submit" value="حذف">
                <button type="button">انصراف</button>
            </div>
        </form>
    </div>

    <div id="nixfile-replace-file-form-container">
        <form id="nixfile-replace-file-form">
            <h1>فایل جدید را بارگزاری کنید</h1>
            <label class="nixfile-upload-file">
                <input type="file" name="id">
                <span>50%</span>
            </label>
        </form>
    </div>

</div>