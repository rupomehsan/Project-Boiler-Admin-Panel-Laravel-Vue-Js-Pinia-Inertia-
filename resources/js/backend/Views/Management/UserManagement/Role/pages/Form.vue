<template>
  <div>
    <form @submit.prevent="submitHandler">
      <!-- Header Card -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="text-capitalize mb-0">
            {{ param_id ? `${setup.edit_page_title}` : `${setup.create_page_title}` }}
          </h5>
          <div>
            <router-link
              v-if="item.slug"
              class="btn btn-outline-info mr-2 btn-sm"
              :to="{ name: `Details${setup.route_prefix}`, params: { id: item.slug } }"
            >{{ setup.details_page_title }}</router-link>
            <router-link
              class="btn btn-outline-warning btn-sm"
              :to="{ name: `All${setup.route_prefix}` }"
            >{{ setup.all_page_title }}</router-link>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label font-weight-bold">Role Name *</label>
              <input
                v-model="form_data.name"
                name="name"
                type="text"
                class="form-control"
                placeholder="e.g., Admin, Editor, Viewer"
                required
              />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label font-weight-bold">Status</label>
              <select v-model="form_data.status" name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <input type="hidden" name="permissions" :value="JSON.stringify(selected_permissions)" />

      <!-- Permissions Card -->
      <div class="card mt-3">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fa fa-lock mr-2"></i>Assign Permissions</h6>
            <div>
              <button type="button" class="btn btn-sm btn-outline-success" @click="selectAll">
                <i class="fa fa-check-square mr-1"></i> Select All
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger ml-2" @click="deselectAll">
                <i class="fa fa-square mr-1"></i> Deselect All
              </button>
            </div>
          </div>
        </div>

        <div class="card-body permissions-table-wrap">
          <div v-if="permissions_loading" class="text-center py-5">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <p class="mt-2">Loading permissions...</p>
          </div>

          <div v-else>
            <div class="permission-table-shell">
              <!-- Head -->
              <div class="permission-table-head">
                <div class="permission-table-col permission-table-col-index">#</div>
                <div class="permission-table-col permission-table-col-module">Modules</div>
                <div class="permission-table-col permission-table-col-select-all">
                  <div class="form-check m-0 d-flex align-items-center">
                    <input
                      id="select-all-permissions"
                      type="checkbox"
                      class="form-check-input m-0"
                      :checked="all_permissions.length > 0 && selected_permissions.length === all_permissions.length"
                      @change="selected_permissions.length === all_permissions.length ? deselectAll() : selectAll()"
                    />
                    <label for="select-all-permissions" class="form-check-label mb-0 ml-2 font-weight-bold">
                      Select All
                    </label>
                  </div>
                </div>
                <div class="permission-table-col permission-table-col-permissions">Specific Permissions</div>
              </div>

              <!-- Rows -->
              <div
                v-for="(category, index) in grouped_permissions"
                :key="index"
                class="permission-table-row"
              >
                <div class="permission-table-col permission-table-col-index">{{ index + 1 }}</div>

                <div class="permission-table-col permission-table-col-module">
                  <div class="module-title">{{ category.name || "Other" }}</div>
                  <div class="module-meta">
                    {{ getSelectedCountInCategory(category.name) }}/{{ category.permissions_count }} selected
                  </div>
                </div>

                <div class="permission-table-col permission-table-col-select-all">
                  <div class="form-check m-0 d-flex align-items-center">
                    <input
                      :id="`category-${index}`"
                      type="checkbox"
                      class="form-check-input m-0"
                      @change="toggleCategory(category.name)"
                      :checked="isCategoryAllSelected(category.name)"
                    />
                  </div>
                </div>

                <div class="permission-table-col permission-table-col-permissions">
                  <div
                    v-for="(subGroup, subKey) in category.subGroups"
                    :key="subKey"
                    class="subgroup-block"
                  >
                    <!-- Sub-group header with toggle — shown only when multiple sub-groups -->
                    <div
                      v-if="subKey !== 'Default' && Object.keys(category.subGroups).length > 1"
                      class="subgroup-header"
                    >
                      <label class="subgroup-toggle" :for="`subgroup-${index}-${subKey}`" :title="`Select all ${subKey}`">
                        <input
                          :id="`subgroup-${index}-${subKey}`"
                          type="checkbox"
                          class="subgroup-checkbox"
                          :checked="isSubGroupAllSelected(category.name, subKey)"
                          @change="toggleSubGroup(category.name, subKey)"
                        />
                        <span class="subgroup-checkbox-box"></span>
                      </label>
                      <span class="subgroup-title">
                        <i class="fa fa-dot-circle-o mr-1"></i>{{ subKey }}
                      </span>
                      <span class="subgroup-count">
                        {{ getSelectedCountInSubGroup(category.name, subKey) }}/{{ subGroup.length }}
                      </span>
                    </div>

                    <div class="permission-chip-grid">
                      <label
                        v-for="permission in subGroup"
                        :key="permission.id"
                        class="permission-chip"
                        :for="`perm-${permission.id}`"
                        :title="`Slug: ${permission.slug}\nRoute: ${permission.route}`"
                      >
                        <input
                          :id="`perm-${permission.id}`"
                          v-model="selected_permissions"
                          type="checkbox"
                          class="permission-chip-input"
                          :value="permission.id"
                        />
                        <span class="permission-chip-box"></span>
                        <span class="permission-chip-text">{{ permission.name }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="grouped_permissions.length === 0" class="text-center py-5">
              <p class="text-muted">No permissions available</p>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <small>
            Selected: <strong>{{ selected_permissions.length }}</strong> permission(s)
          </small>
        </div>
      </div>

      <!-- Submit -->
      <div class="card mt-3">
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5" :disabled="form_loading">
            <i v-if="!form_loading" class="icon-lock"></i>
            <span v-if="!form_loading">Submit</span>
            <span v-else>
              <span class="spinner-border spinner-border-sm mr-2"></span>Processing...
            </span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { store } from "../store";
import setup from "../setup";

export default {
  data() {
    return {
      setup,
      param_id: null,
      form_loading: false,
      permissions_loading: true,
      form_data: { name: "", status: "active" },
      all_permissions: [],
      selected_permissions: [],
    };
  },

  created() {
    this.param_id = this.$route.params.id;
    this.reset_fields();
    this.load_permissions();
  },

  computed: {
    ...mapState(store, { item: "item" }),

    grouped_permissions() {
      const grouped = {};
      this.all_permissions.forEach((permission) => {
        const category = this.getDisplayCategory(permission);
        const subCategory = this.getSubCategory(permission);
        if (!grouped[category]) {
          grouped[category] = { name: category, permissions_count: 0, subGroups: {} };
        }
        if (!grouped[category].subGroups[subCategory]) {
          grouped[category].subGroups[subCategory] = [];
        }
        grouped[category].subGroups[subCategory].push(permission);
        grouped[category].permissions_count++;
      });

      const groups = Object.values(grouped);
      groups.forEach((group) => {
        Object.keys(group.subGroups).forEach((subKey) => {
          group.subGroups[subKey].sort((a, b) =>
            this.comparePermissionsByCategory(group.name, a, b)
          );
        });
      });

      return groups.sort((a, b) => this.compareCategories(a.name, b.name));
    },
  },

  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      set_only_latest_data: "set_only_latest_data",
    }),

    compareCategories(a, b) {
      const order = [
        "Dashboard", "User Management", "Project Management",
        "Product Management", "Blog Management", "Credential Management",
        "Personal Note Management", "Todo List Management", "Contact", "Settings",
      ];
      const ai = order.indexOf(a), bi = order.indexOf(b);
      if (ai !== -1 && bi !== -1) return ai - bi;
      if (ai !== -1) return -1;
      if (bi !== -1) return 1;
      return a.localeCompare(b);
    },

    comparePermissionsByCategory(categoryName, a, b) {
      const actionOrder = ["view", "details", "create", "edit", "delete", "publish", "manage", "import"];
      const getActionIndex = (slug) => {
        const action = String(slug).split("-").pop();
        const i = actionOrder.indexOf(action);
        return i === -1 ? actionOrder.length : i;
      };

      if (categoryName === "Blog Management") {
        const blogOrder = ["blog-category", "blog", "blog-comment", "comment"];
        const getSectionIdx = (slug) => {
          const i = blogOrder.findIndex((s) => String(slug).startsWith(`${s}-`));
          return i === -1 ? blogOrder.length : i;
        };
        const diff = getSectionIdx(a.slug) - getSectionIdx(b.slug);
        if (diff !== 0) return diff;
      }

      const diff = getActionIndex(a.slug) - getActionIndex(b.slug);
      if (diff !== 0) return diff;
      return a.name.localeCompare(b.name);
    },

    getDisplayCategory(permission) {
      const slug = String(permission?.slug || "");
      const category = permission?.category || "Other";
      if (category === "Dashboard") return "Dashboard";
      if (category === "Settings") return "Settings";
      if (category === "Contact Management") return "Contact";
      if (slug.startsWith("user-") || slug.startsWith("role-")) return "User Management";
      if (slug.startsWith("project-")) return "Project Management";
      if (slug.startsWith("product-")) return "Product Management";
      if (slug.startsWith("blog-category-") || slug.startsWith("blog-") || slug.startsWith("blog-comment-") || slug.startsWith("comment-")) return "Blog Management";
      if (slug.startsWith("credential-")) return "Credential Management";
      if (slug.startsWith("note-")) return "Personal Note Management";
      if (slug.startsWith("todo-")) return "Todo List Management";
      return category;
    },

    getSubCategory(permission) {
      const slug = String(permission?.slug || "");
      if (slug.startsWith("user-")) return "User";
      if (slug.startsWith("role-")) return "User Role";
      if (slug.startsWith("blog-category-")) return "Blog Category";
      if (slug.startsWith("blog-comment-") || slug.startsWith("comment-")) return "Blog Comment";
      if (slug.startsWith("blog-")) return "Blog";
      if (slug.startsWith("product-comment-")) return "Product Comments";
      if (slug.startsWith("product-order-")) return "Product Order";
      if (slug.startsWith("product-") || slug.startsWith("digital-product-")) return "Digital Product";
      if (slug.startsWith("project-comment-")) return "Project Comments";
      if (slug.startsWith("project-")) return "Project";
      if (slug.startsWith("credential-")) return "Credential";
      if (slug.startsWith("note-")) return "Personal Note";
      if (slug.startsWith("todo-")) return "Todo List";
      return "Default";
    },

    isCategoryAllSelected(categoryName) {
      const perms = this.all_permissions.filter((p) => this.getDisplayCategory(p) === categoryName);
      return perms.length > 0 && perms.every((p) => this.selected_permissions.includes(p.id));
    },

    getSelectedCountInCategory(categoryName) {
      return this.all_permissions
        .filter((p) => this.getDisplayCategory(p) === categoryName)
        .filter((p) => this.selected_permissions.includes(p.id)).length;
    },

    isSubGroupAllSelected(categoryName, subKey) {
      const perms = this.all_permissions.filter(
        (p) => this.getDisplayCategory(p) === categoryName && this.getSubCategory(p) === subKey
      );
      return perms.length > 0 && perms.every((p) => this.selected_permissions.includes(p.id));
    },

    getSelectedCountInSubGroup(categoryName, subKey) {
      return this.all_permissions
        .filter((p) => this.getDisplayCategory(p) === categoryName && this.getSubCategory(p) === subKey)
        .filter((p) => this.selected_permissions.includes(p.id)).length;
    },

    toggleCategory(categoryName) {
      const perms = this.all_permissions.filter((p) => this.getDisplayCategory(p) === categoryName);
      const allSelected = this.isCategoryAllSelected(categoryName);
      if (allSelected) {
        this.selected_permissions = this.selected_permissions.filter(
          (id) => !perms.some((p) => p.id === id)
        );
      } else {
        perms.forEach((p) => {
          if (!this.selected_permissions.includes(p.id)) this.selected_permissions.push(p.id);
        });
      }
    },

    toggleSubGroup(categoryName, subKey) {
      const perms = this.all_permissions.filter(
        (p) => this.getDisplayCategory(p) === categoryName && this.getSubCategory(p) === subKey
      );
      const allSelected = this.isSubGroupAllSelected(categoryName, subKey);
      if (allSelected) {
        this.selected_permissions = this.selected_permissions.filter(
          (id) => !perms.some((p) => p.id === id)
        );
      } else {
        perms.forEach((p) => {
          if (!this.selected_permissions.includes(p.id)) this.selected_permissions.push(p.id);
        });
      }
    },

    selectAll() {
      this.selected_permissions = this.all_permissions.map((p) => p.id);
    },

    deselectAll() {
      this.selected_permissions = [];
    },

    async load_permissions() {
      try {
        this.permissions_loading = true;
        const response = await axios.get("permissions");
        if (response.data.status === "success") {
          this.all_permissions = response.data.data;
        }
        if (this.param_id) await this.load_role_data();
      } catch (error) {
        window.s_alert("Failed to load permissions", "error");
      } finally {
        this.permissions_loading = false;
      }
    },

    async load_role_data() {
      try {
        await this.details(this.param_id);
        if (this.item) {
          this.form_data.name = this.item.name || "";
          this.form_data.status = this.item.status || "active";
          const response = await axios.get(`permissions/role/${this.param_id}`);
          if (response.data.status === "success") {
            this.selected_permissions = response.data.data.map((id) => Number(id));
          }
        }
      } catch (error) {
        window.s_alert("Failed to load role data", "error");
      }
    },

    reset_fields() {
      this.form_data = { name: "", status: "active" };
      this.selected_permissions = [];
    },

    async submitHandler(event) {
      try {
        this.form_loading = true;
        this.set_only_latest_data(true);
        let response;
        if (this.param_id) {
          response = await this.update(event);
          if ([200, 201].includes(response.status)) {
            window.s_alert("Role updated successfully");
            this.$router.push({ name: `Details${this.setup.route_prefix}`, params: { id: this.item.slug } });
          }
        } else {
          response = await this.create(event);
          if ([200, 201].includes(response.status)) {
            window.s_alert("Role created successfully");
            this.$router.push({ name: `All${this.setup.route_prefix}` });
          }
        }
      } catch (error) {
        const msg = error.response?.data?.message || "An error occurred while saving";
        window.s_alert(msg, "error");
      } finally {
        this.form_loading = false;
      }
    },
  },
};
</script>

