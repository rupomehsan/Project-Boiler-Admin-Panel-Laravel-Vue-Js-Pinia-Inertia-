<template lang="">
    <router-link
        v-if="canDetails"
        :to="{
            name: `Details${moduleSetup.route_prefix}`,
            params: { id: item.slug || item.uuid || item.id }
        }"
        class="border-secondary">
        <i class="fa fa-eye text-secondary"></i>
        Show
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
        default: () => ({ slug: 1 }),
    }
});

const canDetails = computed(() => {
    const slug = moduleSetup?.permission_slugs?.details;
    if (!slug) return true;
    return authStore.has_permission(slug);
});
</script>
<style lang=""></style>
