<template>
    <div class="space-y-8">

        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('settings.active_currencies.title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ t('settings.active_currencies.subtitle') }}</p>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center py-12">
            <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        </div>

        <template v-else>
            <!-- Summary badges -->
            <div class="flex flex-wrap gap-3">
                <div class="inline-flex items-center gap-2 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-2">
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">
                        {{ activeCurrencies.length }} {{ t('settings.active_currencies.active') }}
                    </span>
                </div>
                <div class="inline-flex items-center gap-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-2">
                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ inactiveCurrencies.length }} {{ t('settings.active_currencies.inactive') }}
                    </span>
                </div>
            </div>

            <!-- Active currencies list -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">
                    {{ t('settings.active_currencies.active_section') }}
                </h4>
                <div class="space-y-2">
                    <div v-for="currency in activeCurrencies" :key="currency.code"
                        class="flex items-center gap-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/60 px-4 py-3">
                        <span class="text-xl shrink-0">{{ currency.flag ?? '💱' }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ currency.code }}
                                <span v-if="currency.is_cup || currency.is_cla"
                                    class="ml-2 inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 px-2 py-0.5 text-xs font-medium">
                                    {{ t('settings.active_currencies.cuba_currency') }}
                                </span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ currency.name }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2.5 py-1 text-xs font-medium">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                {{ t('settings.active_currencies.status_active') }}
                            </span>
                            <button @click="initiateDeactivate(currency)"
                                :disabled="processing === currency.code"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2.5 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-300 dark:hover:border-red-700 hover:text-red-600 dark:hover:text-red-400 disabled:opacity-50 transition-colors">
                                <svg v-if="processing === currency.code" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                {{ t('settings.active_currencies.deactivate') }}
                            </button>
                        </div>
                    </div>

                    <div v-if="!activeCurrencies.length" class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('settings.active_currencies.no_active') }}</p>
                    </div>
                </div>
            </div>

            <!-- Inactive currencies list -->
            <div v-if="inactiveCurrencies.length">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">
                    {{ t('settings.active_currencies.available_section') }}
                </h4>
                <div class="space-y-2">
                    <div v-for="currency in inactiveCurrencies" :key="currency.code"
                        class="flex items-center gap-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30 px-4 py-3 opacity-70">
                        <span class="text-xl shrink-0 grayscale">{{ currency.flag ?? '💱' }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ currency.code }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ currency.name }}</p>
                        </div>
                        <button @click="activate(currency.code)"
                            :disabled="processing === currency.code"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 disabled:opacity-50 transition-colors">
                            <svg v-if="processing === currency.code" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ t('settings.active_currencies.activate') }}
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- ===== Deactivation Modal ===== -->
        <Teleport to="body">
            <div v-if="deactivateModal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal"></div>
                <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-5">

                    <!-- Products affected warning -->
                    <template v-if="deactivateModal.affectedProducts.length">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
                                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ t('settings.active_currencies.products_affected_title') }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ t('settings.active_currencies.products_affected_desc', { count: deactivateModal.affectedProducts.length, currency: deactivateModal.currency }) }}
                                </p>
                            </div>
                        </div>

                        <!-- Affected products list -->
                        <div class="max-h-40 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                            <div v-for="product in deactivateModal.affectedProducts" :key="product.id"
                                class="flex items-center gap-3 px-4 py-2.5">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ product.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ product.sku }}</p>
                                </div>
                                <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0">{{ product.base_currency }}</span>
                            </div>
                        </div>

                        <!-- Migration option -->
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 space-y-3">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ t('settings.active_currencies.migrate_option') }}</p>
                            <select v-model="deactivateModal.migrateTo"
                                class="w-full rounded-lg border border-blue-300 dark:border-blue-700 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">{{ t('settings.active_currencies.no_migrate') }}</option>
                                <option v-for="c in activeCurrencies.filter(c => c.code !== deactivateModal.currency)" :key="c.code" :value="c.code">
                                    {{ c.flag }} {{ c.code }} — {{ c.name }}
                                </option>
                            </select>
                            <p v-if="!deactivateModal.migrateTo" class="text-xs text-amber-600 dark:text-amber-400">
                                {{ t('settings.active_currencies.no_migrate_warning') }}
                            </p>
                        </div>
                    </template>

                    <!-- Simple confirmation (no products affected) -->
                    <template v-else>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ t('settings.active_currencies.confirm_deactivate_title', { currency: deactivateModal.currency }) }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ t('settings.active_currencies.confirm_deactivate_desc') }}
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-2">
                        <button @click="closeModal" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            {{ t('common.cancel') }}
                        </button>
                        <button @click="confirmDeactivate"
                            :disabled="processing === deactivateModal.currency"
                            :class="[
                                'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white disabled:opacity-50 transition-colors',
                                deactivateModal.affectedProducts.length && !deactivateModal.migrateTo
                                    ? 'bg-amber-600 hover:bg-amber-700'
                                    : 'bg-red-600 hover:bg-red-700'
                            ]">
                            <svg v-if="processing === deactivateModal.currency" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span v-if="deactivateModal.affectedProducts.length && deactivateModal.migrateTo">
                                {{ t('settings.active_currencies.migrate_and_deactivate') }}
                            </span>
                            <span v-else-if="deactivateModal.affectedProducts.length && !deactivateModal.migrateTo">
                                {{ t('settings.active_currencies.deactivate_anyway') }}
                            </span>
                            <span v-else>
                                {{ t('settings.active_currencies.confirm_deactivate') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

const currencies = ref([]);
const loading = ref(true);
const processing = ref(null);

const deactivateModal = ref({
    open: false,
    currency: null,
    affectedProducts: [],
    migrateTo: '',
});

const activeCurrencies = computed(() => currencies.value.filter(c => c.is_active));
const inactiveCurrencies = computed(() => currencies.value.filter(c => !c.is_active));

const fetchCurrencies = async () => {
    try {
        loading.value = true;
        const { data } = await axios.get('/business/currencies');
        currencies.value = data.data?.currencies ?? [];
    } catch (err) {
        console.error('Failed to load currencies', err);
    } finally {
        loading.value = false;
    }
};

const activate = async (code) => {
    processing.value = code;
    try {
        await axios.post('/business/currencies/activate', { currency_code: code });
        const found = currencies.value.find(c => c.code === code);
        if (found) found.is_active = true;
    } catch (err) {
        console.error('Failed to activate currency', err);
    } finally {
        processing.value = null;
    }
};

const initiateDeactivate = async (currency) => {
    processing.value = currency.code;
    try {
        const { data } = await axios.post('/business/currencies/deactivate', {
            currency_code: currency.code,
        });

        if (data.success) {
            const found = currencies.value.find(c => c.code === currency.code);
            if (found) found.is_active = false;
        } else if (data.requires_action) {
            deactivateModal.value = {
                open: true,
                currency: currency.code,
                affectedProducts: data.affected_products ?? [],
                migrateTo: '',
            };
        }
    } catch (err) {
        if (err.response?.status === 409) {
            const d = err.response.data;
            deactivateModal.value = {
                open: true,
                currency: currency.code,
                affectedProducts: d.affected_products ?? [],
                migrateTo: '',
            };
        } else {
            console.error('Failed to deactivate currency', err);
        }
    } finally {
        processing.value = null;
    }
};

const confirmDeactivate = async () => {
    const code = deactivateModal.value.currency;
    const migrateTo = deactivateModal.value.migrateTo;
    processing.value = code;

    try {
        if (migrateTo) {
            await axios.post('/business/currencies/deactivate-migrate', {
                currency_code: code,
                migrate_to_currency: migrateTo,
            });
        } else {
            await axios.post('/business/currencies/deactivate', { currency_code: code, force: true });
        }

        const found = currencies.value.find(c => c.code === code);
        if (found) found.is_active = false;
        closeModal();
    } catch (err) {
        console.error('Failed to confirm deactivation', err);
    } finally {
        processing.value = null;
    }
};

const closeModal = () => {
    deactivateModal.value = { open: false, currency: null, affectedProducts: [], migrateTo: '' };
};

onMounted(fetchCurrencies);
</script>
