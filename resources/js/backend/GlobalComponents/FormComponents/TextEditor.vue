<template>
  <div>
    <!-- Using <textarea> as the base so Summernote keeps it in sync for FormData -->
    <textarea :id="name" :name="name"></textarea>
  </div>
</template>

<script>
export default {
  props: {
    name: { required: true, type: String },
    value: { type: String, default: null },
    rows: { type: [String, Number], default: null },
  },

  data: () => ({ ready: false }),

  mounted() {
    this.initSummerNote();
  },

  beforeUnmount() {
    try {
      const el = $(`#${this.name}`);
      if (el.length && el.summernote) el.summernote('destroy');
    } catch (_) {}
  },

  watch: {
    value(newVal) {
      if (newVal !== null && newVal !== undefined && this.ready) {
        $(`#${this.name}`).summernote('code', newVal);
      }
    },
  },

  methods: {
    initSummerNote() {
      const height = this.rows ? Number(this.rows) * 30 : 216;
      const fieldName = this.name;
      setTimeout(() => {
        $(`#${fieldName}`).summernote({
          height,
          tabsize: 2,
          callbacks: {
            // Sync editor content to the hidden textarea on every change
            // so new FormData(form) always picks up the latest content.
            onChange: (contents) => {
              const el = document.getElementById(fieldName);
              if (el) el.value = contents;
            },
          },
        });
        this.ready = true;
        if (this.value) {
          $(`#${fieldName}`).summernote('code', this.value);
        }
        setTimeout(() => this.setupTooltips(), 500);
      }, 1000);
    },

    setupTooltips() {
      const editorContainer = document.querySelector(`#${this.name}`)?.parentElement;
      if (!editorContainer) return;
      this.summerNoteTooltip('Style', 'dropdown-style', editorContainer);
      this.summerNoteTooltip('Font Family', 'dropdown-fontname', editorContainer);
      this.summerNoteTooltip('More Color', 'note-color', editorContainer);
      this.summerNoteTooltip('Paragraph', 'note-color', editorContainer);
      this.summerNoteTooltip('Table', 'note-table', editorContainer);
    },

    summerNoteTooltip(style, classname, container = document) {
      let target = container.querySelector(`[data-bs-original-title="${style}"]`);
      let targetClass = container.querySelector(`.${classname}`);
      if (target && targetClass) {
        target.addEventListener('click', (event) => {
          event.stopPropagation();
          targetClass.classList.toggle('show');
          if (classname === 'note-color' || classname === 'note-table') {
            const next = target.nextElementSibling;
            if (next) next.classList.toggle('show');
          }
        });
      }
    },
  },
};
</script>

<style>
.popover-content.note-children-container {
  background: gray;
}
</style>
