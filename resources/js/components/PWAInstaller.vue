<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// State
const deferredPrompt = ref(null); // beforeinstallprompt event (Chrome/Brave)
const showInstallBanner = ref(false); // controls visibility of the banner
const isInstalled = ref(false); // app already installed / standalone
const beforeInstallPromptHandled = ref(false); // ensure we only handle first event

const installApp = async () => {
  const isFirefox = navigator.userAgent.toLowerCase().includes('firefox');
  
  if (isFirefox) {
    // Firefox no dispara beforeinstallprompt de forma estándar.
    alert('Para instalar en Firefox:\n1. Haz clic en el ícono de página (📄) en la barra de direcciones.\n2. Elige "Instalar" o "Instalar aplicación".');
    // Ocultamos el banner para no molestar.
    showInstallBanner.value = false;
    localStorage.setItem('pwa_banner_dismissed', 'true');
    return;
  }

  if (!deferredPrompt.value) {
    console.log('⚠️ No hay beforeinstallprompt diferido disponible.');
    return;
  }

  // Chrome / Brave: mostrar el prompt nativo
  deferredPrompt.value.prompt();
  const { outcome } = await deferredPrompt.value.userChoice;

  console.log('📱 Resultado de userChoice:', outcome);

  if (outcome === 'accepted') {
    isInstalled.value = true;
  }

  deferredPrompt.value = null;
  showInstallBanner.value = false;
};

const dismissBanner = () => {
  showInstallBanner.value = false;
  localStorage.setItem('pwa_banner_dismissed', 'true');
};

const checkInstallStatus = () => {
  // 1) Si ya está en modo standalone, marcamos como instalado y no mostramos nada
  if (window.matchMedia('(display-mode: standalone)').matches) {
    isInstalled.value = true;
    showInstallBanner.value = false;
    return;
  }

  // 2) Si el usuario ya descartó el banner, no lo volvemos a mostrar
  const dismissed = localStorage.getItem('pwa_banner_dismissed') === 'true';
  if (dismissed) {
    showInstallBanner.value = false;
    return;
  }

  const ua = navigator.userAgent.toLowerCase();
  const isFirefox = ua.includes('firefox');

  console.log('🔍 checkInstallStatus', { isFirefox, dismissed });

  // 3) En Firefox, no hay beforeinstallprompt estándar.
  //    Mostramos un banner manual siempre que no esté instalado ni descartado.
  if (isFirefox && !isInstalled.value) {
    showInstallBanner.value = true;
  }
};

onMounted(() => {
  checkInstallStatus();

  // Navegadores tipo Chrome/Brave: gestionan beforeinstallprompt
  window.addEventListener('beforeinstallprompt', (e) => {
    console.log('📱 beforeinstallprompt event fired');
    e.preventDefault();

    const dismissed = localStorage.getItem('pwa_banner_dismissed') === 'true';

    // Si ya manejamos el primer evento o el usuario lo descartó, no hacemos nada
    if (beforeInstallPromptHandled.value || dismissed) {
      console.log('📱 Ignored beforeinstallprompt', {
        beforeInstallPromptHandled: beforeInstallPromptHandled.value,
        dismissed,
      });
      return;
    }

    // Guardamos el evento diferido y mostramos UN solo banner
    deferredPrompt.value = e;
    beforeInstallPromptHandled.value = true;
    showInstallBanner.value = true;

    console.log('📱 Banner ON from beforeinstallprompt', {
      showInstallBanner: showInstallBanner.value,
      hasDeferredPrompt: !!deferredPrompt.value,
    });
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
    class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-gray-900 border-2 border-blue-500 rounded-lg shadow-2xl p-4 z-[9999] transition-all duration-300"
  >
    <div class="flex items-start gap-3">
      <div class="flex-shrink-0">
        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </div>
      </div>
      
      <div class="flex-1 min-w-0">
        <h3 class="text-sm font-semibold text-white mb-1">
          {{ t('pwa.install_title', 'Instalar Kromerce') }}
        </h3>
        <p class="text-xs text-gray-300 mb-3">
          {{ t('pwa.install_description', 'Instala nuestra aplicación para un acceso rápido y mejor experiencia') }}
        </p>
        
        <div class="flex gap-2">
          <button
            @click="installApp"
            class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors"
          >
            {{ t('pwa.install_button', 'Instalar') }}
          </button>
          <button
            @click="dismissBanner"
            class="px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors"
          >
            {{ t('common.cancel', 'Cancelar') }}
          </button>
        </div>
      </div>
      
      <button
        @click="dismissBanner"
        class="flex-shrink-0 p-1 rounded hover:bg-gray-700 transition-colors"
      >
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>
</template>
