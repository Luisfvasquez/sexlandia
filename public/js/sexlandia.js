/* =====================================================================
   SEXLANDIA · interacciones del storefront (sin dependencias)
   El carrito vive en Alpine (storefrontCart); esto cubre el resto.
   ===================================================================== */
(function () {
  'use strict';

  var root = document.documentElement;
  function lockScroll(on) { root.classList.toggle('sl-locked', !!on); }

  /* ---- Age gate ---------------------------------------------------- */
  function initAgeGate() {
    var gate = document.getElementById('age-gate');
    if (!gate) return;
    var KEY = 'sexlandia_age_ok';
    try {
      if (localStorage.getItem(KEY) === '1') { gate.remove(); return; }
    } catch (e) { /* modo privado: mostramos el gate igual */ }

    var yes = gate.querySelector('[data-age-yes]');
    var no = gate.querySelector('[data-age-no]');

    // Si el gate no tiene botón de confirmación, no bloqueamos la página.
    if (!yes) { gate.remove(); return; }

    lockScroll(true);
    gate.hidden = false;

    yes.addEventListener('click', function () {
      try { localStorage.setItem(KEY, '1'); } catch (e) {}
      lockScroll(false);
      gate.remove();
    });
    if (no) no.addEventListener('click', function () {
      window.location.href = 'https://www.google.com';
    });
  }

  /* ---- Scroll reveal --------------------------------------------- */
  function initReveal() {
    var items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    function showAll() {
      items.forEach(function (el) { el.classList.add('reveal--in'); });
    }

    if (!('IntersectionObserver' in window)) { showAll(); return; }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('reveal--in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
    items.forEach(function (el) { io.observe(el); });

    // Red de seguridad: si algo impide que el observer dispare, revela todo.
    setTimeout(showAll, 2500);
  }

  /* ---- Header compacto + barra de progreso ---------------------- */
  function initHeader() {
    var header = document.querySelector('.site-header');
    var bar = document.querySelector('.scroll-progress');
    function onScroll() {
      var y = window.scrollY || window.pageYOffset;
      if (header) header.classList.toggle('site-header--compact', y > 70);
      if (bar) {
        var h = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.transform = 'scaleX(' + (h > 0 ? y / h : 0) + ')';
      }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---- Menú móvil ---------------------------------------------- */
  function initMobileMenu() {
    var menu = document.getElementById('mobile-menu');
    var openBtn = document.querySelector('[data-menu-open]');
    var closeBtn = menu && menu.querySelector('[data-menu-close]');
    if (!menu || !openBtn) return;
    function set(open) {
      menu.classList.toggle('mobile-menu--open', open);
      lockScroll(open);
    }
    openBtn.addEventListener('click', function () { set(true); });
    if (closeBtn) closeBtn.addEventListener('click', function () { set(false); });
    menu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { set(false); });
    });
  }

  /* ---- FAQ acordeón ------------------------------------------- */
  function initFaq() {
    document.querySelectorAll('.faq__item button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var item = btn.closest('.faq__item');
        var open = item.classList.contains('is-open');
        document.querySelectorAll('.faq__item.is-open').forEach(function (o) {
          o.classList.remove('is-open');
          o.querySelector('button').setAttribute('aria-expanded', 'false');
        });
        if (!open) {
          item.classList.add('is-open');
          btn.setAttribute('aria-expanded', 'true');
        }
      });
    });
  }

  /* ---- Producto destacado: cambiar foto ---------------------- */
  function initFeaturedGallery() {
    var box = document.querySelector('[data-featured-gallery]');
    if (!box) return;
    var main = box.querySelector('[data-featured-main]');
    box.querySelectorAll('[data-featured-thumb]').forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        if (main) main.src = thumb.getAttribute('data-src');
        box.querySelectorAll('[data-featured-thumb]').forEach(function (t) { t.classList.remove('is-active'); });
        thumb.classList.add('is-active');
      });
    });
  }

  function boot() {
    // Cada init es independiente: si uno falla, los demás siguen funcionando.
    [initAgeGate, initReveal, initHeader, initMobileMenu, initFaq, initFeaturedGallery]
      .forEach(function (fn) {
        try { fn(); } catch (e) { if (window.console) console.error('[sexlandia]', e); }
      });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
