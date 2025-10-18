// button-handler.js - Minor UX for buttons
(function(){
  if (window.__buttonHandlerLoaded) return; window.__buttonHandlerLoaded = true;
  document.addEventListener('click', function(e){
    const btn = e.target.closest('button, .btn');
    if (!btn) return;
    if (btn.dataset.once === 'true') { e.preventDefault(); e.stopPropagation(); return false; }
    if (btn.dataset.preventDouble === 'true') {
      btn.dataset.once = 'true';
      btn.disabled = true;
      setTimeout(()=>{ btn.disabled = false; btn.dataset.once = 'false'; }, 3000);
    }
  }, true);
})();
