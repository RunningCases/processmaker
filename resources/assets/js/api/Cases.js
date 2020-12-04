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
