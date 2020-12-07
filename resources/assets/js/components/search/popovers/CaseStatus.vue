<template>
    <div id="">
        <SearchPopover
            :target="tag"
            @closePopover="onClose"
            @savePopover="onOk"
        >
            <template v-slot:target-item>
                <div @click="onClickTag(tag)" :id="tag">
                    <b-icon icon="tags-fill" font-scale="1"></b-icon>
                    {{ tagText }}
                </div>
            </template>
            <template v-slot:body>
                <h6>{{ info.title }}</h6>
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
            return `Case Status: `;
        },
    },
    methods: {
        onClose() {
        },
        onOk() {

        },
        onRemoveTag() {},
        onClickTag(tag) {
            this.$root.$emit("bv::hide::popover");
        },
    },
};
</script>
<style scoped></style>
