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
        const result = items.value.filter(item => isSelected(item[key]));
        console.log('[useSelectableItems] selectedItems:', result.map(i => ({ id: i[key], name: i.name })));
        return result;
    });

    const unselectedItems = computed(() => {
        const result = items.value.filter(item => !isSelected(item[key]));
        return result;
    });

    const filteredItems = computed(() => {
        const query = searchQuery.value.trim().toLowerCase();
        let result = unselectedItems.value;
        
        if (query) {
            result = result.filter(item => {
                const name = (item.name || '').toLowerCase();
                const description = (item.description || '').toLowerCase();
                return name.includes(query) || description.includes(query);
            });
        }
        
        console.log('[useSelectableItems] filteredItems (unselected):', result.map(i => ({ id: i[key], name: i.name })));
        return result;
    });

    const hasSelection = computed(() => selectedIds.value.length > 0);

    const selectionCount = computed(() => selectedIds.value.length);

    // Métodos
    function isSelected(id) {
        const strId = String(id);
        const result = selectedIds.value.some(selectedId => String(selectedId) === strId);
        return result;
    }

    function select(id) {
        const strId = String(id);
        console.log('[useSelectableItems] select called:', strId, 'current selected:', selectedIds.value);
        if (!isSelected(strId)) {
            selectedIds.value.push(strId);
            console.log('[useSelectableItems] added:', strId, 'new selected:', selectedIds.value);
        } else {
            console.log('[useSelectableItems] already selected:', strId);
        }
    }

    function deselect(id) {
        const strId = String(id);
        console.log('[useSelectableItems] deselect called:', strId, 'current selected:', selectedIds.value);
        const index = selectedIds.value.findIndex(selectedId => String(selectedId) === strId);
        if (index > -1) {
            selectedIds.value.splice(index, 1);
            console.log('[useSelectableItems] removed:', strId, 'new selected:', selectedIds.value);
        }
    }

    function toggle(id) {
        const strId = String(id);
        console.log('[useSelectableItems] toggle called:', strId, 'isSelected:', isSelected(strId));
        if (isSelected(strId)) {
            deselect(strId);
        } else {
            select(strId);
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
