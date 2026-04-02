<script setup>
import { computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations';
import BusinessLayout from '@/layouts/BusinessLayout.vue';
import { useProductManager } from '../composables/useProductManager.js';
import { useProductFilters } from '../composables/useProductFilters.js';
import { useBulkActions } from '../composables/useBulkActions.js';
import ProductModal from '../components/ProductModal.vue';
import ProductFilters from '../components/ProductFilters.vue';
import BulkActionsBar from '../components/BulkActionsBar.vue';
import DataTable from '@/components/shared/DataTable.vue';
import ConfirmationModal from '@/components/shared/ConfirmationModal.vue';
import ProductView from '../components/ProductView.vue';
import StatusBadge from '@/components/ui/data-display/StatusBadge.vue';
import StockIndicator from '@/components/ui/data-display/StockIndicator.vue';

const { t } = useTranslations();
const page = usePage();

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    statistics: Object,
});

// Product Manager (Modal CRUD)
const {
    selectedProduct,
    isModalOpen,
    isViewOpen,
    isEditing,
    currentStep,
    form,
    steps,
    errors,
    loading,
    isConfirmModalOpen,
    confirmModalConfig,
    openCreate,
    openEdit,
    openView,
    closeModal,
    closeView,
    nextStep,
    prevStep,
    goToStep,
    save,
    confirmDelete,
    closeConfirmModal,
} = useProductManager({
    initialProducts: props.products?.data || [],
    initialFilters: props.filters,
});

// Filters
const {
    filters: activeFilters,
    showFilters,
    hasActiveFilters,
    activeFilterCount,
    updateFilter,
    clearFilters,
    toggleFilters,
    searchProducts,
} = useProductFilters(props.filters);

// Bulk Actions
const {
    selectedItems,
    isProcessing,
    hasSelection,
    selectionCount,
    toggleItem,
    toggleAll,
    clearSelection,
    isSelected,
    quickActions,
} = useBulkActions();

// Table columns configuration
const tableColumns = [
    {
        key: 'name',
        label: t('products.fields.product'),
        type: 'image-text',
        imageKey: 'images.0.thumbnail_url',
        labelKey: 'name',
        subLabelKey: 'sku',
        fallbackIcon: '📦',
    },
    {
        key: 'categories',
        label: t('products.fields.categories'),
        type: 'custom',
    },
    {
        key: 'base_price',
        label: t('products.fields.price'),
        type: 'currency',
        currencyKey: 'base_currency',
        align: 'left',
    },
    {
        key: 'stock_quantity',
        label: t('products.fields.stock'),
        type: 'custom',
    },
    {
        key: 'status',
        label: t('products.fields.status'),
        type: 'custom',
    },
];

// Table actions configuration
const tableActions = [
    { key: 'view', color: 'blue' },
    { key: 'edit', color: 'green' },
    { key: 'delete', color: 'red' },
];

// Handle table actions
const handleTableAction = ({ action, item }) => {
    if (action === 'view') openView(item);
    else if (action === 'edit') openEdit(item);
    else if (action === 'delete') confirmDelete(item);
};

// Handle query params for modal auto-open
onMounted(() => {
    const query = page.props.query || {};
    const modalType = query.modal;
    const productId = query.product;

    if (modalType === 'create') {
        openCreate();
    } else if (modalType === 'edit' && productId) {
        const product = props.products.data.find(p => p.id == productId);
        if (product) openEdit(product);
    } else if (modalType === 'view' && productId) {
        const product = props.products.data.find(p => p.id == productId);
        if (product) openView(product);
    }
});

