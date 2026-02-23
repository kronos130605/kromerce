<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const timeLeft = ref({
    hours: 48,
    minutes: 0,
    seconds: 0
});

let interval;

const updateCountdown = () => {
    if (timeLeft.value.seconds > 0) {
        timeLeft.value.seconds--;
    } else if (timeLeft.value.minutes > 0) {
        timeLeft.value.minutes = 59;
        timeLeft.value.seconds = 59;
        timeLeft.value.hours--;
    } else if (timeLeft.value.hours > 0) {
        timeLeft.value.hours--;
        timeLeft.value.minutes = 59;
        timeLeft.value.seconds = 59;
    }
};

onMounted(() => {
    interval = setInterval(updateCountdown, 1000);
});

const formatTime = (value) => {
    return value.toString().padStart(2, '0');
};
</script>

<template>
    <div class="bg-gradient-to-r from-black to-gray-900 text-white rounded-2xl p-12">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4">{{ t('dashboard.flash_sale') }} - {{ t('dashboard.limited_time') }}</h2>
            <p class="text-xl mb-8">{{ t('dashboard.up_to_50_off') }}</p>
            <div class="flex items-center justify-center space-x-6">
                <div class="text-center">
                    <p class="text-sm text-gray-300">{{ t('dashboard.ends_in') }}:</p>
                    <div class="text-2xl font-mono font-bold">
                        {{ formatTime(timeLeft.hours) }}:{{ formatTime(timeLeft.minutes) }}:{{ formatTime(timeLeft.seconds) }}
                    </div>
                </div>
                <button class="px-8 py-3 bg-white text-black text-lg font-bold rounded hover:bg-gray-100 transition-colors">
                    {{ t('dashboard.shop_now') }}
                </button>
            </div>
        </div>
    </div>
</template>
