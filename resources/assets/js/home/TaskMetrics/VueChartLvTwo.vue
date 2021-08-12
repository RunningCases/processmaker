<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <div class="p-1 v-flex">
      <h6 class="v-search-title">Number of Tasks Status per Process</h6>
      <div>
        <BreadCrumb :options="dataBreadCrumbs(data)" />
        <div class="vp-width-p30 vp-inline-block">
          <b-form-datepicker id="date-from" size="sm"></b-form-datepicker>
        </div>
        <div class="vp-width-p30 vp-inline-block">
          <b-form-datepicker id="date-to" size="sm"></b-form-datepicker>
        </div>
        <div class="vp-inline-block">
          <b-button-group size="sm">
            <b-button variant="outline-secondary">{{ $t("ID_DAY") }}</b-button>
            <b-button variant="outline-secondary">{{
              $t("ID_MONTH")
            }}</b-button>
            <b-button variant="outline-secondary">{{ $t("ID_YEAR") }}</b-button>
          </b-button-group>
        </div>
      </div>
      <apexchart
        ref="apexchart1"
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

export default {
  name: "VueChartLvOne",
  mixins: [],
  components: {
    Multiselect,
    BreadCrumb,
  },
  props: ["data"],
  data() {
    let that = this;
    return {
      category: null,
      optionsCategory: [],
      top: false,
      width: 0,

      series: [
        {
          name: "TEAM 1",
          data: this.generateDayWiseTimeSeries(
            new Date("11/02/2017").getTime(),
            20,
            {
              min: 10,
              max: 60,
            }
          ),
        },
        {
          name: "TEAM 2",
          data: this.generateDayWiseTimeSeries(
            new Date("11 Feb 2017 GMT").getTime(),
            20,
            {
              min: 10,
              max: 60,
            }
          ),
        },
        {
          name: "TEAM 3",
          data: this.generateDayWiseTimeSeries(
            new Date("11 Feb 2017 GMT").getTime(),
            30,
            {
              min: 10,
              max: 60,
            }
          ),
        },
        {
          name: "TEAM 4",
          data: this.generateDayWiseTimeSeries(
            new Date("11 Feb 2017 GMT").getTime(),
            10,
            {
              min: 10,
              max: 60,
            }
          ),
        },
        {
          name: "TEAM 5",
          data: this.generateDayWiseTimeSeries(
            new Date("11 Feb 2017 GMT").getTime(),
            30,
            {
              min: 10,
              max: 60,
            }
          ),
        },
      ],
      options: {
        chart: {
          height: 350,
          type: "scatter",
          zoom: {
            type: "xy",
          },
        },
        dataLabels: {
          enabled: false,
        },
        grid: {
          xaxis: {
            lines: {
              show: true,
            },
          },
          yaxis: {
            lines: {
              show: true,
            },
          },
        },
        xaxis: {
          type: "datetime",
        },
        yaxis: {
          max: 70,
        },
      },
    };
  },
  created() {},
  mounted() {
    this.getBodyHeight();
    this.getCategories();
    //this.getDataDonut();
    //this.getData();
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
     * Change view - donut/bar
     */
    changeView(view) {
      this.typeView = view;
      this.getData();
    },
    /**
     * Get data from rest API
     */
    getData() {
      let that = this;
      Api.cases
        .listTotalCases()
        .then((response) => {
          that.formatData(response.data);
        })
        .catch((response) => {});
    },
    /**
     * Format the data for chart
     */
    formatData(data) {
      let l = [],
        c = [],
        s = [];
      _.each(data, (el) => {
        l.push(el["List Name"]);
        s.push(el["Total"]);
        if (el["Color"] == "green") {
          c.push("#179a6e");
        }
        if (el["Color"] == "yellow") {
          c.push("#feb019");
        }
        if (el["Color"] == "blue") {
          c.push("#008ffb");
        }
        if (el["Color"] == "gray") {
          c.push("#8f99a0");
        }
      });
      this.seriesDonut = s;
      this.seriesBar = [
        {
          data: s,
        },
      ];
      this.$refs["apexchart1"].updateOptions({ labels: l, colors: c });
      this.$refs["apexchart2"].updateOptions({ labels: l, colors: c });
      this.$apexcharts.exec("apexchart1", "updateSeries", s);
      this.$apexcharts.exec("apexchart2", "updateSeries", [
        {
          data: s,
        },
      ]);
    },
    getCategories() {
      let that = this;
      console.log("jonas");
      Api.filters
        .categories()
        .then((response) => {
          that.formatDataCategories(response.data);
        })
        .catch((e) => {
          console.error(err);
        });
    },
    formatDataCategories(data) {
      let array = [];
      _.each(data, (el) => {
        array.push({ name: el["CATEGORY_NAME"], id: el["CATEGORY_ID"] });
      });
      this.optionsCategory = array;
      this.category = array[0];
    },
    changeOption(option) {
      console.log("asda sdas d");
      let dt = {
        category: option.id,
        caseList:
          this.data && this.data.dataLv0
            ? this.data.dataLv0["List Name"].toLowerCase()
            : "inbox",
      };

      Api.process
        .processTotalCases(dt)
        .then((response) => {
          console.log("asda sdas d11111111111111");
          console.log(response);
        })
        .catch((e) => {
          console.error(err);
        });
    },
    dataBreadCrumbs(options) {
      let res = [],
        that = this;
      res.push({
        label: "Start",
        onClick() {
          console.log("STARTTTTTTTTTT");
          that.$emit("onChangeLevel", 0);
        },
      });
      _.each(options, (el) => {
        res.push({
          label: el.name,
          onClick() {
            that.$emit("onChangeLevel", el.level);
          },
        });
      });
      return res;
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