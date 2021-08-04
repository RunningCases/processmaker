<template>
  <div
    id="v-pm-charts"
    ref="v-pm-charts"
    class="v-pm-charts vp-inline-block"
  >
  <vue-chart-lv-one/>
  </div>
</template>

<script>
import VueChartLvZero from "./VueChartLvZero.vue";
import VueChartLvOne from "./VueChartLvOne.vue";

export default {
  name: "VueCharts",
  mixins: [],
  components: {
    VueChartLvZero,
    VueChartLvOne,
  },
  props: [],
  data() {
    let that = this;
    return {
      width: 0,
    };
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
</style>