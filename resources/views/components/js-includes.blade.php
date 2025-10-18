{{-- Comprehensive JavaScript Inclusion Component for MCQ PRO --}}
{{-- Usage: @include('components.js-includes') --}}
{{-- Note: This component should only be used for standalone pages that don't extend layouts --}}

<!-- Essential JavaScript Files (Multiple Loading Methods for cPanel Compatibility) -->
<!-- Global Fixes - Core functionality -->
<script src="{{ asset('js/global-fixes.js') }}"></script>
<script src="{{ url('js/global-fixes.js') }}"></script>
<script src="/js/global-fixes.js"></script>

<!-- Auth Enhanced - Authentication functionality -->
<script src="{{ asset('js/auth-enhanced.js') }}"></script>
<script src="{{ url('js/auth-enhanced.js') }}"></script>
<script src="/js/auth-enhanced.js"></script>

<!-- Dashboard Enhanced - Dashboard functionality -->
<script src="{{ asset('js/dashboard-enhanced.js') }}"></script>
<script src="{{ url('js/dashboard-enhanced.js') }}"></script>
<script src="/js/dashboard-enhanced.js"></script>

<!-- MCQ Exam - Exam functionality -->
<script src="{{ asset('js/mcq-exam.js') }}"></script>
<script src="{{ url('js/mcq-exam.js') }}"></script>
<script src="/js/mcq-exam.js"></script>

<!-- MCQ Utils - Common utilities -->
<script src="{{ asset('js/mcq-utils.js') }}"></script>
<script src="{{ url('js/mcq-utils.js') }}"></script>
<script src="/js/mcq-utils.js"></script>

<!-- Admin Enhanced - Admin functionality -->
<script src="{{ asset('js/admin-enhanced.js') }}"></script>
<script src="{{ url('js/admin-enhanced.js') }}"></script>
<script src="/js/admin-enhanced.js"></script>

<!-- Critical Inline JavaScript for Emergency Fallback -->
<script>
// CRITICAL JAVASCRIPT - ALWAYS AVAILABLE
(function() {
    'use strict';
    
    // Basic form validation
    function validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#ef4444';
                isValid = false;
            } else {
                field.style.borderColor = '';
            }
        });
        
        return isValid;
    }
    
    // Basic loading state
    function showLoading(button) {
        if (!button) return;
        button.disabled = true;
        const originalText = button.textContent;
        button.setAttribute('data-original-text', originalText);
        button.innerHTML = '<span style="display:inline-block;width:16px;height:16px;border:2px solid #fff;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;margin-right:8px;"></span>Loading...';
    }
    
    function hideLoading(button) {
        if (!button) return;
        button.disabled = false;
        const originalText = button.getAttribute('data-original-text');
        if (originalText) {
            button.textContent = originalText;
        }
    }
    
    // Auto-hide alerts
    function initAlerts() {
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    }
    
    // Form submission handlers
    function initForms() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                
                if (!validateForm(form)) {
                    e.preventDefault();
                    return false;
                }
                
                if (submitBtn) {
                    showLoading(submitBtn);
                    setTimeout(() => hideLoading(submitBtn), 10000);
                }
            });
        });
    }
    
    // Mobile menu toggle
    function initMobileMenu() {
        const toggleBtn = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobileOverlay');
        
        if (toggleBtn && sidebar && overlay) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });
            
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    }
    
    // Password visibility toggle
    function initPasswordToggle() {
        document.querySelectorAll('[data-toggle="password"]').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (input && icon) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.className = icon.className.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.className = icon.className.replace('fa-eye-slash', 'fa-eye');
                    }
                }
            });
        });
    }
    
    // Initialize all functionality
    function init() {
        initAlerts();
        initForms();
        initMobileMenu();
        initPasswordToggle();
    }
    
    // Auto-initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Re-initialize for dynamic content
    document.addEventListener('livewire:navigated', init);
    
    // Add CSS for spinner animation
    if (!document.getElementById('mcq-spinner-styles')) {
        const style = document.createElement('style');
        style.id = 'mcq-spinner-styles';
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Expose basic API
    window.MCQBasic = {
        validateForm,
        showLoading,
        hideLoading,
        init
    };
    
})();
</script>
