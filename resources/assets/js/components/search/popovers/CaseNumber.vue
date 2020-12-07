<template>
    <div>
        <SearchPopover
            :target="tag"
            @closePopover="onClose"
            @savePopover="onOk"
        >
            <template v-slot:target-item>
                <div @click="onClickTag(tag)" :id="tag">
                    <i class="fas fa-tags"></i>
                    {{ tagText }}
                </div>
            </template>
            <template v-slot:body>
                <div class="row">
                    <div class="col">
                        <input
                            v-model="from"
                            type="text"
                            size="210"
                            class="form-control"
                            placeholder="From Case number #"
                        />
                    </div>
                    <div class="col">
                        <input
                            v-model="to"
                            type="text"
                            size="210"
                            class="form-control"
                            placeholder="To Case number # (Optional)"
                        />
                    </div>
                </div>
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
            return `From: ${this.from} To: ${this.to}`;
        },
    },
    methods: {
        onClose() {},
        onOk() {
            this.$emit("updateSearchTag", { from: this.from, to: this.to });
        },
        onRemoveTag() {},
        onClickTag(tag) {
            this.$root.$emit("bv::hide::popover");
        },
        handler() {
        },
    },
};
</script>
<style scoped>
.popovercustom {
    max-width: 650px !important;
}
</style>
