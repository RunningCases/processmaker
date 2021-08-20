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
                :pageUri="pageUri"
                :name="pageName"
                :defaultOption="defaultOption"
                :settings="config.setting[page]"
                :filters="filters"
                @onSubmitFilter="onSubmitFilter"
                @onRemoveFilter="onRemoveFilter"
                @onUpdatePage="onUpdatePage"
                @onUpdateDataCase="onUpdateDataCase"
                @onLastPage="onLastPage"
                @onUpdateFilters="onUpdateFilters"
                @cleanDefaultOption="cleanDefaultOption"
                @updateUserSettings="updateUserSettings"
            ></component>
        </div>
    </div>
</template>
<script>
import CustomSidebar from "./../components/menu/CustomSidebar";
import CustomSidebarMenuItem from "./../components/menu/CustomSidebarMenuItem";
import MyCases from "./MyCases/MyCases.vue";
import MyDocuments from "./MyDocuments";
import Inbox from "./Inbox/Inbox.vue";
import Paused from "./Paused/Paused.vue";
import Draft from "./Draft/Draft.vue";
import Unassigned from "./Unassigned/Unassigned.vue";
import BatchRouting from "./BatchRouting";
import CaseDetail from "./CaseDetail";
import XCase from "./XCase";
import TaskReassignments from "./TaskReassignments";
import AdvancedSearch from "./AdvancedSearch/AdvancedSearch.vue";
import LegacyFrame from "./LegacyFrame";

import api from "./../api/index";
import eventBus from './EventBus/eventBus'
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
        Inbox,
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
            config: {
                id: window.config.userId || "1",
                name: "userConfig",
                setting: {}
            },
            menuMap: {
                CASES_MY_CASES: "MyCases",
                CASES_SENT: "MyCases",
                CASES_SEARCH: "advanced-search",
                CASES_INBOX: "inbox",
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
        let that = this;
        this.onResize();
        this.getMenu();
        this.getUserSettings();
        this.listenerIframe();
        window.setInterval(
            this.setCounter,
            parseInt(window.config.FORMATS.casesListRefreshTime) * 1000
        );
        // adding eventBus listener
         eventBus.$on('sort-menu', (data) => {
            that.updateUserSettings('customCasesList', data);
        });
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
                    that.page = "inbox";
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
         * Gets the user config
         */
        getUserSettings() {
            api.config
                .get({
                    id: this.config.id,
                    name: this.config.name
                })
                .then((response) => {
                    if(response.data && response.data.status === 404) {
                        this.createUserSettings();
                    } else if (response.data) {
                        this.config = response.data;
                    }
                })
                .catch((e) => {
                    console.error(e);
                });
        },
        /**
         * Creates the user config service
         */
        createUserSettings() {
            api.config
                .post(this.config)
                .then((response) => {
                    if (response.data) {
                        this.config = response.data;
                    }
                })
                .catch((e) => {
                    console.error(e);
                });
        },
        /**
         * Update the user config service
         */
        updateUserSettings(prop, data) {
            if (this.config.setting) {
                if (!this.config.setting[this.page]) {
                    this.config.setting[this.page] = {};
                }
                this.config.setting[this.page][prop] = data;
                api.config
                    .put(this.config)
                    .then((response) => {
                        if (response.data) {
                            //TODO success response
                        }
                    })
                    .catch((e) => {
                        console.error(e);
                    });
            }
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
                // Tasks group need pie chart icon
                if (data[i].header && data[i].id === "FOLDERS") {
                    data[i] = {
                        component: CustomSidebarMenuItem,
                        props: {
                            isCollapsed: this.collapsed? true: false,
                            item: {
                                header: data[i].header,
                                title: data[i].title,
                                hiddenOnCollapse: data[i].hiddenOnCollapse,
                                icon: 'pie-chart-fill',
                                onClick: function (item) {
                                    // TODO click evet handler
                                }
                            }
                        }
                    }
                }
                if (data[i].id === "inbox" || data[i].id === "draft"
                || data[i].id === "paused" || data[i].id === "unassigned")  {
                    data[i]["child"] = this.sortCustomCasesList(data[i].customCasesList, this.config.setting[this.page] && this.config.setting[this.page].customCasesList ? this.config.setting[this.page].customCasesList: [])
                    data[i]["sortable"] = data[i].customCasesList.length > 1;
                    data[i]["sortIcon"] = "gear-fill";
                    data[i] = {
                        component: CustomSidebarMenuItem,
                        props: {
                            isCollapsed: this.collapsed? true: false,
                            item: data[i]
                        }
                    };
                }
            }
            return newData;
        },
        /**
         * Sort the custom case list menu items
         * @param {array} list
         * @param {array} ref
         * @returns {array}
         */
        sortCustomCasesList(list, ref) {
            let item,
                newList = [],
                temp = [];
            if (ref && ref.length) {
                ref.forEach(function (menu) {
                    item = list.find(x => x.id === menu.id);
                    if (item) {
                        newList.push(item);
                    }
                })
            } else {
                return list;
            }
            temp = list.filter(this.comparerById(newList));
            return  [...newList, ...temp];

        },
        /**
         * Util to compare an oobject by id
         * @param {array} otherArray
         * @returns {object}
         */
        comparerById(otherArray){
            return function(current){
                return otherArray.filter(function(other){
                    return other.id == current.id
                }).length == 0;
            }
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
        },
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
