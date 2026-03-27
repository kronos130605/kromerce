<template>
  <svg
    v-if="iconData"
    :viewBox="iconData.viewBox"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    :class="[iconClass, customClass]"
    v-bind="iconProps"
  >
    <path :d="iconData.svg" />
  </svg>
  <span v-else class="inline-block" :class="[iconClass, customClass]">?</span>
</template>

<script setup>
import { computed } from 'vue';
import { getIcon } from '@/icons/index.js';

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  category: {
    type: String,
    default: 'all',
    validator: (value) => ['all', 'customer', 'business', 'ui'].includes(value)
  },
  size: {
    type: String,
    default: null
  },
  customClass: {
    type: String,
    default: ''
  }
});

const iconData = computed(() => {
  const icon = getIcon(props.name, props.category);
  if (!icon) {
    console.warn(`Icon "${props.name}" not found in category "${props.category}"`);
    return null;
  }
  return icon;
});

const iconClass = computed(() => {
    return props.size || iconData.value?.size || 'w-5 h-5';
});

const iconProps = computed(() => ({
  'aria-hidden': true,
  'focusable': false
}));
</script>
