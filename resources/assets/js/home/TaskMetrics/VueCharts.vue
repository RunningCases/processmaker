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
      :key="key2"
      v-show="level === 2"
      :data="data"
      @onChangeLevel="onChangeLevel"
      @updateDataLevel="updateDataLevel"
      :breadCrumbs="dataBreadCrumbs()"
    />
    <vue-chart-lv-three
      :key="key3"
      v-show="level === 3"
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
import VueChartLvThree from "./VueChartLvThree.vue";
import _ from "lodash";

export default {
  name: "VueCharts",
  mixins: [],
  components: {
    VueChartLvZero,
    VueChartLvOne,
    VueChartLvTwo,
    VueChartLvThree,
  },
  props: [],
  data() {
    let that = this;
    return {
      level: 0,
      key1: _.random(0, 100),
      key2: _.random(0, 100),
      key3: _.random(0, 100),
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
          this.key2++;
          break;
        case 3:
          this.key3++;
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
    /**
     * Change level with changes in data
     * @param {object} lv
     */
    onChangeLevel(lv) {
      _.remove(this.data, function (n) {
        return n.level >= lv;
      });
      this.level = lv;
      this.$emit("onChangeLevel", this.level);
    },
    /**
     * Format data for data beadcrumbs
     */
    dataBreadCrumbs() {
      let res = [],
        that = this,
        index = 0;
      _.each(this.data, (el) => {
        if (index <= that.level && el.data) {
          res.push({
            label: el.name,
            onClick() {
              that.onChangeLevel(el.level);
            },
            data: el,
          });
        }
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