document.addEventListener('DOMContentLoaded', function () {
  var menuToggle = document.getElementById('menuToggle');
  var navLinks = document.querySelector('.links');
  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', function () {
      var open = navLinks.classList.toggle('mobile-open');
      navLinks.style.cssText = open
        ? 'display:flex;position:fixed;top:64px;left:16px;right:16px;background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:14px;flex-direction:column;gap:4px;box-shadow:var(--shadow);z-index:70;max-height:75vh;overflow:auto;'
        : '';
    });
  }
});
