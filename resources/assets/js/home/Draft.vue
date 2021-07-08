<template>
  <div id="v-draft" ref="v-draft" class="v-container-draft">
    <button-fleft :data="newCase"></button-fleft>
    <modal-new-request ref="newRequest"></modal-new-request>
    <CasesFilter
      :filters="filters"
      :title="$t('ID_DRAFT')"
      @onRemoveFilter="onRemoveFilter"
      @onUpdateFilters="onUpdateFilters"
    />
    <v-server-table
      :data="tableData"
      :columns="columns"
      :options="options"
      ref="vueTable"
      @row-click="onRowClick"
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
      <div slot="task" slot-scope="props">
        <TaskCell :data="props.row.TASK" />
      </div>
      <div slot="priority" slot-scope="props">{{ props.row.PRIORITY }}</div>
      <div slot="actions" slot-scope="props">
        <div @click="updateDataEllipsis(props.row)">
          <ellipsis v-if="dataEllipsis" :data="dataEllipsis"> </ellipsis>
        </div>
      </div>
    </v-server-table>
  </div>
</template>

<script>
import HeaderCounter from "../components/home/HeaderCounter.vue";
import ButtonFleft from "../components/home/ButtonFleft.vue";
import ModalNewRequest from "./ModalNewRequest.vue";
import CasesFilter from "../components/search/CasesFilter";
import TaskCell from "../components/vuetable/TaskCell.vue";
import api from "./../api/index";
import utils from "./../utils/utils";
import Ellipsis from '../components/utils/ellipsis.vue';

