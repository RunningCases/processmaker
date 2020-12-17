<template>
    <div id="v-mycases3" ref="v-mycases2" class="v-container-mycases">
        <b-alert
            :show="dismissCountDown"
            dismissible
            :variant="variant"
            @dismissed="dismissCountDown = 0"
            @dismiss-count-down="countDownChanged"
        >
            {{ message }}
        </b-alert>
        <button-fleft :data="newCase"></button-fleft>
        <h5>{{$t('ID_ADVANCEDSEARCH')}}</h5>
        
        <AdvancedFilter
            :id="id"
            :name="name"
            :filters="filters"
            @onJumpCase="onJumpCase"
            @onSubmit="onSubmitFilter"
            @onRemoveFilter="onRemoveFilter"
            @onSearch="onSearch"
            @onUpdateFilters="onUpdateFilters"
        />

        <modal-new-request ref="newRequest"></modal-new-request>

        <v-server-table
            :data="tableData"
            :columns="columns"
            :options="options"
            ref="test"
        >
            <div slot="info" slot-scope="props">
                <b-icon
                    icon="exclamation-circle-fill"
                    variant="primary"
                    @click="openCaseDetail(props.row)"
                ></b-icon>
            </div>
            <div slot="case_number" slot-scope="props">
                {{ props.row.CASE_NUMBER }}
            </div>
            <div slot="case_title" slot-scope="props">
                {{ props.row.CASE_TITLE }}
            </div>
            <div slot="process_name" slot-scope="props">
                {{ props.row.PROCESS_NAME }}
            </div>
            <div slot="task" slot-scope="props">
                <TaskCell :data="props.row.TASK" />
            </div>
            <div slot="status" slot-scope="props">
                {{ props.row.STATUS }}
            </div>
            <div slot="current_user" slot-scope="props">
                {{
                    nameFormatCases(
                        props.row.USR_FIRSTNAME,
                        props.row.USR_LASTNAME,
                        props.row.USR_USERNAME
                    )
                }}
            </div>
            <div slot="due_date" slot-scope="props">
                {{ props.row.DUE_DATE }}
            </div>
            <div slot="delegation_date" slot-scope="props">
                {{ props.row.DELEGATION_DATE }}
            </div>
            <div slot="priority" slot-scope="props">
                {{ props.row.PRIORITY }}
            </div>
            <div slot="actions" slot-scope="props">
                <div class="btn-default">
                    <i class="fas fa-comments"></i>
                    <span class="badge badge-light">9</span>
                    <span class="sr-only">unread messages</span>
                </div>
            </div>
        </v-server-table>
    </div>
</template>
<script>
import ButtonFleft from "../components/home/ButtonFleft.vue";
import ModalNewRequest from "./ModalNewRequest.vue";
import AdvancedFilter from "../components/search/AdvancedFilter";
import TaskCell from "../components/vuetable/TaskCell.vue";
import api from "./../api/index";
import { Event } from "vue-tables-2";

