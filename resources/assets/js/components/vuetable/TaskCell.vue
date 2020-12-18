<template>
  <div class="v-task-cell">
    <div v-for="item in data" class="d-flex mb-3">
      <div v-bind:style="{ color: activeColor(item.CODE_COLOR) }">
        <i class="fas fa-square"></i>
      </div>
      <div class="col .v-task-cell-ellipsis">
        {{ item.TITLE }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TaskCell",
  props: ["data"],
  data() {
    return {
      //Color map for ["In Progress", "overdue", "inDraft", "paused", "unnasigned"]
      colorMap: ["green", "red", "orange", "aqua", "silver"],
    };
  },
  methods: {
    /**
     * Get the style color to be applied in the square icon
     * @param {number} - status color(1-5)
     * @return {string} - color atribute string
     */
    activeColor: function (codeColor) {
      return this.colorMap[codeColor - 1];
    },
  },
};
</script>

<style>
.v-task-cell {
  display: inline-flex;
}

.v-task-cell-ellipsis {
  white-space: nowrap;
  width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
