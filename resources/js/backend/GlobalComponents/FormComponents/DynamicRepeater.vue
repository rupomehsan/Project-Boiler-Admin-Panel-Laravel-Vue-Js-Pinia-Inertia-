<template>
  <div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2 dr-header">
      <h6 class="m-0 dr-title">{{ label }}</h6>
      <button class="btn btn-sm btn-outline-success" @click.prevent="addRow">
        <i class="fa fa-plus mr-1"></i> Add row
      </button>
    </div>

    <!-- Rows -->
    <div
      v-for="(row, rowIdx) in rows"
      :key="rowIdx"
      class="row align-items-end mb-2 dr-row"
    >
      <div class="col" v-for="field in sub_fields" :key="field">
        <div class="form-group mb-1">
          <label class="dr-field-label">{{ titleCase(field) }}</label>
          <input
            class="form-control form-control-square"
            type="text"
            :name="`${name}[${rowIdx}][${field}]`"
            v-model="row[field]"
            :placeholder="titleCase(field)"
          />
        </div>
      </div>
      <div class="col-auto pb-1">
        <button
          class="btn btn-sm btn-outline-danger"
          @click.prevent="deleteRow(rowIdx)"
          :disabled="rows.length === 1"
          title="Remove row"
        >
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DynamicRepeater',

  props: {
    name:       { type: String,         required: true },
    label:      { type: String,         default: '' },
    value:      { type: [String, Array], default: null },
    sub_fields: { type: Array,          default: () => [] },
  },

  data() {
    return { rows: [] };
  },

  created() {
    this.rows = [this.emptyRow()];
  },

  watch: {
    value: {
      immediate: true,
      handler(val) {
        if (!val) { this.rows = [this.emptyRow()]; return; }
        try {
          const parsed = Array.isArray(val) ? val : JSON.parse(val);
          if (Array.isArray(parsed) && parsed.length > 0) {
            this.rows = parsed.map(item => {
              const row = this.emptyRow();
              this.sub_fields.forEach(f => { row[f] = item[f] ?? ''; });
              return row;
            });
            return;
          }
        } catch (_) {}
        this.rows = [this.emptyRow()];
      },
    },
  },

  methods: {
    emptyRow() {
      const row = {};
      this.sub_fields.forEach(f => { row[f] = ''; });
      return row;
    },
    addRow() {
      this.rows.push(this.emptyRow());
    },
    deleteRow(idx) {
      if (this.rows.length > 1) this.rows.splice(idx, 1);
    },
    titleCase(str) {
      return String(str).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    },
  },
};
</script>

<style scoped>
.dr-header {
  padding: 8px 12px;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 5px;
  margin-bottom: 10px;
}
.dr-title {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-secondary, #6c757d);
}
.dr-field-label {
  font-size: 0.78rem;
  color: var(--text-secondary, #6c757d);
  margin-bottom: 3px;
}
.dr-row {
  padding: 6px 4px;
  border-radius: 4px;
}
.dr-row:nth-child(odd) {
  background: rgba(255,255,255,0.03);
}
</style>
