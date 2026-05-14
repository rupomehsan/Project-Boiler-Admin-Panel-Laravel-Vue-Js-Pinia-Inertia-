<template>
    <a
        v-if="canDelete && !item.deleted_at"
        @click.prevent="softDelete(item)"
        href=""
        class="border-danger"
    >
        <i class="fa fa-ban text-warning"></i>
        Soft Delete
    </a>

    <a
        v-if="canDelete && item.deleted_at"
        @click.prevent="restore_data(item)"
        href=""
        class="border-danger"
    >
        <i class="fa fa-ban text-warning"></i>
        Restore data
    </a>
</template>
<script>
import { auth_store } from "@/GlobalStore/auth_store";
export default {
    props: {
        item: {
            slug: "",
        },
    },
    inject: ['dataStoreConstructor', 'moduleSetup'],
    computed: {
        canDelete() {
            const slug = this.moduleSetup?.permission_slugs?.delete;
            if (!slug) return true;
            return auth_store().has_permission(slug);
        }
    },
    methods: {
        softDelete: async function (item) {
            let con = await window.s_confirm("Are you sure want to soft delete ?");
            if (con) {
                const store = this.dataStoreConstructor();
                store.set_item(item);
                store.set_only_latest_data(true);

                let response = await store.soft_delete();
                if (response.data.status === "success") {
                    if (response.data.data) {
                        Object.assign(this.item, response.data.data);
                    }
                    await store.get_all();
                    window.s_alert(response.data?.message);
                    store.set_only_latest_data(true);
                } else {
                    window.s_warning(response.data?.message);
                }
            }
        },
        restore_data: async function (item) {
            let con = await window.s_confirm("Restore");
            if (con) {
                const store = this.dataStoreConstructor();
                store.set_item(item);
                store.set_only_latest_data(true);
                let response = await store.restore();
                if (response.data.status === "success") {
                    if (response.data.data) {
                        Object.assign(this.item, response.data.data);
                    }
                    await store.get_all();
                    window.s_alert("Permanently deleted.");
                    store.set_only_latest_data(true);
                } else {
                    window.s_warning(response.data?.message);
                }
            }
        },
    },
}
</script>
<style lang=""></style>
