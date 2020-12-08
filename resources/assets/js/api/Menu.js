import axios from 'axios';

export let menu = {
    get() {
        return axios.get(window.config.SYS_SERVER + '/api/1.0/' + window.config.SYS_WORKSPACE + '/home/menu', {
            headers: {
                'Authorization': 'Bearer ' + window.config.SYS_CREDENTIALS.accessToken
              }
        });
        
    },
};
