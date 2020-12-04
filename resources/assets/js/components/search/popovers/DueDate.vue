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
                <div class="row">
                    <div class="col">
                          <b-form-datepicker id="from" v-model="from"  placeholder="From Due Date"></b-form-datepicker>
                    </div>
                    <div class="col">
                         <b-form-datepicker id="to" v-model="to"   placeholder="To Due Date"></b-form-datepicker>
                    </div>  
                </div>
                   
            </template>
        </SearchPopover>
       
    </div>
</template>

<script>
import SearchPopover from "./SearchPopover.vue";

    export default {
        components: {
            SearchPopover,
        },
        props: ['tag','info'],
        data() {
            return {
                from: "",
                to: ""
            };
        },
        computed: {
            tagText: function() {
                return `From: ${this.from} To:  ${this.to}`;
            },
        },
        methods: {
            onClose() {
            // this.popoverShow = false;
                // this.$emit("closePopover");
            },
            onOk() {
                this.$emit("updateSearchTag", {
                    columnSearch: "APP_TITLE",
                    search: null,
                    dateFrom: this.from,
                    dateTo: this.to
                });
            },
            onClickTag(tag) {
                this.$root.$emit("bv::hide::popover");
            }
          
        },
    };
</script>
<style scoped>
</style>