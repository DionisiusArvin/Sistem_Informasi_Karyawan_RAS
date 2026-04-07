const blockedPathPatterns = [
    /^\/projects\/create(?:\/|$)/,
    /^\/projects\/[^/]+\/tasks\/create(?:\/|$)/,
    /^\/tasks\/[^/]+\/edit(?:\/|$)/,
    /^\/daily-tasks\/[^/]+\/upload(?:\/|$)/,
]
let lastReloadAt = 0

function shouldSkipRealtimeReload() {
    const path = window.location.pathname
    const isBlockedPath = blockedPathPatterns.some((pattern) => pattern.test(path))

    if (isBlockedPath) {
        return true
    }

    if (document.body?.dataset.realtimeReload === 'off') {
        return true
    }

    if (document.querySelector('[data-flash-success]')) {
        return true
    }

    const now = Date.now()
    const reloadedRecently = now - lastReloadAt < 1200

    if (reloadedRecently) {
        return true
    }

    lastReloadAt = now

    return false
}

document.addEventListener('DOMContentLoaded', () => {
    if (!window.Echo) {
        return
    }

    window.Echo.channel('data-channel').listen('.DataChanged', (payload) => {
        window.dispatchEvent(new CustomEvent('app:data-changed', { detail: payload }))

        if (shouldSkipRealtimeReload()) {
            return
        }

        window.location.reload()
    })
})
