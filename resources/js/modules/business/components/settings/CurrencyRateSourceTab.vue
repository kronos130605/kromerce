<template>
    <div class="space-y-8">

        <!-- Section Header -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ t('settings.currency.cup_rate_source_title') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ t('settings.currency.cup_rate_source_subtitle') }}
            </p>
        </div>

        <!-- Info box -->
        <div class="flex gap-3 rounded-lg border border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950/40 p-4">
            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">{{ t('settings.currency.info_box_title') }}</p>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-0.5">{{ t('settings.currency.info_box_body') }}</p>
            </div>
        </div>

        <!-- No sources available -->
        <div v-if="!sources.length" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('settings.currency.no_sources_available') }}</p>
        </div>

        <!-- Use global default option -->
        <div v-if="sources.length" class="space-y-4">

            <!-- Global default toggle -->
            <label :class="[
                'flex items-start gap-4 rounded-xl border-2 p-4 cursor-pointer transition-all duration-200',
                selectedSourceId === null
                    ? 'border-blue-500 bg-blue-50/60 dark:bg-blue-950/30 dark:border-blue-500'
                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
            ]">
                <input
                    type="radio"
                    :value="null"
                    v-model="selectedSourceId"
                    class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                />
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ t('settings.currency.use_global_default') }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                            {{ t('settings.currency.global_default') }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                        {{ t('settings.currency.use_global_description') }}
                    </p>
                </div>
            </label>

            <!-- Source cards -->
            <label
                v-for="source in sources"
                :key="source.id"
                :class="[
                    'flex items-start gap-4 rounded-xl border-2 p-4 cursor-pointer transition-all duration-200',
                    selectedSourceId === source.id
                        ? 'border-blue-500 bg-blue-50/60 dark:bg-blue-950/30 dark:border-blue-500'
                        : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                ]"
            >
                <input
                    type="radio"
                    :value="source.id"
                    v-model="selectedSourceId"
                    class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                />

                <div class="flex-1 min-w-0">
                    <!-- Source name + badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ source.name }}</span>

                        <span :class="[
                            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                            source.type === 'api'
                                ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'
                                : 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400'
                        ]">
                            {{ source.type_label }}
                        </span>

                        <span v-if="source.is_global_default" class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 px-2 py-0.5 text-xs font-medium">
                            ★ {{ t('settings.currency.global_default') }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        {{ getSourceDescription(source.code) }}
                    </p>

                    <!-- Stats row -->
                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1">
                            <span :class="[
                                'inline-block h-2 w-2 rounded-full',
                                source.success_rate >= 90 ? 'bg-green-500' : source.success_rate >= 70 ? 'bg-yellow-500' : 'bg-red-500'
                            ]"></span>
                            {{ t('settings.currency.success_rate') }}: {{ source.success_rate }}%
                        </span>
                        <span>
                            {{ t('settings.currency.last_tested') }}:
                            {{ source.last_tested_at ?? t('settings.currency.never_tested') }}
                        </span>
                    </div>
                </div>

                <!-- Test button (only when selected) -->
                <div v-if="selectedSourceId === source.id" class="shrink-0">
                    <button
                        type="button"
                        @click.prevent="testConnection(source)"
                        :disabled="testing"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 transition-colors"
                    >
                        <svg v-if="testing" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ testing ? t('settings.currency.testing') : t('settings.currency.test_connection') }}
                    </button>

                    <!-- Test result -->
                    <div v-if="testResult" :class="[
                        'mt-2 rounded-lg px-3 py-2 text-xs',
                        testResult.success
                            ? 'bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800'
                            : 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800'
                    ]">
                        <p class="font-medium">
                            {{ testResult.success ? t('settings.currency.connection_ok') : t('settings.currency.connection_failed') }}
                        </p>
                        <p v-if="testResult.rates_found > 0" class="mt-0.5 opacity-80">
                            {{ testResult.rates_found }} {{ t('settings.currency.currencies_found') }}
                        </p>
                        <p v-if="!testResult.success" class="mt-0.5 opacity-80">{{ testResult.message }}</p>
                    </div>
                </div>
            </label>
        </div>

        <!-- Save button -->
        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
            <span v-if="savedFeedback" class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5 transition-opacity">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ t('settings.saved') }}
            </span>

            <button
                type="button"
                @click="save"
                :disabled="saving || !isDirty"
                :class="[
                    'inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition-all',
                    saving || !isDirty
                        ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                        : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm'
                ]"
            >
                <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ saving ? t('settings.saving') : t('settings.save') }}
            </button>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useTranslations } from '@/composables/useTranslations';
import axios from 'axios';

const props = defineProps({
    sources: {
        type: Array,
        default: () => [],
    },
    preferredCubaSourceId: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['updated']);

const { t } = useTranslations();

const selectedSourceId = ref(props.preferredCubaSourceId ?? null);
const saving = ref(false);
const savedFeedback = ref(false);
const testing = ref(false);
const testResult = ref(null);

const originalSourceId = ref(props.preferredCubaSourceId ?? null);

const isDirty = computed(() => selectedSourceId.value !== originalSourceId.value);

watch(selectedSourceId, () => {
    testResult.value = null;
});

const getSourceDescription = (code) => {
    const descriptions = {
        'eltoque': t('settings.currency.eltoque_description'),
        'bcc':     t('settings.currency.bcc_description'),
    };
    return descriptions[code] ?? '';
};

const testConnection = async (source) => {
    testing.value = true;
    testResult.value = null;

    try {
        const response = await axios.post('/business/currency-sources/test', {
            source_id: source.id,
        });

        testResult.value = response.data?.data ?? { success: false, message: 'Unknown error' };
    } catch (error) {
        testResult.value = {
            success: false,
            message: error.response?.data?.message ?? 'Connection test failed',
            rates_found: 0,
        };
    } finally {
        testing.value = false;
    }
};

const save = async () => {
    if (!isDirty.value) return;

    saving.value = true;
    savedFeedback.value = false;

    try {
        await axios.put('/settings/currency-source', {
            preferred_cuba_source_id: selectedSourceId.value,
        });

        originalSourceId.value = selectedSourceId.value;
        savedFeedback.value = true;
        emit('updated', { preferred_cuba_source_id: selectedSourceId.value });

        setTimeout(() => { savedFeedback.value = false; }, 3000);
    } catch (error) {
        console.error('Error saving currency source:', error);
    } finally {
        saving.value = false;
    }
};
</script>
