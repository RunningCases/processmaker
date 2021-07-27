<template>
<div id="people">
    <button-fleft :data="newList"></button-fleft>
    <button-fleft :data="importList"></button-fleft>
    <v-server-table 
        :data="tableData"
        :columns="columns"
        :options="options"
        ref="table" 
    >
         <div slot="actions" slot-scope="props">
            <div>
            <ellipsis v-if="dataEllipsis" :data="dataEllipsis"> </ellipsis>
            </div>
        </div>
    </v-server-table>
</div>


</template>
<script>
import Api from "./Api/CaseList";
import ButtonFleft from "../../../components/home/ButtonFleft.vue";
import Ellipsis from '../../../components/utils/ellipsis.vue';
export default {
    name: "Tables",
    props: ["module"],
    components: {
        ButtonFleft,
        Ellipsis
    },
    data() {
        return {
            dataEllipsis: {
                buttons: {
                    open: {
                    name: "edit",
                    icon: "far fa-edit",
                    fn: function() {console.log("Edit");}
                    },
                    note: {
                    name: "case note",
                    icon: "far fa-comments",
                    fn: function() {console.log("comments");}
                    },
                }
        },
            newList: {
                title: this.$i18n.t("New List"),
                class: "btn-success",
                onClick: () => {
                    //TODO button
                }
            },
            importList: {
                title: this.$i18n.t("Import List"),
                class: "btn-success",   
                onClick: () => {
                    //TODO button
                }
            },
            columns: [
                "name",
                "process",
                "tableName",
                "owner",
                "createDate",
                "updateDate",
                "actions"
            ],
            tableData: [],
            options: {
                perPage:25,
                perPageValues:[25],
                filterable: true,
                headings: {
                    name: this.$i18n.t("ID_NAME"),
                    process: this.$i18n.t("ID_PROCESS"),
                    tableName: this.$i18n.t("ID_PM_TABLE"),
                    owner: this.$i18n.t("ID_OWNER"),
                    createDate: this.$i18n.t("ID_DATE_CREATED"),
                    updateDate: this.$i18n.t("ID_DATE_UPDATED"),
                    actions: ""
                },
                texts: {
                    count: this.$i18n.t("ID_SHOWING_FROM_RECORDS_COUNT"),
                    first: this.$i18n.t("ID_FIRST"),
                    last: this.$i18n.t("ID_LAST"),
                    filter: this.$i18n.t("ID_FILTER") + ":",
                    limit: this.$i18n.t("ID_RECORDS") + ":",
                    page: this.$i18n.t("ID_PAGE") + ":",
                    noResults: this.$i18n.t("ID_NO_MATCHING_RECORDS"),
                },
                requestFunction(data) {
                    return this.$parent.$parent.getCasesForVueTable(data);
                },
           
            }
        };
    },
    methods: {
        /**
         * Get cases data by module
         * @param {object} datas
         * @returns {object}
         */
        getCasesForVueTable(data) {
            let that = this,
                dt,
                paged,
                limit = data.limit,
                start = data.page === 1 ? 0 : limit * (data.page - 1),
                filters = {};
            filters = {
                offset: start,
                limit: limit
            };
            if (data && data.query) {
                filters["search"] = data.query;
            }
            _.forIn(this.filters, function (item, key) {
                if(filters && item.value) {
                    filters[item.filterVar] = item.value;
                }
            });
            return new Promise((resolutionFunc, rejectionFunc) => {
                Api.getCaseList(filters, that.module)
                .then((response) => {       
                    resolutionFunc({
                        data: response.data.data,        
                        count: response.data.total
                    });
                })
                .catch((e) => {
                    rejectionFunc(e);
                });
            });
        }
    }
};
</script>