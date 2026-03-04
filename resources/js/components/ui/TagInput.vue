<template>
    <div class="tag-input">
        <div class="flex flex-wrap gap-2 p-2 border rounded-lg min-h-[42px] focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent">
            <!-- Selected Tags -->
            <span
                v-for="(tag, index) in selectedTags"
                :key="tag.id || tag.name"
                class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm"
            >
                {{ tag.name }}
                <button
                    @click="removeTag(index)"
                    type="button"
                    class="text-blue-600 hover:text-blue-800"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </span>
            
            <!-- Input -->
            <input
                v-model="inputValue"
                @keydown="handleKeydown"
                @input="handleInput"
                @focus="showDropdown = true"
                @blur="handleBlur"
                type="text"
                :placeholder="placeholder"
                class="flex-1 min-w-[120px] outline-none bg-transparent"
            >
        </div>
        
        <!-- Dropdown -->
        <div
            v-show="showDropdown && filteredTags.length > 0"
            class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"
        >
            <div
                v-for="tag in filteredTags"
                :key="tag.id"
                @click="selectTag(tag)"
                class="px-3 py-2 hover:bg-gray-50 cursor-pointer flex items-center justify-between"
            >
                <span>{{ tag.name }}</span>
                <span v-if="tag.color" class="w-3 h-3 rounded-full" :style="{ backgroundColor: tag.color }"></span>
            </div>
        </div>
        
        <!-- Create New Tag Option -->
        <div
            v-show="showDropdown && inputValue && !filteredTags.length"
            @click="createNewTag"
            class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg px-3 py-2 hover:bg-gray-50 cursor-pointer"
        >
            <span class="text-blue-600">Create "{{ inputValue }}"</span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    availableTags: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: 'Add tags...'
    },
    maxTags: {
        type: Number,
        default: 10
    }
})

const emit = defineEmits(['update:modelValue', 'tag-created'])

const inputValue = ref('')
const showDropdown = ref(false)
const selectedTags = ref([])

// Initialize with modelValue
watch(() => props.modelValue, (newValue) => {
    selectedTags.value = [...newValue]
}, { immediate: true })

// Watch selectedTags and emit changes
watch(selectedTags, (newValue) => {
    emit('update:modelValue', newValue)
}, { deep: true })

// Computed
const filteredTags = computed(() => {
    if (!inputValue.value) return props.availableTags
    
    const existingTagNames = selectedTags.value.map(tag => tag.name.toLowerCase())
    
    return props.availableTags.filter(tag => 
        tag.name.toLowerCase().includes(inputValue.value.toLowerCase()) &&
        !existingTagNames.includes(tag.name.toLowerCase())
    )
})

// Methods
const handleKeydown = (event) => {
    if (event.key === 'Enter') {
        event.preventDefault()
        
        if (filteredTags.value.length > 0) {
            selectTag(filteredTags.value[0])
        } else if (inputValue.value) {
            createNewTag()
        }
    } else if (event.key === 'Backspace' && !inputValue.value && selectedTags.value.length > 0) {
        removeTag(selectedTags.value.length - 1)
    } else if (event.key === 'Escape') {
        showDropdown.value = false
    }
}

const handleInput = () => {
    showDropdown.value = true
}

const handleBlur = () => {
    // Delay hiding dropdown to allow click events
    setTimeout(() => {
        showDropdown.value = false
    }, 200)
}

const selectTag = (tag) => {
    if (selectedTags.value.length >= props.maxTags) {
        return
    }
    
    selectedTags.value.push(tag)
    inputValue.value = ''
    showDropdown.value = false
}

const removeTag = (index) => {
    selectedTags.value.splice(index, 1)
}

const createNewTag = () => {
    if (!inputValue.value || selectedTags.value.length >= props.maxTags) {
        return
    }
    
    const newTag = {
        id: null, // Will be set by backend
        name: inputValue.value.trim(),
        color: null
    }
    
    selectedTags.value.push(newTag)
    emit('tag-created', newTag)
    
    inputValue.value = ''
    showDropdown.value = false
}
</script>

<style scoped>
.tag-input {
    position: relative;
}
</style>
