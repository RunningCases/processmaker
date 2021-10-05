<template>
  <div class="pm-vue-list-view" :height="height">
    <div class="pm-vue-list-view-container">
      <div
        class="pm-vue-list-view-body"
        :style="{height: height + 'px'}"
      >
        <vue-list v-for="item in data" :key="item.id" :item="item" :options="options"> 
          <b-row>
            <b-col sm="5">
              <slot
                v-for="column in chunkColumns[0]"
                :name="column"
                :item="item"
                :column="column"
                :headings="options.headings"
                ref="containerList"
              ></slot>
            </b-col>
            <b-col sm="5">
              <!-- <slot
                name="send_by"
                :item="item"
                column="send_by"
                :headings="options.headings"
              ></slot> -->
               <slot
                v-for="column in chunkColumns[1]"
                :name="column"
                :item="item"
                :column="column"
                :headings="options.headings"
                ref="containerList"
              ></slot>
            </b-col>
            <b-col sm="2">
              <slot
                name="actions"
                :item="item"
              ></slot>
            </b-col>
          </b-row>
        </vue-list>
      </div>

      <div class="pm-vue-list-view-footer">
        <a @click="viewMore" class="list-group-item">{{ loadMore }}</a>
      </div>
    </div>
  </div>
</template>

<script>
import VueList from "./VueList.vue";
import DefaultMixins from "./VueListViewMixins";
export default {
  name: "VueListView",
  mixins: [DefaultMixins],
  components: {
    VueList,
  },
  props: ["options"],
  data() {
    return {
      loadMore: this.$t("ID_LOAD_MORE"),
      chunkColumns: []
    };
  },
  mounted() {
    this.chunkColumns = this.chunkArray(this.options.columns, 2);
    debugger
  },
  methods: {
    chunkArray(array, size) {
      let result = [],
        arrayCopy = [...array];
      while (arrayCopy.length > 0) {
          result.push(arrayCopy.splice(0, size));
      }
      return result;
    },
    classBtn(cls) {
      return "btn btn-slim btn-force-radius v-btn-header " + cls;
    },
    /**
     * Filter the column send_by
     */
    filterOptions() {
      this.options.columns = this.options.columns.filter(function(item) {
        return item !== "send_by";
      });
    }
  },
};
</script>

<style>
.pm-vue-list-view {
  font-family: "proxima-nova", "Helvetica Neue", Helvetica, Arial, sans-serif;
  font-size: 0.9rem;
}

.pm-vue-list-view-body {
  border: 1px solid rgba(0, 0, 0, 0.125);
  padding-bottom: 5px;
  margin-top: 5px;
  overflow-y: auto;
}

.pm-vue-list-view-footer {
  text-align: center;
  line-height: 1.25;
}
</style>
