import _ from "lodash";
import api from "../../api/index";
export default {
  data() {
    let that = this;
    return {
      typeView: "GRID",
      random: 1,
      dataCasesList: [],
      defaultColumns: [
        "case_number",
        "case_title",
        "process_name",
        "task",
        "send_by",
        "due_date",
        "delegation_date",
        "priority",
      ],
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
      optionsVueView: {
        limit: 10,
        dblClick: (event, item, options) => {
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
        columns: [],
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
        typeList = that.data.pageParent == "inbox"? "todo": that.data.pageParent,
        start = 0,
        paged,
        limit = data.limit,
        filters = {},
        id = this.data.customListId;
      filters = {
          paged: paged,
          limit: limit,
          offset: start,
      };
      if (_.isEmpty(that.filters) && this.data.settings) {
          _.forIn(this.data.settings.filters, function(item, key) {
              if (filters && item.value) {
                  filters[item.filterVar] = item.value;
              }
          });
      } else {
          _.forIn(this.filters, function(item, key) {
              if (filters && item.value) {
                  filters[item.filterVar] = item.value;
              }
          });
      }
      return new Promise((resolutionFunc, rejectionFunc) => {
          api.custom[that.data.pageParent]
            ({
                id,
                filters,
            })
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
        typeList = that.data.pageParent == "inbox"? "todo": that.data.pageParent,
        limit = data.limit,
        start = data.page === 1 ? 0 : limit * (data.page - 1),
        filters = {};

      filters = {
        limit: limit,
        offset: start
      };
      _.forIn(this.filters, function (item, key) {
        if (filters && item.value) {
          filters[item.filterVar] = item.value;
        }
      });
      return new Promise((resolutionFunc, rejectionFunc) => {
        api.cases[typeList]
          (filters)
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
     * Event handler when update the settings columns
     * @param {*} columns 
     */
    onUpdateColumnSettings(columns) {
      this.columns = this.getTableColumns(columns);
      this.random = _.random(0, 10000000000);
    },
    /**
     * Get columns for origin , settings or custom cases list
     */
    getColumnsFromSource() {
      let dt = _.clone(this.dataCasesList),
        res = _.clone(this.defaultColumns);
      if (!this.data.customListId) {
        res = _.map(_.filter(dt, o => o.set), s => s.field);
      }
      return res;
    },
    /**
     * Return the columns for table - concat with field "detail" "actions"
     */
    getTableColumns(columns) {
      return _.concat(["detail"], columns, ["actions"]);
    },
    /**
     * Return options for Table
     * @returns Object
     */
    getTableOptions() {
      let dt = _.clone(this.options);
      dt.headings = _.pick(this.headings, this.columns);
      return dt;
    },
    /**
     * Return options for Table
     * @returns Object
     */
    getVueViewOptions() {
      let dt = _.clone(this.optionsVueView);
      dt.columns = this.cardColumns;
      return dt;
    },
    /**
     * Format column settings for popover
     * @param {*} headings 
     * @returns 
     */
    formatColumnSettings(columns) {
      return _.map(_.pick(this.headings, columns), (value, key) => {
        return { value, key }
      });
    }
  }
}