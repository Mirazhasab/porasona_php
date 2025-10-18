// animated-alerts.js - Optional, overridden by global showAlert. Kept for compatibility.
// If other files call functions here, provide a bridge.
(function(){
  if (window.__animatedAlertsLoaded) return; window.__animatedAlertsLoaded = true;
  window.makeAlert = function(msg, type){ if (window.showAlert) window.showAlert(msg, type); };
})();
