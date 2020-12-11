<template>
  <div id="case-detail" ref="case-detail" class="v-container-case-detail">
    <div>
      <p class="">
        <b-icon icon="arrow-left"></b-icon>
        <button type="button" class="btn btn-link" @click="$emit('onLastPage')">
          {{ $t("ID_BACK") }}
        </button>
        <button-fleft :data="newCase"></button-fleft>
      </p>
      <modal-new-request ref="newRequest"></modal-new-request>
    </div>
    <div class="row">
      <div class="col-sm-9">
        <div id="pending-task" ref="pending-task">
          <v-server-table
            :data="tableData"
            :columns="columns"
            :options="options"
            ref="vueTable"
            style="height: 120px"
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
        <TabsCaseDetail
          :dataCaseSummary="dataCaseSummaryTab"
          :dataCase="dataCase"
        ></TabsCaseDetail>
        <ModalCancelCase ref="modal-cancel-case"></ModalCancelCase>
      </div>
      <div class="col-sm-3">
        <case-summary
          v-if="dataCaseSummary"
          :data="dataCaseSummary"
        ></case-summary>
        <io-documents
          v-if="
            dataIoDocuments.inputDocuments.length > 0 ||
            dataIoDocuments.outputDocuments.length > 0
          "
          :data="dataIoDocuments"
        ></io-documents>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-9">
        <case-comments
          :data="dataComments"
          :onClick="onClickComment"
          :postComment="postComment"
        />
      </div>
      <div class="col-sm-3">
        <attached-documents
          v-if="dataAttachedDocuments.items.length > 0"
          :data="dataAttachedDocuments"
        ></attached-documents>
      </div>
    </div>
  </div>
</template>

<script>
import IoDocuments from "../components/home/caseDetail/IoDocuments.vue";
import CaseSummary from "../components/home/caseDetail/CaseSummary.vue";
import AttachedDocuments from "../components/home/caseDetail/AttachedDocuments.vue";
import CaseComment from "../components/home/caseDetail/CaseComment";
import CaseComments from "../components/home/caseDetail/CaseComments";
import TabsCaseDetail from "../home/TabsCaseDetail.vue";
import ButtonFleft from "../components/home/ButtonFleft.vue";
import ModalCancelCase from "../home/modal/ModalCancelCase.vue";
import ModalNewRequest from "./ModalNewRequest.vue";

