<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const billingCycle = ref('monthly');

const plans = computed(() => [
  {
    name: t('pricing.plans.starter.name'),
    description: t('pricing.plans.starter.description'),
    price: { monthly: 0, yearly: 0 },
    features: [
      t('pricing.plans.starter.features.0'),
      t('pricing.plans.starter.features.1'),
      t('pricing.plans.starter.features.2'),
      t('pricing.plans.starter.features.3'),
      t('pricing.plans.starter.features.4'),
      t('pricing.plans.starter.features.5')
    ],
    cta: t('pricing.plans.starter.cta'),
    popular: false,
    color: 'gray'
  },
  {
    name: t('pricing.plans.professional.name'),
    description: t('pricing.plans.professional.description'),
    price: { monthly: 49, yearly: 39 },
    features: [
      t('pricing.plans.professional.features.0'),
      t('pricing.plans.professional.features.1'),
      t('pricing.plans.professional.features.2'),
      t('pricing.plans.professional.features.3'),
      t('pricing.plans.professional.features.4'),
      t('pricing.plans.professional.features.5'),
      t('pricing.plans.professional.features.6'),
      t('pricing.plans.professional.features.7')
    ],
    cta: t('pricing.plans.professional.cta'),
    popular: true,
    color: 'blue'
  },
  {
    name: t('pricing.plans.enterprise.name'),
    description: t('pricing.plans.enterprise.description'),
    price: { monthly: 199, yearly: 159 },
    features: [
      t('pricing.plans.enterprise.features.0'),
      t('pricing.plans.enterprise.features.1'),
      t('pricing.plans.enterprise.features.2'),
      t('pricing.plans.enterprise.features.3'),
      t('pricing.plans.enterprise.features.4'),
      t('pricing.plans.enterprise.features.5'),
      t('pricing.plans.enterprise.features.6'),
      t('pricing.plans.enterprise.features.7')
    ],
    cta: t('pricing.plans.enterprise.cta'),
    popular: false,
    color: 'purple'
  }
]);

const toggleBilling = () => {
  billingCycle.value = billingCycle.value === 'monthly' ? 'yearly' : 'monthly';
};

const getDisplayPrice = (plan) => {
  const price = plan.price[billingCycle.value];
  return price === 0 ? t('pricing.free') : `$${price}`;
};

const getBillingText = () => {
  return billingCycle.value === 'monthly' ? t('pricing.per_month') : t('pricing.per_year');
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
  <section id="pricing" class="py-20 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-950 dark:to-slate-950">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold mb-4 text-foreground">
          {{ t('pricing.title') }}
          <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
            {{ t('pricing.title_highlight') }}
          </span>
        </h2>
        <p class="text-xl max-w-3xl mx-auto mb-8 text-muted-foreground">
          {{ t('pricing.subtitle') }}
        </p>

        <!-- Billing Toggle -->
        <div class="flex items-center justify-center gap-4">
          <span :class="['font-medium transition-colors', billingCycle === 'monthly' ? 'text-foreground' : 'text-muted-foreground']">
            {{ t('pricing.monthly') }}
          </span>
          <button
            @click="toggleBilling"
            class="relative inline-flex h-6 w-11 items-center rounded-full bg-primary transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 cursor-pointer"
          >
            <span
              :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', billingCycle === 'monthly' ? 'translate-x-1' : 'translate-x-6']"
            />
          </button>
          <span :class="['font-medium transition-colors', billingCycle === 'yearly' ? 'text-foreground' : 'text-muted-foreground']">
            {{ t('pricing.yearly') }}
            <span class="text-green-600 ml-1">{{ t('pricing.yearly_discount') }}</span>
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
              : 'bg-card text-card-foreground border border-border hover:shadow-lg'
          ]"
        >
          <!-- Popular Badge -->
          <div
            v-if="plan.popular"
            class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-4 py-1 rounded-full text-sm font-medium"
          >
            {{ t('pricing.most_popular') }}
          </div>

          <!-- Plan Header -->
          <div class="text-center mb-6 lg:mb-8">
            <h3 :class="['text-xl lg:text-2xl font-bold mb-2', plan.popular ? 'text-primary-foreground' : 'text-foreground']">{{ plan.name }}</h3>
            <p :class="['mb-4 leading-relaxed text-sm lg:text-base', plan.popular ? 'text-primary-foreground/80' : 'text-muted-foreground']">
              {{ plan.description }}
            </p>

            <!-- Price -->
            <div class="mb-4">
              <div :class="['text-3xl lg:text-4xl font-bold', plan.popular ? 'text-primary-foreground' : 'text-foreground']">
                {{ getDisplayPrice(plan) }}
              </div>
              <div :class="['text-sm lg:text-base', plan.popular ? 'text-primary-foreground/70' : 'text-muted-foreground']">
                por {{ getBillingText() }}
              </div>
              <div
                v-if="getYearlySavings(plan) && billingCycle === 'yearly'"
                class="text-sm mt-1 font-medium text-green-600 dark:text-green-400"
              >
                {{ t('pricing.save') }} {{ getYearlySavings(plan) }}%
              </div>
            </div>
          </div>

          <!-- Features -->
          <ul class="space-y-2 lg:space-y-3 mb-6 lg:mb-8">
            <li
              v-for="(feature, index) in plan.features"
              :key="index"
              class="flex items-start gap-3"
            >
              <svg
                class="w-4 h-4 lg:w-5 lg:h-5 mt-0.5 flex-shrink-0"
                :class="plan.popular ? 'text-primary-foreground' : 'text-green-600 dark:text-green-400'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span :class="['text-sm lg:text-base', plan.popular ? 'text-primary-foreground/90' : 'text-foreground']">
                {{ feature }}
              </span>
            </li>
          </ul>

          <!-- CTA Button -->
          <button
            :class="[
              'w-full py-3 px-6 rounded-lg font-medium transition-colors cursor-pointer',
              plan.popular
                ? 'bg-white text-primary hover:bg-gray-100 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200'
                : 'bg-primary text-primary-foreground hover:bg-primary/90'
            ]"
          >
            {{ plan.cta }}
          </button>
        </div>
      </div>

      <!-- Additional Info -->
      <div class="mt-16 text-center">
        <div class="rounded-2xl p-8 shadow-lg max-w-4xl mx-auto bg-card text-card-foreground">
          <h3 class="text-xl font-bold mb-4 text-foreground">{{ t('pricing.questions.title') }}</h3>
          <p class="mb-6 leading-relaxed text-muted-foreground">
            {{ t('pricing.questions.subtitle') }}
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium cursor-pointer">
              {{ t('pricing.questions.talk_sales') }}
            </button>
            <button class="px-6 py-3 border border-border rounded-lg hover:bg-accent transition-colors font-medium cursor-pointer text-foreground">
              {{ t('pricing.questions.documentation') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
