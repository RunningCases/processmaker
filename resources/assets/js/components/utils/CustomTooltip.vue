<template>
    <span
        :id="data.id"
        @mouseover="hoverHandler"
        v-b-tooltip.hover
        :title="labelTooltip"
        @mouseleave="unhoverHandler"
    >
        {{ data.title }}
    <b-tooltip
        :target="data.id"
        triggers="hoverHandler"
        :show.sync="show"
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
        data: Object,
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
            }
        }
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
            this.labelTooltip = "";
            this.show = false;
            clearTimeout(this.hovering);
        },
        /**
         * Set the label to show in the tooltip
         */
        setTooltip() {
            let that = this;
            api.menu
                .getTooltip(that.menuMap[that.data.id])
                .then((response) => {
                    that.labelTooltip = response.data.label;
                    that.show = true;
                });
        },
    }
}
</script>
