<template>
    <a v-if="canEdit && item.status == 'active'" href="" @click.prevent="updateStatus(item)" class="border-warning">
        <i class="fa fa-eye-slash text-warning"></i>
        Inactive
    </a>
    <a v-if="canEdit && item.status == 'inactive'" href="" @click.prevent="updateStatus(item)" class="border-warning">
        <i class="fa fa-eye text-warning"></i>
        Active
    </a>
</template>
<script>
import { auth_store } from "@/GlobalStore/auth_store";
export default {
    props: {
        item: {
            slug: "",
        }
    },
    inject: ['dataStoreConstructor', 'moduleSetup'],
    computed: {
        canEdit() {
            const slug = this.moduleSetup?.permission_slugs?.edit;
            if (!slug) return true;
            return auth_store().has_permission(slug);
        }
    },
    methods: {
        updateStatus: async function (item) {
            if (!item.slug) {
                window.s_alert('Item slug is missing', 'error');
                return;
            }

            let action = item.status == 'active' ? 'deactive' : 'active';
            let con = await window.s_confirm('Are you sure want to ' + action + ' ?');
            if (con) {
                const store = this.dataStoreConstructor();
                const completeItem = {
                    ...item,
                    slug: item.slug || item.id,
                    status: item.status
                };
                store.set_item(completeItem);
                store.set_only_latest_data(true);
                let response = await store.update_status();
                if (response.data.status === "success") {
                    Object.assign(item, response.data.data);
                    await store.get_all();
                    window.s_alert(response.data?.message);
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
