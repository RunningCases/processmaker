import api from "../../api/index";
export default {
  data() {
    let that = this;
    return {
      typeView: "GRID",
      random: 1,
      dataMultiviewHeader: {
        actions: [
          {
            id: "view-grid",
            title: "Grid",
            onClick(action) {
              that.typeView = "GRID";
            },
            icon: "fas fa-table",
          },
          {
            id: "view-list",
            title: "List",
            onClick(action) {
              that.typeView = "LIST";
            },
            icon: "fas fa-list",
          },
          {
            id: "view-card",
            title: "Card",
            onClick(action) {
              that.typeView = "CARD";
            },
            icon: "fas fa-th",
          },
        ],
      },
      optionsVueList: {
        limit: 10,
        dblClick:(event, item, options)=>{
          this.openCase(item);
        },
        headings: {
          case_number: this.$i18n.t("ID_MYCASE_NUMBER"),
          case_title: this.$i18n.t("ID_CASE_TITLE"),
          process_name: this.$i18n.t("ID_PROCESS_NAME"),
          task: this.$i18n.t("ID_TASK"),
          send_by: this.$i18n.t("ID_SEND_BY"),
          current_user: this.$i18n.t("ID_CURRENT_USER"),
          due_date: this.$i18n.t("ID_DUE_DATE"),
          delegation_date: this.$i18n.t("ID_DELEGATION_DATE"),
          priority: this.$i18n.t("ID_PRIORITY")
        },
        columns: [
          "case_number",
          "case_title",
          "process_name",
          "due_date",
          "delegation_date",
          "priority",
          "task",
          "send_by",
        ],
        requestFunction(data) {
          return that.getCases(data);
        },
        requestFunctionViewMore(data) {
          return that.getCasesViewMore(data);
        }
      }
    }
  },
  created: function () {

  },
  methods: {
    /**
    * Get cases for Vue Card View
    */
    getCases(data) {
      let that = this,
        dt,
        start = 0,
        limit = data.limit,
        filters = {};
      filters = {
        paged: "0," + limit,
      };

      _.forIn(this.filters, function (item, key) {
          if(filters && item.value) {
              filters[item.filterVar] = item.value;
          }
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
    * Get cases for Vue Card View
    */
    getCasesViewMore(data) {
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
     * Format columns for custom columns
     * @param {*} headings 
     * @returns 
     */
    formatColumnSettings(headings) {
      let res=[];
      _.forEach(headings, function(value, key) {
        if(key != "actions"){
            res.push({value,key});
        }
      });
      return res;
    },
    /**
     * Formating the columns selected
     * @param {*} columns 
     * @returns 
     */
    formatColumnSelected(columns) {
      let cols = _.clone(columns);
      cols.pop();
      return cols;
    },
    /**
     * Event handler when update the settings columns
     * @param {*} columns 
     */
    onUpdateColumnSettings(columns) {
      let cols = columns;
      if(_.findIndex(cols, 'actions') == -1){
        cols.push("actions");
      }
      this.columns = cols;
      this.random = _.random(0, 10000000000);
    }
  }
}