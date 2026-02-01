import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const apiUrl =
    import.meta.env.VITE_APP_URL_PRODUCT || 'https://invoscope.tech/';
window.axios.defaults.baseURL = apiUrl;
