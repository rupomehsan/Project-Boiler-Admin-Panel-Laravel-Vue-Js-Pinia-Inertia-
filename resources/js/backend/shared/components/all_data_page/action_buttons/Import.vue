<template>
    <a v-if="canImport" href="" @click.prevent="set_import_csv_modal(true)"
        class="btn action_btn btn-sm btn-secondary d-flex align-items-center ">
        <i class="fa fa-download mr-1"></i> Import
    </a>
</template>
<script>
import { inject, computed } from 'vue';
import { auth_store } from "@/GlobalStore/auth_store";

export default {
    setup() {
        const dataStoreConstructor = inject('dataStoreConstructor');
        const moduleSetup = inject('moduleSetup');
        const store = dataStoreConstructor();
        const authStore = auth_store();

        const canImport = computed(() => {
            const slug = moduleSetup?.permission_slugs?.import;
            if (!slug) return true;
            return authStore.has_permission(slug);
        });

        return {
            canImport,
            set_import_csv_modal(value) {
                store.set_import_csv_modal(value);
            }
        };
    }
}
</script>
<style></style>
