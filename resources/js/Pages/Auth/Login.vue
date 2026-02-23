<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import LoginAttempts from '@/Components/LoginAttempts.vue';
import LanguageSelector from '@/components/LanguageSelector.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    loginAttempts: {
        type: Number,
        default: 5
    },
    maxAttempts: {
        type: Number,
        default: 5
    },
    lockoutTime: {
        type: Number,
        default: 0
    },
    isLocked: {
        type: Boolean,
        default: false
    }
});

const { t } = useI18n();
const { locale } = useI18n();
const page = usePage();
const isDarkMode = ref(false);
const showPassword = ref(false);
const isFormLocked = ref(page.props.isLocked || false);

// Watch for changes in page.props.isLocked from backend
watch(() => page.props.isLocked, (newValue) => {
    isFormLocked.value = newValue;
});

// Watch for changes in page.props.lockoutTime from backend
watch(() => page.props.lockoutTime, (newValue) => {
    // If backend sends new lockout time, update local state
    if (newValue > 0) {
        isFormLocked.value = true;
    }
});

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
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    applyDarkMode(isDarkMode.value);
    try {
        localStorage.setItem('kromerce_theme', isDarkMode.value ? 'dark' : 'light');
    } catch {
        // ignore
    }
};

const handleLockoutEnded = () => {
    // Actualizar el estado local para mostrar el formulario
    isFormLocked.value = false;
};
</script>

