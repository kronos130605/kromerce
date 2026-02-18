<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

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

const stats = [
  { 
    number: 10000, 
    suffix: '+', 
    label: 'Negocios Activos', 
    description: 'Confían en Kromerce para crecer'
  },
  { 
    number: 50, 
    suffix: '+', 
    label: 'Países', 
    description: 'Presencia global con soporte local'
  },
  { 
    number: 99.9, 
    suffix: '%', 
    label: 'Uptime', 
    description: 'Disponibilidad garantizada'
  },
  { 
    number: 24, 
    suffix: '/7', 
    label: 'Soporte', 
    description: 'Ayuda cuando la necesitas'
  }
];

const animatedNumbers = ref(stats.map(() => 0));

const animateNumber = (index, target) => {
  const duration = 2000;
  const steps = 60;
  const increment = target / steps;
  let current = 0;
  
  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      current = target;
      clearInterval(timer);
    }
    animatedNumbers.value[index] = Math.floor(current);
  }, duration / steps);
};

onMounted(() => {
  checkDarkMode();
  // Listen for dark mode changes
  const observer = new MutationObserver(checkDarkMode);
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  });
  
  stats.forEach((stat, index) => {
    animateNumber(index, stat.number);
  });
});
</script>

<template>
  <section :class="['py-20', isDarkTheme ? 'bg-gray-900' : 'bg-gradient-to-br from-blue-50 to-cyan-50']">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 :class="['text-3xl md:text-4xl font-bold mb-4', isDarkTheme ? 'text-gray-100' : 'text-foreground']">
          Números que hablan por
          <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
            sí solos
          </span>
        </h2>
        <p :class="['text-xl max-w-3xl mx-auto', isDarkTheme ? 'text-gray-300' : 'text-muted-foreground']">
          El crecimiento de nuestra plataforma refleja el éxito de nuestros clientes. 
          Únete a miles de negocios que ya están transformando su futuro.
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div
          v-for="(stat, index) in stats"
          :key="stat.label"
          class="text-center group"
        >
          <div :class="['bg-white dark:bg-card rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 group-hover:-translate-y-2', isDarkTheme ? 'bg-gray-800' : 'bg-white']">
            <!-- Number -->
            <div class="text-4xl md:text-5xl font-bold text-primary mb-2">
              {{ animatedNumbers[index] }}{{ stat.suffix }}
            </div>
            
            <!-- Label -->
            <h3 :class="['text-xl font-semibold mb-2', isDarkTheme ? 'text-gray-100' : 'text-foreground']">
              {{ stat.label }}
            </h3>
            
            <!-- Description -->
            <p :class="[isDarkTheme ? 'text-gray-300' : 'text-muted-foreground']">
              {{ stat.description }}
            </p>
          </div>
        </div>
      </div>

      <!-- Additional Context -->
      <div class="mt-16 text-center">
        <div :class="['bg-white dark:bg-card rounded-2xl p-8 shadow-lg max-w-4xl mx-auto', isDarkTheme ? 'bg-gray-800' : 'bg-white']">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
              <div class="text-3xl font-bold text-green-600 mb-2">$2M+</div>
              <p :class="[isDarkTheme ? 'text-gray-300' : 'text-muted-foreground']">Procesados en transacciones</p>
            </div>
            <div>
              <div class="text-3xl font-bold text-blue-600 mb-2">4.9★</div>
              <p :class="[isDarkTheme ? 'text-gray-300' : 'text-muted-foreground']">Calificación promedio de clientes</p>
            </div>
            <div>
              <div class="text-3xl font-bold text-purple-600 mb-2">150ms</div>
              <p :class="[isDarkTheme ? 'text-gray-300' : 'text-muted-foreground']">Tiempo de respuesta promedio</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
