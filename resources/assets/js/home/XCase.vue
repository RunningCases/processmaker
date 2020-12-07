<template>
  <div>
    <iframe
      :width="width"
      ref="xIFrame"
      frameborder="0"
      :src="path"
      :height="height"
      allowfullscreen
    ></iframe>
  </div>
</template>

<script>
export default {
  name: "XCase",
  components: {},
  props: {
    data: Object,
  },
  mounted() {
    this.height = window.innerHeight - this.diffHeight;
    this.dataCase = this.$parent.dataCase;
    this.path =
      window.config.SYS_SERVER +
      window.config.SYS_URI +
      `cases/open?APP_UID=${this.dataCase.APP_UID}&DEL_INDEX=${this.dataCase.DEL_INDEX}&action=${this.dataCase.ACTION}`;
  },
  data() {
    return {
      height: "100%",
      width: "100%",
      diffHeight: 10,
      dataCase: null,
      path: "",
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
