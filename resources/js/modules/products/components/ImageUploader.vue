<template>
    <div class="image-uploader">
        <!-- Upload Area -->
        <div
            @drop="handleDrop"
            @dragover.prevent="handleDragOver"
            @dragleave.prevent="handleDragLeave"
            @dragenter.prevent="handleDragEnter"
            class="border-2 border-dashed rounded-lg p-8 text-center transition-colors"
            :class="[
                isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300',
                images.length >= maxImages || uploading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-blue-400'
            ]"
            @click="triggerFileInput"
        >
            <div v-if="uploading" class="text-center">
                <svg class="animate-spin mx-auto h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                <p class="mt-4 text-sm text-gray-600">{{ t('common.loading') }}</p>
            </div>
            <template v-else>
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-900">
                        {{ t('products.hints.drag_drop_images') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ t('products.hints.image_formats') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ t('products.images.uploaded_count', { count: images.length, max: maxImages }) }}
                    </p>
                </div>
            </template>
            
            <input
                ref="fileInput"
                type="file"
                :accept="accept"
                :multiple="maxImages > 1"
                @change="handleFileSelect"
                class="hidden"
                :disabled="uploading"
            >
        </div>

        <!-- Image Gallery -->
        <div v-if="images.length > 0" class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('products.images.title') }}</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div
                    v-for="(image, index) in images"
                    :key="image.id || index"
                    class="relative group"
                >
                    <!-- Image Preview -->
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                        <img
                            :src="image.full_url || image.preview || image.url"
                            :alt="image.alt || `Image ${index + 1}`"
                            class="w-full h-full object-cover"
                        >
                    </div>
                    
                    <!-- Primary Badge -->
                <div
                        v-if="image.is_primary"
                        class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full"
                    >
                        {{ t('products.images.primary') }}
                    </div>
                    
                    <!-- Actions Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                        <!-- Set as Primary -->
                        <button
                            v-if="!image.is_primary"
                            @click="setAsPrimary(index)"
                            class="p-2 bg-white rounded-full hover:bg-blue-50 text-blue-600"
                            :title="t('products.images.set_primary')"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </button>
                        
                        <!-- Edit -->
                        <button
                            @click="editImage(index)"
                            class="p-2 bg-white rounded-full hover:bg-gray-50 text-gray-600"
                            :title="t('products.images.edit_details')"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        
                        <!-- Delete -->
                        <button
                            @click="removeImage(index)"
                            class="p-2 bg-white rounded-full hover:bg-red-50 text-red-600"
                            :title="t('products.images.remove')"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Order Controls -->
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button
                            v-if="index > 0"
                            @click="moveImage(index, index - 1)"
                            class="p-1 bg-white rounded shadow-md hover:bg-gray-50"
                            :title="t('products.images.move_left')"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button
                            v-if="index < images.length - 1"
                            @click="moveImage(index, index + 1)"
                            class="p-1 bg-white rounded shadow-md hover:bg-gray-50"
                            :title="t('products.images.move_right')"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Edit Modal -->
        <div
            v-if="editingImage !== null"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click="closeEditModal"
        >
            <div
                class="bg-white rounded-lg p-6 max-w-md w-full mx-4"
                @click.stop
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('products.images.modal.title') }}</h3>
                
                <div class="space-y-4">
                    <!-- Alt Text -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ t('products.images.modal.alt_text') }}
                        </label>
                        <input
                            v-model="editingImageData.alt"
                            type="text"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :placeholder="t('products.placeholders.alt_text')"
                        >
                    </div>
                    
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ t('products.images.modal.image_title') }}
                        </label>
                        <input
                            v-model="editingImageData.title"
                            type="text"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :placeholder="t('products.placeholders.image_title')"
                        >
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        @click="closeEditModal"
                        class="btn btn-outline"
                    >
                        {{ t('products.actions.cancel') }}
                    </button>
                    <button
                        @click="saveImageEdit"
                        class="btn btn-primary"
                    >
                        {{ t('products.images.modal.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

const { t } = useTranslations()

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    maxImages: {
        type: Number,
        default: 10
    },
    accept: {
        type: String,
        default: 'image/*'
    },
    maxSize: {
        type: Number,
        default: 5 * 1024 * 1024 // 5MB max
    },
    productId: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const isDragging = ref(false)
const images = ref([...props.modelValue])
const editingImage = ref(null)
const editingImageData = ref({})
const uploading = ref(false)

// Watch for changes and emit
watch(images, (newValue) => {
    emit('update:modelValue', newValue)
}, { deep: true })

// Sync local images when parent updates modelValue (e.g. when edit modal opens with existing images)
watch(() => props.modelValue, (newValue) => {
    if (!newValue) return
    const incomingJson = JSON.stringify(newValue)
    const localJson    = JSON.stringify(images.value)
    if (incomingJson !== localJson) {
        images.value = [...newValue]
    }
})

// Methods
const triggerFileInput = () => {
    if (images.value.length >= props.maxImages) return
    fileInput.value?.click()
}

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files)
    processFiles(files)
    event.target.value = '' // Reset input
}

const handleDrop = (event) => {
    event.preventDefault()
    isDragging.value = false
    
    if (images.value.length >= props.maxImages) return
    
    const files = Array.from(event.dataTransfer.files)
    processFiles(files)
}

const handleDragOver = (event) => {
    event.preventDefault()
    if (images.value.length < props.maxImages) {
        isDragging.value = true
    }
}

const handleDragLeave = (event) => {
    event.preventDefault()
    isDragging.value = false
}

const handleDragEnter = (event) => {
    event.preventDefault()
    if (images.value.length < props.maxImages) {
        isDragging.value = true
    }
}

const processFiles = async (files) => {
    const imageFiles = files.filter(file => file.type.startsWith('image/'))
    const remainingSlots = props.maxImages - images.value.length
    const filesToProcess = imageFiles.slice(0, remainingSlots)
    
    for (const file of filesToProcess) {
        if (file.size > props.maxSize) {
            const maxSizeMB = (props.maxSize / 1024 / 1024).toFixed(0)
            const fileSizeMB = (file.size / 1024 / 1024).toFixed(2)
            alert(`El archivo "${file.name}" (${fileSizeMB}MB) excede el límite de ${maxSizeMB}MB. Por favor, reduce el tamaño de la imagen o usa una imagen más pequeña.`)
            continue
        }

        // Always create temporary image - upload happens during product save
        const reader = new FileReader()
        reader.onload = (e) => {
            const newImage = {
                id: null,
                file: file, // Keep file reference for later upload
                url: e.target.result,
                preview: e.target.result,
                alt: '',
                title: '',
                order: images.value.length,
                is_primary: images.value.length === 0,
                isTemporary: true
            }
            images.value.push(newImage)
        }
        reader.readAsDataURL(file)
    }
}

const uploadImage = async (file, isPrimary = false) => {
    if (!props.productId) return

    uploading.value = true
    
    try {
        const formData = new FormData()
        formData.append('image', file)
        formData.append('is_primary', (isPrimary || images.value.length === 0) ? '1' : '0')
        formData.append('order', images.value.length)
        
        const response = await fetch(`/products/${props.productId}/images`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: formData
        })

        if (!response.ok) {
            throw new Error('Upload failed')
        }

        const data = await response.json()
        
        if (data.success) {
            images.value.push({
                id: data.data.id,
                url: data.data.thumbnail_url || data.data.url,
                full_url: data.data.full_url || data.data.url,
                preview: data.data.thumbnail_url || data.data.url,
                alt: data.data.alt || '',
                title: data.data.title || '',
                order: data.data.order,
                is_primary: data.data.is_primary,
                isTemporary: false
            })
        }
    } catch (error) {
        console.error('Upload error:', error)
        alert('Failed to upload image. Please try again.')
    } finally {
        uploading.value = false
    }
}

