<template>
  <div class="pm-multiview-header">
    <div class="pm-multiview-header-title"></div>
    <div class="pm-multiview-header-actions">
      <div>
        <button
          v-for="action in data.actions"
          :key="action.id"
          @click="action.onClick(action)"
          class="pm-multiview-header-button"
          :title="action.title"
        >
          <div>
            <span>
              <i :class="action.icon"></i>
            </span>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MultiviewHeader",
  props: ["data"],
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
.pm-multiview-header {
}
.pm-multiview-header-title {
}
.pm-multiview-header-actions {
  text-align: end;
}
.pm-multiview-header-button {
  background-color: transparent;
  border-color: white;
  font-size: 1.6rem;
  color: #007bff;
  display: inline-block;
  padding: 0px 7px 0px 7px;
}
</style>
