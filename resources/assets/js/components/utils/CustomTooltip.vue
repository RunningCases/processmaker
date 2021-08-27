<template>
    <span
        :id="`label-${data.id}`"
        @mouseover="hoverHandler"
        :title="labelTooltip"
    >
        {{ data.title }}
        <b-tooltip :target="`label-${data.page}`" :ref="`tooltip-${data.page}`">
            {{ labelTooltip }}
        </b-tooltip>
    </span>
</template>

<script>
import api from "./../../api/index";

export default {
    name: "CustomTooltip",
    props: {
        data: Object,
    },
    data() {
        return {
            labelTooltip: "",
            menuMap: {
                CASES_INBOX: "inbox",
                CASES_DRAFT: "draft",
                CASES_PAUSED: "paused",
                CASES_SELFSERVICE: "unassigned",
                todo: "inbox",
                draft: "draft",
                paused: "paused",
                unassigned: "unassigned",
            },
        };
    },
    methods: {
        /**
         * Delay the hover event
         */
        hoverHandler() {
            this.setTooltip();
        },
        /**
         * Reset the delay and hide the tooltip
         */
        unhoverHandler() {
            let key = `tooltip-${this.data.page}`;
            this.labelTooltip = "";
            this.$refs[key].$emit("close");
        },
        /**
         * Set the label to show in the tooltip
         */
        setTooltip() {
            let that = this;
            api.menu.getTooltip(that.data.page).then((response) => {
                let key = `tooltip-${that.data.page}`;
                that.labelTooltip = response.data.label;
                that.$refs[key].$emit("open");
            });
        },
    },
};
</script>
