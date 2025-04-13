import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Enable logging for Pusher in development
Pusher.logToConsole = true;

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '20dccbda8996e1fe6603',  // Your Pusher key
    cluster: 'mt1',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss']
});
