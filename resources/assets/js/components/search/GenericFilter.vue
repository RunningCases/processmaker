<template>
    <div>
        <b-container fluid class="bv-example-row" id="my-container">
            <b-row>
                <b-col md="10"><h5>Advanced Search</h5></b-col>
                <b-col md="2">
                    <b-button variant="success" size="sm" class="float-right"
                        ><b-icon icon="plus"></b-icon>Request</b-button
                    >
                </b-col>
            </b-row>
            <b-row>
                <b-col md="4">
                    <div class="d-flex flex-row">
                        <SearchPopover
                            target="popover-target-1"
                            @closePopover="onClose"
                            @savePopover="onOk"
                        >
                            <template v-slot:target-item>
                                <b-button
                                    id="popover-target-1"
                                    variant="success"
                                    size="sm"
                                    href="#"
                                    tabindex="0"
                                >
                                    <b-icon icon="plus"></b-icon>Add Filter
                                </b-button>
                            </template>
                            <template v-slot:body>
                                <b-form-group
                                    label="Add Serch filter criteria: "
                                >
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
                            >Clean All</b-button
                        >
                    </div>
                </b-col>

                <b-col md="8">
                    <div class="d-flex flex-row-reverse">
                        <div class="p-2">
                            <b-button v-b-modal.modal-prevent-closing variant="primary" size="sm">
                                <b-icon icon="menu-button"></b-icon>Save Search
                            </b-button>
                        </div>
                        <div class="p-2">
                            <b-button variant="danger" size="sm" @click="onDeleteSearch">
                                <b-icon icon="trash"></b-icon>Delete Search
                            </b-button>
                        </div>
                        <div class="p-2">
                            <b-button variant="success" size="sm" @click="onJumpCase">
                                <b-icon icon="arrow-up-right-square"></b-icon>
                                Jump
                            </b-button>
                        </div>
                        <div class="p-2">
                            <input v-model="caseNumber" size="7" type="text" class="form-control"  placeholder="Case Number"/>
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
                                         <component v-bind:is="tag"
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
                ref="modal"
                :title="saveModalTitle"
                @show="resetModal"
                @hidden="resetModal"
                @ok="handleOk"
                >
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <b-form-group
                    :state="nameState"
                    label="Name"
                    label-for="name-input"
                    invalid-feedback="Name is required"
                    >
                    <b-form-input
                        id="name-input"
                        v-model="name"
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
import CaseTitle from "./popovers/CaseTitle.vue";
import ProcessName from "./popovers/ProcessName.vue";
import ParticipatedLevel from "./popovers/ParticipatedLevel.vue";
import CasePriority from "./popovers/CasePriority.vue";
import SentBy from "./popovers/SentBy.vue";
import CaseStatus from "./popovers/CaseStatus.vue";
export default {
    name: "GenericFilter",
    components: {
        SearchPopover,
        CaseNumber,
        DueDate,
        CaseTitle,
        ProcessName,
        ParticipatedLevel,
        SentBy,
        CaseStatus
    },
    data() {
        return {
            searchTags: [],
            searchTagsModels: {
                "CaseNumber": {
                    text: "#",
                    tagText: "From: 1, 3, 7 To: 15",
                    default: {
                        from: "",
                        to: "",
                    },
                },
                "DueDate": {
                    text: "Due Date",
                    tagText: "From: 01-01-2020 To: 01-01-2020",
                     default: {
                        from: "",
                        to: "",
                    },

                },
                "CaseTitle": {
                    text: "Case",
                    tagText: "Case: title",
                    default: {
                        name: ""
                    }

                },
                "ProcessName": {
                    text: "Process",
                    tagText: "Process: name",
                    default: {
                        name: ""
                    }

                },
                "ParticipatedLevel": {
                    text: "Participated",
                    tagText: "Process: name",
                    default: {
                        name: ""
                    }

                },
                "CasePriority": {
                    text: "Priority",
                    tagText: "Process: name",
                    title: "Filter: Priority",
                    label: "Please select the priority for the search",
                    options: [
                        { text: 'Very Low', value: '1' },
                        { text: 'Low', value: '2' },
                        { text: 'Niormal', value: '3' },
                        { text: 'Very High', value: '4' },
                        { text: 'High', value: '5' }
                    ]

                },
                "SentBy": {
                    text: "Sent By",
                    title: "Filter: Sent By",
                    placeHolder: "User name",
                },
                "CaseStatus": {
                    text: "Status",
                    title: "Filter: Case Status",
                    label: "Please select the status for the search",
                    options: [
                        { text: 'Draft', value: '1' },
                        { text: 'To Do', value: '2' },
                        { text: 'Completed', value: '4' },
                        { text: 'Canceled', value: '5' },
                        { text: 'Paused', value: '6' }
                    ]

                },
            },
            text: "",
            selected: [],
            jsonFilter: {},
            caseNumber: "",
            saveModalTitle: "SaveSearch",
            name: '',
            nameState: null
        };
    },
    computed: {
        filterOptions: function() {
            let options = [];
            _.forIn(this.searchTagsModels, function(value, key) {
                options.push({
                    text: value.text,
                    value: key,
                });
            });
            return options;
        }
    },
    methods: {
        onClose() {
            this.popoverShow = false;
        },
        onOk() {
            this.searchTags = [...this.searchTags, ...this.selected];
            this.onClose();
        },
        cleanAllTags() {
            this.searchTags = [];
            this.jsonFilter = {
                search: "",
            };
        },
        customRemove (removeTag, tag) {
            removeTag(tag);
            this.jsonFilter = {
                search: "",
            };
        },
        onSearch() {
            this.$emit("onSearch", this.jsonFilter);
        },
        updateSearchTag(params) {
            this.jsonFilter = {...this.jsonFilter, ...params}
        },
        onJumpCase() {
            this.$emit("onJumpCase", {caseNumber: this.caseNumber});
        },
      
        /**
         * Delete Search handler
         */
        onDeleteSearch () {
        },
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity();
            this.nameState = valid;
            return valid;
        },
        resetModal() {
            this.name = '';
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
                this.$bvModal.hide('modal-prevent-closing');
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