<template>
  <div id="v-pm-charts" ref="v-pm-charts" class="v-pm-charts vp-inline-block">
    <vue-chart-lv-zero
      v-show="level === 0"
      @updateDataLevel="updateDataLevel"
      :breadCrumbs="dataBreadCrumbs()"
    />
    <vue-chart-lv-one
      :key="key1"
      v-show="level === 1"
      :data="data"
      @onChangeLevel="onChangeLevel"
      @updateDataLevel="updateDataLevel"
      :breadCrumbs="dataBreadCrumbs()"
    />
    <vue-chart-lv-two
      v-show="level === 2"
      :data="data"
      @onChangeLevel="onChangeLevel"
      :breadCrumbs="dataBreadCrumbs()"
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
      key1: 1,
      data: [],
      settingsBreadCrumbs: [
        {
          class: "fas fa-info-circle",
          onClick() {},
        },
      ],
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
      this.$emit("onChangeLevel", data.level + 1);
      this.updateKey();
    },
    updateKey() {
      switch (this.level) {
        case 0:
          break;
        case 1:
          this.key1++;
          break;
        case 2:
          break;
        case 3:
          break;
      }
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
      _.remove(this.data, function (n) {
        return n.level >= lv;
      });
      this.level = lv;
      this.$emit("onChangeLevel", this.level);
    },
    dataBreadCrumbs() {
      let res = [],
        that = this,
        index = 0;
      _.each(this.data, (el) => {
        if (index <= that.level) {
          res.push({
            label: el.name,
            onClick() {
              that.onChangeLevel(el.level);
            },
          });
        }
      });
      res.push({
        label: "Select the drill option",
        onClick() {},
      });
      switch (this.level) {
        case 0:
          return {
            data: res,
            settings: this.settingsBreadCrumbs,
          };
          break;
        default:
          return {
            data: res,
            settings: this.settingsBreadCrumbs,
          };
          break;
      }
    },
  },
};
</script>
<style>
</style>