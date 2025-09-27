const api = {
    url: "http://api.naring.ir",
    version: ["v1", "v2"]
}

export function link($version = 1) {
    return `${api.url}/${api.version[$version - 1]}`
}