<template>
    <span
        :id="`label-${data.id}`"
        @mouseover="hoverHandler"
        :title="labelTooltip"
    >
        {{ data.title }}
        <b-tooltip :target="`label-${data.id}`" :ref="`tooltip-${data.id}`">
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
            let key = `tooltip-${this.data.id}`;
            this.labelTooltip = "";
            this.$refs[key].$emit("close");
        },
        /**
         * Set the label to show in the tooltip
         */
        setTooltip() {
            let that = this;
            debugger;
            api.menu.getTooltip(that.data.id).then((response) => {
                let key = `tooltip-${that.data.id}`;
                that.labelTooltip = response.data.label;
                that.$refs[key].$emit("open");
            });
        },
    },
};
</script>
