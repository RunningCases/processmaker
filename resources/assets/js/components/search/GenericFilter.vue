<template>
    <div>
        <b-container fluid class="bv-example-row" id="my-container">
          
            <b-row>
                <b-col md="10"><h5>{{$t('ID_OPEN_SEARCH')}}</h5></b-col>
            </b-row>
            <b-row>
                <b-col md="4">
                    <div class="d-flex flex-row">
                        <SearchPopover
                            target="popover-target-1"
                            @closePopover="onClose"
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
                                        :options="filterOptions"
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
                                            {{ searchTagsModels[tag].tagText }}
                                        </div>
                                        <component
                                            v-bind:is="tag"
                                            v-bind:info="searchTagsModels[tag]"
                                            v-bind:tag="tag"
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
import DueDate from "./popovers/DueDate.vue";
import LastModifiedDate from "./popovers/LastModifiedDate.vue";
import CaseTitle from "./popovers/CaseTitle.vue";
import ProcessName from "./popovers/ProcessName.vue";
import api from "./../../api/index";

export default {
    name: "GenericFilter",
    props: ["id", "name", "filters"],
    components: {
        SearchPopover,
        CaseNumber,
        DueDate,
        LastModifiedDate,
        CaseTitle,
        ProcessName
    },
    data() {
        return {
            addSearchTitle: this.$i18n.t('ID_ADD_SEARCH_FILTER_CRITERIA'),
            
            searchTags: [],
            searchTagsModels: {
                CaseNumber: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_CASE')}${this.$i18n.t('ID_IUD')}`,
                    optionLabel: this.$i18n.t('ID_IUD'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_TO_CASES_TO_SEARCH'),
                    tagText: "",
                    filterBy: ["filterCases"],
                    values: {}
                },
                DueDate: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_DUE_DATE')}`,
                    optionLabel: this.$i18n.t('ID_DUE_DATE'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_OF_CASES_DUE_DATE_TO_SEARCH'),
                    tagText: "",
                    filterBy: ["dueDateFrom", "dueDateTo"],
                    values: {}
                },
                LastModifiedDate: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_LAST_MODIFIED_DATE')}`,
                    optionLabel: this.$i18n.t('ID_LAST_MODIFIED_DATE'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_OF_LAST_MODIFIED_CASES_DATE_TO_SEARCH'),
                    tagText: "",
                    filterBy: ["delegationDateFrom", "delegationDateTo"],
                    values: {}
                },
                CaseTitle: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_CASE_TITLE')}`,
                    optionLabel: this.$i18n.t('ID_CASE_TITLE'),
                    detail: "",
                    tagText: "",
                    filterBy: ["caseTitle"],
                    values: {}
                },
                
                ProcessName: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_PROCESS_NAME')}`,
                    optionLabel: this.$i18n.t('ID_PROCESS_NAME'),
                    detail: "",
                    placeholder: this.$i18n.t('ID_PROCESS_NAME'),
                    tagText: "",
                    filterBy: ["process", "processOption"],
                    processOption: {"PRO_TITLE": ""}
                }
            },
            text: "",
            selected: [],
            jsonFilter: {},
            caseNumber: "",
            saveModalTitle: this.$i18n.t('ID_SAVE_SEARCH'),
            localName: "",
            nameState: null,        
        };
    },
    watch: {
        filters: function (filters) {
          this.searchTags = [];
          this.searchTags = [];
          this.setFilters(filters);
        }
    },
    
    computed: {
        filterOptions: function() {
            let options = [];
            _.forIn(this.searchTagsModels, function(value, key) {
                options.push({
                    text: value.optionLabel,
                    value: key,
                });
            });
            return options;
        },
    },
    methods: {
       
        onClose() {
        },
        setFilters(filters) {
            let that = this;
            _.forIn(filters, function(value, key) {
                let temp = that.createTagText(key, value);
                that.searchTags.push(key);
                that.selected.push(key);
            });
        },
        onOk() {
            let initialFilters = {};
            this.$root.$emit('bv::hide::popover');
            for (var i = 0; i < this.selected.length; i++) {
                let item = this.selected[i];
                initialFilters[item] = {};
                if(this.searchTagsModels[item].filterBy) {
                    for (var j = 0; j < this.searchTagsModels[item].filterBy.length; j++) { 
                        initialFilters[item][this.searchTagsModels[item].filterBy [j]] = "";
                    }
                }
                
            }
            this.$emit("onUpdateFilters", initialFilters) 
        },
        createTagText(type, params) {
            let label = "";
            switch (type) {
                case "CaseNumber":
                    label = `${this.$i18n.t("ID_IUD")}: ${params.filterCases}`
                    this.searchTagsModels[type].values["filterCases"] =  params.filterCases;
                    break;
                case "DueDate":
                    label = `${this.$i18n.t('ID_FROM')}: ${params.dueDateFrom} ${this.$i18n.t('ID_TO')}:  ${params.dueDateTo}`;
                    this.searchTagsModels[type].values["dueDateFrom"] =  params.dueDateFrom;
                    this.searchTagsModels[type].values["dueDateTo"] =  params.dueDateTo;
                    break;
                case "LastModifiedDate":
                    label = `${this.$i18n.t('ID_FROM')}: ${params.delegationDateFrom} ${this.$i18n.t('ID_TO')}:  ${params.delegationDateTo}`;
                    this.searchTagsModels[type].values["delegationDateFrom"] =  params.delegationDateFrom;
                    this.searchTagsModels[type].values["delegationDateTo"] =  params.delegationDateTo;
                    break;
                case "CaseTitle":
                    label = `${this.$i18n.t("ID_CASE_TITLE")}: ${params.caseTitle}`;
                    this.searchTagsModels[type].values["caseTitle"] =  params.caseTitle;
                    break;
                case "ProcessName":
                    label = `${this.$i18n.t("ID_PROCESS")}: ${params.processOption.PRO_TITLE || ''}`;
                    this.searchTagsModels[type].processOption =  params.processOption || null;
                    break;
                default:
                    break;
            }
            this.searchTagsModels[type].tagText = label;
           
        },
        cleanAllTags() {
            this.searchTags = [];
            this.selected = [];
            this.$emit("onUpdateFilters", {});
        },
        customRemove(removeTag, tag) {
            let temp = { ...this.filters};
            delete temp[tag];
            removeTag(tag);
            this.$emit("onUpdateFilters", temp);
        },
        onSearch() {
            this.$emit("onSearch", this.filters);
        },
        updateSearchTag(params) {          
            this.$emit("onUpdateFilters", { ...this.filters, ...params });
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
