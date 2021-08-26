<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <div class="p-1 v-flex">
      <h6 class="v-search-title">{{$t("ID_DRILL_DOWN_NUMBER_TASKS_PROCESS_BY_TASK")}}</h6>
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
          <b-form-radio-group
            id="btn-radios"
            v-model="period"
            :options="periodOptions"
            button-variant="outline-secondary"
            size="sm"
            name="radio-btn-outline"
            buttons
            @change="changeOption"
          ></b-form-radio-group>
        </div>
      </div>
      <apexchart
        ref="LevelTwoChart"
        :width="width"
        :options="options"
        :series="series"
      ></apexchart>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Api from "../../api/index";
import Multiselect from "vue-multiselect";
import BreadCrumb from "../../components/utils/BreadCrumb.vue";
import moment from "moment";
export default {
  name: "VueChartLvTwo",
  mixins: [],
  components: {
    Multiselect,
    BreadCrumb,
  },
  props: ["data", "breadCrumbs"],
  data() {
    let that = this;
    return {
      dateFrom: "",
      dateTo: "",
      period: "",
      periodOptions: [
        { text: this.$t("ID_DAY"), value: "day" },
        { text: this.$t("ID_MONTH"), value: "month" },
        { text: this.$t("ID_YEAR"), value: "year" },
      ],
      settingsBreadcrumbs: [
        {
          class: "fas fa-info-circle",
          tooltip: this.$t("ID_TASK_RISK_LEVEL2_INFO"),
          onClick() {},
        },
      ],
      dataCasesByRange: [],
      width: 0,
      options: {
        chart: {
          type: "area",
          zoom: {
            enabled: false,
          },
          id: "LevelTwoChart",
          events: {
            markerClick: function (event, chartContext, config) {
              that.currentSelection = that.dataCasesByRange[config.seriesIndex];
              that.$emit("updateDataLevel", {
                id: that.currentSelection["PRO_ID"],
                name: that.currentSelection["PRO_TITLE"],
                level: 2,
                data: null,
              });
            },
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "smooth",
        },
        xaxis: {
          type: "datetime",
        },
        tooltip: {
          fixed: {
            enabled: false,
            position: "topRight",
          },
        },
      },
      series: [],
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
      this.width = window.innerHeight;
    },
    /**
     * Change datepickers or radio button
     */
    changeOption() {
      let that = this,
        dt;
      if (this.dateFrom && this.dateTo && this.period) {
        dt = {
          processId: this.data[1].id,
          caseList: this.data[0].id.toLowerCase(),
          dateFrom: moment(this.dateFrom).format("DD/MM/YYYY"),
          dateTo: moment(this.dateTo).format("DD/MM/YYYY"),
          groupBy: this.period,
        };
        Api.process
          .totalCasesByRange(dt)
          .then((response) => {
            that.formatDataRange(response.data);
          })
          .catch((e) => {
            console.error(e);
          });
      }
    },
    /**
     * Format response fromn API
     */
    formatDataRange(data) {
      let labels = [],
        serie = [];

      this.dataCasesByRange = data;
      _.each(data, (el) => {
        serie.push(el["TOTAL"]);
        labels.push(el["dateGroup"]);
      });
      this.$refs["LevelTwoChart"].updateOptions({
        labels: labels,
        title: {
          text: this.data[0]["PRO_TITLE"],
          align: "left",
        },
      });
      this.$apexcharts.exec("LevelTwoChart", "updateSeries", [
        {
          name: this.data[0]["PRO_TITLE"],
          data: serie,
        },
      ]);
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
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>