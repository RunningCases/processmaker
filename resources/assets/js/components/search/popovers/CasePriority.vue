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
                <b-form-group :label="info.label">
                    <b-form-checkbox
                        v-for="option in info.options"
                        v-model="selected"
                        :key="option.value"
                        :value="option.value"
                        name="flavour-2a"
                        stacked
                    >
                        {{ option.text }}
                    </b-form-checkbox>
                </b-form-group>
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
            selected: [], // Must be an array reference!
        };
    },
    computed: {
        tagText: function() {
            return `${this.$i18n.t('ID_PRIORITY')}: ${this.selected.join(",")}`;
        },
    },
    methods: {
        /**
         * Ok button handler
         */
        onOk() {
            this.handleSubmit();
        },
        /**
         * Submit button handler
         */
        handleSubmit() {
            this.$nextTick(() => {
                this.$emit("updateSearchTag", {
                    priorities: this.selected.join(","),
                });
                this.$root.$emit("bv::hide::popover");
            });
        },
        /**
         * Tag Click handler
         */
        onClickTag(tag) {
            this.$root.$emit("bv::hide::popover");
        }
    }
};
</script>
<style scoped></style>
