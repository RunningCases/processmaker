<template>
    <div id="">
        <SearchPopover
            :target="tag"
            @closePopover="onClose"
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
                        label-for="name-input"
                        :invalid-feedback="$t('ID_REQUIRED_FIELD')"
                    >
                        <b-form-input
                            id="name-input"
                            v-model="title"
                            :placeholder="$t('ID_CASE_TITLE_NAME')"
                            :state="valueState"
                            required
                        ></b-form-input>
                    </b-form-group>
                </form>
            </template>
        </SearchPopover>
    </div>
</template>
|
<script>
import SearchPopover from "./SearchPopover.vue";

export default {
    components: {
        SearchPopover,
    },
    props: ["tag", "info"],
    data() {
        return {
            title: "",
            valueState: null,
        };
    },
    computed: {
        tagText: function() {
            return `${this.$i18n.t("ID_CASE_TITLE")}: ${this.title}`;
        },
    },
    methods: {
        /**
         * Check the form validations and requiered fields
         */
        checkFormValidity() {
            const valid = this.$refs.form.checkValidity();
            this.valueState = valid;
            return valid;
        },
        /**
         * Submit form handler
         */
        handleSubmit() {
            let self = this;
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return;
            }
            this.$nextTick(() => {
                this.$emit("updateSearchTag", {
                    caseTitle: self.title,
                });
                self.$root.$emit("bv::hide::popover");
            });
        },
        /**
         * On ok event handler
         */
        onOk() {
            this.handleSubmit();
        },
        /**
         * On ok event handler
         */
        onClickTag(tag) {
            this.$root.$emit("bv::hide::popover");
        },
    },
};
</script>
<style scoped></style>