import Api from "../api/index";
export default {
  name: "CaseDetail",
  components: {
    TabsCaseDetail,
    IoDocuments,
    CaseSummary,
    AttachedDocuments,
    CaseComment,
    CaseComments,
    ModalCancelCase,
    ButtonFleft,
    ModalNewRequest,
  },
  props: {},
  data() {
    return {
      dataCase: null,
      newCase: {
        title: this.$i18n.t("ID_NEW_CASE"),
        class: "btn-success",
        onClick: () => {
          this.$refs["newRequest"].show();
        },
      },
      columns: [
        "task",
        "case_title",
        "assignee",
        "status",
        "due_date",
        "actions",
      ],
      tableData: [
        {
          task: "Approve Art",
          case_title: "Case Title A",
          assignee: "User 1",
          status: "To Do",
          due_date: "3 days",
        },
      ],
      options: {
        headings: {
          task: this.$i18n.t("ID_TASK"),
          case_title: this.$i18n.t("ID_CASE_TITLE"),
          assignee: this.$i18n.t("ID_ASSIGNEE"),
          status: this.$i18n.t("ID_STATUS"),
          due_date: this.$i18n.t("ID_DUE_DATE"),
          actions: this.$i18n.t("ID_ACTIONS"),
        },
        selectable: {
          mode: "single", // or 'multiple'
          only: function (row) {
            return true; // any condition
          },
          selectAllMode: "all", // or 'page',
          programmatic: false,
        },
        filterable: false,
      },
      dataCaseSummary: null,
      dataCaseSummaryTab: null,
      dataIoDocuments: {
        titleInput: this.$i18n.t("ID_REQUEST_DOCUMENTS"),
        titleOutput: this.$i18n.t("ID_OUTPUT_DOCUMENTS"),
        inputDocuments: [],
        outputDocuments: [],
      },
      dataAttachedDocuments: {
        title: "Attached Documents",
        items: [],
      },
      dataComments: {
        title: "Comments",
        items: [],
      },
    };
  },

  mounted() {
    let that = this;
    this.dataCase = this.$parent.dataCase;
    this.$el.getElementsByClassName("VuePagination__count")[0].remove();
    this.getDataCaseSummary();
    this.getInputDocuments();
    this.getOutputDocuments();
    this.getCasesNotes();
  },
  methods: {
    postComment(comment, send) {
      let that = this;
      Api.caseNotes
        .post(
          _.extend({}, this.dataCase, {
            COMMENT: comment,
            SEND_MAIL: send,
          })
        )
        .then((response) => {
          if (response.data.success === "success") {
            that.getCasesNotes();
          }
        });
    },
    onClickComment(data) {
      let att = [];
      this.dataAttachedDocuments.items = [];
      _.each(data.data.attachments, (a) => {
        att.push({
          data: a,
          title: a.APP_DOC_FILENAME,
          extension: a.APP_DOC_FILENAME.split(".").pop(),
          onClick: () => {},
        });
      });
      this.dataAttachedDocuments.items = att;
    },
    getDataCaseSummary() {
      let that = this;
      Api.cases
        .casesummary(this.dataCase)
        .then((response) => {
          var data = response.data;
          this.formatCaseSummary(response.data);
          this.dataCaseSummary = {
            title: this.$i18n.t("ID_SUMMARY"),
            titleActions: this.$i18n.t("ID_ACTIONS"),
            btnLabel: this.$i18n.t("ID_CANCEL_CASE"),
            btnType: false,
            onClick: () => {
              that.$refs["modal-cancel-case"].show();
            },
            label: {
              numberCase: data[2].label,
              process: data[0].label,
              status: data[3].label,
              caseTitle: data[1].label,
              created: data[6].label,
              delegationDate: response.data[11].label,
              duration: this.$i18n.t("ID_DURATION"),
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
          };
        })
        .catch((err) => {
          throw new Error(err);
        });
    },
    getInputDocuments() {
      Api.cases
        .inputdocuments(this.dataCase)
        .then((response) => {
          let data = response.data,
            document = data.data,
            i,
            info;

          if (data.totalCount > 0 && document !== []) {
            this.dataIoDocuments.inputDocuments = [];
            for (i = 0; i < data.totalCount; i += 1) {
              info = {
                title: document[i].TITLE,
                extension: document[i].TITLE.split(".")[1],
                onClick: document[i].DOWNLOAD_LINK,
              };
              this.dataIoDocuments.inputDocuments.push(info);
            }
          }
        })
        .catch((err) => {
          throw new Error(err);
        });
    },
    getOutputDocuments() {
      Api.cases
        .outputdocuments(this.dataCase)
        .then((response) => {
          var data = response.data,
            document = data.data,
            i,
            info;

          if (data.totalCount > 0 && document !== []) {
            this.dataIoDocuments.outputDocuments = [];
            for (i = 0; i < data.totalCount; i += 1) {
              info = {
                title: document[i].TITLE,
                extension: document[i].TITLE.split(".")[1],
                onClick: document[i].DOWNLOAD_LINK,
              };
              this.dataIoDocuments.outputDocuments.push(info);
            }
          }
        })
        .catch((err) => {
          throw new Error(err);
        });
    },
    /**
     * Get for user format name configured in Processmaker Environment Settings
     *
     * @param {string} name
     * @param {string} lastName
     * @param {string} userName
     * @return {string} nameFormat
     */
    nameFormatCases(name, lastName, userName) {
      let nameFormat = "";
      if (/^\s*$/.test(name) && /^\s*$/.test(lastName)) {
        return nameFormat;
      }
      if (this.nameFormat === "@firstName @lastName") {
        nameFormat = name + " " + lastName;
      } else if (this.nameFormat === "@firstName @lastName (@userName)") {
        nameFormat = name + " " + lastName + " (" + userName + ")";
      } else if (this.nameFormat === "@userName") {
        nameFormat = userName;
      } else if (this.nameFormat === "@userName (@firstName @lastName)") {
        nameFormat = userName + " (" + name + " " + lastName + ")";
      } else if (this.nameFormat === "@lastName @firstName") {
        nameFormat = lastName + " " + name;
      } else if (this.nameFormat === "@lastName, @firstName") {
        nameFormat = lastName + ", " + name;
      } else if (this.nameFormat === "@lastName, @firstName (@userName)") {
        nameFormat = lastName + ", " + name + " (" + userName + ")";
      } else {
        nameFormat = name + " " + lastName;
      }
      return nameFormat;
    },

    getCasesNotes() {
      let that = this;
      Api.cases
        .casenotes(this.dataCase)
        .then((response) => {
          that.formatResponseCaseNotes(response.data.notes);
        })
        .catch((err) => {
          throw new Error(err);
        });
    },
    formatResponseCaseNotes(notes) {
      let that = this,
        notesArray = [];
      _.each(notes, (n) => {
        notesArray.push({
          user: that.nameFormatCases(
            n.USR_FIRSTNAME,
            n.USR_LASTNAME,
            n.USR_USERNAME
          ),
          date: n.NOTE_DATE,
          comment: n.NOTE_CONTENT,
          data: n,
        });
      });

      this.dataComments.items = notesArray;
    },
    formatCaseSummary(data) {
      let index,
        sections = [];
      this.dataCaseSummaryTab = [];
      _.each(data, (o) => {
        if (
          (index = _.findIndex(sections, (s) => {
            return s.title == o.section;
          })) == -1
        ) {
          sections.push({
            title: o.section,
            items: [],
          });
          index = 0;
        }
        sections[index].items.push(o);
      });

      this.dataCaseSummaryTab = sections;
    },
  },
};
</script>
<style>
.v-container-case-detail {
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 50px;
  padding-right: 20px;
}
</style>