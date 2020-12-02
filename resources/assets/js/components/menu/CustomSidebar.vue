<template>
  <div>
    <sidebar-menu
      ref="sidebar"
      :width="sidebarWidth"
      :menu="menu"
      :hideToggle="hideToggle"
      :collapsed="collapsed"
      :theme="selectedTheme"
      :show-one-child="true"
      @item-click="onItemClick"
    >
      <div slot="header">
        <div class="text-right" @click="onToggleClick">
          <b-icon :icon="className"></b-icon>
        </div>
      </div>
    </sidebar-menu>
  </div>
</template>

<script>
import api from "./../../api/index";

export default {
  name: "CustomSidebar",
  data() {
    return {
      menu: [],
      collapsed: false,
      isOnMobile: false,
      hideToggle: true,
      selectedTheme: "",
      sidebarWidth: "310px",
    };
  },
  computed: {
    className() {
      return this.collapsed
        ? "arrow-right-circle-fill"
        : "arrow-left-circle-fill";
    },
  },
  mounted() {
    this.onResize();
    window.addEventListener("resize", this.onResize);
    api.menu
      .get()
      .then((response) => {
        this.menu = response;
      })
      .catch((e) => {
        console.error(e);
      });
  },
  methods: {
    onToggleClick() {
      this.$refs.sidebar.$emit("toggle-collapse", this.collapsed);
      this.collapsed = !this.collapsed;
    },
    createCounter() {
      let els = document.querySelectorAll("a[href='#/foo']");
      this.inboxCounter = document.createElement("span");
      this.inboxCounter.setAttribute(
        "class",
        "float-right badge badge-light navBadget"
      );
      this.inboxCounter.innerHTML += "5";
      if (els && els[0]) {
        els[0].appendChild(this.inboxCounter);
      }
    },
    onToggleCollapse(collapsed) {
      console.log(collapsed);
      this.collapsed = collapsed;
    },
    onItemClick(event, item, node) {
      this.$emit("OnClickSidebarItem", { item });
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
  },
};
</script>

<style lang="scss">
.badge-custom {
  color: #000;
  background-color: #d4dfe6;
  padding: 0px 6px;
  font-size: 12px;
  border-radius: 3px;
  height: 20px;
  line-height: 20px;
  font-weight: 600;
  text-transform: uppercase;
}
.text-right {
  color: white;
  font-size: x-large;
  margin: 3px;
  margin-right: 16px;
}
.sidebar-overlay {
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-color: #000;
  opacity: 0.5;
  z-index: 900;
}

pre {
  font-family: Consolas, monospace;
  color: #000;
  background: #fff;
  border-radius: 2px;
  padding: 15px;
  line-height: 1.5;
  overflow: auto;
}
</style>