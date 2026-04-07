import axios from 'axios'
window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

import Pusher from 'pusher-js'
window.Pusher = Pusher

import Echo from 'laravel-echo'

const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname
const reverbPort = Number(import.meta.env.VITE_REVERB_PORT || 8080)
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http'
const useTls = reverbScheme === 'https'

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'local',
    wsHost: reverbHost,
    wsPort: reverbPort,
    wssPort: reverbPort,
    forceTLS: useTls,
    encrypted: useTls,
    enabledTransports: ['ws', 'wss'],
})
