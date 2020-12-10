import axios from "axios";
import headerData from "./../mocks/casesHeader.json";
import startedCasesFaker from "./../mocks/startedCasesFaker.js";
import Api from "./Api.js";

export let cases = {
    myCases(data) {
        return Api.get({
            service: "MY_CASES",
            params: {
                filter: data.filter
            },
            keys: {}
        });
    },
    todo(data) {
        return Api.get({
            service: "TODO_LIST",
            params: {
            },
            keys: {}
        });
    },
    draft(data) {
        return Api.get({
            service: "DRAFT_LIST",
            params: {
            },
            keys: {}
        });
    },
    paused(data) {
        return Api.get({
            service: "PAUSED_LIST",
            params: {
            },
            keys: {}
        });
    },
    unassigned(data) {
        return Api.get({
            service: "UNASSIGNED_LIST",
            params: {
            },
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
    inputdocuments(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('action', "getCasesInputDocuments");

        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `cases/cases_Ajax.php?action=getCasesInputDocuments`, params);
    },
    outputdocuments(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('action', "getCasesOutputDocuments");

        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `cases/cases_Ajax.php?action=getCasesOutputDocuments`, params);
    },
    casesummary(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('action', "todo");

        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `appProxy/getSummary`, params);
    },
    casenotes(data) {
        var params = new FormData();
        params.append('appUid', data.APP_UID);
        params.append('delIndex', data.DEL_INDEX);
        params.append('pro', data.PRO_UID);
        params.append('tas', data.TAS_UID);
        params.append('start', "0");
        params.append('limit', "30");
        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `appProxy/getNotesList`, params);
    },
    start(dt) {
        var params = new URLSearchParams();
        params.append('action', 'startCase');
        params.append('processId', dt.pro_uid);
        params.append('taskId', dt.task_uid);
        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `cases/casesStartPage_Ajax.php`, params);
    },
    open(data) {
        return axios.get(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `cases/open?APP_UID=${data.APP_UID}&DEL_INDEX=${data.DEL_INDEX}&action=${data.ACTION}`);
    },
    cancel(data) {
        var params = new URLSearchParams();
        params.append('action', 'cancelCase');
        params.append('NOTE_REASON', data.COMMENT);
        params.append('NOTIFY_CANCEL', data.SEND);
        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `cases/ajaxListener`, params);
    },
    unpause(data) {
        var params = new URLSearchParams();
        params.append('action', 'unpauseCase');
        params.append('sApplicationUID', data.APP_UID);
        params.append('iIndex', data.DEL_INDEX);
        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `cases/cases_Ajax`, params);
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
        return axios.post(window.config.SYS_SERVER +
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
            keys: {}
        })
    }

};

export let casesHeader = {
    get() {
        return new Promise((resolutionFunc, rejectionFunc) => {
            resolutionFunc({
                data: headerData
            });
        });
    }
};
