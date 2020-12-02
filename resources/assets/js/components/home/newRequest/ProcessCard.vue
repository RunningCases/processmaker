<template>
  <div class="v-inline v-process-card" @click="data.onClick(data)">
    <b-card
      :sub-title="data.title"
      class="overflow-hidden"
      style="min-width: 340px; max-width: 340px"
      bg-variant="light"
    >
      <b-card-text class="v-process-card-text">
        {{ data.description }}
      </b-card-text>
    </b-card>
  </div>
</template>

<script>
export default {
  name: "ProcessCard",
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
.v-inline {
  display: inline-block;
}

.v-process-card {
  margin-right: 15px;
  margin-bottom: 15px;
  border-left: solid lightseagreen;
}

.v-process-card-text {
  font-size: 13px;
}
</style>