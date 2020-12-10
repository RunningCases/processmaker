<template>
    <div>
        <SearchPopover
            :target="tag"
            :showPopover="showPopover"
            @closePopover="onClose"
            @savePopover="onOk"
            :title="info.title"
        >
            <template v-slot:target-item>
                <div @click="onClickTag(tag)" :id="tag">
                    <i class="fas fa-tags"></i>
                    {{ tagText }}
                </div>
            </template>
            <template v-slot:body>
                <p>{{ info.detail }}</p>
                <form ref="form" @submit.stop.prevent="handleSubmit">
                    <b-form-group
                        :state="valueState"
                        label-for="name-input"
                        :invalid-feedback="$t('ID_INVALID_CASE_NUMBER_RANGE')"
                    >
                        <b-form-input
                            id="name-input"
                            v-model="value"
                            :placeholder="$t('ID_CASE_NUMBER_FILTER_EG')"
                            :state="valueState"
                            required
                        ></b-form-input>
                    </b-form-group>
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
            value: "",
            valueState: null,
            showPopover: false,
        };
    },
    computed: {
        tagText: function() {
            return `${this.$i18n.t("ID_IUD")}: ${this.value} `;
        },
    },
    methods: {
        onClose() {
            this.showPopover = true;
        },
        checkFormValidity() {
            const regex = /^((\d+?)|(\d+?)(?:\-(\d+?))?)(?:\, ((\d+?)|(\d+?)(?:\-(\d+?))?))*$/;
            regex.test(this.value);
            this.valueState = regex.test(this.value);
            return this.valueState;
        },
        handleSubmit() {
            let self = this;
            // Exit when the form isn't valid
            if (!this.checkFormValidity()) {
                return;
            }
            // Hide the modal manually
            this.$nextTick(() => {
                this.$emit("updateSearchTag", {
                    filterCases: self.value.replace(/ /g, ""),
                });
                self.$root.$emit("bv::hide::popover");
            });
        },
        onOk() {
            this.handleSubmit();
        },
        onClickTag(tag) {
            this.$root.$emit("bv::hide::popover");
        }
    },
};
</script>
<style scoped>
.popovercustom {
    max-width: 650px !important;
}
</style>
