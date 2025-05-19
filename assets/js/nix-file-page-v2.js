import {fetchFileManagerData} from "./utils/fetchFileManagerData.js";
import {postFolder} from "./actions/postFolder.js";
import {uploaderInput} from "./ui/uploaderInput.js";
import {setting} from "./ui/setting.js";

jQuery(async function ($) {
    const nixfileLoader = $("#nixfile-loader");
    const nixfileUploaderSection = $(".nixfile-uploader");
    const nixfileSettingSection = $(".nixfile-setting");
    const nixfileFolderFormContainer = $(".nixfile-folder-form-container");
    const nixfileFolderContextMenu = $(".nixfile-folder-contextmenu");
    const nixfileDeleteFolderContainer = $("#nixfile-delete-folder-form-container");
    const nixfileMoveFolderContainer = $("#nixfile-folder-move-container");
    const nixfileDetailBar = $(".nixfile-detail-bar");
    const nixfileFileContextMenu = $(".nixfile-file-contextmenu");
    const nixfileFileEditNameContainer = $(".nixfile-file-edit-name-form-container");
    const nixfileDeleteFileContainer = $("#nixfile-delete-file-form-container");
    const nixfileCreateNewFolderForm = $(".nixfile-create-new-folder-form");
    const nixfileReplaceFormContainer = $("#nixfile-replace-file-form-container");
    const nixfileMultiSelectTools = $(".nixfile-multi-select-tools");

    nixfileLoader.fadeOut(400);
    nixfileUploaderSection.hide();
    nixfileSettingSection.hide();
    nixfileFolderFormContainer.hide();
    nixfileFolderContextMenu.hide();
    nixfileDeleteFolderContainer.hide();
    nixfileMoveFolderContainer.hide();
    nixfileDetailBar.hide();
    nixfileFileContextMenu.hide();
    nixfileFileEditNameContainer.hide();
    nixfileDeleteFileContainer.hide();
    nixfileCreateNewFolderForm.hide();
    nixfileReplaceFormContainer.hide();
    nixfileMultiSelectTools.hide();

    window.nixfileMediaPage = 1;
    window.nixfileMediaLoading = false;
    window.nixfileMediaReachedEnd = false;
    window.nixfileMediaRequest = null;

    setting();
    await fetchFileManagerData({
        force: true
    });
    postFolder();
    uploaderInput();
});