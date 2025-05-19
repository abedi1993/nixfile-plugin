import {link} from "../__apiRoutes.js";
import {xhr} from "../utils/fetch.js";
import {getToken} from "../utils/getToken.js";

export function uploadFile(payload) {
    const box = jQuery("<div/>",
        {class: "nixfile-media-box uploading"})
        .append(
            jQuery("<div/>", {class: "nixfile-media-progress"})
        );
    jQuery(".nixfile-media-section").prepend(box);
    const formData = new FormData();
    formData.append("file", payload.file);
    formData.append("folder_id", window.currentFolderId);
    return xhr(`${link(2)}/domain/file-manager/${getToken}/media`, formData, box);
}