<script setup>
import { computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import BusinessLayout from '@/layouts/BusinessLayout.vue';
import { useProductManager } from '../composables/useProductManager.js';
import ProductModal from '../components/ProductModal.vue';
import ProductView from '../components/ProductView.vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    statistics: Object,
});

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
} = useProductManager({
    initialProducts: props.products?.data || [],
    initialFilters: props.filters,
});

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

const formatPrice = (price, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(price);
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

            <!-- Products Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ t('products.fields.product') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ t('products.fields.price') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ t('products.fields.status') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-lg">📦</div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ product.name }}</p>
                                        <p class="text-xs text-gray-500">{{ product.sku || '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ formatPrice(product.base_price, product.base_currency) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="['px-2 py-1 rounded-full text-xs font-medium capitalize', getStatusColor(product.status)]">
                                    {{ product.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="openView(product)" class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="openEdit(product)" class="p-1.5 text-gray-400 hover:text-green-600 dark:hover:text-green-400 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(product)" class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="!products.data?.length" class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-4xl mb-4">📦</div>
                <h3 class="text-base font-medium text-gray-900 dark:text-white">{{ t('products.empty.title') }}</h3>
                <button @click="openCreate" class="mt-4 btn btn-primary">{{ t('products.add_first') }}</button>
            </div>
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