<style scoped>
.permissions-table-wrap {
  max-height: 70vh;
  overflow: auto;
}

.permission-table-shell {
  min-width: 980px;
}

.permission-table-head,
.permission-table-row {
  display: grid;
  grid-template-columns: 60px 260px 170px 1fr;
  align-items: stretch;
}

.permission-table-head {
  background: var(--table-header-bg);
  border: 1px solid var(--border-color);
  border-bottom: 0;
  font-weight: 700;
  color: var(--text-primary);
}

.permission-table-row {
  border: 1px solid var(--border-color);
  border-top: 0;
  background: var(--bg-card);
}

.permission-table-col {
  padding: 14px 16px;
  border-right: 1px solid var(--border-light);
}

.permission-table-col:last-child {
  border-right: 0;
}

.permission-table-col-index {
  display: flex;
  align-items: center;
  justify-content: center;
}

.permission-table-col-select-all {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding-left: 20px;
}

.permission-table-col-module {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.module-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--text-primary);
}

.module-meta {
  font-size: 12px;
  margin-top: 4px;
  color: var(--text-tertiary);
}

/* Sub-group block */
.subgroup-block {
  margin-bottom: 14px;
}

.subgroup-block:last-child {
  margin-bottom: 0;
}

.subgroup-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.subgroup-toggle {
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  margin: 0;
}

