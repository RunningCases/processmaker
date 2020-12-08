<template>
  <div>
    <tabs>
      <tab :name="$t('ID_SUMMARY')">
        <PmCaseSummary :data="dataCaseSummary"> </PmCaseSummary>
      </tab>
      <tab name="Second tab">
        <h2 class="page-subtitle">Second tab</h2>
        This is the content of the second tab.
      </tab>
      <tab name="Disabled tab" :is-disabled="true">
        <h2 class="page-subtitle">Disabled tab</h2>
        This content will be unavailable while :is-disabled prop set to true
      </tab>
      <tab id="oh-hi-mark" name="Custom fragment">
        <h2 class="page-subtitle">Custom fragment</h2>
        The hash that is appended to the url can be customized.
      </tab>
      <tab
        prefix="<span class='prefix'>→</span>"
        name="Prefix and suffix"
        suffix="<span class='suffix'>4</span>"
      >
        <h2 class="page-subtitle">Prefix and suffix</h2>
        A prefix and a suffix can be added — HTML allowed.
      </tab>
    </tabs>
  </div>
</template>

<script>
import Tabs from "./../components/tabs/Tabs.vue";
import Tab from "./../components/tabs/Tab.vue";
import PmCaseSummary from "./../components/home/caseDetail/PmCaseSummary.vue";
import Api from "../api/index";

export default {
  name: "TabsCaseDetail",
  components: {
    Tabs,
    Tab,
    PmCaseSummary,
  },
  props: {
    dataCaseSummary: Array,
  },
  data() {
    return {};
  },

  mounted() {},
  methods: {},
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
</style>












