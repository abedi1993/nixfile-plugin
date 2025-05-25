import {get} from "./fetch.js";
import {link} from "../__apiRoutes.js";
import {getToken} from "./getToken.js";
import {breadcrumb} from "../ui/breadcrumb.js";
import {createFolder} from "../ui/createFolder.js";
import {createMedia} from "../ui/createMedia.js";
import {statistics} from "./statistics.js";
import {createTypeFilters} from "../actions/createTypeFilters.js";
import {createDateFilters} from "../actions/createDateFilter.js";
import {searchInput} from "../actions/searchInput.js";

export async function fetchFileManagerData(params = {}) {
    const page = params.page || 1;
    const force = params.force || false;
    if ((window.nixfileMediaLoading || window.nixfileMediaReachedEnd) && !force) return;
    if (force) {
        if (window.nixfileMediaAbortController) {
            window.nixfileMediaAbortController.abort();
        }
        window.nixfileMediaPage = 1;
        window.nixfileMediaReachedEnd = false;
        jQuery(".nixfile-media-section").empty();
        jQuery(".nixfile-media-section-container").scrollTop(0);
    }
    window.nixfileMediaLoading = true;
    const response = await get(`${link(2)}/domain/file-manager/${getToken}?folder_id=${params.folder_id ?? ''}&page=${page}&month=${params.month}&type=${params.type}&search=${params.search}`);
    await statistics();
    createTypeFilters(response.data.filter.type);
    createDateFilters(response.data.filter.date);
    searchInput();

    if (!response) {
        window.nixfileMediaLoading = false;
        return;
    }
    window.currentFolderId = response.data.current_folder.id;
    window.nixfileMediaPage = response.data.media.current_page;
    window.nixfileMediaLastPage = response.data.media.last_page;
    if (window.nixfileMediaPage >= window.nixfileMediaLastPage) {
        window.nixfileMediaReachedEnd = true;
    }
    await breadcrumb(response.data.current_folder);
    if (page === 1) await createFolder(response.data.folders);
    await createMedia(response.data.media);
    window.nixfileMediaLoading = false;
    return response;
}

