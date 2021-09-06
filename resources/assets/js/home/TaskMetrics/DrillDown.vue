<template>
  <div
    id="v-pm-drill-down"
    ref="v-pm-drill-down"
    class="v-pm-drill-down vp-inline-block"
  >
    <div class="p-1 v-flex">
      <h6 class="v-search-title">{{ $t("ID_DRILL_DOWN_NAVIGATOR") }}</h6>
    </div>
    <div
      v-for="item in loadItems(data, level)"
      :key="item.content"
      class="vp-padding-b10"
      @click="onClick(item)"
    >
      <span class="vp-inline-block vp-padding-r10 vp-font-size-r1">
        {{ item.label }}
      </span>
      <div class="vp-inline-block">
        <span :class="item.classObject"> {{ item.content }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
export default {
  name: "DrillDown",
  mixins: [],
  components: {},
  props: ["level"],
  data() {
    let that = this;
    return {
      classObject: {
        "rounded-circle": true,
        "v-pm-drill-down-number": true,
        "vp-btn-secondary": true,
        "btn-primary": false,
        "vp-block": true,
      },
      data: [
        {
          label: that.$t("ID_LEVEL"),
          content: "0",
          click() {
            that.$emit("onChangeLevel", 0);
          },
        },
        {
          label: that.$t("ID_LEVEL"),
          content: "1",
          click() {
            that.$emit("onChangeLevel", 1);
          },
        },
        {
          label: that.$t("ID_LEVEL"),
          content: "2",
          click() {
            that.$emit("onChangeLevel", 2);
          },
        },
        {
          label: that.$t("ID_LEVEL"),
          content: "3",
          click() {},
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
     * Click in drill option
     */
    onClick(item) {
      let array,
        i = 0,
        nindex;
      array = _.clone(this.data);
      array.forEach((el) => {
        if (el.content === item.content) {
          nindex = i;
        }
        i++;
      });
      this.index = nindex;
      if (nindex <= this.level) {
        item.click(item);
      }
    },
    /**
     * Load items in drill items
     */
    loadItems(items, index) {
      let array,
        i = 0,
        that = this;
      array = _.clone(items);
      array.forEach((el) => {
        el.classObject = _.clone(that.classObject);
        if (i <= index) {
          el.classObject["vp-btn-secondary"] = false;
          el.classObject["btn-primary"] = true;
        }
        i += 1;
      });
      return array;
    },
  },
};
</script>
<style>
.v-pm-drill-down-number {
  height: 5rem;
  width: 5rem;
  text-align: center;
  line-height: 5rem;
  font-size: 1.5rem;
}

.vp-inline-block {
  display: inline-block;
}
.vp-block {
  display: block;
}
.vp-padding-r10 {
  padding-right: 10px;
}

.vp-padding-b10 {
  padding-bottom: 10px;
}

.vp-font-size-r1 {
  font-size: 1rem;
}

.vp-btn-secondary {
  color: #2f3133;
  background-color: #b5b6b6;
}

.vp-btn-secondary:hover {
  color: #fff;
  background-color: #6c757d;
  border-color: #6c757d;
}

.v-pm-drill-down {
  vertical-align: top;
  padding-left: 50px;
}
</style>