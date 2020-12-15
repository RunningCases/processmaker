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
        <div class="v-btn-textm-header">
          <i :class="header.icon"></i>
        </div>
        <div>
          <span class="v-btn-textb-header">
            {{ header.counter }}
          </span>
        </div>
        <span class="v-btn-texts-header">
          {{ header.title }}
        </span>
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
  font-size: 1.5vw;
}

.v-btn-textm-header {
  font-size: 1vw;
}

.v-btn-texts-header {
  font-size: 0.8vw;
}
.v-btn-header {
  min-width: 150px;
  margin-left: 5px !important;
  margin-right: 5px !important;
  padding-bottom: 5px !important;
  padding-top: 5px !important;
}
</style>
