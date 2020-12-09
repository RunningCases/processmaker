<template>
  <div id="home" :class="[{ collapsed: collapsed }, { onmobile: isOnMobile }]">
    <div class="demo">
      <div class="container">
        <router-view />
      </div>

      <CustomSidebar
        @OnClickSidebarItem="OnClickSidebarItem"
        @onToggleCollapse="onToggleCollapse"
      />
      <div
        v-if="isOnMobile && !collapsed"
        class="sidebar-overlay"
        @click="collapsed = true"
      />

      <component v-bind:is="page" ref="component"></component>
    </div>
  </div>
</template> 
<script>
import CustomSidebar from "./../components/menu/CustomSidebar";
import MyCases from "./MyCases";
import MyDocuments from "./MyDocuments";
import Todo from "./Todo";
import Draft from "./Draft";
import Paused from "./Paused";
import Unassigned from "./Unassigned";
import BatchRouting from "./BatchRouting";
import CaseDetail from "./CaseDetail";
import XCase from "./XCase";
import TaskReassignments from "./TaskReassignments";
import AdvancedSearch from "./AdvancedSearch";

export default {
  name: "Home",
  components: {
    CustomSidebar,
    MyCases,
    AdvancedSearch,
    MyDocuments,
    BatchRouting,
    TaskReassignments,
    XCase,
    Todo,
    Draft,
    Paused,
    Unassigned,
    CaseDetail,
  },
  data() {
    return {
      page: "MyCases",
      menu: [],
      dataCase: {},
      hideToggle: true,
      collapsed: false,
      selectedTheme: "",
      isOnMobile: false,
      sidebarWidth: "310px",
    };
  },
  mounted() {
    this.onResize();
    window.addEventListener("resize", this.onResize);
  },
  methods: {
    OnClickSidebarItem(item) {
      this.page = item.item.id || "MyCases";
    },
    /**
     * Update page component
     */
    updatePage(data, page, callback) {
      this.dataCase = data;
      this.page = page;
      if (this.$refs["component"] && this.$refs["component"].update) {
        this.$refs["component"].update(data, callback);
      }
    },
    onResize() {
      if (window.innerWidth <= 767) {
        this.isOnMobile = true;
        this.collapsed = true;
      } else {
        this.isOnMobile = false;
        this.collapsed = false;
      }
    },
    /**
     * Toggle sidebar handler
     * @param {Boolean} collapsed - if sidebar is collapsed true|false
     *
     */
    onToggleCollapse(collapsed) {
      this.collapsed = collapsed;
    },
  },
};
</script>

<style lang="scss">
#home {
  padding-left: 310px;
  transition: 0.3s;
}
#home.collapsed {
  padding-left: 50px;
}
#home.onmobile {
  padding-left: 50px;
}

.container {
  max-width: 1500px;
}
</style>