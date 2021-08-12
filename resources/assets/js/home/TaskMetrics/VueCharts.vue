<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <vue-chart-lv-zero
      v-show="level === 0"
      @updateDataLevel="updateDataLevel"
    />
    <vue-chart-lv-one
      v-if="level === 1"
      :data="data"
      @onChangeLevel="onChangeLevel"
      @updateDataLevel="updateDataLevel"
    />
    <vue-chart-lv-two
      v-if="level === 2"
      :data="data"
      @onChangeLevel="onChangeLevel"
    />
  </div>
</template>

<script>
import VueChartLvZero from "./VueChartLvZero.vue";
import VueChartLvOne from "./VueChartLvOne.vue";
import VueChartLvTwo from "./VueChartLvTwo.vue";

export default {
  name: "VueCharts",
  mixins: [],
  components: {
    VueChartLvZero,
    VueChartLvOne,
    VueChartLvTwo,
  },
  props: [],
  data() {
    let that = this;
    return {
      level: 0,
      data: [],
      dataBreadCrumbs: [],
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
     * Set data level 0
     */
    updateDataLevel(data) {
      this.data.push(data);
      this.level = data.level + 1;
      this.$emit("onChangeLevel", this.level);
    },

    /**
     * Format data to vue charts any level
     */
    formatData() {
      return {
        level: this.level,
        data: this.data,
      };
    },
    onChangeLevel(lv) {
      console.log("leveeeeeeeeeeeeeee");
      this.level = lv;
      this.$emit("onChangeLevel", this.level);
    },
  },
};
</script>
<style>
</style>