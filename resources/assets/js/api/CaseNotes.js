import axios from "axios";

export let caseNotes = {
    post(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('noteText', data.COMMENT);
        params.append('swSendMail', data.SEND_MAIL ? 1 : 0);
        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `appProxy/postNote`, params);
    }
};
