<template>
  <div>
    <iframe
      :width="width"
      ref="xCaseFrame"
      frameborder="0"
      :src="path"
      :height="height"
      allowfullscreen
    ></iframe>
  </div>
</template>

<script>
export default {
  name: "MyDocuments",
  components: {},
  props: {
    data: Object,
  },
  mounted() {
    this.height = window.innerHeight - this.diffHeight;
  },
  data() {
    return {
      height: "100%",
      width: "100%",
      diffHeight: 10,
      path:
        window.config.SYS_SERVER +
        window.config.SYS_URI +
        "cases/casesStartPage?action=documents",
    };
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
