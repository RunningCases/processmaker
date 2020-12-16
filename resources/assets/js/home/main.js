import Vue from "vue";
import VueRouter from "vue-router";
import VueSidebarMenu from "vue-sidebar-menu";
import VueI18n from 'vue-i18n';
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
import { ServerTable, Event} from 'vue-tables-2';
import "@fortawesome/fontawesome-free/css/all.css";
import "@fortawesome/fontawesome-free/js/all.js";
import 'bootstrap/dist/css/bootstrap-grid.css';
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import Home from "./Home";

Vue.use(VueRouter);
Vue.use(VueSidebarMenu);
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);
Vue.use(VueI18n);
Vue.use(ServerTable, {}, false, 'bootstrap3', {});
window.ProcessMaker = {
    apiClient: require('axios')
};
window.ProcessMaker.pluginBase = "/sysworkflow/en/neoclassic/viena/index.php";
window.ProcessMaker.apiClient.defaults.baseURL = '/sysworkflow/en/neoclassic/viena/index.php/api/';
window.ProcessMaker.SYS_SYS = "workflow";
window.ProcessMaker.SYS_LANG = "en";
window.ProcessMaker.SYS_SKIN = "neoclassic";

let messages = {};
messages[config.SYS_LANG] = config.TRANSLATIONS;
const i18n = new VueI18n({
    locale: config.SYS_LANG, // set locale
    messages, // set locale messages
});

// Define routes
const routes = [
    //{ path: "/advanced-search", component: AdvancedSearch }
];

const router = new VueRouter({
    routes, // short for `routes: routes`,
});

new Vue({
    i18n,
    // eslint-disable-line no-new
    el: "#app",
    router,
    render: (h) => h(Home),
});