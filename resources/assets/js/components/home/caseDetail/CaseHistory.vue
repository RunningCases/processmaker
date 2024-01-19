<template>
  <div class="card v-case-history">
    <iframe
      :width="width"
      ref="IFrameCaseHistory"
      title="IFrameCaseHistory"
      frameborder="0"
      :src="url"
      :height="height"
      allowfullscreen
    ></iframe>
  </div>
</template>

<script>
export default {
  name: "CaseHistory",
  props: {
    historyData: Object,
  },
  data() {
    return {
      url: "",
      height: "500px",
      width: "100%",
      diffHeight: 10,
    };
  },
  computed: {},
  mounted() {
    this.url = window.config.SYS_SERVER_AJAX +
      window.config.SYS_URI +
      `cases/ajaxListener?action=caseHistory&appUid=` + this.historyData.APP_UID;
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
  },
};
</script>

<style>
.v-case-history {
  font-size: 13px;
}
</style>