<template>
    <div class="category-selector">
        <!-- Label -->
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ label }}
            <span v-if="selectionCount > 0" class="text-xs text-gray-500 ml-1">
                ({{ selectionCount }} seleccionadas)
            </span>
        </label>

        <!-- Search Input -->
        <div class="relative mb-3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                :value="searchQuery"
                @input="setSearch($event.target.value)"
                type="text"
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :placeholder="t('products.categories.search_placeholder')"
            >
            <button
                v-if="searchQuery"
                @click="clearSearch"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Selected Categories -->
        <div v-if="selectedItems.length > 0" class="mb-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ t('products.categories.selected') }}:</p>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="category in selectedItems"
                    :key="category.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700"
                >
                    {{ category.name }}
                    <button
                        @click.stop="toggle(category.id)"
                        class="ml-2 text-blue-600 dark:text-blue-300 hover:text-blue-800 dark:hover:text-blue-100 focus:outline-none"
                        :title="t('common.remove')"
                    >
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            </div>
        </div>

        <!-- Available Categories Grid -->
        <div class="category-grid">
            <p v-if="filteredItems.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                {{ t('products.categories.no_results') }}
            </p>
            <div v-else class="flex flex-wrap gap-2">
                <button
                    v-for="category in filteredItems"
                    :key="category.id"
                    @click.stop="toggle(category.id)"
                    class="category-badge"
                    :class="{
                        'selected': isSelected(category.id),
                        'unselected': !isSelected(category.id)
                    }"
                >
                    <span v-if="isSelected(category.id)" class="check-icon">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    {{ category.name }}
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div v-if="selectedItems.length > 0" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex gap-2">
            <button
                @click="clearAll"
                class="text-xs text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 underline"
            >
                {{ t('products.categories.clear_all') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
        default: () => []
    },
    modelValue: {
        type: Array,
        required: true,
        default: () => []
    },
    label: {
        type: String,
        default: 'Categorías'
    }
});

const emit = defineEmits(['update:modelValue']);
const { t } = useTranslations();

// Estado local - simple y directo
const selectedIds = ref([...props.modelValue.map(id => String(id))]);
const searchQuery = ref('');

// Sincronizar prop externo -> estado local
watch(() => props.modelValue, (newValue) => {
    const newIds = (newValue || []).map(id => String(id)).sort();
    const currentIds = [...selectedIds.value].sort();
    if (JSON.stringify(newIds) !== JSON.stringify(currentIds)) {
        selectedIds.value = newIds;
    }
}, { deep: true });

// Emitir cambios cuando cambia la selección
watch(selectedIds, (newValue) => {
    emit('update:modelValue', [...newValue]);
}, { deep: true });

// Computadas - filtrar items no seleccionados
const selectedItems = computed(() => {
    return props.categories.filter(cat => selectedIds.value.includes(String(cat.id)));
});

const unselectedItems = computed(() => {
    return props.categories.filter(cat => !selectedIds.value.includes(String(cat.id)));
});

const filteredItems = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();
    let items = unselectedItems.value;
    
    if (query) {
        items = items.filter(cat => {
            const name = (cat.name || '').toLowerCase();
            const desc = (cat.description || '').toLowerCase();
            return name.includes(query) || desc.includes(query);
        });
    }
    
    return items;
});

const selectionCount = computed(() => selectedIds.value.length);

// Métodos simples
function isSelected(categoryId) {
    return selectedIds.value.includes(String(categoryId));
}

function toggle(categoryId) {
    const strId = String(categoryId);
    const index = selectedIds.value.indexOf(strId);
    
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(strId);
    }
}

function clearAll() {
    selectedIds.value = [];
}

function setSearch(value) {
    searchQuery.value = value;
}

function clearSearch() {
    searchQuery.value = '';
}
</script>

<style scoped>
.category-selector {
    @apply w-full;
}

.category-badge {
    @apply inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-150 ease-in-out;
    @apply border focus:outline-none focus:ring-2 focus:ring-offset-1;
}

.category-badge.unselected {
    @apply bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200 hover:border-gray-400;
    @apply dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 dark:hover:border-gray-500;
}

.category-badge.selected {
    @apply bg-green-100 text-green-800 border-green-300;
    @apply dark:bg-green-900 dark:text-green-200 dark:border-green-700;
}

.category-badge .check-icon {
    @apply mr-1.5 flex-shrink-0;
}

/* Animation for badges */
.category-badge {
    @apply transform hover:scale-105 active:scale-95;
}
</style>
