<script setup>
import { computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations';
import { CheckCircleIcon, DocumentTextIcon } from '@heroicons/vue/24/outline';
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
import SearchFilterBar from '@/components/shared/SearchFilterBar.vue';
import DataActionsBar from '@/components/shared/DataActionsBar.vue';
import StockIndicator from '@/components/ui/data-display/StockIndicator.vue';
import StatusBadge from "@/components/ui/data-display/StatusBadge.vue";

const { t } = useTranslations();
const page = usePage();

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    statistics: Object,
    active_currencies: { type: Array, default: () => [] },
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
        label: t('products.labels.product'),
        type: 'image-text',
        imageKey: 'images.0.thumbnail_url',
        labelKey: 'name',
        subLabelKey: 'sku',
        fallbackIcon: '📦',
    },
    {
        key: 'categories',
        label: t('products.labels.categories'),
        type: 'custom',
    },
    {
        key: 'base_price',
        label: t('products.labels.price'),
        type: 'currency',
        currencyKey: 'base_currency',
        align: 'left',
    },
    {
        key: 'stock_quantity',
        label: t('products.labels.stock'),
        type: 'custom',
    },
    {
        key: 'status',
        label: t('products.labels.status'),
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
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ t('products.metadata.page_title') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ t('products.metadata.page_subtitle') }}</p>
                </div>
                <button @click="openCreate" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ t('products.actions.add') }}
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

            <!-- Data Actions & Search Bar -->
            <div class="flex flex-wrap items-center gap-3">
                <SearchFilterBar
                    :search-value="activeFilters.search"
                    :search-placeholder="t('common.search')"
                    :has-active-filters="hasActiveFilters"
                    :active-filter-count="activeFilterCount"
                    :filters-open="showFilters"
                    :quick-filters="[
                        { key: 'active', label: t('products.filters.active'), icon: CheckCircleIcon, color: 'green' },
                        { key: 'draft', label: t('products.filters.draft'), icon: DocumentTextIcon, color: 'yellow' }
                    ]"
                    :active-quick-filter="activeFilters.status"
                    @search="searchProducts"
                    @toggle-filters="toggleFilters"
                    @quick-filter="(filter) => { activeFilters.status = filter.key; updateFilter('status', filter.key); }"
                />

                <div class="ml-auto">
                    <DataActionsBar
                        @export="(format) => console.log('Export:', format)"
                        @import="(format) => console.log('Import:', format)"
                    />
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
                :empty-title="t('products.empty_state.title')"
                :empty-description="t('products.empty_state.description')"
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
                    <button @click="openCreate" class="mt-4 btn btn-primary">{{ t('products.empty_state.create_first') }}</button>
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
            :active-currencies="active_currencies"
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
