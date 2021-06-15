import axios from 'axios';

export let menu = {
    get() {
        return axios.get(
            window.config.SYS_SERVER_API +
            '/api/1.0/' +
            window.config.SYS_WORKSPACE +
            '/home/menu', {
            headers: {
                'Authorization': 'Bearer ' + window.config.SYS_CREDENTIALS.accessToken,
                "Accept-Language": window.config.SYS_LANG
              }
        });
        
    },
    getCounters() {
        return axios.get(
            window.config.SYS_SERVER_API +
            '/api/1.0/' +
            window.config.SYS_WORKSPACE +
            '/home/tasks/counter', {
            headers: {
                'Authorization': 'Bearer ' + window.config.SYS_CREDENTIALS.accessToken,
                "Accept-Language": window.config.SYS_LANG
              }
        });
    }
};
