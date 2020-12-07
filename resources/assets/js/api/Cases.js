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
