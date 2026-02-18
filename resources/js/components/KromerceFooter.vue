<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const currentYear = new Date().getFullYear();

const footerLinks = {
  product: [
    { key: 'features', href: '#features' },
    { key: 'pricing', href: '#pricing' },
    { key: 'integrations', href: '#integrations' },
    { key: 'api', href: '#api' }
  ],
  company: [
    { key: 'about', href: '#about' },
    { key: 'blog', href: '#blog' },
    { key: 'contact', href: '#contact' },
    { key: 'careers', href: '#careers' }
  ],
  legal: [
    { key: 'terms', href: '#terms' },
    { key: 'privacy', href: '#privacy' },
    { key: 'cookies', href: '#cookies' },
    { key: 'gdpr', href: '#gdpr' }
  ]
};

const socialLinks = [
  { nameKey: 'twitter', href: '#', icon: 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z' },
  { nameKey: 'linkedin', href: '#', icon: 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z' },
  { nameKey: 'github', href: '#', icon: 'M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.39.6.11.82-.26.82-.58 0-.29-.01-1.05-.02-2.05-3.34.73-4.04-1.61-4.04-1.61-.55-1.39-1.34-1.76-1.34-1.76-1.09-.74.08-.73.08-.73 1.2.09 1.83 1.24 1.83 1.24 1.07 1.83 2.8 1.3 3.49.99.11-.78.42-1.3.76-1.6-2.67-.3-5.47-1.34-5.47-5.95 0-1.31.47-2.38 1.24-3.22-.12-.3-.54-1.52.12-3.17 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 016 0c2.28-1.55 3.29-1.23 3.29-1.23.66 1.65.24 2.87.12 3.17.77.84 1.23 1.91 1.23 3.22 0 4.62-2.8 5.64-5.48 5.93.43.37.81 1.1.81 2.22 0 1.6-.02 2.88-.02 3.27 0 .32.21.7.82.58A12.01 12.01 0 0024 12c0-6.63-5.37-12-12-12z' }
];
</script>

<template>
  <footer class="bg-background border-t border-border">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
        <!-- Logo y Newsletter -->
        <div class="lg:col-span-2">
          <div class="flex items-center mb-4">
            <img src="/images/kromerce-business-text.png" alt="Kromerce" class="h-8 w-auto" />
          </div>
          <p class="text-muted-foreground mb-6 max-w-md">
            {{ t('footer.description') }}
          </p>
          
          <!-- Newsletter -->
          <div class="space-y-4">
            <h3 class="font-semibold text-foreground">Newsletter</h3>
            <div class="flex flex-col sm:flex-row gap-2">
              <input
                type="email"
                :placeholder="'email@example.com'"
                class="flex-1 px-3 py-2 border border-border rounded-md bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
              />
              <button class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors">
                OK
              </button>
            </div>
          </div>
        </div>

        <!-- Product Links -->
        <div>
          <h3 class="font-semibold text-foreground mb-4">{{ t('footer.links.product') }}</h3>
          <ul class="space-y-2">
            <li v-for="link in footerLinks.product" :key="link.key">
              <a :href="link.href" class="text-muted-foreground hover:text-foreground transition-colors">
                {{ t(`footer.links.${link.key}`) }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Company Links -->
        <div>
          <h3 class="font-semibold text-foreground mb-4">{{ t('footer.links.company') }}</h3>
          <ul class="space-y-2">
            <li v-for="link in footerLinks.company" :key="link.key">
              <a :href="link.href" class="text-muted-foreground hover:text-foreground transition-colors">
                {{ t(`footer.links.${link.key}`) }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Legal Links -->
        <div>
          <h3 class="font-semibold text-foreground mb-4">{{ t('footer.links.legal') }}</h3>
          <ul class="space-y-2">
            <li v-for="link in footerLinks.legal" :key="link.key">
              <a :href="link.href" class="text-muted-foreground hover:text-foreground transition-colors">
                {{ t(`footer.links.${link.key}`) }}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Social Links and Copyright -->
      <div class="border-t border-border mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
        <div class="flex items-center space-x-6 mb-4 md:mb-0">
          <a
            v-for="social in socialLinks"
            :key="social.nameKey"
            :href="social.href"
            :aria-label="t(`footer.social.${social.nameKey}`)"
            class="text-muted-foreground hover:text-foreground transition-colors"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path :d="social.icon" />
            </svg>
          </a>
        </div>
        
        <div class="text-muted-foreground text-sm">
          {{ t('footer.copyright').replace('2024', String(currentYear)) }}
        </div>
      </div>
    </div>
  </footer>
</template>
