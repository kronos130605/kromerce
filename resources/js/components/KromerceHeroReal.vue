<script setup>
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';

const features = [
  { icon: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z', title: 'Dashboard Intuitivo' },
  { icon: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z', title: 'Pagos Globales' },
  { icon: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z', title: 'Analytics Avanzado' }
];
</script>

<template>
  <section class="py-20 md:py-32 bg-gradient-to-b from-background to-blue-50/50 dark:to-blue-950/20">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <!-- Left Content -->
        <div class="space-y-8">
          <!-- Badge -->
          <Badge variant="outline" class="text-sm py-2">
            <span class="mr-2 text-blue-600">🚀</span>
            <span>Plataforma para negocios</span>
          </Badge>

          <!-- Main Title -->
          <h1 class="text-4xl md:text-6xl font-bold leading-tight">
            <span class="text-foreground">Tu negocio,</span>
            <br />
            <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
              escala global
            </span>
          </h1>

          <!-- Description -->
          <p class="text-xl text-muted-foreground leading-relaxed">
            La plataforma e-commerce que transforma negocios locales en empresas globales. 
            Acepta pagos de cualquier parte del mundo con la confianza y seguridad que mereces.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4">
            <Button class="px-6 py-3 font-semibold">
              Comenzar Gratis
            </Button>
            <Button variant="secondary" class="px-6 py-3 font-semibold">
              Ver Demo
            </Button>
          </div>

          <!-- Trust Indicators -->
          <div class="flex flex-wrap gap-6">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm">Setup en 5 minutos</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm">Pagos 150+ países</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span class="text-sm">Soporte 24/7</span>
            </div>
          </div>
        </div>

        <!-- Right Content - Real Interface -->
        <div class="relative">
          <!-- Main Interface Card -->
          <div class="relative bg-white dark:bg-card rounded-2xl shadow-2xl border border-border overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <img src="/resources/images/logos/kromerce-business-icon-logo.png" alt="Kromerce" class="h-8 w-8 rounded-lg bg-white/20 p-1" />
                  <div class="text-white">
                    <div class="font-semibold">Kromerce Dashboard</div>
                    <div class="text-xs text-white/80">Tienda Demo</div>
                  </div>
                </div>
                <div class="flex gap-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                  <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                  <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                </div>
              </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-3 gap-4 p-4 border-b border-border">
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">$12,450</div>
                <div class="text-xs text-muted-foreground">Ventas Hoy</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">342</div>
                <div class="text-xs text-muted-foreground">Pedidos</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">89%</div>
                <div class="text-xs text-muted-foreground">Conversión</div>
              </div>
            </div>

            <!-- Recent Orders -->
            <div class="p-4">
              <div class="font-semibold mb-3">Pedidos Recientes</div>
              <div class="space-y-2">
                <div class="flex items-center justify-between p-2 bg-muted rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-xs font-semibold text-blue-600">JD</div>
                    <div>
                      <div class="text-sm font-medium">Juan Díaz</div>
                      <div class="text-xs text-muted-foreground">Hace 2 min</div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-semibold">$89.99</div>
                    <div class="text-xs text-green-600">✓ Pagado</div>
                  </div>
                </div>
                <div class="flex items-center justify-between p-2 bg-muted rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-xs font-semibold text-purple-600">MG</div>
                    <div>
                      <div class="text-sm font-medium">María García</div>
                      <div class="text-xs text-muted-foreground">Hace 5 min</div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-semibold">$156.00</div>
                    <div class="text-xs text-green-600">✓ Pagado</div>
                  </div>
                </div>
                <div class="flex items-center justify-between p-2 bg-muted rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-xs font-semibold text-green-600">RC</div>
                    <div>
                      <div class="text-sm font-medium">Roberto Chen</div>
                      <div class="text-xs text-muted-foreground">Hace 12 min</div>
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-semibold">$234.50</div>
                    <div class="text-xs text-yellow-600">⏳ Procesando</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="p-4 border-t border-border bg-muted/50">
              <div class="grid grid-cols-3 gap-2">
                <button class="p-2 bg-white dark:bg-card rounded-lg text-xs font-medium hover:bg-accent transition-colors">
                  + Producto
                </button>
                <button class="p-2 bg-white dark:bg-card rounded-lg text-xs font-medium hover:bg-accent transition-colors">
                  Ver Reportes
                </button>
                <button class="p-2 bg-white dark:bg-card rounded-lg text-xs font-medium hover:bg-accent transition-colors">
                  Configurar
                </button>
              </div>
            </div>
          </div>

          <!-- Floating Elements -->
          <div class="absolute -top-4 -right-4 w-20 h-20 bg-blue-500/20 rounded-full blur-xl" />
          <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-cyan-500/20 rounded-full blur-xl" />
        </div>
      </div>
    </div>
  </section>
</template>
