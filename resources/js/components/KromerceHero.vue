<script setup lang="ts">
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
</script>

<template>
  <section class="container">
    <div class="grid place-items-center lg:max-w-screen-xl gap-8 mx-auto py-20 md:py-32">
      <div class="text-center space-y-8">
        <!-- Badge -->
        <Badge variant="outline" class="text-sm py-2">
          <span class="mr-2 text-blue-600">🚀</span>
          <span>Plataforma para MiPYMES</span>
        </Badge>

        <!-- Main Title -->
        <div class="max-w-screen-md mx-auto text-center text-5xl md:text-6xl font-bold">
          <h1>
            Vende tus productos y
            <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
              servicios
            </span>
            al mundo
          </h1>
        </div>

        <!-- Description -->
        <p class="max-w-screen-sm mx-auto text-xl text-muted-foreground">
          La plataforma e-commerce diseñada para MiPYMES latinas. 
          Acepta pagos internacionales y locales, incluso desde Cuba.
        </p>

        <!-- CTA Buttons -->
        <div class="space-y-4 md:space-y-0 md:space-x-4">
          <Button class="w-5/6 md:w-1/4 font-bold">
            Comenzar Gratis
          </Button>

          <Button
            variant="secondary"
            class="w-5/6 md:w-1/4 font-bold"
          >
            Ver Demo
          </Button>
        </div>

        <!-- Features Pills -->
        <div class="flex flex-wrap justify-center gap-4 pt-8">
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
            Sin comisiones de setup
          </div>
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
            Pagos locales e internacionales
          </div>
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
            Soporte en español
          </div>
        </div>
      </div>

      <!-- Hero Image Placeholder -->
      <div class="relative group mt-14">
        <!-- Gradient Shadow -->
        <div class="absolute -top-6 right-12 w-[90%] h-12 lg:h-[80%] bg-blue-500/20 blur-3xl rounded-full"></div>

        <!-- Dashboard Preview -->
        <div class="w-full md:w-[1200px] mx-auto rounded-lg relative leading-none flex items-center border border-t-2 border-t-blue-500/30 bg-card p-8">
          <div class="w-full space-y-4">
            <!-- Mock Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-muted rounded-lg p-4">
                <div class="text-2xl font-bold text-blue-600">$2,450</div>
                <div class="text-sm text-muted-foreground">Ventas este mes</div>
              </div>
              <div class="bg-muted rounded-lg p-4">
                <div class="text-2xl font-bold text-green-600">142</div>
                <div class="text-sm text-muted-foreground">Pedidos</div>
              </div>
              <div class="bg-muted rounded-lg p-4">
                <div class="text-2xl font-bold text-purple-600">89</div>
                <div class="text-sm text-muted-foreground">Clientes</div>
              </div>
            </div>
            
            <!-- Mock Product List -->
            <div class="bg-muted rounded-lg p-4">
              <div class="text-sm font-medium mb-2">Productos populares</div>
              <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span>Producto A</span>
                  <span class="text-green-600">+12%</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span>Servicio B</span>
                  <span class="text-green-600">+8%</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gradient Effect -->
        <div class="absolute bottom-0 left-0 w-full h-20 md:h-28 bg-gradient-to-b from-background/0 via-background/50 to-background rounded-lg"></div>
      </div>
    </div>
  </section>
</template>
