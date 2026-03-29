import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast.js';

/**
 * Composable para gestión de variantes de producto
 * 
 * @param {String} productId - ID del producto
 * @returns {Object} Estado y métodos para gestión de variantes
 */
export function useVariants(productId) {
    const { success: toastSuccess, error: toastError } = useToast();
    
    // Estado
    const variants = ref([]);
    const loading = ref(false);
    const selectedVariant = ref(null);
    const isModalOpen = ref(false);
    const isEditing = ref(false);
    
    // Formulario de variante
    const form = ref({
        sku: '',
        name: '',
        price: '',
        sale_price: '',
        stock_quantity: 0,
        weight: '',
        is_default: false,
        is_active: true,
        attribute_values: []
    });
    
    const errors = ref({});
    
    // Computed
    const hasVariants = computed(() => variants.value.length > 0);
    const activeVariants = computed(() => variants.value.filter(v => v.is_active));
    const defaultVariant = computed(() => variants.value.find(v => v.is_default));
    
    const totalStock = computed(() => {
        return variants.value.reduce((sum, variant) => sum + (variant.stock_quantity || 0), 0);
    });
    
    const lowestPrice = computed(() => {
        if (!hasVariants.value) return null;
        const prices = variants.value
            .filter(v => v.is_active)
            .map(v => parseFloat(v.sale_price || v.price));
        return Math.min(...prices);
    });
    
    const highestPrice = computed(() => {
        if (!hasVariants.value) return null;
        const prices = variants.value
            .filter(v => v.is_active)
            .map(v => parseFloat(v.price));
        return Math.max(...prices);
    });
    
    // Métodos CRUD
    const fetchVariants = async () => {
        if (!productId) return;
        
        loading.value = true;
        try {
            const response = await fetch(`/products/${productId}/variants`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                variants.value = data.data || [];
            }
        } catch (error) {
            console.error('Failed to fetch variants:', error);
            toastError('Failed to load variants');
        } finally {
            loading.value = false;
        }
    };
    
    const createVariant = async (data) => {
        loading.value = true;
        errors.value = {};
        
        try {
            const response = await fetch(`/products/${productId}/variants`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                variants.value.push(result.data);
                toastSuccess('Variant created successfully');
                closeModal();
                return true;
            } else {
                errors.value = result.errors || {};
                toastError(result.message || 'Failed to create variant');
                return false;
            }
        } catch (error) {
            console.error('Failed to create variant:', error);
            toastError('Failed to create variant');
            return false;
        } finally {
            loading.value = false;
        }
    };
    
    const updateVariant = async (variantId, data) => {
        loading.value = true;
        errors.value = {};
        
        try {
            const response = await fetch(`/products/${productId}/variants/${variantId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                const index = variants.value.findIndex(v => v.id === variantId);
                if (index !== -1) {
                    variants.value[index] = result.data;
                }
                toastSuccess('Variant updated successfully');
                closeModal();
                return true;
            } else {
                errors.value = result.errors || {};
                toastError(result.message || 'Failed to update variant');
                return false;
            }
        } catch (error) {
            console.error('Failed to update variant:', error);
            toastError('Failed to update variant');
            return false;
        } finally {
            loading.value = false;
        }
    };
    
    const deleteVariant = async (variantId) => {
        loading.value = true;
        
        try {
            const response = await fetch(`/products/${productId}/variants/${variantId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                variants.value = variants.value.filter(v => v.id !== variantId);
                toastSuccess('Variant deleted successfully');
                return true;
            } else {
                toastError('Failed to delete variant');
                return false;
            }
        } catch (error) {
            console.error('Failed to delete variant:', error);
            toastError('Failed to delete variant');
            return false;
        } finally {
            loading.value = false;
        }
    };
    
    const bulkUpdateVariants = async (variantsData) => {
        loading.value = true;
        
        try {
            const response = await fetch(`/products/${productId}/variants/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ variants: variantsData })
            });
            
            const result = await response.json();
            
            if (response.ok) {
                variants.value = result.data;
                toastSuccess('Variants updated successfully');
                return true;
            } else {
                toastError(result.message || 'Failed to update variants');
                return false;
            }
        } catch (error) {
            console.error('Failed to bulk update variants:', error);
            toastError('Failed to update variants');
            return false;
        } finally {
            loading.value = false;
        }
    };
    
    const updateStock = async (variantId, quantity) => {
        try {
            const response = await fetch(`/products/${productId}/variants/${variantId}/stock`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ stock_quantity: quantity })
            });
            
            if (response.ok) {
                const index = variants.value.findIndex(v => v.id === variantId);
                if (index !== -1) {
                    variants.value[index].stock_quantity = quantity;
                }
                return true;
            }
            return false;
        } catch (error) {
            console.error('Failed to update stock:', error);
            return false;
        }
    };
    
    // Modal actions
    const openCreate = () => {
        resetForm();
        isEditing.value = false;
        isModalOpen.value = true;
    };
    
    const openEdit = (variant) => {
        selectedVariant.value = variant;
        populateForm(variant);
        isEditing.value = true;
        isModalOpen.value = true;
    };
    
    const closeModal = () => {
        isModalOpen.value = false;
        selectedVariant.value = null;
        resetForm();
        errors.value = {};
    };
    
    const save = async () => {
        const data = { ...form.value };
        
        if (isEditing.value && selectedVariant.value) {
            return await updateVariant(selectedVariant.value.id, data);
        } else {
            return await createVariant(data);
        }
    };
    
    // Helpers
    const resetForm = () => {
        form.value = {
            sku: '',
            name: '',
            price: '',
            sale_price: '',
            stock_quantity: 0,
            weight: '',
            is_default: false,
            is_active: true,
            attribute_values: []
        };
    };
    
    const populateForm = (variant) => {
        form.value = {
            sku: variant.sku || '',
            name: variant.name || '',
            price: variant.price || '',
            sale_price: variant.sale_price || '',
            stock_quantity: variant.stock_quantity || 0,
            weight: variant.weight || '',
            is_default: variant.is_default || false,
            is_active: variant.is_active ?? true,
            attribute_values: variant.attribute_values?.map(av => av.id) || []
        };
    };
    
    const setDefaultVariant = async (variantId) => {
        // Unset all other defaults
        variants.value.forEach(v => {
            if (v.id !== variantId) {
                v.is_default = false;
            }
        });
        
        // Set new default
        const variant = variants.value.find(v => v.id === variantId);
        if (variant) {
            variant.is_default = true;
            await updateVariant(variantId, { is_default: true });
        }
    };
    
    return {
        // Estado
        variants,
        loading,
        selectedVariant,
        isModalOpen,
        isEditing,
        form,
        errors,
        
        // Computed
        hasVariants,
        activeVariants,
        defaultVariant,
        totalStock,
        lowestPrice,
        highestPrice,
        
        // Métodos CRUD
        fetchVariants,
        createVariant,
        updateVariant,
        deleteVariant,
        bulkUpdateVariants,
        updateStock,
        
        // Modal
        openCreate,
        openEdit,
        closeModal,
        save,
        
        // Helpers
        setDefaultVariant
    };
}
