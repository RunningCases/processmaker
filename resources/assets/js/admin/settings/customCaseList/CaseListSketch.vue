<template>
    <div
        id="home"
    >
        <div class="demo">
            <div class="container">
                <h5 >{{ $t("ID_NEW_CASES_LISTS") }}</h5>

                 <div class="row">
                    <div class="col-sm">

                    <b-row>
                        <b-col>
                            <b-form-group
                                id="nameLabel"
                                label="Name "
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


                        <v-client-table :columns="columns" v-model="data" :options="options">
                            <a slot="uri" slot-scope="props" target="_blank" :href="props.row.uri" class="glyphicon glyphicon-eye-open"></a>

                            <div slot="child_row" slot-scope="props">
                            The link to {{props.row.name}} is <a :href="props.row.uri">{{props.row.uri}}</a>
                            </div>

                            <div slot="name" slot-scope="{row, update, setEditing, isEditing, revertValue}">
                            <span @click="setEditing(true)" v-if="!isEditing()">
                                <a>{{row.name}}</a>
                            </span>
                            <span v-else>
                                <input type="text" v-model="row.name">
                                <button type="button" class="btn btn-info btn-xs" @click="update(row.name); setEditing(false)">Submit</button>
                            <button type="button" class="btn btn-default btn-xs" @click="revertValue(); setEditing(false)">Cancel</button>
                            
                            </span>

                            </div>
                        </v-client-table>
                        <b-form-group
                            id="iconLabel"
                            label="Icon "
                            label-for="icon"
                        >
                            <icon-picker @selected="onSelectIcon" :default="params.iconList"/>
                        </b-form-group>
                        <div>
                             <b-form-group
                                id="menuColor"
                                label="Menu Color "
                                label-for="icon"    
                            >  
                                <verte :value="params.iconColor" id="icon" @input="onChangeColor" picker="square" menuPosition="left" model="hex">
                                        <svg viewBox="0 0 50 50">
                                            <path d="M 10 10 H 90 V 90 H 10 L 10 10"/>
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
                                <verte id="screen" picker="square" menuPosition="left" model="rgb">
                                        <svg viewBox="0 0 50 50">
                                            <path d="M 10 10 H 90 V 90 H 10 L 10 10"/>
                                        </svg> 
                                </verte>
                            </b-form-group>
                        </div>
                        
                    </div>
                    <div class="col-sm">
                    One of two columns
                  
                    </div>
                    
                </div>
                <div>
                    <b-button variant="danger" @click="onCancel">Cancel</b-button>
                    <b-button variant="outline-primary">Preview</b-button>
                    <b-button variant="success">Save</b-button>
                </div>
              
            </div>
        </div>
    </div>
</template>
<script>
import utils from "../../../utils/utils"
import Multiselect from "vue-multiselect";
import api from "./../../../api/index";
import IconPicker from '../../../components/iconPicker/IconPicker.vue';

export default {
    name: "CaseListSketh",
    components: {
        Multiselect,
        IconPicker,
        IconPicker
    },
    props:["params"],
    data() {
        return {
             icon: "fas fa-user-cog",
            isLoading: false,
            pmTablesOptions: [],
            columns: ['name', 'code', 'uri'],
            data: utils.getData(),
            options: {
                headings: {
                    name: 'Country Name',
                    code: 'Country Code',
                    uri: 'View Record'
                },
                filterable:true,
                editableColumns:['name'],
                sortable: ['name', 'code'],
                filterable: ['name', 'code']
            }
        };
    },
    mounted() {
       
    },
    methods: {
        onSelectIcon(data){
            console.log (data);
            // this.params.iconList = data;
        },
        onChangeColor(color){
            console.log(color);
            this.menuColor = color;
        },
        onCancel(){
            this.$emit("closeSketch");
        },
        onChange (e) {
            console.log(e)
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
    }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped>
.verte {
    position: relative;
    display: flex;
    justify-content: normal;
}
</style>

