<template>
  <div class="pm-vue-card-view">
    <div class="pm-vue-card-view-container">
      <div>
        <vue-card v-for="item in data" :key="item.id">
          <slot
            v-for="column in options.columns"
            :name="column"
            :item="item"
            :column="column"
            :headings="options.headings"
          ></slot>
        </vue-card>
      </div>
    </div>
  </div>
</template>

<script>
import VueCard from "./VueCard.vue";
import DefaultMixins from "./VueCardViewMixins";
export default {
  name: "VueCardView",
  mixins: [DefaultMixins],
  components: {
    VueCard,
  },
  props: ["options"],
  data() {
    return {
 
    };
  },
  mounted() {

  },
  methods: {
    classBtn(cls) {
      return "btn btn-slim btn-force-radius v-btn-header " + cls;
    },
  },
};
</script>

<style>
.pm-vue-card-view {
}

.pm-vue-card-view-container {
}
</style>
