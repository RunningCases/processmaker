<template>
  <div id="v-todo" ref="v-todo" class="v-container-todo">
    <button-fleft :data="newCase"></button-fleft>
    <modal-new-request ref="newRequest"></modal-new-request>
    <CasesFilter
      :filters="filters"
      @onRemoveFilter="onRemoveFilter"
      @onUpdateFilters="onUpdateFilters"
    />
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

      <div slot="task" slot-scope="props">
        <TaskCell :data="props.row.TASK" />
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
      <div slot="priority" slot-scope="props">{{ props.row.PRIORITY }}</div>
      <div slot="actions" slot-scope="props">
        <button class="btn btn-success btn-sm" @click="openCase(props.row)">
          {{ $t("ID_OPEN_CASE") }}
        </button>
      </div>
    </v-server-table>
  </div>
</template>

<script>
import HeaderCounter from "../components/home/HeaderCounter.vue";
import ButtonFleft from "../components/home/ButtonFleft.vue";
import ModalNewRequest from "./ModalNewRequest.vue";
import TaskCell from "../components/vuetable/TaskCell.vue";
import CasesFilter from "../components/search/CasesFilter";
import api from "./../api/index";

export default {
  name: "Todo",
  components: {
    HeaderCounter,
    ButtonFleft,
    ModalNewRequest,
    TaskCell,
    CasesFilter,
  },
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
        "current_user",
        "due_date",
        "delegation_date",
        "priority",
        "actions",
      ],
      tableData: [],
      filters: {},
      options: {
        filterable: false,
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
    };
  },
  mounted() {},
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
          .todo(filters)
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
          PRO_UID: v.PRO_UID,
          TAS_UID: v.TAS_UID,
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
      } else if (this.nameFormat === "@lastName, @firstName (@userName)") {
        nameFormat = lastName + ", " + name + " (" + userName + ")";
      } else {
        nameFormat = name + " " + lastName;
      }
      return nameFormat;
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
        ACTION: "todo",
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
    onRemoveFilter(data) {},
    onUpdateFilters(data) {
      this.filters = data.params;
      if (data.refresh) {
        this.$nextTick(() => {
          this.$refs["vueTable"].getData();
        });
      }
    },
  },
};
</script>
<style>
.v-container-todo {
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 50px;
  padding-right: 50px;
}
.VueTables__limit {
  display: none;
}
</style>