<template>
  <div class="pm-all-view-popover">
    <b-popover
      :target="target"
      ref="popover"
      triggers="click"
      placement="bottom"
      @show="onshow"
    >
      <template #title>{{ $t("ID_PROCESSES").toUpperCase() }}</template>

      <div>
        <div class="input-group input-group-sm mb-3">
          <span class="input-group-text" id="inputGroup-sizing-sm"
            ><i class="fas fa-search"></i
          ></span>

          <input
            type="text"
            class="form-control"
            aria-describedby="inputGroup-sizing-sm"
            @keyup="search"
            v-model="text"
          />
        </div>

        <div class="form-check border-bottom">
          <input
            class="form-check-input"
            type="checkbox"
            v-model="allColumns"
            @change="toogleAllColumns"
          />

          <label class="form-check-label" for="flexCheckDefault">
            {{ $t("ID_ALL") }}
          </label>
        </div>

        <b-form-group>
          <b-form-checkbox-group
            v-model="localSelected"
            :options="results"
            value-field="key"
            text-field="value"
            name="flavour-2a"
            @change="changeOptions"
            stacked
          ></b-form-checkbox-group>
        </b-form-group>

        <div class="v-popover-footer">
          <div class="float-right">
            <b-button @click="onClose" size="sm" variant="danger">
              {{ $t("ID_CANCEL") }}</b-button
            >

            <b-button @click="onSave" size="sm" variant="success">{{
              $t("ID_SAVE")
            }}</b-button>
          </div>
        </div>
      </div>
    </b-popover>
  </div>
</template>

<script>
export default {
  name: "ProcessPopover",
  props: ["target"],
  data() {
    return {
      options: [],
      text: "",
      results: [],
      allColumns: false,
      localSelected: [],
      selected: [],
    };
  },
  mounted() {
    this.results = this.options;
    this.localSelected = this.selected;
  },
  methods: {
    /**
     * Set options
     */
    setOptions(options) {
      this.options = options;
      this.results = options;
    },
    /**
     * Set selected options
     */
    setSelectedOptions(options) {
      console.log("SELECTED");
      this.selected = options;
      this.localSelected = options;
    },
    /**
     * Close buton click handler
     */
    onClose() {
      this.$refs.popover.$emit("close");
      this.$emit("closePopover");
    },
    /**
     * Save button click handler
     */
    onSave() {
      let sels;
      sels = _.clone(this.localSelected);
      this.$root.$emit("bv::hide::popover");
      this.$emit("onUpdateColumnSettings", sels);
    },
    /**
     * Show popover event handler
     */
    onshow() {
      this.$root.$emit("bv::hide::popover");
    },
    /**
     * Search in the column name
     */
    search() {
      let txt = this.text.toLowerCase(),
        val,
        opts = [];

      opts = _.filter(this.options, function (o) {
        val = o.value.toLowerCase();

        return val.search(txt) != -1;
      });

      this.results = opts;
    },
    /**
     * Toogle all options in popover
     */
    toogleAllColumns() {
      let res = [];
      if (this.allColumns) {
        _.each(this.options, function (o) {
          res.push(o.key);
        });
      }
      this.selected = res;
    },
    /**
     * Handler when change options event
     */
    changeOptions() {
      let that = this,
        res = [];
      _.each(this.options, function (o) {
        if (
          _.findIndex(that.localSelected, function (v) {
            return v === o.key;
          }) != -1
        ) {
          res.push(o.key);
        }
      });
      this.localSelected = res;
    },
  },
};
</script>


<style scoped>
.pm-all-view-popover .popover {
  max-width: 350px !important;
  min-width: 200px !important;
}

.v-popover-footer {
  display: flow-root;
}
</style>