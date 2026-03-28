import { ref, computed, unref } from 'vue';

/**
 * Composable para manejar la selección de items
 * Útil para selects múltiples, checkboxes, badges seleccionables, etc.
 * 
 * @param {Object} options
 * @param {Array|Ref} options.items - Lista de items disponibles (puede ser ref o array)
 * @param {String} options.key - Propiedad única de cada item (default: 'id')
 * @param {Array} options.initialSelected - IDs inicialmente seleccionados
 * @returns {Object}
 */
export function useSelectableItems(options = {}) {
    const {
        items: itemsInput = [],
        key = 'id',
        initialSelected = []
    } = options;

    // Estado reactivo
    const selectedIds = ref([...initialSelected.map(id => String(id))]);
    const searchQuery = ref('');

    // Items como referencia computada para manejar tanto refs como arrays
    const items = computed(() => unref(itemsInput) || []);

    // Getters computados
    const selectedItems = computed(() => {
        return items.value.filter(item => isSelected(item[key]));
    });

    const unselectedItems = computed(() => {
        return items.value.filter(item => !isSelected(item[key]));
    });

    const filteredItems = computed(() => {
        const query = searchQuery.value.trim().toLowerCase();
        // Siempre mostrar solo las no seleccionadas
        let items = unselectedItems.value;
        
        if (query) {
            items = items.filter(item => {
                const name = (item.name || '').toLowerCase();
                const description = (item.description || '').toLowerCase();
                return name.includes(query) || description.includes(query);
            });
        }
        
        return items;
    });

    const hasSelection = computed(() => selectedIds.value.length > 0);

    const selectionCount = computed(() => selectedIds.value.length);

    // Métodos
    function isSelected(id) {
        // Asegurar comparación consistente convirtiendo a string
        const strId = String(id);
        return selectedIds.value.some(selectedId => String(selectedId) === strId);
    }

    function select(id) {
        const strId = String(id);
        if (!isSelected(strId)) {
            selectedIds.value.push(strId);
        }
    }

    function deselect(id) {
        const strId = String(id);
        const index = selectedIds.value.findIndex(selectedId => String(selectedId) === strId);
        if (index > -1) {
            selectedIds.value.splice(index, 1);
        }
    }

    function toggle(id) {
        if (isSelected(id)) {
            deselect(id);
        } else {
            select(id);
        }
    }

    function selectAll() {
        items.value.forEach(item => {
            if (!isSelected(item[key])) {
                selectedIds.value.push(String(item[key]));
            }
        });
    }

    function clearAll() {
        selectedIds.value = [];
    }

    function setSelection(ids) {
        selectedIds.value = ids.map(id => String(id));
    }

    function setSearch(query) {
        searchQuery.value = query;
    }

    function clearSearch() {
        searchQuery.value = '';
    }

    return {
        // Estado
        selectedIds,
        searchQuery,
        
        // Computados
        selectedItems,
        unselectedItems,
        filteredItems,
        hasSelection,
        selectionCount,
        
        // Métodos
        isSelected,
        select,
        deselect,
        toggle,
        selectAll,
        clearAll,
        setSelection,
        setSearch,
        clearSearch
    };
}

export default useSelectableItems;
