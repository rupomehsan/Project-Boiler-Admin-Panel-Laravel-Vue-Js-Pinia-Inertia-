<template>
    <div v-if="(canEdit || canDelete) && selected && selected.length > 0">
        <select @change="bulkActions" class="form-select bulk-action-select">
            <option disabled selected>Select action</option>
            <template v-if="canEdit">
                <option value="inactive">Inactive</option>
                <option value="active">Active</option>
            </template>
            <template v-if="canDelete">
                <option value="soft_delete">Soft Delete</option>
                <option value="restore">Restore</option>
                <option value="destroy">Destroy</option>
            </template>
        </select>
    </div>
</template>
<script>
import { auth_store } from "@/GlobalStore/auth_store";
export default {
    props: {
        item: { slug: "" }
    },
    inject: ['dataStoreConstructor', 'moduleSetup'],
    computed: {
        canEdit() {
            const slug = this.moduleSetup?.permission_slugs?.edit;
            if (!slug) return true;
            return auth_store().has_permission(slug);
        },
        canDelete() {
            const slug = this.moduleSetup?.permission_slugs?.delete;
            if (!slug) return true;
            return auth_store().has_permission(slug);
        },
    },
    data() {
        return {
            selected: [],
            store: null
        };
    },
    methods: {
        bulkActions: async function (event) {
            let action = event.target.value;
            let con = await window.s_confirm('Are you sure want to ' + action + ' items ?');
            if (con) {
                const store = this.dataStoreConstructor();
                let selected_ids = this.selected.map((item) => item.id || item.slug);
                store.set_only_latest_data(true);
                let response = await store.bulk_action(action, selected_ids);
                if (response?.data?.status === "success") {
                    await store.get_all();
                    const selectAllCheckbox = document.querySelector('.select_all_checkbox');
                    if (selectAllCheckbox) selectAllCheckbox.checked = false;
                    store.clear_selected();
                    store.set_only_latest_data(false);
                    window.s_alert('You have ' + action + ' items');
                } else {
                    window.s_warning(response?.data?.message || 'Action failed');
                }
                event.target.value = '';
            } else {
                event.target.value = '';
            }
        },
    },
    watch: {
        selected: {
            handler(newVal) { this.selected = newVal || []; },
            deep: true
        }
    },
    mounted() {
        if (this.dataStoreConstructor) {
            this.store = this.dataStoreConstructor();
            this.selected = this.store.selected || [];
            this.store.$subscribe((mutation, state) => {
                if (state.selected !== undefined) this.selected = state.selected;
            });
        }
    },
}
</script>
<style>
.bulk-action-select {
    width: 150px;
    padding: 5px;
    margin-left: 10px;
}
</style>
