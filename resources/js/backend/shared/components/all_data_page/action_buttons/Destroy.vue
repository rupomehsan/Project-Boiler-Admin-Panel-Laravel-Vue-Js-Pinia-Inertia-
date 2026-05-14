<template lang="">
    <a v-if="canDelete" href="/destroy" @click.prevent="destroy_data" class="border-danger">
        <i class="fa fa-trash text-danger"></i>
        Destroy
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
        canDelete() {
            const slug = this.moduleSetup?.permission_slugs?.delete;
            if (!slug) return true;
            return auth_store().has_permission(slug);
        }
    },
    methods: {
        destroy_data: async function(){
            let con = await window.s_confirm('Permanently delete');
            if(con){
                const store = this.dataStoreConstructor();
                store.set_item(this.item);
                store.set_only_latest_data(true);

                let res = await store.destroy();
                await store.get_all();
                if(res.data.status == "success"){
                    window.s_alert('Permanently deleted.');
                }

                store.set_only_latest_data(false);
            }
        },
    },
}
</script>
<style lang=""></style>
