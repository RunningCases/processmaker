import ApiInstance from "./Api.js";
import Services from "./Services";
let Api = new ApiInstance( Services );

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