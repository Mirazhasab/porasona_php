// alert-replacer.js - Replace native alert with custom toast (backfill)
(function(){
  if (window.__alertReplacerLoaded) return; window.__alertReplacerLoaded = true;
  const nativeAlert = window.alert.bind(window);
  window.alert = function(msg){ if (window.showAlert) return window.showAlert(String(msg), 'info'); nativeAlert(msg); };
})();
