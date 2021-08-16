<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <div class="p-1 v-flex">
      <h6 class="v-search-title">Number of Tasks Status per Process</h6>
      <div>
        <BreadCrumb
          :options="breadCrumbs.data"
          :settings="breadCrumbs.settings"
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
      dataCasesByRange: [],
      width: 0,
      series: [
        {
          name: "Process",
          data: [],
        },
      ],
      options: {
        chart: {
          type: "area",
          zoom: {
            enabled: false,
          },
          id: "LevelTwoChart",
        },
        dataLabels: {
          enabled: true,
        },
        stroke: {
          curve: "straight",
        },

        title: {
          text: "",
          align: "left",
        },
        labels: [],
        xaxis: {
          type: "datetime",
        },
        yaxis: {
          opposite: false,
        },
        legend: {
          horizontalAlign: "left",
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
            console.error(err);
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
      console.log("DRWAWWW");
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
    generateDayWiseTimeSeries(baseval, count, yrange) {
      var i = 0;
      var series = [];
      while (i < count) {
        var y =
          Math.floor(Math.random() * (yrange.max - yrange.min + 1)) +
          yrange.min;

        series.push([baseval, y]);
        baseval += 86400000;
        i++;
      }
      console.log(series);
      return series;
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