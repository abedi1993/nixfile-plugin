import { link } from "../__apiRoutes.js";
import { getToken } from "../utils/getToken.js";
import { xhrReplaceMedia } from "../utils/fetch.js"; 

export function replaceMedia() {
    jQuery(function ($) {
        const nixfileReplaceFileTrigger = $("#nixfile-replace-file");
        const nixfileReplaceFormContainer = $("#nixfile-replace-file-form-container");
        const nixfileReplaceForm = $("#nixfile-replace-file-form");
        const nixfileFileContextMenu = $(".nixfile-file-contextmenu");

        nixfileReplaceFileTrigger.on('click', function () {
            nixfileReplaceFormContainer.fadeIn();
        });

        nixfileReplaceFormContainer.on('click', function () {
            $(this).fadeOut();
        });

        nixfileReplaceForm
            .on('click', function (e) {
                e.stopPropagation();
            })
            .find('input[type=file]')
            .on('change', function (e) {
                const item = JSON.parse(nixfileFileContextMenu.attr('data-item'));
                const [file] = e.target.files;
                const formData = new FormData();
                formData.append("_method", 'PUT');  
                formData.append("file", file);

                const box = $(`.nixfile-media-box[data-id='${item.id}']`);

                const progressBar = jQuery("<div/>", { class: "nixfile-media-progress" });
                box.append(progressBar);  

                
                nixfileReplaceFormContainer.fadeOut();

                xhrReplaceMedia(`${link(2)}/domain/file-manager/${getToken}/replace/${item.id}`, formData, box);
            });
    });
}
