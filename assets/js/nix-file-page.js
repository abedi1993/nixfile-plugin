jQuery(function ($) {
    const nixfileContainer = $(".nixfile-container");
    const nixfileCloseBtn = $("#nixfile-close-btn");
    const nixfileOpenerBtn = $("#nixfile-uploader-opener");
    const nixfileUploaderSection = $(".nixfile-uploader");
    const nixfileSettingToggler = $("#nixfile-setting-toggler");
    const nixfileSettingSection = $(".nixfile-setting");
    const nixfileAjaxData = nixfile_ajax_data;
    const nixfileStoreTokenBtn = $("#nixfile_store_token");
    const nixfileStoreEmailBtn = $("#nixfile_store_email");
    const apiUrl = "http://192.168.0.244:7000/v1";
    const searchInput = $("#nixfile-search-input");
    const nixfileMediaBox = $(".nixfile-media-box");
    const nixfileMediaSection = $(".nixfile-media-section");
    const nixfileUploaderLabel = $("#nixfile-uploader");
    const uploaderDir = $("#nixfile-box");
    const errorsBox = $(".nixfile-errors-box");
    const nixfileFolderFormContainer = $(".nixfile-folder-form-container");
    const nixfileFolderFormOpener = $("#nixfile-folder-opener");
    const nixfileTokenInput = $("input[name=nixfile_store_token]");
    const nixfileFolderForm = $(".nixfile-folder-form");
    let searchTimeout;
    let nixfileMediaPage = 1;
    let nixfileMediaLastPage = 1;
    let isLoading = false;
    nixfileUploaderSection.hide();
    nixfileSettingSection.hide();
    nixfileFolderFormContainer.hide()
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
    });
    nixfileStoreTokenBtn.on("click", (e) => {
        const formData = new FormData();
        const token = $("input[name=nixfile_store_token]").val();
        formData.append('token', token);
        formData.append("action", nixfileAjaxData.action.set_token);
        formData.append("security", nixfileAjaxData.nonce);
        $.ajax({
            url: nixfileAjaxData.ajax_url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (res) => {
                const toast = $("<span/>", {
                    class: 'nixfile-success-toast',
                    text: res.message,
                    style: "inset-inline-start:0;"
                });
                nixfileContainer.append(toast)
                setTimeout(() => {
                    toast.css("inset-inline-start", "-100%");
                }, 2500);
                setTimeout(() => {
                    toast.detach();
                }, 4000)
            },
            error: (err) => {
            }
        });
    })
    const loadMedia = (page = 1) => {
        if (isLoading) return
        isLoading = true;
        $.ajax({
            url: `${apiUrl}/domain/${nixfileTokenInput.val()}`,
            type: "GET",
            data: {
                per_page: 50,
                page: nixfileMediaPage,
                search: searchInput.val(),
            },
            success: (res) => {
                const media = res.data.media.data;
                nixfileMediaLastPage = res.data.media.last_page;
                media.forEach((item, index) => {
                    const box = $("<div/>", {
                        class: 'nixfile-media-box',
                        style: `background-image:url(${item.url})`,
                        text: parseInt(item.type.int) !== 0 ? item.type.fa : "",
                    });
                    if (index === media.length - 1) {
                        box.attr('data-scroll', 'true')
                        setupInfiniteScrollObserver(box);
                    }
                    nixfileMediaSection.append(box)
                });
                isLoading = false;
            },
            error: (err) => {
                isLoading = false;
            }
        })
    }
    searchInput.on("input", async (e) => {
        console.log(e.target.value);
        nixfileMediaPage = 1;
        if (searchTimeout)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(async () => {
            nixfileMediaSection.empty()
            await loadMedia();
        }, 200)
    })

    function setupInfiniteScrollObserver(element) {
        const domElement = element instanceof jQuery ? element[0] : element;
        if (!domElement || !(domElement instanceof Element)) {
            console.error("Invalid element for observer");
            return;
        }
        if (nixfileMediaLastPage <= nixfileMediaPage)
            return;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    observer.unobserve(entry.target);
                    nixfileMediaPage += 1;
                    loadMedia(nixfileMediaPage)
                }
            });
        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        });

        observer.observe(domElement);
    }

    loadMedia(nixfileMediaPage);

    const uploadFile = async (file) => {
        const box = $("<div/>", {
            class: 'nixfile-media-box',
            html: `<span class="nixfile-media-progress"></span>`
        });
        nixfileMediaSection.prepend(box);
        const formData = new FormData();
        formData.append('file', file);
        formData.append('upload_type', '1');
        formData.append('expired_at', '2');
        formData.append("domain_id", nixfileTokenInput.val())
        await $.ajax({
            url: `${apiUrl}/upload`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        box.find(".nixfile-media-progress").css("width", percent + "%")
                    }
                }, false);
                return xhr;
            },
            success: function (response) {
                box.addClass('uploaded');
                const slug = response.data.slug;
                const url = `${apiUrl}/private/${slug}`
                box.css("background-image", `url(${url})`);
                box.html('')
                console.log(response)
            },
            error: function (xhr, status, error) {
                box.find(".nixfile-media-progress").css("background-color", "red");
                setTimeout(() => {
                    box.detach();
                }, 5000)
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const responseError = xhr.responseJSON.errors;
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

    nixfileUploaderLabel.find("input").on("change", async function (e) {
        const [file] = e.target.files;
        await uploadFile(file);
        nixfileMediaSection.empty();
        loadMedia();
        loadFolders()
    })
    nixfileUploaderLabel.on("dragover", (e) => {
        e.preventDefault();
        e.stopPropagation();
        nixfileUploaderLabel.addClass("active")
    })
        .on("dragleave", (e) => {
            e.preventDefault();
            e.stopPropagation();
            nixfileUploaderLabel.removeClass("active")
        })
        .on("drop", async (e) => {
            e.preventDefault();
            e.stopPropagation();
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                await uploadFile(files[0]);
                nixfileUploaderLabel.removeClass("active");
                nixfileMediaSection.empty();
                loadMedia();
                loadFolders()
            }
        });
    nixfileFolderFormContainer.on("click", (e) => {
        $(e.currentTarget).stop().fadeOut();
    });
    nixfileFolderFormOpener.on('click', (e) => {
        nixfileFolderFormContainer.stop().fadeIn()
    })
    nixfileFolderForm.on('click', (e) => {
        e.stopPropagation();
    }).on('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        formData.append('domain_id', nixfileTokenInput.val());
        if (false)
            formData.append('parent_id', null);
        $.ajax({
            url: `${apiUrl}/upload/folder`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (res) => {
                nixfileFolderFormContainer.stop().fadeOut();
                loadFolders()
                const toast = $("<span/>", {
                    class: 'nixfile-success-toast',
                    text: res.message,
                    style: "inset-inline-start:0;"
                });
                nixfileContainer.append(toast)
                setTimeout(() => {
                    toast.css("inset-inline-start", "-100%");
                }, 2500);
                setTimeout(() => {
                    toast.detach();
                }, 4000)
            },
            error: (error) => {
                if (error.responseJSON && error.responseJSON.errors) {
                    const responseError = error.responseJSON.errors;
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
        })
    });

    const loadFolders = () => {
        $.ajax({
            url: `${apiUrl}/upload/folder/${nixfileTokenInput.val()}`,
            type: "GET",
            processData: false,
            contentType: false,
            success: (res) => {
                res.data.forEach((item) => {
                    const box = $("<div/>", {
                        class: "nixfile-folder",
                        text: '',
                        html: `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <path fill="currentColor"
                      d="M20 5h-9.586L8.707 3.293A1 1 0 0 0 8 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V7c0-1.103-.897-2-2-2"/>
            </svg>
            <p>
                ${item.title}
            </p>`
                    });
                    nixfileMediaSection.prepend(box)
                })
            },
            error: (error) => {

            }
        })
    }
    loadFolders()
});