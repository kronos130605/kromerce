import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast.js';

/**
 * Composable para acciones masivas de productos
 * 
 * @returns {Object} Estado y métodos para bulk actions
 */
export function useBulkActions() {
    const { success: toastSuccess, error: toastError } = useToast();
    
    // Estado
    const selectedItems = ref([]);
    const isProcessing = ref(false);
    const showBulkMenu = ref(false);
    const confirmAction = ref(null);
    
    // Computed
    const hasSelection = computed(() => selectedItems.value.length > 0);
    const selectionCount = computed(() => selectedItems.value.length);
    const isAllSelected = computed(() => false); // Se calculará desde el componente padre
    
    // Métodos de selección
    const toggleItem = (itemId) => {
        const index = selectedItems.value.indexOf(itemId);
        if (index === -1) {
            selectedItems.value.push(itemId);
        } else {
            selectedItems.value.splice(index, 1);
        }
    };
    
    const toggleAll = (items) => {
        if (selectedItems.value.length === items.length) {
            selectedItems.value = [];
        } else {
            selectedItems.value = items.map(item => item.id);
        }
    };
    
    const clearSelection = () => {
        selectedItems.value = [];
    };
    
    const isSelected = (itemId) => {
        return selectedItems.value.includes(itemId);
    };
    
    // Acciones masivas
    const bulkUpdateStatus = async (status) => {
        if (!hasSelection.value) return;
        
        isProcessing.value = true;
        
        try {
            const response = await fetch('/products/bulk/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    ids: selectedItems.value,
                    status
                })
            });
            
            if (response.ok) {
                toastSuccess(`${selectionCount.value} products updated to ${status}`);
                clearSelection();
                router.reload({ only: ['products', 'statistics'] });
                return true;
            } else {
                toastError('Failed to update products');
                return false;
            }
        } catch (error) {
            console.error('Bulk update error:', error);
            toastError('Failed to update products');
            return false;
        } finally {
            isProcessing.value = false;
        }
    };
    
    const bulkDelete = async () => {
        if (!hasSelection.value) return;
        
        if (!confirm(`Are you sure you want to delete ${selectionCount.value} products? This action cannot be undone.`)) {
            return false;
        }
        
        isProcessing.value = true;
        
        try {
            const response = await fetch('/products/bulk/delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    ids: selectedItems.value
                })
            });
            
            if (response.ok) {
                toastSuccess(`${selectionCount.value} products deleted`);
                clearSelection();
                router.reload({ only: ['products', 'statistics'] });
                return true;
            } else {
                toastError('Failed to delete products');
                return false;
            }
        } catch (error) {
            console.error('Bulk delete error:', error);
            toastError('Failed to delete products');
            return false;
        } finally {
            isProcessing.value = false;
        }
    };
    
    const bulkUpdateCategory = async (categoryIds) => {
        if (!hasSelection.value) return;
        
        isProcessing.value = true;
        
        try {
            const response = await fetch('/products/bulk/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    ids: selectedItems.value,
                    category_ids: categoryIds
                })
            });
            
            if (response.ok) {
                toastSuccess(`Categories updated for ${selectionCount.value} products`);
                clearSelection();
                router.reload({ only: ['products', 'statistics'] });
                return true;
            } else {
                toastError('Failed to update categories');
                return false;
            }
        } catch (error) {
            console.error('Bulk update categories error:', error);
            toastError('Failed to update categories');
            return false;
        } finally {
            isProcessing.value = false;
        }
    };
    
    const bulkUpdatePrice = async (priceData) => {
        if (!hasSelection.value) return;
        
        isProcessing.value = true;
        
        try {
            const response = await fetch('/products/bulk/price', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    ids: selectedItems.value,
                    ...priceData
                })
            });
            
            if (response.ok) {
                toastSuccess(`Prices updated for ${selectionCount.value} products`);
                clearSelection();
                router.reload({ only: ['products', 'statistics'] });
                return true;
            } else {
                toastError('Failed to update prices');
                return false;
            }
        } catch (error) {
            console.error('Bulk update price error:', error);
            toastError('Failed to update prices');
            return false;
        } finally {
            isProcessing.value = false;
        }
    };
    
    const bulkExport = async (format = 'csv') => {
        if (!hasSelection.value) return;
        
        isProcessing.value = true;
        
        try {
            const params = new URLSearchParams({
                ids: selectedItems.value.join(','),
                format
            });
            
            window.location.href = `/products/export?${params.toString()}`;
            
            toastSuccess(`Exporting ${selectionCount.value} products...`);
            clearSelection();
            return true;
        } catch (error) {
            console.error('Bulk export error:', error);
            toastError('Failed to export products');
            return false;
        } finally {
            isProcessing.value = false;
        }
    };
    
    // Acciones rápidas
    const quickActions = {
        activate: () => bulkUpdateStatus('active'),
        deactivate: () => bulkUpdateStatus('inactive'),
        draft: () => bulkUpdateStatus('draft'),
        delete: () => bulkDelete(),
        exportCSV: () => bulkExport('csv'),
        exportExcel: () => bulkExport('xlsx')
    };
    
    return {
        // Estado
        selectedItems,
        isProcessing,
        showBulkMenu,
        confirmAction,
        
        // Computed
        hasSelection,
        selectionCount,
        isAllSelected,
        
        // Selección
        toggleItem,
        toggleAll,
        clearSelection,
        isSelected,
        
        // Acciones masivas
        bulkUpdateStatus,
        bulkDelete,
        bulkUpdateCategory,
        bulkUpdatePrice,
        bulkExport,
        quickActions
    };
}
