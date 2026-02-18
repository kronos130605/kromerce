<script setup>
const currentYear = new Date().getFullYear();

const footerLinks = {
  product: [
    { name: 'Características', href: '#features' },
    { name: 'Precios', href: '#pricing' },
    { name: 'Integraciones', href: '#integrations' },
    { name: 'API', href: '#api' }
  ],
  company: [
    { name: 'Sobre Nosotros', href: '#about' },
    { name: 'Blog', href: '#blog' },
    { name: 'Contacto', href: '#contact' },
    { name: 'Carreras', href: '#careers' }
  ],
  legal: [
    { name: 'Términos de Servicio', href: '#terms' },
    { name: 'Política de Privacidad', href: '#privacy' },
    { name: 'Cookies', href: '#cookies' },
    { name: 'GDPR', href: '#gdpr' }
  ]
};

const socialLinks = [
  { name: 'Twitter', href: '#', icon: 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z' },
  { name: 'LinkedIn', href: '#', icon: 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z M4 6a2 2 0 100-4 2 2 0 000 4z' },
  { name: 'Facebook', href: '#', icon: 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z' },
  { name: 'Instagram', href: '#', icon: 'M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z' }
];
</script>

<template>
  <footer class="bg-background border-t border-border">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
        <!-- Logo y Newsletter -->
        <div class="lg:col-span-2">
          <div class="flex items-center mb-4">
            <img src="/resources/images/logos/kromerce-business-text.png" alt="Kromerce" class="h-8 w-auto" />
          </div>
          <p class="text-muted-foreground mb-6 max-w-md">
            La plataforma e-commerce diseñada para negocios de todos los tamaños. 
            Vende productos y servicios con pagos internacionales y locales.
          </p>
          
          <!-- Newsletter -->
          <div class="space-y-4">
            <h3 class="font-semibold text-foreground">Suscríbete a nuestro newsletter</h3>
            <div class="flex flex-col sm:flex-row gap-2">
              <input
                type="email"
                placeholder="tu@email.com"
                class="flex-1 px-3 py-2 border border-border rounded-md bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
              />
              <button class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors">
                Suscribir
              </button>
            </div>
          </div>
        </div>

        <!-- Product Links -->
        <div>
          <h3 class="font-semibold text-foreground mb-4">Producto</h3>
          <ul class="space-y-2">
            <li v-for="link in footerLinks.product" :key="link.name">
              <a :href="link.href" class="text-muted-foreground hover:text-foreground transition-colors">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Company Links -->
        <div>
          <h3 class="font-semibold text-foreground mb-4">Empresa</h3>
          <ul class="space-y-2">
            <li v-for="link in footerLinks.company" :key="link.name">
              <a :href="link.href" class="text-muted-foreground hover:text-foreground transition-colors">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Legal Links -->
        <div>
          <h3 class="font-semibold text-foreground mb-4">Legal</h3>
          <ul class="space-y-2">
            <li v-for="link in footerLinks.legal" :key="link.name">
              <a :href="link.href" class="text-muted-foreground hover:text-foreground transition-colors">
                {{ link.name }}
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
            :key="social.name"
            :href="social.href"
            :aria-label="social.name"
            class="text-muted-foreground hover:text-foreground transition-colors"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path :d="social.icon" />
            </svg>
          </a>
        </div>
        
        <div class="text-muted-foreground text-sm">
          © {{ currentYear }} Kromerce. Todos los derechos reservados.
        </div>
      </div>
    </div>
  </footer>
</template>
