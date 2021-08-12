<template>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
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
    </ol>
  </nav>
</template>

<script>
import _ from "lodash";
export default {
  name: "BreadCrumb",
  props: ["options"],
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
          };
        } else {
          options[i].classObject = {
            "breadcrumb-item": true,
            active: false,
          };
        }
      }
      console.log("aquii");
      return options;
    },
  },
};
</script>
