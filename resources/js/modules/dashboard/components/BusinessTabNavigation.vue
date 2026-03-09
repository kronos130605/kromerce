<script setup>
import { computed } from 'vue';
import { useDarkMode } from '@/composables/useDarkMode';

const props = defineProps({
    activeTab: {
        type: String,
        required: true
    },
    tabOptions: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['tab-changed']);
const { isDark } = useDarkMode();

const handleTabChange = (tabKey) => {
    emit('tab-changed', tabKey);
};

const tabClasses = (isActive) => {
    const baseClasses = 'py-4 px-1 border-b-2 font-medium text-sm transition-colors';
    
    if (isDark.value) {
        return isActive
            ? `${baseClasses} border-blue-500 text-blue-400`
            : `${baseClasses} border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-600`;
    } else {
        return isActive
            ? `${baseClasses} border-blue-500 text-blue-600`
            : `${baseClasses} border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300`;
    }
};

const containerClasses = computed(() => {
    return isDark.value
        ? 'bg-gray-900 rounded-2xl shadow-lg border border-gray-700'
        : 'bg-white rounded-2xl shadow-lg border border-gray-100';
});

const borderClasses = computed(() => {
    return isDark.value
        ? 'border-b border-gray-700'
        : 'border-b border-gray-200';
});

const contentClasses = computed(() => {
    return 'p-6';
});
</script>

<template>
    <div :class="containerClasses">
        <div :class="borderClasses">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button
                    v-for="tab in tabOptions"
                    :key="tab.key"
                    @click="handleTabChange(tab.key)"
                    :class="tabClasses(activeTab === tab.key)"
                >
                    {{ tab.label }}
                </button>
            </nav>
        </div>

        <!-- Tab Content Slot -->
        <div :class="contentClasses">
            <slot />
        </div>
    </div>
</template>
