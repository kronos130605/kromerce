<script setup>
const testimonials = [
  {
    name: 'María González',
    role: 'CEO - Tienda Moda Express',
    avatar: 'MG',
    content: 'Kromerce transformó completamente mi negocio. Pasé de vender localmente a tener clientes en 5 países en solo 3 meses. ¡Increíble!',
    rating: 5,
    company: 'Retail',
    location: 'México'
  },
  {
    name: 'Carlos Rodríguez',
    role: 'Fundador - Tech Solutions Pro',
    avatar: 'CR',
    content: 'La integración de pagos fue lo más fácil que he visto. Mis clientes pagan desde cualquier parte del mundo sin problemas.',
    rating: 5,
    company: 'Servicios',
    location: 'España'
  },
  {
    name: 'Ana Silva',
    role: 'Directora - Arte Digital Studio',
    avatar: 'AS',
    content: 'El dashboard me permite tomar decisiones inteligentes. Mis ventas aumentaron 40% desde que empecé a usar Kromerce.',
    rating: 5,
    company: 'Creative',
    location: 'Brasil'
  },
  {
    name: 'Roberto Chen',
    role: 'Gerente - Global Import Co',
    avatar: 'RC',
    content: 'El soporte 24/7 es excepcional. Siempre hay alguien disponible para ayudarme con cualquier duda técnica.',
    rating: 5,
    company: 'Import/Export',
    location: 'Perú'
  },
  {
    name: 'Laura Martínez',
    role: 'Propietaria - Handmade Crafts',
    avatar: 'LM',
    content: 'Nunca pensé que mi pequeño negocio podría vender a nivel internacional. Kromerce hizo posible mi sueño.',
    rating: 5,
    company: 'Artisan',
    location: 'Colombia'
  },
  {
    name: 'David Park',
    role: 'CTO - Innovation Labs',
    avatar: 'DP',
    content: 'La API es robusta y la documentación excelente. Integramos Kromerce con nuestros sistemas en menos de una semana.',
    rating: 5,
    company: 'Technology',
    location: 'Chile'
  }
];

const industries = [
  { name: 'Retail', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
  { name: 'Servicios', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
  { name: 'Technology', icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
  { name: 'Creative', icon: 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4' }
];
</script>

<template>
  <section id="testimonials" class="py-20 bg-background">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          Lo que dicen nuestros
          <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
            clientes
          </span>
        </h2>
        <p class="text-xl text-muted-foreground max-w-3xl mx-auto">
          Miles de negocios ya están creciendo con Kromerce. 
          Descubre sus historias de éxito.
        </p>
      </div>

      <!-- Industries -->
      <div class="flex flex-wrap justify-center gap-4 mb-12">
        <div
          v-for="industry in industries"
          :key="industry.name"
          class="flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-full text-primary"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="industry.icon" />
          </svg>
          <span class="text-sm font-medium">{{ industry.name }}</span>
        </div>
      </div>

      <!-- Testimonials Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="testimonial in testimonials"
          :key="testimonial.name"
          class="group relative p-6 rounded-xl border border-border bg-card hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
        >
          <!-- Rating -->
          <div class="flex mb-4">
            <svg
              v-for="i in 5"
              :key="i"
              class="w-5 h-5 text-yellow-400"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </div>

          <!-- Content -->
          <blockquote class="text-muted-foreground mb-6 leading-relaxed">
            "{{ testimonial.content }}"
          </blockquote>

          <!-- Author -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white font-semibold">
                {{ testimonial.avatar }}
              </div>
              <div>
                <div class="font-semibold">{{ testimonial.name }}</div>
                <div class="text-sm text-muted-foreground">{{ testimonial.role }}</div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-xs text-muted-foreground">{{ testimonial.company }}</div>
              <div class="text-xs text-muted-foreground">{{ testimonial.location }}</div>
            </div>
          </div>

          <!-- Hover Effect -->
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-600/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" />
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center mt-16">
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-950/20 dark:to-cyan-950/20 rounded-2xl p-8 max-w-4xl mx-auto">
          <h3 class="text-2xl font-bold mb-4">
            ¿Listo para unirte a miles de negocios exitosos?
          </h3>
          <p class="text-muted-foreground mb-6">
            Comienza gratis y escala según creces. Sin tarjetas de crédito, sin compromiso.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium">
              Comenzar Gratis
            </button>
            <button class="px-6 py-3 border border-border rounded-lg hover:bg-accent transition-colors font-medium">
              Ver Casos de Éxito
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
