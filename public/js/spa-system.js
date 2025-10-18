// Single Page Application System
(function() {
    'use strict';
    
    let isLoading = false;
    let initialized = false;
    
    // Initialize SPA system
    function initSPA() {
        if (initialized) return;
        initialized = true;
        
        interceptLinks();
        interceptForms();
        handleBrowserNavigation();
    }
    
    // Handle link clicks
    function handleLinkClick(e) {
        const link = e.target.closest('a');
        if (!link) return;
        
        // Skip external links, downloads, and special links
        if (link.hasAttribute('download') || 
            link.href.includes('mailto:') || 
            link.href.includes('tel:') ||
            link.target === '_blank' ||
            link.classList.contains('no-spa')) {
            return;
        }
        
        // Skip if same page
        if (link.href === window.location.href) {
            e.preventDefault();
            return;
        }
        
        e.preventDefault();
        navigateTo(link.href);
    }
    
    // Handle form submissions
    function handleFormSubmit(e) {
        const form = e.target;
        
        // Skip file uploads and special forms
        if (form.enctype === 'multipart/form-data' || 
            form.classList.contains('no-spa')) {
            return;
        }
        
        e.preventDefault();
        submitForm(form);
    }
    
    // Intercept all navigation links
    function interceptLinks() {
        document.addEventListener('click', handleLinkClick);
    }
    
    // Intercept all form submissions
    function interceptForms() {
        document.addEventListener('submit', handleFormSubmit);
    }
    
    // Handle browser back/forward
    function handleBrowserNavigation() {
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.url) {
                loadPage(e.state.url, false);
            }
        });
    }
    
    // Navigate to URL
    function navigateTo(url) {
        if (isLoading) return;
        
        history.pushState({url: url}, '', url);
        loadPage(url, true);
    }
    
    // Submit form via AJAX
    function submitForm(form) {
        if (isLoading) return;
        
        isLoading = true;
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
        }
        
        const formData = new FormData(form);
        const method = form.method || 'POST';
        const action = form.action || window.location.href;
        
        fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            }
            return response.text();
        })
        .then(data => {
            if (typeof data === 'object' && data && data.success !== undefined) {
                // Handle JSON response
                if (data.success) {
                    ;(window.Swal ? Swal.fire : function(cfg){ window.showAlert(cfg.text || 'Success', 'success'); })({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Clear form immediately
                    form.reset();
                    const firstInput = form.querySelector('input, textarea');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 100);
                    }
                    
                    // Navigate to redirect URL if provided
                    if (data.redirect) {
                        setTimeout(() => navigateTo(data.redirect), 1500);
                    }
                } else {
                    ;(window.Swal ? Swal.fire : function(cfg){ window.showAlert(cfg.text || 'Error', 'error'); })({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        timer: 3000
                    });
                }
            } else if (typeof data === 'string') {
                // Check if it's a redirect response
                if (data.includes('Redirecting') || data.length < 500) {
                    // Likely a redirect, don't update page
                    return;
                }
                updatePage(data);
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            ;(window.Swal ? Swal.fire : function(cfg){ window.showAlert(cfg.text || 'Error', 'error'); })({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                timer: 3000
            });
        })
        .finally(() => {
            isLoading = false;
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Question';
            }
        });
    }
    
    // Load page via AJAX
    function loadPage(url, addToHistory = true) {
        if (isLoading) return;
        
        isLoading = true;
        showLoadingIndicator();
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            updatePage(html);
            if (addToHistory && window.location.href !== url) {
                history.pushState({url: url}, '', url);
            }
        })
        .catch(error => {
            console.error('Navigation error:', error);
            window.location.href = url;
        })
        .finally(() => {
            isLoading = false;
            hideLoadingIndicator();
        });
    }
    
    // Update page content
    function updatePage(html) {
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');
        
        // Update title
        document.title = newDoc.title;
        
        // Update main content
    const newContent = newDoc.querySelector('main');
    const currentContent = document.querySelector('main');
        
        if (newContent && currentContent) {
            currentContent.innerHTML = newContent.innerHTML;
        }
        
        // Update page header if exists
        const newHeader = newDoc.querySelector('header h2');
        const currentHeader = document.querySelector('header h2');
        if (newHeader && currentHeader) {
            currentHeader.textContent = newHeader.textContent;
        }
        
        const newSubtitle = newDoc.querySelector('header p');
        const currentSubtitle = document.querySelector('header p');
        if (newSubtitle && currentSubtitle) {
            currentSubtitle.textContent = newSubtitle.textContent;
        }
        
        // Handle session messages without executing scripts
        const sessionMessages = html.match(/<script>[\s\S]*?Swal\.fire[\s\S]*?<\/script>/g);
        if (sessionMessages) {
            sessionMessages.forEach(scriptTag => {
                const swalMatch = scriptTag.match(/Swal\.fire\(([^}]+})\);/);
                if (swalMatch) {
                    try {
                        const swalConfig = eval('(' + swalMatch[1] + ')');
                        if (window.Swal) Swal.fire(swalConfig); else window.showAlert(swalConfig.text || 'Notice', 'info');
                    } catch (e) {
                        console.warn('SweetAlert execution error:', e);
                    }
                }
            });
        }
        
        // Re-initialize components
        reinitializeComponents();
        
        // Scroll to top
        window.scrollTo(0, 0);
    }
    
    // Reinitialize components after page update
    function reinitializeComponents() {
        // Reinitialize Livewire if present
        if (window.Livewire) {
            window.Livewire.rescan();
        }
        
        // Reinitialize any custom components
        initializeCustomComponents();
        
        // Handle success messages
        if (window.handleSuccessMessages) {
            window.handleSuccessMessages();
        }
    }
    
    // Initialize custom components
    function initializeCustomComponents() {
        // Reinitialize dropdowns
        document.querySelectorAll('[onclick*="toggleAvatarDropdown"]').forEach(btn => {
            btn.onclick = function() {
                const type = this.getAttribute('onclick').match(/toggleAvatarDropdown\('(\w+)'\)/)[1];
                toggleAvatarDropdown(type);
            };
        });
        
        // Reinitialize mobile sidebar
        document.querySelectorAll('[onclick*="toggleMobileSidebar"]').forEach(btn => {
            btn.onclick = toggleMobileSidebar;
        });
        
        document.querySelectorAll('[onclick*="closeMobileSidebar"]').forEach(btn => {
            btn.onclick = closeMobileSidebar;
        });
    }
    
    // Show loading indicator
    function showLoadingIndicator() {
        let loader = document.getElementById('spa-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'spa-loader';
            loader.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; right: 0; height: 3px; background: #3b82f6; z-index: 9999; animation: loading 1s ease-in-out infinite;"></div>
                <style>
                    @keyframes loading {
                        0% { transform: translateX(-100%); }
                        50% { transform: translateX(0%); }
                        100% { transform: translateX(100%); }
                    }
                </style>
            `;
            document.body.appendChild(loader);
        }
        loader.style.display = 'block';
    }
    
    // Hide loading indicator
    function hideLoadingIndicator() {
        const loader = document.getElementById('spa-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSPA);
    } else {
        initSPA();
    }
    
    // Expose functions globally for compatibility
    window.spaNavigate = navigateTo;
    
})();