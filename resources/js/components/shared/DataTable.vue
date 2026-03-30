<script setup>
import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    // Data
    data: {
        type: Array,
        required: true,
        default: () => []
    },
    keyField: {
        type: String,
        default: 'id'
    },
    
    // Columns configuration
    columns: {
        type: Array,
        required: true,
        // Each column: { key: string, label: string, type: 'text'|'number'|'currency'|'date'|'badge'|'image'|'custom', 
        //              align?: 'left'|'center'|'right', width?: string, format?: function, 
        //              badgeColors?: object, currencyKey?: string, imageFallback?: string }
        default: () => []
    },
    
    // Actions
    actions: {
        type: Array,
        default: () => []
        // Each action: { key: string, icon: string, label: string, color: 'blue'|'green'|'red'|'yellow'|'gray',
        //               show?: function(item) => boolean, handler: function(item) }
    },
    showActions: {
        type: Boolean,
        default: true
    },
    actionsAlign: {
        type: String,
        default: 'right' // 'left' | 'center' | 'right'
    },
    
    // Loading & Empty states
    loading: {
        type: Boolean,
        default: false
    },
    emptyTitle: {
        type: String,
        default: ''
    },
    emptyDescription: {
        type: String,
        default: ''
    },
    emptyIcon: {
        type: String,
        default: '📦'
    },
    
    // Selection
    selectable: {
        type: Boolean,
        default: false
    },
    selectedItems: {
        type: Array,
        default: () => []
    },
    
    // Row styling
    rowClass: {
        type: [String, Function],
        default: ''
    },
    
    // Sorting
    sortable: {
        type: Boolean,
        default: false
    },
    sortKey: {
        type: String,
        default: ''
    },
    sortDirection: {
        type: String,
        default: 'asc' // 'asc' | 'desc'
    }
});

const emit = defineEmits([
    'action',
    'row-click',
    'selection-change',
    'sort'
]);

// Column type formatters
const formatters = {
    text: (value) => value ?? '-',
    number: (value) => {
        if (value === null || value === undefined) return '-';
        return new Intl.NumberFormat('en-US').format(value);
    },
    currency: (value, item, column) => {
        if (value === null || value === undefined || isNaN(parseFloat(value))) return '$0.00';
        const currency = column.currencyKey ? item[column.currencyKey] : 'USD';
        return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(value);
    },
    date: (value, item, column) => {
        if (!value) return '-';
        const date = new Date(value);
        return column.dateFormat ? date.toLocaleDateString(undefined, column.dateFormat) : date.toLocaleDateString();
    },
    datetime: (value) => {
        if (!value) return '-';
        return new Date(value).toLocaleString();
    },
    badge: (value, item, column) => {
        const colors = column.badgeColors || {};
        return colors[value] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    },
    boolean: (value) => value ? '✓' : '✗'
};

// Computed
const hasData = computed(() => props.data.length > 0);
const allSelected = computed(() => props.data.length > 0 && props.selectedItems.length === props.data.length);

// Methods
const getCellValue = (item, column) => {
    const value = column.key.includes('.') 
        ? column.key.split('.').reduce((obj, key) => obj?.[key], item)
        : item[column.key];
    
    if (column.format) {
        return column.format(value, item, column);
    }
    
    const formatter = formatters[column.type || 'text'];
    return formatter ? formatter(value, item, column) : value;
};

const getCellClass = (column) => {
    const align = column.align || 'left';
    return `text-${align}`;
};

const getRowClass = (item) => {
    if (typeof props.rowClass === 'function') {
        return props.rowClass(item);
    }
    return props.rowClass;
};

const handleActionClick = (action, item, event) => {
    event.stopPropagation();
    emit('action', { action: action.key, item });
    if (action.handler) {
        action.handler(item);
    }
};

const handleRowClick = (item) => {
    emit('row-click', item);
};

const toggleSelection = (item) => {
    const key = item[props.keyField];
    const index = props.selectedItems.indexOf(key);
    const newSelection = [...props.selectedItems];
    
    if (index === -1) {
        newSelection.push(key);
    } else {
        newSelection.splice(index, 1);
    }
    
    emit('selection-change', newSelection);
};

const toggleAllSelection = () => {
    if (allSelected.value) {
        emit('selection-change', []);
    } else {
        emit('selection-change', props.data.map(item => item[props.keyField]));
    }
};

const handleSort = (column) => {
    if (!props.sortable || !column.sortable) return;
    emit('sort', column.key);
};

const getActionButtonClass = (action) => {
    const colorMap = {
        blue: 'text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30',
        green: 'text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30',
        red: 'text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30',
        yellow: 'text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/30',
        gray: 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50'
    };
    return colorMap[action.color] || colorMap.gray;
};

