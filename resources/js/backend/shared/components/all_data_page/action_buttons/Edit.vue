<template lang="">
    <router-link
        v-if="canEdit"
        :to="{
            name: `Edit${moduleSetup.route_prefix}`,
            params: { id: item.slug || item.uuid || item.id }
        }"
        class="border-secondary">
        <i class="fa fa-pencil-square-o text-info"></i>
        Edit
    </router-link>
</template>
<script setup>
import { inject, computed } from 'vue';
import { auth_store } from "@/GlobalStore/auth_store";

const moduleSetup = inject('moduleSetup');
const authStore = auth_store();

defineProps({
    item: {
        type: Object,
        default: () => ({ slug: 1 })
    }
});

const canEdit = computed(() => {
    const slug = moduleSetup?.permission_slugs?.edit;
    if (!slug) return true;
    return authStore.has_permission(slug);
});
</script>
<style lang=""></style>
