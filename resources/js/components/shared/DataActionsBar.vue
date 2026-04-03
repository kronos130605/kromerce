<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { 
    ArrowDownTrayIcon,
    ArrowUpTrayIcon,
    DocumentIcon,
    DocumentTextIcon,
    CodeBracketIcon,
    EllipsisVerticalIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    showImport: {
        type: Boolean,
        default: true
    },
    showExport: {
        type: Boolean,
        default: true
    },
    exportFormats: {
        type: Array,
        default: () => ['excel', 'pdf', 'xml']
    },
    importFormats: {
        type: Array,
        default: () => ['excel', 'xml']
    },
    loading: {
        type: Boolean,
        default: false
    },
    compact: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits([
    'export',
    'import'
]);

const showExportMenu = ref(false);
const showImportMenu = ref(false);
const showCompactMenu = ref(false);
const containerRef = ref(null);

const formatIcons = {
    excel: DocumentIcon,
    pdf: DocumentTextIcon,
    xml: CodeBracketIcon
};

const formatLabels = {
    excel: 'Excel',
    pdf: 'PDF',
    xml: 'XML'
};

const hasAnyAction = computed(() => 
    (props.showExport && props.exportFormats.length > 0) || 
    (props.showImport && props.importFormats.length > 0)
);

const allActions = computed(() => {
    const actions = [];
    if (props.showImport) {
        props.importFormats.forEach(format => {
            actions.push({ type: 'import', format, icon: formatIcons[format], label: `Importar ${formatLabels[format]}` });
        });
    }
    if (props.showExport) {
        props.exportFormats.forEach(format => {
            actions.push({ type: 'export', format, icon: formatIcons[format], label: `Exportar ${formatLabels[format]}` });
        });
    }
    return actions;
});

const handleExport = (format) => {
    emit('export', format);
    showExportMenu.value = false;
};

const handleImport = (format) => {
    emit('import', format);
    showImportMenu.value = false;
};

const handleCompactAction = (action) => {
    if (action.type === 'export') {
        emit('export', action.format);
    } else {
        emit('import', action.format);
    }
    showCompactMenu.value = false;
};

// Click outside handler
const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        showImportMenu.value = false;
        showExportMenu.value = false;
        showCompactMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="containerRef" class="flex items-center gap-2">
        <!-- Desktop: Separate buttons -->
        <template v-if="!compact">
            <!-- Import Dropdown -->
            <div v-if="showImport && importFormats.length" class="relative">
                <button
                    @click="showImportMenu = !showImportMenu; showExportMenu = false"
                    @blur="setTimeout(() => showImportMenu = false, 200)"
                    :class="[
                        'inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border transition-colors',
                        showImportMenu
                            ? 'bg-gray-100 dark:bg-gray-700 border-gray-400 dark:border-gray-500 text-gray-900 dark:text-white'
                            : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                    ]"
                    :disabled="loading"
                >
                    <ArrowUpTrayIcon class="w-4 h-4" />
                    <span class="hidden sm:inline">Importar</span>
                </button>
                
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <div 
                        v-if="showImportMenu" 
                        class="absolute top-full right-0 mt-1 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10 origin-top-right"
                    >
                        <button
                            v-for="fmt in importFormats"
                            :key="'import-'+fmt"
                            @click="handleImport(fmt)"
                            class="w-full px-3 py-2 text-sm text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"
                        >
                            <component :is="formatIcons[fmt]" class="w-4 h-4" />
                            {{ formatLabels[fmt] }}
                        </button>
                    </div>
                </Transition>
            </div>

            <!-- Export Dropdown -->
            <div v-if="showExport && exportFormats.length" class="relative">
                <button
                    @click="showExportMenu = !showExportMenu; showImportMenu = false"
                    @blur="setTimeout(() => showExportMenu = false, 200)"
                    :class="[
                        'inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border transition-colors',
                        showExportMenu
                            ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-400'
                            : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                    ]"
                    :disabled="loading"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                    <span class="hidden sm:inline">Exportar</span>
                </button>
                
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <div 
                        v-if="showExportMenu" 
                        class="absolute top-full right-0 mt-1 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10 origin-top-right"
                    >
                        <button
                            v-for="fmt in exportFormats"
                            :key="'export-'+fmt"
                            @click="handleExport(fmt)"
                            class="w-full px-3 py-2 text-sm text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"
                        >
                            <component :is="formatIcons[fmt]" class="w-4 h-4" />
                            {{ formatLabels[fmt] }}
                        </button>
                    </div>
                </Transition>
            </div>
        </template>

        <!-- Mobile/Compact: Single button -->
        <template v-else-if="hasAnyAction">
            <div class="relative">
                <button
                    @click="showCompactMenu = !showCompactMenu"
                    @blur="setTimeout(() => showCompactMenu = false, 200)"
                    :class="[
                        'inline-flex items-center justify-center w-9 h-9 rounded-lg border transition-colors',
                        showCompactMenu
                            ? 'bg-gray-100 dark:bg-gray-700 border-gray-400 dark:border-gray-500 text-gray-900 dark:text-white'
                            : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
                    ]"
                    :disabled="loading"
                    title="Acciones"
                >
                    <EllipsisVerticalIcon class="w-5 h-5" />
                </button>
                
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <div 
                        v-if="showCompactMenu" 
                        class="absolute top-full right-0 mt-1 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10 origin-top-right"
                    >
                        <div v-if="showImport && importFormats.length" class="border-b border-gray-100 dark:border-gray-700 pb-1 mb-1">
                            <div class="px-3 py-1 text-xs text-gray-500 dark:text-gray-400 font-medium">Importar</div>
                            <button
                                v-for="fmt in importFormats"
                                :key="'import-'+fmt"
                                @click="handleCompactAction({ type: 'import', format: fmt })"
                                class="w-full px-3 py-2 text-sm text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"
                            >
                                <component :is="formatIcons[fmt]" class="w-4 h-4" />
                                {{ formatLabels[fmt] }}
                            </button>
                        </div>
                        <div v-if="showExport && exportFormats.length">
                            <div class="px-3 py-1 text-xs text-gray-500 dark:text-gray-400 font-medium">Exportar</div>
                            <button
                                v-for="fmt in exportFormats"
                                :key="'export-'+fmt"
                                @click="handleCompactAction({ type: 'export', format: fmt })"
                                class="w-full px-3 py-2 text-sm text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors"
                            >
                                <component :is="formatIcons[fmt]" class="w-4 h-4" />
                                {{ formatLabels[fmt] }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </template>
    </div>
</template>
