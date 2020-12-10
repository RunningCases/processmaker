<template>
    <div>
        <b-container fluid class="bv-example-row" id="my-container">
            <b-alert
                :show="dismissCountDown"
                dismissible
                :variant="variant"
                @dismissed="dismissCountDown = 0"
                @dismiss-count-down="countDownChanged"
            >
                {{ message }}
            </b-alert>
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
import ParticipatedLevel from "./popovers/ParticipatedLevel.vue";
import CasePriority from "./popovers/CasePriority.vue";
import TaskName from "./popovers/TaskName.vue";
import CaseStatus from "./popovers/CaseStatus.vue";
import CurrentUser from "./popovers/CurrentUser.vue";
import api from "./../../api/index";

export default {
    name: "GenericFilter",
    props: ["id", "name"],
    components: {
        SearchPopover,
        CaseNumber,
        DueDate,
        LastModifiedDate,
        CaseTitle,
        ProcessName,
        ParticipatedLevel,
        TaskName,
        CaseStatus,
        CasePriority,
        CurrentUser
    },
    data() {
        return {
            addSearchTitle: this.$i18n.t('ID_ADD_SEARCH_FILTER_CRITERIA'),
            dismissSecs: 5,
            dismissCountDown: 0,
            message: "",
            variant: "info",
            searchTags: [],
            searchTagsModels: {
                CaseNumber: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_CASE')}${this.$i18n.t('ID_IUD')}`,
                    optionLabel: this.$i18n.t('ID_IUD'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_TO_CASES_TO_SEARCH')
                },
                DueDate: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_DUE_DATE')}`,
                    optionLabel: this.$i18n.t('ID_DUE_DATE'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_OF_CASES_DUE_DATE_TO_SEARCH')
                },
                LastModifiedDate: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_LAST_MODIFIED_DATE')}`,
                    optionLabel: this.$i18n.t('ID_LAST_MODIFIED_DATE'),
                    detail: this.$i18n.t('ID_PLEASE_SET_A_RANGE_OF_LAST_MODIFIED_CASES_DATE_TO_SEARCH')
                },
                CaseTitle: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_CASE_TITLE')}`,
                    optionLabel: this.$i18n.t('ID_CASE_TITLE'),
                    detail: ""
                },
                ProcessName: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_PROCESS_NAME')}`,
                    optionLabel: this.$i18n.t('ID_PROCESS_NAME'),
                    detail: "",
                    placeholder: this.$i18n.t('ID_PROCESS_NAME')
                },
                CasePriority: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_PRIORITY')}`,
                    optionLabel: this.$i18n.t('ID_PRIORITY'),
                    detail: this.$i18n.t('ID_PLEASE_SELECT_THE_PRIORITY_FOR_THE_SEARCH'),
                    options: [
                        { text: this.$i18n.t('ID_VERY_LOW'), value: "VL" },
                        { text: this.$i18n.t('ID_LOW'), value: "L" },
                        { text: this.$i18n.t('ID_NORMAL'), value: "N" },
                        { text: this.$i18n.t('ID_HIGH'), value: "H" },
                        { text: this.$i18n.t('ID_VERY_HIGH'), value: "VH" }
                    ]
                },
                TaskName: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_TASK')}`,
                    optionLabel: this.$i18n.t('ID_TASK'),
                    detail: "",
                    placeholder: this.$i18n.t('ID_TASK_NAME')
                },
                CaseStatus: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_STATUS')}`,
                    optionLabel: this.$i18n.t('ID_STATUS'),
                    detail: this.$i18n.t('ID_PLEASE_SELECT_THE_STATUS_FOR_THE_SEARCH'),
                    options: [
                        { text: this.$i18n.t('ID_CASES_STATUS_DRAFT'), value: "DRAFT" },
                        { text: this.$i18n.t('ID_CASES_STATUS_TO_DO'), value: "TO_DO" },
                        { text: this.$i18n.t('ID_CASES_STATUS_COMPLETED'), value: "COMPLETED" },
                        { text: this.$i18n.t('ID_CASES_STATUS_CANCELLED'), value: "CANCELLED" },
                        { text: this.$i18n.t('ID_CASES_STATUS_PAUSED'), value: "PAUSED" },
                    ]
                },
                CurrentUser: {
                    title: `${this.$i18n.t('ID_FILTER')}: ${this.$i18n.t('ID_CURRENT_USER')}`,
                    optionLabel: this.$i18n.t('ID_CURRENT_USER'),
                    detail: "",
                    placeholder: this.$i18n.t('ID_USER_NAME'),
                    default: {
                        name: "",
                    },
                },
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
        /**
         * Updates the alert dismiss value to update
         * dismissCountDown and decrease
         * @param {mumber}
         */
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown;
        },
        /**
         * Show the alert message
         * @param {string} message - message to be displayen in the body
         * @param {string} type - alert type
         */
        showAlert(message, type) {
            this.message = message;
            this.variant = type || "info";
            this.dismissCountDown = this.dismissSecs;
        },
        onClose() {
        },
        onOk() {
            this.$root.$emit('bv::hide::popover');
            this.searchTags = [...this.searchTags, ...this.selected];
        },
        cleanAllTags() {
            this.searchTags = [];
            this.jsonFilter = {
                search: "",
            };
        },
        customRemove(removeTag, tag) {
            removeTag(tag);
            this.jsonFilter = {
                search: "",
            };
        },
        onSearch() {
            this.$emit("onSearch", this.jsonFilter);
        },
        updateSearchTag(params) {
            this.jsonFilter = { ...this.jsonFilter, ...params };
        },
        onJumpCase() {
            this.$emit("onJumpCase",  this.caseNumber);
        },
        onClick() {
            if (this.id) {
                this.updateData(this.name);
            } else {
                this.$refs['saveFilter'].show();
            }
            
        },

        /**
         * Delete Search handler
         */
        onDeleteSearch() {
            api.filters
                .delete({
                    id: this.id,
                })
                .then((response) => {
                    
                    this.$emit("onRemoveFilter", this.id);
                })
                .catch((e) => {
                    this.showAlert(e.message, "danger");
                });
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
        saveData(name) {
            api.filters
            .post({
                name: name,
                filters: JSON.stringify({ uno: "first" }),
            })
            .then((response) => {
                this.$emit("onSubmit", response.data);
            })
            .catch((e) => {
                this.showAlert(e.message, "danger");
            });
        },
        updateData() {
            this.onDeleteSearch();
            this.saveData(this.name);
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
