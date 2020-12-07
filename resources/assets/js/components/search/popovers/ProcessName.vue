<template>
    <div id="">
        <SearchPopover
            :target="tag"
            @closePopover="onClose"
            @savePopover="onOk"
        >
            <template v-slot:target-item>
                <div @click="onClickTag(tag)" :id="tag">
                    <b-icon icon="tags-fill" font-scale="1"></b-icon>
                    {{ tagText }}
                </div>
            </template>
            <template v-slot:body>
                <h6>Filter: Process</h6>
                <vue-bootstrap-typeahead
                    class="mb-4"
                    v-model="query"
                    :data="process"
                    :serializer="(item) => item.PRO_TITLE"
                    @hit="selectedUser = $event"
                    placeholder="Search GitHub Users"
                />
            </template>
        </SearchPopover>
    </div>
</template>

<script>
import SearchPopover from "./SearchPopover.vue";
import VueBootstrapTypeahead from "vue-bootstrap-typeahead";

// OR
export default {
    components: {
        SearchPopover,
        VueBootstrapTypeahead,
    },
    props: ["tag", "info"],
    data() {
        return {
            query: "",
            process: [],
        };
    },
    computed: {
        ProcessMaker() {
            return window.ProcessMaker;
        },
        tagText: function() {
            return `Process: ${this.query}`;
        },
    },
    watch: {
        query(newQuery) {
            ProcessMaker.apiClient
                .post(
                    `http://localhost:350/sysworkflow/en/neoclassic/cases/casesList_Ajax?actionAjax=processListExtJs&action=search`,
                    {
                        query: this.query,
                    }
                )
                .then((response) => {
                    this.process = response.data;
                })
                .catch((err) => {
                    console.error(err);
                });
        },
    },
    methods: {
        onClose() {},
        onOk() {
            this.$emit("updateSearchTag", {
                columnSearch: "APP_TITLE",
                process_label: this.query,
                process: _.find(this.process, { PRO_TITLE: this.query }).PRO_ID,
            });
        },
        onClickTag(tag) {
            this.$root.$emit("bv::hide::popover");
        },
    },
};
</script>
<style scoped></style>
