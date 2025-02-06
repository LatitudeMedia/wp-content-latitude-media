const loadScriptsTimer = setTimeout(loadScripts, 5000);
const userInteractionEvents = [
  "mouseover",
  "keydown",
  "touchmove",
  "touchstart"
];
userInteractionEvents.forEach(function(event) {
  window.addEventListener(event, triggerScriptLoader, { passive: true });
});

function triggerScriptLoader() {
  loadScripts();
  clearTimeout(loadScriptsTimer);
  userInteractionEvents.forEach(function(event) {
    window.removeEventListener(event, triggerScriptLoader, { passive: true });
  });
}

function loadScripts() {
  document.querySelectorAll("script[data-type='lazy']").forEach(function(elem) {
    elem.setAttribute("src", elem.getAttribute("data-src"));
  });
}
