import {link} from "../__apiRoutes.js";
import {fetchFileManagerData} from "./fetchFileManagerData.js";

export function get(link) {
    if (window.nixfileMediaRequest) {
        window.nixfileMediaRequest.abort();
    }
    window.nixfileMediaRequest = jQuery.ajax({
        url: link,
        type: "GET",
        headers: {
            Accept: "application/json"
        }
    });

    return window.nixfileMediaRequest.then(res => res);
}

export async function post(link, body) {
    return jQuery.ajax({
        url: link,
        type: "POST",
        data: body,
        processData: false,
        contentType: false,
        headers: {
            Accept: "application/json"
        }
    }).then(res => res.data);
}

export function xhr(url, formData, box) {
    return jQuery.ajax({
        url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        xhr: function () {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    box.find(".nixfile-media-progress").css("width", `${percent}%`);
                }
            });
            return xhr;
        },
        success: async function (response) {
            box.addClass("uploaded");
            const slug = response.data.slug;
            const url = `${link(2)}/private/${slug}`;
            box.css("background-image", `url(${url})`);
            box.html('');
            await fetchFileManagerData({
                folder_id: window.currentFolderId,
                page: 1,
                force: true,
            })
            //getStatistic?.();
        },
        error: function () {
            box.find(".nixfile-media-progress").css("background-color", "red");
            setTimeout(() => box.detach(), 5000);
        }
    });
}
