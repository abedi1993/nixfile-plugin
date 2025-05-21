import {post} from "../utils/fetch.js";
import {link} from "../__apiRoutes.js";
import {getToken} from "../utils/getToken.js";
import {fetchFileManagerData} from "../utils/fetchFileManagerData.js";

export function deleteMedia() {
    jQuery(function ($) {
        const nixfileDeleteFile = $("#nixfile-delete-file");
        const nixfileDeleteFileContainer = $("#nixfile-delete-file-form-container");
        const nixfileDeleteFileForm = $("#nixfile-delete-file-form");
        const nixfileFileContextMenu = $(".nixfile-file-contextmenu");

        nixfileDeleteFile.on('click', function (e) {
            nixfileDeleteFileContainer.fadeIn();
        });
        nixfileDeleteFileForm
            .find("button")
            .on('click', function () {
                nixfileDeleteFileContainer.fadeOut();
            });
        nixfileDeleteFileForm
            .find('input[type=submit]')
            .on('click', async function (e) {
                e.preventDefault();
                const item = JSON.parse(nixfileFileContextMenu.attr('data-item'));
                const formData = new FormData();
                formData.append("_method", "DELETE");
                await post(`${link(2)}/domain/file-manager/${getToken}/media/${item.id}`, formData);
                nixfileDeleteFileContainer.fadeOut();
                const media = $(`.nixfile-media-box[data-id='${item.id}']`);
                if (media.length) {
                    media.fadeOut()
                }
            });
    })
}