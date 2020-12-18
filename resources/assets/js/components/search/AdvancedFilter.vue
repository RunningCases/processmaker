<template>
    <div>
        <b-container fluid class="bv-example-row" id="my-container">
            <b-row>
                <b-col md="4">
                    <div class="d-flex flex-row">
                        <SearchPopover
                            target="popover-target-1"
                            @savePopover="onOk"
                            :title="addSearchTitle"
                        >
                            <template v-slot:target-item>
                                <b-button
                                    id="popover-target-1"
                                    variant="success"
                                    size="sm"
                                    href="#"
                                    tabindex="0"
                                >
                                    <b-icon icon="plus"></b-icon>{{$t('ID_ADD_FILTER')}}
                                </b-button>
                            </template>
                            <template v-slot:body>
                                <b-form-group>
                                    <b-form-checkbox-group
                                        v-model="selected"
                                        :options="filterItems"
                                        value-field="id"
                                        text-field="optionLabel"
                                        name="flavour-2a"
                                        stacked
                                    ></b-form-checkbox-group>
                                </b-form-group>
                            </template>
                        </SearchPopover>
                        <b-button
                            size="sm"
                            @click="cleanAllTags"
                            variant="danger"
                            >{{$t('ID_CLEAN_ALL')}}</b-button
                        >
                    </div>
                </b-col>

                <b-col md="8">
                    <div class="d-flex flex-row-reverse">
                        <div class="p-2">
                            <b-button
                                @click="onClick"
                                variant="primary"
                                size="sm"
                            >
                                <b-icon icon="menu-button"></b-icon>{{$t('ID_SAVE_SEARCH')}}
                            </b-button>
                        </div>
                        <div class="p-2">
                            <b-button
                                variant="danger"
                                size="sm"
                                @click="onDeleteSearch"
                                :disabled="id == null"
                            >
                                <b-icon icon="trash"></b-icon>{{$t('ID_DELETE_SEARCH')}}
                            </b-button>
                        </div>
                        <div class="p-2">
                            <b-button
                                variant="success"
                                size="sm"
                                @click="onJumpCase"
                            >
                                <b-icon icon="arrow-up-right-square"></b-icon>
                                {{$t('ID_JUMP')}}
                            </b-button>
                        </div>
                        <div class="p-2">
                            <input
                                v-model="caseNumber"
                                size="1"
                                class="form-control"
                                :placeholder="$t('ID_CASE_NUMBER_CAPITALIZED')"
                                type="number"
                            />
                        </div>
                    </div>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <div class="d-flex flex-row">
                        <b-form-tags
                            input-id="tags-pills"
                            v-model="searchTags"
                            size="sm"
                        >
                            <template v-slot="{ tags, tagVariant, removeTag }">
                                <div
                                    class="d-inline-block"
                                    style="font-size: 1rem;"
                                >
                                    <b-form-tag
                                        v-for="tag in tags"
                                        @remove="customRemove(removeTag, tag)"
                                        :key="tag"
                                        :title="tag"
                                        :variant="tagVariant"
                                        class="mr-1"
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
                        <b-input-group-append>
                            <b-button
                                pill
                                variant="outline-secondary"
                                class="pull-right"
                                @click="onSearch"
                            >
                                <b-icon icon="search"></b-icon>
                            </b-button>
                        </b-input-group-append>
                    </div>
                </b-col>
            </b-row>

            <b-modal
                id="modal-prevent-closing"
                ref="saveFilter"
                :title="saveModalTitle"
                @show="resetModal"
                @hidden="resetModal"
                @ok="handleOk"
            >
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <b-form-group
                        :state="nameState"
                        :label="$t('ID_NAME')"
                        label-for="name-input"
                        :invalid-feedback="$t('ID_REQUIRED_FIELD')"
                    >
                        <b-form-input
                            id="name-input"
                            v-model="localName"
                            :state="nameState"
                            required
                        ></b-form-input>
                    </b-form-group>
                </form>
            </b-modal>
        </b-container>
    </div>
