<template>
    <i v-if="props.sortable" :class="icon"></i>
</template>
<script>
export default {
    name: "VtSortControl",
    props: ["props"],
    computed: {
        icon() {
            // if not sorted return base icon
            if (!this.props.sortStatus.sorted) return "fas fa-sort";
            // return sort direction icon
            return this.props.sortStatus.asc
                ? "fas fa-sort-amount-up"
                : "fas fa-sort-amount-down";
        },
    },
};
</script>
