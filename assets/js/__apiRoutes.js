const api = {
    url: "http://192.168.0.244:7000",
    version: ["v1", "v2"]
}

export function link($version = 1) {
    return `${api.url}/${api.version[$version - 1]}`
}