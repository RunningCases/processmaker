<template>
    <div>
        <SearchPopover :target="tag" @savePopover="onOk" :title="info.title">
            <template v-slot:body>
                <p>{{ info.detail }}</p>
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <b-form-group
                        label-for="name"
                        :invalid-feedback="$t('ID_PROCESS_IS_REQUIRED')"
                    >
                        <multiselect
                            v-model="info.selectedOption"
                            :options="users"
                            placeholder="Sselect one"
                            label="USR_FULLNAME"
                            track-by="USR_ID"
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
import Multiselect from "vue-multiselect";
import api from "./../../../api/index";

export default {
    components: {
        SearchPopover,
        Multiselect
    },
    props: ["tag", "info"],
    data() {
        return {
            users: [],
            isLoading: false
        };
    },
    methods: {
        /**
         * Find asynchronously in the server
         * @param {string} query - string from the text field
         */

        asyncFind(query) {
            this.isLoading = true;
            api.filters
                .userList(query)
                .then((response) => {
                    this.users = response.data;
                    this.isLoading = false;
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
                CurrentUser: {
                    selectedOption: this.info.selectedOption,
                    userId: this.info.selectedOption.USR_ID
                }
            });
            this.$root.$emit("bv::hide::popover");
        }
    }
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style scoped></style>
