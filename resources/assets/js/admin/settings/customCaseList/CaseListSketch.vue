<template>
    <div id="home">
        <div class="demo">
            <div class="container">
                <h5>{{ $t("ID_NEW_CASES_LISTS") }}</h5>

                <div class="row">
                    <div class="col-sm">
                        <b-row>
                            <b-col>
                                <b-form-group
                                    id="nameLabel"
                                    label="Name"
                                    label-for="name"
                                >
                                    <b-form-input
                                        id="name"
                                        v-model="params.name"
                                        placeholder="Set a Case List Name"
                                        required
                                    ></b-form-input>
                                </b-form-group>
                            </b-col>
                            <b-col>
                                <b-form-group
                                    id="tableLabel"
                                    label="PM Table "
                                    label-for="name"
                                >
                                    <multiselect
                                        v-model="params.tableUid"
                                        :options="pmTablesOptions"
                                        placeholder="Chose an option"
                                        label="label"
                                        track-by="value"
                                        :show-no-results="false"
                                        @search-change="asyncFind"
                                        :loading="isLoading"
                                        id="ajax"
                                        :limit="10"
                                        :clear-on-select="true"
                                    >
                                    </multiselect>
                                </b-form-group>
                            </b-col>
                        </b-row>

                        <b-form-group
                            id="descriptionLabel"
                            label="Description "
                            label-for="description"
                        >
                            <b-form-textarea
                                id="description"
                                v-model="params.description"
                                placeholder="Some Text"
                                rows="1"
                                max-rows="1"
                            ></b-form-textarea>
                        </b-form-group>
                        <b-row>
                            <b-col cols="10">
                                <v-client-table
                                    :columns="columns"
                                    v-model="data"
                                    :options="options"
                                >
                                </v-client-table>
                            </b-col>
                            <b-col cols="2">
                                <!-- Control panel -->
                                <div class="control-panel">
                                    <div class="vertical-center">
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            @click="assignAll()"
                                            :disabled="isButtonDisabled"
                                        >
                                            <i
                                                class="fa fa-angle-double-right"
                                            ></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            @click="assignSelected()"
                                            :disabled="isButtonDisabled"
                                        >
                                            <i class="fa fa-angle-right"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            @click="unassignSelected()"
                                            :disabled="isButtonDisabled"
                                        >
                                            <i class="fa fa-angle-left"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            @click="unassignAll()"
                                            :disabled="isButtonDisabled"
                                        >
                                            <i
                                                class="fa fa-angle-double-right"
                                            ></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- End Control panel -->
                            </b-col>
                        </b-row>
                        <b-form-group
                            id="iconLabel"
                            label="Icon "
                            label-for="icon"
                        >
                            <icon-picker
                                @selected="onSelectIcon"
                                :default="params.iconList"
                            />
                        </b-form-group>
                        <div>
                            <b-form-group
                                id="menuColor"
                                label="Menu Color "
                                label-for="icon"
                            >
                                <verte
                                    :value="params.iconColor"
                                    id="icon"
                                    @input="onChangeColor"
                                    picker="square"
                                    menuPosition="left"
                                    model="hex"
                                >
                                    <svg viewBox="0 0 50 50">
                                        <path
                                            d="M 10 10 H 90 V 90 H 10 L 10 10"
                                        />
                                    </svg>
                                </verte>
                            </b-form-group>
                        </div>

                        <div>
                            <b-form-group
                                id="screenColor"
                                label="Screen Color Icon"
                                label-for="screen"
                            >
                                <verte
                                    :value="params.iconColorScreen"
                                    @input="onChangeColor"
                                    picker="square"
                                    menuPosition="left"
                                    model="hex"
                                >
                                    <svg viewBox="0 0 50 50">
                                        <path
                                            d="M 10 10 H 90 V 90 H 10 L 10 10"
                                        />
                                    </svg>
                                </verte>
                            </b-form-group>
                        </div>
                    </div>
                    <div class="col-sm">
                        <v-client-table
                            :columns="columnsCaseList"
                            v-model="data"
                            :options="caseListOptions"
                        >
                        </v-client-table>
                    </div>
                </div>
                <div>
                    <b-button variant="danger" @click="onCancel"
                        >Cancel</b-button
                    >
                    <b-button variant="outline-primary">Preview</b-button>
                    <b-button variant="success">Save</b-button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import utils from "../../../utils/utils";
import Multiselect from "vue-multiselect";
import api from "./../../../api/index";
import IconPicker from "../../../components/iconPicker/IconPicker.vue";

export default {
    name: "CaseListSketh",
    components: {
        Multiselect,
        IconPicker,
        IconPicker,
    },
    props: ["params"],
    data() {
        return {
            icon: "fas fa-user-cog",
            isLoading: false,
            isButtonDisabled: false,
            pmTablesOptions: [],
            columns: ["name", "field", "type", "source", "source"],

            data: utils.getData(),
            options: {
                headings: {
                    name: "Name",
                    field: "Field",
                    type: "Type",
                    source: "Source",
                },
                filterable: true,
            },
            columnsCaseList: [
                "name",
                "field",
                "type",
                "source",
                "typeOfSearching",
                "enableSearchFilter",
                "actions",
            ],
            caseListOptions: {
                headings: {
                    name: "Name",
                    field: "Field",
                    type: "Type",
                    typeOfSearching: "Type of Searching",
                    enableSearchFilter: "Enable Search Filter",
                },
                filterable: false,
                perPage: 1000,
                perPageValues: [],
                texts: {
                    count: "",
                },
            },
        };
    },
    mounted() {},
    methods: {
        unassignSelected() {},
        unassignAll() {},
        assignSelected() {},
        assignAll() {},
        onSelectIcon(data) {
            console.log(data);
            // this.params.iconList = data;
        },
        onChangeColor(color) {
            console.log(color);
            this.menuColor = color;
        },
        onCancel() {
            this.$emit("closeSketch");
        },
        onChange(e) {
            console.log(e);
        },
        /**
         * Find asynchronously in the server
         * @param {string} query - string from the text field
         */
        asyncFind(query) {
            let self = this;
            this.isLoading = true;
            self.processes = [];
            api.filters
                .processList(query)
                .then((response) => {
                    self.processes = [];
                    _.forEach(response.data, function(elem, key) {
                        self.pmTablesOptions.push({
                            label: elem.PRO_TITLE,
                            value: elem.PRO_ID,
                        });
                    });
                    this.isLoading = false;
                })
                .catch((e) => {
                    console.error(err);
                });
        },
    },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
.verte {
    position: relative;
    display: flex;
    justify-content: normal;
}
.control-panel {
    height: 100%;
    width: 8%;
    float: left;
    position: relative;
}
.vertical-center {
    margin: 0;
    position: absolute;
    top: 50%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
}
.vertical-center > button {
    width: 70%;
    margin: 5px;
}
</style>
