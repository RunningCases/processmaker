<template>
  <div class="card v-case-summary-card" style="width: 20rem">
    <div class="card-body">
      <h6 class="card-subtitle mb-2 text-muted">{{ data.title }}</h6>
      <div class="card-text v-case-summary-card-body">
        <div>
          <span> {{ data.label.numberCase }} </span>:<span
            class="font-weight-bold"
          >
            {{ data.text.numberCase }}</span
          >
        </div>
        <div>
          <span> {{ data.label.process }} </span>:<span
            class="font-weight-bold"
          >
            {{ data.text.process }}</span
          >
        </div>
        <div>
          <span> {{ data.label.status }} </span>:<span class="font-weight-bold">
            {{ data.text.status }}</span
          >
        </div>
        <div>
          <span> {{ data.label.caseTitle }} </span>:<span
            class="font-weight-bold"
          >
            {{ data.text.caseTitle }}</span
          >
        </div>
        <div>
          <span> {{ data.label.created }} </span>:<span
            class="font-weight-bold"
          >
            {{ data.text.created }}</span
          >
        </div>
        <div>
          <span> {{ data.label.delegationDate }} </span>:<span
            class="font-weight-bold"
          >
            {{ data.text.delegationDate }}</span
          >
        </div>
        <div>
          <span> {{ data.label.duration }} </span>:<span
            class="font-weight-bold"
          >
            {{ data.text.duration }}</span
          >
        </div>
      </div>
      <br />
      <h6 class="card-subtitle mb-2 text-muted">{{ data.titleActions }}</h6>
      <div v-if="data.btnType" class="container v-case-summary-center">
        <button
          type="button"
          class="btn btn-success btn-sm"
          @click="data.onClick"
        >
          {{ data.btnLabel }}
        </button>
      </div>
      <div v-else class="container v-case-summary-center">
        <button
          type="button"
          class="btn btn-success btn-sm"
          @click="data.onClick"
        >
          {{ data.btnLabel }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CaseSummary",
  props: {
    data: Object,
  },
  data() {
    return {};
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
  },
};
</script>

<style>
.v-case-summary-card {
  font-size: 13px;
}

.v-case-summary-card-body {
  padding-left: 15px;
}

.v-case-summary-center {
  text-align: center;
}
</style>