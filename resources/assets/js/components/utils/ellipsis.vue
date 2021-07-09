<template>
    <div class="float-right">
         <transition name="fade">
            <div
                class="v-inline"
                v-show="showActions"
            >
                <div class="buttonGroup" @focus="hidebuttonsAction()">
                    <b-button
                        v-if="data.showClaim"
                        variant="outline-info"
                        @click="claimCase"
                    >
                        <i class="fas fa-briefcase"></i>
                    </b-button>
                    <b-button
                        v-if="data.showOpen"
                        variant="outline-info"
                        @click="openCase"
                    >
                        <i class="far fa-edit"></i>
                    </b-button>
                    <b-button
                        v-show="data.showPlay"
                        variant="outline-info"
                        @click="unPauseCase"
                    >
                        <i class="far fa-play-circle"></i>
                    </b-button>
                    <b-button
                        v-show="data.showPause"
                        variant="outline-info"
                        @click="pauseCase"
                    >
                        <i class="far fa-pause-circle"></i>
                    </b-button>
                    <b-button
                        v-show="data.showReassign"
                        variant="outline-info"
                        @click="reassingCase"
                    >
                        <i class="fas fa-redo"></i>
                    </b-button>
                    <b-button
                        v-show="data.showNote"
                        variant="outline-info"
                        @click="openCaseNote"
                    >
                        <i class="far fa-comments"></i>
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
        /**
         * Call the event to reassign a case
         */
        reassingCase() {
            this.hideActionButtons();
            console.log("Action Reassing Case");
            console.log(this.data);
        },
        /**
         * Call the event to open a case
         */
        openCase() {
            this.hideActionButtons();
            console.log("Action Open Case");
            console.log(this.data);
        },
        /**
         * Call the event to unpause a case
         */
        unPauseCase() {
            this.hideActionButtons();
            console.log("Action Unpause Case");
            console.log(this.data);
        },
        /**
         * Call the event to pause a case
         */
        pauseCase() {
            this.hideActionButtons();
            console.log("Action Pause Case");
            console.log(this.data);
        },
        /**
         * Call the event to open case note
         */
        openCaseNote() {
            this.hideActionButtons();
            console.log("Action Case Note");
            console.log(this.data);
        },
        /**
         * Call the event to claim a case
         */
        claimCase() {
            this.hideActionButtons();
            console.log("Action Claim Case");
            console.log(this.data);
        }
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
        z-index: 999999;
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