<template>
    <div id="home">
        <div class="demo">
            <div class="container">
                <h5>{{ $t("ID_NEW_CASES_LISTS") }} ({{ module.title }})</h5>
                <b-form @submit="onSubmit">
                    <b-row>
                        <b-col cols="6">
                            <b-row>
                                <b-col>
                                    <b-form-group
                                        id="nameLabel"
                                        :label="$t('ID_NAME')"
                                        label-for="name"
                                    >
                                        <b-form-input
                                            id="name"
                                            v-model="params.name"
                                            :state="isValidName"
                                            :placeholder="
                                                $t('ID_SET_A_CASE_LIST_NAME')
                                            "
                                        ></b-form-input>
                                        <b-form-invalid-feedback
                                            :state="isValidName"
                                        >
                                            {{ $t("ID_REQUIRED_FIELD") }}
                                        </b-form-invalid-feedback>
                                    </b-form-group>
                                </b-col>
                                <b-col>
                                    <div :class="{ invalid: isValidTable === false }">
                                        <label>{{ $t("ID_PM_TABLE") }}</label>
                                        <multiselect
                                            v-model="pmTable"
                                            :options="pmTablesOptions"
                                            :placeholder="
                                                $t('ID_CHOOSE_OPTION')
                                            "
                                            label="label"
                                            track-by="value"
                                            :show-no-results="false"
                                            @search-change="asyncFind"
                                            @select="onSelect"
                                            :loading="isLoading"
                                            id="ajax"
                                            :limit="10"
                                            :clear-on-select="true"
                                        >
                                        </multiselect>
                                        <label
                                            :class="{
                                                'd-block invalid-feedback': isValidTable === false
                                            }"
                                            v-show="isValidTable === false"
                                            >{{
                                                $t("ID_REQUIRED_FIELD")
                                            }}</label
                                        >
                                    </div>
                                </b-col>
                            </b-row>

                            <b-form-group
                                id="descriptionLabel"
                                :label="$t('ID_DESCRIPTION')"
                                label-for="description"
                            >
                                <b-form-textarea
                                    id="description"
                                    v-model="params.description"
                                    :placeholder="$t('ID_SOME_TEXT')"
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
                                        ref="pmTableColumns"
                                    >
                                        <!-- checkbox for each header (prefix column name with h__-->
                                        <template slot="h__selected">
                                            <input
                                                type="checkbox"
                                                @click="selectAllAtOnce()"
                                            />
                                        </template>
                                        <input
                                            slot="selected"
                                            slot-scope="props"
                                            type="checkbox"
                                            v-model="checkedRows"
                                            :checked="props.row.selected"
                                            :value="props.row.field"
                                        />
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
                                                <i
                                                    class="fa fa-angle-right"
                                                ></i>
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
                                                    class="fa fa-angle-double-left"
                                                ></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- End Control panel -->
                                </b-col>
                            </b-row>
                            <b-form-group
                                id="iconLabel"
                                :label="$t('ID_ICON')"
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
                                    :label="$t('ID_MENU_COLOR')"
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
                                    :label="$t('ID_SCREEN_COLOR_ICON')"
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
                        </b-col>
                        <b-col cols="6">
                            <b-form-group
                                id="caseListFieldset"
                                :label="$t('ID_CASE_LIST')"
                            >
                                <v-client-table
                                    :columns="columnsCaseList"
                                    v-model="dataCaseList"
                                    :options="caseListOptions"
                                >
                                    <!-- checkbox for each header (prefix column name with h__-->
                                    <template slot="h__selected">
                                        <input
                                            type="checkbox"
                                            @click="selectAllAtOnceCaseList()"
                                        />
                                    </template>
                                    <input
                                        slot="selected"
                                        slot-scope="props"
                                        type="checkbox"
                                        v-model="checkedRowsCaseList"
                                        :checked="props.row.selected"
                                        :value="props.row.field"
                                    />
                                    <div slot="action" slot-scope="props">
                                        <b-button
                                            variant="light"
                                            @click="onRemoveRow(props.row)"
                                        >
                                            <i
                                                ref="iconClose"
                                                class="far fa-window-close"
                                            ></i>
                                        </b-button>
                                    </div>
                                </v-client-table>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <div>
                        <b-button tvariant="danger" @click="onCancel">{{
                            $t("ID_CANCEL")
                        }}</b-button>
                        <b-button variant="outline-primary">{{
                            $t("ID_PREVIEW")
                        }}</b-button>
                        <b-button type="submit" variant="primary">{{
                            $t("ID_SAVE")
                        }}</b-button>
                    </div>
                </b-form>
            </div>
        </div>
    </div>
</template>
<script>
import utils from "../../../utils/utils";
import Multiselect from "vue-multiselect";
import Api from "./Api/CaseList";
import IconPicker from "../../../components/iconPicker/IconPicker.vue";

export default {
    name: "CaseListSketh",
    components: {
        Multiselect,
        IconPicker,
        IconPicker,
    },
    props: ["params", "module"],
    data() {
        return {
            icon: "fas fa-user-cog",
            isLoading: false,
            isButtonDisabled: false,
            isSelected: false,
            isSelectedCaseList: false,
            pmTablesOptions: [],
            checkedRows: [],
            closedRows: [],
            checkedRowsCaseList: [],
            columns: ["selected", "name", "field", "type", "source"],
            //data: utils.getData(),
            data: [],
            options: {
                headings: {
                    name: this.$i18n.t("ID_NAME"),
                    field: this.$i18n.t("ID_FIELD"),
                    type: this.$i18n.t("ID_TYPE"),
                    source: this.$i18n.t("ID_SOURCE"),
                },
                sortable: [],
                filterable: true,
                columnsDisplay: {
                    type: "desktop",
                },
            },
            dataCaseList: [],
            columnsCaseList: [
                "selected",
                "name",
                "field",
                "type",
                "typeSearch",
                "enableFilter",
                "action",
            ],
            caseListOptions: {
                headings: {
                    name: this.$i18n.t("ID_NAME"),
                    field: this.$i18n.t("ID_FIELD"),
                    type: this.$i18n.t("ID_TYPE"),
                    typeOfSearching: "Type of Searching",
                    enableSearchFilter: "Enable Search Filter",
                    action: "",
                },
                filterable: false,
                perPage: 1000,
                perPageValues: [],
                sortable: [],
                texts: {
                    count: "",
                },
            },
            defaultCaseList: null,
            isValidName: null,
            isValidTable: null,
            pmTable: null
        };
    },
    computed: {
        validation() {
            return this.params.name !== "";
        },
    },
    mounted() {
        this.defaultCaseList = Api.getDefault(this.module.key);
        this.dataCaseList = this.defaultCaseList;
    },
    methods: {
        selectAllAtOnce() {
            console.log("isSelected: ", this.isSelected);
            let length = this.data.length;
            this.isSelected = !this.isSelected;
            this.checkedRows = [];
            for (let i = 0; i < length; i++) {
                this.data[i].selected = this.isSelected;
                if (this.isSelected) {
                    this.checkedRows.push(this.data[i].field);
                }
            }
        },
        selectAllAtOnceCaseList() {
            let length = this.dataCaseList.length;
            this.isSelectedCaseList = !this.isSelectedCaseList;
            this.checkedRowsCaseList = [];
            for (let i = 0; i < length; i++) {
                this.dataCaseList[i].selected = this.isSelectedCaseList;
                if (this.isSelectedCaseList) {
                    this.checkedRowsCaseList.push(this.dataCaseList[i].field);
                }
            }
        },
        unassignSelected() {
            let temp;
            let length = this.checkedRowsCaseList.length;
            for (let i = 0; i < length; i++) {
                temp = this.dataCaseList.find(
                    (x) => x.field === this.checkedRowsCaseList[i]
                );
                temp["set"] = false;
                this.data.push(temp);
                this.dataCaseList = this.dataCaseList.filter((item) => {
                    return item.field != this.checkedRowsCaseList[i];
                });
            }
            this.checkedRowsCaseList = [];
        },
        unassignAll() {
            this.data = [...this.data, ...this.dataCaseList];
            this.dataCaseList = [];
        },
        assignSelected() {
            let temp;
            let length = this.checkedRows.length;
            for (let i = 0; i < length; i++) {
                temp = this.data.find((x) => x.field === this.checkedRows[i]);
                temp["set"] = true;
                this.dataCaseList.push(temp);
                this.data = this.data.filter((item) => {
                    return item.field != this.checkedRows[i];
                });
            }
            this.checkedRows = [];
        },
        assignAll() {
            this.dataCaseList = [...this.dataCaseList, ...this.data];
            this.data = [];
        },
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
            self.pmTablesOptions = [];
            Api.reportTables({
                search: query,
            })
            .then((response) => {
                self.processes = [];
                _.forEach(response.data, function(elem, key) {
                    self.pmTablesOptions.push({
                        label: elem.name,
                        value: elem.uid,
                        fields: elem.fields,
                    });
                });

                this.isLoading = false;
            })
            .catch((e) => {
                console.error(err);
            });
        },
        onSelect(option) {
            this.checkedRows = [];
            this.data = option.fields;
            this.dataCaseList = this.defaultCaseList;
        },
        onRemoveRow(row) {
            var temp = this.dataCaseList.find((x) => x.field === row.field);
            if (temp) {
                temp["set"] = false;
                this.data.push(temp);
                this.dataCaseList = this.dataCaseList.filter((item) => {
                    return item.field != row.field;
                });
            }
        },
        onSubmit() {
            this.isValidName = true;
            this.isValidTable = true;
            if (!this.params.name) {
                this.isValidName = false;
                return;
            }
            if (!this.pmTable) {
                this.isValidTable = false;
                return;
            }
            this.params.tableUid = this.pmTable.value;
            this.params.columns = this.dataCaseList;
            this.params.type = this.module.key;
            this.params.userId = window.config.userId;
            Api.createCaseList(this.params)
            .then((response) => {
                this.$emit("closeSketch");
              
            })
            .catch((e) => {
                console.error(err);
            });

        },
        onReset() {},   
    },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>
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
.invalid .multiselect__tags {
    border-color: #f04124;
}

.invalid .typo__label {
    color: #f04124;
}
</style>