const statsCards = computed(() => [
    { title: t('products.statistics.total'), value: props.statistics?.total_products || 0, icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', color: 'blue' },
    { title: t('products.statistics.active'), value: props.statistics?.active_products || 0, icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'green' },
    { title: t('products.statistics.low_stock'), value: props.statistics?.low_stock_products || 0, icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', color: 'yellow' },
    { title: t('products.statistics.out_of_stock'), value: props.statistics?.out_of_stock_products || 0, icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'red' },
]);

const getStatusColor = (status) => ({
    active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
}[status] || 'bg-gray-100 text-gray-800');
</script>

<template>
    <BusinessLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ t('products.title') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ t('products.subtitle') }}</p>
                </div>
                <button @click="openCreate" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('products.add') }}
                </button>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="stat in statsCards" :key="stat.title" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center">
                        <div :class="`p-2 bg-${stat.color}-100 dark:bg-${stat.color}-900/30 rounded-lg`">
                            <svg class="w-5 h-5" :class="`text-${stat.color}-600 dark:text-${stat.color}-400`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ stat.title }}</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white">{{ stat.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                :value="activeFilters.search"
                                @input="searchProducts($event.target.value)"
                                type="text"
                                :placeholder="t('products.list.search_placeholder')"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Filter Toggle Button -->
                    <button
                        @click="toggleFilters"
                        :class="[
                            'inline-flex items-center gap-2 px-4 py-2 rounded-lg border transition-colors',
                            hasActiveFilters
                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'
                        ]"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>{{ t('products.list.filters.title') }}</span>
                        <span v-if="activeFilterCount > 0" class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-600 rounded-full">
                            {{ activeFilterCount }}
                        </span>
                    </button>

                    <!-- Quick Filters -->
                    <div class="flex gap-2">
                        <button
                            @click="activeFilters.status = 'active'; updateFilter('status', 'active')"
                            :class="[
                                'px-3 py-2 text-sm rounded-lg transition-colors',
                                activeFilters.status === 'active'
                                    ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            ]"
                        >
                            {{ t('products.list.filters.active') }}
                        </button>
                        <button
                            @click="activeFilters.status = 'draft'; updateFilter('status', 'draft')"
                            :class="[
                                'px-3 py-2 text-sm rounded-lg transition-colors',
                                activeFilters.status === 'draft'
                                    ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            ]"
                        >
                            {{ t('products.list.filters.draft') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filters Panel -->
            <ProductFilters
                :filters="activeFilters"
                :categories="categories"
                :show="showFilters"
                @update:filter="updateFilter"
                @clear="clearFilters"
            />

            <!-- Products Table -->
            <DataTable
                :data="products.data"
                :columns="tableColumns"
                :actions="tableActions"
                :selectable="true"
                :selected-items="selectedItems"
                @action="handleTableAction"
                @selection-change="selectedItems = $event"
                :empty-title="t('products.empty.title')"
                :empty-description="t('products.empty.description')"
                empty-icon="📦"
            >
                <!-- Custom Slots -->
                <template #cell-categories="{ item }">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="category in item.categories?.slice(0, 2)"
                            :key="category.id"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                        >
                            {{ category.name }}
                        </span>
                        <span
                            v-if="item.categories?.length > 2"
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400"
                        >
                            +{{ item.categories.length - 2 }}
                        </span>
                    </div>
                </template>

                <template #cell-stock_quantity="{ item }">
                    <StockIndicator
                        :stock="item.stock_quantity"
                        :low-stock-threshold="item.low_stock_threshold || 10"
                        size="sm"
                        variant="badge"
                    />
                </template>

                <template #cell-status="{ item }">
                    <StatusBadge
                        :status="item.status"
                        type="product"
                        size="sm"
                    />
                </template>

                <template #empty-action>
                    <button @click="openCreate" class="mt-4 btn btn-primary">{{ t('products.add_first') }}</button>
                </template>
            </DataTable>

            <!-- Bulk Actions Bar -->
            <BulkActionsBar
                :selection-count="selectionCount"
                :is-processing="isProcessing"
                @clear-selection="clearSelection"
                @bulk-activate="quickActions.activate"
                @bulk-deactivate="quickActions.deactivate"
                @bulk-draft="quickActions.draft"
                @bulk-delete="quickActions.delete"
                @bulk-export="quickActions.exportCSV"
            />
        </div>

        <!-- Modals -->
        <ProductModal
            :is-open="isModalOpen"
            :is-editing="isEditing"
            v-model:form="form"
            :steps="steps"
            :current-step="currentStep"
            :errors="errors"
            :loading="loading"
            :categories="categories"
            @close="closeModal"
            @save="save"
            @next-step="nextStep"
            @prev-step="prevStep"
            @go-to-step="goToStep"
        />

        <ProductView
            :is-open="isViewOpen"
            :product="selectedProduct"
            @close="closeView"
            @edit="(p) => { closeView(); openEdit(p); }"
        />

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :show="isConfirmModalOpen"
            :title="confirmModalConfig.title"
            :message="confirmModalConfig.message"
            :danger="confirmModalConfig.danger"
            :confirm-text="confirmModalConfig.confirmText"
            :cancel-text="confirmModalConfig.cancelText"
            :loading="loading"
            @confirm="confirmModalConfig.onConfirm"
            @cancel="closeConfirmModal"
        />
    </BusinessLayout>
</template>

<style scoped>
.btn {
    @apply inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors;
}
.btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
}
</style>
