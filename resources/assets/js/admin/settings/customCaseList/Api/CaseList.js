import axios from "axios";
import Api from "../../../../api/Api";
import Services from "./Services";

class caseListApi extends Api {
    constructor(services) {
    // Here, it calls the parent class' constructor with lengths
    // provided for the Polygon's width and height
        super(services, services);
    }
    /**
     * Get the case list
     * @param {object} data 
     * @param {string} module 
     */
    getCaseList(data, module) {
        let service = "CASE_LIST_TODO";
        switch (module) {
            case 'inbox' :
                service = "CASE_LIST_TODO";
                break;
            case 'draft' :
                service = "CASE_LIST_DRAFT";
                break;
            case 'unassigned' :
                service = "CASE_LIST_UNASSIGNED";
                break;
            case 'paused' :
                service = "CASE_LIST_PAUSED";
                break;
        }
        
        return this.get({
            service: service,
            params: data,
            keys: {}
        });
    }
}
let api = new caseListApi(Services);

export default api;