const removeImage = (index) => {
    const removedImage = images.value[index]
    
    // If image is saved on server (has id and not temporary), mark for deletion
    // The actual deletion happens when product is saved
    // Temporary images are just removed from the local array
    
    images.value.splice(index, 1)
    
    // If we removed the primary image, set the first remaining image as primary
    if (removedImage.is_primary && images.value.length > 0) {
        images.value[0].is_primary = true
    }
    
    // Update order for remaining images
    images.value.forEach((img, idx) => {
        img.order = idx
    })
}

const setAsPrimary = (index) => {
    // Remove primary from all images (both existing and temporary)
    images.value.forEach(img => {
        img.is_primary = false
    })
    
    // Set new primary
    images.value[index].is_primary = true
    
    // Reorder: move primary to index 0
    const primaryImage = images.value.splice(index, 1)[0]
    images.value.unshift(primaryImage)
    
    // Update order for all
    images.value.forEach((img, idx) => {
        img.order = idx
    })
}

const moveImage = (fromIndex, toIndex) => {
    const image = images.value.splice(fromIndex, 1)[0]
    images.value.splice(toIndex, 0, image)
    
    // Update order for all images
    images.value.forEach((img, idx) => {
        img.order = idx
    })
}

const editImage = (index) => {
    editingImage.value = index
    editingImageData.value = {
        alt: images.value[index].alt || '',
        title: images.value[index].title || ''
    }
}

const closeEditModal = () => {
    editingImage.value = null
    editingImageData.value = {}
}

const saveImageEdit = () => {
    if (editingImage.value !== null) {
        images.value[editingImage.value].alt = editingImageData.value.alt
        images.value[editingImage.value].title = editingImageData.value.title
    }
    closeEditModal()
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
