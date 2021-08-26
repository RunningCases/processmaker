<template>
  <div id="v-pm-task-metrics" ref="v-pm-task-metrics" class="v-pm-task-metrics">
    <button-fleft :data="newCase"></button-fleft>
    <div class="p-1 v-flex">
      <h4 class="v-search-title">
        Task metrics
        <span class="vp-padding-r3"> <i class="fas fa-chart-pie"></i></span>
      </h4>
    </div>
    <modal-new-request ref="newRequest"></modal-new-request>
    <div class="d-inline-flex p-2">
      <vue-charts
        ref="pm-vue-chart"
        @onChangeLevel="changeLevel"
        :level="level"
      />
      <div class="vp-6"></div>
      <drill-down :level="level" @onChangeLevel="updateVueChart" />
    </div>
  </div>
</template>

<script>
import ButtonFleft from "../../components/home/ButtonFleft.vue";
import ModalNewRequest from "../ModalNewRequest.vue";
import DrillDown from "./DrillDown.vue";
import VueCharts from "./VueCharts.vue";

import defaultMixins from "./defaultMixins";
export default {
  name: "TaskMetrics",
  mixins: [defaultMixins],
  components: {
    ButtonFleft,
    ModalNewRequest,
    DrillDown,
    VueCharts,
  },
  props: [],
  data() {
    let that = this;
    return {
      level: 0,
    };
  },
  created() {},
  mounted() {},
  watch: {},
  computed: {},
  updated() {},
  beforeCreate() {},
  methods: {
    /**
     * Change level in drill down
     */
    changeLevel(lv) {
      this.level = lv;
    },
    /**
     * update data in charts
     */
    updateVueChart(lv) {
      this.$refs["pm-vue-chart"].onChangeLevel(lv);
    },
  },
};
</script>
<style>
.v-pm-task-metrics {
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 50px;
  padding-right: 50px;
}

.vp-padding-r3 {
  padding-right: 3rem;
}
</style>