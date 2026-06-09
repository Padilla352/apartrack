import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Make Pusher globally available for Laravel Echo
window.Pusher = Pusher;

// Laravel Echo configured for Pusher (cloud)
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST || undefined,   // blank = use default Pusher host
    wsPort: import.meta.env.VITE_PUSHER_PORT || undefined,   // blank = use default port
    wssPort: import.meta.env.VITE_PUSHER_PORT || undefined,  // blank = use default port
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});