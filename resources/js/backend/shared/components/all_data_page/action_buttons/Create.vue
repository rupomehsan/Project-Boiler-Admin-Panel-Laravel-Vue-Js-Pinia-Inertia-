<template lang="">
    <router-link
        v-if="canCreate"
        :to="{name: `Create${moduleSetup.route_prefix}`}"
        class="btn action_btn mr-1 btn-sm btn-info d-flex align-content-center align-items-center">
        <i class="fa fa-edit mr-1"></i>
        Create
    </router-link>
</template>
<script setup>
import { inject, computed } from 'vue';
import { auth_store } from "@/GlobalStore/auth_store";

const moduleSetup = inject('moduleSetup');
const authStore = auth_store();

const canCreate = computed(() => {
    const slug = moduleSetup?.permission_slugs?.create;
    if (!slug) return true;
    return authStore.has_permission(slug);
});
</script>
<style lang=""></style>
