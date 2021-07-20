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
                :filters="filters"
                :id="pageId"
                :pageUri="pageUri"
                :name="pageName"
                :defaultOption="defaultOption"
                @onSubmitFilter="onSubmitFilter"
                @onRemoveFilter="onRemoveFilter"
                @onUpdatePage="onUpdatePage"
                @onUpdateDataCase="onUpdateDataCase"
                @onLastPage="onLastPage"
                @onUpdateFilters="onUpdateFilters"
                @cleanDefaultOption="cleanDefaultOption"
            ></component>
        </div>
    </div>
</template>
<script>
import CustomSidebar from "./../components/menu/CustomSidebar";
import MyCases from "./MyCases";
import MyDocuments from "./MyDocuments";
import Todo from "./Inbox/Todo.vue";
import Paused from "./Paused/Paused.vue";
import Draft from "./Draft/Draft.vue";
import Unassigned from "./Unassigned";
import BatchRouting from "./BatchRouting";
import CaseDetail from "./CaseDetail";
import XCase from "./XCase";
import TaskReassignments from "./TaskReassignments";
import AdvancedSearch from "./AdvancedSearch";
import LegacyFrame from "./LegacyFrame";

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
        LegacyFrame
    },
    data() {
        return {
            lastPage: "MyCases",
            page: null,
            menu: [],
            dataCase: {},
            hideToggle: true,
            collapsed: false,
            selectedTheme: "",
            isOnMobile: false,
            sidebarWidth: "260px",
            pageId: null,
            pageName: null,
            pageUri: null,
            filters: null,
            menuMap: {
                CASES_MY_CASES: "MyCases",
                CASES_SENT: "MyCases",
                CASES_SEARCH: "advanced-search",
                CASES_INBOX: "todo",
                CASES_DRAFT: "draft",
                CASES_PAUSED: "paused",
                CASES_SELFSERVICE: "unassigned",
                CONSOLIDATED_CASES: "batch-routing",
                CASES_TO_REASSIGN: "task-reassignments",
                CASES_FOLDERS: "my-documents"
            },
            defaultOption: window.config.defaultOption || ''
        };
    },
    mounted() {
        this.onResize();
        this.getMenu();
        this.listenerIframe();
        window.setInterval(
            this.setCounter,
            parseInt(window.config.FORMATS.casesListRefreshTime) * 1000
        );
    },
    methods: {
        /**
         * Listener for iframes childs
         */
        listenerIframe() {
            let that = this,
                eventMethod = window.addEventListener
                    ? "addEventListener"
                    : "attachEvent",
                eventer = window[eventMethod],
                messageEvent =
                    eventMethod === "attachEvent" ? "onmessage" : "message";

            eventer(messageEvent, function(e) {
                if ( e.data === "redirect=todo" || e.message === "redirect=todo"){
                    that.page = "todo";
                }
                if ( e.data === "update=debugger" || e.message === "update=debugger"){
                    if(that.$refs["component"].updateView){
                        that.$refs["component"].updateView();
                    }
                }
            });
        },
        /**
         * Gets the menu from the server
         */
        getMenu() {
            api.menu
                .get()
                .then((response) => {
                    this.setDefaultCasesMenu(response.data);
                    this.menu = this.mappingMenu(this.setDefaultIcon(response.data));
                    this.setCounter();
                })
                .catch((e) => {
                    console.error(e);
                });
        },
        /**
         * Set default cases menu option
         */
        setDefaultCasesMenu(data) {
            let menuItem = _.find(data, function(o) {
                return o.id === window.config._nodeId;
            });
            if (menuItem && menuItem.href) {
                this.page = this.menuMap[window.config._nodeId] || "MyCases";
                this.$router.push(menuItem.href);
            } else {
                this.page = "MyCases";
            }
            this.lastPage = this.page;
        },
        /**
         * Do a mapping of vue view for menus
         * @returns array
         */
        mappingMenu(data) {
            var i,
                j,
                newData = data,
                auxId;
            for (i = 0; i < data.length; i += 1) {
                auxId = data[i].id || "";
                if (auxId !== "" && this.menuMap[auxId]) {
                    newData[i].id = this.menuMap[auxId];
                } else if (newData[i].href) {
                    newData[i].id  = "LegacyFrame";
                }
            }
            return newData;
        },
        /**
         * Set a default icon if the item doesn't have one
         */
        setDefaultIcon(data){
            var i,
                auxData = data;
            for (i = 0; i < auxData.length; i += 1) {
                if (auxData[i].icon !== undefined && auxData[i].icon === "") {
                    auxData[i].icon = "fas fa-bars";
                }
            }
            return auxData;
        },
        /**
         * Clean the default option property
         */
        cleanDefaultOption() {
            this.defaultOption = "";
        },
        OnClickSidebarItem(item) {
            if (item.item.page && item.item.page === "/advanced-search") {
                this.page = "advanced-search";
                this.filters = item.item.filters;
                this.pageId = item.item.id;
                this.pageUri = item.item.href;
                this.pageName = item.item.title;
            } else {
                this.filters = [];
                this.pageId = null;
                this.pageUri = item.item.href;
                this.page = item.item.id || "MyCases";
                if (this.page === this.lastPage 
                    && this.$refs["component"] 
                    && this.$refs["component"].updateView) {
                    this.$refs["component"].updateView();
                }
                this.lastPage = this.page;
            }
        },
        setCounter() {
            let that = this,
                counters = [];
            if (that.menu.length > 0) {
                api.menu
                .getCounters()
                .then((response) => {
                    var i,
                        j,
                        data = response.data;
                    that.counters = data;
                    for (i = 0; i < that.menu.length; i += 1) {
                        if (that.menu[i].id && data[that.menu[i].id]) {
                            that.menu[i].badge.text = data[that.menu[i].id];
                        }
                    }
                })
                .catch((e) => {
                    console.error(e);
                });
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
                return o.id === "advanced-search";
            });
            if (advSearch) {
                const index = advSearch.child.findIndex(function(o) {
                    return o.id === data.id;
                });
                if (index !== -1) {
                    advSearch.child[index].filters = data.filters;
                } else {
                    if (!advSearch.hasOwnProperty("child")) {
                        advSearch["child"] = [];
                    }
                    advSearch.child.push({
                        filters: data.filters,
                        href: "/advanced-search/" + data.id,
                        title: data.name,
                        icon: "fas fa-circle",
                        id: data.id,
                        page: "/advanced-search",
                    });
                }
            }
        },
        onRemoveFilter(id) {
            this.removeMenuSearchChild(id);
            this.resetSettings();
        },
        resetSettings() {
            this.page = "advanced-search";
            this.pageId = null;
            this.pageName = null;
            this.filters = [];
        },
        onUpdatePage(page) {
            this.lastPage = this.page;
            this.page = page;
            if (this.$refs["component"] && this.$refs["component"].updateView) {
                this.$refs["component"].updateView();
            }
        },
        onUpdateDataCase(data) {
            this.dataCase = data;
        },
        onLastPage() {
            this.page = this.lastPage;
            this.lastPage = "MyCases";
        },
        removeMenuSearchChild(id) {
            let newMenu = this.menu;
            let advSearch = _.find(newMenu, function(o) {
                return o.id === "advanced-search";
            });
            if (advSearch) {
                const index = advSearch.child.findIndex(function(o) {
                    return o.id === id;
                });
                if (index !== -1) advSearch.child.splice(index, 1);
            }
        },
        onUpdateFilters(filters) {
            this.filters = filters;
        }
    }
};
</script>

<style lang="scss">
#home {
    padding-left: 260px;
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
