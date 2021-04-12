<template>
  <div id="v-unassigned" ref="v-unassigned" class="v-container-unassigned">
    <button-fleft :data="newCase"></button-fleft>
    <modal-new-request ref="newRequest"></modal-new-request>
    <CasesFilter
      :filters="filters"
      :title="$t('ID_UNASSIGNED')"
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
      <div slot="due_date" slot-scope="props">
        {{ props.row.DUE_DATE }}
      </div>
      <div slot="delegation_date" slot-scope="props">
        {{ props.row.DELEGATION_DATE }}
      </div>
      <div slot="priority" slot-scope="props">{{ props.row.PRIORITY }}</div>
      <div slot="actions" slot-scope="props">
        <button class="btn btn-success btn-sm" @click="claimCase(props.row)">
          {{ $t("ID_CLAIM") }}
        </button>
      </div>
    </v-server-table>
    <ModalClaimCase ref="modal-claim-case"></ModalClaimCase>
  </div>
</template>

<script>
import HeaderCounter from "../components/home/HeaderCounter.vue";
import ButtonFleft from "../components/home/ButtonFleft.vue";
import ModalNewRequest from "./ModalNewRequest.vue";
import TaskCell from "../components/vuetable/TaskCell.vue";
import CasesFilter from "../components/search/CasesFilter";
import ModalClaimCase from "./modal/ModalClaimCase.vue";
import api from "./../api/index";
import utils from "./../utils/utils";

export default {
  name: "Unassigned",
  components: {
    HeaderCounter,
    ButtonFleft,
    ModalNewRequest,
    TaskCell,
    ModalClaimCase,
    CasesFilter,
  },
  props: ["defaultOption", "filters"],
  data() {
    return {
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
        "task",
        "due_date",
        "delegation_date",
        "priority",
        "actions",
      ],
      tableData: [],
      options: {
        filterable: false,
        sendInitialRequest: false,
        headings: {
          case_number: this.$i18n.t("ID_MYCASE_NUMBER"),
          case_title: this.$i18n.t("ID_CASE_TITLE"),
          process_name: this.$i18n.t("ID_PROCESS_NAME"),
          task: this.$i18n.t("ID_TASK"),
          current_user: this.$i18n.t("ID_CURRENT_USER"),
          due_date: this.$i18n.t("ID_DUE_DATE"),
          delegation_date: this.$i18n.t("ID_DELEGATION_DATE"),
          priority: this.$i18n.t("ID_PRIORITY"),
          actions: "",
          detail: "",
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
      }
    };
  },
  mounted() {
    this.initFilters();
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
     * updates the filters if there is an appUid parameter
     */
    initFilters() {
       let params,
       filter = {refresh: true};
        if(this.defaultOption) {
            params = utils.getAllUrlParams(this.defaultOption);
            if (params && params.openapplicationuid) {
                filter = {
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
                    refresh: true
                };
            }
            this.$emit("cleanDefaultOption");
        }
        this.onUpdateFilters(filter);
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
            self.claimCase(event.row);
        }
    },
    /**
     * Get cases unassigned data
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

      _.forIn(this.$parent.filters, function (item, key) {
        filters[item.filterVar] = item.value;
      });
      return new Promise((resolutionFunc, rejectionFunc) => {
        api.cases
          .unassigned(filters)
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
          DUE_DATE: v.DEL_TASK_DUE_DATE_LABEL,
          DELEGATION_DATE: v.DEL_DELEGATE_DATE_LABEL,
          PRIORITY: v.DEL_PRIORITY_LABEL,
          PRO_UID: v.PRO_UID,
          TAS_UID: v.TAS_UID,
          DEL_INDEX: v.DEL_INDEX,
          APP_UID: v.APP_UID,
        });
      });
      return data;
    },
    /**
     * Claim case
     *
     * @param {object} item
     */
    claimCase(item) {
      let that = this;
      api.cases.open(_.extend({ ACTION: "unassigned" }, item)).then(() => {
        api.cases.cases_open(_.extend({ ACTION: "todo" }, item)).then(() => {
          that.$refs["modal-claim-case"].data = item;
          that.$refs["modal-claim-case"].show();
        });
      });
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
        ACTION: "todo"
      });
      this.$emit("onUpdatePage", "XCase");
    },
    /**
     * Open case detail
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
          });
          that.$emit("onUpdatePage", "case-detail");
        });
      });
    },
    onRemoveFilter(data) {},
    onUpdateFilters(data) {
      if (data.params) {
        this.$emit("onUpdateFilters", data.params);
      }
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
    }
  },
};
</script>
<style>
.v-container-unassigned {
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 50px;
  padding-right: 50px;
}
</style>