.subgroup-checkbox {
  display: none;
}

.subgroup-checkbox-box {
  width: 15px;
  height: 15px;
  border-radius: 3px;
  border: 1px solid var(--border-dark);
  background: var(--bg-input);
  position: relative;
  flex: 0 0 auto;
  transition: all 0.15s ease;
}

.subgroup-checkbox:checked + .subgroup-checkbox-box {
  border-color: var(--primary-color);
  background: var(--primary-color);
}

.subgroup-checkbox:checked + .subgroup-checkbox-box::after {
  content: "";
  position: absolute;
  left: 3px;
  top: 1px;
  width: 5px;
  height: 8px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.subgroup-title {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-secondary);
}

.subgroup-count {
  font-size: 11px;
  color: var(--text-tertiary);
  background: var(--bg-hover);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 1px 7px;
}

/* Permission chips */
.permission-chip-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 14px;
}

.permission-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin: 0;
  padding: 5px 10px;
  border-radius: 10px;
  background: var(--bg-main);
  border: 1px solid var(--border-color);
  cursor: pointer;
  transition: all 0.15s ease;
}

.permission-chip:hover {
  border-color: var(--border-dark);
  background: var(--bg-hover);
}

.permission-chip-input {
  display: none;
}

.permission-chip-box {
  width: 16px;
  height: 16px;
  border-radius: 4px;
  border: 1px solid var(--text-light);
  background: var(--bg-input);
  position: relative;
  flex: 0 0 auto;
  transition: all 0.15s ease;
}

.permission-chip-input:checked + .permission-chip-box {
  border-color: var(--primary-color);
  background: var(--primary-color);
}

.permission-chip-input:checked + .permission-chip-box::after {
  content: "";
  position: absolute;
  left: 4px;
  top: 1px;
  width: 5px;
  height: 9px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.permission-chip-text {
  font-size: 13px;
  color: var(--text-primary);
  white-space: nowrap;
}

/* Checked chip state */
.permission-chip:has(.permission-chip-input:checked) {
  background: color-mix(in srgb, var(--primary-color) 12%, var(--bg-card));
  border-color: var(--primary-color);
}

/* Scrollbar */
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: var(--bg-main); }
::-webkit-scrollbar-thumb { background: var(--border-dark); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: var(--text-tertiary); }

@media (max-width: 1200px) {
  .permission-table-shell { min-width: 860px; }
  .permission-table-head,
  .permission-table-row { grid-template-columns: 56px 220px 150px 1fr; }
}
</style>
