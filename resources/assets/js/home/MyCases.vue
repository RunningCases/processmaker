<template>
    <div id="v-mycases" ref="v-mycases" class="v-container-mycases">
        <button-fleft :data="newCase"></button-fleft>
        <MyCasesFilter
            :filters="filters"
            :title="title"
            @onRemoveFilter="onRemoveFilter"
            @onUpdateFilters="onUpdateFilters"
        />
        <header-counter :data="headers"> </header-counter>
        <modal-new-request ref="newRequest"></modal-new-request>

        <v-server-table
            :data="tableData"
            :columns="columns"
            :options="options"
            ref="vueTable"
        >
            <div slot="detail" slot-scope="props">
                <div class="btn-default" @click="openCaseDetail(props.row)">
                <i class="fas fa-info-circle"></i>
                </div>
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
            <div slot="pending_taks" slot-scope="props">
                <GroupedCell :data="props.row.PENDING_TASKS" />
            </div>
            <div slot="status" slot-scope="props">{{ props.row.STATUS }}</div>
            <div slot="start_date" slot-scope="props">
                {{ props.row.START_DATE }}
            </div>
            <div slot="finish_date" slot-scope="props">
                {{ props.row.FINISH_DATE }}
            </div>
            <div slot="duration" slot-scope="props">
                {{ props.row.DURATION }}
            </div>
            <div slot="actions" slot-scope="props">
                <div class="btn-default"  v-bind:style="{ color: props.row.MESSAGE_COLOR}" @click="openComments(props.row)">
                    <span class="fas fa-comments"></span>
                </div>
            </div>
        </v-server-table>
        <ModalComments ref="modal-comments" @postNotes="onPostNotes"></ModalComments>
    </div>
</template>

<script>
import HeaderCounter from "../components/home/HeaderCounter.vue";
import ButtonFleft from "../components/home/ButtonFleft.vue";
import ModalNewRequest from "./ModalNewRequest.vue";
import MyCasesFilter from "../components/search/MyCasesFilter";
import ModalComments from "./modal/ModalComments.vue";
import GroupedCell from "../components/vuetable/GroupedCell.vue";
import api from "./../api/index";

