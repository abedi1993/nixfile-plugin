jQuery(async function ($) {
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
    const nixfileBreadcrumb = $("#breadcrumb");
    const nixfileFolderContextMenu = $(".nixfile-folder-contextmenu");
    const nixfileFolderEdit = $("#nixfile-edit-folder");
    const nixfileFolderDelete = $("#nixfile-delete-folder");
    const nixfileFolderMove = $("#nixfile-move-folder");
    const nixfileDeleteFolderContainer = $("#nixfile-delete-folder-form-container");
    const nixfileMoveFolderContainer = $("#nixfile-folder-move-container");
    const cancelMoveFolderModalBtn = $(".nixfile-cancel-button");
    let searchTimeout;
    let nixfileMediaPage = 1;
    let nixfileMediaLastPage = 1;
    let isLoading = false;
    let selectedFolderId = null;
    let currentFolder = null;
    let editFolder;
    nixfileUploaderSection.hide();
    nixfileSettingSection.hide();
    nixfileFolderFormContainer.hide();
    nixfileFolderContextMenu.hide();
    nixfileDeleteFolderContainer.hide();
    nixfileMoveFolderContainer.hide();
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
    });

    const loadMedia = () => {
        if (isLoading) return
        isLoading = true;
        $.ajax({
            url: `${apiUrl}/domain/${nixfileTokenInput.val()}`,
            type: "GET",
            data: {
                per_page: 50,
                page: nixfileMediaPage,
                search: searchInput.val(),
                folder_id: selectedFolderId,
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
        nixfileMediaPage = 1;
        if (searchTimeout)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(async () => {
            nixfileMediaSection.empty()
            await loadMedia();
            if (!e.target.value)
                await loadFolders()
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

    loadMedia();

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
        if (selectedFolderId !== null)
            formData.append('folder_id', selectedFolderId);
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
                }
            }
        });
    }

    nixfileUploaderLabel.find("input").on("change", async function (e) {
        const [file] = e.target.files;
        await uploadFile(file);
        nixfileMediaSection.empty();
        nixfileMediaPage = 1;
        await loadMedia();
        await loadFolders()
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
                nixfileMediaPage = 1;
                loadMedia();
                await loadFolders()
            }
        });

    nixfileFolderFormContainer.on("click", (e) => {
        $(e.currentTarget).stop().fadeOut();
    });

    nixfileFolderFormOpener.on('click', (e) => {
        nixfileFolderFormContainer.stop().fadeIn();
        nixfileFolderFormContainer.find('button').text('ثبت');
        $("#nixfile-edit-folder-name").remove();
        nixfileFolderFormContainer.find('label input').attr('placeholder', 'مثلا: نمونه کار');
    })

    nixfileFolderForm.on('click', (e) => {
        e.stopPropagation();
    }).on('submit', function (e) {
        e.preventDefault();
        let url = `${apiUrl}/upload/folder`;
        const form = $(e.currentTarget);
        const formData = new FormData(e.currentTarget);
        if (editFolder) {
            url += `/${editFolder.id}`;
            type = editFolder.type;
            formData.append('_method', 'PUT');
        }
        formData.append('domain_id', nixfileTokenInput.val());
        if (selectedFolderId !== null)
            formData.append('parent_id', selectedFolderId);
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: async function (res) {
                editFolder = null;
                nixfileFolderFormContainer.stop().fadeOut();
                const nixfileFolderBox = $(".nixfile-folder");
                if (nixfileFolderBox)
                    nixfileFolderBox.remove()
                await loadFolders()
                const toast = $("<span/>", {
                    class: 'nixfile-success-toast',
                    text: res.message,
                    style: "inset-inline-start:0;"
                });
                form.find('input').val("");
                nixfileContainer.append(toast)
                setTimeout(() => {
                    toast.css("inset-inline-start", "-100%");
                }, 2500);
                setTimeout(() => {
                    toast.detach();
                }, 4000)
            },
            error: (error) => {
                editFolder = null;
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
                }
            }
        })
    });

    const loadFolders = async () => {
        let url = `${apiUrl}/upload/folder/${nixfileTokenInput.val()}`;
        if (selectedFolderId)
            url += `?parent_id=${selectedFolderId}`;
        if (currentFolder)
            url += selectedFolderId ? `&current_id=${currentFolder}` : `?current_id=${currentFolder}`
        await $.ajax({
            url: url,
            type: "GET",
            processData: false,
            contentType: false,
            success: (res) => {
                res.data.forEach((item) => {
                    const box = $("<div/>", {
                        class: "nixfile-folder",
                    });
                    const nixfileFolderIcon = $("<div/>", {
                        class: 'nixfile-folder-icon'
                    }).css("background-image", `url(${nixfile_ajax_data.images_url}/folder.png)`);
                    const p = $("<p/>", {
                        class: 'nixfile-folder-title',
                        text: item.title
                    })
                    box.append(nixfileFolderIcon);
                    box.append(p)
                    box.attr({
                        'data-id': item.id,
                        'data-name': item.title,
                        'data-parent-id': item.parent_id,
                    });
                    if (item.id === selectedFolderId)
                        nixfileFolderIcon.css("background-image", `url(${nixfile_ajax_data.images_url}/back.png)`);
                    box.on("click", async function (e) {
                        const id = $(this).attr('data-id');
                        await nixfileMediaSection.empty()
                        if (id === selectedFolderId) {
                            selectedFolderId = item.parent_id;
                            nixfileMediaPage = 1;
                            await loadFolders()
                            await loadMedia()
                            $(`.nixfile-breadcrumb-items[data-id=${id}]`).remove()
                        } else {
                            selectedFolderId = $(this).attr('data-id');
                            nixfileMediaPage = 1;
                            await loadFolders()
                            await loadMedia()
                            breadcrumbMaker()
                        }
                    }).on('contextmenu', function (e) {
                        e.preventDefault();
                        nixfileFolderContextMenu.stop().slideDown(100);
                        nixfileFolderContextMenu.css({
                            'position': 'absolute',
                            'top': e.pageY + 'px',
                            'left': e.pageX + 'px'
                        });
                        nixfileFolderContextMenu.attr({
                            'data-id': $(this).attr('data-id'),
                            'data-name': $(this).attr("data-name")
                        })
                    });
                    nixfileMediaSection.prepend(box)
                })
            },
            error: (error) => {
            }
        })
    }
    await loadFolders()

    function breadcrumbMaker(e) {
        let folder;
        if (selectedFolderId)
            folder = $(`.nixfile-folder[data-id=${selectedFolderId}]`);
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><!-- Icon from Material Symbols by Google - https://github.com/google/material-design-icons/blob/master/LICENSE --><path fill="#888888" d="m6.05 19l5-7l-5-7H8.5l5 7l-5 7zM12 19l5-7l-5-7h2.45l5 7l-5 7z"/></svg>`;

        const breadcrumbItem = $("<p/>", {
            class: 'nixfile-breadcrumb-items',
            html: `${svg}
<p>${folder ? folder.attr('data-name') : 'خانه'}</p>
`
        });
        breadcrumbItem.attr('data-id', selectedFolderId ? selectedFolderId : null);
        breadcrumbItem.on("click", async function (e) {
            selectedFolderId = $(this).attr('data-id');
            nixfileMediaSection.empty();
            removeBreadcrumb($(this).next());
            breadcrumbLink(selectedFolderId)
            nixfileMediaPage = 1;
            await loadFolders();
            await loadMedia();
        });
        nixfileBreadcrumb.append(breadcrumbItem);
        breadcrumbLink(selectedFolderId);
    }

    const removeBreadcrumb = (element) => {
        if (element.next().length > 0)
            removeBreadcrumb(element.next());
        element.remove();
    }

    function breadcrumbLink(elementId) {
        $(`.nixfile-breadcrumb-items[data-id=${elementId}]`).prevAll().css("color", '#2d77b0');

    }

    breadcrumbMaker()

    $(document).on('click', function (e) {
        nixfileFolderContextMenu.stop().slideUp(100);
    })

    nixfileFolderEdit.on("click", function (e) {
        nixfileFolderFormContainer.stop().fadeIn();
        editFolder = {
            'name': nixfileFolderContextMenu.attr('data-name'),
            'id': nixfileFolderContextMenu.attr('data-id'),
            'type': 'PUT',
        }
        nixfileFolderFormContainer.find("label input").attr('placeholder', editFolder.name);
        nixfileFolderFormContainer.find('button').text("ویرایش نام");
    });
    nixfileDeleteFolderContainer.on('click', function (e) {
        $(this).fadeOut();
    });
    nixfileFolderDelete.on('click', function (e) {
        nixfileDeleteFolderContainer.fadeIn();
        nixfileDeleteFolderContainer.find('input[type=text]').attr('value', nixfileFolderContextMenu.attr('data-id'))
    })
    nixfileDeleteFolderContainer.find('form').on("submit", function (e) {
        e.preventDefault();
        const url = `${apiUrl}/upload/folder/${nixfileFolderContextMenu.attr('data-id')}?domain_id=${nixfileTokenInput.val()}`
        $.ajax({
            url: url,
            type: "DELETE",
            processData: false,
            contentType: false,
            success: async function (res) {
                nixfileDeleteFolderContainer.stop().fadeOut();
                const nixfileFolderBox = $(".nixfile-folder");
                if (nixfileFolderBox)
                    nixfileFolderBox.remove()
                await loadFolders()
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
                }
            }
        });
    }).on('click', function (e) {
        e.stopPropagation();
    });
    nixfileDeleteFolderContainer.find('button').on('click', function (e) {
        nixfileDeleteFolderContainer.fadeOut();
    });
    nixfileMoveFolderContainer.on('click' , function (e){
       $(this).fadeOut();
    });
    nixfileMoveFolderContainer.find('.nixfile-folder-move-content').on('click' , function (e){
        e.preventDefault();
        e.stopPropagation();
    })
    nixfileFolderMove.on("click" , function (e){
       nixfileMoveFolderContainer.fadeIn();
    });
    cancelMoveFolderModalBtn.on("click" , function (e){
        e.preventDefault();
        nixfileMoveFolderContainer.fadeOut();
    })
    
});