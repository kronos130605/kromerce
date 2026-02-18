<script setup>
import { ref, onMounted } from 'vue';

const isDarkTheme = ref(false);

// Check for dark mode changes
const checkDarkMode = () => {
  isDarkTheme.value = document.documentElement.classList.contains('dark');
};

onMounted(() => {
  checkDarkMode();
  // Listen for dark mode changes
  const observer = new MutationObserver(checkDarkMode);
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  });
});

const benefits = [
  'Configuración en menos de 5 minutos',
  'Sin tarjetas de crédito requeridas',
  'Cancela cuando quieras',
  'Soporte 24/7 desde día 1'
];

const formData = ref({
  name: '',
  email: '',
  business: '',
  message: ''
});

const isSubmitting = ref(false);

const submitForm = async () => {
  isSubmitting.value = true;

  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 2000));

  // Reset form
  formData.value = {
    name: '',
    email: '',
    business: '',
    message: ''
  };

  isSubmitting.value = false;

  // Show success message (in real app, would use toast/notification)
  alert('¡Gracias por contactarnos! Te responderemos en menos de 24 horas.');
};
</script>

<template>
  <section :class="['py-20', isDarkTheme ? 'bg-gray-900' : 'bg-gradient-to-br from-blue-600 to-cyan-600']">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
          <h2 :class="['text-3xl md:text-4xl font-bold mb-4', isDarkTheme ? 'text-gray-100' : 'text-white']">
            ¿Listo para transformar tu
            <span :class="isDarkTheme ? 'text-yellow-400' : 'text-yellow-300'">
              negocio hoy?
            </span>
          </h2>
          <p :class="['text-xl max-w-2xl mx-auto', isDarkTheme ? 'text-gray-300' : 'text-white/90']">
            Únete a miles de negocios que ya están vendiendo más,
            gestionando mejor y creciendo más rápido con Kromerce.
          </p>
        </div>

        <!-- Benefits -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
          <div
            v-for="benefit in benefits"
            :key="benefit"
            class="text-center"
          >
            <div :class="['w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3', isDarkTheme ? 'bg-gray-700' : 'bg-white/20']">
              <svg :class="['w-6 h-6', isDarkTheme ? 'text-yellow-400' : 'text-yellow-300']" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <p :class="[isDarkTheme ? 'text-gray-300' : 'text-white/90']">{{ benefit }}</p>
          </div>
        </div>

        <!-- Contact Form -->
        <div :class="['bg-white/10 backdrop-blur-sm rounded-2xl p-8 border', isDarkTheme ? 'border-gray-700' : 'border-white/20']">
          <h3 :class="['text-2xl font-bold mb-6 text-center', isDarkTheme ? 'text-gray-100' : 'text-white']">
            Comienza tu prueba gratuita
          </h3>

          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label :class="['block text-sm font-medium mb-2', isDarkTheme ? 'text-gray-100' : 'text-white']">Nombre Completo</label>
                <input
                  v-model="formData.name"
                  type="text"
                  required
                  :class="['w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent', isDarkTheme ? 'bg-gray-800/50 border-gray-700 text-gray-100 placeholder-gray-400 focus:ring-yellow-400' : 'bg-white/20 border-white/30 text-white placeholder-white/60 focus:ring-yellow-300']"
                  placeholder="Tu nombre"
                />
              </div>

              <div>
                <label :class="['block text-sm font-medium mb-2', isDarkTheme ? 'text-gray-100' : 'text-white']">Email</label>
                <input
                  v-model="formData.email"
                  type="email"
                  required
                  :class="['w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent', isDarkTheme ? 'bg-gray-800/50 border-gray-700 text-gray-100 placeholder-gray-400 focus:ring-yellow-400' : 'bg-white/20 border-white/30 text-white placeholder-white/60 focus:ring-yellow-300']"
                  placeholder="tu@email.com"
                />
              </div>
            </div>

            <div>
              <label :class="['block text-sm font-medium mb-2', isDarkTheme ? 'text-gray-100' : 'text-white']">Nombre del Negocio</label>
              <input
                v-model="formData.business"
                type="text"
                required
                :class="['w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent', isDarkTheme ? 'bg-gray-800/50 border-gray-700 text-gray-100 placeholder-gray-400 focus:ring-yellow-400' : 'bg-white/20 border-white/30 text-white placeholder-white/60 focus:ring-yellow-300']"
                placeholder="Tu negocio"
              />
            </div>

            <div>
              <label :class="['block text-sm font-medium mb-2', isDarkTheme ? 'text-gray-100' : 'text-white']">Mensaje (Opcional)</label>
              <textarea
                v-model="formData.message"
                rows="4"
                :class="['w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent resize-none', isDarkTheme ? 'bg-gray-800/50 border-gray-700 text-gray-100 placeholder-gray-400 focus:ring-yellow-400' : 'bg-white/20 border-white/30 text-white placeholder-white/60 focus:ring-yellow-300']"
                placeholder="Cuéntanos sobre tu negocio..."
              />
            </div>

            <button
              type="submit"
              :disabled="isSubmitting"
              :class="['w-full py-4 px-6 rounded-lg transition-colors font-bold text-lg disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer', isDarkTheme ? 'bg-yellow-500 text-gray-900 hover:bg-yellow-400' : 'bg-yellow-400 text-gray-900 hover:bg-yellow-300']"
            >
              <span v-if="isSubmitting">Enviando...</span>
              <span v-else>Comenzar Gratis →</span>
            </button>
          </form>

          <!-- Trust Indicators -->
          <div :class="['mt-8 pt-8 border-t', isDarkTheme ? 'border-gray-700' : 'border-white/20']">
            <div :class="['flex flex-wrap justify-center items-center gap-8 text-sm', isDarkTheme ? 'text-gray-400' : 'text-white/70']">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                <span>Seguro y encriptado</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
                <span>Respuesta en 24h</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span :class="[isDarkTheme ? 'text-gray-300' : 'text-white']">Sin compromiso</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
