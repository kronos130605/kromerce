<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    triggerClass: {
        type: String,
        default: ''
    },
    dropdownClass: {
        type: String,
        default: ''
    },
    width: {
        type: String,
        default: '280px'
    },
    position: {
        type: String,
        default: 'left', // 'left', 'right', 'center'
        validator: (value) => ['left', 'right', 'center'].includes(value)
    },
    triggerType: {
        type: String,
        default: 'click', // 'click', 'hover', 'both'
        validator: (value) => ['click', 'hover', 'both'].includes(value)
    },
    closeOnClickOutside: {
        type: Boolean,
        default: true
    },
    closeOnSelect: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['open', 'close', 'toggle']);

const isOpen = ref(false);
const dropdownRef = ref(null);

const positionClasses = computed(() => {
    switch (props.position) {
        case 'right':
            return 'right-0';
        case 'center':
            return 'left-1/2 -translate-x-1/2';
        case 'left':
        default:
            return 'left-0';
    }
});

const handleMouseEnter = () => {
    if (props.triggerType === 'hover' || props.triggerType === 'both') {
        open();
    }
};

const handleMouseLeave = () => {
    if (props.triggerType === 'hover' || props.triggerType === 'both') {
        close();
    }
};

const handleClick = () => {
    if (props.triggerType === 'click' || props.triggerType === 'both') {
        toggle();
    }
};

const open = () => {
    if (!isOpen.value) {
        isOpen.value = true;
        emit('open');
    }
};

const close = () => {
    if (isOpen.value) {
        isOpen.value = false;
        emit('close');
    }
};

const toggle = () => {
    isOpen.value = !isOpen.value;
    emit('toggle', isOpen.value);
};

// For external control
defineExpose({
    open,
    close,
    toggle,
    isOpen: computed(() => isOpen.value)
});
</script>

<template>
    <div
        ref="dropdownRef"
        class="relative inline-block"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
    >
        <!-- Trigger Slot -->
        <button
            type="button"
            @click="handleClick"
            :class="[
                'inline-flex items-center transition-colors',
                triggerClass
            ]"
        >
            <slot name="trigger" :isOpen="isOpen">
                <span class="text-sm font-medium">Menu</span>
                <svg
                    class="w-4 h-4 ml-1 transition-transform duration-200"
                    :class="{ 'rotate-180': isOpen }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </slot>
        </button>

        <!-- Dropdown Content -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="isOpen"
                :class="[
                    'absolute top-full z-50 mt-2 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800',
                    positionClasses,
                    dropdownClass
                ]"
                :style="{ width }"
                @click="closeOnSelect && close()"
            >
                <slot name="content" :close="close" />
            </div>
        </Transition>
    </div>
</template>
