<template>
  <div>
    <b-modal
      ref="modal-claim-case"
      hide-footer
      :title="$t('ID_CONFIRMATION')"
      size="md"
    >
      <p>
        {{ $t("ID_ARE_YOU_SURE_CLAIM_TASK") }}
      </p>
      <div class="row float-right">
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-danger"
            data-dismiss="modal"
            @click="cancel"
          >
            {{ $t("ID_CANCEL") }}
          </button>
          <button type="button" class="btn btn-success" @click="claimCase">
            {{ $t("ID_CLAIM") }}
          </button>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import api from "./../../api/index";
export default {
  name: "ModalClaimCase",
  components: {},
  props: {},
  mounted() {},
  data() {
    return {
      data: null,
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    show() {
      this.$refs["modal-claim-case"].show();
    },
    cancel() {
      this.$refs["modal-claim-case"].hide();
    },
    claimCase() {
      let that = this;
      api.cases.claim(this.data).then((response) => {
        if (response.statusText == "OK") {
          that.$refs["modal-claim-case"].hide();
          that.$parent.$emit("onUpdateDataCase", {
            APP_UID: this.data.APP_UID,
            DEL_INDEX: this.data.DEL_INDEX,
            PRO_UID: this.data.PRO_UID,
            TAS_UID: this.data.TAS_UID,
            ACTION: "todo",
          });
          that.$parent.$emit("onUpdatePage", "XCase");
        }
      });
    },
  },
};
</script>

<style>
</style>
