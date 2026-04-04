<template>
    <div class="space-y-10">

        <!-- ============================================ -->
        <!-- SECCIÓN 1: FUENTES PARA CUP (CUBA) -->
        <!-- ============================================ -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🇨🇺</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('settings.currency.cup_section_title') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ t('settings.currency.cup_section_subtitle') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- No CUP sources -->
            <div v-if="!cupSources.length" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('settings.currency.no_cup_sources') }}</p>
            </div>

            <!-- CUP Source Cards -->
            <div v-else class="space-y-3">
                <label
                    v-for="source in cupSources"
                    :key="source.id"
                    :class="[
                        'flex items-start gap-4 rounded-xl border-2 p-4 cursor-pointer transition-all duration-200',
                        selectedCupSourceId === source.id
                            ? 'border-blue-500 bg-blue-50/60 dark:bg-blue-950/30 dark:border-blue-500'
                            : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                    ]"
                >
                    <input type="radio" :value="source.id" v-model="selectedCupSourceId"
                        class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" />
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ source.name }}</span>
                            <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', source.type === 'api' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400']">
                                {{ source.type_label }}
                            </span>
                            <span v-if="source.is_global_default" class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 px-2 py-0.5 text-xs font-medium">
                                ★ {{ t('settings.currency.global_default') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ getSourceDescription(source.code) }}</p>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <span :class="['inline-block h-2 w-2 rounded-full', source.success_rate >= 90 ? 'bg-green-500' : source.success_rate >= 70 ? 'bg-yellow-500' : 'bg-red-500']"></span>
                                {{ t('settings.currency.success_rate') }}: {{ source.success_rate }}%
                            </span>
                            <span>{{ t('settings.currency.last_tested') }}: {{ source.last_tested_at ?? t('settings.currency.never_tested') }}</span>
                        </div>
                    </div>
                    <!-- Test button -->
                    <div v-if="selectedCupSourceId === source.id" class="shrink-0">
                        <button type="button" @click.prevent="testConnection(source, 'cup')" :disabled="testing.cup"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 transition-colors">
                            <svg v-if="testing.cup" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ testing.cup ? t('settings.currency.testing') : t('settings.currency.test_connection') }}
                        </button>
                        <div v-if="testResults.cup" :class="['mt-2 rounded-lg px-3 py-2 text-xs', testResults.cup.success ? 'bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800']">
                            <p class="font-medium">{{ testResults.cup.success ? t('settings.currency.connection_ok') : t('settings.currency.connection_failed') }}</p>
                            <p v-if="testResults.cup.rates_found > 0" class="mt-0.5 opacity-80">{{ testResults.cup.rates_found }} {{ t('settings.currency.currencies_found') }}</p>
                            <p v-if="!testResults.cup.success" class="mt-0.5 opacity-80">{{ testResults.cup.message }}</p>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Save CUP -->
            <div v-if="cupSources.length" class="flex items-center justify-end gap-3 pt-2">
                <span v-if="savedFeedback.cup" class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ t('settings.saved') }}
                </span>
                <button type="button" @click="save('cup')" :disabled="saving.cup || !isDirty.cup"
                    :class="['inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition-all', saving.cup || !isDirty.cup ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm']">
                    <svg v-if="saving.cup" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ saving.cup ? t('settings.saving') : t('settings.save') }}
                </button>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 2: FUENTES PARA DIVISAS EXTRANJERAS -->
        <!-- ============================================ -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🌍</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('settings.currency.foreign_section_title') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ t('settings.currency.foreign_section_subtitle') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- No foreign sources -->
            <div v-if="!foreignSources.length" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('settings.currency.no_foreign_sources') }}</p>
            </div>

            <!-- Foreign Source Cards -->
            <div v-else class="space-y-3">
                <label v-for="source in foreignSources" :key="source.id"
                    :class="['flex items-start gap-4 rounded-xl border-2 p-4 cursor-pointer transition-all duration-200', selectedForeignSourceId === source.id ? 'border-blue-500 bg-blue-50/60 dark:bg-blue-950/30 dark:border-blue-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600']">
                    <input type="radio" :value="source.id" v-model="selectedForeignSourceId"
                        class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" />
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ source.name }}</span>
                            <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium', source.type === 'api' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400']">
                                {{ source.type_label }}
                            </span>
                            <span v-if="source.is_global_default" class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 px-2 py-0.5 text-xs font-medium">
                                ★ {{ t('settings.currency.global_default') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ getSourceDescription(source.code) }}</p>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <span :class="['inline-block h-2 w-2 rounded-full', source.success_rate >= 90 ? 'bg-green-500' : source.success_rate >= 70 ? 'bg-yellow-500' : 'bg-red-500']"></span>
                                {{ t('settings.currency.success_rate') }}: {{ source.success_rate }}%
                            </span>
                            <span>{{ t('settings.currency.last_tested') }}: {{ source.last_tested_at ?? t('settings.currency.never_tested') }}</span>
                        </div>
                    </div>
                    <!-- Test button -->
                    <div v-if="selectedForeignSourceId === source.id" class="shrink-0">
                        <button type="button" @click.prevent="testConnection(source, 'foreign')" :disabled="testing.foreign"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 transition-colors">
                            <svg v-if="testing.foreign" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ testing.foreign ? t('settings.currency.testing') : t('settings.currency.test_connection') }}
                        </button>
                        <div v-if="testResults.foreign" :class="['mt-2 rounded-lg px-3 py-2 text-xs', testResults.foreign.success ? 'bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800']">
                            <p class="font-medium">{{ testResults.foreign.success ? t('settings.currency.connection_ok') : t('settings.currency.connection_failed') }}</p>
                            <p v-if="testResults.foreign.rates_found > 0" class="mt-0.5 opacity-80">{{ testResults.foreign.rates_found }} {{ t('settings.currency.currencies_found') }}</p>
                            <p v-if="!testResults.foreign.success" class="mt-0.5 opacity-80">{{ testResults.foreign.message }}</p>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Save Foreign -->
            <div v-if="foreignSources.length" class="flex items-center justify-end gap-3 pt-2">
                <span v-if="savedFeedback.foreign" class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ t('settings.saved') }}
                </span>
                <button type="button" @click="save('foreign')" :disabled="saving.foreign || !isDirty.foreign"
                    :class="['inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition-all', saving.foreign || !isDirty.foreign ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm']">
                    <svg v-if="saving.foreign" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ saving.foreign ? t('settings.saving') : t('settings.save') }}
                </button>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 3: PARES A MOSTRAR EN DASHBOARD -->
        <!-- ============================================ -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">📊</span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('settings.currency.dashboard_pairs_title') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ t('settings.currency.dashboard_pairs_subtitle') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                <button
                    v-for="pair in ALL_PAIRS"
                    :key="pairKey(pair)"
                    type="button"
                    @click="togglePair(pair)"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 rounded-xl border-2 px-3 py-3 text-sm font-medium transition-all duration-150',
                        selectedPairKeys.includes(pairKey(pair))
                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300'
                            : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600'
                    ]"
                >
                    <span class="font-bold text-base">{{ pair.from }}</span>
                    <svg class="h-3 w-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span>{{ pair.to }}</span>
                </button>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <span v-if="savedFeedback.pairs" class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ t('settings.saved') }}
                </span>
                <button
                    type="button"
                    @click="savePairs"
                    :disabled="saving.pairs || !isPairsDirty"
                    :class="['inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition-all', saving.pairs || !isPairsDirty ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm']"
                >
                    <svg v-if="saving.pairs" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ saving.pairs ? t('settings.saving') : t('settings.save') }}
                </button>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useTranslations } from '@/composables/useTranslations';
