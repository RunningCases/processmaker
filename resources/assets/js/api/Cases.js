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
