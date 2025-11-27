import { nixfileAjaxData } from "../utils/ajaxData.js";
import { setupInfiniteScrollObserver } from "../utils/setupInfiniteScrollObserver.js";
import { copyToClipboard } from "../actions/copyToClipboard.js";
import { editMediaTitle } from "../actions/editMediaTitle.js";
import { deleteMedia } from "../actions/deleteMedia.js";
import { mediaDetail } from "../actions/mediaDetail.js";
import { replaceMedia } from "../actions/replaceMedia.js";
import { moveMedia } from "../actions/moveMedia.js";
import { post } from "../utils/fetch.js";
import { link } from "../__apiRoutes.js";
import { getToken } from "../utils/getToken.js";
import { pixelExplode } from "../animate/pixelExplode.js";

export function createMedia(media) {
    if (!media.data.length) return;

    jQuery(async function ($) {
        const nixfileFileContextMenu = $(".nixfile-file-contextmenu");
        const nixfileFolderContextMenu = $(".nixfile-folder-contextmenu");
        const fileManagerSection = $(".nixfile-media-section");
        const nixfileMediaSectionContainer = $(".nixfile-media-section-container");
        let openedMediaId = null;

        media.data.forEach((item, index) => {
            const box = $("<div/>", {
                class: "nixfile-media-box",
                style: `background-image:url(${item.url})`,
                'data-item': JSON.stringify(item),
                'data-id': item.id
            });

            /**************************************
             * CLICK — OPEN DETAIL PANEL
             **************************************/
            box.on('click', function () {

                const currentId = $(this).attr("data-id");

               
                if (openedMediaId === currentId) {
                    $(".nixfile-detail-bar").slideUp(200, function () {
                        $(this).remove();
                        openedMediaId = null; 
                        $(".nixfile-media-box").removeClass("selected");
                        fileManagerSection.css("grid-template-columns", 'repeat(12, 1fr)');
                    });
                    return;
                }

              
                openedMediaId = currentId;

                $(".nixfile-media-box").removeClass("selected");
                $(this).addClass("selected");

                const existedNixfileDetailBar = $(".nixfile-detail-bar");
                if (existedNixfileDetailBar.length > 0) existedNixfileDetailBar.remove();

                const clickedItem = JSON.parse($(this).attr('data-item'));

                const nixfileDetailBar = $("<div/>", { class: "nixfile-detail-bar media-detail" });

                // Close Btn
                const closeBtn = $("<div/>", {
                    class: "nixfile-detail-close",
                    html: "&times;"
                }).on("click", function () {
                    nixfileDetailBar.slideUp(200, function () {
                        $(this).remove();
                        openedMediaId = null;
                        fileManagerSection.css("grid-template-columns", 'repeat(12, 1fr)');
                        $(".nixfile-media-box").removeClass("selected");
                    });
                });

                nixfileDetailBar.append(closeBtn);


                // Preview media
                let mediaEl;
                switch (parseInt(clickedItem.type.int)) {
                    case 0: 
                        
                        mediaEl = $("<img/>", { src: item.url });

                        
                        mediaEl.on('load', function() {
                            const resolutionText = `ابعاد: ${mediaEl[0].naturalWidth} * ${mediaEl[0].naturalHeight} پیکسل`;
                            resolution.text(resolutionText);
                        });
                        break;
                    case 1: mediaEl = $("<video/>", { src: item.url, controls: true }); break;
                    case 2: mediaEl = $("<audio/>", { src: item.url, controls: true }); break;
                    default:
                        const $folderBox = $(`.nixfile-media-box[data-id=${clickedItem.id}]`).find('.nixfile-folder-icon');
                        if ($folderBox.length) {
                            const bgImage = $folderBox.css('background-image') || '';
                            const url = bgImage.replace(/url\((['"])?(.*?)\1\)/gi, '$2');
                            mediaEl = $("<img/>", { src: url });
                        }
                        break;
                }

                const hr = $("<hr/>");
                const name = $("<p/>", { text: clickedItem.title });
                const date = $("<p/>", { text: item.created_at.sh_date });
                
                // Convert size to KB or GB depending on the value
                let sizeText;
                const fileSizeInMB = item.size;
                if (fileSizeInMB < 1) {
                    sizeText = ' حجم: ' + (fileSizeInMB * 1024).toFixed(2) + ' کیلوبایت '; // less than 1MB, show KB
                } else if (fileSizeInMB >= 1 && fileSizeInMB < 1024) {
                    sizeText = ' حجم: ' + fileSizeInMB.toFixed(2) + ' مگابایت '; // show MB if between 1MB and 1GB
                } else {
                    sizeText = ' حجم: ' +(fileSizeInMB / 1024).toFixed(2) + ' گیگابایت '; // more than 1GB, show GB
                }


                const size = $("<p/>", { text: sizeText });
                const resolution = $("<p/>", { text: `ابعاد: ${item.width} * ${item.height} پیکسل ` });
                const copyRight = $("<p/>").html(
                    `آپلود شده در <a target="_blank" href="https://nixfile.com">نیکس فایل</a>`
                );


                // Detail actions container
                const nixfileDetailAction = $("<div/>", { class: "nixfile-detail-actions" });

                // Clipboard input & button
                const input = $("<input/>", { value: clickedItem.url, readonly: true });
                const copyButton = $("<button/>", {
                    html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="#ffffff" d="M15 20H5V7c0-.55-.45-1-1-1s-1 .45-1 1v13c0 1.1.9 2 2 2h10c.55 0 1-.45 1-1s-.45-1-1-1m5-4V4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h9c1.1 0 2-.9 2-2m-2 0H9V4h9z"/>
                        </svg>`
                }).on("click", function () {
                    navigator.clipboard.writeText(input.val()).then(() => {
                        const btn = $(this);
                        btn.css('background-color', 'rgb(0,170,44)');
                        setTimeout(() => { btn.css("background-color", '#666'); btn.css('color', '#fff'); }, 500);
                    });
                });

                // Trash button
                const trashButton = $("<button/>", {
                    html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>`
                }).on("click", async function () {
                    if (!confirm(`آیا می‌خواهید فایل "${clickedItem.title}" حذف شود؟`)) return;

                    const formData = new FormData();
                    formData.append("_method", "DELETE");
                    await post(`${link(2)}/domain/file-manager/${getToken}/media/${clickedItem.id}`, formData);

                    const mediaBox = document.querySelector(`.nixfile-media-box[data-id='${clickedItem.id}']`);
                    if (mediaBox) await pixelExplode(mediaBox);

                    nixfileDetailBar.slideUp(200, () => nixfileDetailBar.remove());
                });

                nixfileDetailAction.append(copyButton).append(trashButton).append(input);

                nixfileDetailBar
                    .append(mediaEl)
                    .append(hr)
                    .append(name)
                    // .append(date)
                    .append(size)
                    .append(resolution)
                    .append(copyRight)
                    .append(nixfileDetailAction);

                nixfileMediaSectionContainer.append(nixfileDetailBar);
                nixfileDetailBar.hide().slideDown();
                fileManagerSection.css("grid-template-columns", 'repeat(8, 1fr)');
            });


            /**************************************
             * RIGHT CLICK — CONTEXT MENU
             **************************************/
            box.on('contextmenu', function (e) {
                e.preventDefault();
                moveMedia();
                nixfileFileContextMenu.stop().slideDown(100);
                if (nixfileFolderContextMenu) nixfileFolderContextMenu.stop().slideUp(100);
                nixfileFileContextMenu.css({ 'position': 'absolute', 'top': e.pageY + 'px', 'left': e.pageX + 'px' });
                nixfileFileContextMenu.attr({ 'data-item': $(this).attr('data-item') });
            });

            /**************************************
             * NON-IMAGE THUMBNAILS
             **************************************/
            if (parseInt(item.type.int) !== 0) {
                const title = $("<p/>", { text: item.title });
                const icon = $("<div/>", { class: "nixfile-folder-icon" });

                switch (parseInt(item.type.int)) {
                    case 1: icon.css('background-image', `url(${nixfileAjaxData.images_url}/formats/mp4.svg)`); break;
                    case 2: icon.css('background-image', `url(${nixfileAjaxData.images_url}/formats/mp3.svg)`); break;
                    case 3: icon.css('background-image', `url(${nixfileAjaxData.images_url}/formats/zip.svg)`); break;
                }

                box.append(icon).append(title);
            }

            fileManagerSection.append(box);

            if (index === media.data.length - 1) {
                setupInfiniteScrollObserver(box);
            }
        });

        $(document).on('click', function () {
            nixfileFolderContextMenu.stop().slideUp(100);
            nixfileFileContextMenu.stop().slideUp(100);
        });

        copyToClipboard();
        editMediaTitle();
        deleteMedia();
        mediaDetail();
        replaceMedia();
    });
}
