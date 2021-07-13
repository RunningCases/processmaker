<template>
  <div class="pm-vue-list">
    <div class="card pm-vue-list-inside">
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
    return {};
  },
  methods: {
    classBtn(cls) {
      return "btn btn-slim btn-force-radius v-btn-header " + cls;
    },
  },
};
</script>

<style>
.pm-vue-list {
  padding: 0.7rem 0.7rem 0rem 0.7rem;
}
.pm-vue-list-inside {
  border-left: solid lightseagreen;
  color: #212529;
  background-color: #f8f9fa;
}

.pm-vue-list-inside:hover {
  background-color: #cfd9e4;
}
</style>
