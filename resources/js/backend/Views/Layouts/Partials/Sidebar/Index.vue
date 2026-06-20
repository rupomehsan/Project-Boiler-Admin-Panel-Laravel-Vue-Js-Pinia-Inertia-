<template>
  <div id="sidebar-wrapper">
    <!-- Brand logo -->
    <div class="brand-logo">
      <router-link :to="{ name: `adminDashboard` }" class="d-flex align-items-center">
        <img :src="`${get_setting_value('image') ?? 'avatar.png'}`" class="logo-icon" alt="logo icon" />
        <h5 class="logo-text">Control Panel</h5>
      </router-link>
      <div class="close-btn">
        <i class="zmdi zmdi-close" @click="toggle_menu"></i>
      </div>
    </div>

    <!-- User profile card -->
    <div class="sb-user-card">
      <div class="sb-user-card-inner">
        <div class="sb-avatar-wrap">
          <img class="sb-user-avatar" :src="`${auth_info.image ?? 'avatar.png'}`" alt="avatar" />
          <span class="sb-online-dot"></span>
        </div>
        <div class="sb-user-details">
          <p class="sb-user-name">Mr. {{ auth_info.name }}</p>
          <span class="sb-user-role">
            <i class="zmdi zmdi-shield-check"></i>
            {{ auth_info.role_name || 'Admin' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Search bar -->
    <div class="sidebar-search-wrap">
      <i class="zmdi zmdi-search sidebar-search-icon"></i>
      <input v-model="searchQuery" class="sidebar-search-input" type="text" placeholder="Search menu..." autocomplete="off" />
      <button v-if="searchQuery" class="sidebar-search-clear" @click="searchQuery = ''">
        <i class="zmdi zmdi-close"></i>
      </button>
    </div>

    <!-- Search results (flat list) -->
    <ul v-if="searchQuery.trim()" class="metismenu" id="menu">
      <li v-if="filteredItems.length === 0" class="sidebar-no-results"><i class="zmdi zmdi-alert-circle-o"></i> No results found</li>
      <li v-for="item in filteredItems" :key="item.route_name" :class="{ active: $route.name === item.route_name }">
        <router-link :to="item.route ? item.route : { name: item.route_name }">
          <div class="parent-icon"><i :class="item.icon || 'zmdi zmdi-dot-circle-alt'"></i></div>
          <div class="menu-title">
            {{ item.title }}
            <span class="sidebar-result-group">{{ item.group }}</span>
          </div>
        </router-link>
      </li>
    </ul>

    <!-- Normal menu (original structure) -->
    <ul v-else class="metismenu" id="menu">

      <li class="menu-section-label">Main</li>

      <side-bar-single-menu
        v-if="has_permission('dashboard-view')"
        :icon="`zmdi zmdi-view-dashboard`"
        :menu_title="`Dashboard`"
        :route_name="`adminDashboard`"
      />

      <li class="menu-section-label">Management</li>

      <side-bar-drop-down-menus
        v-if="has_permission('user-view') || has_permission('role-view')"
        :icon="`zmdi zmdi-accounts`"
        :menu_title="`User Management`"
        :menus="[
          { route_name: `AllUser`, title: `Users`, icon: `zmdi zmdi-account`, permission: 'user-view' },
          { route_name: `AllRole`, title: `User Roles`, icon: `zmdi zmdi-shield-check`, permission: 'role-view' },
        ]"
      />

      <side-bar-drop-down-menus
        :icon="`zmdi zmdi-edit`"
        :menu_title="`Blog Management`"
        :menus="[
          { route_name: `AllBlogCategory`, title: `Categories`, icon: `zmdi zmdi-folder` },
          { route_name: `AllBlogTag`, title: `Tags`, icon: `zmdi zmdi-label` },
          { route_name: `AllBlogWriter`, title: `Writers`, icon: `zmdi zmdi-account-o` },
          { route_name: `AllBlog`, title: `Posts`, icon: `zmdi zmdi-collection-text` },
        ]"
      />

    </ul>
  </div>
</template>

<script>
import { auth_store } from "../../../../GlobalStore/auth_store";
import { site_settings_store } from "../../../../GlobalStore/site_settings_store";
import { mapState, mapActions } from "pinia";
import SideBarDropDownMenus from "./SideBarDropDownMenus.vue";
import SideBarSingleMenu from "./SideBarSingleMenu.vue";

