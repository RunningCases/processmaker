<template>
  <div class="pm-vue-card-view">
    <div class="pm-vue-card-view-container">
      <div v-for="item in data" :columns="columns" :key="item" :data="item">
        asdasd {{item}}
      </div>
    </div>
  </div>
</template>

<script>
import VueCard from "./VueCard.vue";
export default {
  name: "VueCardView",
  components: {
    VueCard,
  },
  props: ["data"],
  data() {
    return {
      columns: [
        "detail",
        "case_number",
        "case_title",
        "process_name",
        "task",
        "due_date",
        "delegation_date",
        "priority",
        "actions",
      ],
    };
  },
  mounted(){
    console.log(this.data);
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
