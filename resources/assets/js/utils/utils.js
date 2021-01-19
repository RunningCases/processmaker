import _ from "lodash";
export default {
    /**
     * Environment Formats function for full name
     * @param {object} params
     */
    userNameDisplayFormat(params) {
        let aux;
        let defaultValues = {
                userName: '',
                firstName: '',
                lastName: '',
                format: '(@lastName, @firstName) @userName'
            };
        _.assignIn(defaultValues, params);
        aux = defaultValues.format;
        aux = aux.replace('@userName',defaultValues.userName);
        aux = aux.replace('@firstName',defaultValues.firstName);
        aux = aux.replace('@lastName',defaultValues.lastName);
        return aux;
    }
}