export default {
  components: { SideBarDropDownMenus, SideBarSingleMenu },

  data() {
    return {
      searchQuery: "",

      // Mirror of the sidebar menus — used only for search filtering
      allMenuItems: [
        { title: "Dashboard", route_name: "adminDashboard", icon: "zmdi zmdi-view-dashboard", group: "Main", permission: "dashboard-view" },
        { title: "Users", route_name: "AllUser", icon: "zmdi zmdi-account", group: "User Management", permission: "user-view" },
        { title: "User Roles", route_name: "AllRole", icon: "zmdi zmdi-shield-check", group: "User Management", permission: "role-view" },
        { title: "Categories", route_name: "AllBlogCategory", icon: "zmdi zmdi-folder", group: "Blog Management" },
        { title: "Tags", route_name: "AllBlogTag", icon: "zmdi zmdi-label", group: "Blog Management" },
        { title: "Writers", route_name: "AllBlogWriter", icon: "zmdi zmdi-account-o", group: "Blog Management" },
        { title: "Posts", route_name: "AllBlog", icon: "zmdi zmdi-collection-text", group: "Blog Management" },
      ],
    };
  },

  computed: {
    ...mapState(auth_store, {
      auth_info: "auth_info",
      has_permission: "has_permission",
    }),

    filteredItems() {
      const q = this.searchQuery.trim().toLowerCase();
      if (!q) return [];
      return this.allMenuItems.filter((item) => {
        const matchesText = item.title.toLowerCase().includes(q) || item.group.toLowerCase().includes(q);
        const hasAccess = !item.permission || this.has_permission(item.permission);
        return matchesText && hasAccess;
      });
    },
  },

  methods: {
    ...mapActions(site_settings_store, { get_setting_value: "get_setting_value" }),

    toggle_menu() {
      document.getElementById("wrapper").classList.toggle("toggled");
    },
    hide_menu() {
      document.getElementById("wrapper").classList.add("toggled");
    },
  },

  watch: {
    $route(to) {
      if (to.name === "TaskBoard") this.hide_menu();
    },
  },
};
</script>

<style scoped>
/* ── User profile card ───────────────────────────────────────────────── */
.sb-user-card {
  padding: 10px 12px 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
}
.sb-user-card-inner {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 10px 12px;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.09);
  border-radius: 10px;
}
.sb-avatar-wrap {
  position: relative;
  flex-shrink: 0;
}
.sb-user-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.18);
  display: block;
}
.sb-online-dot {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #22c55e;
  border: 2px solid rgba(0, 0, 0, 0.4);
}
.sb-user-details {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.sb-user-name {
  margin: 0 0 2px;
  font-size: 0.84rem;
  font-weight: 600;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.sb-user-role {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.71rem;
  color: rgba(255, 255, 255, 0.45);
}
.sb-user-role i {
  font-size: 0.75rem;
  color: var(--primary-color, #3b82f6);
}

/* ── Search ──────────────────────────────────────────────────────────── */
.sidebar-search-wrap {
  position: relative;
  margin: 10px 12px 6px;
}
.sidebar-search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(255, 255, 255, 0.35);
  font-size: 0.9rem;
  pointer-events: none;
}
.sidebar-search-input {
  width: 100%;
  padding: 7px 30px 7px 32px;
  background: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  color: #fff;
  font-size: 0.8rem;
  outline: none;
  transition: background 0.15s, border-color 0.15s;
}
.sidebar-search-input::placeholder { color: rgba(255, 255, 255, 0.3); }
.sidebar-search-input:focus {
  background: rgba(255, 255, 255, 0.11);
  border-color: rgba(255, 255, 255, 0.22);
}
.sidebar-search-clear {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: none;
  color: rgba(255, 255, 255, 0.4);
  cursor: pointer;
  padding: 0;
  font-size: 0.8rem;
  line-height: 1;
}
.sidebar-search-clear:hover {
  color: #fff;
}

/* ── Search result group badge ───────────────────────────────────────── */
.sidebar-result-group {
  display: inline-block;
  margin-left: 6px;
  font-size: 0.65rem;
  padding: 1px 6px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  color: rgba(255, 255, 255, 0.45);
  vertical-align: middle;
  font-weight: 400;
}

/* ── No results ──────────────────────────────────────────────────────── */
.sidebar-no-results {
  padding: 20px 16px;
  text-align: center;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.3);
  list-style: none;
}
</style>

<style>
/* ── Section labels ──────────────────────────────────────────────────── */
#menu .menu-section-label {
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.28);
  padding: 16px 18px 4px;
  list-style: none;
  pointer-events: none;
  user-select: none;
}

/* ── Metismenu base reset ────────────────────────────────────────────── */
#menu, #menu ul {
  list-style: none;
  padding: 0 8px;
  margin: 0;
}
#menu li { margin-bottom: 4px; }

/* ── Border on every top-level menu item ─────────────────────────────── */
#menu > li:not(.menu-section-label) {
  border: 1px solid rgba(255, 255, 255, 0.25) !important;
  border-radius: 8px !important;
  overflow: hidden;
  transition: border-color 0.15s;
  margin-bottom: 4px;
}
#menu > li:not(.menu-section-label):hover {
  border-color: rgba(255, 255, 255, 0.45) !important;
}
#menu > li.active:not(.menu-section-label),
#menu > li.mm-active:not(.menu-section-label) {
  border-color: rgba(59, 130, 246, 0.8) !important;
}

