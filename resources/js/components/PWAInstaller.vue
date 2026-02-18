<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const deferredPrompt = ref(null);
const showInstallBanner = ref(false);
const isInstalled = ref(false);

const installApp = async () => {
  if (!deferredPrompt.value) return;
  
  deferredPrompt.value.prompt();
  const { outcome } = await deferredPrompt.value.userChoice;
  
  if (outcome === 'accepted') {
    console.log('User accepted the install prompt');
  } else {
    console.log('User dismissed the install prompt');
  }
  
  deferredPrompt.value = null;
  showInstallBanner.value = false;
};

const dismissBanner = () => {
  showInstallBanner.value = false;
  localStorage.setItem('pwa_banner_dismissed', 'true');
};

const checkInstallStatus = () => {
  // Check if app is running in standalone mode
  if (window.matchMedia('(display-mode: standalone)').matches) {
    isInstalled.value = true;
  }
  
  // Check if banner was previously dismissed
  const dismissed = localStorage.getItem('pwa_banner_dismissed');
  if (dismissed === 'true') {
    showInstallBanner.value = false;
  }
};

onMounted(() => {
  checkInstallStatus();
  
  // Listen for beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt.value = e;
    showInstallBanner.value = true;
  });
  
  // Listen for app installed event
  window.addEventListener('appinstalled', () => {
    console.log('PWA was installed');
    isInstalled.value = true;
    showInstallBanner.value = false;
    deferredPrompt.value = null;
  });
  
  // Listen for display mode changes
  window.matchMedia('(display-mode: standalone)').addEventListener('change', (e) => {
    if (e.matches) {
      isInstalled.value = true;
    }
  });
});

onBeforeUnmount(() => {
  // Clean up event listeners
  window.removeEventListener('beforeinstallprompt', () => {});
  window.removeEventListener('appinstalled', () => {});
});
</script>

<template>
  <!-- Install Banner -->
  <div
    v-if="showInstallBanner && !isInstalled"
    class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-background border border-border rounded-lg shadow-lg p-4 z-50 transition-all duration-300"
  >
    <div class="flex items-start gap-3">
      <div class="flex-shrink-0">
        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
          <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </div>
      </div>
      
      <div class="flex-1 min-w-0">
        <h3 class="text-sm font-semibold text-foreground mb-1">
          {{ t('pwa.install_title', 'Instalar Kromerce') }}
        </h3>
        <p class="text-xs text-muted-foreground mb-3">
          {{ t('pwa.install_description', 'Instala nuestra aplicación para un acceso rápido y mejor experiencia') }}
        </p>
        
        <div class="flex gap-2">
          <button
            @click="installApp"
            class="px-3 py-1.5 bg-primary text-primary-foreground text-xs font-medium rounded hover:bg-primary/90 transition-colors"
          >
            {{ t('pwa.install_button', 'Instalar') }}
          </button>
          <button
            @click="dismissBanner"
            class="px-3 py-1.5 bg-muted text-muted-foreground text-xs font-medium rounded hover:bg-muted/80 transition-colors"
          >
            {{ t('common.cancel', 'Cancelar') }}
          </button>
        </div>
      </div>
      
      <button
        @click="dismissBanner"
        class="flex-shrink-0 p-1 rounded hover:bg-accent transition-colors"
      >
        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>
