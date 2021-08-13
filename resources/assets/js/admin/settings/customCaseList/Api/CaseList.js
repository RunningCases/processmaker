import axios from "axios";
import Api from "../../../../api/Api";
import Services from "./Services";
import Defaults from "./Mocks/defaults.json";
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
    deleteCaseList(data) {
        return axios.delete(
            window.config.SYS_SERVER_API +
            '/api/1.0/' +
            window.config.SYS_WORKSPACE +
            '/caseList/' + data.id, {
            headers: {
                'Authorization': 'Bearer ' + window.config.SYS_CREDENTIALS.accessToken,
                "Accept-Language": window.config.SYS_LANG
              }
            }
        );
    }
    reportTables(data) {
        return this.get({
            service: 'REPORT_TABLES',
            params: data,
            keys: {}
        });
    }
    getDefault(module){
        return Defaults[module]
    }
    createCaseList(data) {
        return this.post({
            service: "CASE_LIST",
            data: data
        });
    }
    updateCaseList(data) {
        return this.put({
            service: "PUT_CASE_LIST",
            keys: {
                id: data.id
            },
            data: data
        });
    }
}
let api = new caseListApi(Services);

export default api;
