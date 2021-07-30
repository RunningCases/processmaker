<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <div class="p-1 v-flex">
      <h6 class="v-search-title">Number of tasks per Task Status</h6>
      <apexchart
        v-show="typeView === 'donut'"
        ref="apexchart1"
        :width="width"
        :options="chartOptions1"
        :series="series1"
      ></apexchart>
      <apexchart
        v-show="typeView === 'bar'"
        ref="apexchart2"
        :width="width"
        :options="chartOptions2"
        :series="series2"
      ></apexchart>

      <div class="row">
        <div class="col-sm vp-center">
          <button
            @click="changeView('donut')"
            type="button"
            class="btn btn-primary"
          >
            <i class="fas fa-chart-pie"></i
            ><span class="vp-padding-l10">View</span>
          </button>
        </div>
        <div class="col-sm">
          <button
            @click="changeView('bar')"
            type="button"
            class="btn btn-primary"
          >
            <i class="fas fa-chart-bar"></i
            ><span class="vp-padding-l10">View</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Api from "../../api/index";
export default {
  name: "VueChartLvOne",
  mixins: [],
  components: {},
  props: [],
  data() {
    let that = this;
    return {
      typeView: "bar",
      width: 0,
      series1: [],
      chartOptions1: {
        labels: ["Team A", "Team B", "Team C", "Team D", "Team E"],
        chart: {
          id: "apexchart1",
          type: "pie",
        },
      },
      series2: [
        {
          name: "Inflation",
          data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2],
        },
      ],
      chartOptions2: {
        chart: {
          id: "apexchart2",
          type: "bar",
        },
        plotOptions: {
          bar: {
            borderRadius: 10,
            dataLabels: {
              position: "top", // top, center, bottom
            },
          },
        },
        dataLabels: {
          enabled: true,
          formatter: function (val) {
            return val + "%";
          },
          offsetY: -20,
          style: {
            fontSize: "12px",
            colors: ["#304758"],
          },
        },

        xaxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ],
          position: "top",
          axisBorder: {
            show: false,
          },
          axisTicks: {
            show: false,
          },
          crosshairs: {
            fill: {
              type: "gradient",
              gradient: {
                colorFrom: "#D8E3F0",
                colorTo: "#BED1E6",
                stops: [0, 100],
                opacityFrom: 0.4,
                opacityTo: 0.5,
              },
            },
          },
          tooltip: {
            enabled: true,
          },
        },
        yaxis: {
          axisBorder: {
            show: false,
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            formatter: function (val) {
              return val + "%";
            },
          },
        },
        title: {
          text: "Monthly Inflation in Argentina, 2002",
          floating: true,
          offsetY: 330,
          align: "center",
          style: {
            color: "#444",
          },
        },
      },
    };
  },
  created() {},
  mounted() {
    this.getBodyHeight();
    this.getDataDonut();
    this.getData();
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
      this.width = window.innerHeight * 0.8;
    },
    /**
     * Change view - donut/bar
     */
    changeView(view) {
      this.typeView = view;
      if (view == "donut") {
        this.getDataDonut();
      } else {
        //this.getDataBar();
      }
    },
    getDataDonut() {
      this.chartOptions1 = {
        labels: ["Team A", "Team B", "Team C", "Team D", "Team E"],
        chart: {
          id: "apexchart1",
          type: "donut",
        },
        responsive: [
          {
            breakpoint: 480,
            options: {
              chart: {
                width: 200,
              },
              legend: {
                position: "bottom",
              },
            },
          },
        ],
      };
      this.series1 = [44, 55, 41, 17, 15];
    },
    getDataBar() {
      this.chartOptions2 = {
        chart: {
          id: "apexchart2",
          type: "bar",
        },
        plotOptions: {
          bar: {
            borderRadius: 10,
            dataLabels: {
              position: "top", // top, center, bottom
            },
          },
        },
        dataLabels: {
          enabled: true,
          formatter: function (val) {
            return val + "%";
          },
          offsetY: -20,
          style: {
            fontSize: "12px",
            colors: ["#304758"],
          },
        },

        xaxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ],
          position: "top",
          axisBorder: {
            show: false,
          },
          axisTicks: {
            show: false,
          },
          crosshairs: {
            fill: {
              type: "gradient",
              gradient: {
                colorFrom: "#D8E3F0",
                colorTo: "#BED1E6",
                stops: [0, 100],
                opacityFrom: 0.4,
                opacityTo: 0.5,
              },
            },
          },
          tooltip: {
            enabled: true,
          },
        },
        yaxis: {
          axisBorder: {
            show: false,
          },
          axisTicks: {
            show: false,
          },
          labels: {
            show: false,
            formatter: function (val) {
              return val + "%";
            },
          },
        },
        title: {
          text: "Monthly Inflation in Argentina, 2002",
          floating: true,
          offsetY: 330,
          align: "center",
          style: {
            color: "#444",
          },
        },
      };
      /*
      this.$apexcharts.exec("apexchart2", "updateSeries", [
        {
          name: "Inflation",
          data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2],
        },
      ]);*/

      /*this.series2 = [
        {
          name: "Inflation",
          data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2],
        },
      ];*/
    },
    getData() {
      Api.cases
        .listTotalCases()
        .then((response) => {
          console.log("response");
          console.log(response);
        })
        .catch((response) => {
          console.log("error");
          console.log(response);
        });
    },
  },
};
</script>
<style>
.vp-center {
  text-align: center;
}

.vp-padding-l10 {
  padding-left: 10px;
}
</style>