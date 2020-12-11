<template>
  <div class="comment mb-2 row" @click="onClick(data)">
    <div class="comment-avatar col-md-1 col-sm-2 text-center pr-1">
      <a href=""
        ><img
          class="mx-auto rounded-circle v-img-fluid"
          :src="path"
          alt="avatar"
      /></a>
    </div>
    <div class="comment-content col-md-11 col-sm-10 v-comment">
      <div class="comment-meta">
        <a href="#">{{ data.user }}</a> {{ data.date }}
      </div>
      <div class="comment-body">
        <p>{{ data.comment }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CaseComment",
  props: {
    data: Object,
    onClick: Function,
  },
  data() {
    return {};
  },
  computed: {
    path() {
      if (this.data) {
        return (
          window.config.SYS_SERVER +
          window.config.SYS_URI +
          `users/users_ViewPhotoGrid?pUID=${this.data.data.USR_UID}`
        );
      }
      return "";
    },
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    classIcon(icon) {
      return this.icon[icon];
    },
  },
};
</script>

<style>
.v-img-fluid {
  max-width: 30px;
  height: auto;
}
</style>