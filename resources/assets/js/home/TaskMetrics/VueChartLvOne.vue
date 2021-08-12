<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <div class="p-1 v-flex">
      <h6 class="v-search-title">Number of Tasks Status per Process</h6>
      <div>
        <BreadCrumb :options="dataBreadCrumbs(data)" />
        <ProcessPopover
          :options="optionsProcesses"
          target="pm-task-process"
          ref="pm-task-process"
          @onUpdateColumnSettings="onUpdateColumnSettings"
        />
        <div class="vp-width-p40 vp-inline-block">
          <multiselect
            v-model="category"
            :options="optionsCategory"
            :searchable="false"
            :close-on-select="false"
            :show-labels="false"
            track-by="id"
            label="name"
            @select="changeOption"
          ></multiselect>
        </div>
        <label class="vp-inline-block vp-padding-l20">{{
          $t("ID_MAFE_a4ffdcf0dc1f31b9acaf295d75b51d00")
        }}</label>
        <div class="vp-inline-block">
          <b-form-checkbox v-model="top" name="check-button" switch>
          </b-form-checkbox>
        </div>
        <div class="vp-inline-block vp-right vp-padding-r40">
          <h4
            class="v-search-title"
            @click="showProcessesPopover"
            id="pm-task-process"
          >
            <i class="fas fa-cog"></i>
          </h4>
        </div>
      </div>
      <apexchart
        ref="LevelOneChart"
        :width="width"
        :options="optionsBar"
        :series="seriesBar"
      ></apexchart>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Api from "../../api/index";
import BreadCrumb from "../../components/utils/BreadCrumb.vue";
import ProcessPopover from "./ProcessPopover.vue";
import Multiselect from "vue-multiselect";

export default {
  name: "VueChartLvOne",
  mixins: [],
  components: {
    Multiselect,
    BreadCrumb,
    ProcessPopover,
  },
  props: ["data"],
  data() {
    let that = this;
    return {
      category: null,
      dataProcesses: null, //Data API processes

      optionsCategory: [],
      optionsProcesses: [],
      selectedProcesses: [],
      top: false,
      width: 0,
      totalCases: [],
      currentSelection: null,
      seriesBar: [
        {
          data: [],
        },
      ],
      optionsBar: {
        chart: {
          type: "bar",
          id: "LevelOneChart",
          toolbar: {
            show: false,
          },
          events: {
            legendClick: function (chartContext, seriesIndex, config) {
              that.currentSelection = that.totalCases[seriesIndex];
              console.log("LEGENDDDDDDDDDDDDD");
              that.$emit("updateDataLevel", {
                id: that.currentSelection["PRO_ID"],
                name: that.currentSelection["PRO_TITLE"],
                level: 1,
                data: that.currentSelection,
              });
            },
          },
        },
        plotOptions: {
          bar: {
            barHeight: "100%",
            distributed: true,
            horizontal: true,
          },
        },
        legend: {
          position: "left",
        },
        colors: ["#33b2df", "#546E7A", "#d4526e", "#13d8aa"],
        dataLabels: {
          enabled: false,
        },
        xaxis: {
          categories: [],
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
    this.getCategories();
    this.getProcesses();
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
     * Get Categories form API
     */
    getCategories() {
      let that = this;
      Api.filters
        .categories()
        .then((response) => {
          that.formatDataCategories(response.data);
        })
        .catch((e) => {
          console.error(err);
        });
    },
    /**
     * Get Processes form API
     */
    getProcesses() {
      let that = this;
      Api.filters
        .processList("")
        .then((response) => {
          that.formatDataProcesses(response.data);
          that.changeOption({
            id: 0,
          });
        })
        .catch((e) => {
          console.error(err);
        });
    },
    /**
     * Format categories for multiselect
     */
    formatDataCategories(data) {
      let array = [];
      array.push({
        name: "No Categories",
        id: "0",
      });
      _.each(data, (el) => {
        array.push({ name: el["CATEGORY_NAME"], id: el["CATEGORY_ID"] });
      });
      this.optionsCategory = array;
      this.category = array[0];
    },
    /**
     * Format processes for popover
     */
    formatDataProcesses(data) {
      let sels = [],
        labels = [],
        array = [];

      _.each(data, (el) => {
        array.push({ value: el["PRO_TITLE"], key: el["PRO_ID"] });
        sels.push(el["PRO_ID"]);
        labels;
      });
      this.optionsProcesses = array;
      this.selectedProcesses = sels;

      //Update the labels
      this.dataProcesses = data;
      this.updateLabels(data);
      console.log("aaaaaaaaaaaaaaaaa aaaaaaa");
    },
    /**
     * Change the options in TOTAL CASES BY PROCESS
     */
    changeOption(option) {
      let that = this,
        dt = {};
      if (this.data.length > 0) {
        dt = {
          category: option.id,
          caseList: this.data[0].id.toLowerCase(),
          processes: this.selectedProcesses,
        };
        Api.process
          .totalCasesByProcess(dt)
          .then((response) => {
            that.totalCases = response.data;
            that.formatTotalCases(response.data);
          })
          .catch((e) => {
            console.error(err);
          });
      }
    },
    /**
     * Show the processes popover
     */
    showProcessesPopover() {
      this.$root.$emit("bv::show::popover", "pm-task-process");
      this.$refs["pm-task-process"].setOptions(this.optionsProcesses);
      this.$refs["pm-task-process"].setSelectedOptions(this.selectedProcesses);
    },
    formatTotalCases(data) {
      let serie = [],
        labels = [];
      _.each(data, (el) => {
        serie.push(el["TOTAL"]);
        labels.push(el["PRO_TITLE"]);
      });
      
      this.$refs["LevelOneChart"].updateOptions({ labels: labels });
      
      this.$apexcharts.exec("LevelOneChart", "updateSeries", [
        {
          data: serie,
        },
      ]);
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
          label: el.id,
          onClick() {
            that.$emit("onChangeLevel", el.level);
          },
        });
      });
      return res;
    },
    /**
     * Update list processes in chart
     */
    onUpdateColumnSettings(data) {
      let res;
      this.selectedProcesses = data;
      res = _.intersectionBy(this.totalCases, data, (el) => {
        if (_.isNumber(el)) {
          return el;
        }
        if (_.isObject(el) && el["PRO_ID"]) {
          return el["PRO_ID"];
        }
      });
      this.formatTotalCases(res);
    },
    updateLabels(processes) {
      let labels = [];
      _.each(processes, (el) => {
        labels.push(el["PRO_TITLE"]);
      });
      this.$refs["LevelOneChart"].updateOptions({ labels: labels });
    },
    updateSerie(processes) {
      let labels = [];
      _.each(processes, (el) => {
        labels.push(el["TOTAL"]);
      });
      this.$refs["LevelOneChart"].updateOptions({ labels: labels });
    },
    
  },
};
</script>
<style>
.vp-task-metrics-label {
  display: inline-block;
}

.vp-width-p40 {
  width: 40%;
}

.vp-inline-block {
  display: inline-block;
}

.vp-padding-l20 {
  padding-left: 20px;
}

.vp-padding-r40 {
  padding-right: 40px;
}

.vp-right {
  float: right;
}
</style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>