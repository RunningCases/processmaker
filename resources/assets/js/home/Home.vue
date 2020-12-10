<template>
    <div
        id="home"
        :class="[{ collapsed: collapsed }, { onmobile: isOnMobile }]"
    >
        <div class="demo">
            <div class="container">
                <router-view />
            </div>
            <CustomSidebar
                :menu="menu"
                @OnClickSidebarItem="OnClickSidebarItem"
                @onToggleCollapse="onToggleCollapse"
            />
            <div
                v-if="isOnMobile && !collapsed"
                class="sidebar-overlay"
                @click="collapsed = true"
            />

            <component
                v-bind:is="page"
                ref="component"
                :id="pageId"
                :name="pageName"
                @onSubmitFilter="onSubmitFilter"
                @onRemoveFilter="onRemoveFilter"
            ></component>
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

import api from "./../api/index";

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
            menu: null,
            dataCase: {},
            hideToggle: true,
            collapsed: false,
            selectedTheme: "",
            isOnMobile: false,
            sidebarWidth: "310px",
            pageId: null,
            pageName: null,
        };
    },
    mounted() {
        this.onResize();
        window.addEventListener("resize", this.onResize);
        this.getMenu();
    },
    methods: {
        /**
         * Gets the menu from the server
         */
        getMenu() {
            api.menu
                .get()
                .then((response) => {
                    this.menu = response;
                })
                .catch((e) => {
                    console.error(e);
                });
        },
        OnClickSidebarItem(item) {
            this.page = item.item.page || "MyCases";
            this.pageId = item.item.id || null;
            this.pageName = item.item.title || null;
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
        /**
         * Handle if filter was submited
         */

        onSubmitFilter(data) {
            this.addMenuSearchChild(data);
        },
        /**
         * Add a child submenu to search menu
         * @param {object} data - cnotains theinfo to generate a menu
         */
        addMenuSearchChild(data) {
            let newMenu = this.menu;
            let advSearch = _.find(newMenu, function(o) {
                return o.href === "/advanced-search";
            });
            if (!advSearch.hasOwnProperty("child")) {
                advSearch["child"] = [];
            }
            advSearch.child.push({
                href: "/advanced-search/" + data.id,
                title: data.name,
                icon: "fas fa-circle",
                id: data.id,
                page: "advanced-search",
            });
        },
        onRemoveFilter(id) {
            this.removeMenuSearchChild(id);
            this.page = "advanced-search";
            this.pageId = null;
            this.pageName = null;
        },
        removeMenuSearchChild(id) {
            let newMenu = this.menu;
            let advSearch = _.find(newMenu, function(o) {
                return o.href === "/advanced-search";
            });
            const index = advSearch.child.findIndex(function(o) {
                return o.id === id;
            });
            if (index !== -1) advSearch.child.splice(index, 1);
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
