<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

const languages = [
  { code: 'es', name: 'Español', flag: '🇪🇸' },
  { code: 'en', name: 'English', flag: '🇺🇸' }
];

const currentLanguage = computed(() => 
  languages.find(lang => lang.code === locale.value) || languages[0]
);

const changeLanguage = (langCode) => {
  locale.value = langCode;
  localStorage.setItem('kromerce_locale', langCode);
};
</script>

<template>
  <div class="relative group">
    <button
      class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-accent transition-colors"
      :title="currentLanguage.name"
    >
      <span class="text-lg">{{ currentLanguage.flag }}</span>
      <span class="text-sm font-medium hidden sm:inline">{{ currentLanguage.code.toUpperCase() }}</span>
      <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    
    <div class="absolute right-0 mt-2 w-48 bg-background border border-border rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
      <div class="py-1">
        <button
          v-for="language in languages"
          :key="language.code"
          @click="changeLanguage(language.code)"
          class="w-full flex items-center gap-3 px-4 py-2 text-left hover:bg-accent transition-colors"
          :class="{ 'bg-accent': language.code === locale }"
        >
          <span class="text-lg">{{ language.flag }}</span>
          <div class="flex-1">
            <div class="font-medium">{{ language.name }}</div>
            <div class="text-xs text-muted-foreground">{{ language.code.toUpperCase() }}</div>
          </div>
          <svg 
            v-if="language.code === locale" 
            class="w-4 h-4 text-primary" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
