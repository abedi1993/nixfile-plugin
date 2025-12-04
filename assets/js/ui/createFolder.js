import {fetchFileManagerData} from "../utils/fetchFileManagerData.js";
import {nixfileAjaxData} from "../utils/ajaxData.js";
import {editFolderTitle} from "../actions/editFolderTitle.js";
import {deleteFolder} from "../actions/deleteFolder.js";
import {moveFolder} from "../actions/moveFolder.js";
import {folderDetails} from "../actions/folderDetails.js";
import {multiMediaSelect} from "../actions/multiMediaSelect.js";

export function createFolder(folders) {
    jQuery(function ($) {
        const fileManagerSection = $(".nixfile-media-section");

        // if (!folders.length) return fileManagerSection.empty().append('<p class="no-folders">هیچ پوشه‌ای وجود ندارد.</p>');

        const folderElements = folders.map(function (item) {
            const box = $("<div/>", { class: "nixfile-folder" })
                .attr({
                    'data-id': item.id,
                    'data-item': JSON.stringify(item),
                    'data-open': false,
                })
                .on('click', async function () {
                    const clickedItem = JSON.parse($(this).attr('data-item'));
                    window.currentFolderId = (window.currentFolderId === clickedItem.id)
                        ? clickedItem.parent_id
                        : clickedItem.id;

                    await fetchFileManagerData({
                        folder_id: window.currentFolderId,
                        force: true
                    });
                })
                .on('contextmenu', function (e) {
                    e.preventDefault();
                    moveFolder();

                    const nixfileFolderContextMenu = $(".nixfile-folder-contextmenu");
                    nixfileFolderContextMenu.stop().slideDown(100);
                    nixfileFolderContextMenu.css({
                        'position': 'absolute',
                        'top': e.pageY + 'px',
                        'left': e.pageX + 'px'
                    });
                    nixfileFolderContextMenu.attr({
                        'data-id': $(this).attr('data-id'),
                        'data-item': JSON.stringify(item)
                    });
                });

            const icon = $("<div/>", { class: 'nixfile-folder-icon' })
                .css("background-image", `url(${nixfileAjaxData.images_url}/folder.png)`);

            const title = $("<p/>", { class: 'nixfile-folder-title', text: item.title });

            if (item.id === window.currentFolderId) {
                icon.css("background-image", `url(${nixfileAjaxData.images_url}/back.png)`);
                box.attr('data-open', true);
                box.css('order', '-1');
            }

            box.append(icon).append(title);
            fileManagerSection.prepend(box)
            return box;
        });

        editFolderTitle();
        deleteFolder();
        folderDetails();
        multiMediaSelect();

        
        $(document).trigger("nixfile-folder-created");
    });
}
