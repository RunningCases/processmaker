<template>
  <Tabs>
    <Tab
      v-for="item in data.items"
      :key="item.title"
      :title="item.title"
      v-on:click="selectedTab"
    >
      {{ item.title }}
    </Tab>
  </Tabs>
</template>

<script>
import { Tabs, Tab } from 'vue-slim-tabs'

export default {
  props: {
    data: Object
  },
  components: {
    Tabs, Tab
  },
  data() {
    return {
      0: function() {
        console.log(this.data.items[0].appUid);
      }
    };
  },
  methods: {
    selectedTab(e, index) {
      if (index === 0) {
        console.log("fabio")
      }
    }
  },
}
</script>
<style src="vue-slim-tabs/themes/default.css"></style>