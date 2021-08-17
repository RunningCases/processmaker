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
          <label class="form-label">{{
            $t("ID_MAFE_a4ffdcf0dc1f31b9acaf295d75b51d00")
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
          ></multiselect>
        </div>
      </div>
      <apexchart
        ref="LevelThreeChart"
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
  name: "VueChartLvThree",
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
      size: "all",
      sizeOptions: [
        { name: this.$t("ID_ALL"), id: "all" },
        { name: "5", id: "5" },
        { name: "10", id: "10" },
        { name: "15", id: "15" },
        { name: "20", id: "20" },
      ],
      dataCasesByRange: [],
      width: 0,
      series: [
        {
          name: "SAMPLE A",
          data: [
            [16.4, 5.4],
            [21.7, 2],
            [25.4, 3],
            [19, 2],
            [10.9, 1],
            [13.6, 3.2],
            [10.9, 7.4],
            [10.9, 0],
            [10.9, 8.2],
            [16.4, 0],
            [16.4, 1.8],
            [13.6, 0.3],
            [13.6, 0],
            [29.9, 0],
            [27.1, 2.3],
            [16.4, 0],
            [13.6, 3.7],
            [10.9, 5.2],
            [16.4, 6.5],
            [10.9, 0],
            [24.5, 7.1],
            [10.9, 0],
            [8.1, 4.7],
            [19, 0],
            [21.7, 1.8],
            [27.1, 0],
            [24.5, 0],
            [27.1, 0],
            [29.9, 1.5],
            [27.1, 0.8],
            [22.1, 2],
          ],
        },
        {
          name: "SAMPLE B",
          data: [
            [36.4, 13.4],
            [1.7, 11],
            [5.4, 8],
            [9, 17],
            [1.9, 4],
            [3.6, 12.2],
            [1.9, 14.4],
            [1.9, 9],
            [1.9, 13.2],
            [1.4, 7],
            [6.4, 8.8],
            [3.6, 4.3],
            [1.6, 10],
            [9.9, 2],
            [7.1, 15],
            [1.4, 0],
            [3.6, 13.7],
            [1.9, 15.2],
            [6.4, 16.5],
            [0.9, 10],
            [4.5, 17.1],
            [10.9, 10],
            [0.1, 14.7],
            [9, 10],
            [12.7, 11.8],
            [2.1, 10],
            [2.5, 10],
            [27.1, 10],
            [2.9, 11.5],
            [7.1, 10.8],
            [2.1, 12],
          ],
        },
        {
          name: "SAMPLE C",
          data: [
            [21.7, 3],
            [23.6, 3.5],
            [24.6, 3],
            [29.9, 3],
            [21.7, 20],
            [23, 2],
            [10.9, 3],
            [28, 4],
            [27.1, 0.3],
            [16.4, 4],
            [13.6, 0],
            [19, 5],
            [22.4, 3],
            [24.5, 3],
            [32.6, 3],
            [27.1, 4],
            [29.6, 6],
            [31.6, 8],
            [21.6, 5],
            [20.9, 4],
            [22.4, 0],
            [32.6, 10.3],
            [29.7, 20.8],
            [24.5, 0.8],
            [21.4, 0],
            [21.7, 6.9],
            [28.6, 7.7],
            [15.4, 0],
            [18.1, 0],
            [33.4, 0],
            [16.4, 0],
          ],
        },
      ],
      options: {
        chart: {
          height: 350,
          type: "scatter",
          zoom: {
            enabled: true,
            type: "xy",
          },
        },
        xaxis: {
          tickAmount: 10,
          labels: {
            formatter: function (val) {
              return parseFloat(val).toFixed(1);
            },
          },
        },
        yaxis: {
          tickAmount: 7,
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