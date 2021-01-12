<template>
  <div
    v-bind:class="{ hiddencon: !isRtl, 'hiddencon-rtl': isRtl }"
    v-if="openCaseState"
  >
    <div
      v-bind:class="{ 'hiddencon-label': !isRtl, 'hiddencon-label-rtl': isRtl }"
    >
      <i class="fa fa-th"></i>
    </div>
    <div class="btn-group-vertical">
      <button
        type="button"
        class="btn btn-secondary"
        data-toggle="tooltip"
        data-placement="bottom"
        title="CLOSE"
      >
        <i class="fa fa-x2 fa-times"></i>
      </button>
      <button
        type="button"
        class="btn btn-secondary"
        data-toggle="tooltip"
        data-placement="bottom"
        title="HIDE_INBOX"
      >
        <i class="fa fa-outdent"></i>
      </button>
      <button
        type="button"
        class="btn btn-secondary"
        data-toggle="tooltip"
        data-placement="bottom"
        title="language.ID_INBOX_SHOW_INBOX"
      >
        <i class="fa fa-columns"></i>
      </button>
      <button
        type="button"
        class="btn btn-secondary"
        data-toggle="tooltip"
        data-placement="bottom"
        title="language.ID_INBOX_FULL_SCREEN"
      >
        <i class="fa fa-window-maximize"></i>
      </button>
      <button
        type="button"
        class="btn btn-secondary"
        data-toggle="modal"
        data-placement="bottom"
        data-target="#debugModal"
        @click="showDebugger"
      >
        <i class="fa fa-bug"></i>
      </button>
    </div>
    <ModalDebugger ref="modal-debugger" />
  </div>
</template>

<script>
import ModalDebugger from "./ModalDebugger.vue";
export default {
  name: "ButtonFleft",
  props: {
    data: Object,
  },
  components: {
    ModalDebugger,
  },
  data() {
    return {
      openCaseState: true,
      isRtl: false,
    };
  },
  methods: {
    classBtn(cls) {
      return "btn v-btn-request " + cls;
    },
    showDebugger() {
      this.$refs["modal-debugger"].show();
    },
  },
};
</script>

<style>
.btn-group,
.btn-group-vertical {
  position: relative;
  display: -webkit-inline-box;
  display: -ms-inline-flexbox;
  display: inline-flex;
  vertical-align: middle;
}

.btn-group > .btn,
.btn-group-vertical > .btn {
  position: relative;
  -webkit-box-flex: 0;
  -ms-flex: 0 1 auto;
  flex: 0 1 auto;
}

.btn-group > .btn:hover,
.btn-group-vertical > .btn:hover {
  z-index: 1;
}

.btn-group > .btn:focus,
.btn-group > .btn:active,
.btn-group > .btn.active,
.btn-group-vertical > .btn:focus,
.btn-group-vertical > .btn:active,
.btn-group-vertical > .btn.active {
  z-index: 1;
}

.btn-group .btn + .btn,
.btn-group .btn + .btn-group,
.btn-group .btn-group + .btn,
.btn-group .btn-group + .btn-group,
.btn-group-vertical .btn + .btn,
.btn-group-vertical .btn + .btn-group,
.btn-group-vertical .btn-group + .btn,
.btn-group-vertical .btn-group + .btn-group {
  margin-left: -1px;
}

.btn-toolbar {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  -webkit-box-pack: start;
  -ms-flex-pack: start;
  justify-content: flex-start;
}

.btn-toolbar .input-group {
  width: auto;
}

.btn-group > .btn:first-child {
  margin-left: 0;
}

