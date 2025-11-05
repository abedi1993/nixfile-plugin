const api = {
    url: nixfile_ajax_data.url,
    version: ["v1", "v2"]
}

console.log("iaasndasda", nixfile_ajax_data.url)

export function link($version = 1) {
    return `${api.url}/${api.version[$version - 1]}`
}