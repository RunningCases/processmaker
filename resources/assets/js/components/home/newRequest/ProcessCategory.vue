<template>
  <div>
    <h5>{{ data.title }}</h5>
    <div v-for="item in data.items" :key="item.title" class="v-inline">
      <process-card :data="item" />
    </div>
  </div>
</template>

<script>
import ProcessCard from "./ProcessCard.vue";
export default {
  name: "ProcessCategory",
  components: {
    ProcessCard,
  },
  props: {
    data: Object,
  },
  data() {
    return {};
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
  },
};
</script>

<style>
</style>