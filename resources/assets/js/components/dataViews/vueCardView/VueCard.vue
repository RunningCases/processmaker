<template>
  <div class="pm-vue-card">
    <div class="card" style="width: 18rem">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
        <span v-for="column in columns" :key="column">
          <slot :name="column" :item="item"></slot>
        </span>

        <a href="#" class="card-link">Card link</a>
        <a href="#" class="card-link">Another link</a>
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
      headings: {
        detail: "",
        case_number: this.$i18n.t("ID_MYCASE_NUMBER"),
        case_title: this.$i18n.t("ID_CASE_TITLE"),
        process_name: this.$i18n.t("ID_PROCESS_NAME"),
        task: this.$i18n.t("ID_TASK"),
        current_user: this.$i18n.t("ID_CURRENT_USER"),
        due_date: this.$i18n.t("ID_DUE_DATE"),
        delegation_date: this.$i18n.t("ID_DELEGATION_DATE"),
        priority: this.$i18n.t("ID_PRIORITY"),
        actions: "",
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
.pm-vue-card {
  display: inline-block;
}
</style>
