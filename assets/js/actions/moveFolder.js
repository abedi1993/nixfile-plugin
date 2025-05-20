import {get, post} from "../utils/fetch.js";
import {link} from "../__apiRoutes.js";
import {getToken} from "../utils/getToken.js";
import {nixfileAjaxData} from "../utils/ajaxData.js";
import {fetchFileManagerData} from "../utils/fetchFileManagerData.js";

export function moveFolder() {
    jQuery(function ($) {
        const container = $("#nixfile-folder-move-container");
        const move = $("#nixfile-move-folder");
        const moveBtn = $("#nixfile-submit-move-folder");
        const context = $(".nixfile-folder-contextmenu");
        const cancelMoveFolderModalBtn = $(".nixfile-cancel-button");

        let selected;

        container.on('click', function (e) {
            $(this).fadeOut();
        });
        container
            .find('.nixfile-folder-move-content')
            .on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
            });

        move.on("click", async function (e) {
            container.fadeIn();
            const divider = container.find('.nixfile-divider');
            divider.empty();
            const response = await get(`${link(2)}/domain/file-manager/${getToken}/move-list`);
            const folderList = response.data;
            divider.append(createFolder({
                id: folderList.id,
                title: folderList.title,
                children: [],
            }))
            if (folderList.children && folderList.children.length > 0) {
                folderList.children.forEach(function (folder) {
                    divider.append(createFolder(folder));
                });
            }
        });
        cancelMoveFolderModalBtn
            .on('click', function (e) {
                console.log("12321");
                container.fadeOut();
                selected = null;
            });
        moveBtn.on('click', async function () {
            const item = JSON.parse(context.attr("data-item"))
            if (!selected) return;
            const formData = new FormData();
            formData.append('folder_id', item.id);
            formData.append('parent_id', selected);
            formData.append('_method', 'PUT');
            await post(`${link(2)}/domain/file-manager/${getToken}/move-folder/`, formData);
            await fetchFileManagerData({
                folder_id: window.currentFolderId,
                force: true,
                page: 1,
            });
            selected = null;
            container.fadeOut();
        })

        function createFolder(folder) {
            const selectedId = JSON.parse(context.attr('data-item')).id;
            const container = $("<div/>", {
                class: `${folder.children.length > 0 ? 'nixfile-folder-item-dropdown-container' : 'nixfile-move-folder-container'}`,
            })
                .attr('data-id', folder.id)
                .on('click', function (e) {
                    e.stopPropagation();
                    const id = $(this).attr('data-id');
                    if (selected === id) selected = null;
                    else selected = id;
                    const divider = $('.nixfile-divider');
                    divider.find('.selected').not(this).removeClass('selected');
                    divider.find(`div[data-id=${id}]`).toggleClass('selected');
                });
            const item = $("<div/>", {
                class: "nixfile-folder-item"
            });
            const icon = $("<span/>", {
                class: "nixfile-folder-move-icon",
            })
                .css("background-image", `url(${nixfileAjaxData.images_url}/folder.png)`);
            const text = $("<p/>", {
                text: folder.title
            });
            container.append(item);
            item.append(icon).append(text);
            if (folder.children && folder.children.length > 0) {
                const dropDownSvg = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="nixfile-size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>`;
                const dropDownTrigger = $("<button/>", {
                    html: dropDownSvg,
                })
                    .on('click', function (e) {
                        e.stopPropagation();
                        $(`.nixfile-divider div[parent-id=${folder.id}]`).stop().slideToggle()
                        $(this).toggleClass('active')
                    });
                dropDownTrigger.attr('data-id', folder.id);
                item.append(dropDownTrigger);
                folder.children.forEach(async (subFolder) => {
                    if (subFolder.id !== selectedId) {
                        await container.append(createFolder(subFolder).attr('parent-id', folder.id).hide());
                    }
                });
            }
            return container;
        }

    })
}