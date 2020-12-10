<template>
    <div id="">
        <SearchPopover :target="tag" @savePopover="onOk" :title="info.title">
            <template v-slot:body>
                <p>{{ info.detail }}</p>
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <b-form-group
                        label-for="name"
                        :invalid-feedback="$t('ID_PROCESS_IS_REQUIRED')"
                    >
                        <multiselect 
                            v-model="info.processOption" 
                            :options="processes" 
                            placeholder="Select one" 
                            label="PRO_TITLE" 
                            track-by="PRO_ID"
                            :show-no-results="false"
                            @search-change="asyncFind"
                            :loading="isLoading"
                            id="ajax"
                            :limit="10"
                            :clear-on-select="true" 
                        >
                        </multiselect>
                    </b-form-group>
                </form>
            </template>
        </SearchPopover>
    </div>
</template>

<script>
import SearchPopover from "./SearchPopover.vue";
import Multiselect from 'vue-multiselect'
import api from "./../../../api/index";

export default {
    components: {
        SearchPopover,
        Multiselect,
    },
    props: ["tag", "info"],
    data() {
        return {
            processes: [],
            isLoading: false
        };
    },
    methods: {
        /**
         * Find asynchronously in the server
         * @param {string} query - string from the text field
         */ 
        asyncFind (query) {
            this.isLoading = true
               api.filters
                .processList(query)
                .then((response) => {
                    this.processes = response.data;
                    this.countries = response
                    this.isLoading = false
                })
                .catch((e) => {
                    console.error(err);
                });
        },
        /**
         * Form validations review
         */
        checkFormValidity() {
            const valid = this.query !== "";
            this.valueState = valid;
            return valid;
        },
        /**
         * On Ok event handler
         */
        onOk() {
            this.handleSubmit();
        },
        /**
         *  Form submit handler
         */
        handleSubmit() {
                this.$emit("updateSearchTag", {
                    ProcessName: {
                        processOption: this.info.processOption,
                        process: this.info.processOption.PRO_ID
                    }
                });
                this.$root.$emit("bv::hide::popover");
        }
    }
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped></style>
