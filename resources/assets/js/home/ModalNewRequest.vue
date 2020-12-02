<template>
  <div>
    <b-modal
      ref="my-modal"
      hide-footer
      title="Weve made it easy for you to make the following request"
      size="xl"
    >
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1"
            ><i class="fas fa-search"></i
          ></span>
        </div>
        <input
          v-model="filter"
          type="text"
          class="form-control"
          placeholder="Search"
          aria-label="Search"
          aria-describedby="basic-addon1"
          @input="onChangeFilter"
        />
      </div>
      <div v-for="item in categories" :key="item.title">
        <process-category :data="item" />
      </div>
    </b-modal>
  </div>
</template>

<script>
import ProcessCategory from "./../components/home/newRequest/ProcessCategory.vue";
import api from "./../api/index";
import _ from "lodash";
export default {
  name: "ModalNewRequest",
  components: {
    ProcessCategory,
  },
  props: {
    data: Object,
  },
  mounted() {
    //this.categoriesFiltered = this.categories;
  },
  data() {
    return {
      filter: "",
      categories: [],
      //Data for test
      dataCaseSummary: {
        title: "Case Summary",
        titleActions: "Actions",
        btnLabel: "Success",
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
          numberCase: "123",
          process: "Leave Absence Request",
          status: "In progress",
          caseTitle: "CVacation request for Enrique",
          created: "# days Ago",
          delegationDate: "10 mins ago",
          duration: "34hrs",
        },
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
          },
          {
            title: "Invoice Febrauery 2018.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "GPRD Employee GR90.doc",
            extension: "doc",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "Contract one tres.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "GPRD Employee 2020.doc",
            extension: "doc",
            onClick: () => {
              console.log("Attached document");
            },
          },
        ],
      },
      dataIoDocuments: {
        titleInput: "Input Document",
        inputDocuments: [
          {
            title: "Invoice January 2018.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "Invoice Febrauery 2018.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "GPRD Employee GR90.doc",
            extension: "doc",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "Contract one tres.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "GPRD Employee 2020.doc",
            extension: "doc",
            onClick: () => {
              console.log("Attached document");
            },
          },
        ],
        titleOutput: "Output Document",
        outputDocuments: [
          {
            title: "Invoice January 2018.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "Invoice Febrauery 2018.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "GPRD Employee GR90.doc",
            extension: "doc",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "Contract one tres.pdf",
            extension: "pdf",
            onClick: () => {
              console.log("Attached document");
            },
          },
          {
            title: "GPRD Employee 2020.doc",
            extension: "doc",
            onClick: () => {
              console.log("Attached document");
            },
          },
        ],
      },
      dataComments: {
        title: "Comments",
        items: [
          {
            user: "Gustavo Cruz",
            date: "Today 2:38",
            comment:
              "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod http://wwwwww.com tempoua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.",
          },
          {
            user: "Gustavo Cruz",
            date: "Today 2:39",
            comment:
              "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod http://wwwwww.com tempoua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.",
          },
          {
            user: "Gustavo Cruz",
            date: "Today 2:40",
            comment:
              "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod http://wwwwww.com tempoua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.",
          },
        ],
      },
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    show() {
      this.$refs["my-modal"].show();
      this.getProcess();
    },
    getProcess() {
      let that = this;
      api.process
        .get()
        .then((response) => {
          that.categories = that.formatResponseGetProcess(response);
        })
        .catch((e) => {
          console.error(e);
        });
    },
    /**
     * Change the property filter
     */
    onChangeFilter() {
      let that = this,
        categories = [],
        processes = [];
      this.categoriesFiltered = [];
      _.each(this.categories, (o) => {
        processes = that.filterProcesses(o.items);
        if (processes.length != 0) {
          that.categoriesFiltered.push({
            title: o.title,
            items: processes,
          });
        }
      });
    },
    /**
     * Filter the processes in category, serach by title and description
     */
    filterProcesses(processes) {
      let that = this;
      return _.filter(processes, (p) => {
        return (
          _.toLower(p.title).search(_.lowerCase(that.filter)) != -1 ||
          _.toLower(p.description).search(_.lowerCase(that.filter)) != -1
        );
      });
    },
    formatResponseGetProcess(response) {
      let res = [],
        items,
        that = this,
        data = response.data;
      _.each(data, (o) => {
        items = [];
        _.each(o.children, (v) => {
          items.push({
            title: v.otherAttributes.value,
            task_uid: v.tas_uid,
            pro_uid: v.pro_uid,
            description: v.otherAttributes.catname,
            onClick: that.startNewCase,
          });
        });
        res.push({
          title: o.text,
          items: items,
        });
      });
      return res;
    },
    startNewCase(dt) {
      let self = this;
      api.cases
        .start(dt)
        .then(function (data) {
          console.log("newCase yeah!!!!!!!!!!");
          if (self.isIE) {
            window.open(data.data.url);
          } else {
            window.location.href = `http://localhost/sysworkflow/en/neoclassic/viena/index.php/cases/xcase/project/${dt.pro_uid}/activity/${dt.task_uid}/case/${data.data.caseId}/index/${data.data.caseIndex}`;
          }
        })
        .catch((err) => {
          throw new Error(err);
        });
    },
  },
};
</script>

<style>
</style>
