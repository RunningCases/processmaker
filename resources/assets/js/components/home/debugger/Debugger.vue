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
      >
        <i class="fa fa-bug"></i>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "ButtonFleft",
  props: {
    data: Object,
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
</style>