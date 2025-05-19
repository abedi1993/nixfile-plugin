import {post} from "../utils/fetch.js";
import {link} from "../__apiRoutes.js";
import {getToken} from "../utils/getToken.js";
import {fetchFileManagerData} from "../utils/fetchFileManagerData.js";

export function postFolder() {
    jQuery(function ($) {
        const opener = $("#nixfile-folder-opener");
        const formContainer = $(".nixfile-folder-form-container");
        const form = $(".nixfile-folder-form");

        formContainer.on("click", (e) => {
            $(e.currentTarget).fadeOut();
        });
        opener.on("click", () => {
            formContainer.fadeIn();
            formContainer.find("button").text("ثبت");
            $("#nixfile-edit-folder-name").remove();
            formContainer.find("label input").attr("placeholder", "مثلا: نمونه کار");
        });
        form.on("click", (e) => e.stopPropagation());
        form.on("submit", async function (e) {
            e.preventDefault();
            const formData = new FormData(e.currentTarget);
            formData.append("parent_id", window.currentFolderId);
            try {
                await post(`${link(2)}/domain/file-manager/${getToken}/folder`, formData);
                e.currentTarget.reset();
                formContainer.fadeOut();
                await fetchFileManagerData({
                    folder_id: window.currentFolderId
                })
            } catch (error) {
                console.error("Folder submit failed:", error);
            }
        });
    });
}
