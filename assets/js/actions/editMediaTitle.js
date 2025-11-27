import {link} from "../__apiRoutes.js";
import {post} from "../utils/fetch.js";
import {fetchFileManagerData} from "../utils/fetchFileManagerData.js";
import {getToken} from "../utils/getToken.js";

export function editMediaTitle() {
    jQuery(($) => {
        const editFileNameBtn = $("#nixfile-edit-file-name");
        const input = $("#nixfile-file-edit-form-input");
        const editNameFormContainer = $(".nixfile-file-edit-name-form-container");
        const fileContextMenu = $(".nixfile-file-contextmenu");

        editFileNameBtn.off('click').on("click", () => {
            const itemData = fileContextMenu.attr("data-item");
            if (!itemData) return;



            const item = JSON.parse(itemData);
            editNameFormContainer.fadeIn(() => {
                input.attr("placeholder", item.title); 
                input.val("").focus();
            });
        });

        editNameFormContainer.off('click').on("click", function () {
            $(this).fadeOut();
        });

        editNameFormContainer.find("form")
            .off('click')
            .on("click", (e) => e.stopPropagation());

        
        let inputValue = "";
        input.on("input", function () {
            inputValue = $(this).val();
        });

        editNameFormContainer.find("form")
            .off('submit')
            .on("submit", async function (e) {
                e.preventDefault();

                const itemData = fileContextMenu.attr("data-item");
                if (!itemData) return;

                const item = JSON.parse(itemData);
                const formData = new FormData();

                
                formData.append("title", inputValue);
                formData.append("_method", "PUT");

                await post(
                    `${link(2)}/domain/file-manager/${getToken}/media-title/${item.id}`,
                    formData
                );

                await fetchFileManagerData({
                    force: true,
                    page: 1,
                    folder_id: window.currentFolderId,
                });

                editNameFormContainer.fadeOut();
                this.reset();
            });
    });
}
