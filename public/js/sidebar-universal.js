/**
 * Universal Sidebar JavaScript - Works across all layouts
 */

(function() {
    'use strict';

    // Simple, direct functions that always work
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        if (!sidebar || !overlay) return;
        
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
    
    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        if (!sidebar || !overlay) return;
        
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    function toggleAvatarDropdown(type) {
        const dropdown = document.getElementById(type + 'AvatarDropdown');
        if (!dropdown) return;
        
        const isVisible = dropdown.classList.contains('show');
        document.querySelectorAll('.avatar-dropdown').forEach(d => d.classList.remove('show'));
        
        if (!isVisible) {
            dropdown.classList.add('show');
        }
    }
    
    // Force override any existing functions
    window.toggleMobileSidebar = toggleMobileSidebar;
    window.closeMobileSidebar = closeMobileSidebar;
    window.toggleAvatarDropdown = toggleAvatarDropdown;
    
    // Event listeners
    document.addEventListener('click', function(e) {
        if (e.target.closest('#sidebar a') || e.target.closest('#sidebar button[type="submit"]')) {
            if (window.innerWidth < 1024) {
                setTimeout(closeMobileSidebar, 100);
            }
        }
        
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('.avatar-dropdown').forEach(d => d.classList.remove('show'));
        }
    });
    
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeMobileSidebar();
        }
    });
    
    // Re-initialize after navigation
    document.addEventListener('livewire:navigated', function() {
        window.toggleMobileSidebar = toggleMobileSidebar;
        window.closeMobileSidebar = closeMobileSidebar;
        window.toggleAvatarDropdown = toggleAvatarDropdown;
    });
    
})();