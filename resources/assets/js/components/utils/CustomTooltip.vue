<template>
    <span
        :id="`label-${data.id}`"
        @mouseover="hoverHandler"
        v-b-tooltip.hover
        @mouseleave="unhoverHandler"
        v-bind:class="{highlightText: isHighlight}"
    >
        {{ data.title }}
        <b-tooltip
            :target="`label-${data.id}`"
            triggers="hoverHandler"
            :ref="`tooltip-${data.id}`"
        >
            {{ labelTooltip }}
        </b-tooltip>
    </span>
</template>

<script>
import api from "./../../api/index";

export default {
    name: "CustomTooltip",
    props: {
        data: Object
    },
    data() {
        return {
            labelTooltip: "",
            hovering: "",
            show: false,
            menuMap: {
                CASES_INBOX: "inbox",
                CASES_DRAFT: "draft",
                CASES_PAUSED: "paused",
                CASES_SELFSERVICE: "unassigned"
            },
            isHighlight: false
        };
    },
    methods: {
        /**
         * Delay the hover event
         */
        hoverHandler() {
            this.hovering = setTimeout(() => { this.setTooltip() }, 3000);
        },
        /**
         * Reset the delay and hide the tooltip
         */
        unhoverHandler() {
            let key = `tooltip-${this.data.id}`;
            this.labelTooltip = "";
            this.$refs[key].$emit("close");
            clearTimeout(this.hovering);
        },
        /**
         * Set the label to show in the tooltip
         */
        setTooltip() {
            let that = this;
            api.menu.getTooltip(that.data.id).then((response) => {
                let key = `tooltip-${that.data.id}`;
                that.labelTooltip = response.data.label;
                that.$refs[key].$emit("open");
                that.isHighlight = false;
            });
        },
        /**
         * Set bold the label 
         */
        setHighlight() {
            this.isHighlight = true;
        }
    },
};
</script>
<style>
.highlightText {
    font-weight: 900;
}
</style>
