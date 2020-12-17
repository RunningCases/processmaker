<template>
  <div class="row-cont">
    <div class="text-center">
      <button
        type="button"
        v-for="header in data"
        :key="header.title"
        @click="header.onClick(header)"
        :class="classBtn(header.class)"
      >
        <div class="v-center-header">
          <div class="v-btn-textm-header">
            <span class="v-text2">
              <i :class="header.icon"></i>
            </span>
            <span class="v-btn-textb-header float-right">
              {{ header.counter }}
            </span>
          </div>
          <span class="v-btn-texts-header">
            {{ header.title }}
          </span>
        </div>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "HeaderCounter",
  props: {
    data: Array,
  },
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
.v-btn-textb-header {
  font-size: 1.1vw;
  padding-top: 5px;
}

.v-btn-textm-header {
  font-size: 1vw;
}

.v-btn-texts-header {
  font-size: 1vw;
}
.v-btn-header {
  min-width: 200px;
  margin-left: 5px !important;
  margin-right: 5px !important;
  padding-bottom: 10px !important;
  padding-top: 10px !important;
  text-align: center;
}

.v-center-header {
  width: fit-content;
  display: inline-block;
}
.v-center {
  text-align: center;
}

.v-text2 {
  font-size: 2vw;
  float: left;
}
</style>
