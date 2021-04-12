<template>
    <div>
        <SearchPopover
            target="popover-target-1"
            @savePopover="onOk"
            :title="addSearchTitle"
        >
            <template v-slot:body>
                <b-form-group>
                    <b-form-radio-group
                        v-model="selected"
                        :options="filterItems"
                        value-field="id"
                        text-field="optionLabel"
                        name="flavour-2a"
                        stacked
                    ></b-form-radio-group>
                </b-form-group>
            </template>
        </SearchPopover>

    <div class="p-1 v-flex">
      <h5 class="v-search-title">{{ title }}</h5>

      <b-input-group class="w-75 p-1">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span
              class="input-group-text bg-primary-pm text-white"
              id="popover-target-1"
              @click="searchClickHandler"
            >
              <b-icon icon="search"></b-icon
            ></span>
              <b-tooltip target="popover-target-1">{{$t('ID_MY_CASES_SEARCH')}}</b-tooltip>
          </div>
          <b-form-tags input-id="tags-pills" v-model="searchTags">
            <template v-slot="{ tags, tagVariant, removeTag }">
              <div class="d-inline-block" style="font-size: 1rem">
                <b-form-tag
                  v-for="tag in tags"
                  @remove="customRemove(removeTag, tag)"
                  :key="tag"
                  :title="tag"
                  :variant="tagVariant"
                  class="mr-1 badge badge-light"
                >
                  <div :id="tag">
                    <i class="fas fa-tags"></i>
                    {{ tagContent(tag) }}
                  </div>

                  <component
                    v-bind:is="tagComponent(tag)"
                    v-bind:info="tagInfo(tag)"
                    v-bind:tag="tag"
                    v-bind:filter="dataToFilter(tag)"
                    @updateSearchTag="updateSearchTag"
                  />
                </b-form-tag>
              </div>
            </template>
          </b-form-tags>
        </div>
      </b-input-group>
    </div>
    </div>
</template>

<script>
import SearchPopover from "./popovers/SearchPopover.vue";
import CaseIntegerNumber from "./popovers/CaseIntegerNumber.vue";
import CaseTitle from "./popovers/CaseTitle.vue";
import ProcessName from "./popovers/ProcessName.vue";
import DateFilter from "./popovers/DateFilter.vue";
import TaskTitle from "./popovers/TaskTitle.vue";
import api from "./../../api/index";

