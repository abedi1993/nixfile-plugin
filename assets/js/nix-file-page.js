jQuery(function ($) {
    const nixfileCloseBtn = $("#nixfile-close-btn");
    const nixfileOpenerBtn = $("#nixfile-uploader-opener");
    const nixfileUploaderSection = $(".nixfile-uploader");
    const nixfileSettingToggler = $("#nixfile-setting-toggler");
    const nixfileSettingSection = $(".nixfile-setting")
    nixfileUploaderSection.hide();
    nixfileSettingSection.hide();
    nixfileOpenerBtn.on("click", (e) => {
        e.preventDefault();
        nixfileUploaderSection.stop().slideToggle()
    });
    nixfileCloseBtn.on("click", (e) => {
        e.preventDefault();
        nixfileUploaderSection.stop().slideUp();
    });
    nixfileSettingToggler.on('click', (e) => {
        e.preventDefault();
        nixfileSettingSection.stop().slideToggle();
    })
});