<template>
  <div class="pm-multiview-header">
    <div class="pm-multiview-header-title"></div>
    <div class="pm-multiview-header-actions">
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
</template>

<script>
export default {
  name: "MultiviewHeader",
  props: [],
  data() {
    return {
      data: {
        actions: [
          {
            id: "view-grid",
            title: "Grid",
            onClick(action) {
              console.log("action");
              console.log(action);
            },
            icon: "fas fa-table",
          },
          {
            id: "view-list",
            title: "List",
            onClick(action) {
              console.log("action");
              console.log(action);
            },
            icon: "fas fa-list",
          },
          {
            id: "view-card",
            title: "Card",
            onClick(action) {
              console.log("action");
              console.log(action);
            },
            icon: "fas fa-th",
          },
        ],
      },
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
.pm-multiview-header {
}
.pm-multiview-header-title {
}
.pm-multiview-header-actions {
  float: right;
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
