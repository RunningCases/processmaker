<template>
    <div>
        <slot name="target-item"></slot>

        <b-popover
            :show.sync="popoverShow"
            :target="target"
            triggers="click"
            placement="bottom"
            class="popovercustom"
        >
            <template #title>
                <b-button @click="onClose" class="close" aria-label="Close">
                    <span class="d-inline-block" aria-hidden="true"
                        >&times;</span
                    >
                </b-button>
                {{ title }}
            </template>
            <div>
                <slot name="body"></slot>
                <div class="float-right">
                    <b-button @click="onClose" size="sm" variant="danger"> {{$t('ID_CANCEL')}}</b-button>
                    <b-button @click="onSave" size="sm" variant="primary">{{$t('ID_SAVE')}}</b-button>
                </div>
            </div>
        </b-popover>
    </div>
</template>
<script>
export default {
    props: ['target', "title"],
    data() {
        return {
           popoverShow: false
        };
    },
    methods: {
        /**
         * Close buton click handler 
         */
        onClose() {
            this.popoverShow = false;
            this.$emit('closePopover');
        },
        /**
         * Save button click handler 
         */
        onSave() {
            this.$emit('savePopover');
        }
    }
};
</script>

<style scoped>

.popover {
    max-width: 650px !important; 
    min-width: 400px !important;
}
</style>