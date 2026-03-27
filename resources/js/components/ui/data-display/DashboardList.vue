<script setup>
import { Link } from '@inertiajs/vue3';
import SkeletonLoader from '../feedback/SkeletonLoader.vue';
import EmptyState from '../feedback/EmptyState.vue';

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    loading: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    description: {
        type: String,
        default: ''
    },
    viewAllLink: {
        type: String,
        default: ''
    },
    viewAllText: {
        type: String,
        default: 'Ver todos'
    },
    emptyIcon: {
        type: String,
        default: '📭'
    },
    emptyTitle: {
        type: String,
        default: 'No hay elementos'
    },
    emptyDescription: {
        type: String,
        default: ''
    },
    emptyActionLink: {
        type: String,
        default: ''
    },
    emptyActionText: {
        type: String,
        default: ''
    },
    skeletonRows: {
        type: Number,
        default: 3
    }
});

const emit = defineEmits(['itemClick']);
</script>

<template>
    <div class="space-y-4">
        <!-- Header -->
        <div v-if="title || $slots.header" class="flex items-center justify-between">
            <div v-if="title">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h3>
                <p v-if="description" class="text-gray-600 dark:text-gray-300 mt-1">{{ description }}</p>
            </div>
            <slot name="header" />

            <Link v-if="viewAllLink" :href="viewAllLink"
                  class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center space-x-1">
                <span>{{ viewAllText }}</span>
                <span>→</span>
            </Link>
        </div>

        <!-- Loading State -->
        <SkeletonLoader v-if="loading" type="card" :rows="skeletonRows" />

        <!-- Empty State -->
        <EmptyState v-else-if="items.length === 0"
                    variant="card"
                    :icon="emptyIcon"
                    :title="emptyTitle"
                    :description="emptyDescription"
                    :actionLink="emptyActionLink"
                    :actionText="emptyActionText" />

        <!-- Content List -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="space-y-3">
                <div v-for="(item, index) in items" :key="item.id || index"
                     @click="emit('itemClick', item)"
                     :class="[
                         'flex items-center justify-between p-4 rounded-xl transition-colors cursor-pointer',
                         $slots.item ? '' : 'bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600'
                     ]">
                    <!-- Default Item Layout -->
                    <template v-if="!$slots.item">
                        <div class="flex items-center space-x-4 flex-1">
                            <!-- Rank/Number (optional) -->
                            <div v-if="item.rank" class="w-8 h-8 bg-gradient-to-br from-blue-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ item.rank }}
                            </div>

                            <!-- Icon/Image -->
                            <div v-if="item.icon || item.image" class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center text-2xl flex-shrink-0">
                                {{ item.icon || item.image }}
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white truncate">{{ item.title || item.name }}</h4>
                                    <span v-if="item.badge" :class="item.badge.class">{{ item.badge.text }}</span>
                                </div>
                                <p v-if="item.subtitle || item.description" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ item.subtitle || item.description }}
                                </p>

                                <!-- Metadata row -->
                                <div v-if="item.metadata" class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    <span v-for="(meta, i) in item.metadata" :key="i">{{ meta }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right side: value/actions -->
                        <div class="flex items-center space-x-4">
                            <div v-if="item.value || item.amount" class="text-right">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ item.value || item.amount }}</p>
                                <p v-if="item.change" :class="item.change.class || 'text-xs'">{{ item.change.text }}</p>
                            </div>

                            <div v-if="item.status" :class="item.status.class">
                                {{ item.status.text }}
                            </div>

                            <!-- Action button -->
                            <button v-if="item.action"
                                    @click.stop="item.action.handler"
                                    :class="item.action.class || 'p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30'">
                                <span v-if="item.action.icon">{{ item.action.icon }}</span>
                                <span v-else>→</span>
                            </button>
                        </div>
                    </template>

                    <!-- Custom Item Slot -->
                    <slot v-else name="item" :item="item" :index="index" />
                </div>
            </div>

            <!-- Footer slot -->
            <slot name="footer" />
        </div>
    </div>
</template>