export default { 
    name: "MyCasesFilter",
    props: ["filters","title"],
    components:{
        SearchPopover,
        CaseIntegerNumber,
        CaseTitle,
        ProcessName,
        DateFilter,
        TaskTitle
    },
    data() {
        return {
            searchLabel: this.$i18n.t('ID_SEARCH'),
            addSearchTitle: this.$i18n.t('ID_ADD_SEARCH_FILTER_CRITERIA'),
            searchTags: [],
        
            filterItems: [
                {   
                    type: "CaseIntegerNumber",
                    id: "caseNumber",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_BY_CASE_NUMBER')}`,
                    optionLabel: this.$i18n.t('ID_BY_CASE_NUMBER'),
                    detail: this.$i18n.t('ID_PLEASE_SET_THE_CASE_NUMBER_TO_BE_SEARCHED'),
                    tagText: "",
                    tagPrefix:  this.$i18n.t('ID_SEARCH_BY_CASE_NUMBER'),
                    items:[
                        {
                            id: "caseNumber",
                            value: ""
                        }
                    ],
                    autoShow: true,
                    makeTagText: function (params, data) {
                          return  `${params.tagPrefix}: ${data[0].value}`;
                    }
                },
                {
                    type: "CaseTitle",
                    id: "caseTitle",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_BY_CASE_TITLE')}`,
                    optionLabel: this.$i18n.t('ID_BY_CASE_TITLE'),
                    tagPrefix:  this.$i18n.t('ID_SEARCH_BY_CASE_TITLE'),
                    detail: "",
                    tagText: "",
                    items:[
                        {
                            id: "caseTitle",
                            value: ""
                        }
                    ],
                    autoShow: true,
                    makeTagText: function (params, data) {
                        return  `${this.tagPrefix} ${data[0].value}`;
                    }
                },
                {
                    type: "ProcessName",
                    id: "processName",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_BY_PROCESS_NAME')}`,
                    optionLabel: this.$i18n.t('ID_BY_PROCESS_NAME'),
                    detail: "",
                    tagText: "",
                    tagPrefix:  this.$i18n.t('ID_SEARCH_BY_PROCESS_NAME'),
                    autoShow: true,
                    items:[
                        {
                            id: "process",
                            value: "",
                            options: [],
                            placeholder: this.$i18n.t('ID_PROCESS_NAME')
                        }
                    ],
                    makeTagText: function (params, data) {

                        return  `${this.tagPrefix} ${data[0].options && data[0].options.label || ''}`;
                    }
                },
                {
                    type: "TaskTitle",
                    id: "taskTitle",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_BY_TASK')}`,
                    optionLabel: this.$i18n.t('ID_BY_TASK'),
                    detail: "",
                    tagText: "",
                    tagPrefix:  this.$i18n.t('ID_SEARCH_BY_TASK_NAME'),
                    autoShow: true,
                    items:[
                        {
                            id: "task",
                            value: "",
                            options: [],
                            placeholder: this.$i18n.t('ID_TASK_NAME')
                        }
                    ],
                    makeTagText: function (params, data) {
                        return  `${this.tagPrefix} ${data[0].label || ''}`;
                    }
                },
                {
                    type: "DateFilter",
                    id: "startDate",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_START_DATE')}`,
                    optionLabel: this.$i18n.t('ID_BY_START_DATE'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_OF_CASES_START_DATE_TO_SEARCH'),
                    tagText: "",
                    autoShow: true,
                    tagPrefix:  this.$i18n.t('ID_SEARCH_BY_START_DATE'),
                    items:[
                        {
                            id: "startCaseFrom",
                            value: "",
                            label: this.$i18n.t('ID_FROM_START_DATE')
                        },
                        {
                            id: "startCaseTo",
                            value: "",
                            label: this.$i18n.t('ID_TO_START_DATE')
                        }
                    ],
                    makeTagText: function (params, data) {
                        return  `${params.tagPrefix} ${data[0].value} - ${data[1].value}`;
                    }
                },
                {
                    type: "DateFilter",
                    id: "finishDate",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_FINISH_DATE')}`,
                    optionLabel: this.$i18n.t('ID_BY_FINISH_DATE'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_OF_CASES_FINISH_DATE_TO_SEARCH'),
                    tagText: "",
                    autoShow: true,
                    tagPrefix:  this.$i18n.t('ID_SEARCH_BY_FINISH_DATE'),
                    items:[
                        {
                            id: "finishCaseFrom",
                            value: "",
                            label: this.$i18n.t('ID_FROM_FINISH_DATE'),
                        },
                        {
                            id: "finishCaseTo",
                            value: "",
                            label: this.$i18n.t('ID_TO_FINISH_DATE'),
                        }
                    ],
                    makeTagText: function (params, data) {
                        return  `${params.tagPrefix} ${data[0].value} - ${data[1].value}`;
                    }
                },
            ],
            selected: "",
            itemModel: {}
        };
    },
    mounted() {
        // Force to load filters when mounted the component
        let fils= this.filters;
        if(_.isArray(this.filters)){
            _.forEach(fils,(o)=>{
                o.autoShow = false;
            });
            this.setFilters(fils);
        }
  },
    watch: {
        filters: function (filters) {
            this.searchTags = [];
            this.selected = "";
            this.setFilters(filters);
            
        }
    },
    methods: {
        /**
         * Add filter criteria save button handler
         */
        onOk() {
            let self = this,
                element,
                initialFilters = [],
                item;
                // element = _.find(this.filterItems, function(o) { return o.id === self.selected; });
            this.$root.$emit('bv::hide::popover');
            element = _.find(this.filterItems, function(o) { return o.id === self.selected; });
                if  (element) {
                    _.forEach(element.items, function(value, key) {
                        item = {
                            filterVar: value.id,
                            fieldId: self.selected,
                            value:  '',
                            label: "",
                            options: []
                        };
                        initialFilters.push(item);
                    });
                }
            this.$emit("onUpdateFilters", {params: initialFilters, refresh: false}); 
        },
        /**
         * Set Filters and make the tag labels
         * @param {object} filters json to manage the query 
         */
        setFilters(filters) {
            let self = this;
            _.forEach(filters, function(item, key) {
                let component = _.find(self.filterItems, function(o) { return o.id === item.fieldId; });
                if (component) {
                    self.searchTags.push(component.id);
                    self.selected = component.id;
                    self.itemModel[component.id] = component;
                    self.itemModel[component.id].autoShow = typeof item.autoShow !== "undefined" ? item.autoShow : true;
                }
            });
        },
        dataToFilter(id) {
            let data = [];
            _.forEach(this.filters, function(item) { 
                if (item.fieldId === id) {
                    data.push(item);
                }
            });
            return data;
        },
        /**
         * 
         */
        tagContent(id) {
            if (this.itemModel[id]  && typeof this.itemModel[id].makeTagText === "function") {
                return this.itemModel[id].makeTagText(this.itemModel[id],  this.dataToFilter(id));
            }
            return "";
        },
        tagComponent(id) {
            if (this.itemModel[id]) {
                return this.itemModel[id].type;
            }
            return null;
        },
    
        tagInfo(id) {
             if (this.itemModel[id]) {
                return this.itemModel[id];
            }
            return null;
        },
        /**
         * Remove from tag button
         * @param {function} removeTag - default callback
         * @param {string} tag filter identifier
         */
        customRemove(removeTag, tag) {
            this.selected = "";
            this.$emit("onUpdateFilters",  {params: [], refresh: true});
        },
        /**
         * Update the filter model this is fired from filter popaver save action
         * @param {object} params - arrives the settings
         * @param {string} tag filter identifier
         */
        updateSearchTag(params) {          
            let temp = this.filters.concat(params);
            temp = [...new Set([...this.filters,...params])]
            this.$emit("onUpdateFilters",  {params: temp, refresh: true});
        },    
        searchClickHandler() {
            this.$root.$emit('bv::hide::popover');
        }
    }
};
</script>
<style scoped>
.bv-example-row .row + .row {
    margin-top: 1rem;
}

.bv-example-row-flex-cols .row {
    min-height: 10rem;
}
.bg-primary-pm {
  background-color: #0099dd;
}

.v-flex {
  display: flex;
}

.v-search-title {
  padding-right: 20px;
  line-height: 40px;
}
</style>