<template>
    <div :class="[
        'min-h-screen flex items-center justify-center p-4',
        isDarkMode 
            ? 'bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900' 
            : 'bg-gradient-to-br from-blue-50 via-white to-emerald-50'
    ]">
        <Head :title="t('nav.login')" />

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
            <LanguageSelector />

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
        <div :class="[
            'w-full max-w-5xl backdrop-blur-xl rounded-3xl shadow-2xl border overflow-hidden',
            isDarkMode 
                ? 'bg-gray-800/80 border-gray-700' 
                : 'bg-white/80 border-white/20'
        ]">
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
                        <h1 class="text-4xl font-bold mb-6">{{ t('auth.welcome_back') }}</h1>
                        <p class="text-xl text-white/90 mb-8">
                            {{ t('auth.login_subtitle') }}
                        </p>
                        
                        <!-- Features -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-white/90">{{ t('auth.multi_role_access') }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2h5a2 2 0 002-2zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM15 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-white/90">{{ t('auth.tenant_management') }}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 .646-.056 1.288-.166 1.907-.138.8-.197 1.604-.197 2.415 0 1.492.395 2.78 1.083 3.717l1.258 1.887c.056.084.145.134.238.134.099 0 .182-.05.238-.134l1.258-1.887c.688-.937 1.083-2.225 1.083-3.717 0-.811-.06-1.614-.197-2.415zM10 18.056a9.954 9.954 0 01-7.834-4.055c-.11-.65-.166-1.32-.166-2.001 0-.646.056-1.288.166-1.907.138-.8.197-1.604.197-2.415 0-1.492-.395-2.78-1.083-3.717l-1.258-1.887c-.056-.084-.145-.134-.238-.134-.099 0-.182.05-.238.134l-1.258 1.887c-.688.937-1.083 2.225-1.083 3.717 0 .811.06 1.614.197 2.415z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-white/90">{{ t('auth.global_marketplace') }}</span>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="mt-12 grid grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold">99.9%</div>
                                <div class="text-sm text-white/70">{{ t('auth.uptime') }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">24/7</div>
                                <div class="text-sm text-white/70">{{ t('auth.support') }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">150+</div>
                                <div class="text-sm text-white/70">{{ t('auth.countries') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Login Form -->
                <div :class="[
                    'lg:w-1/2 p-12',
                    isDarkMode ? 'bg-gray-800' : 'bg-white'
                ]">
                    <div class="max-w-md mx-auto">
                        <!-- Status Message -->
                        <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                            {{ status }}
                        </div>

                        <!-- Header -->
                        <div class="text-center mb-8">
                            <h2 :class="[
                                'text-3xl font-bold mb-2',
                                isDarkMode ? 'text-white' : 'text-gray-900'
                            ]">{{ t('auth.sign_in') }}</h2>
                            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
                                {{ t('auth.access_account') }}
                            </p>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="space-y-8">
                            <div v-if="!isFormLocked">
                                <div class="mb-6">
                                    <label :class="[
                                        'block text-sm font-medium mb-2',
                                        isDarkMode ? 'text-gray-300' : 'text-gray-700'
                                    ]">{{ t('auth.email_address') }}</label>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        :class="[
                                            'w-full px-4 py-3 border rounded-xl transition-colors',
                                            isDarkMode 
                                                ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                                                : 'bg-white border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500'
                                        ]"
                                        :placeholder="t('auth.email_placeholder')"
                                        required
                                        autofocus
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <div>
                                    <label :class="[
                                        'block text-sm font-medium mb-2',
                                        isDarkMode ? 'text-gray-300' : 'text-gray-700'
                                    ]">{{ t('auth.password') }}</label>
                                    <div class="relative">
                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            v-model="form.password"
                                            :class="[
                                                'w-full px-4 py-3 pr-12 border rounded-xl transition-colors',
                                                isDarkMode 
                                                    ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
                                                    : 'bg-white border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500'
                                            ]"
                                            placeholder="•••••••"
                                            required
                                        />
                                        <button
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            :class="[
                                                'absolute right-3 top-1/2 -translate-y-1/2 transition-colors',
                                                isDarkMode ? 'text-gray-400 hover:text-gray-300' : 'text-gray-400 hover:text-gray-600'
                                            ]"
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

                                <!-- Remember Me -->
                                <div class="flex items-center justify-between mb-8">
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.remember"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        />
                                        <span :class="[
                                            'ml-2 text-sm',
                                            isDarkMode ? 'text-gray-300' : 'text-gray-700'
                                        ]">{{ t('auth.remember_me') }}</span>
                                    </label>

                                    <Link
                                        v-if="canResetPassword"
                                        :href="route('password.request')"
                                        class="text-sm text-blue-600 hover:text-blue-700 transition-colors"
                                    >
                                        {{ t('auth.forgot_password') }}
                                    </Link>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    :disabled="form.processing || isFormLocked"
                                    class="w-full bg-gradient-to-r from-blue-600 to-emerald-600 text-white py-3 px-4 rounded-xl font-medium hover:from-blue-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                                >
                                    <span v-if="form.processing" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ t('auth.signing_in') }}...
                                    </span>
                                    <span v-else>{{ t('auth.sign_in') }}</span>
                                </button>
                            </div>
                        </form>

                        <!-- Login Attempts Indicator -->
                        <LoginAttempts 
                            :attempts="page.props.loginAttempts || 5"
                            :max-attempts="page.props.maxAttempts || 5"
                            :lockout-time="page.props.lockoutTime || 0"
                            :is-locked="isFormLocked"
                            @lockout-ended="handleLockoutEnded"
                            class="mb-6"
                        />

                        <!-- Footer Links -->
                        <div class="mt-8 text-center">
                            <p :class="isDarkMode ? 'text-gray-400' : 'text-gray-600'">
                                {{ t('auth.no_account') }}
                                <Link :href="route('register')" class="font-medium text-blue-600 hover:text-blue-700 transition-colors">
                                    {{ t('auth.create_account') }}
                                </Link>
                            </p>
                        </div>

                        <!-- Security Notice -->
                        <div :class="[
                            'mt-6 p-4 rounded-xl',
                            isDarkMode ? 'bg-blue-900/30 border border-blue-800' : 'bg-blue-50'
                        ]">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1 1 1 5.257-5.257A6 6 0 0118 8zM2 8a6 6 0 1112 0 6 6 0 01-12 0zm6-4a1 1 0 100 2 2 2 0 000-2z" clip-rule="evenodd" />
                                </svg>
                                <div :class="[
                                    'text-sm',
                                    isDarkMode ? 'text-blue-300' : 'text-blue-700'
                                ]">
                                    <strong>{{ t('auth.secure_login') }}:</strong> {{ t('auth.secure_login_desc') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
