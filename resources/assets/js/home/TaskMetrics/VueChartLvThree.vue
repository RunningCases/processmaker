<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <div class="p-1 v-flex">
      <h6 class="v-search-title">{{$t("ID_DRILL_DOWN_RISK_MATRIX")}}</h6>
      <div>
        <BreadCrumb
          :options="breadCrumbs.data"
          :settings="settingsBreadcrumbs"
        />
        <div class="vp-width-p30 vp-inline-block">
          <b-form-datepicker
            id="date-from"
            :date-format-options="{
              year: '2-digit',
              month: '2-digit',
              day: '2-digit',
            }"
            size="sm"
            :placeholder="$t('ID_DELEGATE_DATE_FROM')"
            v-model="dateFrom"
            @input="changeOption"
          ></b-form-datepicker>
        </div>
        <div class="vp-width-p30 vp-inline-block">
          <b-form-datepicker
            id="date-to"
            size="sm"
            :date-format-options="{
              year: '2-digit',
              month: '2-digit',
              day: '2-digit',
            }"
            :placeholder="$t('ID_DELEGATE_DATE_TO')"
            v-model="dateTo"
            @input="changeOption"
          ></b-form-datepicker>
        </div>
        <div class="vp-inline-block">
          <label class="form-label">{{
            $t("ID_TOP")
          }}</label>
        </div>
        <div class="vp-inline-block">
          <multiselect
            v-model="size"
            :options="sizeOptions"
            :searchable="false"
            :close-on-select="true"
            :show-labels="false"
            track-by="id"
            label="name"
            @input="changeOption"
          ></multiselect>
        </div>
      </div>
      <apexchart
        ref="LevelThreeChart"
        :width="width"
        :options="options"
        :series="series"
      ></apexchart>
      <div class="vp-width-p100">
        <div class="vp-text-align-center" role="group">
          <button
            type="button"
            @click="
              riskType = 'ON_TIME';
              changeOption();
            "
            class="btn vp-btn-success btn-sm"
          >
            On Time
          </button>
          <button
            type="button"
            @click="
              riskType = 'AT_RISK';
              changeOption();
            "
            class="btn vp-btn-warning btn-sm"
          >
            At Risk
          </button>
          <button
            type="button"
            @click="
              riskType = 'OVERDUE';
              changeOption();
            "
            class="btn vp-btn-danger btn-sm"
          >
            Overdue
          </button>
        </div>
      </div>
      <div class="vp-width-p100 vp-text-align-center">
        <label class="vp-form-label">
          {{ $t("ID_TODAY") }} : {{ dateNow }}
        </label>
      </div>
      <ModalUnpauseCase ref="modal-unpause-case"></ModalUnpauseCase>
      <ModalClaimCase ref="modal-claim-case"></ModalClaimCase>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Api from "../../api/index";
import Multiselect from "vue-multiselect";
import BreadCrumb from "../../components/utils/BreadCrumb.vue";
import moment from "moment";
import ModalUnpauseCase from "../modal/ModalUnpauseCase.vue";
import ModalClaimCase from "../modal/ModalClaimCase.vue";

