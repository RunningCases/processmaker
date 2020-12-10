<template>
    <div id="">
        <SearchPopover
            :target="tag"
            @savePopover="onOk"
            :title="info.title"
        >
            <template v-slot:target-item>
                <div @click="onClickTag(tag)" :id="tag">
                    <b-icon icon="tags-fill" font-scale="1"></b-icon>
                    {{ tagText }}
                </div>
            </template>
            <template v-slot:body>
                <p>{{ info.detail }}</p>
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <b-form-group
                        :state="valueState"
                        label-for="name"
                        :invalid-feedback="$t('ID_PROCESS_IS_REQUIRED')"
                    >
                        <vue-bootstrap-typeahead
                            class="mb-4"
                            id="name"
                            v-model="query"
                            :minMatchingChars="minMatchingChars"
                            :data="users"
                            :serializer="(item) => item.USR_FULLNAME"
                   
                            :placeholder="info.placeholder"
                            required
                            :state="valueState"
                        />
                    </b-form-group>
                </form>
            </template>
        </SearchPopover>
    </div>
</template>

<script>
import SearchPopover from "./SearchPopover.vue";
import VueBootstrapTypeahead from "vue-bootstrap-typeahead";
import api from "./../../../api/index";

export default {
    components: {
        SearchPopover,
        VueBootstrapTypeahead
    },
    props: ["tag", "info"],
    data() {
        return {
            minMatchingChars: 1,
            query: "",
            users: [],
            valueState: null
        };
    },
    computed: {
        tagText: function() {
            return `${this.$i18n.t('ID_USER')}: ${this.query}`;
        }
    },
    watch: {
        query(newQuery) {
            api.filters
                .userValues(this.query)
                .then((response) => {
                    this.users = response.data;
                })
                .catch((e) => {
                    console.error(err);
                });
        }
    },
    methods: {
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
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return;
            }
            this.$nextTick(() => {
                let user = _.find(this.users, { USR_FULLNAME: this.query });
                this.$emit("updateSearchTag", {
                    userId: user.USR_UID
                });
                this.$root.$emit("bv::hide::popover");
            });
        },
        /**
         * Cick tag event handler
         */
        onClickTag() {
            this.$root.$emit("bv::hide::popover");
        }
    }
};
</script>
<style scoped></style>
