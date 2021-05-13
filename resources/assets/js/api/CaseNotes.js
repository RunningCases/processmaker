import axios from "axios";
import Api from "./Api.js";

export let caseNotes = {
    post(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('noteText', data.COMMENT);
        params.append('swSendMail', data.SEND_MAIL ? 1 : 0);

        _.each(data.FILES, (f) => {
            params.append("filesToUpload[]", f);
        })

        return Api.post({
            service: "ADD_NOTE",
            data:{
                note_content: data.COMMENT,
                send_mail: data.SEND_MAIL ? 1 : 0
            },
            keys: {
                app_uid: data.APP_UID
            }
        });
    },
};
