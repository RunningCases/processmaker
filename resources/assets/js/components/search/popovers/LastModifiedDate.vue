<template>
    <div id="">
        <SearchPopover :target="tag" @savePopover="onOk" :title="info.title">
            <template v-slot:target-item>
                <div @click="onClickTag(tag)" :id="tag">
                    <b-icon icon="tags-fill" font-scale="1"></b-icon>
                    {{ tagText }}
                </div>
            </template>
            <template v-slot:body>
                <p>{{ info.detail }}</p>
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <div class="row">
                        <div class="col">
                            <b-form-group>
                                <b-form-datepicker
                                    id="from"
                                    v-model="from"
                                    :placeholder="$t('ID_FROM_LAST_MODIFIED_DATE')"
                                ></b-form-datepicker>
                            </b-form-group>
                        </div>
                        <div class="col">
                            <b-form-group>
                                <b-form-datepicker
                                    id="to"
                                    v-model="to"
                                    :placeholder="$t('ID_TO_LAST_MODIFIED_DATE')"
                                ></b-form-datepicker>
                            </b-form-group>
                        </div>
                    </div>
                </form>
            </template>
        </SearchPopover>
    </div>
</template>

<script>
import SearchPopover from "./SearchPopover.vue";

export default {
    components: {
        SearchPopover,
    },
    props: ["tag", "info"],
    data() {
        return {
            from: "",
            to: "",
        };
    },
    computed: {
        tagText: function() {
            return `${this.$i18n.t('ID_FROM')}: ${this.from} ${this.$i18n.t('ID_TO')}:  ${this.to}`;
        },
    },
    methods: {
        /**
         * Submit form handler
         */
        handleSubmit() {
            this.$emit("updateSearchTag", {
                delegationDateFrom: this.from,
                delegationDateTo: this.to
            });
        },
        /**
         * On ok event handler
         */
        onOk() {
            this.handleSubmit();
        },
        /**
         * On click tag event handler
         */
        onClickTag(tag) {
            this.$root.$emit("bv::hide::popover");
        }
    }
};
</script>
<style scoped></style>
