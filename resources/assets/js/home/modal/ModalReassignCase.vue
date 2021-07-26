<template>
  <div>
    <b-modal
      ref="modal-reassign-case"
      hide-footer
      size="lg"
    >
      <template v-slot:modal-title>
        {{ $t('ID_REASSIGN_CASE') }}
        <i class="fas fa-undo"></i>
      </template>
      <b-container fluid>
        <b-row class="my-1">
          <b-col sm="3">
            <label for="selectUser">{{ $t('ID_SELECT_USER') }}</label>
          </b-col>
          <b-col sm="9">
            <b-form-select v-model="userSelected" :options="users"></b-form-select>
          </b-col>
        </b-row>

        <b-row class="my-1">
          <b-col sm="3">
            <label for="reasonReassign">{{ $t('ID_REASON_REASSIGN') }}</label>
          </b-col>
          <b-col sm="9">
            <b-form-textarea
              id="reasonReassign"
              v-model="reasonReassign"              
              rows="3"
              max-rows="6"
            ></b-form-textarea>
          </b-col>
        </b-row>

        <b-row class="my-1">
          <b-col sm="3">
            <label for="notifyUser">{{ $t('ID_NOTIFY_USERS_CASE') }}</label>
          </b-col>
          <b-col sm="9">
            <b-form-checkbox v-model="notifyUser" id="notifyUser" name="notifyUser" switch>
            </b-form-checkbox>
          </b-col>
        </b-row>
      </b-container>
      <div class="modal-footer">
        <div class="float-right">
          <b-button
            variant="danger"
            data-dismiss="modal"
            @click="cancel"
          >
            {{ $t("ID_CANCEL") }}
          </b-button>
          <b-button 
            variant="success" 
            @click="reassignCase"
          >
            {{ $t("ID_PAUSE") }}
          </b-button>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import api from "./../../api/index";
import utils from "../../utils/utils";

export default {
  name: "ModalPauseCase",
  components: {},
  props: {},
  mounted() {},
  data() {
    return {
      data: null,
      locale: 'en-US',
      users: [],
      reasonReassign: null,
      userSelected: null,
      notifyUser: false
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    show() {
      this.getUsersReassign();
      this.$refs["modal-reassign-case"].show();
    },
    cancel() {
      this.$refs["modal-reassign-case"].hide();
    },
    getUsersReassign() {
      let that = this;
      api.cases.getUserReassign(this.data).then((response) => {
        var users = response.data.data,
          i;
        if (response.statusText == "OK") {
          for (i = 0; i < users.length; i += 1) {
            that.users.push({
              value: users[i].USR_UID,
              text: utils.userNameDisplayFormat({
                userName: users[i].USR_USERNAME || "",
                firstName: users[i].USR_FIRSTNAME || "",
                lastName: users[i].USR_LASTNAME || "",
                format: window.config.FORMATS.format || null
              })
            });
          }
        }
      });
    },
    reassignCase() {
      let that = this;
      this.data.userSelected = this.userSelected;
      this.data.reasonReassign = this.reasonReassign;
      this.notifyUser = this.notifyUser;
      api.cases.reassingCase(this.data).then((response) => {
        if (response.statusText == "OK") {
          that.$refs["modal-reassign-case"].hide();
          that.$parent.$refs["vueTable"].getData();
        }
      });
    },
  },
};
</script>

<style>
</style>