.btn-group > .btn:not(:last-child):not(.dropdown-toggle),
.btn-group > .btn-group:not(:last-child) > .btn {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.btn-group > .btn:not(:first-child),
.btn-group > .btn-group:not(:first-child) > .btn {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

.dropdown-toggle-split {
  padding-right: 0.5625rem;
  padding-left: 0.5625rem;
}

.dropdown-toggle-split::after,
.dropup .dropdown-toggle-split::after,
.dropright .dropdown-toggle-split::after {
  margin-left: 0;
}

.dropleft .dropdown-toggle-split::before {
  margin-right: 0;
}

.btn-sm + .dropdown-toggle-split,
.btn-group-sm > .btn + .dropdown-toggle-split {
  padding-right: 0.375rem;
  padding-left: 0.375rem;
}

.btn-lg + .dropdown-toggle-split,
.btn-group-lg > .btn + .dropdown-toggle-split {
  padding-right: 0.75rem;
  padding-left: 0.75rem;
}

.btn-group-vertical {
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-align: start;
  -ms-flex-align: start;
  align-items: flex-start;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
}

.btn-group-vertical .btn,
.btn-group-vertical .btn-group {
  width: 100%;
}

.btn-group-vertical > .btn + .btn,
.btn-group-vertical > .btn + .btn-group,
.btn-group-vertical > .btn-group + .btn,
.btn-group-vertical > .btn-group + .btn-group {
  margin-top: -1px;
  margin-left: 0;
}

.btn-group-vertical > .btn:not(:last-child):not(.dropdown-toggle),
.btn-group-vertical > .btn-group:not(:last-child) > .btn {
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.btn-group-vertical > .btn:not(:first-child),
.btn-group-vertical > .btn-group:not(:first-child) > .btn {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

.btn-group-toggle > .btn,
.btn-group-toggle > .btn-group > .btn {
  margin-bottom: 0;
}

.btn-group-toggle > .btn input[type="radio"],
.btn-group-toggle > .btn input[type="checkbox"],
.btn-group-toggle > .btn-group > .btn input[type="radio"],
.btn-group-toggle > .btn-group > .btn input[type="checkbox"] {
  position: absolute;
  clip: rect(0, 0, 0, 0);
  pointer-events: none;
}

.hiddencon {
  margin: 0;
  padding: 0;
  position: fixed;
  right: -37px;
  top: 10px;
  opacity: 0.9;
}

.hiddencon-rtl {
  margin: 0;
  padding: 0;
  position: fixed;
  left: -37px;
  top: 10px;
  opacity: 0.9;
}

.hiddencon2 {
  top: auto;
  bottom: 10px;
}

.hiddencon:hover {
  right: 0;
}

.hiddencon-rtl:hover {
  left: 0;
}

.hiddencon-label {
  margin-top: -40px;
  margin-left: -23px;
  padding: 4px;
  position: absolute;
  top: 50%;
  display: inline-block;
  color: white;
  background: #626262;
  font-size: 14px;
  border-radius: 20px 0 0 20px;
}

.hiddencon-label-rtl {
  margin-top: -40px;
  margin-right: -23px;
  padding: 4px;
  position: absolute;
  top: 50%;
  display: inline-block;
  color: white;
  background: #626262;
  font-size: 14px;
  border-radius: 0 20px 20px 0;
}

.hiddencon,
.hiddencon-label {
  -webkit-transition: all 0.4s ease-in-out;
  transition: all 0.4s ease-in-out;
}

.hiddencon p,
.hiddencon ul {
  margin: 0;
  padding: 0;
  border: 8px solid #798189;
  border-right: 0;
  color: #fff;
  background-color: #000;
  text-align: center;
  vertical-align: center;
  border-radius: 10px 0 0 10px;
}

.hiddencon ul {
  margin: 0;
  overflow: auto;
}

.hiddencon li {
  display: block;
}

.hiddencon li a {
  display: block;
  padding: 10px;
  border-bottom: 1px solid #333;
  color: #ddd;
  -webkit-transition: all 0.4s linear;
  transition: all 0.4s linear;
}

.hiddencon li:last-child a {
  border-bottom: 0;
}

.hiddencon li a:hover {
  background-color: #333;
  text-decoration: none;
}
</style>