<script setup>
import BusinessLayout from '@/layouts/BusinessLayout.vue';
import { useRoleGuard } from '@/composables/useRoleGuard.js';

// Role validation - this page should only be accessible to business users
const { requireBusinessRole } = useRoleGuard();
requireBusinessRole('dashboard');
</script>

<template>
    <BusinessLayout />
</template>
