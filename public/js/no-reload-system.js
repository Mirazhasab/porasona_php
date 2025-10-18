// no-reload-system.js - Placeholder to avoid 404s; integrates with Livewire if present
(function(){
  if (window.__noReloadSystemLoaded) return; window.__noReloadSystemLoaded = true;
  // If Livewire present, we can add a tiny hook to animate scroll to top on navigate
  document.addEventListener('livewire:navigated', function(){
    try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (_) { window.scrollTo(0,0); }
  });
})();
