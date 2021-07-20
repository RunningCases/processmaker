<template>
    <div class="float-right">
         <transition name="fade">
            <div
                class="v-inline"
                v-show="showActions"
                ref="ellipsis"
            >
                <div class="buttonGroup">
                    <b-button
                        v-for="item in data.buttons"
                        :key="item.name"
                        variant="outline-info"
                        @click="executeFunction(item.fn)"
                    >
                        <i :class="item.icon"></i>
                    </b-button>
                </div>
            </div>
        </transition>
        <div class="ellipsis-button">
            <div @click="showActionButtons()">
                <i class="fas fa-ellipsis-v"></i>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Ellipsis",
    props: {
        data: Object
    },
    data () {
        return {
            showActions: false
        }
    },
    methods: {
        /**
         * Callback function from parent
         */
        executeFunction(fn) {
            if (fn) {
                fn();
            }
        },
        /**
         * Show the action buttons by row
         */
        showActionButtons() {
            var i;
            this.showActions = !this.showActions;
            if (this.showActions) {
                for (i = 0; i < this.$parent.$parent.$parent.$children.length -1 ; i++){
                    this.$parent.$parent.$parent.$children[i].$el.style.opacity = 0.15
                }
            } else {
                this.hideActionButtons();     
            }
        },
        /**
         * Hide action buttons
         */
        hideActionButtons() {
            var i;
            this.showActions = false;
            for (i = 0; i < this.$parent.$parent.$parent.$children.length -1 ; i++){
                    this.$parent.$parent.$parent.$children[i].$el.style.opacity = 1
                }
        },
    }
}
</script>

<style>
    .v-btn-request {
        display: inline-block;
    }
    .v-inline {
        display: inline-block;
    }
    .ellipsis-button {
        font-size: 22px;
        width: 15px;
        text-align: center;
        float: inherit;
        margin-top: 9px;
    }
    .buttonGroup {
        position: relative;
        flex-direction: row-reverse;
        width: 0px;
        z-index: 999;
        display: inline-flex !important;
    }
    .btn-outline-info {
        border: none;
        font-size: 25px;
    }
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s;
        position: relative;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
        position: relative;
    }
</style>