import axios from 'axios';

const props = defineProps({
    cupSources: { type: Array, default: () => [] },
    foreignSources: { type: Array, default: () => [] },
    preferredCubaSourceId: { type: String, default: null },
    preferredForeignSourceId: { type: String, default: null },
    dashboardPairs: { type: Array, default: () => [] },
    activeCurrencyCodes: { type: Array, default: () => [] },
});

const emit = defineEmits(['updated']);
const { t } = useTranslations();

// State
const selectedCupSourceId = ref(props.preferredCubaSourceId ?? null);
const selectedForeignSourceId = ref(props.preferredForeignSourceId ?? null);
const originalCupSourceId = ref(props.preferredCubaSourceId ?? null);
const originalForeignSourceId = ref(props.preferredForeignSourceId ?? null);

const saving = ref({ cup: false, foreign: false, pairs: false });
const savedFeedback = ref({ cup: false, foreign: false, pairs: false });
const testing = ref({ cup: false, foreign: false });
const testResults = ref({ cup: null, foreign: null });

// All possible pairs (base catalog)
const BASE_PAIRS = [
    { from: 'USD', to: 'CUP' },
    { from: 'EUR', to: 'CUP' },
    { from: 'MLC', to: 'CUP' },
    { from: 'CLA', to: 'CUP' },
    { from: 'USD', to: 'EUR' },
    { from: 'USD', to: 'GBP' },
    { from: 'EUR', to: 'USD' },
    { from: 'USD', to: 'MXN' },
    { from: 'USD', to: 'BRL' },
    { from: 'USD', to: 'CAD' },
];

