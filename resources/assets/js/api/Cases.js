import axios from "axios";
import headerData from "./../mocks/casesHeader.json";
import startedData from "./../mocks/startedCasesFaker.js";
import inprogressData from "./../mocks/inprogressCases.json";
import completedData from "./../mocks/completedCases.json";
import supervisingData from "./../mocks/supervisingCases.json";

export let cases = {
    get(data) {
        if (data.type == "STARTED_BY_ME") {
            return new Promise((resolutionFunc, rejectionFunc) => {
                resolutionFunc(startedData);
            });
        }
        if (data.type == "IN_PROGRESS") {
            return new Promise((resolutionFunc, rejectionFunc) => {
                resolutionFunc(inprogressData);
            });
        }
        if (data.type == "COMPLETED") {
            return new Promise((resolutionFunc, rejectionFunc) => {
                resolutionFunc(completedData);
            });
        }
        if (data.type == "SUPERVISING") {
            return new Promise((resolutionFunc, rejectionFunc) => {
                resolutionFunc(supervisingData);
            });
        }
    },
    getPost(id) {
        return Client.get(`${resource}/${id}`);
    },
    create(payload) {
        return Client.post(`${resource}`, payload);
    },
    update(payload, id) {
        return Client.put(`${resource}/${id}`, payload);
    },
    delete(id) {
        return Client.delete(`${resource}/${id}`)
    },
    start(dt) {
        var params = new URLSearchParams();
        params.append('action', 'startCase');
        params.append('processId', dt.pro_uid);
        params.append('taskId', dt.task_uid);
        return axios.post(window.config.SYS_SERVER +
            window.config.SYS_URI +
            `cases/casesStartPage_Ajax.php`, params);
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
