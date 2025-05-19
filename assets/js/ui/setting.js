import {nixfileAjaxData} from "../utils/ajaxData.js";
import {post} from "../utils/fetch.js";
import {fetchFileManagerData} from "../utils/fetchFileManagerData.js";

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
                console.log("asdas");
                const formData = new FormData();
                const token = $("input[name=nixfile_store_token]").val();
                formData.append('token', token);
                formData.append("action", nixfileAjaxData.action.set_token);
                formData.append("security", nixfileAjaxData.nonce);
                await post(nixfileAjaxData.ajax_url, formData);
                location.reload()
            });
    });
}