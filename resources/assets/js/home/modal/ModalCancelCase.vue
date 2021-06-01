<template>
  <div>
    <b-modal
      ref="modal-cancel-case"
      hide-footer
      :title="$t('ID_CANCEL_CASE')"
      size="md"
    >
      <p>
        You are tying to cancel the current case. Please be aware this action
        cannot be undone
      </p>
      <div class="form-group">
        <textarea
          class="form-control"
          name="comments"
          ref="comment"
          cols="80"
          rows="5"
        ></textarea>
      </div>
      <div class="row">
        <div class="col-md-12 ml-auto">
          <input type="checkbox" class="" ref="send" />
          <label class="form-check-label" for="sendEmail">
            Send email to participants</label
          >
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" @click="cancelCase">
          {{ $t("ID_CANCEL_CASE") }}
        </button>
        <button
          type="button"
          class="btn btn-secondary"
          data-dismiss="modal"
          @click="cancel"
        >
          {{ $t("ID_CANCEL") }}
        </button>
      </div>
    </b-modal>
  </div>
</template>

<script>
import api from "./../../api/index";
export default {
  name: "ModalCancelCase",
  components: {},
  props: {
    dataCase: Object,
  },
  mounted() {},
  data() {
    return {
      filter: "",
      categories: [],
      categoriesFiltered: [],
      TRANSLATIONS: window.config.TRANSLATIONS,
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    show() {
      this.$refs["modal-cancel-case"].show();
    },
    cancel() {
      this.$refs["modal-cancel-case"].hide();
    },
    cancelCase() {
      let that = this;
      api.cases
        .cancel(_.extend({}, this.dataCase, {
          COMMENT: this.$refs["comment"].value,
          SEND: this.$refs["send"].checked ? 1 : 0,
        }))
        .then((response) => {
          if (response.status === 200) {
            that.$refs["modal-cancel-case"].hide();
            that.$parent.$parent.page = "todo";
          }
        });
    },
  },
};
</script>

<style>
</style>
