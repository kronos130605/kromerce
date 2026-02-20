<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);

const isBusinessOwner = computed(() => user.value?.roles?.some(role => role.name === 'business_owner'));
const isCustomer = computed(() => user.value?.roles?.some(role => role.name === 'customer'));
const isSuperAdmin = computed(() => user.value?.roles?.some(role => role.name === 'super_admin'));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Welcome back, {{ user.name }}!
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Tenant Info for Business Owners -->
                <div v-if="isBusinessOwner && currentTenant" class="mb-6 bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-blue-900">
                            {{ currentTenant.name }}
                        </h3>
                        <p class="text-sm text-blue-700 mt-1">
                            Subdomain: {{ currentTenant.slug }}.kromerce.test
                        </p>
                    </div>
                </div>

                <!-- Role-based Dashboard Content -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Super Admin Dashboard -->
                        <div v-if="isSuperAdmin">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Super Admin Dashboard</h3>
                            <p class="text-gray-600 mb-6">Manage the entire platform, users, and tenants.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <button class="p-4 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition-colors">
                                    Manage Users
                                </button>
                                <button class="p-4 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition-colors">
                                    Manage Tenants
                                </button>
                                <button class="p-4 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition-colors">
                                    View Analytics
                                </button>
                            </div>
                        </div>

                        <!-- Business Owner Dashboard -->
                        <div v-else-if="isBusinessOwner">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Business Dashboard</h3>
                            <p class="text-gray-600 mb-6">Manage your products, orders, and business settings.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <button class="p-4 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors">
                                    Manage Products
                                </button>
                                <button class="p-4 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors">
                                    View Orders
                                </button>
                                <button class="p-4 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors">
                                    Business Settings
                                </button>
                            </div>
                        </div>

                        <!-- Customer Dashboard -->
                        <div v-else-if="isCustomer">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Dashboard</h3>
                            <p class="text-gray-600 mb-6">Browse products, place orders, and manage your profile.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <button class="p-4 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition-colors">
                                    Browse Products
                                </button>
                                <button class="p-4 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition-colors">
                                    My Orders
                                </button>
                                <button class="p-4 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition-colors">
                                    Profile Settings
                                </button>
                            </div>
                        </div>

                        <!-- Default/Fallback -->
                        <div v-else>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Dashboard</h3>
                            <p class="text-gray-600">Welcome to your dashboard. Your role is being configured.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
