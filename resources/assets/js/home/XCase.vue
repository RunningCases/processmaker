<template>
  <div class="d-flex">
    <iframe
      :width="width"
      ref="xIFrame"
      frameborder="0"
      :src="path"
      :height="height"
      allowfullscreen
      @load="onLoadIframe"
    ></iframe>
    <Debugger v-if="openDebug === true" :style="'height:' + height + 'px'" ref="debugger"/>
  </div>
</template>

<script>
import Debugger from "../components/home/debugger/Debugger.vue";
import api from "../api/index";
export default {
  name: "XCase",
  components: {
    Debugger
  },
  props: {
    data: Object
  },
  mounted() {
    let that = this;
    this.height = window.innerHeight - this.diffHeight;
    this.dataCase = this.$parent.dataCase;
    if (this.dataCase.ACTION === "jump") {
      this.path =
        window.config.SYS_SERVER +
        window.config.SYS_URI +
        `cases/open?APP_NUMBER=${this.dataCase.APP_NUMBER}&action=${this.dataCase.ACTION}&actionFromList=${this.dataCase.ACTION_FROM_LIST}`;
    } else {
      this.path =
        window.config.SYS_SERVER +
        window.config.SYS_URI +
        `cases/open?APP_UID=${this.dataCase.APP_UID}&DEL_INDEX=${this.dataCase.DEL_INDEX}&TAS_UID=${this.dataCase.TAS_UID}&action=${this.dataCase.ACTION}`;
    }

    setTimeout(() => {
      api.cases.debugStatus(this.dataCase).then((response) => {
        if (response.data) {
          that.openDebug = true;
        }
      });
    }, 2000);
  },
  data() {
    return {
      openDebug: false,
      dataCase: null,
      height: "100%",
      width: "100%",
      diffHeight: 10,
      path: "",
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    /**
     * update view in component
     */
    updateView(){
      if(this.openDebug){
        this.$refs["debugger"].loadData();
      }
    },
    onLoadIframe() {},
  },
};
</script>

<style>
.debugger-inline-cont {
  overflow: hidden;
}
</style>