/* ── Top-level menu item (parent-icon + menu-title) ─────────────────── */
#menu > li > a,
#menu .has-arrow {
  display: flex !important;
  align-items: center;
  gap: 10px;
  padding: 10px 12px !important;
  border-radius: 8px !important;
  font-size: 0.83rem;
  font-weight: 500;
  color: rgba(255,255,255,0.62) !important;
  text-decoration: none;
  transition: background 0.15s, color 0.15s !important;
  border: none !important;
  margin: 0 !important;
  background: transparent !important;
}
#menu > li > a:hover,
#menu .has-arrow:hover {
  background: rgba(255,255,255,0.07) !important;
  color: #fff !important;
}

/* ── Active single item ──────────────────────────────────────────────── */
#menu > li.active > a,
#menu > li > a.active,
#menu > li > a.router-link-active {
  background: var(--primary-color, #3b82f6) !important;
  color: #fff !important;
  border-radius: 8px !important;
  margin: 0 !important;
}

/* ── Active dropdown group header ────────────────────────────────────── */
#menu li.mm-active > a.has-arrow {
  background: rgba(59,130,246,0.12) !important;
  color: #fff !important;
  border-left: 3px solid var(--primary-color, #3b82f6) !important;
  border-radius: 8px !important;
  padding-left: 9px !important;
}

/* ── Icon wrapper ────────────────────────────────────────────────────── */
#menu .parent-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 6px;
  font-size: 1rem;
  flex-shrink: 0;
}

/* ── Arrow chevron (mm-arrow auto-added by metismenu) ────────────────── */
#menu a.has-arrow::after {
  margin-left: auto !important;
  border-top-color: rgba(255,255,255,0.35) !important;
  transition: transform 0.2s !important;
}
#menu li.mm-active > a.has-arrow::after {
  border-top-color: rgba(255,255,255,0.7) !important;
}

/* ── Child submenu ───────────────────────────────────────────────────── */
#menu .mm-collapse {
  border: none !important;
  background: transparent !important;
}
#menu .mm-collapse ul {
  list-style: none;
  padding: 4px 0 6px 0 !important;
  margin: 0 0 0 20px !important;
  /* vertical trunk — same color as parent li border */
  border-left: 1px solid rgba(255, 255, 255, 0.22) !important;
  position: relative;
}
/* trunk matches active parent border */
#menu li.mm-active > ul.mm-show,
#menu li.mm-active > ul.mm-collapse {
  border-left-color: rgba(59, 130, 246, 0.45) !important;
}

/* ── Tree connector lines ─────────────────────────────────────────────── */
#menu .mm-collapse ul > li {
  position: relative;
  padding-left: 18px;
  margin-bottom: 1px;
}
/* horizontal branch — same color as parent li border */
#menu .mm-collapse ul > li::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  width: 14px;
  height: 1px;
  background: rgba(255, 255, 255, 0.22);
  transition: background 0.15s;
}
/* junction dot — same color as parent li border */
#menu .mm-collapse ul > li::after {
  content: '';
  position: absolute;
  left: 9px;
  top: 50%;
  transform: translateY(-50%);
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.22);
  border: 1px solid rgba(255, 255, 255, 0.22);
  transition: background 0.15s, border-color 0.15s;
}
/* active parent → branch + dot match active border rgba(59,130,246,0.45) */
#menu li.mm-active .mm-collapse ul > li::before {
  background: rgba(59, 130, 246, 0.45);
}
#menu li.mm-active .mm-collapse ul > li::after {
  background: rgba(59, 130, 246, 0.45);
  border-color: rgba(59, 130, 246, 0.45);
}
/* active child item → solid filled dot */
#menu .mm-collapse ul > li.active::after,
#menu .mm-collapse ul > li:has(a.router-link-active)::after {
  background: var(--primary-color, #3b82f6);
  border-color: var(--primary-color, #3b82f6);
}

/* ── Child links ─────────────────────────────────────────────────────── */
#menu .mm-collapse ul li > a {
  display: flex !important;
  align-items: center;
  gap: 7px;
  padding: 7px 8px !important;
  border-radius: 6px !important;
  font-size: 0.79rem;
  color: rgba(255,255,255,0.5) !important;
  text-decoration: none;
  transition: background 0.12s, color 0.12s !important;
  background: transparent !important;
  border: none !important;
  margin: 0 !important;
}
#menu .mm-collapse ul li > a:hover {
  background: rgba(255,255,255,0.06) !important;
  color: rgba(255,255,255,0.85) !important;
}
#menu .mm-collapse ul li.active > a,
#menu .mm-collapse ul li > a.active,
#menu .mm-collapse ul li > a.router-link-active {
  background: rgba(59,130,246,0.18) !important;
  color: #fff !important;
  border-radius: 6px !important;
  margin: 0 !important;
}

/* ── Child icon ──────────────────────────────────────────────────────── */
#menu .mm-collapse ul li > a i {
  font-size: 0.8rem;
  width: 15px;
  text-align: center;
  opacity: 0.55;
  flex-shrink: 0;
}
#menu .mm-collapse ul li.active > a i,
#menu .mm-collapse ul li > a.router-link-active i {
  opacity: 1;
  color: var(--primary-color, #3b82f6);
}
</style>