// Default icons for actions
const defaultIcons = {
    view: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
    edit: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
    delete: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    duplicate: 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <!-- Selection Header -->
                        <th v-if="selectable" class="px-4 py-3 w-10">
                            <input 
                                type="checkbox" 
                                :checked="allSelected"
                                @change="toggleAllSelection"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                            />
                        </th>
                        
                        <!-- Column Headers -->
                        <th 
                            v-for="column in columns" 
                            :key="column.key"
                            class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase"
                            :class="[getCellClass(column), { 'cursor-pointer hover:text-gray-700 dark:hover:text-gray-300': sortable && column.sortable }]"
                            :style="column.width ? { width: column.width } : {}"
                            @click="handleSort(column)"
                        >
                            <div class="flex items-center gap-1">
                                {{ column.label || t(`table.columns.${column.key}`) }}
                                <span v-if="sortable && column.sortable && sortKey === column.key" class="ml-1">
                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            </div>
                        </th>
                        
                        <!-- Actions Header -->
                        <th v-if="showActions && actions.length" 
                            class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase"
                            :class="`text-${actionsAlign}`"
                        >
                            {{ t('common.actions') }}
                        </th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Loading State -->
                    <tr v-if="loading">
                        <td :colspan="columns.length + (selectable ? 1 : 0) + (actions.length ? 1 : 0)" class="px-4 py-12 text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ t('common.loading') }}</p>
                        </td>
                    </tr>
                    
                    <!-- Data Rows -->
                    <tr 
                        v-else-if="hasData"
                        v-for="item in data" 
                        :key="item[keyField]"
                        class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                        :class="getRowClass(item)"
                        @click="handleRowClick(item)"
                    >
                        <!-- Selection Cell -->
                        <td v-if="selectable" class="px-4 py-3" @click.stop>
                            <input 
                                type="checkbox" 
                                :checked="selectedItems.includes(item[keyField])"
                                @change="toggleSelection(item)"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                            />
                        </td>
                        
                        <!-- Data Cells -->
                        <td 
                            v-for="column in columns" 
                            :key="column.key"
                            class="px-4 py-3"
                            :class="getCellClass(column)"
                        >
                            <!-- Custom slot if provided -->
                            <slot 
                                v-if="column.type === 'custom' || $slots[`cell-${column.key}`]" 
                                :name="`cell-${column.key}`" 
                                :item="item" 
                                :column="column"
                                :value="item[column.key]"
                            />
                            
                            <!-- Badge type -->
                            <span 
                                v-else-if="column.type === 'badge'"
                                :class="['px-2 py-1 rounded-full text-xs font-medium capitalize', getCellValue(item, column)]"
                            >
                                {{ item[column.key] }}
                            </span>
                            
                            <!-- Image type -->
                            <div v-else-if="column.type === 'image'" class="flex items-center">
                                <img 
                                    :src="item[column.key] || column.imageFallback || '📦'"
                                    :alt="item[column.labelKey || 'name'] || ''"
                                    class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-gray-700"
                                    @error="$event.target.src = column.imageFallback || ''"
                                />
                            </div>
                            
                            <!-- Image with text -->
                            <div v-else-if="column.type === 'image-text'" class="flex items-center">
                                <img 
                                    v-if="item[column.imageKey || 'image']"
                                    :src="item[column.imageKey || 'image']"
                                    :alt="item[column.labelKey || 'name'] || ''"
                                    class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-gray-700"
                                    @error="$event.target.style.display='none'"
                                />
                                <div v-else class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-lg">
                                    {{ column.fallbackIcon || '📦' }}
                                </div>
                                <div class="ml-3" v-if="column.labelKey">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item[column.labelKey] }}</p>
                                    <p v-if="column.subLabelKey" class="text-xs text-gray-500">{{ item[column.subLabelKey] || '-' }}</p>
                                </div>
                            </div>
                            
                            <!-- Currency type -->
                            <span v-else-if="column.type === 'currency'" class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ getCellValue(item, column) }}
                            </span>
                            
                            <!-- Boolean type -->
                            <span v-else-if="column.type === 'boolean'" :class="item[column.key] ? 'text-green-600' : 'text-red-600'">
                                {{ getCellValue(item, column) }}
                            </span>
                            
                            <!-- Default text -->
                            <span v-else class="text-sm text-gray-900 dark:text-white">
                                {{ getCellValue(item, column) }}
                            </span>
                        </td>
                        
                        <!-- Actions Cell -->
                        <td v-if="showActions && actions.length" 
                            class="px-4 py-3"
                            :class="`text-${actionsAlign}`"
                        >
                            <div class="flex items-center gap-1" :class="actionsAlign === 'right' ? 'justify-end' : (actionsAlign === 'center' ? 'justify-center' : 'justify-start')">
                                <button
                                    v-for="action in actions"
                                    :key="action.key"
                                    v-show="!action.show || action.show(item)"
                                    @click="handleActionClick(action, item, $event)"
                                    :title="action.label || t(`common.actions.${action.key}`)"
                                    class="p-1.5 rounded-lg transition-colors"
                                    :class="getActionButtonClass(action)"
                                >
                                    <!-- Custom icon slot -->
                                    <slot v-if="$slots[`action-${action.key}`]" :name="`action-${action.key}`" :action="action" :item="item" />
                                    
                                    <!-- Default SVG icon -->
                                    <svg v-else-if="action.icon || defaultIcons[action.key]" 
                                        class="w-4 h-4" 
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="action.icon || defaultIcons[action.key]" />
                                    </svg>
                                    
                                    <!-- Text fallback -->
                                    <span v-else class="text-xs">{{ action.label || action.key }}</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Empty State -->
                    <tr v-else>
                        <td :colspan="columns.length + (selectable ? 1 : 0) + (actions.length ? 1 : 0)" class="px-4 py-12">
                            <slot name="empty">
                                <div class="text-center">
                                    <div class="text-4xl mb-4">{{ emptyIcon }}</div>
                                    <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ emptyTitle || t('table.empty.title') }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ emptyDescription || t('table.empty.description') }}
                                    </p>
                                    <slot name="empty-action" />
                                </div>
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Slot -->
        <slot name="pagination" v-if="hasData" />
    </div>
</template>
