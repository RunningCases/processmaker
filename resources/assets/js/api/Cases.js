import axios from "axios";
import Api from "./Api.js";

export let cases = {
    myCases(data) {
        return Api.get({
            service: "MY_CASES",
            params: data,
            keys: {}
        });
    },
    todo(data) {
        return Api.get({
            service: "TODO_LIST",
            params: data,
            keys: {}
        });
    },
    draft(data) {
        return Api.get({
            service: "DRAFT_LIST",
            params: data,
            keys: {}
        });
    },
    paused(data) {
        return Api.get({
            service: "PAUSED_LIST",
            params: data,
            keys: {}
        });
    },
    unassigned(data) {
        return Api.get({
            service: "UNASSIGNED_LIST",
            params: data,
            keys: {}
        });
    },
    summary(data) {
        return Api.get({
            service: "UNASSIGNED_LIST",
            params: {
            },
            keys: {}
        });
    },
    openSummary(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('action', 'todo');

        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `appProxy/requestOpenSummary`, params);
    },
    inputdocuments(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('action', "getCasesInputDocuments");

        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/cases_Ajax.php?action=getCasesInputDocuments`, params);
    },
    outputdocuments(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('action', "getCasesOutputDocuments");

        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/cases_Ajax.php?action=getCasesOutputDocuments`, params);
    },
    casesummary(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('action', "todo");

        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `appProxy/getSummary`, params, {
            headers: {
                'Cache-Control': 'no-cache'
            }
        });
    },
    casenotes(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('pro', data.PRO_UID);
        params.append('tas', data.TAS_UID);
        params.append('start', "0");
        params.append('limit', "30");
        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `appProxy/getNotesList`, params);
    },
    pendingtask(data) {
        return axios.get(window.config.SYS_SERVER_API +
            '/api/1.0/' +
            window.config.SYS_WORKSPACE +
            '/home/' + data.APP_NUMBER + '/pending-tasks', {
            headers: {
                'Authorization': 'Bearer ' + window.config.SYS_CREDENTIALS.accessToken
            }
        });
    },
    start(dt) {
        var params = new URLSearchParams();
        params.append('action', 'startCase');
        params.append('processId', dt.pro_uid);
        params.append('taskId', dt.task_uid);
        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/casesStartPage_Ajax.php`, params);
    },
    open(data) {
        return axios.get(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/open?APP_UID=${data.APP_UID}&DEL_INDEX=${data.DEL_INDEX}&action=${data.ACTION}`);
    },
    cases_open(data) {
        return axios.get(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/cases_Open?APP_UID=${data.APP_UID}&DEL_INDEX=${data.DEL_INDEX}&action=${data.ACTION}`);
    },
    cancel(data) {
        var params = new URLSearchParams();
        params.append('action', 'cancelCase');
        params.append('NOTE_REASON', data.COMMENT);
        params.append('NOTIFY_CANCEL', data.SEND);

        return Api.put({
            service: "REQUEST_CANCEL_CASE",
            params: {},
            keys: {
                app_uid: data.APP_UID
            }
        });
    },
    actions(data) {
        var params = new URLSearchParams();
        params.append('action', 'getCaseMenu');
        params.append('app_status', 'TO_DO');
        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/ajaxListener`, params);
    },
    unpause(data) {
        var params = new URLSearchParams();
        params.append('action', 'unpauseCase');
        params.append('sApplicationUID', data.APP_UID);
        params.append('iIndex', data.DEL_INDEX);

        return Api.put({
            service: "REQUEST_UNPAUSE_CASE",
            params: {},
            keys: {
                app_uid: data.APP_UID
            }
        });
    },
    claim(data) {
        var params = new URLSearchParams();
        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/cases_CatchExecute`, params);
    },
    /**
     * Service to jump a case by it's number
     * @param {object} dt 
     */
    jump(dt) {
        var params = new URLSearchParams();
        params.append('action', 'previusJump');
        params.append('appNumber', dt.APP_NUMBER);
        params.append('actionFromList', dt.ACTION_FROM_LIST);
        return axios.post(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/cases_Ajax.php`, params);
    },
    /**
     * Make a search request to the Api service 
     * @param {object} dt - filter parameters
     */
    search(dt) {
        return Api.get({
            service: "SEARCH",
            params: dt,
            keys: {},
            paged: dt.paged
        })
    },
    /**
     * Make a search request to the Api service 
     * @param {object} dt - filter parameters
     */
    debugStatus(dt) {
        return Api.get({
            service: "DEBUG_STATUS",
            params: {},
            keys: {
                prj_uid: dt.PRO_UID
            }
        })
    },
    /**
     * Get debug Vars in ajax service
     * @param {*} data 
     */
    debugVars(data) {
        var params;
        if (data.filter === "all") {
            return axios.get(window.config.SYS_SERVER_AJAX +
                window.config.SYS_URI +
                `cases/debug_vars`);
        } else {
            params = new URLSearchParams();
            params.append('filter', data.filter);
            return axios.post(window.config.SYS_SERVER_AJAX +
                window.config.SYS_URI +
                `cases/debug_vars`, params);
        }
    },
    /**
     * Get triggers debug Vars in ajax service
     * @param {*} data 
     */
    debugVarsTriggers(data) {
        let dc = _.random(0, 10000000000),
            r = _.random(1.0, 100.0);
        return axios.get(window.config.SYS_SERVER_AJAX +
            window.config.SYS_URI +
            `cases/debug_triggers?r=${r}&_dc=${dc}`);
    },
};

export let casesHeader = {
    get() {
        return axios.get(window.config.SYS_SERVER_API +
            '/api/1.0/' +
            window.config.SYS_WORKSPACE +
            '/home/counters', {
            headers: {
                'Authorization': 'Bearer ' + window.config.SYS_CREDENTIALS.accessToken
            }
        });
    }
}; 
