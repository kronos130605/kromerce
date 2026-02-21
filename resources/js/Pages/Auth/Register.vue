<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { locale } = useI18n();
const isDarkMode = ref(false);

// Detect dark mode from localStorage or system preference
const detectDarkMode = () => {
    try {
        const stored = localStorage.getItem('kromerce_theme');
        if (stored === 'dark' || stored === 'light') {
            isDarkMode.value = stored === 'dark';
        } else {
            isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
    } catch {
        isDarkMode.value = false;
    }
};

// Apply dark mode to document
const applyDarkMode = (enabled) => {
    if (enabled) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

// Initialize dark mode
detectDarkMode();
applyDarkMode(isDarkMode.value);

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('kromerce_theme')) {
        isDarkMode.value = e.matches;
        applyDarkMode(e.matches);
    }
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    user_type: 'customer', // customer, business_owner
    tenant_name: '', // solo para business_owner
});

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            form.reset('tenant_name');
        },
    });
};

const isBusinessOwner = computed(() => form.user_type === 'business_owner');

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    applyDarkMode(isDarkMode.value);
    try {
        localStorage.setItem('kromerce_theme', isDarkMode.value ? 'dark' : 'light');
    } catch {
        // ignore
    }
};
</script>

<template>
    <div :class="[
        'min-h-screen flex items-center justify-center p-4',
        isDarkMode 
            ? 'bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900' 
            : 'bg-gradient-to-br from-blue-50 via-white to-emerald-50'
    ]">
        <Head :title="t('auth.create_account')" />

        <!-- Background Pattern -->
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div :class="[
                'absolute -top-40 -right-40 h-80 w-80 rounded-full blur-3xl',
                isDarkMode ? 'bg-blue-600/10' : 'bg-blue-200/20'
            ]"></div>
            <div :class="[
                'absolute -bottom-40 -left-40 h-80 w-80 rounded-full blur-3xl',
                isDarkMode ? 'bg-emerald-600/10' : 'bg-emerald-200/20'
            ]"></div>
        </div>

        <!-- Language & Theme Controls -->
        <div class="absolute top-4 right-4 flex items-center gap-2">
            <!-- Language Selector -->
            <select 
                v-model="$i18n.locale" 
                @change="localStorage.setItem('kromerce_locale', $i18n.locale)"
                :class="[
                    'px-3 py-2 rounded-lg border text-sm transition-colors',
                    isDarkMode 
                        ? 'bg-gray-800 border-gray-700 text-gray-200 hover:bg-gray-700' 
                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
            >
                <option value="es">🇪🇸 ES</option>
                <option value="en">🇬🇧 EN</option>
            </select>

            <!-- Theme Toggle -->
            <button
                @click="toggleDarkMode"
                :class="[
                    'p-2 rounded-lg transition-colors',
                    isDarkMode 
                        ? 'bg-gray-800 text-yellow-400 hover:bg-gray-700' 
                        : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                ]"
                :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <span class="text-xl">{{ isDarkMode ? '☀️' : '🌙' }}</span>
            </button>
        </div>

        <!-- Main Container -->
        <div class="w-full max-w-5xl bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                
                <!-- Left Panel - Hero Section -->
                <div class="lg:w-1/2 bg-gradient-to-br from-blue-600 to-emerald-600 p-12 text-white relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-10 left-10 w-32 h-32 border-4 border-white/30 rounded-full"></div>
                        <div class="absolute bottom-10 right-10 w-24 h-24 border-4 border-white/20 rounded-lg rotate-45"></div>
                        <div class="absolute top-1/2 left-1/2 w-40 h-40 border-4 border-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <!-- Logo -->
                        <div class="mb-8">
                            <img src="/images/kromerce-business-text.png" alt="Kromerce" class="h-12 w-auto filter brightness-0 invert" />
                        </div>
                        
                        <!-- Content -->
                        <h1 class="text-4xl font-bold mb-6">Join the Future of Business</h1>
                        <p class="text-xl text-white/90 mb-8">
                            Transform your business with our cutting-edge platform. Whether you're selling or buying, we've got you covered.
                        </p>
                        
                        <!-- Features -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-white/90">Global payment processing</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-white/90">Real-time analytics</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-white/90">Custom branding for businesses</span>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="mt-12 grid grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold">10K+</div>
                                <div class="text-sm text-white/70">Businesses</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">$2M+</div>
                                <div class="text-sm text-white/70">Processed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">150+</div>
                                <div class="text-sm text-white/70">Countries</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Registration Form -->
                <div class="lg:w-1/2 p-12">
                    <div class="max-w-md mx-auto">
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                            <p class="text-gray-600">Join thousands of businesses already growing with Kromerce</p>
                        </div>

                        <!-- User Type Selection -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-4">I want to:</label>
                            <div class="grid grid-cols-2 gap-4">
                                <button
                                    type="button"
                                    @click="form.user_type = 'customer'"
                                    :class="[
                                        'relative p-4 rounded-xl border-2 transition-all duration-200 text-left',
                                        form.user_type === 'customer'
                                            ? 'border-blue-500 bg-blue-50 shadow-lg'
                                            : 'border-gray-200 bg-white hover:border-gray-300'
                                    ]"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div :class="[
                                            'w-5 h-5 rounded-full border-2 flex items-center justify-center',
                                            form.user_type === 'customer' ? 'border-blue-500' : 'border-gray-300'
                                        ]">
                                            <div v-if="form.user_type === 'customer'" class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Buy Products</div>
                                            <div class="text-sm text-gray-500">Shop from amazing businesses</div>
                                        </div>
                                    </div>
                                </button>
                                
                                <button
                                    type="button"
                                    @click="form.user_type = 'business_owner'"
                                    :class="[
                                        'relative p-4 rounded-xl border-2 transition-all duration-200 text-left',
                                        form.user_type === 'business_owner'
                                            ? 'border-emerald-500 bg-emerald-50 shadow-lg'
                                            : 'border-gray-200 bg-white hover:border-gray-300'
                                    ]"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div :class="[
                                            'w-5 h-5 rounded-full border-2 flex items-center justify-center',
                                            form.user_type === 'business_owner' ? 'border-emerald-500' : 'border-gray-300'
                                        ]">
                                            <div v-if="form.user_type === 'business_owner'" class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Sell Products</div>
                                            <div class="text-sm text-gray-500">Grow your business online</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Business Name (for business owners) -->
                        <div v-if="isBusinessOwner" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                            <input
                                type="text"
                                v-model="form.tenant_name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                placeholder="My Awesome Business"
                                required
                            />
                            <div v-if="form.errors.tenant_name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.tenant_name }}
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="John Doe"
                                    required
                                    autofocus
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input
                                    type="email"
                                    v-model="form.email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="john@example.com"
                                    required
                                />
                                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number (Optional)</label>
                                <input
                                    type="tel"
                                    v-model="form.phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="+1 (555) 123-4567"
                                />
                                <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.phone }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <input
                                        :type="showPassword ? 'text' : 'password'"
                                        v-model="form.password"
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="••••••••"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                    >
                                        <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <input
                                        :type="showPasswordConfirm ? 'text' : 'password'"
                                        v-model="form.password_confirmation"
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="••••••••"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="showPasswordConfirm = !showPasswordConfirm"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                    >
                                        <svg v-if="showPasswordConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password_confirmation }}
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-gradient-to-r from-blue-600 to-emerald-600 text-white py-3 px-4 rounded-xl font-medium hover:from-blue-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                            >
                                <span v-if="form.processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating Account...
                                </span>
                                <span v-else>Create Account</span>
                            </button>
                        </form>

                        <!-- Footer Links -->
                        <div class="mt-8 text-center">
                            <p class="text-gray-600">
                                Already have an account?
                                <Link :href="route('login')" class="font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                    Sign in here
                                </Link>
                            </p>
                        </div>

                        <!-- Terms -->
                        <div class="mt-6 text-center">
                            <p class="text-xs text-gray-500">
                                By creating an account, you agree to our
                                <a href="#" class="text-blue-600 hover:text-blue-700">Terms of Service</a>
                                and
                                <a href="#" class="text-blue-600 hover:text-blue-700">Privacy Policy</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