export default {
  name: "Draft",
  components: {
    HeaderCounter,
    ButtonFleft,
    ModalNewRequest,
    TaskCell,
    CasesFilter,
    Ellipsis,
  },
  props: ["defaultOption", "filters"],
  data() {
    return {
      newCase: {
        title: this.$i18n.t("ID_NEW_CASE"),
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
        "task",
        "priority",
        "actions"
      ],
      tableData: [],
      options: {
        filterable: false,
        headings: {
          detail: "",
          case_number: this.$i18n.t("ID_MYCASE_NUMBER"),
          case_title: this.$i18n.t("ID_CASE_TITLE"),
          process_name: this.$i18n.t("ID_PROCESS_NAME"),
          task: this.$i18n.t("ID_TASK"),
          actions: ""
        },
        selectable: {
          mode: "single",
          only: function (row) {
            return true;
          },
          selectAllMode: "page",
          programmatic: false,
        },
        requestFunction(data) {
          return this.$parent.$parent.getCasesForVueTable(data);
        },
        texts: {
            count:this.$i18n.t("ID_SHOWING_FROM_RECORDS_COUNT"),
            first: this.$i18n.t("ID_FIRST"),
            last: this.$i18n.t("ID_LAST"),
            filter: this.$i18n.t("ID_FILTER") + ":",
            limit: this.$i18n.t("ID_RECORDS") + ":",
            page: this.$i18n.t("ID_PAGE") + ":",
            noResults: this.$i18n.t("ID_NO_MATCHING_RECORDS")
        }
      },
      pmDateFormat: "Y-m-d H:i:s",
      clickCount: 0,  
      singleClickTimer: null,
      statusTitle: {
          "ON_TIME": this.$i18n.t("ID_IN_PROGRESS"),
          "OVERDUE": this.$i18n.t("ID_TASK_OVERDUE"),
          "DRAFT": this.$i18n.t("ID_IN_DRAFT"),
          "PAUSED": this.$i18n.t("ID_PAUSED"),
          "UNASSIGNED": this.$i18n.t("ID_UNASSIGNED")
      },
      dataEllipsis: null,
    };
  },
  created() {
    this.initFilters();
  },
  mounted() {
    this.openDefaultCase();
    this.setDataEllipsis();
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
     * Initialize filters
     */
    initFilters() {
       let params;
        if(this.defaultOption) {
            params = utils.getAllUrlParams(this.defaultOption);
              if (params && params.openapplicationuid) {
                this.$emit("onUpdateFilters",[
                    {
                        fieldId: "caseNumber",
                        filterVar: "caseNumber",
                        label: "",
                        options:[],
                        value: params.openapplicationuid,
                        autoShow: false
                    }
                ]);
              }
        }
    },
    /**
     * Open a case when the component was mounted
     */
    openDefaultCase() {
        let params;
        if(this.defaultOption) {
            params = utils.getAllUrlParams(this.defaultOption);
            if (params && params.app_uid && params.del_index) {
                this.openCase({
                    APP_UID: params.app_uid,
                    DEL_INDEX: params.del_index
                });
              this.$emit("cleanDefaultOption");
            }
            //force to search in the parallel tasks
            if (params && params.openapplicationuid) {
                this.onUpdateFilters({
                        params: [
                            {
                                fieldId: "caseNumber",
                                filterVar: "caseNumber",
                                label: "",
                                options:[],
                                value: params.openapplicationuid,
                                autoShow: false
                            }
                        ],
                        refresh: false
                });
                this.$emit("cleanDefaultOption");                
            }
        }
    },
    /**
     * On row click event handler
     * @param {object} event
     */
    onRowClick(event) {
        let self = this;
        self.clickCount += 1;
        if (self.clickCount === 1) {
            self.singleClickTimer = setTimeout(function() {
                self.clickCount = 0;            
            }, 400);
        } else if (self.clickCount === 2) {
            clearTimeout(self.singleClickTimer);
            self.clickCount = 0;
            self.openCase(event.row);
        }
    },
    /**
     * Get cases todo data
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
        paged: paged,
      };

      _.forIn(this.filters, function (item, key) {
        filters[item.filterVar] = item.value;
      });
      return new Promise((resolutionFunc, rejectionFunc) => {
        api.cases
          .draft(filters)
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
          CASE_TITLE: v.DEL_TITLE,
          PROCESS_NAME: v.PRO_TITLE,
          TASK: [{
            TITLE: v.TAS_TITLE,
            CODE_COLOR: v.TAS_COLOR,
            COLOR: v.TAS_COLOR_LABEL,
            DELAYED_TITLE: v.TAS_STATUS === "OVERDUE" ?
              this.$i18n.t("ID_DELAYED") + ":" : this.statusTitle[v.TAS_STATUS],
            DELAYED_MSG: v.TAS_STATUS === "OVERDUE" ? v.DELAY : ""
          }],
          PRIORITY: v.DEL_PRIORITY_LABEL,
          PRO_UID: v.PRO_UID,
          TAS_UID: v.TAS_UID,
          DEL_INDEX: v.DEL_INDEX,
          APP_UID: v.APP_UID
        });
      });
      return data;
    },
    /**
     * Open selected cases in the inbox
     *
     * @param {object} item
     */
    openCase(item) {
      this.$emit("onUpdateDataCase", {
        APP_UID: item.APP_UID,
        DEL_INDEX: item.DEL_INDEX,
        PRO_UID: item.PRO_UID,
        TAS_UID: item.TAS_UID,
        ACTION: "draft"
      });
      this.$emit("onUpdatePage", "XCase");
    },
    /**
     * Open case detail from draft
     *
     * @param {object} item
     */
    openCaseDetail(item) {
      let that = this;
      api.cases.open(_.extend({ ACTION: "todo" }, item)).then(() => {
        api.cases.cases_open(_.extend({ ACTION: "todo" }, item)).then(() => {
          that.$emit("onUpdateDataCase", {
            APP_UID: item.APP_UID,
            DEL_INDEX: item.DEL_INDEX,
            PRO_UID: item.PRO_UID,
            TAS_UID: item.TAS_UID,
            APP_NUMBER: item.CASE_NUMBER,
            ACTION: "draft"
          });
          that.$emit("onUpdatePage", "case-detail");
        });
      });
    },
    onRemoveFilter(data) {},
    onUpdateFilters(data) {
      this.$emit("onUpdateFilters", data.params);
      if (data.refresh) {
        this.$nextTick(() => {
          this.$refs["vueTable"].getData();
        });
      }
    },
    /**
     * update view in component
     */
    updateView(){
      this.$refs["vueTable"].getData();
    },
    /**
     * set data by default in the ellipsis component 
     */
    setDataEllipsis() {
      this.dataEllipsis = {
        showNote: false,
        showReassign: false,
        showPause: false,
        showPlay: false,
        showOpen: false,
        showClaim: false
      }
    },
    /**
     * 
     */
    updateDataEllipsis(data) {
      this.dataEllipsis = {
        APP_UID: data.APP_UID || "",
        PRO_UID: data.PRO_UID || "",
        showOpen: true,
        showNote: true,
        showPlay: false,
        showReassign: false,
        showPause: false,
        showClaim: false
      };
    }
  },
};
</script>
<style>
.v-container-draft {
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 50px;
  padding-right: 50px;
}
</style>