import {fetchFileManagerData} from "../utils/fetchFileManagerData.js";
import {nixfileAjaxData} from "../utils/ajaxData.js";

export function createFolder(folders) {
    jQuery(async function ($) {
        if (!folders.length) return;
        const fileManagerSection = $(".nixfile-media-section");
        folders.forEach(function (item) {
            const box = $("<div/>", {
                class: "nixfile-folder"
            })
                .attr({
                    'data-item': JSON.stringify(item),
                    'data-open': false,
                })
                .on('click', async function () {
                    const clickedItem = JSON.parse($(this).attr('data-item'));
                    if (window.currentFolderId === clickedItem.id) {
                        window.currentFolderId = clickedItem.parent_id;
                    } else {
                        window.currentFolderId = clickedItem.id;
                    }
                    await fetchFileManagerData({
                        folder_id: window.currentFolderId,
                        force: true
                    });
                });
            const icon = $("<div/>", {
                class: 'nixfile-folder-icon'
            })
                .css("background-image", `url(${nixfileAjaxData.images_url}/folder.png)`);
            const title = $("<p/>", {
                class: 'nixfile-folder-title',
                text: item.title
            });
            console.log(item.id, window.currentFolderId);
            if (item.id === window.currentFolderId) {
                icon.css("background-image", `url(${nixfileAjaxData.images_url}/back.png)`);
                box.attr('data-open', true);
                box.css('order', '-1')
            }
            box.append(icon).append(title);
            fileManagerSection.prepend(box);
        });
    });
}