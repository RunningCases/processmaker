<template>
    <div id="">
       <SearchPopover
            :target="tag"
            @closePopover="onClose"
            @savePopover="onOk"
        >
            <template v-slot:target-item>
                <div
                    @click="onClickTag(tag)"
                    :id="tag"
                >
                    <b-icon
                        icon="tags-fill"
                        font-scale="1"
                    ></b-icon>
                    {{
                        tagText
                    }}
                </div>
            </template>
            <template v-slot:body>
                <h6>{{ info.title }}</h6>
                <input v-model="title" type="text" size="150" class="form-control" :placeholder="info.placeHolder">
            </template>
        </SearchPopover>
       
    </div>
</template>
|
<script>
import SearchPopover from "./SearchPopover.vue";

    export default {
        components: {
            SearchPopover,
        },
        props: ['tag','info'],
        data() {
            return {
                title: ''
            };
        },
        computed: {
            tagText:  function ()  {
                return `Set By: `;
            } 
        },
        methods: {
             onClose() {
            },
            onOk() {
                this.$emit("updateSearchTag", {
                    columnSearch: "APP_TITLE",
                    search: this.title,
                });
            },
            onRemoveTag() {

            },
            onClickTag(tag) {
                this.$root.$emit("bv::hide::popover");
            }
          
        },
    };
</script>
<style scoped>

</style>