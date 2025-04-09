jQuery(document).ready(function ($) {
    const uploaderDir = $("#nixfile-box");
    const nixfileSettingData = nixfile_setting_data;
    $(window).on('load', function () {
        uploaderDir.css("display", 'flex');
    });
    const container = $('<div id="nixfile-box"><strong>NixFileUploader:</strong> This is where your file uploader will go!</div>');
    $('#postdivrich').after(container);
    const formData = new FormData();
    const errorsBox = $(".nixfile-errors-box");
    // const api = "https://api.yekpayamak.com/v1"
    const api = "http://127.0.0.1:7000/v1"
    $("#nixfile-uploader-input").on('change', async function (e) {
        const files = e.target.files;
        if (files.length > 0) {
            for (const file of files) {
                formData.append("file", file);
                formData.append('upload_type', '1');
                formData.append('expired_at', '2');
                formData.append("domain_id", nixfileSettingData.token);
                $.ajax({
                    url: `${api}/upload`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        const slug = res.data.slug;
                        const defaultSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            <path d="M7 9.667A2.667 2.667 0 0 1 9.667 7h8.666A2.667 2.667 0 0 1 21 9.667v8.666A2.667 2.667 0 0 1 18.333 21H9.667A2.667 2.667 0 0 1 7 18.333z"/>
                                            <path d="M4.012 16.737A2 2 0 0 1 3 15V5c0-1.1.9-2 2-2h10c.75 0 1.158.385 1.5 1"/>
                                        </g>
                                    </svg>`
                        const successSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                    <path d="M7 9.667A2.667 2.667 0 0 1 9.667 7h8.666A2.667 2.667 0 0 1 21 9.667v8.666A2.667 2.667 0 0 1 18.333 21H9.667A2.667 2.667 0 0 1 7 18.333z"/>
                                                    <path d="M4.012 16.737A2 2 0 0 1 3 15V5c0-1.1.9-2 2-2h10c.75 0 1.158.385 1.5 1M11 14l2 2l4-4"/>
                                                </g>
                                            </svg>`;
                        fetch(`${api}/private/${slug}`)
                            .then(response => response.blob())
                            .then(blob => {
                                const imageObjectURL = URL.createObjectURL(blob);
                                let deleteBtn = $("<button/>", {
                                    class: "nixfile-delete-btn",
                                    text: "X",
                                    click: () => {
                                        copyBtn.detach();
                                    }
                                })
                                let copyBtn = $("<button/>", {
                                    class: "nixfile-copy-btn",
                                    html: defaultSVG,
                                    style: `background-image : url(${imageObjectURL})`,
                                    click: function () {
                                        const btn = $(this);
                                        btn.addClass("copied");
                                        navigator.clipboard.writeText(`${api}/private/${slug}`).then(() => {
                                            btn.html(successSVG);
                                            setTimeout(() => {
                                                btn.removeClass("copied");
                                                btn.html(defaultSVG);
                                                copyBtn.append(deleteBtn)
                                                deleteBtn.on('click', () => {
                                                    copyBtn.detach()
                                                })
                                            }, 1000);
                                        }).catch(err => {
                                            console.error('Clipboard copy failed:', err);
                                        });
                                    }
                                });

                                $("#nixfile-box").append(copyBtn)
                                copyBtn.append(deleteBtn)
                            })
                            .catch(error => {
                                console.error('Error fetching image blob:', error);
                            });
                    },
                    error: (err) => {
                        if (err.responseJSON && err.responseJSON.errors) {
                            const responseError = err.responseJSON.errors;
                            errorsBox.css("display", 'block')
                            uploaderDir.append(errorsBox)
                            Object.keys(responseError).forEach((key) => {
                                const errorText = $("<span/>", {
                                    class: "nixfile-errors",
                                    text: responseError[key],
                                    style: "margin-inline-start: 0; transition: all .5s ease-in-out;"
                                });
                                errorsBox.append(errorText);
                                setTimeout(() => {
                                    errorText.css("margin-inline-start", '-100%');
                                }, 2000)
                                setTimeout(() => {
                                    errorText.detach();
                                }, 3000)
                            });

                        } else {
                            console.log('No specific error details available.');
                        }
                    }
                });
            }
        }
    });
    const elementorPanel = $("#elementor-panel");
    if (elementorPanel.length > 0 && elementorPanel.clientLeft !== 0) {
        uploaderDir.css("flex-direction", 'row-reverse');
        uploaderDir.css("justify-content", 'flex-start');
    } else {
        uploaderDir.css("display", 'flex');
        const html = $("html");
        if (html.attr('rtl') === 'rtl') {
            uploaderDir.css("flex-direction", 'row-reverse');
            uploaderDir.css("direction", 'ltr');
        } else {
            uploaderDir.css("flex-direction", 'row');
        }
    }
});
