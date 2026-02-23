<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();

const password = ref('');
const passwordStrength = ref(0);
const passwordFeedback = ref('');

const strengthLevels = [
    { min: 0, max: 20, color: 'bg-red-500', text: 'password_strength_weak' },
    { min: 21, max: 40, color: 'bg-orange-500', text: 'password_strength_fair' },
    { min: 41, max: 60, color: 'bg-yellow-500', text: 'password_strength_good' },
    { min: 61, max: 100, color: 'bg-green-500', text: 'password_strength_strong' }
];

const checkPasswordStrength = (password) => {
    let score = 0;
    let feedback = [];

    // Length check
    if (password.length >= 8) {
        score += 25;
    } else {
        feedback.push('At least 8 characters');
    }

    // Uppercase check
    if (/[A-Z]/.test(password)) {
        score += 25;
    } else {
        feedback.push('One uppercase letter');
    }

    // Lowercase check
    if (/[a-z]/.test(password)) {
        score += 25;
    } else {
        feedback.push('One lowercase letter');
    }

    // Numbers check
    if (/\d/.test(password)) {
        score += 15;
    } else {
        feedback.push('One number');
    }

    // Special characters check
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        score += 10;
    } else {
        feedback.push('One special character');
    }

    passwordStrength.value = score;
    passwordFeedback.value = feedback.join(', ');
};

const getStrengthLevel = computed(() => {
    return strengthLevels.find(level => 
        passwordStrength.value >= level.min && passwordStrength.value <= level.max
    ) || strengthLevels[0];
});

const getStrengthColor = computed(() => {
    return getStrengthLevel.value.color;
});

const getStrengthText = computed(() => {
    return t(`auth.${getStrengthLevel.value.text}`);
});

const emit = defineEmits(['update:modelValue']);

onMounted(() => {
    if (password.value) {
        checkPasswordStrength(password.value);
    }
});

const updatePassword = (value) => {
    password.value = value;
    checkPasswordStrength(value);
    emit('update:modelValue', value);
};
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs">
            <span :class="[
                'font-medium',
                passwordStrength > 60 ? 'text-green-600' : 
                passwordStrength > 40 ? 'text-yellow-600' : 
                passwordStrength > 20 ? 'text-orange-600' : 'text-red-600'
            ]">
                {{ getStrengthText }}
            </span>
            <span class="text-gray-500">{{ passwordStrength }}/100</span>
        </div>
        
        <!-- Strength indicator bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div 
                :class="[
                    'h-full transition-all duration-300 ease-out',
                    getStrengthColor
                ]"
                :style="{ width: `${passwordStrength}%` }"
            ></div>
        </div>
        
        <!-- Requirements feedback -->
        <div v-if="passwordFeedback && passwordStrength < 100" class="text-xs text-gray-500">
            <p class="font-medium mb-1">{{ t('auth.password_requirements') }}:</p>
            <ul class="list-disc list-inside space-y-1">
                <li v-for="feedback in passwordFeedback.split(',')" :key="feedback">
                    {{ feedback.trim() }}
                </li>
            </ul>
        </div>
    </div>
</template>
