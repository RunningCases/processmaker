import axios from "axios";
import Api from "./Api.js";
export let filters = {
    get(data) {
        return Api.get({
            service: "MY_FILTERS",
            params: {
                filter: data.params,
            },
            keys: {},
        });
    },
    post(data) {
        return Api.post({
            service: "POST_MY_FILTERS",
            data,
            keys: {},
        });
    },
    delete(data) {
        return Api.delete({
            service: "DELETE_MY_FILTERS",

            id: data.id,

            keys: {},
        });
    },
    /**
     * Service to generate a jump case URL
     */
    jumpCase() {
        var params = new URLSearchParams();
        params.append("action", "startCase");
        return axios.post(
            window.config.SYS_SERVER +
                window.config.SYS_URI +
                `cases/casesStartPage_Ajax.php`,
            params
        );
    },
    /**
     * Service to get the process list
     */
    processList(query) {
        return axios.post(
            window.config.SYS_SERVER +
                window.config.SYS_URI +
                `cases/casesList_Ajax?actionAjax=processListExtJs&action=search`,
            {
                query,
            }
        );
    },
    /**
     * Service to get the users list
     */
    userValues(query) {
        return axios.post(
            window.config.SYS_SERVER +
                window.config.SYS_URI +
                `cases/casesList_Ajax?actionAjax=userValues&action=search`,
            {
                query,
            }
        );
    },
};
