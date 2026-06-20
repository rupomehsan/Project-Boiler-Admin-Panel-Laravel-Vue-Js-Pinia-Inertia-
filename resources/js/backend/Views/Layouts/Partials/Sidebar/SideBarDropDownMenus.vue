<template>
  <li :class="`mm-collapse ${is_show && 'mm-active'}`">
    <a @click.prevent="toggleMenu" class="has-arrow" href="#">
      <div class="parent-icon">
        <i v-if="icon" :class="icon"></i>
        <img v-else-if="icon_image" :src="icon_image" alt="" />
      </div>
      <div class="menu-title">{{ menu_title }}</div>
    </a>
    <ul :class="`mm-collapse ${is_show && 'mm-show'}`">
      <template v-for="(menu, index) in menus" :key="index">
        <li
          v-if="!menu.permission || has_permission(menu.permission)"
          :class="{ active: isActiveRoute(menu) }"
        >
          <router-link
            :to="menu.route ? menu.route : { name: menu.route_name }"
            :class="{ active: isActiveRoute(menu) }"
          >
            <i :class="menu.icon"></i>
            {{ menu.title }}
          </router-link>
        </li>
      </template>
    </ul>
  </li>
</template>

<script>
import { auth_store } from "../../../../GlobalStore/auth_store";
import { mapState } from "pinia";

export default {
  props: {
    menu_title: String,
    menus: Array,
    icon_image: {
      type: String,
      default: "",
    },
    icon: {
      type: String,
      default: "",
    },
  },
  data: () => ({
    is_show: 0,
  }),
  computed: {
    ...mapState(auth_store, {
      has_permission: "has_permission",
    }),
    isCurrentMenuActive() {
      // Check if any child route is currently active (supports menu.route object or legacy route_name)
      return this.menus.some((menu) => this.isActiveRoute(menu));
    },
  },
  watch: {
    $route: {
      immediate: true,
      handler() {
        this.updateMenuState();
      },
    },
  },
  mounted() {
    this.updateMenuState();

    // Create unique event name for this menu
    this.collapseEventName = `collapse-${this.menu_title.replace(/\s+/g, "-").toLowerCase()}`;

    // Listen for global collapse events
    window.addEventListener("collapse-all-menus", this.handleCollapseAll);
  },
  beforeDestroy() {
    window.removeEventListener("collapse-all-menus", this.handleCollapseAll);
  },
  methods: {
    toggleMenu() {
      if (!this.is_show) {
        // Before opening this menu, close all others
        this.collapseAllOtherMenus();
        this.is_show = true;
      } else {
        this.is_show = false;
      }
    },
    collapseAllOtherMenus() {
      // Dispatch event to close all other menus
      window.dispatchEvent(
        new CustomEvent("collapse-all-menus", {
          detail: { except: this.menu_title },
        }),
      );
    },
    handleCollapseAll(event) {
      // Only collapse if this menu is not the exception
      if (event.detail.except !== this.menu_title) {
        this.is_show = false;
      }
    },

    updateMenuState() {
      if (this.isCurrentMenuActive) {
        this.collapseAllOtherMenus();
        this.is_show = true;
      }
    },
    isActiveRoute(menu) {
      if (!menu) return false;
      if (menu.route && menu.route.name) {
        return this.$route.name === menu.route.name;
      }
      if (menu.route_name) {
        return this.$route.name === menu.route_name;
      }
      // fallback if a plain string is passed
      if (typeof menu === "string") {
        return this.$route.name === menu;
      }
      return false;
    },
  },
};
</script>

<style></style>
