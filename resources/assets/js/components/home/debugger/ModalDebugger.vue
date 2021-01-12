<template>
  <b-modal ref="modal-debugger" hide-footer size="xl">
    <tabs>
      <tab name="Variables">
        <div
          class="btn-toolbar justify-content-between"
          role="toolbar"
          aria-label="Toolbar with button groups"
        >
          <div
            class="btn-group"
            role="group"
            aria-label="Basic radio toggle button group"
          >
            <input
              type="radio"
              class="btn-check"
              name="btnradio"
              id="btnradio1"
              autocomplete="off"
              checked
            />
            <label class="btn btn-outline-secondary" for="btnradio1"
              >Radio 1</label
            >

            <input
              type="radio"
              class="btn-check"
              name="btnradio"
              id="btnradio2"
              autocomplete="off"
            />
            <label class="btn btn-outline-secondary" for="btnradio2"
              >Radio 2</label
            >

            <input
              type="radio"
              class="btn-check"
              name="btnradio"
              id="btnradio3"
              autocomplete="off"
            />
            <label class="btn btn-outline-secondary" for="btnradio3"
              >Radio 3</label
            >
          </div>
          <div class="input-group">
            <div class="input-group-text" id="btnGroupAddon2">@</div>
            <input
              type="text"
              class="form-control"
              placeholder="Input group example"
              aria-label="Input group example"
              aria-describedby="btnGroupAddon2"
            />
          </div>
        </div>
      </tab>
      <tab name="Triggers"> </tab>
    </tabs>
  </b-modal>
</template>

<script>
import Tabs from "../../../components/tabs/Tabs.vue";
import Tab from "../../../components/tabs/Tab.vue";
export default {
  name: "ModalDebugger",
  components: {
    Tabs,
    Tab,
  },
  props: {},
  mounted() {
    this.initializeDebugTab();
  },
  data() {
    return {
      data: null,
      debugFullPage: false,
      debugTabs: [],
      activetab: 1,
      variableTabs: [],
      debugSearch: "",
      isRTL: false,
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    show() {
      this.$refs["modal-debugger"].show();
    },
    cancel() {
      this.$refs["modal-debugger"].hide();
    },
    unpauseCase() {},
    /**
     * Initializate debug menu
     */
    initializeDebugTab() {
      this.debugTabs = [
        {
          id: "this.language.ID_VARIABLES",
          title: "this.language.ID_VARIABLES",
          function: "",
        },
        {
          id: "this.language.ID_TRIGGERS",
          title: "this.language.ID_TRIGGERS",
          function: "",
        },
      ];
      this.variableTabs = [
        {
          id: "this.language.ID_ALL",
          title: "this.language.ID_ALL",
          function: "",
        },
        {
          id: "this.language.ID_DYNAFORMS",
          title: "this.language.ID_DYNAFORMS",
          function: "",
        },
        {
          id: "this.language.ID_SYSTEM",
          title: "this.language.ID_SYSTEM",
          function: "",
        },
      ];
    },
  },
};
</script>

<style>
.tabs-component {
  margin: 0 0;
}

.tabs-component-tabs {
  border: solid 1px #ddd;
  border-radius: 6px;
  margin-bottom: 5px;
}

@media (min-width: 700px) {
  .tabs-component-tabs {
    border: 0;
    align-items: stretch;
    display: flex;
    justify-content: flex-start;
    margin-bottom: -1px;
  }
}

.tabs-component-tab {
  color: #999;
  font-size: 14px;
  font-weight: 600;
  margin-right: 0;
  list-style: none;
}

.tabs-component-tab:not(:last-child) {
  border-bottom: dotted 1px #ddd;
}

.tabs-component-tab:hover {
  color: #666;
}

.tabs-component-tab.is-active {
  color: #000;
}

.tabs-component-tab.is-disabled * {
  color: #cdcdcd;
  cursor: not-allowed !important;
}

@media (min-width: 700px) {
  .tabs-component-tab {
    background-color: #fff;
    border: solid 1px #ddd;
    border-radius: 3px 3px 0 0;
    margin-right: 0.5em;
    transform: translateY(2px);
    transition: transform 0.3s ease;
  }

  .tabs-component-tab.is-active {
    border-bottom: solid 1px #fff;
    z-index: 2;
    transform: translateY(0);
  }
}

.tabs-component-tab-a {
  align-items: center;
  color: inherit;
  display: flex;
  padding: 0.75em 1em;
  text-decoration: none;
}

.tabs-component-panels {
  padding: 4em 0;
}

@media (min-width: 700px) {
  .tabs-component-panels {
    border-top-left-radius: 0;
    background-color: #fff;
    border: solid 1px #ddd;
    border-radius: 0 6px 6px 6px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    padding: 0.5em 0.5em;
  }
}

.btn-group > input[type="checkbox"],
input[type="radio"] {
  box-sizing: border-box;
  padding: 0;
  display: none;
}
</style>


