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
        :levels="visited"
      />
      <div class="vp-6"></div>
      <drill-down :visited="visited" @onChangeLevel="changeLevel" />
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
  props: ["settings"],
  data() {
    let that = this;
    return {
      visited: this.settings && this.settings.visited ? this.settings.visited : [
          {
              level: 0,
              active: true,
              id: _.random(0,100),
          }
      ]
    };
  },
  created() {},
  mounted() {},
  computed: {},
  updated() {},
  beforeCreate() {},
  methods: {
    /**
     * Change level in drill down
     */
    changeLevel(data) {
      let item  = _.find(this.visited, data);
      this.visited.forEach(function (elem) {
          elem.active = false;
      });
      if(!item) {
        data.active = true;
        this.visited = _.filter(this.visited, function(o) { 
          return o.level < data.level; 
        });
        this.visited.push(data);
      } else {
        item.active = true;
      }
      this.$emit("updateSettings", {
        data: this.visited,
        key: "visited",
        page: "task-metrics",
        type: "normal",
        id: this.id
      });
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