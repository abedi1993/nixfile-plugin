const api = {
    url: "https://api.nixfile.com",
    version: ["v1", "v2"]
}

export function link($version = 1) {
    return `${api.url}/${api.version[$version - 1]}`
}