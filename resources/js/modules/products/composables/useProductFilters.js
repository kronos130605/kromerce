import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * Composable para filtros avanzados de productos
 * 
 * @param {Object} initialFilters - Filtros iniciales desde el servidor
 * @returns {Object} Estado y métodos para filtros
 */
export function useProductFilters(initialFilters = {}) {
    // Estado de filtros
    const filters = ref({
        search: initialFilters.search || '',
        category_id: initialFilters.category_id || '',
        status: initialFilters.status || '',
        stock_status: initialFilters.stock_status || '',
        min_price: initialFilters.min_price || '',
        max_price: initialFilters.max_price || '',
        featured: initialFilters.featured || '',
        sort_by: initialFilters.sort_by || 'created_at',
        sort_order: initialFilters.sort_order || 'desc',
        per_page: initialFilters.per_page || 15
    });

    const isFilterActive = ref(false);
    const showFilters = ref(false);

    // Computed
    const hasActiveFilters = computed(() => {
        return !!(
            filters.value.search ||
            filters.value.category_id ||
            filters.value.status ||
            filters.value.stock_status ||
            filters.value.min_price ||
            filters.value.max_price ||
            filters.value.featured
        );
    });

    const activeFilterCount = computed(() => {
        let count = 0;
        if (filters.value.search) count++;
        if (filters.value.category_id) count++;
        if (filters.value.status) count++;
        if (filters.value.stock_status) count++;
        if (filters.value.min_price || filters.value.max_price) count++;
        if (filters.value.featured) count++;
        return count;
    });

    const filterParams = computed(() => {
        const params = {};
        Object.keys(filters.value).forEach(key => {
            if (filters.value[key]) {
                params[key] = filters.value[key];
            }
        });
        return params;
    });

    // Métodos
    const applyFilters = (preserveScroll = true) => {
        router.get('/products', filterParams.value, {
            preserveState: true,
            preserveScroll,
            only: ['products', 'statistics']
        });
    };

    const updateFilter = (key, value) => {
        filters.value[key] = value;
        applyFilters();
    };

    const clearFilters = () => {
        filters.value = {
            search: '',
            category_id: '',
            status: '',
            stock_status: '',
            min_price: '',
            max_price: '',
            featured: '',
            sort_by: 'created_at',
            sort_order: 'desc',
            per_page: 15
        };
        applyFilters();
    };

    const clearFilter = (key) => {
        filters.value[key] = '';
        applyFilters();
    };

    const toggleSort = (column) => {
        if (filters.value.sort_by === column) {
            filters.value.sort_order = filters.value.sort_order === 'asc' ? 'desc' : 'asc';
        } else {
            filters.value.sort_by = column;
            filters.value.sort_order = 'asc';
        }
        applyFilters();
    };

    const setPerPage = (perPage) => {
        filters.value.per_page = perPage;
        applyFilters();
    };

    const toggleFilters = () => {
        showFilters.value = !showFilters.value;
    };

    // Debounced search
    let searchTimeout = null;
    const searchProducts = (query) => {
        filters.value.search = query;
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    };

    // Price range
    const setPriceRange = (min, max) => {
        filters.value.min_price = min;
        filters.value.max_price = max;
        applyFilters();
    };

    // Quick filters
    const quickFilters = {
        all: () => {
            clearFilters();
        },
        active: () => {
            filters.value.status = 'active';
            applyFilters();
        },
        draft: () => {
            filters.value.status = 'draft';
            applyFilters();
        },
        lowStock: () => {
            filters.value.stock_status = 'low';
            applyFilters();
        },
        outOfStock: () => {
            filters.value.stock_status = 'out';
            applyFilters();
        },
        featured: () => {
            filters.value.featured = '1';
            applyFilters();
        }
    };

    return {
        // Estado
        filters,
        showFilters,
        
        // Computed
        hasActiveFilters,
        activeFilterCount,
        filterParams,
        
        // Métodos
        applyFilters,
        updateFilter,
        clearFilters,
        clearFilter,
        toggleSort,
        setPerPage,
        toggleFilters,
        searchProducts,
        setPriceRange,
        quickFilters
    };
}
