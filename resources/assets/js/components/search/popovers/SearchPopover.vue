<template>
    <div>
        <slot name="target-item">here component</slot>

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
                &nbsp;
            </template>
            <div>
                <slot name="body">
                    popover body
                </slot>
                <div class="float-right">
                    <b-button @click="onClose" size="sm" variant="danger">Cancel</b-button>
                    <b-button @click="onSave" size="sm" variant="primary">Save</b-button>
                </div>
            </div>
        </b-popover>
    </div>
</template>
<script>
export default {
    props: ['target'],
    data() {
        return {
           popoverShow: false
        };
    },
    methods: {
        onClose() {
            this.popoverShow = false;
            this.$emit('closePopover');
        },
        onSave() {
            this.$emit('savePopover');
            this.onClose();
        },
    },
};
</script>
</script>
<style scoped>

.popover {
    max-width: 650px !important; 
    min-width: 400px !important;
}
</style>