<template>
  <div class="pm-vue-card">
    <div class="card pm-vue-card-inside" style="width: 20rem" @dblclick="dblClick">
      <div class="card-body">
        <slot> </slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "VueCardView",
  props: ["options", "item"],
  data() {
    return {};
  },
  methods: {
    classBtn(cls) {
      return "btn btn-slim btn-force-radius v-btn-header " + cls;
    },
    dblClick(event){
      this.options.dblClick(event, this.item, this.options);
    }
  },
};
</script>

<style>
.pm-vue-card {
  display: inline-block;
  padding: 0.7rem;
}
.pm-vue-card-inside {
  border-left: solid lightseagreen;
  color: #212529;
  background-color: #f8f9fa;
}

.pm-vue-card-inside:hover {
  background-color: #cfd9e4;
}
</style>
