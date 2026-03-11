<template>
  <div class="p-6 space-y-4">
    <h3 class="text-lg font-bold mb-4">Icon Test Component</h3>
    
    <div class="grid grid-cols-3 gap-4">
      <div class="text-center">
        <Icon name="dashboard" category="customer" />
        <p class="text-xs mt-1">Customer Dashboard</p>
      </div>
      
      <div class="text-center">
        <Icon name="dashboard" category="business" />
        <p class="text-xs mt-1">Business Dashboard</p>
      </div>
      
      <div class="text-center">
        <Icon name="close" category="ui" />
        <p class="text-xs mt-1">UI Close</p>
      </div>
      
      <div class="text-center">
        <Icon name="products" category="business" />
        <p class="text-xs mt-1">Products</p>
      </div>
      
      <div class="text-center">
        <Icon name="menu" category="ui" />
        <p class="text-xs mt-1">Menu</p>
      </div>
      
      <div class="text-center">
        <Icon name="nonexistent" category="business" />
        <p class="text-xs mt-1">Non-existent</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import Icon from './Icon.vue';
</script>
