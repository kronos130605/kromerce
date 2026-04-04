<template>
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/40">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ t('settings.title') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('settings.subtitle') }}</p>
                </div>
            </div>
        </div>

        <!-- Settings Layout: Vertical tabs sidebar + content -->
        <div class="flex gap-6">

            <!-- Tabs Sidebar -->
            <nav class="w-56 shrink-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-2 space-y-0.5">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        :class="[
                            'w-full flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-left transition-all duration-150',
                            activeTab === tab.key
                                ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white'
                        ]"
                    >
                        <span :class="[
                            'flex h-8 w-8 items-center justify-center rounded-lg shrink-0',
                            activeTab === tab.key
                                ? 'bg-blue-100 dark:bg-blue-900/60'
                                : 'bg-gray-100 dark:bg-gray-700'
                        ]">
                            <component :is="tab.icon" :class="[
                                'h-4 w-4',
                                activeTab === tab.key ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'
                            ]" />
                        </span>
                        <span>{{ tab.label }}</span>
                    </button>
                </div>
            </nav>

            <!-- Tab Content -->
            <div class="flex-1 min-w-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">

                    <!-- Currency Rate Sources Tab -->
                    <CurrencyRateSourceTab
                        v-if="activeTab === 'currency'"
                        :cup-sources="currencySettings.cup_sources"
                        :foreign-sources="currencySettings.foreign_sources"
                        :preferred-cuba-source-id="currencySettings.preferred_cuba_source_id"
                        :preferred-foreign-source-id="currencySettings.preferred_foreign_source_id"
                        :dashboard-pairs="currencySettings.dashboard_pairs"
                        :active-currency-codes="currencySettings.active_currency_codes ?? []"
                        @updated="onCurrencyUpdated"
                    />

                    <!-- Active Currencies Tab -->
                    <ActiveCurrenciesTab v-else-if="activeTab === 'active_currencies'" />

                    <!-- Placeholder tabs -->
                    <PlaceholderTab
                        v-else
                        :title="currentTab?.titleKey"
                        :subtitle="currentTab?.subtitleKey"
                    />

                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, defineComponent, h } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations';
import CurrencyRateSourceTab from '@/modules/business/components/settings/CurrencyRateSourceTab.vue';
import ActiveCurrenciesTab from '@/modules/business/components/settings/ActiveCurrenciesTab.vue';

const page = usePage();
const { t } = useTranslations();

const activeTab = ref('currency');

const currencySettings = computed(() => page.props.settings?.currency ?? {
    cup_sources: [],
    foreign_sources: [],
    preferred_cuba_source_id: null,
    preferred_foreign_source_id: null,
    default_currency: 'USD',
    display_currencies: [],
    auto_update_rates: false,
});

// Inline SVG icon components for tabs
const IconGeneral = defineComponent({
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
    ])
});

const IconCurrency = defineComponent({
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
    ])
});

const IconStore = defineComponent({
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z' })
    ])
});

const IconNotifications = defineComponent({
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' })
    ])
});

const IconActiveCurrencies = defineComponent({
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
    ])
});

// Placeholder tab component
const PlaceholderTab = defineComponent({
    props: { title: String, subtitle: String },
    setup(props) {
        const { t } = useTranslations();
        return () => h('div', { class: 'flex flex-col items-center justify-center py-16 text-center gap-4' }, [
            h('div', { class: 'h-14 w-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center' }, [
                h('svg', { class: 'h-7 w-7 text-gray-400', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6v6m0 0v6m0-6h6m-6 0H6' })
                ])
            ]),
            h('div', {}, [
                h('h3', { class: 'text-base font-semibold text-gray-900 dark:text-white mb-1' }, props.title ? t(props.title) : ''),
                h('p', { class: 'text-sm text-gray-500 dark:text-gray-400' }, props.subtitle ? t(props.subtitle) : t('settings.general.coming_soon'))
            ])
        ]);
    }
});

const tabs = computed(() => [
    {
        key: 'general',
        label: t('settings.tabs.general'),
        icon: IconGeneral,
        titleKey: 'settings.general.title',
        subtitleKey: 'settings.general.subtitle',
    },
    {
        key: 'currency',
        label: t('settings.tabs.currency'),
        icon: IconCurrency,
        titleKey: 'settings.currency.title',
        subtitleKey: 'settings.currency.subtitle',
    },
    {
        key: 'active_currencies',
        label: t('settings.tabs.active_currencies'),
        icon: IconActiveCurrencies,
        titleKey: 'settings.active_currencies.title',
        subtitleKey: 'settings.active_currencies.subtitle',
    },
    {
        key: 'store',
        label: t('settings.tabs.store'),
        icon: IconStore,
        titleKey: 'settings.store.title',
        subtitleKey: 'settings.store.subtitle',
    },
    {
        key: 'notifications',
        label: t('settings.tabs.notifications'),
        icon: IconNotifications,
        titleKey: 'settings.notifications.title',
        subtitleKey: 'settings.notifications.subtitle',
    },
]);

const currentTab = computed(() => tabs.value.find(tab => tab.key === activeTab.value));

const onCurrencyUpdated = (data) => {
    if (page.props.settings?.currency) {
        if (data.type === 'cup') {
            page.props.settings.currency.preferred_cuba_source_id = data.preferred_cuba_source_id;
        } else if (data.type === 'foreign') {
            page.props.settings.currency.preferred_foreign_source_id = data.preferred_foreign_source_id;
        }
    }
};
</script>
