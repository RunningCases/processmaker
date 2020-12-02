<template>
  <div id="home" :class="[{ collapsed: collapsed }, { onmobile: isOnMobile }]">
    <div class="demo">
      <div class="container">
        <router-view />
      </div>

      <CustomSidebar @OnClickSidebarItem="OnClickSidebarItem" />
      <div
        v-if="isOnMobile && !collapsed"
        class="sidebar-overlay"
        @click="collapsed = true"
      />

      <component v-bind:is="page"></component>
    </div>
  </div>
</template> onResize() {
            if (window.innerWidth <= 767) {
                this.isOnMobile = true;
                this.collapsed = true;
            } else {
                this.isOnMobile = false;
                this.collapsed = false;
            }
        },

<script>
import CustomSidebar from "./../components/menu/CustomSidebar";
import MyCases from "./MyCases";


export default {
    name: "Home",
    components: {
        CustomSidebar,
        MyCases
    },
    data() {
        return {
            page:"MyCases",
            menu: [],
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
            console.log(item);
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
#home {
  padding-left: 310px;
  transition: 0.3s ease;
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

</style>