import {post, wpRestPost} from "../utils/fetch.js";
import {nixfileAjaxData} from "../utils/ajaxData.js";

export function showOnNavbar() {
    jQuery(async function ($) {
        const btn = $("#nixfile-show-on-navbar");
        btn
            .on("click", async function () {
                await wpRestPost(`${nixfileAjaxData.rest_url + nixfileAjaxData.action.show_status_navbar}`)
                location.reload();
            });
    })
}