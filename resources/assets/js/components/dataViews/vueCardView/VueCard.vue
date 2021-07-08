<template>
  <div class="pm-vue-card">
    <div class="card" style="width: 18rem">
      <div class="card-body">
        <slot> </slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "VueCardView",
  props: ["columns", "item"],
  data() {
    return {
    };
  },
  methods: {
    classBtn(cls) {
      return "btn btn-slim btn-force-radius v-btn-header " + cls;
    },
  },
};
</script>

<style>
.pm-vue-card {
  display: inline-block;
}
</style>
