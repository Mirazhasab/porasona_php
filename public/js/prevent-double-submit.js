// Prevent Multiple Form Submissions
(function() {
    'use strict';
    
    let submittedForms = new Set();
    
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const formId = form.id || form.action || 'default-form';
        
        // Check if this form was already submitted
        if (submittedForms.has(formId)) {
            e.preventDefault();
            return false;
        }
        
        // Mark form as submitted
        submittedForms.add(formId);
        
        // Disable submit button
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
        }
        
        // Clear the flag after 5 seconds (for error cases)
        setTimeout(() => {
            submittedForms.delete(formId);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Question';
            }
        }, 5000);
    });
    
    // Handle success messages
    document.addEventListener('DOMContentLoaded', function() {
        const successMsg = document.querySelector('.alert-success, [class*="success"]');
        if (successMsg && successMsg.textContent.includes('saved successfully')) {
            alert('Question saved successfully!');
            
            // Clear form
            const form = document.querySelector('form');
            if (form) {
                form.reset();
                const firstInput = form.querySelector('input[type="text"], textarea');
                if (firstInput) firstInput.focus();
            }
            
            // Clear all submission flags
            submittedForms.clear();
        }
    });
})();