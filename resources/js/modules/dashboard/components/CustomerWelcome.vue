<template>
    <div class="bg-gradient-to-br from-blue-600 to-emerald-600 dark:from-blue-800 dark:to-emerald-800 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">
                    {{ $t('dashboard.welcome_back') }}, {{ user.name }}! 👋
                </h1>
                <p class="text-blue-100 text-lg">
                    {{ $t('dashboard.ready_to_shop') }}
                </p>
            </div>
            <div class="text-right">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-sm text-blue-100 mb-1">{{ $t('dashboard.available_balance') }}</div>
                    <div class="text-3xl font-bold">${{ userProfile.balance.toLocaleString() }}</div>
                    <div class="text-xs text-blue-200 mt-1">{{ userProfile.level }}</div>
                </div>
            </div>
        </div>
        
        <!-- User Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
                <div class="text-2xl font-bold">{{ userProfile.points }}</div>
                <div class="text-xs text-blue-100">{{ $t('dashboard.points') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
                <div class="text-2xl font-bold">${{ userProfile.savedAmount.toFixed(2) }}</div>
                <div class="text-xs text-blue-100">{{ $t('dashboard.saved') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
                <div class="text-2xl font-bold">{{ userProfile.carbonSaved }}kg</div>
                <div class="text-xs text-blue-100">{{ $t('dashboard.co2_saved') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center">
                <div class="text-2xl font-bold">{{ userProfile.ordersCount }}</div>
                <div class="text-xs text-blue-100">{{ $t('dashboard.orders') }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const page = usePage();
const user = computed(() => page.props.auth.user);
const { t } = useI18n();

// Enhanced user data
const userProfile = computed(() => ({
    balance: 2847.50,
    level: 'VIP Gold',
    points: 2450,
    savedAmount: 689.75,
    carbonSaved: 12.3,
    ordersCount: 47,
    memberSince: '2023-01-15',
    totalSpent: 3420.50
}));
</script>