export default {
    name: "MyCases",
    components: {
        MyCasesFilter,
        HeaderCounter,
        ButtonFleft,
        ModalNewRequest,
        GroupedCell,
        ModalComments,
    },
    props: ["filters"],
    data() {
        return {
            metrics: [],
            title: this.$i18n.t('ID_MY_CASES'),
            filter: "CASES_INBOX",
            allView: [],
            filterHeader: "STARTED",
            headers: [],
            newCase: {
                title: "New Case",
                class: "btn-success",
                onClick: () => {
                    this.$refs["newRequest"].show();
                },
            },
            columns: [
                "detail",
                "case_number",
                "case_title",
                "process_name",
                "pending_taks",
                "status",
                "start_date",
                "finish_date",
                "duration",
                "actions",
            ],
            tableData: [],
            options: {
                filterable: false,
                headings: {
                    case_number: this.$i18n.t("ID_MYCASE_NUMBER"),
                    case_title: this.$i18n.t("ID_CASE_TITLE"),
                    process_name: this.$i18n.t("ID_PROCESS_NAME"),
                    pending_taks: this.$i18n.t("ID_PENDING_TASKS"),
                    status: this.$i18n.t("ID_CASESLIST_APP_STATUS"),
                    start_date: this.$i18n.t("ID_START_DATE"),
                    finish_date: this.$i18n.t("ID_FINISH_DATE"),
                    duration: this.$i18n.t("ID_DURATION"),
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
            },
            translations: null,
            pmDateFormat: window.config.FORMATS.dateFormat
        };
    },
    mounted() {
        this.getHeaders();
        // force to open start cases modal
        // if the user has start case as a default case menu option
        if (window.config._nodeId === "CASES_START_CASE") {
            this.$refs["newRequest"].show();
        }
    },
    watch: {},
    computed: {
        /**
         * Build our ProcessMaker apiClient
         */
        ProcessMaker() {
            return window.ProcessMaker;
        },
    },
    updated() {},
    beforeCreate() {},
    methods: {
        /**
         * Open case detail
         *
         * @param {object} item
         */
        openCaseDetail(item) {
            let that = this;
            api.cases.cases_open(_.extend({ ACTION: "todo" }, item)).then(() => {
                that.$emit("onUpdateDataCase", {
                APP_UID: item.APP_UID,
                DEL_INDEX: item.DEL_INDEX,
                PRO_UID: item.PRO_UID,
                TAS_UID: item.TAS_UID,
                APP_NUMBER: item.CASE_NUMBER,
                });
                that.$emit("onUpdatePage", "case-detail");
            });
        },
        /**
         * Get Cases Headers from BE
         */
        getHeaders() {
            let that = this;
            api.casesHeader.get().then((response) => {
                that.headers = that.formatCasesHeaders(response.data);
            });
        },
        /**
         * Get cases data by header
         */
        getCasesForVueTable(data) {
            let that = this,
                dt,
                paged,
                limit = data.limit,
                start = data.page === 1 ? 0 : limit * (data.page - 1),
                filters = {};
                paged = start + "," + limit;
                
            filters = {
                filter: that.filterHeader,
                paged: paged,
            };
            _.forIn(this.filters, function(item, key) {
                filters[item.filterVar] = item.value;
            });
            return new Promise((resolutionFunc, rejectionFunc) => {
                api.cases
                    .myCases(filters)
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
         * Format Response API TODO to grid inbox and columns
         */
        formatDataResponse(response) {
            let that = this,
                data = [];
            _.forEach(response, (v) => {
                data.push({
                    CASE_NUMBER: v.APP_NUMBER,
                    CASE_TITLE: v.DEL_TITLE,
                    PROCESS_NAME: v.PRO_TITLE,
                    STATUS: v.APP_STATUS,
                    START_DATE: v.APP_CREATE_DATE_LABEL || "",
                    FINISH_DATE: v.APP_FINISH_DATE_LABEL || "",
                    PENDING_TASKS: that.formantPendingTask(v.PENDING),
                    DURATION: v.DURATION,
                    DEL_INDEX: v.DEL_INDEX,
                    APP_UID: v.APP_UID,
                    PRO_UID: v.PRO_UID,
                    TAS_UID: v.TAS_UID,
                    MESSAGE_COLOR: v.CASE_NOTES_COUNT > 0 ? "black":"silver"
                });
            });
            return data;
        },
        /**
         * Format data for pending task.
         */
        formantPendingTask(data) {
            var i,
                dataFormat = [];
            for (i = 0; i < data.length; i += 1) {
                dataFormat.push(
                    {
                        TAS_NAME: data[i].tas_title,
                        STATUS: data[i].tas_color,
                        PENDING: ""
                    }
                );
            }
            return dataFormat;
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
         * Format Response from HEADERS
         * @param {*} response
         */
        formatCasesHeaders(response) {
            let data = [],
                that = this,
                info = {
                    STARTED: {
                        icon: "fas fa-inbox",
                        class: "btn-primary",
                    },
                    COMPLETED: {
                        icon: "fas fa-check-square",
                        class: "btn-success",
                    },
                    IN_PROGRESS: {
                        icon: "fas fa-tasks",
                        class: "btn-danger",
                    },
                    SUPERVISING: {
                        icon: "fas fa-binoculars",
                        class: "btn-warning",
                    },
                };
            _.forEach(response, (v) => {
                data.push({
                    title: v.title,
                    counter: v.counter,
                    item: v.id,
                    icon: info[v.id].icon,
                    onClick: (obj) => {
                        that.title = obj.title;
                        that.filterHeader = obj.item;
                        that.$refs["vueTable"].getData();
                    },
                    class: info[v.id].class,
                });
            });
            return data;
        },
        /**
         * Open the case notes modal
         * @param {object} data - needed to create the data
         */
        openComments(data) {
            let that = this;
            api.cases.open(_.extend({ ACTION: "todo" }, data)).then(() => {
                that.$refs["modal-comments"].dataCase = data;
                that.$refs["modal-comments"].show();
            });
        },
        onRemoveFilter(data) {
        },
        onUpdateFilters(data) {
            this.$emit("onUpdateFilters", data.params);
            if (data.refresh) {
                this.$nextTick(() => {
                    this.$refs["vueTable"].getData();
                });
            }
        },
        /**
         * Post notes event handler
         */
        onPostNotes() {
            this.$refs["vueTable"].getData();
        }
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
