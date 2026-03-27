<template>
    <BusinessLayout>
        <div class="products-create">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Product</h1>
                    <p class="text-gray-600 mt-1">Add a new product to your catalog</p>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="saveDraft"
                        :disabled="saving"
                        class="btn btn-outline"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V2"></path>
                        </svg>
                        Save Draft
                    </button>
                <button
                    @click="submitForm"
                    :disabled="saving"
                    class="btn btn-primary"
                >
                    <svg v-if="saving" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ saving ? 'Creating...' : 'Create Product' }}
                </button>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <div v-for="(step, index) in steps" :key="index" class="flex items-center">
                        <div
                            :class="[
                                'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                                currentStep >= index ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'
                            ]"
                        >
                            {{ index + 1 }}
                        </div>
                        <span
                            :class="[
                                'ml-2 text-sm font-medium',
                                currentStep >= index ? 'text-blue-600' : 'text-gray-500'
                            ]"
                        >
                            {{ step.title }}
                        </span>
                        <div v-if="index < steps.length - 1" class="w-8 h-0.5 bg-gray-200 mx-4"></div>
                    </div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    :style="{ width: ((currentStep + 1) / steps.length) * 100 + '%' }"
                ></div>
            </div>
        </div>

        <!-- Form Steps -->
        <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Step 1: Basic Information -->
            <div v-show="currentStep === 0" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter product name"
                        >
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                            SKU
                        </label>
                        <input
                            id="sku"
                            v-model="form.sku"
                            type="text"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Auto-generated if empty"
                        >
                        <div v-if="form.errors.sku" class="text-red-500 text-sm mt-1">
                            {{ form.errors.sku }}
                        </div>
                    </div>

                    <!-- Barcode -->
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">
                            Barcode
                        </label>
                        <input
                            id="barcode"
                            v-model="form.barcode"
                            type="text"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Optional barcode"
                        >
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Detailed product description"
                        ></textarea>
                    </div>

                    <!-- Short Description -->
                    <div class="md:col-span-2">
                        <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Short Description
                        </label>
                        <textarea
                            id="short_description"
                            v-model="form.short_description"
                            rows="2"
                            maxlength="500"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Brief description for product listings"
                        ></textarea>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ form.short_description?.length || 0 }}/500 characters
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">
                            Visibility
                        </label>
                        <select
                            id="visibility"
                            v-model="form.visibility"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>

                    <!-- Product Type -->
                    <div>
                        <label for="product_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Product Type
                        </label>
                        <select
                            id="product_type"
                            v-model="form.product_type"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="simple">Simple Product</option>
                            <option value="variable">Variable Product</option>
                            <option value="grouped">Grouped Product</option>
                        </select>
                    </div>

                    <!-- Featured -->
                    <div class="flex items-center">
                        <input
                            id="featured"
                            v-model="form.featured"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="featured" class="ml-2 block text-sm text-gray-700">
                            Featured Product
                        </label>
                    </div>
                </div>
            </div>

            <!-- Step 2: Categories and Tags -->
            <div v-show="currentStep === 1" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Categories and Tags</h2>

                <div class="space-y-6">
                    <!-- Categories -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Categories
                        </label>
                        <div class="space-y-2">
                            <div v-for="category in categories" :key="category.id" class="flex items-center">
                                <input
                                    :id="'category-' + category.id"
                                    v-model="form.categories"
                                    :value="category.id"
                                    type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label :for="'category-' + category.id" class="ml-2 block text-sm text-gray-700">
                                    {{ category.name }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tags
                        </label>
                        <TagInput
                            v-model="form.tags"
                            :available-tags="tags"
                            placeholder="Add tags..."
                        />
                    </div>
                </div>
            </div>

            <!-- Step 3: Pricing -->
            <div v-show="currentStep === 2" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Base Currency -->
                    <div>
                        <label for="base_currency" class="block text-sm font-medium text-gray-700 mb-1">
                            Currency <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="base_currency"
                            v-model="form.base_currency"
                            required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="MXN">MXN - Mexican Peso</option>
                        </select>
                    </div>

                    <!-- Base Price -->
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Regular Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">{{ getCurrencySymbol(form.base_currency) }}</span>
                            <input
                                id="base_price"
                                v-model="form.base_price"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="w-full pl-8 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0.00"
                            >
                        </div>
                        <div v-if="form.errors.base_price" class="text-red-500 text-sm mt-1">
                            {{ form.errors.base_price }}
                        </div>
                    </div>

                    <!-- Sale Price -->
                    <div>
                        <label for="base_sale_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Sale Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">{{ getCurrencySymbol(form.base_currency) }}</span>
                            <input
                                id="base_sale_price"
                                v-model="form.base_sale_price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full pl-8 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0.00"
                            >
                        </div>
                    </div>

                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Cost Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">{{ getCurrencySymbol(form.base_currency) }}</span>
                            <input
                                id="cost_price"
                                v-model="form.cost_price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full pl-8 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0.00"
                            >
                        </div>
                    </div>

                    <!-- Sale Settings -->
                    <div class="md:col-span-2">
                        <div class="border-t pt-4">
                            <h3 class="text-md font-medium text-gray-900 mb-3">Sale Settings</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Is On Sale -->
                                <div class="flex items-center">
                                    <input
                                        id="is_on_sale"
                                        v-model="form.is_on_sale"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <label for="is_on_sale" class="ml-2 block text-sm text-gray-700">
                                        Enable Sale
                                    </label>
                                </div>

                                <!-- Sale Type -->
                                <div>
                                    <label for="sale_type" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sale Type
                                    </label>
                                    <select
                                        id="sale_type"
                                        v-model="form.sale_type"
                                        :disabled="!form.is_on_sale"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100"
                                    >
                                        <option value="fixed">Fixed Amount</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>

                                <!-- Sale Discount -->
                                <div>
                                    <label for="sale_discount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Discount
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="sale_discount"
                                            v-model="form.sale_discount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            :disabled="!form.is_on_sale"
                                            class="w-full pr-8 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100"
                                            placeholder="0.00"
                                        >
                                        <span class="absolute right-3 top-2 text-gray-500 text-sm">
                                            {{ form.sale_type === 'percentage' ? '%' : getCurrencySymbol(form.base_currency) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Sale Dates -->
                                <div>
                                    <label for="sale_start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sale Start Date
                                    </label>
                                    <input
                                        id="sale_start_date"
                                        v-model="form.sale_start_date"
                                        type="date"
                                        :disabled="!form.is_on_sale"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100"
                                    >
                                </div>

                                <div>
                                    <label for="sale_end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sale End Date
                                    </label>
                                    <input
                                        id="sale_end_date"
                                        v-model="form.sale_end_date"
                                        type="date"
                                        :disabled="!form.is_on_sale"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Inventory -->
            <div v-show="currentStep === 3" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventory</h2>

                <div class="space-y-6">
                    <!-- Stock Management -->
                    <div class="flex items-center">
                        <input
                            id="manage_stock"
                            v-model="form.manage_stock"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="manage_stock" class="ml-2 block text-sm text-gray-700">
                            Enable Stock Management
                        </label>
                    </div>

                    <div v-if="form.manage_stock" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Stock Quantity -->
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">
                                Stock Quantity
                            </label>
                            <input
                                id="stock_quantity"
                                v-model.number="form.stock_quantity"
                                type="number"
                                min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0"
                            >
                        </div>

                        <!-- Low Stock Threshold -->
                        <div>
                            <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">
                                Low Stock Threshold
                            </label>
                            <input
                                id="low_stock_threshold"
                                v-model.number="form.low_stock_threshold"
                                type="number"
                                min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0"
                            >
                        </div>

                        <!-- Stock Status -->
                        <div>
                            <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">
                                Stock Status
                            </label>
                            <select
                                id="stock_status"
                                v-model="form.stock_status"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="instock">In Stock</option>
                                <option value="outofstock">Out of Stock</option>
                                <option value="onbackorder">On Backorder</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Shipping -->
            <div v-show="currentStep === 4" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping</h2>

                <div class="space-y-6">
                    <!-- Virtual Product -->
                    <div class="flex items-center">
                        <input
                            id="virtual"
                            v-model="form.virtual"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="virtual" class="ml-2 block text-sm text-gray-700">
                            Virtual Product (no shipping)
                        </label>
                    </div>

                    <!-- Downloadable Product -->
                    <div class="flex items-center">
                        <input
                            id="downloadable"
                            v-model="form.downloadable"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="downloadable" class="ml-2 block text-sm text-gray-700">
                            Downloadable Product
                        </label>
                    </div>

                    <div v-if="!form.virtual" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Weight -->
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
                                Weight (kg)
                            </label>
                            <input
                                id="weight"
                                v-model="form.weight"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0.00"
                            >
                        </div>

                        <!-- Dimensions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Dimensions (cm)
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <input
                                    v-model="form.length"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Length"
                                >
                                <input
                                    v-model="form.width"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Width"
                                >
                                <input
                                    v-model="form.height"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Height"
                                >
                            </div>
                        </div>

                        <!-- Shipping Class -->
                        <div>
                            <label for="shipping_class" class="block text-sm font-medium text-gray-700 mb-1">
                                Shipping Class
                            </label>
                            <input
                                id="shipping_class"
                                v-model="form.shipping_class"
                                type="text"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Standard"
                            >
                        </div>

                        <!-- Free Shipping -->
                        <div class="flex items-center">
                            <input
                                id="free_shipping"
                                v-model="form.free_shipping"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="free_shipping" class="ml-2 block text-sm text-gray-700">
                                Free Shipping
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 6: Images -->
            <div v-show="currentStep === 5" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h2>

                <ImageUploader
                    v-model="form.images"
                    :max-images="10"
                    accept="image/*"
                />
            </div>

            <!-- Navigation -->
            <div class="flex justify-between items-center">
                <button
                    v-if="currentStep > 0"
                    type="button"
                    @click="previousStep"
                    class="btn btn-outline"
                >
                    Previous
                </button>
                <div v-else></div>

                <div class="flex gap-3">
                    <button
                        v-if="currentStep < steps.length - 1"
                        type="button"
                        @click="nextStep"
                        class="btn btn-primary"
                    >
                        Next
                    </button>
                    <button
                        v-if="currentStep === steps.length - 1"
                        type="submit"
                        :disabled="saving"
                        class="btn btn-primary"
                    >
                        <svg v-if="saving" class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ saving ? 'Creating...' : 'Create Product' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    </BusinessLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import TagInput from '@/components/ui/forms/TagInput.vue'
import ImageUploader from '@/modules/products/components/ImageUploader.vue';
import BusinessLayout from '@/layouts/BusinessLayout.vue';

const props = defineProps({
    categories: Array,
    tags: Array,
})

const currentStep = ref(0)
const saving = ref(false)

const steps = [
    { title: 'Basic Info' },
    { title: 'Categories' },
    { title: 'Pricing' },
    { title: 'Inventory' },
    { title: 'Shipping' },
    { title: 'Images' },
]

const form = useForm({
    name: '',
    sku: '',
    barcode: '',
    description: '',
    short_description: '',
    status: 'active',
    visibility: 'public',
    product_type: 'simple',
    featured: false,
    categories: [],
    tags: [],
    base_currency: 'USD',
    base_price: '',
    base_sale_price: '',
    cost_price: '',
    is_on_sale: false,
    sale_type: 'fixed',
    sale_discount: '',
    sale_start_date: '',
    sale_end_date: '',
    manage_stock: true,
    stock_quantity: 0,
    low_stock_threshold: 5,
    stock_status: 'instock',
    virtual: false,
    downloadable: false,
    weight: '',
    length: '',
    width: '',
    height: '',
    shipping_class: '',
    free_shipping: false,
    images: [],
})

// Methods
const nextStep = () => {
    if (currentStep.value < steps.length - 1) {
        currentStep.value++
    }
}

const previousStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--
    }
}

const submitForm = () => {
    saving.value = true

    form.post(route('products.store'), {
        onSuccess: () => {
            router.visit(route('products.index'))
        },
        onFinish: () => {
            saving.value = false
        }
    })
}

const saveDraft = () => {
    saving.value = true

    const draftForm = useForm({ ...form, status: 'draft' })

    draftForm.post(route('products.store'), {
        onSuccess: () => {
            router.visit(route('products.index'))
        },
        onFinish: () => {
            saving.value = false
        }
    })
}

const getCurrencySymbol = (currency) => {
    const symbols = {
        USD: '$',
        EUR: '€',
        GBP: '£',
        MXN: '$',
    }
    return symbols[currency] || '$'
}
</script>

<style scoped>
.btn {
    @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.btn-primary {
    @apply text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500;
}

.btn-outline {
    @apply text-gray-700 bg-white border-gray-300 hover:bg-gray-50 focus:ring-blue-500;
}
</style>
