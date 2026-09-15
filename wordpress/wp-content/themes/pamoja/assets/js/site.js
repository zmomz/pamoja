/*
 * Pamoja — site script. The tree menu, the "you are here" mark, the About
 * climb, and the tiny keep-me-posted forms. Without JavaScript every page
 * still works: the header links go to their pages, the footer carries the
 * whole map, and the forms post normally.
 */
(function () {
  'use strict';

  var PARTS = ['soil', 'roots', 'trunk', 'branches', 'canopy', 'seeds', 'all'];

  function light(el, part) {
    if (!el) return;
    PARTS.forEach(function (p) { el.classList.remove('lit-' + p); });
    el.classList.add('lit-' + (part || 'all'));
    el.setAttribute('data-lit', part || 'all');
  }

  /* ---------- Header: condensed on scroll ---------- */
  var hd = document.getElementById('hd');
  if (hd) {
    var ticking = false;
    function onScroll() { ticking = false; hd.classList.toggle('scrolled', window.scrollY > 24); }
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } }, { passive: true });
    onScroll();
  }

  /* ---------- The tree menu ---------- */
  (function menu() {
    var menu = document.getElementById('menu');
    var veil = document.getElementById('veil');
    var mapbtn = document.getElementById('mapbtn');
    var burger = document.getElementById('burger');
    var minitree = document.getElementById('minitree');
    var menutree = document.getElementById('menutree');
    if (!menu || !veil || !hd) return;

    var restPart = minitree ? (minitree.getAttribute('data-lit') || 'all') : 'all';
    var lastFocus = null;
    var hoverTimer;
    // Everything the open map covers. Marked inert so keyboard and screen
    // reader users cannot wander behind it while it is up.
    var behind = Array.prototype.filter.call(document.body.children, function (el) {
      return el !== menu && el !== veil;
    });

    function setBehindInert(on) {
      behind.forEach(function (el) {
        if (on) { el.setAttribute('inert', ''); } else { el.removeAttribute('inert'); }
      });
      // The header holds the button that closes the map, so it stays usable.
      if (on && hd) hd.removeAttribute('inert');
    }

    function focusables() {
      return Array.prototype.filter.call(
        menu.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'),
        function (el) { return el.offsetParent !== null || el === document.activeElement; }
      );
    }

    function setMenu(open) {
      if (open) {
        lastFocus = document.activeElement;
        menu.hidden = false; veil.hidden = false;
        setBehindInert(true);
        // Let the display change land before the transition.
        requestAnimationFrame(function () { document.body.classList.add('menu-open'); hd.classList.add('menu-open'); });
      } else {
        document.body.classList.remove('menu-open'); hd.classList.remove('menu-open');
        setBehindInert(false);
        setTimeout(function () { if (!document.body.classList.contains('menu-open')) { menu.hidden = true; veil.hidden = true; } }, 260);
        light(menutree, 'all');
        if (lastFocus && lastFocus.focus) lastFocus.focus();
      }
      [mapbtn, burger].forEach(function (b) { if (b) b.setAttribute('aria-expanded', String(open)); });
      if (mapbtn) {
        var label = mapbtn.querySelector('.mapbtn-label');
        if (label) label.textContent = open ? label.getAttribute('data-open') : label.getAttribute('data-closed');
      }
      if (burger) burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      document.body.style.overflow = open && window.innerWidth <= 1000 ? 'hidden' : '';
    }
    function isOpen() { return document.body.classList.contains('menu-open'); }
    window.addEventListener('resize', function () {
      document.body.style.overflow = isOpen() && window.innerWidth <= 1000 ? 'hidden' : '';
    }, { passive: true });

    if (mapbtn) mapbtn.addEventListener('click', function () { setMenu(!isOpen()); });
    if (burger) burger.addEventListener('click', function () { setMenu(!isOpen()); });
    veil.addEventListener('click', function () { setMenu(false); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen()) { setMenu(false); return; }
      if (e.key !== 'Tab' || !isOpen()) return;
      // Keep Tab inside the map: the close button, then the places, and round.
      var stops = [];
      if (burger && burger.offsetParent !== null) stops.push(burger);
      if (mapbtn && mapbtn.offsetParent !== null) stops.push(mapbtn);
      stops = stops.concat(focusables());
      if (!stops.length) return;
      var first = stops[0], last = stops[stops.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      else if (stops.indexOf(document.activeElement) === -1) { e.preventDefault(); first.focus(); }
    });

    // Hover intent on the header links opens the map with their part lit.
    hd.querySelectorAll('.nav-link[data-part]').forEach(function (a) {
      a.addEventListener('mouseenter', function () {
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(function () { setMenu(true); light(menutree, a.getAttribute('data-part')); }, 260);
      });
      a.addEventListener('mouseleave', function () { clearTimeout(hoverTimer); });
    });
    menu.addEventListener('mouseleave', function () { if (window.innerWidth > 1000) hoverTimer = setTimeout(function () { setMenu(false); }, 380); });
    menu.addEventListener('mouseenter', function () { clearTimeout(hoverTimer); });
    hd.addEventListener('mouseleave', function () { if (isOpen() && window.innerWidth > 1000) hoverTimer = setTimeout(function () { if (!menu.matches(':hover')) setMenu(false); }, 380); });

    // Hovering or focusing a place lights its part of the tree.
    menu.querySelectorAll('[data-part]').forEach(function (a) {
      var on = function () {
        light(menutree, a.getAttribute('data-part'));
        menu.querySelectorAll('.hot').forEach(function (x) { x.classList.remove('hot'); });
        if (a.closest('.menu-cols')) a.classList.add('hot');
      };
      a.addEventListener('mouseenter', on);
      a.addEventListener('focus', on);
      a.addEventListener('mouseleave', function () { a.classList.remove('hot'); });
    });
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) setMenu(false); });

    // Keep the header mark honest if the page's lit part is changed later (About).
    document.addEventListener('pamoja:part', function (e) { light(minitree, e.detail || restPart); });
  })();

  /* ---------- About: the side tree lights the stop being read ---------- */
  (function climb() {
    var stops = Array.prototype.slice.call(document.querySelectorAll('.stop[data-part]'));
    var side = document.getElementById('sidetree');
    var you = document.getElementById('you-label');
    var toc = document.querySelector('.toc');
    if (!stops.length || !('IntersectionObserver' in window)) return;

    var current = null;
    function activate(stop) {
      if (stop === current) return;
      current = stop;
      var part = stop.getAttribute('data-part');
      light(side, part);
      document.dispatchEvent(new CustomEvent('pamoja:part', { detail: part }));
      var h = stop.querySelector('h2');
      if (you && h) you.textContent = h.textContent.replace(/\.$/, '');
      if (toc) toc.querySelectorAll('a[data-stop]').forEach(function (a) {
        var on = a.getAttribute('data-stop') === stop.id;
        a.classList.toggle('on', on);
        if (on) a.setAttribute('aria-current', 'true'); else a.removeAttribute('aria-current');
      });
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) activate(en.target); });
    }, { rootMargin: '-35% 0px -55% 0px', threshold: 0 });
    stops.forEach(function (s) { io.observe(s); });
  })();

  /* ---------- Chip bars on other pages: mark the section in view ---------- */
  (function toc() {
    var toc = document.querySelector('.toc');
    if (!toc || document.querySelector('.stop[data-part]') || !('IntersectionObserver' in window)) return;
    var links = Array.prototype.slice.call(toc.querySelectorAll('a[data-stop]'));
    var targets = links.map(function (a) { return document.getElementById(a.getAttribute('data-stop')); }).filter(Boolean);
    if (!targets.length) return;
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        links.forEach(function (a) {
          var on = a.getAttribute('data-stop') === en.target.id;
          a.classList.toggle('on', on);
          if (on) a.setAttribute('aria-current', 'true'); else a.removeAttribute('aria-current');
        });
      });
    }, { rootMargin: '-30% 0px -60% 0px', threshold: 0 });
    targets.forEach(function (t) { io.observe(t); });
  })();

  /* ---------- Engage: a button that jumps to the form also picks the role ---------- */
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[data-role]');
    if (!a) return;
    var radio = document.querySelector('#inquiry-form input[name="role"][value="' + a.getAttribute('data-role') + '"]');
    if (radio) radio.checked = true;
  });

  /* ---------- Video: our poster until they press play, then play here ----------
   * Nothing of the provider's is fetched, and no link of theirs is offered,
   * until the visitor asks for the video. Then the player replaces the poster
   * in place, already playing, so the page they are on is the page it plays on.
   */
  document.querySelectorAll('[data-player] .player-go').forEach(function (go) {
    go.addEventListener('click', function () {
      var src = go.getAttribute('data-src');
      if (!src) return;
      var frame = document.createElement('iframe');
      frame.src = src + (src.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
      frame.title = go.getAttribute('data-title') || '';
      frame.width = 1200;
      frame.height = 675;
      frame.frameBorder = '0';
      frame.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      frame.referrerPolicy = 'strict-origin-when-cross-origin';
      frame.allowFullscreen = true;
      go.replaceWith(frame);
      frame.focus();
    });
  });

  /* ---------- "Tell me when it's announced": stay on the page ---------- */
  document.querySelectorAll('form.keep').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.fetch || !window.FormData) return;
      e.preventDefault();
      var msg = form.querySelector('.keep-msg');
      var button = form.querySelector('button');
      var email = form.querySelector('input[type="email"]');
      if (!email || !email.value) { email && email.focus(); return; }
      if (button) button.disabled = true;
      fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' }, credentials: 'same-origin' })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok && j.ok, message: j.message }; }); })
        // Only a failed request falls back to posting the form normally —
        // never a problem drawing the answer, which would sign them up twice.
        .catch(function () { return null; })
        .then(function (res) {
          if (button) button.disabled = false;
          if (!res) { form.submit(); return; }
          if (msg) {
            msg.textContent = res.message || (res.ok ? form.getAttribute('data-done') : 'That didn’t go through. Please try again.');
            msg.classList.toggle('is-error', !res.ok);
          }
          form.classList.toggle('is-done', res.ok);
        });
    });
  });
})();
