import Api from "./Api.js";

export let process = {
    list: {
        start(dt) {
            return Api.fetch({
                service: "GET_NEW_CASES",
                method: "get",
                data: {},
                keys: {}
            });
        }
    }
};