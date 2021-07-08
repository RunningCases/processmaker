export default {
  data() {
    let that = this;
    return {
      config: {
        page: 1
      },
      data: []
    }
  },
  mounted: function () {
    console.log("jonas");
    this.getData();
  },
  methods: {
    /**
     * Get data similar to vue Table
     */
    getData() {
      let options = _.extend({}, this.config, this.options),
        that = this;
      console.log("GETTTTTTTTTT");

      this.options.requestFunction(options)
        .then((data) => {
          console.log("jonas vue car view");
          that.data = data.data;
        })
        .catch(() => {

        });
    },
    /**
     * Get data when press the button more view
     */
    viewMore() {

    }
  }
}

