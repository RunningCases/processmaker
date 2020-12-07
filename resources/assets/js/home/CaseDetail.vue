<template>
    <div id="case-detail" ref="case-detail" class="v-container-mycases">
        <div>
            <p class="mb-2"><b-icon icon="arrow-left" @click="backSearch()"></b-icon><u>Back to Results</u></p>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div id="pending-task" ref="pending-task">
                    <v-server-table
                        :data="tableData"
                        :columns="columns"
                        :options="options"
                        ref="vueTable"
                        style="height:120px"
                    >
                        <div slot="task" slot-scope="props">
                            {{ props.row.TASK }}
                        </div>
                        <div slot="case_title" slot-scope="props">
                            {{ props.row.CASE_TITLE }}
                        </div>
                        <div slot="assignee" slot-scope="props">
                            {{ props.row.ASSIGNEE }}
                        </div>
                        <div slot="status" slot-scope="props">
                            {{ props.row.STATUS }}
                        </div>
                        <div slot="due_date" slot-scope="props">
                            {{ props.row.DUE_DATE }}
                        </div>
                        <div slot="actions">
                            <div class="btn-default">
                                <i class="fas fa-comments"></i>
                                <span class="badge badge-light">9</span>
                                <span class="sr-only">Continue</span>
                            </div>
                        </div>
                    </v-server-table>
                </div>
                <Tabs :data="dataTabs"></Tabs>
                <div>
                    <p><b>Comments</b></p>
                    <div class="form-group">
                        <textarea class="form-control" name="comments" id="comments" cols="80" rows="5"></textarea>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="sendEmail">
                            <label class="form-check-label" for="sendEmail">Send email to Participants</label>
                            <button class="btn btn-secondary btn-sm" id="comment" name="comment">Comment</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm4">
                <case-summary :data="dataCaseSummary"></case-summary>
                <io-documents :data="dataIoDocuments"></io-documents>
                <attached-documents :data="dataAttachedDocuments"></attached-documents>
            </div>
        </div>
    </div>
</template>

<script>
import Tabs from '../components/Tabs.vue';
import IoDocuments from '../components/cases/IoDocuments.vue';
import CaseSummary from '../components/cases/CaseSummary.vue';
import AttachedDocuments from '../components/cases/AttachedDocuments.vue';

