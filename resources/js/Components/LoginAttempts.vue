<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const emit = defineEmits(['lockout-ended']);

const props = defineProps({
    attempts: {
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

const remainingTime = ref(props.lockoutTime);
let timer = null;

const timeLeft = computed(() => {
    if (remainingTime.value <= 0) return '00:00';
    
    const minutes = Math.floor(remainingTime.value / 60);
    const seconds = remainingTime.value % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

const progressPercentage = computed(() => {
    return Math.max(0, (props.attempts / props.maxAttempts) * 100);
});

const getProgressColor = computed(() => {
    const percentage = (props.attempts / props.maxAttempts) * 100;
    if (percentage >= 80) return 'bg-green-500';  // 5/5, 4/5
    if (percentage >= 60) return 'bg-yellow-500'; // 3/5
    if (percentage >= 40) return 'bg-orange-500'; // 2/5
    return 'bg-red-500';  // 1/5 or less
});

const startTimer = () => {
    if (timer) clearInterval(timer);
    
    if (props.lockoutTime > 0) {
        timer = setInterval(() => {
            if (remainingTime.value > 0) {
                remainingTime.value--;
            } else {
                clearInterval(timer);
                timer = null;
                // Emitir evento cuando el contador llega a 0
                emit('lockout-ended');
            }
        }, 1000);
    }
};

// Watch for changes in lockoutTime prop
watch(() => props.lockoutTime, (newValue) => {
    remainingTime.value = newValue;
    if (newValue > 0) {
        startTimer();
    }
});

// Watch for isLocked changes
watch(() => props.isLocked, (newValue) => {
    if (newValue && props.lockoutTime > 0) {
        startTimer();
    } else if (!newValue) {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
        remainingTime.value = 0;
    }
});

onMounted(() => {
    if (props.isLocked && props.lockoutTime > 0) {
        startTimer();
    }
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
});
</script>

<template>
    <div class="space-y-3">
        <!-- Attempts remaining indicator -->
        <div v-if="!isLocked" class="flex items-center justify-between text-sm">
            <span class="text-gray-600">{{ t('auth.login_attempts_remaining') }}</span>
            <span :class="[
                'font-medium',
                attempts >= 4 ? 'text-green-600' : 
                attempts >= 3 ? 'text-yellow-600' : 
                attempts >= 2 ? 'text-orange-600' : 'text-red-600'
            ]">
                {{ attempts }}/{{ maxAttempts }}
            </span>
        </div>
        
        <!-- Progress bar -->
        <div v-if="!isLocked" class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div 
                :class="[
                    'h-full transition-all duration-300 ease-out',
                    getProgressColor
                ]"
                :style="{ width: `${progressPercentage}%` }"
            ></div>
        </div>
        
        <!-- Lockout message -->
        <div v-if="isLocked" class="bg-red-50 border border-red-200 rounded-lg p-3">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                <div class="text-sm">
                    <p class="font-medium text-red-800">{{ t('auth.account_locked', { minutes: Math.ceil(lockoutTime / 60) }) }}</p>
                    <p class="text-red-600">{{ t('auth.try_again_in') }} {{ timeLeft }}</p>
                </div>
            </div>
        </div>
        
        <!-- Warning message -->
        <div v-else-if="attempts <= 2" class="bg-orange-50 border border-orange-200 rounded-lg p-3">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-orange-800">
                    {{ t('auth.warning_multiple_attempts') }}
                </p>
            </div>
        </div>
    </div>
</template>
