<script setup>
import { ref, onMounted } from 'vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';

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

const floatingCards = [
  {
    title: 'Dashboard Analytics',
    value: '$24,500',
    change: '+12%',
    icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    color: 'blue'
  },
  {
    title: 'Active Users',
    value: '1,234',
    change: '+8%',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    color: 'purple'
  },
  {
    title: 'Conversion Rate',
    value: '3.2%',
    change: '+15%',
    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    color: 'green'
  }
];

const particles = Array.from({ length: 20 }, (_, i) => ({
  id: i,
  size: Math.random() * 4 + 1,
  x: Math.random() * 100,
  y: Math.random() * 100,
  duration: Math.random() * 20 + 10
}));
</script>

<template>
  <section id="hero" :class="['relative min-h-screen flex items-center justify-center overflow-hidden', isDarkTheme ? 'bg-gray-900' : 'bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900']">
    <!-- Particle Effects -->
    <div
      v-for="particle in particles"
      :key="particle.id"
      :class="['absolute rounded-full', isDarkTheme ? 'bg-gray-700/30' : 'bg-white/20']"
      :style="{
        width: `${particle.size}px`,
        height: `${particle.size}px`,
        left: `${particle.x}%`,
        top: `${particle.y}%`,
        animation: `float ${particle.duration}s ease-in-out infinite`
      }"
    />

    <!-- Grid Pattern -->
    <div :class="['absolute inset-0', isDarkTheme ? 'bg-gray-800/20' : 'bg-grid-white/5']" :style="`background-size: 50px 50px`" />

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4">
      <div class="text-center max-w-4xl mx-auto">
        <!-- Badge -->
        <Badge
          variant="outline"
          :class="[
            'text-sm py-2 mb-8',
            isDarkTheme
              ? 'bg-gray-800/50 border-gray-700 text-gray-300'
              : 'bg-white/10 border-white/20 text-white'
          ]"
        >
          <span class="mr-2 text-yellow-400">🚀</span>
          <span>Plataforma para negocios</span>
        </Badge>

        <!-- Main Title -->
        <h1 class="text-3xl sm:text-4xl md:text-6xl lg:text-7xl xl:text-8xl font-bold mb-6">
          <span :class="isDarkTheme ? 'text-gray-100' : 'text-white'">El futuro del</span>
          <br />
          <span class="text-transparent bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-400 bg-clip-text">
            e-commerce
          </span>
          <br />
          <span :class="isDarkTheme ? 'text-gray-100' : 'text-white'">está aquí</span>
        </h1>

        <!-- Description -->
        <p :class="['text-lg sm:text-xl md:text-2xl mb-8 sm:mb-12 max-w-3xl mx-auto leading-relaxed px-4', isDarkTheme ? 'text-gray-300' : 'text-white/80']">
          Transforma tu negocio con la plataforma más avanzada del mercado.
          Pagos globales, analytics en tiempo real y crecimiento ilimitado.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center mb-12 sm:mb-16 px-4">
          <Button
            :class="[
              'px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg font-semibold w-full sm:w-auto',
              isDarkTheme
                ? 'bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 border-0'
                : 'bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 border-0'
            ]"
          >
            Comenzar Gratis
          </Button>
          <Button
            variant="secondary"
            :class="[
              'px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg font-semibold w-full sm:w-auto',
              isDarkTheme
                ? 'bg-gray-800/50 hover:bg-gray-700 text-gray-300 border-gray-700'
                : 'bg-white/10 hover:bg-white/20 text-white border-white/20'
            ]"
          >
            Ver Demo
          </Button>
        </div>

        <!-- Social Proof -->
        <div :class="['flex flex-wrap justify-center items-center gap-4 sm:gap-8 text-xs sm:text-sm', isDarkTheme ? 'text-gray-400' : 'text-white/60']">
          <div class="flex items-center gap-2">
            <div :class="['w-2 h-2 rounded-full animate-pulse', isDarkTheme ? 'bg-green-600' : 'bg-green-400']" />
            <span class="text-xs sm:text-sm">10K+ negocios activos</span>
          </div>
          <div class="flex items-center gap-2">
            <div :class="['w-2 h-2 rounded-full animate-pulse', isDarkTheme ? 'bg-blue-600' : 'bg-blue-400']" />
            <span class="text-xs sm:text-sm">$2M+ procesados</span>
          </div>
          <div class="flex items-center gap-2">
            <div :class="['w-2 h-2 rounded-full animate-pulse', isDarkTheme ? 'bg-purple-600' : 'bg-purple-400']" />
            <span class="text-xs sm:text-sm">150+ países</span>
          </div>
        </div>
      </div>

      <!-- Floating Cards - Hidden on mobile -->
      <div class="hidden lg:block absolute top-20 left-10 animate-float">
        <div :class="['bg-white/10 backdrop-blur-md rounded-xl p-4 border', isDarkTheme ? 'border-gray-700' : 'border-white/20']">
          <div class="flex items-center gap-3 mb-2">
            <svg :class="['w-5 h-5', isDarkTheme ? 'text-blue-400' : 'text-blue-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="floatingCards[0].icon" />
            </svg>
            <div :class="['text-sm', isDarkTheme ? 'text-gray-300' : 'text-white']">{{ floatingCards[0].title }}</div>
          </div>
          <div :class="['text-xl font-bold', isDarkTheme ? 'text-gray-100' : 'text-white']">{{ floatingCards[0].value }}</div>
          <div :class="['text-sm', isDarkTheme ? 'text-green-400' : 'text-green-400']">{{ floatingCards[0].change }}</div>
        </div>
      </div>

      <div class="hidden lg:block absolute top-40 right-10 animate-float-delayed">
        <div :class="['bg-white/10 backdrop-blur-md rounded-xl p-4 border', isDarkTheme ? 'border-gray-700' : 'border-white/20']">
          <div class="flex items-center gap-3 mb-2">
            <svg :class="['w-5 h-5', isDarkTheme ? 'text-purple-400' : 'text-purple-400']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="floatingCards[1].icon" />
            </svg>
            <div :class="['text-sm', isDarkTheme ? 'text-gray-300' : 'text-white']">{{ floatingCards[1].title }}</div>
          </div>
          <div :class="['text-xl font-bold', isDarkTheme ? 'text-gray-100' : 'text-white']">{{ floatingCards[1].value }}</div>
          <div :class="['text-sm', isDarkTheme ? 'text-green-400' : 'text-green-400']">{{ floatingCards[1].change }}</div>
        </div>
      </div>

      <div class="hidden lg:block absolute bottom-20 left-20 animate-float">
        <div :class="['bg-white/10 backdrop-blur-md rounded-xl p-4 border', isDarkTheme ? 'border-gray-700' : 'border-white/20']">
          <div class="flex items-center gap-3 mb-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="floatingCards[2].icon" />
            </svg>
            <div :class="['text-sm', isDarkTheme ? 'text-gray-300' : 'text-white']">{{ floatingCards[2].title }}</div>
          </div>
          <div :class="['text-xl font-bold', isDarkTheme ? 'text-gray-100' : 'text-white']">{{ floatingCards[2].value }}</div>
          <div :class="['text-sm', isDarkTheme ? 'text-green-400' : 'text-green-400']">{{ floatingCards[2].change }}</div>
        </div>
      </div>
    </div>

    <!-- Bottom Gradient Fade -->
    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent" />
  </section>
</template>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

@keyframes float-delayed {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
  animation: float-delayed 8s ease-in-out infinite;
}
</style>
