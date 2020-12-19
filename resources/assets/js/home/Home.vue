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
                :name="pageName"
                @onSubmitFilter="onSubmitFilter"
                @onRemoveFilter="onRemoveFilter"
                @onUpdatePage="onUpdatePage"
                @onUpdateDataCase="onUpdateDataCase"
                @onLastPage="onLastPage"
                @onUpdateFilters="onUpdateFilters"
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
            lastPage: "MyCases",
            page: "MyCases",
            menu: [],
            dataCase: {},
            hideToggle: true,
            collapsed: false,
            selectedTheme: "",
            isOnMobile: false,
            sidebarWidth: "310px",
            pageId: null,
            pageName: null,
            filters:  null,
        };
    },
    mounted() {
        this.onResize();
        window.addEventListener("resize", this.onResize);
        this.getMenu();
        this.listenerIframe();
        window.setInterval(this.setCounter, parseInt(window.config.FORMATS.casesListRefreshTime) * 1000);
    },
    methods: {
        /**
         * Listener for iframes childs
         */
        listenerIframe(){
            let that = this,
                eventMethod = window.addEventListener? "addEventListener": "attachEvent",
	            eventer = window[eventMethod],
	            messageEvent = eventMethod === "attachEvent"? "onmessage": "message";

            eventer(messageEvent, function (e) { 
                if (e.data === "redirect=todo" || e.message === "redirect=todo"){ 
                    that.page = "todo";
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
                    this.menu = this.mappingMenu(response.data);
                    this.setCounter();
                })
                .catch((e) => {
                    console.error(e);
                });
        },
        /**
         * Do a mapping of vue view for menus
         * @returns array
         */
        mappingMenu(data) {
            var i,
                j,
                newData = data,
                auxId,
                viewVue = {
                    CASES_MY_CASES: "MyCases",
                    CASES_SEARCH: "advanced-search",
                    CASES_INBOX: "todo",
                    CASES_DRAFT: "draft",
                    CASES_PAUSED: "paused",
                    CASES_SELFSERVICE: "unassigned",
                    CONSOLIDATED_CASES: "batch-routing",
                    CASES_TO_REASSIGN: "task-reassignments",
                    CASES_FOLDERS: "my-documents",
                };
            for (i = 0; i < data.length; i += 1) {
                auxId = data[i].id || "";
                if (auxId !== "" && viewVue[auxId]) {
                    newData[i].id = viewVue[auxId];
                }
            }
            return newData;
        },
        OnClickSidebarItem(item) {
            if (item.item.page && item.item.page === "/advanced-search") {
                this.page = "advanced-search";
                this.filters = item.item.filters;
                this.pageId = item.item.id;
                this.pageName = item.item.title;
            } else {
                this.filters = [];
                this.page = item.item.id || "MyCases";
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
