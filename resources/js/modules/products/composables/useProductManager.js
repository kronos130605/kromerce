import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

/**
 * Composable para gestión de productos con patrón modal/slider
 *
 * @param {Object} options - Configuración inicial
 * @param {Array} options.initialProducts - Productos iniciales
 * @param {Object} options.initialFilters - Filtros iniciales
 * @returns {Object} Estado y métodos para gestión de productos
 */
export function useProductManager(options = {}) {
    const { t } = useI18n();
    // Estado de la lista
    const products = ref(options.initialProducts || []);
    const filters = ref(options.initialFilters || {});
    const loading = ref(false);
    const selectedProduct = ref(null);

    // Estado de modals
    const isModalOpen = ref(false);
    const isViewOpen = ref(false);
    const isEditing = ref(false);
    const currentStep = ref(0);

    // Formulario
    const form = ref({
        name: '',
        description: '',
        base_price: '',
        sale_price: '',
        cost: '',
        sku: '',
        barcode: '',
        base_currency: 'USD',
        stock_quantity: 0,
        low_stock_threshold: 10,
        manage_stock: true,
        allow_backorders: false,
        status: 'draft',
        category_ids: [],
        tags: [],
        images: [],
        seo_title: '',
        seo_description: '',
        seo_keywords: '',
    });

    const steps = [
        { id: 'basic', titleKey: 'products.steps.basic', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        { id: 'pricing', titleKey: 'products.steps.pricing', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08.402-2.599-1' },
        { id: 'media', titleKey: 'products.steps.media', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
        { id: 'seo', titleKey: 'products.steps.seo', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' },
    ];

    const errors = ref({});

    // Computed
    const canSave = computed(() => {
        return form.value.name && form.value.base_price && !loading.value;
    });

    const isFirstStep = computed(() => currentStep.value === 0);
    const isLastStep = computed(() => currentStep.value === steps.length - 1);

    // Acciones de modal
    const openCreate = () => {
        selectedProduct.value = null;
        isEditing.value = false;
        resetForm();
        currentStep.value = 0;
        isModalOpen.value = true;
    };

    const openEdit = (product) => {
        selectedProduct.value = { ...product };
        isEditing.value = true;
        populateForm(product);
        currentStep.value = 0;
        isModalOpen.value = true;
    };

    const openView = (product) => {
        selectedProduct.value = product;
        isViewOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
        errors.value = {};
    };

    const closeView = () => {
        isViewOpen.value = false;
        selectedProduct.value = null;
    };

    // Navegación del wizard
    const nextStep = () => {
        if (validateCurrentStep() && !isLastStep.value) {
            currentStep.value++;
        }
    };

    const prevStep = () => {
        if (!isFirstStep.value) {
            currentStep.value--;
        }
    };

    const goToStep = (index) => {
        if (index >= 0 && index < steps.length) {
            currentStep.value = index;
        }
    };

    // Validación por paso
    const validateCurrentStep = () => {
        errors.value = {};

        switch (steps[currentStep.value].id) {
            case 'basic':
                if (!form.value.name?.trim()) {
                    errors.value.name = t('products.validation.name_required');
                }
                break;
            case 'pricing':
                if (!form.value.base_price || form.value.base_price <= 0) {
                    errors.value.base_price = t('products.validation.price_required');
                }
                break;
        }

        return Object.keys(errors.value).length === 0;
    };

    // CRUD
    const save = () => {
        if (!validateAllSteps()) return;

        loading.value = true;
        const payload = buildPayload();

        if (isEditing.value && selectedProduct.value?.id) {
            router.put(`/products/${selectedProduct.value.id}`, payload, {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    refreshProducts();
                },
                onError: (err) => {
                    errors.value = err;
                },
                onFinish: () => {
                    loading.value = false;
                },
            });
        } else {
            router.post('/products', payload, {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    refreshProducts();
                },
                onError: (err) => {
                    errors.value = err;
                },
                onFinish: () => {
                    loading.value = false;
                },
            });
        }
    };

    const confirmDelete = (product) => {
        if (!confirm(t('products.messages.delete_confirmation', { name: product.name }))) {
            return;
        }

        loading.value = true;
        router.delete(`/products/${product.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                refreshProducts();
            },
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    const refreshProducts = () => {
        router.reload({ only: ['products'] });
    };

    // Helpers
    const resetForm = () => {
        form.value = {
            name: '',
            description: '',
            base_price: '',
            sale_price: '',
            cost: '',
            sku: '',
            barcode: '',
            base_currency: 'USD',
            stock_quantity: 0,
            low_stock_threshold: 10,
            manage_stock: true,
            allow_backorders: false,
            status: 'draft',
            category_ids: [],
            tags: [],
            images: [],
            seo_title: '',
            seo_description: '',
            seo_keywords: '',
        };
        errors.value = {};
    };

    const populateForm = (product) => {
        form.value = {
            name: product.name || '',
            description: product.description || '',
            base_price: product.base_price || '',
            sale_price: product.sale_price || '',
            cost: product.cost || '',
            sku: product.sku || '',
            barcode: product.barcode || '',
            base_currency: product.base_currency || 'USD',
            stock_quantity: product.stock_quantity || 0,
            low_stock_threshold: product.low_stock_threshold || 10,
            manage_stock: product.manage_stock ?? true,
            allow_backorders: product.allow_backorders || false,
            status: product.status || 'draft',
            category_ids: product.category_ids || [],
            tags: product.tags || [],
            images: product.images || [],
            seo_title: product.seo_title || '',
            seo_description: product.seo_description || '',
            seo_keywords: product.seo_keywords || '',
        };
    };

    const buildPayload = () => {
        return { ...form.value };
    };

    const validateAllSteps = () => {
        errors.value = {};

        if (!form.value.name?.trim()) {
            errors.value.name = t('products.validation.name_required');
        }
        if (!form.value.base_price || form.value.base_price <= 0) {
            errors.value.base_price = t('products.validation.price_required');
        }

        return Object.keys(errors.value).length === 0;
    };

    return {
        // Estado
        products,
        filters,
        loading,
        selectedProduct,
        isModalOpen,
        isViewOpen,
        isEditing,
        currentStep,
        form,
        steps,
        errors,

        // Computed
        canSave,
        isFirstStep,
        isLastStep,

        // Acciones modal
        openCreate,
        openEdit,
        openView,
        closeModal,
        closeView,

        // Wizard
        nextStep,
        prevStep,
        goToStep,

        // CRUD
        save,
        confirmDelete,
        refreshProducts,
    };
}
