<template>
  <div>
    <form @submit.prevent="submitHandler">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <h5 class="text-capitalize">
            {{
              param_id
                ? `${setup.edit_page_title}`
                : `${setup.create_page_title}`
            }}
          </h5>
          <div>
            <router-link
              v-if="item.slug"
              class="btn btn-outline-info mr-2 btn-sm"
              :to="{
                name: `Details${setup.route_prefix}`,
                params: { id: item.slug },
              }"
            >
              {{ setup.details_page_title }}
            </router-link>
            <router-link
              class="btn btn-outline-warning btn-sm"
              :to="{ name: `All${setup.route_prefix}` }"
            >
              {{ setup.all_page_title }}
            </router-link>
          </div>
        </div>
        <div class="card-body card_body_fixed_height">
          <div class="row">
            <template
              v-for="(form_field, index) in form_fields"
              v-bind:key="index"
            >
              <common-input
                :label="form_field.label"
                :type="form_field.type"
                :name="form_field.name"
                :placeholder="form_field.placeholder"
                :rows="form_field.rows"
                :multiple="form_field.multiple"
                :value="form_field.value"
                :sub_fields="form_field.sub_fields"
                :data_list="form_field.data_list"
                :is_visible="form_field.is_visible"
                :class="form_field.class"
              />
            </template>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-light btn-square px-5">
            <i class="icon-lock"></i>
            Submit
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
import form_fields from "../setup/form_fields";

export default {
  data: () => ({
    setup,
    form_fields,
    param_id: null,
  }),
  created: function () {
    this.param_id = this.$route.params.id ?? null;
    this.reset_fields();
  },
  mounted: async function () {
    // Load FK select options first, then populate edit values.
    // This order guarantees data_list exists when value is set,
    // so the select component can resolve the label immediately.
    await this.loadSelectOptions();
    if (this.param_id) {
      await this.set_fields(this.param_id);
    }
  },
  methods: {
    ...mapActions(store, {
      create: "create",
      update: "update",
      details: "details",
      get_all: "get_all",
      set_only_latest_data: "set_only_latest_data",
    }),
    loadSelectOptions: async function () {
      for (const field of this.form_fields) {
        if (field.type === "select" && field.api_end_point) {
          try {
            const res = await axios.get(
              `${field.api_end_point}?get_all=1&status=active`
            );
            const items = res.data?.data ?? [];
            field.data_list = items.map((item) => ({
              label: item.name || item.title || item.label || String(item.id),
              value: item.id,
            }));
          } catch (e) {
            console.warn(`Failed to load options for [${field.name}]`, e);
          }
        }
      }
    },
    reset_fields: function () {
      this.form_fields.forEach((item) => {
        item.value = "";
      });
    },
    set_fields: async function (id) {
      this.param_id = id;
      await this.details(id);
      if (this.item) {
        this.form_fields.forEach((field, index) => {
          Object.entries(this.item).forEach(([key, val]) => {
            if (field.name == key) {
              // FK select fields: API returns the relation object instead of the raw ID.
              // Extract .id so the select component can match it against data_list values.
              if (
                field.type === "select" &&
                field.api_end_point &&
                typeof val === "object" &&
                val !== null &&
                val.id !== undefined
              ) {
                this.form_fields[index].value = val.id;
              } else {
                this.form_fields[index].value = val;
              }
            }

            if (field.name == "description" && key == "description") {
              $("#description").summernote("code", val);
            }
          });
        });
      }
    },

    syncEditors: function () {
      // Flush Summernote content into the hidden textarea for every editor field
      // so new FormData(form) captures the typed content at submit time.
      this.form_fields.forEach((field) => {
        if (field.type === "textarea" || field.type === "editor") {
          try {
            const content = $(`#${field.name}`).summernote("code");
            const el = document.getElementById(field.name);
            if (el) el.value = content;
          } catch (_) {}
        }
      });
    },
    submitHandler: async function ($event) {
      this.set_only_latest_data(true);
      this.syncEditors();
      if (this.param_id) {
        let response = await this.update($event);
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data successfully updated");
          this.$router.push({ name: `Details${this.setup.route_prefix}` });
        }
      } else {
        let response = await this.create($event);
        if ([200, 201].includes(response.status)) {
          window.s_alert("Data Successfully Created");
          this.$router.push({ name: `All${this.setup.route_prefix}` });
        }
      }
    },
  },

  computed: {
    ...mapState(store, {
      item: "item",
    }),
  },
};
</script>

<style scoped></style>