export default {
    name: "AdvancedSearch",
    components: {
        AdvancedFilter,
        ButtonFleft,
        ModalNewRequest,
        TaskCell
    },
    props: ["id", "name", "filters"],
    data() {
        return {
            dismissSecs: 5,
            dismissCountDown: 0,
            message: "",
            variant: "info",
            metrics: [],
            filter: "CASES_INBOX",
            allView: [],
            filtersModel: {},
            filterHeader: "STARTED_BY_ME",
            headers: [],
            newCase: {
                title: "New Case",
                class: "btn-success",
                onClick: () => {
                    this.$refs["newRequest"].show();
                },
            },
            columns: [
                "info",
                "case_number",
                "case_title",
                "process_name",
                "task",
                "current_user",
                "due_date",
                "delegation_date",
                "priority",
                "actions",
            ],
            tableData: [],
            options: {
                filterable: false,
                headings: {
                    info: "",
                    case_number: this.$i18n.t("ID_MYCASE_NUMBER"),
                    case_title: this.$i18n.t("ID_CASE_TITLE"),
                    process_name: this.$i18n.t("ID_PROCESS_NAME"),
                    task: this.$i18n.t("ID_TASK"),
                    current_user: this.$i18n.t("ID_CURRENT_USER"),
                    due_date: this.$i18n.t("ID_DUE_DATE"),
                    delegation_date: this.$i18n.t("ID_DELEGATION_DATE"),
                    priority: this.$i18n.t("ID_PRIORITY"),
                    actions: "",
                },
                selectable: {
                    mode: "single",
                    only: function(row) {
                        return true;
                    },
                    selectAllMode: "page",
                    programmatic: false,
                },
                requestFunction(data) {
                    return this.$parent.$parent.getCasesForVueTable(data);
                },
                customFilters: ["myfilter"],
            },
            pmDateFormat: "Y-m-d H:i:s",
        };
    },
    watch: {
        id: function() {
            this.$refs.test.refresh();
        },
    },
    methods: {
        /**
         * Get cases data by header
         */
        getCasesForVueTable(data) {
            let that = this,
                dt,
                paged,
                limit = data.limit,
                filters = {},
                start = data.page === 1 ? 0 : limit * (data.page - 1);
            paged = start + ',' + limit;
            filters["paged"] = paged;
            return new Promise((resolutionFunc, rejectionFunc) => {
                _.forIn(this.filters, function(item, key) {
                    filters[item.filterVar] = item.value;
                });
                api.cases
                    .search(filters)
                    .then((response) => {
                        dt = that.formatDataResponse(response.data.data);
                        resolutionFunc({
                            data: dt,
                            count: response.data.total,
                        });
                    })
                    .catch((e) => {
                        rejectionFunc(e);
                    });
            });
        },
        /**
         * Format Response API TODO to grid todo and columns
         */
        formatDataResponse(response) {
            let data = [];
            _.forEach(response, (v) => {
                data.push({
                    CASE_NUMBER: v.APP_NUMBER,
                    CASE_TITLE: v.APP_TITLE,
                    PROCESS_NAME: v.PRO_TITLE,
                    TASK: {
                        TITLE: v.TAS_TITLE,
                        CODE_COLOR: v.TAS_COLOR,
                        COLOR: v.TAS_COLOR_LABEL,
                    },
                    USR_FIRSTNAME: v.USR_FIRSTNAME,
                    USR_LASTNAME: v.USR_LASTNAME,
                    USR_USERNAME: v.USR_USERNAME,
                    DUE_DATE: v.DEL_TASK_DUE_DATE,
                    DELEGATION_DATE: v.DEL_DELEGATE_DATE,
                    PRIORITY: v.DEL_PRIORITY_LABEL,
                    DEL_INDEX: v.DEL_INDEX,
                    APP_UID: v.APP_UID,
                });
            });
            return data;
        },
        /**
         * Get for user format name configured in Processmaker Environment Settings
         *
         * @param {string} name
         * @param {string} lastName
         * @param {string} userName
         * @return {string} nameFormat
         */
        nameFormatCases(name, lastName, userName) {
            let nameFormat = "";
            if (/^\s*$/.test(name) && /^\s*$/.test(lastName)) {
                return nameFormat;
            }
            if (this.nameFormat === "@firstName @lastName") {
                nameFormat = name + " " + lastName;
            } else if (this.nameFormat === "@firstName @lastName (@userName)") {
                nameFormat = name + " " + lastName + " (" + userName + ")";
            } else if (this.nameFormat === "@userName") {
                nameFormat = userName;
            } else if (this.nameFormat === "@userName (@firstName @lastName)") {
                nameFormat = userName + " (" + name + " " + lastName + ")";
            } else if (this.nameFormat === "@lastName @firstName") {
                nameFormat = lastName + " " + name;
            } else if (this.nameFormat === "@lastName, @firstName") {
                nameFormat = lastName + ", " + name;
            } else if (
                this.nameFormat === "@lastName, @firstName (@userName)"
            ) {
                nameFormat = lastName + ", " + name + " (" + userName + ")";
            } else {
                nameFormat = name + " " + lastName;
            }
            return nameFormat;
        },
        /**
         * Convert string to date format
         *
         * @param {string} value
         * @return {date} myDate
         */
        convertDate(value) {
            myDate = new Date(1900, 0, 1, 0, 0, 0);
            try {
                if (!isNaN(Date.parse(value))) {
                    var myArray = value.split(" ");
                    var myArrayDate = myArray[0].split("-");
                    if (myArray.length > 1) {
                        var myArrayHour = myArray[1].split(":");
                    } else {
                        var myArrayHour = new Array("0", "0", "0");
                    }
                    var myDate = new Date(
                        myArrayDate[0],
                        myArrayDate[1] - 1,
                        myArrayDate[2],
                        myArrayHour[0],
                        myArrayHour[1],
                        myArrayHour[2]
                    );
                }
            } catch (err) {
                throw new Error(err);
            }
            return myDate;
        },
        /**
         * Get a format for specific date
         *
         * @param {string} d
         * @return {string} dateToConvert
         */
        dateFormatCases(d) {
            let dateToConvert = d;
            const stringToDate = this.convertDate(dateToConvert);
            if (this.pmDateFormat === "Y-m-d H:i:s") {
                dateToConvert = dateFormat(stringToDate, "yyyy-mm-dd HH:MM:ss");
            } else if (this.pmDateFormat === "d/m/Y") {
                dateToConvert = dateFormat(stringToDate, "dd/mm/yyyy");
            } else if (this.pmDateFormat === "m/d/Y") {
                dateToConvert = dateFormat(stringToDate, "mm/dd/yyyy");
            } else if (this.pmDateFormat === "Y/d/m") {
                dateToConvert = dateFormat(stringToDate, "yyyy/dd/mm");
            } else if (this.pmDateFormat === "Y/m/d") {
                dateToConvert = dateFormat(stringToDate, "yyyy/mm/dd");
            } else if (this.pmDateFormat === "F j, Y, g:i a") {
                dateToConvert = dateFormat(
                    stringToDate,
                    "mmmm d, yyyy, h:MM tt"
                );
            } else if (this.pmDateFormat === "m.d.y") {
                dateToConvert = dateFormat(stringToDate, "mm.dd.yy");
            } else if (this.pmDateFormat === "j, n, Y") {
                dateToConvert = dateFormat(stringToDate, "d,m,yyyy");
            } else if (this.pmDateFormat === "D M j G:i:s T Y") {
                dateToConvert = dateFormat(
                    stringToDate,
                    "ddd mmm d HH:MM:ss Z yyyy"
                );
            } else if (this.pmDateFormat === "M d, Y") {
                dateToConvert = dateFormat(stringToDate, "mmm dd, yyyy");
            } else if (this.pmDateFormat === "m D, Y") {
                dateToConvert = dateFormat(stringToDate, "mm ddd, yyyy");
            } else if (this.pmDateFormat === "D d M, Y") {
                dateToConvert = dateFormat(stringToDate, "ddd dd mmm, yyyy");
            } else if (this.pmDateFormat === "D M, Y") {
                dateToConvert = dateFormat(stringToDate, "ddd mmm, yyyy");
            } else if (this.pmDateFormat === "d M, Y") {
                dateToConvert = dateFormat(stringToDate, "dd mmm, yyyy");
            } else if (this.pmDateFormat === "d m, Y") {
                dateToConvert = dateFormat(stringToDate, "dd mm, yyyy");
            } else if (this.pmDateFormat === "d.m.Y") {
                dateToConvert = dateFormat(stringToDate, "mm.dd.yyyy");
            } else {
                dateToConvert = dateFormat(
                    stringToDate,
                    'dd "de" mmmm "de" yyyy'
                );
            }
            return dateToConvert;
        },
        /**
         * Open selected cases in the inbox
         *
         * @param {object} item
         */
        openCase(item) {
            const action = "todo";
            if (this.isIE) {
                window.open(
                    "../../../cases/open?APP_UID=" +
                        item.row.APP_UID +
                        "&DEL_INDEX=" +
                        item.row.DEL_INDEX +
                        "&action=" +
                        action
                );
            } else {
                window.location.href =
                    "../../../cases/open?APP_UID=" +
                    item.row.APP_UID +
                    "&DEL_INDEX=" +
                    item.row.DEL_INDEX +
                    "&action=" +
                    action;
            }
        },
        /**
         * Open case detail
         *
         * @param {object} item
         */
        openCaseDetail(item) {
            this.$parent.dataCase = {
                APP_UID: item.APP_UID,
                DEL_INDEX: item.DEL_INDEX,
                APP_NUMBER: item.CASE_NUMBER
            };
            this.$parent.page = "case-detail";
        },
        onJumpCase(caseNumber) {
            const params = {
                APP_NUMBER: caseNumber,
                ACTION: "jump",
                ACTION_FROM_LIST: "search",
            };
            let self = this;
            api.cases
                .jump(params)
                .then(function(data) {
                    self.$parent.dataCase = params;
                    self.$parent.page = "XCase";
                })
                .catch((err) => {
                    throw new Error(err);
                });
        },
        /**
         * Updates the alert dismiss value to update
         * dismissCountDown and decrease
         * @param {mumber}
         */
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown;
        },
        /**
         * Show the alert message
         * @param {string} message - message to be displayen in the body
         * @param {string} type - alert type
         */
        showAlert(message, type) {  
            this.message = message;
            this.variant = type || "info";
            this.dismissCountDown = this.dismissSecs;
        },
        /**
         * Handler submit filter
         * @param {object} data - data returned from the server
         */
        onSubmitFilter(params) {
            if (params.type === "update") {
                api.filters
                    .put({
                        id: params.id,
                        name: params.name,
                        filters: JSON.stringify(params.filters),
                    })
                    .then((response) => {
                        this.$emit("onSubmitFilter", params);
                    })
                    .catch((e) => {
                        this.showAlert(e.message, "danger");
                    });
            } else {
                api.filters
                    .post({
                        name: params.name,
                        filters: JSON.stringify(params.filters),
                    })
                    .then((response) => {
                        this.$emit("onSubmitFilter", response.data);
                    })
                    .catch((e) => {
                        this.showAlert(e.message, "danger");
                    });
            }
        },
        /**
         * Handler on remove filter
         * @param {number} id - data returned fron the server
         */
        onRemoveFilter(id) {
            api.filters
                .delete({
                    id: this.id,
                })
                .then((response) => {
                    this.$emit("onUpdateFilters", {});
                    this.$emit("onRemoveFilter", id);
                })
                .catch((e) => {
                    this.showAlert(e.message, "danger");
                });
        },
        /**
         * Handler on search filter
         * @param {number} id - data returned fron the server
         */
        onSearch(params) {
            this.$nextTick(() => {
                this.$refs.test.refresh();
            });
        },
        onUpdateFilters(params) {
            this.$emit("onUpdateFilters", params);
        },
    },
};
</script>
<style>
.v-container-mycases {
    padding-top: 20px;
    padding-bottom: 20px;
    padding-left: 50px;
    padding-right: 50px;
}
</style>
