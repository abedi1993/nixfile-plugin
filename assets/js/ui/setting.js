import {nixfileAjaxData} from "../utils/ajaxData.js";
import {post, wpRestPost} from "../utils/fetch.js";
import {showOnNavbar} from "../actions/showOnNavbar.js";

export function setting() {
    jQuery(function ($) {
        const nixfileSettingToggler = $("#nixfile-setting-toggler");
        const nixfileStoreTokenBtn = $("#nixfile_store_token");
        const nixfileSettingSection = $(".nixfile-setting");
        nixfileSettingToggler
            .on('click', (e) => {
                e.preventDefault();
                nixfileSettingSection.stop().slideToggle();
            });
        nixfileStoreTokenBtn
            .on("click", async (e) => {
                const token = $("input[name=nixfile_store_token]").val();
                await wpRestPost(nixfileAjaxData.rest_url + nixfileAjaxData.action.token, {token});
                location.reload();
            });
        showOnNavbar();
    });
}