<script setup>
import { ref, onMounted } from 'vue';

const billingCycle = ref('monthly');
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

const plans = [
  {
    name: 'Starter',
    description: 'Perfecto para negocios que empiezan',
    price: { monthly: 0, yearly: 0 },
    features: [
      'Hasta 100 productos',
      'Pagos básicos',
      'Dashboard simple',
      'Soporte por email',
      '1% comisión por venta',
      'Kromerce branding'
    ],
    cta: 'Comenzar Gratis',
    popular: false,
    color: 'gray'
  },
  {
    name: 'Professional',
    description: 'Ideal para negocios en crecimiento',
    price: { monthly: 49, yearly: 39 },
    features: [
      'Productos ilimitados',
      'Pagos avanzados',
      'Analytics completo',
      'Soporte prioritario',
      '0.5% comisión por venta',
      'Sin branding',
      'API básica',
      'Integraciones populares'
    ],
    cta: 'Comenzar Trial',
    popular: true,
    color: 'blue'
  },
  {
    name: 'Enterprise',
    description: 'Para grandes operaciones y personalización',
    price: { monthly: 199, yearly: 159 },
    features: [
      'Todo lo de Professional',
      'API completa',
      'Integraciones personalizadas',
      'Dedicated account manager',
      'SLA garantizado',
      'Onboarding personalizado',
      'White label disponible',
      'Servidores dedicados'
    ],
    cta: 'Contactar Ventas',
    popular: false,
    color: 'purple'
  }
];

const toggleBilling = () => {
  billingCycle.value = billingCycle.value === 'monthly' ? 'yearly' : 'monthly';
};

const getDisplayPrice = (plan) => {
  const price = plan.price[billingCycle.value];
  return price === 0 ? 'Gratis' : `$${price}`;
};

const getBillingText = () => {
  return billingCycle.value === 'monthly' ? 'mes' : 'año';
};

const getYearlySavings = (plan) => {
  if (plan.price.monthly === 0) return null;
  const monthlyTotal = plan.price.monthly * 12;
  const yearlyTotal = plan.price.yearly * 12;
  const savings = monthlyTotal - yearlyTotal;
  return savings > 0 ? Math.round((savings / monthlyTotal) * 100) : null;
};
</script>

