<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <div class="p-1 v-flex">
      <h6 class="v-search-title">Number of tasks per Task Status</h6>
      <BreadCrumb
        :options="breadCrumbs.data"
        :settings="breadCrumbs.settings"
      />
      <apexchart
        v-show="typeView === 'donut'"
        ref="apexchart1"
        :width="width"
        :options="optionsDonut"
        :series="seriesDonut"
      ></apexchart>
      <apexchart
        v-show="typeView === 'bar'"
        ref="apexchart2"
        :width="width"
        :options="optionsBar"
        :series="seriesBar"
      ></apexchart>

      <div class="row">
        <div class="col-sm vp-align-right">
          <button
            @click="changeView('donut')"
            type="button"
            class="btn btn-primary"
          >
            <i class="fas fa-chart-pie"></i
            ><span class="vp-padding-l10">{{ $t("ID_VIEW") }}</span>
          </button>
        </div>
        <div class="col-sm">
          <button
            @click="changeView('bar')"
            type="button"
            class="btn btn-primary"
          >
            <i class="fas fa-chart-bar"></i
            ><span class="vp-padding-l10">{{ $t("ID_VIEW") }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Api from "../../api/index";
import BreadCrumb from "./../../components/utils/BreadCrumb.vue";
export default {
  name: "VueChartLvZero",
  mixins: [],
  components: { BreadCrumb },
  props: ["breadCrumbs"],
  data() {
    let that = this;
    return {
      typeView: "donut",
      width: 0,
      data: [],
      currentSelection: null,
      seriesDonut: [],
      optionsDonut: {
        labels: [
          this.$i18n.t("ID_INBOX"),
          this.$i18n.t("ID_DRAFT"),
          this.$i18n.t("ID_PAUSED"),
          this.$i18n.t("ID_UNASSIGNED"),
        ],
        chart: {
          id: "apexchart1",
          type: "donut",
          events: {
            legendClick: function (chartContext, seriesIndex, config) {
              that.currentSelection = that.data[seriesIndex];
              that.$emit("updateDataLevel", {
                id: that.currentSelection["List Name"],
                name: that.currentSelection["List Name"],
                level: 0,
                data: that.currentSelection,
              });
            },
          },
        },
        legend: {
          position: "top",
          offsetY: 0,
        },
      },
      seriesBar: [
        {
          data: [400, 430, 448, 470],
        },
      ],
      optionsBar: {
        chart: {
          type: "bar",
          id: "apexchart2",
          toolbar: {
            show: false,
          },
          events: {
            legendClick: function (chartContext, seriesIndex, config) {
              that.currentSelection = that.data[seriesIndex];
              that.$emit("updateDataLevel", {
                id: that.currentSelection["List Name"],
                name: that.currentSelection["List Name"],
                level: 0,
                data: that.currentSelection,
              });
            },
          },
        },
        plotOptions: {
          bar: {
            barHeight: "100%",
            distributed: true,
          },
        },
        legend: {
          position: "top",
          offsetY: 0,
        },
        colors: ["#33b2df", "#546E7A", "#d4526e", "#13d8aa"],
        dataLabels: {
          enabled: false,
        },
        xaxis: {
          categories: [
            this.$i18n.t("ID_INBOX"),
            this.$i18n.t("ID_DRAFT"),
            this.$i18n.t("ID_PAUSED"),
            this.$i18n.t("ID_UNASSIGNED"),
          ],
        },
        tooltip: {
          x: {
            show: false,
          },
          y: {
            title: {
              formatter: function () {
                return "";
              },
            },
          },
        },
      },
    };
  },
  created() {},
  mounted() {
    this.getBodyHeight();
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
      this.data = data;
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
  },
};
</script>
<style>
.vp-center {
  text-align: center;
}

.vp-align-right {
  text-align: right;
}

.vp-padding-l10 {
  padding-left: 10px;
}
</style>