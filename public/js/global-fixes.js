/* global-fixes.js
 * Site-wide helpers and safe defaults loaded on all pages.
 */

(function () {
  // No conflict guard
  if (window.__globalFixesLoaded) return;
  window.__globalFixesLoaded = true;

  // Basic alert/toast helper used by Blade templates
  window.showAlert = function (message, type) {
    try {
      type = (type || 'info').toLowerCase();
      const colors = {
        success: '#16a34a',
        error: '#dc2626',
        info: '#2563eb',
        warning: '#d97706'
      };

      const containerId = 'mcqpro-alert-container';
      let container = document.getElementById(containerId);
      if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '2147483647';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '10px';
        document.body.appendChild(container);
      }

      const alert = document.createElement('div');
      alert.setAttribute('role', 'status');
      alert.style.padding = '12px 14px';
      alert.style.borderRadius = '8px';
      alert.style.color = '#fff';
      alert.style.background = colors[type] || colors.info;
      alert.style.boxShadow = '0 8px 24px rgba(0,0,0,.12)';
      alert.style.transform = 'translateY(-6px)';
      alert.style.opacity = '0';
      alert.style.transition = 'all 200ms ease';
      alert.textContent = String(message ?? '');
      container.appendChild(alert);

      ;(window.requestAnimationFrame || function(cb){return setTimeout(cb,16)})(() => {
        alert.style.transform = 'translateY(0)';
        alert.style.opacity = '1';
      });

      const ttl = 4000;
      setTimeout(() => {
        alert.style.transform = 'translateY(-6px)';
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.remove();
          if (!container.children.length) container.remove();
        }, 220);
      }, ttl);
    } catch (e) {
      // As a last resort fallback to native alert
      try { alert(message); } catch (_) {}
    }
  };

  // Prevent double form submission globally
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.__submitting) {
      e.preventDefault();
      return false;
    }
    form.__submitting = true;
    const btn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (btn) {
      btn.disabled = true;
      btn.classList && btn.classList.add('btn-loading');
    }
    setTimeout(() => { form.__submitting = false; if (btn) btn.disabled = false; }, 5000);
  }, true);

  // Optional: Lucide icons safe init if present
  try { if (window.lucide && lucide.createIcons) lucide.createIcons(); } catch(_) {}

  // :has CSS polyfill note (for older Safari/Chromium versions on cPanel)
  try {
    const supportsHas = CSS && CSS.supports && CSS.supports('selector(:has(*))');
    if (!supportsHas) {
      document.documentElement.setAttribute('data-no-has', '1');
      // For MCQ options: add a class to label when its input is checked
      document.addEventListener('change', function(e){
        const input = e.target;
        if (!input || (input.type !== 'radio' && input.type !== 'checkbox')) return;
        const label = input.closest('.mcq-option-label');
        if (!label) return;
        // For radios: clear siblings in the same group
        if (input.type === 'radio' && input.name) {
          document.querySelectorAll('input[type="radio"][name="' + CSS.escape(input.name) + '"]').forEach(r => {
            const rl = r.closest('.mcq-option-label'); if (rl) rl.classList.remove('has-checked');
          });
        }
        if (input.checked) label.classList.add('has-checked'); else label.classList.remove('has-checked');
      }, true);
    }
  } catch (_) {}
})();
