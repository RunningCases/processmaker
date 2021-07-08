export default {
  data() {
    let that = this;
    return {
      typeView: "GRID",
      dataMultiviewHeader: {
        actions: [
          {
            id: "view-grid",
            title: "Grid",
            onClick(action) {
              that.typeView = "GRID";
            },
            icon: "fas fa-table",
          },
          {
            id: "view-list",
            title: "List",
            onClick(action) {
              that.typeView = "LIST";
            },
            icon: "fas fa-list",
          },
          {
            id: "view-card",
            title: "Card",
            onClick(action) {
              that.typeView = "CARD";
            },
            icon: "fas fa-th",
          },
        ],
      }
    }
  },
  created: function () {

  },
  methods: {

  }
}

