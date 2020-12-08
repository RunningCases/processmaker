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
        params.append('pro', "6161281705fc91129328391060454559");
        params.append('tas', "2076843175fc911573db050062710755");
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
    //remove this section
    search(data) {
        return new Promise((resolutionFunc, rejectionFunc) => {

            resolutionFunc(startedCasesFaker);

        });
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
