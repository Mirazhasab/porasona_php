/* auth-enhanced.js
 * Lightweight enhancements for auth pages (login/register)
 */
(function () {
  if (window.__authEnhancedLoaded) return;
  window.__authEnhancedLoaded = true;

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    // Password visibility toggles
    document.querySelectorAll('input[type="password"]').forEach(function (input) {
      if (input.dataset.toggleBound) return;
      input.dataset.toggleBound = '1';
      const wrapper = input.closest('.input-wrapper');
      if (!wrapper) return;
      const icon = document.createElement('i');
      icon.className = 'fas fa-eye';
      icon.style.cursor = 'pointer';
      icon.style.marginLeft = '8px';
      icon.addEventListener('click', function () {
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
      wrapper.appendChild(icon);
    });

    // Basic client-side validation message
    const forms = document.querySelectorAll('form');
    forms.forEach(function (form) {
      form.addEventListener('invalid', function () {
        window.showAlert && window.showAlert('Please fill in the required fields', 'warning');
      }, true);
    });
  });
})();
