<template>
  <nav aria-label="breadcrumb">
    <ol class="vp-breadcrumb">
      <li
        v-for="item in formatOptions(options)"
        :key="item.label"
        :class="item.classObject"
      >
        <span v-if="item.classObject.active === true">{{ item.label }}</span>
        <a
          v-if="item.classObject.active === false"
          href="#"
          @click="item.onClick"
          >{{ item.label }}</a
        >
      </li>
      <div
        v-for="item in settings"
        :key="item.id"
        class="vp-bread-crumbs-settings vp-float-right vp-inline-block"
      >
        <span @click="item.onClick">
          <i :class="formatClass(item)"></i>
        </span>
      </div>
    </ol>
  </nav>
</template>

<script>
import _ from "lodash";
export default {
  name: "BreadCrumb",
  props: ["options", "settings"],
  data() {
    return {};
  },
  methods: {
    /**
     * format options to Bread Crumbs
     */
    formatOptions(data) {
      let options = data;
      for (let i = 0; i <= options.length - 1; i++) {
        if (i === options.length - 1) {
          options[i].classObject = {
            "breadcrumb-item": true,
            active: true,
            "vp-inline-block": true,
          };
        } else {
          options[i].classObject = {
            "breadcrumb-item": true,
            active: false,
            "vp-inline-block": true,
          };
        }
      }
      return options;
    },
    formatClass(item) {
      return item.class;
    },
  },
};
</script>
<style scoped>
.vp-float-right {
  float: right;
}

.vp-bread-crumbs-settings {
  line-height: 20px;
}

.vp-breadcrumb {
  padding: 0.75rem 1rem;
  margin-bottom: 1rem;
  list-style: none;
  background-color: #e9ecef;
  border-radius: 0.25rem;
}

.vp-inline-block {
  display: inline-block;
}
</style>
