<template>
  <div class="container py-2">
    <div class="row"></div>
    <div class="comments col-md-12" id="comments">
      <p>
        <b>{{ data.title }}</b>
      </p>
      <div v-for="item in data.items" :key="item.date">
        <case-comment :data="item" :onClick="onClick" />
      </div>
    </div>
    <div class="comments col-md-12">
      <div class="comment mb-2 row">
        <div class="comment-avatar col-md-1 col-sm-2 text-center pr-1">
          <a href=""
            ><img
              class="mx-auto rounded-circle v-img-fluid"
              src="http://demos.themes.guide/bodeo/assets/images/users/m103.jpg"
              alt="avatar"
          /></a>
        </div>
        <div class="comment-content col-md-11 col-sm-10 v-comment">
          <div class="comment-meta">
            <a href="#">{{ data.user }}</a> {{ data.date }}
          </div>
          <div class="comment-body">
            <div class="form-group">
              <textarea
                class="form-control"
                name="comments"
                ref="comment"
                cols="80"
                rows="5"
              ></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="comment mb-2 row float-right">
        <div class="form-check v-check-comment">
          <input type="checkbox" class="form-check-input" ref="send" />
          <label class="form-check-label" for="sendEmail">
            {{ $t("ID_SEND_EMAIL_CASE_PARTICIPANTS") }}</label
          >
        </div>

        <button class="btn btn-secondary btn-sm" @click="onClickComment">
          {{ $t("ID_SEND") }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import CaseComment from "./CaseComment.vue";

export default {
  name: "CaseComments",
  props: {
    data: Object,
    onClick: Function,
    postComment: Function,
  },
  components: {
    CaseComment,
  },
  data() {
    return {};
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    classIcon(icon) {
      return this.icon[icon];
    },
    onClickComment() {
      this.postComment(this.$refs["comment"].value, this.$refs["send"].checked);
      this.resetComment();
    },
    resetComment() {
      this.$refs["comment"].value = "";
      this.$refs["send"].checked = false;
    },
  },
};
</script>

<style>
.v-check-comment {
  padding-right: 20px;
}
</style>