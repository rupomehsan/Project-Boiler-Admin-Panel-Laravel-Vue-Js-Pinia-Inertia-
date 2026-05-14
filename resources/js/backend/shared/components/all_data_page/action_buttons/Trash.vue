<template lang="">
    <div v-if="canDelete" class="">
        <a href="" @click.prevent="change_status(`trased`)"
            class="btn action_btn btn-sm btn-danger d-flex align-items-center mx-1">
            <i class="fa fa-trash mr-2"></i> Trased
            ({{ trashed_data_count }})
        </a>
    </div>
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

        const canDelete = computed(() => {
            const slug = moduleSetup?.permission_slugs?.delete;
            if (!slug) return true;
            return authStore.has_permission(slug);
        });

        return {
            canDelete,
            trashed_data_count: computed(() => store.trashed_data_count),
            change_status(status = 'active') {
                store.set_only_latest_data(status === 'trashed');
                store.set_status(status);
                store.set_page(1);
                store.get_all();
                store.set_only_latest_data(false);
            }
        };
    }
}
</script>
<style lang=""></style>
