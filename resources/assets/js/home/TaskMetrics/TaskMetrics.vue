<template>
  <div id="v-pm-task-metrics" ref="v-pm-task-metrics" class="v-pm-task-metrics">
    <button-fleft :data="newCase"></button-fleft>
    <div class="p-1 v-flex">
      <h5 class="v-search-title">Task metrics</h5>
    </div>
    <modal-new-request ref="newRequest"></modal-new-request>
    <div class="d-inline-flex p-2">
      <vue-charts />
      <div class="vp-6"></div>
      <drill-down />
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
    return {};
  },
  created() {},
  mounted() {},
  watch: {},
  computed: {},
  updated() {},
  beforeCreate() {},
  methods: {},
};
</script>
<style>
.v-pm-task-metrics {
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 50px;
  padding-right: 50px;
}

.vp-6 {
  padding: 3.5rem !important;
}
</style>