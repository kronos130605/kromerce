import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

export function useSidebar() {
    // Sidebar state
    const isCollapsed = ref(false);
    const isMobileOpen = ref(false);
    const showExpandButton = ref(false);
    
    // Track if this is the first load (to avoid animation delay on restore)
    const isFirstLoad = ref(true);
    
    // Screen size detection
    const isMobile = ref(false);
    
    // Initialize collapsed state from localStorage
    const initializeCollapsedState = () => {
        const savedState = localStorage.getItem('sidebar-collapsed');
        if (savedState !== null) {
            isCollapsed.value = savedState === 'true';
            // If sidebar is collapsed, show expand button immediately
            // No animation needed when restoring state
            if (isCollapsed.value) {
                showExpandButton.value = true;
            }
        }
    };
    
    // Save collapsed state to localStorage
    const saveCollapsedState = () => {
        localStorage.setItem('sidebar-collapsed', isCollapsed.value.toString());
    };
    
    // Screen size detection
    const checkScreenSize = () => {
        isMobile.value = window.innerWidth < 1024;
        if (isMobile.value) {
            isMobileOpen.value = false; // Always closed on mobile by default
        }
    };
    
    // Toggle sidebar (for desktop)
    const toggleSidebar = () => {
        if (!isMobile.value) {
            isCollapsed.value = !isCollapsed.value;
            // Emit event for components that listen
            document.dispatchEvent(new CustomEvent('toggle-sidebar'));
        }
    };
    
    // Close mobile sidebar
    const closeMobileSidebar = () => {
        if (isMobile.value) {
            isMobileOpen.value = false;
        }
    };
    
    // Open mobile sidebar
    const openMobileSidebar = () => {
        if (isMobile.value) {
            isMobileOpen.value = true;
        }
    };
    
    // Initialize on mount
    onMounted(() => {
        initializeCollapsedState();
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);
        
        // Mark first load as complete after a short delay
        setTimeout(() => {
            isFirstLoad.value = false;
        }, 100);
    });
    
    onUnmounted(() => {
        window.removeEventListener('resize', checkScreenSize);
    });
    
    // Watch for collapse state changes and save to localStorage
    watch(isCollapsed, saveCollapsedState, { immediate: false });
    
    // Watch for collapse state changes (only for animation logic)
    watch(isCollapsed, (newValue, oldValue) => {
        // Skip animation logic on first load (when restoring state)
        if (isFirstLoad.value) {
            return;
        }
        
        if (newValue && !isMobile.value) {
            // Sidebar is being collapsed, wait for animation then show button
            showExpandButton.value = false;
            setTimeout(() => {
                showExpandButton.value = true;
            }, 300); // Wait for sidebar collapse animation
        } else if (!newValue && !isMobile.value) {
            // Sidebar is being expanded, hide button immediately
            showExpandButton.value = false;
        }
    });
    
    return {
        // State
        isCollapsed,
        isMobileOpen,
        showExpandButton,
        isMobile,
        isFirstLoad,
        
        // Methods
        toggleSidebar,
        closeMobileSidebar,
        openMobileSidebar,
        
        // Computed properties
        sidebarClasses: computed(() => [
            isMobileOpen.value ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            isCollapsed.value ? 'w-16 lg:w-16' : 'w-64 lg:w-64',
            'overflow-hidden'
        ]),
        
        // Utility methods
        resetSidebar: () => {
            isCollapsed.value = false;
            isMobileOpen.value = false;
            showExpandButton.value = false;
            localStorage.removeItem('sidebar-collapsed');
        }
    };
}
