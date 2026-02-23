<script setup>
import DashboardNavbar from '@/components/DashboardNavbar.vue';
</script>

<template>
    <div class="min-h-screen bg-background">
        <DashboardNavbar />
        
        <!-- Page Content with proper spacing for fixed navbar -->
        <main class="pt-16">
            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <div>
                <slot />
            </div>
        </main>
    </div>
</template>