// Only show pairs where both currencies are active for this store
const ALL_PAIRS = computed(() => {
    if (!props.activeCurrencyCodes.length) return BASE_PAIRS;
    return BASE_PAIRS.filter(p =>
        props.activeCurrencyCodes.includes(p.from) &&
        props.activeCurrencyCodes.includes(p.to)
    );
});

const pairKey = (p) => `${p.from}-${p.to}`;

const selectedPairKeys = ref(
    (props.dashboardPairs ?? []).map(pairKey)
);
const originalPairKeys = ref([...selectedPairKeys.value]);

const isPairsDirty = computed(() =>
    JSON.stringify([...selectedPairKeys.value].sort()) !== JSON.stringify([...originalPairKeys.value].sort())
);

const togglePair = (pair) => {
    const key = pairKey(pair);
    const idx = selectedPairKeys.value.indexOf(key);
    if (idx >= 0) {
        selectedPairKeys.value.splice(idx, 1);
    } else {
        selectedPairKeys.value.push(key);
    }
};

const savePairs = async () => {
    saving.value.pairs = true;
    savedFeedback.value.pairs = false;
    try {
        const pairs = ALL_PAIRS.filter(p => selectedPairKeys.value.includes(pairKey(p)));
        await axios.put('/settings/currency-source', { type: 'dashboard_pairs', dashboard_pairs: pairs });
        originalPairKeys.value = [...selectedPairKeys.value];
        emit('updated', { type: 'dashboard_pairs', dashboard_pairs: pairs });
        savedFeedback.value.pairs = true;
        setTimeout(() => { savedFeedback.value.pairs = false; }, 3000);
    } catch (error) {
        console.error('Error saving dashboard pairs:', error);
    } finally {
        saving.value.pairs = false;
    }
};

const isDirty = computed(() => ({
    cup: selectedCupSourceId.value !== originalCupSourceId.value,
    foreign: selectedForeignSourceId.value !== originalForeignSourceId.value,
}));

watch(selectedCupSourceId, () => { testResults.value.cup = null; });
watch(selectedForeignSourceId, () => { testResults.value.foreign = null; });

// Sync selectedPairKeys when dashboardPairs prop changes (async data)
watch(() => props.dashboardPairs, (newPairs) => {
    selectedPairKeys.value = (newPairs ?? []).map(pairKey);
    originalPairKeys.value = [...selectedPairKeys.value];
}, { immediate: true });

const getSourceDescription = (code) => {
    const descriptions = {
        'openexchange-global': t('settings.currency.openexchange_description'),
        'exchangerate-api':    t('settings.currency.exchangerate_api_description'),
        'eltoque-cuba':        t('settings.currency.eltoque_description'),
        'bcc-cuba':            t('settings.currency.bcc_description'),
    };
    return descriptions[code] ?? '';
};

const testConnection = async (source, type) => {
    testing.value[type] = true;
    testResults.value[type] = null;
    try {
        const response = await axios.post('/business/currency-sources/test', { source_id: source.id });
        testResults.value[type] = response.data?.data ?? { success: false, message: 'Unknown error' };
    } catch (error) {
        testResults.value[type] = {
            success: false,
            message: error.response?.data?.message ?? 'Connection test failed',
            rates_found: 0,
        };
    } finally {
        testing.value[type] = false;
    }
};

const save = async (type) => {
    if (!isDirty.value[type]) return;
    saving.value[type] = true;
    savedFeedback.value[type] = false;
    try {
        const payload = { type };
        if (type === 'cup') {
            payload.preferred_cuba_source_id = selectedCupSourceId.value;
        } else {
            payload.preferred_foreign_source_id = selectedForeignSourceId.value;
        }
        await axios.put('/settings/currency-source', payload);
        if (type === 'cup') {
            originalCupSourceId.value = selectedCupSourceId.value;
            emit('updated', { type: 'cup', preferred_cuba_source_id: selectedCupSourceId.value });
        } else {
            originalForeignSourceId.value = selectedForeignSourceId.value;
            emit('updated', { type: 'foreign', preferred_foreign_source_id: selectedForeignSourceId.value });
        }
        savedFeedback.value[type] = true;
        setTimeout(() => { savedFeedback.value[type] = false; }, 3000);
    } catch (error) {
        console.error('Error saving currency source:', error);
    } finally {
        saving.value[type] = false;
    }
};
</script>