<template>
  <section :class="['py-20', isDarkTheme ? 'bg-gray-900' : 'bg-gradient-to-br from-blue-50 to-cyan-50']">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 :class="['text-3xl md:text-4xl font-bold mb-4', isDarkTheme ? 'text-gray-100' : 'text-foreground']">
          Precios simples para
          <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
            todos los negocios
          </span>
        </h2>
        <p :class="['text-xl max-w-3xl mx-auto mb-8', isDarkTheme ? 'text-gray-300' : 'text-muted-foreground']">
          Crece con nosotros. Desde emprendimientos individuales hasta grandes empresas,
          tenemos el plan perfecto para ti.
        </p>

        <!-- Billing Toggle -->
        <div class="flex items-center justify-center gap-4">
          <span :class="['font-medium transition-colors', billingCycle === 'monthly' ? (isDarkTheme ? 'text-gray-100' : 'text-foreground') : (isDarkTheme ? 'text-gray-400' : 'text-muted-foreground')]">
            Mensual
          </span>
          <button
            @click="toggleBilling"
            class="relative inline-flex h-6 w-11 items-center rounded-full bg-primary transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 cursor-pointer"
          >
            <span
              :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', billingCycle === 'monthly' ? 'translate-x-1' : 'translate-x-6']"
            />
          </button>
          <span :class="['font-medium transition-colors', billingCycle === 'yearly' ? (isDarkTheme ? 'text-gray-100' : 'text-foreground') : (isDarkTheme ? 'text-gray-400' : 'text-muted-foreground')]">
            Anual
            <span class="text-green-600 ml-1">-20%</span>
          </span>
        </div>
      </div>

      <!-- Pricing Cards -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto px-4">
        <div
          v-for="plan in plans"
          :key="plan.name"
          :class="[
            'relative rounded-2xl p-6 lg:p-8 transition-all duration-300 hover:-translate-y-1',
            plan.popular
              ? 'bg-primary text-primary-foreground shadow-xl ring-2 ring-primary/20'
              : (isDarkTheme ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200 hover:shadow-lg')
          ]"
        >
          <!-- Popular Badge -->
          <div
            v-if="plan.popular"
            class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-4 py-1 rounded-full text-sm font-medium"
          >
            Más Popular
          </div>

          <!-- Plan Header -->
          <div class="text-center mb-6 lg:mb-8">
            <h3 :class="['text-xl lg:text-2xl font-bold mb-2', plan.popular ? 'text-primary-foreground' : (isDarkTheme ? 'text-white' : 'text-gray-900')]">{{ plan.name }}</h3>
            <p :class="['mb-4 leading-relaxed text-sm lg:text-base', plan.popular ? 'text-primary-foreground/80' : (isDarkTheme ? 'text-gray-200' : 'text-gray-600')]">
              {{ plan.description }}
            </p>

            <!-- Price -->
            <div class="mb-4">
              <div :class="['text-3xl lg:text-4xl font-bold', plan.popular ? 'text-primary-foreground' : (isDarkTheme ? 'text-white' : 'text-gray-900')]">
                {{ getDisplayPrice(plan) }}
              </div>
              <div :class="['text-sm lg:text-base', plan.popular ? 'text-primary-foreground/70' : (isDarkTheme ? 'text-gray-200' : 'text-gray-600')]">
                por {{ getBillingText() }}
              </div>
              <div
                v-if="getYearlySavings(plan) && billingCycle === 'yearly'"
                :class="['text-sm mt-1 font-medium', isDarkTheme ? 'text-green-400' : 'text-green-600']"
              >
                Ahorra {{ getYearlySavings(plan) }}%
              </div>
            </div>
          </div>

          <!-- Features -->
          <ul class="space-y-2 lg:space-y-3 mb-6 lg:mb-8">
            <li
              v-for="feature in plan.features"
              :key="feature"
              class="flex items-start gap-3"
            >
              <svg
                class="w-4 h-4 lg:w-5 lg:h-5 mt-0.5 flex-shrink-0"
                :class="plan.popular ? 'text-primary-foreground' : (isDarkTheme ? 'text-green-400' : 'text-green-600')"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span :class="['text-sm lg:text-base', plan.popular ? 'text-primary-foreground/90' : (isDarkTheme ? 'text-gray-100' : 'text-gray-900')]">
                {{ feature }}
              </span>
            </li>
          </ul>

          <!-- CTA Button -->
          <button
            :class="[
              'w-full py-3 px-6 rounded-lg font-medium transition-colors cursor-pointer',
              plan.popular
                ? (isDarkTheme ? 'bg-gray-100 text-gray-900 hover:bg-gray-200' : 'bg-white text-primary hover:bg-gray-100')
                : (isDarkTheme ? 'bg-gray-700 text-white hover:bg-gray-600' : 'bg-primary text-primary-foreground hover:bg-primary/90')
            ]"
          >
            {{ plan.cta }}
          </button>
        </div>
      </div>

      <!-- Additional Info -->
      <div class="mt-16 text-center">
        <div :class="['rounded-2xl p-8 shadow-lg max-w-4xl mx-auto', isDarkTheme ? 'bg-gray-800' : 'bg-white']">
          <h3 :class="['text-xl font-bold mb-4', isDarkTheme ? 'text-white' : 'text-gray-900']">¿Preguntas sobre nuestros planes?</h3>
          <p :class="['mb-6 leading-relaxed', isDarkTheme ? 'text-gray-200' : 'text-gray-600']">
            Nuestro equipo está listo para ayudarte a encontrar el plan perfecto para tu negocio.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium cursor-pointer">
              Hablar con Ventas
            </button>
            <button :class="['px-6 py-3 border rounded-lg hover:bg-accent transition-colors font-medium cursor-pointer', isDarkTheme ? 'border-gray-600 text-white hover:bg-gray-700' : 'border-gray-300 text-gray-900 hover:bg-gray-50']">
              Ver Documentación
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