export default {
    name: "CaseDetail",
    components: {
        Tabs,
        IoDocuments,
        CaseSummary,
        AttachedDocuments
    },
    props: {},
    data () {
        return {
                columns: ["task", "case_title", "assignee", "status", "due_date", "actions"],
                tableData:[{
                    task: "Approve Art",
                    case_title: "Case Title A",
                    assignee: "User 1",
                    status: "To Do",
                    due_date: "3 days",
                    
                }],
                options: {
                    headings: {
                        task: this.$i18n.t("ID_TASK"),
                        case_title: this.$i18n.t("ID_CASE_TITLE"),
                        assignee: this.$i18n.t("ID_ASSIGNEE"),
                        status: this.$i18n.t("ID_STATUS"),
                        due_date: this.$i18n.t("ID_DUE_DATE"),
                        actions: this.$i18n.t("ID_ACTIONS")
                    },
                    selectable: {
                        mode: 'single', // or 'multiple'
                        only: function (row) {
                            return true // any condition
                        },
                        selectAllMode: 'all',// or 'page',
                        programmatic: false,
                    },
                    filterable: false
                    
                },
                dataCaseSummary: {
                    title: "Case Summary",
                    titleActions: "Actions",
                    btnLabel: "Cancel Request",
                    btnType: false,
                    onClick: () => {
                        console.log("acitons");
                    },
                    label: {
                        numberCase: "Case #",
                        process: "Process",
                        status: "Status",
                        caseTitle: "Case title",
                        created: "Created",
                        delegationDate: "Delegation Date",
                        duration: "Duration",
                    },
                    text: {
                        numberCase: "",
                        process: "",
                        status: "",
                        caseTitle: "",
                        created: "",
                        delegationDate: "",
                        duration: "",
                    },
                },
                dataIoDocuments: {
                  titleInput: "Input Document",
                  inputDocuments: [
                    {
                      title: "Invoice January 2018.pdf",
                      extension: "pdf",
                      onClick: () => {
                        console.log("Attached document");
                      }
                    }
                  ],
                  titleOutput: "Output Document",
                  outputDocuments: [
                    {
                      title: "Invoice January 2018.pdf",
                      extension: "pdf",
                      onClick: () => {
                        console.log("Attached document");
                      }
                    }
                  ],
                },
                dataAttachedDocuments: {
                  title: "Attached Documents",
                  items: [
                    {
                      title: "Invoice January 2018.pdf",
                      extension: "pdf",
                      onClick: () => {
                        console.log("Attached document");
                      },
                    }
                  ],
                },
                dataTabs: {
                  items: [
                    {
                      title: "Request Summary",
                      data: {
                        firstName: "Jason",
                        lastName: "Burne",
                        userName: "jburne@processmaker.com",
                      }
                    },
                    {
                      title: "Process Map",
                      data: [
                        {
                          pro_uid: "123dit"
                        }
                      ]
                    },
                    {
                      title: "Case History"
                    },
                    {
                      title: "Change Log"
                    }
                  ]
                }
            };
        },
    
    mounted() {
        let that = this;
        this.$el.getElementsByClassName('VuePagination__count')[0].remove();
        this.getDataCaseSummary();
        this.getDataIODocuments();
    },
    methods: {
        getDataCaseSummary() {
            var data = new FormData();
            data.append('appUid', APP_UID);
            data.append('delIndex', DEL_INDEX);
            data.append('action', 'todo');

            ProcessMaker.apiClient.post('../../../appProxy/getSummary', data).then((response) => {
                var data = response.data;
                this.dataCaseSummary = {
                    title: "Case Summary",
                    titleActions: "Actions",
                    btnLabel: "Cancel Request",
                    btnType: false,
                    onClick: () => {
                        console.log("acitons");
                    },
                    label: {
                        numberCase: data[2].label,
                        process: data[0].label,
                        status: data[3].label,
                        caseTitle: data[1].label,
                        created: data[6].label,
                        delegationDate: response.data[11].label,
                        duration: "Duration",
                    },
                    text: {
                        numberCase: data[2].value,
                        process: data[0].value,
                        status: data[3].value,
                        caseTitle: data[1].value,
                        created: data[6].value,
                        delegationDate: response.data[11].value.split(" ")[0],
                        duration: response.data[11].value.split(" ")[1],
                    },
                }  
            }).catch((err) => {
                throw new Error(err);
            });
        },
        getDataIODocuments() {
            this.getInputDocuments();
            this.getOutputDocuments();
        },
        getInputDocuments() {
            var data = new FormData();
            data.append ('appUid', APP_UID);
            data.append ('delIndex', DEL_INDEX);
            ProcessMaker.apiClient.post('../../../cases/cases_Ajax.php?action=getCasesInputDocuments',data).then((response) => {
                var data = response.data,
                    document = data.data,
                    i,
                    info;

                if (data.totalCount > 0 && document !== []) {
                    this.dataIoDocuments.inputDocuments = [];
                    for (i = 0; i < data.totalCount; i += 1) {
                        info = {
                            "title" : document[i].TITLE,
                            "extension" : document[i].TITLE.split(".")[1],
                            "onClick" : document[i].DOWNLOAD_LINK
                        };
                    this.dataIoDocuments.inputDocuments.push(info);
                    }
                }
            }).catch((err) => {
                throw new Error(err);
            });
        },
        getOutputDocuments() {
            var data = new FormData();
            data.append ('appUid', APP_UID);
            data.append ('delIndex', DEL_INDEX);
            ProcessMaker.apiClient.post('../../../cases/cases_Ajax.php?action=getCasesOutputDocuments',data).then((response) => {
                var data = response.data,
                    document = data.data,
                    i,
                    info;

                if (data.totalCount > 0 && document !== []) {
                    this.dataIoDocuments.outputDocuments = [];
                    for (i = 0; i < data.totalCount; i += 1) {
                        info = {
                            "title" : document[i].TITLE,
                            "extension" : document[i].TITLE.split(".")[1],
                            "onClick" : document[i].DOWNLOAD_LINK
                        };
                    this.dataIoDocuments.outputDocuments.push(info);
                    }
                }
            }).catch((err) => {
                throw new Error(err);
            });
        },
    }
}
</script>