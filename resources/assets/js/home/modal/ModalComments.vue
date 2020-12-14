<template>
  <div>
    <b-modal ref="modal-comments" hide-footer size="xl">
      <div class="row">
        <div class="col-sm-8">
          <case-comments
            :data="dataComments"
            :onClick="onClickComment"
            :postComment="postComment"
          />
        </div>
        <div class="col-sm-4">
          <attached-documents
            :data="dataAttachedDocuments"
          ></attached-documents>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import Api from "./../../api/index";
import CaseComments from "../../components/home/caseDetail/CaseComments.vue";
import AttachedDocuments from "../../components/home/caseDetail/AttachedDocuments.vue";
export default {
  name: "ModalComments",
  components: {
    CaseComments,
    AttachedDocuments,
  },
  props: {},
  mounted() {},
  data() {
    return {
      dataCase: null,
      dataComments: {
        title: this.$i18n.t("ID_COMMENTS"),
        items: [],
      },
      dataAttachedDocuments: {
        title: "Attached Documents",
        items: [],
      },
      onClickComment: (data) => {
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
      postComment: (comment, send) => {
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
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    show() {
      this.getCasesNotes();
      this.$refs["modal-comments"].show();
    },
    cancel() {
      this.$refs["modal-comments"].hide();
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
  },
};
</script>

<style>
</style>