</template>

<script>
import SearchPopover from "./popovers/SearchPopover.vue";
import CaseNumber from "./popovers/CaseNumber.vue";

import DateFilter from "./popovers/DateFilter.vue";
import CaseTitle from "./popovers/CaseTitle.vue";
import ProcessName from "./popovers/ProcessName.vue";
import CasePriority from "./popovers/CasePriority.vue";
import CaseStatus from "./popovers/CaseStatus.vue";
import CurrentUser from "./popovers/CurrentUser.vue";
import TaskTitle from "./popovers/TaskTitle.vue";
import api from "./../../api/index";

export default {
    name: "AdvancedFilter",
    props: ["id", "name", "filters"],
    components: {
        SearchPopover,
        CaseNumber,
        CaseTitle,
        ProcessName,
        CasePriority,
        CaseStatus,
        CurrentUser,
        DateFilter,
        TaskTitle
    },
    data() {
        return {
            addSearchTitle: this.$i18n.t('ID_ADD_SEARCH_FILTER_CRITERIA'),
            searchTags: [],
            filterItems: [
                {   
                    type: "CaseNumber",
                    id: "caseNumber",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_IUD')}`,
                    optionLabel: this.$i18n.t('ID_IUD'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_TO_CASES_TO_SEARCH'),
                    tagText: "",
                    tagPrefix:  this.$i18n.t('ID_IUD'),
                    items:[
                        {
                            id: "filterCases",
                            value: ""
                        }
                    ],
                    makeTagText: function (params, data) {
                          return  `${params.tagPrefix}: ${data[0].value}`;
                    }
                },
                {
                    type: "CaseTitle",
                    id: "caseTitle",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_CASE_TITLE')}`,
                    optionLabel: this.$i18n.t('ID_CASE_TITLE'),
                    tagPrefix:  this.$i18n.t('ID_CASE_TITLE'),
                    detail: "",
                    tagText: "",
                    items:[
                        {
                            id: "caseTitle",
                            value: ""
                        }
                    ],
                    makeTagText: function (params, data) {
                        return  `${this.tagPrefix}: ${data[0].value}`;
                    }
                },
                {
                    type: "ProcessName",
                    id: "processName",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_PROCESS_NAME')}`,
                    optionLabel: this.$i18n.t('ID_PROCESS_NAME'),
                    detail: "",
                    tagText: "",
                    tagPrefix:  this.$i18n.t('ID_PROCESS_NAME'),
                    items:[
                        {
                            id: "process",
                            value: "",
                            options: [],
                            placeholder: this.$i18n.t('ID_PROCESS_NAME')
                        }
                    ],
                    makeTagText: function (params, data) {
                        return  `${params.tagPrefix}:  ${data[0].options && data[0].options.label || ''}`;
                    }
                },
                {
                    type: "TaskTitle",
                    id: "taskTitle",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_TASK_NAME')}`,
                    optionLabel: this.$i18n.t('ID_TASK'),
                    detail: "",
                    tagText: "",
                    tagPrefix:  this.$i18n.t('ID_SEARCH_BY_TASK_NAME'),
                    items:[
                        {
                            id: "task",
                            value: "",
                            options: [],
                            placeholder: this.$i18n.t('ID_TASK_NAME')
                        }
                    ],
                    makeTagText: function (params, data) {
                        return  `${this.tagPrefix}: ${data[0].label || ''}`;
                    }
                },
                {
                    type: "CurrentUser",
                    id: "currentUser",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_CURRENT_USER')}`,
                    optionLabel: this.$i18n.t('ID_CURRENT_USER'),
                    detail: "",
                    placeholder: this.$i18n.t('ID_USER_NAME'),
                    tagText: "",
                    tagPrefix:  this.$i18n.t('ID_USER'),
                    items:[
                        {
                            id: "userId",
                            value: "",
                            options: [],
                            placeholder: this.$i18n.t('ID_USER_NAME')
                        }
                    ],
                    makeTagText: function (params, data) {
                        return  `${params.tagPrefix} : ${data[0].label || ''}`;
                    }
                },
                {
                    type: "DateFilter",
                    id: "startDate",
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_START_DATE')}`,
                    optionLabel: this.$i18n.t('ID_BY_START_DATE'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_OF_CASES_START_DATE_TO_SEARCH'),
                    tagText: "",
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
                    optionLabel: this.$i18n.t('ID_FINISH_DATE'),
                    detail: this.$i18n.t('Please set a range of cases Finish Date to search:'),
                    tagText: "",
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
                }
              
            ],
            selected: "",
            itemModel: {},
            filterModel: {},
            selected: [],
            caseNumber: "",
            saveModalTitle: this.$i18n.t('ID_SAVE_SEARCH'),
            localName: "",
            nameState: null,        
        };
    },
    watch: {
        filters: { 
            immediate: true, 
            handler(newVal, oldVal) { 
                this.searchTags = [];
                this.selected = [];
                this.setFilters(newVal);
            },
        }
    },
    

    methods: {
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
                    self.selected.push(component.id);
                    self.itemModel[component.id] = component;
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
            if (this.itemModel[id]  && typeof this.itemModel[id] .makeTagText === "function") {
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
         * Add filter criteria save button handler
         */
        onOk() {
            let self = this,
                element,
                tmp,
                item,
                initialFilters = [];
            this.$root.$emit('bv::hide::popover');   
            for (var i = 0; i < this.selected.length; i+=1) {
                item = this.selected[i];
                element = _.find(this.filterItems, function(o) { return o.id === item; });
                if  (element) {
                    _.forEach(element.items, function(value, key) {
                        tmp = {
                            filterVar: value.id,
                            fieldId: item,
                            value: '',
                            label: "",
                            options: []
                        };
                        initialFilters.push(tmp);
                    });
                }
                
            }
            this.$emit("onUpdateFilters", initialFilters);
            
            
        },

        cleanAllTags() {
            this.searchTags = [];
            this.selected = [];
            this.$emit("onUpdateFilters", {});
        },
        customRemove(removeTag, tag) {
            let temp = [];
             _.forEach(this.filters, function(item, key) {
                 if(item.fieldId !== tag) {
                     temp.push(item);
                 }
             });
            this.$emit("onUpdateFilters", temp);
        },
        onSearch() {
            this.$emit("onSearch", this.filters);
        },
        updateSearchTag(params) {          
            let temp = this.filters.concat(params);
            temp = [...new Set([...this.filters,...params])]
            this.$emit("onUpdateFilters", temp);
        },
        onJumpCase() {
            this.$emit("onJumpCase",  this.caseNumber);
        },
        onClick() {
            if (this.id) {
                this.updateData(this.id);
            } else {
                this.$refs['saveFilter'].show();
            }
            
        },
        /**
         * Delete Search handler
         */
        onDeleteSearch() {
             this.$emit("onRemoveFilter", this.id);
        },
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity();
            this.nameState = valid;
            return valid;
        },
        resetModal() {
            this.localName = "";
            this.nameState = null;
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            // Trigger submit handler
            this.handleSubmit();
        },
        handleSubmit() {
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return;
            }
            // Hide the modal manually
            this.$nextTick(() => {
                this.$bvModal.hide("modal-prevent-closing");
                this.saveData(this.localName);
            });
        },
        /**
         * Save Data Handler
         */ 
        saveData(name) {
            this.$emit("onSubmit", {
                name: name,
                filters: this.filters
            });
        },
        /**
         * Update Data Handler
         * @param {string} id - filter id
         */ 
        updateData(id) {
            this.$emit("onSubmit", {
                type: "update",
                id: id,
                name: this.name,
                filters: this.filters
            });
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
</style>

    },
};
</script>
<style scoped>
.bv-example-row .row + .row {
    margin-top: 1rem;
}

.bv-example-row-flex-cols .row {
    min-height: 10rem;
}
</style>
