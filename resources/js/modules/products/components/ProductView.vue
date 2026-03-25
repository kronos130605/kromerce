<script setup>
import { watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    isOpen: { type: Boolean, required: true },
    product: { type: Object, default: null },
});

const emit = defineEmits(['close', 'edit']);

// Lock body scroll
watch(() => props.isOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

const handleClose = () => emit('close');
const handleEdit = () => emit('edit', props.product);

const formatPrice = (price) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
const formatDate = (date) => date ? new Date(date).toLocaleDateString() : '-';

const getStatusColor = (status) => ({
    active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
}[status] || 'bg-yellow-100 text-yellow-800');
</script>

<template>
    <Teleport to="body">
        <Transition name="slider">
            <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="handleClose" />
                <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white dark:bg-gray-900 shadow-2xl flex flex-col" @click.stop>
                    <!-- Header -->
                    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('products.view.title') }}</h2>
                        <div class="flex items-center space-x-2">
                            <button @click="handleEdit" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="handleClose" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </header>

                    <!-- Content -->
                    <div v-if="product" class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Image -->
                        <div class="aspect-video bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center">
                            <span v-if="!product.images?.length" class="text-6xl">📦</span>
                            <img v-else :src="product.images[0].url" class="w-full h-full object-cover rounded-xl" />
                        </div>

                        <!-- Info -->
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ product.name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ product.description || t('products.no_description') }}</p>
                        </div>

                        <!-- Status & Price -->
                        <div class="flex items-center justify-between">
                            <span :class="['px-3 py-1 rounded-full text-sm font-medium capitalize', getStatusColor(product.status)]">
                                {{ product.status }}
                            </span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatPrice(product.price) }}</span>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.sku') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ product.sku || '-' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.barcode') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ product.barcode || '-' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.stock') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ product.stock_quantity }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.created') }}</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(product.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.slider-enter-active, .slider-leave-active { transition: all 0.3s ease; }
.slider-enter-from, .slider-leave-to { opacity: 0; }
.slider-enter-from > div:last-child, .slider-leave-to > div:last-child { transform: translateX(100%); }
</style>