export default {
  name: "VueChartLvThree",
  mixins: [],
  components: {
    Multiselect,
    BreadCrumb,
    ModalUnpauseCase,
    ModalClaimCase,
  },
  props: ["data", "breadCrumbs"],
  data() {
    let that = this;
    return {
      currentSelection: null,
      dateFrom: moment().format("YYYY-MM-DD"),
      dateTo: moment().add(30, "d").format("YYYY-MM-DD"),
      dateNow: "",
      size: { name: this.$t("ID_ALL"), id: "all" },
      riskType: "ON_TIME",
      settingsBreadcrumbs: [
        {
          class: "fas fa-info-circle",
          tooltip: this.$t("ID_TASK_RISK_LEVEL3_INFO"),
          onClick() {},
        },
      ],
      sizeOptions: [
        { name: this.$t("ID_ALL"), id: "all" },
        { name: "5", id: "5" },
        { name: "10", id: "10" },
        { name: "15", id: "15" },
        { name: "20", id: "20" },
      ],
      dataCasesByRisk: [],
      width: 0,
      series: [],
      options: {
        chart: {
          height: 350,
          type: "bubble",
          zoom: {
            enabled: true,
            type: "xy",
          },
          id: "LevelThreeChart",
          events: {
            markerClick: function (event, chartContext, config) {
              that.currentSelection = that.dataCasesByRisk[config.seriesIndex];
              that.onClickCaseMarker(that.currentSelection);
            },
          },
        },
        legend: {
          show: false,
        },
        dataLabels: {
          enabled: true,
          formatter: function (val, opt) {
            if (that.dataCasesByRisk.length > 0) {
              return that.dataCasesByRisk[opt["seriesIndex"]]["number_case"];
            }
            return "";
          },
          offsetX: 0,
        },
        xaxis: {
          type: "datetime",
        },
        yaxis: {
          tickAmount: 7,
        },
        tooltip: {
          custom: function ({ series, seriesIndex, dataPointIndex, w }) {
            return that.customTooltip(series, seriesIndex, dataPointIndex, w);
          },
        },
      },
    };
  },
  created() {},
  mounted() {
    this.getBodyHeight();
  },
  watch: {},
  computed: {},
  updated() {},
  beforeCreate() {},
  methods: {
    /**
     * Return the height for Vue Card View body
     */
    getBodyHeight() {
      this.width = window.innerHeight * 0.95;
    },
    /**
     * Change datepickers or radio button
     */
    changeOption() {
      let that = this,
        dt;
      if (this.dateFrom && this.dateTo) {
        dt = {
          process: this.data[1].id,
          caseList: this.data[0].id.toLowerCase(),
          dateFrom: moment(this.dateFrom).format("DD/MM/YYYY"),
          dateTo: moment(this.dateTo).format("DD/MM/YYYY"),
          riskStatus: this.riskType,
        };
        this.size.id != "all" ? (dt["topCases"] = this.size.id) : null;
        this.dateNow = moment().format("DD/MM/YYYY h:mm:ss a");
        Api.process
          .totalCasesByRisk(dt)
          .then((response) => {
            that.formatDataRange(response.data);
          })
          .catch((e) => {
            console.error(err);
          });
      }
    },
    /**
     * Format response fromn API
     * @param {object} data
     */
    formatDataRange(data) {
      let that = this,
        serie = [];
      this.dataCasesByRisk = data;
      _.each(data, (el) => {
        //Format the response to risk type Overdue/At risk/ On time
        switch (that.riskType) {
          case "OVERDUE":
            serie.push({
              name: el["number_case"].toString(),
              data: [
                [moment(el["due_date"]).toDate().getTime(), el["days"], 20],
              ],
            });
            break;
          case "AT_RISK":
            serie.push({
              name: el["number_case"].toString(),
              data: [
                [moment(el["delegated"]).toDate().getTime(), -el["days"], 20],
              ],
            });
            break;
          case "ON_TIME":
            serie.push({
              name: el["number_case"].toString(),
              data: [
                [moment(el["delegated"]).toDate().getTime(), -el["days"], 20],
              ],
            });
            break;
        }
      });

      this.updateApexchartAxis();
      if (this.data[0].id.toLowerCase() == "draft") {
        this.series = []; // Draft is empty
      } else {
        this.series = serie;
      }
    },
    /**
     * Update axis chart
     */
    updateApexchartAxis() {
      switch (this.riskType) {
        case "OVERDUE":
          this.$refs["LevelThreeChart"].updateOptions({
            yaxis: {
              min: 0,
              tickAmount: 7,
            },
            title: {
              text: "Overdue days",
            },
          });
          break;
        case "AT_RISK":
          this.$refs["LevelThreeChart"].updateOptions({
            yaxis: {
              max: 0,
              tickAmount: 7
            },
            title: {
              text: "Days before being Overdue",
            },
          });
          break;
        case "ON_TIME":
          this.$refs["LevelThreeChart"].updateOptions({
            yaxis: {
              max: 0,
              tickAmount: 7
            },
            title: {
              text: "Days before being At-Risk",
            },
          });
          break;
      }
    },
    /**
     * Create custom tooltip
     */
    customTooltip(series, seriesIndex, dataPointIndex, w) {
      let obj = this.dataCasesByRisk[seriesIndex];
      return `<div class="apexcharts-theme-light">
                  <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                    ${"Number Case"} : ${obj["number_case"]}
                  </div>
                  <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                  <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                  <div class="apexcharts-tooltip-y-group">
                    <span class="" style="background-color: #28a745;"></span>
                    <span class="apexcharts-tooltip-text-y-label">Delegated</span> : <span class="apexcharts-tooltip-text-y-value">${
                      obj["delegated"]
                    }</span>
                  </div>
                   <div class="apexcharts-tooltip-y-group">
                    <span class="" style="background-color: #28a745;"></span>
                    <span class="apexcharts-tooltip-text-y-label">At Risk</span> : <span class="apexcharts-tooltip-text-y-value">${
                      obj["at_risk"]
                    }</span>
                  </div>
                   <div class="apexcharts-tooltip-y-group">
                    <span class="" style="background-color: #28a745;"></span>
                    <span class="apexcharts-tooltip-text-y-label">Due Date</span> : <span class="apexcharts-tooltip-text-y-value">${
                      obj["due_date"]
                    }</span>
                  </div>
                </div>
              </div>
            </div>`;
    },
    /**
     * Open selected cases in the inbox
     * @param {object} item
     */
    openCase(item) {
      this.$parent.$parent.$emit("onUpdateDataCase", {
        APP_UID: item.APP_UID,
        DEL_INDEX: item.DEL_INDEX,
        PRO_UID: item.PRO_UID,
        TAS_UID: item.TAS_UID,
        ACTION: "todo",
      });
      this.$parent.$parent.$emit("onUpdatePage", "XCase");
    },
    /**
     * Click in marker chart
     * @param {object} selection
     */
    onClickCaseMarker(selection) {
      let process = this.data[1].id,
        caseList = this.data[0].id.toLowerCase();
      switch (caseList) {
        case "inbox":
        case "draft":
          this.openCase({
            APP_UID: selection["app_uid"],
            DEL_INDEX: selection["del_index"],
            PRO_UID: process,
            TAS_UID: selection["tas_uid"],
          });
          break;
        case "paused":
          this.showModalUnpauseCase({
            APP_UID: selection["app_uid"],
            DEL_INDEX: selection["del_index"],
            PRO_UID: process,
            TAS_UID: selection["tas_uid"],
          });
          break;
        case "unassigned":
          this.showModalClaimCase({
            APP_UID: selection["app_uid"],
            DEL_INDEX: selection["del_index"],
            PRO_UID: process,
            TAS_UID: selection["tas_uid"],
          });
          break;
      }
    },
    /**
     * Show modal unpause
     * @param {object} item
     */
    showModalUnpauseCase(item) {
      this.$refs["modal-unpause-case"].data = item;
      this.$refs["modal-unpause-case"].show();
    },
    /**
     * Claim case
     * @param {object} item
     */
    showModalClaimCase(item) {
      let that = this;
      api.cases.open(_.extend({ ACTION: "unassigned" }, item)).then(() => {
        api.cases.cases_open(_.extend({ ACTION: "todo" }, item)).then(() => {
          that.$refs["modal-claim-case"].data = item;
          that.$refs["modal-claim-case"].show();
        });
      });
    },
  },
};
</script>
<style>
.vp-task-metrics-label {
  display: inline-block;
}

.vp-width-p30 {
  width: 30%;
}

.vp-inline-block {
  display: inline-block;
}

.vp-padding-l20 {
  padding-left: 20px;
}

.vp-width-p100 {
  width: 100%;
}

.vp-text-align-center {
  text-align: center;
}

.vp-btn-success {
  color: #fff;
  background-color: #368b48;
  border-color: #368b48;
}
.vp-btn-success:hover {
  color: #fff;
  background-color: #255a30;
  border-color: #368b48;
}

.vp-btn-warning {
  color: #fff;
  background-color: #c99e11;
  border-color: #a1831d;
}
.vp-btn-warning:hover {
  color: #fff;
  background-color: #886c0e;
  border-color: #a1831d;
}
.vp-btn-danger {
  color: #fff;
  background-color: #b63b32;
  border-color: #b63b32;
}

.vp-btn-danger:hover {
  color: #fff;
  background-color: #832923;
  border-color: #b63b32;
}

.vp-form-label {
  display: inline-block;
  font-family: Helvetica, Arial, sans-serif;
  text-anchor: start;
  font-size: 14px;
  font-weight: 900;
  fill: rgb(15 17 18);
  margin-top: 0.5rem;
  color: #7d7f93;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>