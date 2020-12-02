<template>
    <div v-if="data.length" class="grouped-cell">
        <div v-for="item in data" class="d-flex mb-3">
            <div
                v-bind:style="{ color: activeColor(item.STATUS) }"
                v-b-popover.hover.top="item.DELAYED_MSG"
            >
                <i class="fas fa-square"></i>
            </div>
            <div class="col ellipsis" v-b-popover.hover.top="item.TAS_NAME">
                {{ item.TAS_NAME }}
            </div>
            <div class="avatar">
                <b-avatar
                    variant="info"
                    :src="item.AVATAR"
                    size="1.2em"
                ></b-avatar>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "GroupedCell",
    props: ["data"],
    data() {
        return {
            //Color map for ["In Progress", "overdue", "inDraft", "paused", "unnasigned"]
            colorMap: ["green", "red", "orange", "aqua", "silver"],
        };
    },
    methods: {
        /**
         * Get the style color to be applied in the square icon
         * @param {number} - status color(1-5)
         * @return {string} - color atribute string
         */
        activeColor: function(codeColor) {
            return this.colorMap[codeColor-1];
        },
    }
};
</script>

<style>
.grouped-cell {
    font-size: smaller;
}

.ellipsis {
    white-space: nowrap;
    width: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.color {
  
    color: red;
}
.avatar {
    color: "red";
    width: "1.3em";
}
</style>
