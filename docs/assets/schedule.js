/*
 * Waeverstad Schedule: day switching, the NOW line, and stars for "Mijn programma".
 * The server-rendered HTML works without this script; this only enhances it.
 *
 * Demo tip: ?now=2026-02-14T16:20 pretends it is that moment.
 */
(function () {
	'use strict';

	var TIME_ZONE = 'Europe/Amsterdam';

	function currentMoment() {
		var override = null;
		try {
			override = new URLSearchParams(window.location.search).get('now');
		} catch (e) {}
		if (override && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(override)) {
			var hm = override.slice(11).split(':');
			return { date: override.slice(0, 10), hour: +hm[0] + hm[1] / 60, simulated: true };
		}
		var parts = {};
		new Intl.DateTimeFormat('nl-NL', {
			timeZone: TIME_ZONE, year: 'numeric', month: '2-digit', day: '2-digit',
			hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
		}).formatToParts(new Date()).forEach(function (p) { parts[p.type] = p.value; });
		return { date: parts.year + '-' + parts.month + '-' + parts.day, hour: +parts.hour + parts.minute / 60 };
	}

	function formatClock(hour) {
		var h = Math.floor(hour);
		var m = Math.round((hour - h) * 60);
		if (m === 60) { h += 1; m = 0; }
		return (h % 24 < 10 ? '0' : '') + (h % 24) + ':' + (m < 10 ? '0' : '') + m;
	}

	function loadSet(key) {
		try {
			var raw = window.localStorage.getItem(key);
			var list = raw ? JSON.parse(raw) : [];
			return new Set(Array.isArray(list) ? list : []);
		} catch (e) {
			return new Set();
		}
	}

	function saveSet(key, set) {
		try {
			window.localStorage.setItem(key, JSON.stringify(Array.from(set)));
		} catch (e) {}
	}

	function init(root) {
		var tabs = [].slice.call(root.querySelectorAll('.wvp-daytab'));
		var panels = [].slice.call(root.querySelectorAll('.wvp-day'));
		if (!tabs.length) return;

		var start = parseFloat(root.getAttribute('data-start')) || 13;
		var end = parseFloat(root.getAttribute('data-end')) || 24;
		var storageKey = 'wvp-mine-' + (root.getAttribute('data-year') || '');
		var favourites = loadSet(storageKey);
		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var jumpButton = root.querySelector('.wvp-jump');
		var hint = root.querySelector('.wvp-hint');
		var counter = root.querySelector('.wvp-count');
		var filterButtons = [].slice.call(root.querySelectorAll('.wvp-filter .wvp-chip'));
		var now = currentMoment();
		var activePanel = null;

		root.setAttribute('data-js', '');

		tabs.forEach(function (tab) {
			if (tab.getAttribute('data-date') === now.date) tab.classList.add('is-today');
		});

		function panelFor(tab) {
			return document.getElementById(tab.getAttribute('aria-controls'));
		}

		function isLive(panel) {
			return !!panel && panel.getAttribute('data-date') === now.date && now.hour >= start && now.hour < end;
		}

		function isHorizontal(panel) {
			var scroller = panel.querySelector('.wvp-scroller');
			return !!scroller && scroller.scrollWidth > scroller.clientWidth + 2;
		}

		function hourWidth(panel) {
			var canvas = panel.querySelector('.wvp-canvas');
			var label = panel.querySelector('.wvp-label');
			return (canvas.offsetWidth - label.offsetWidth) / (end - start);
		}

		// Hide the NOW line while it is scrolled underneath the sticky location labels.
		function tuckNowLine(panel) {
			var line = panel.querySelector('.wvp-now');
			if (!line || line.hidden) return;
			if (!isHorizontal(panel)) {
				line.classList.remove('is-tucked');
				return;
			}
			var scroller = panel.querySelector('.wvp-scroller');
			var labelWidth = panel.querySelector('.wvp-label').offsetWidth;
			var x = labelWidth + (now.hour - start) * hourWidth(panel) - scroller.scrollLeft;
			line.classList.toggle('is-tucked', x < labelWidth + 2);
		}

		function refreshNow() {
			now = currentMoment();
			panels.forEach(function (panel) {
				var line = panel.querySelector('.wvp-now');
				var live = isLive(panel);
				if (line) {
					if (live) {
						line.style.setProperty('--t', (now.hour - start).toFixed(4));
						line.querySelector('b').textContent = formatClock(now.hour);
						if (line.hidden) {
							line.hidden = false;
							if (!reduceMotion) {
								line.classList.add('is-landing');
								setTimeout(function () { line.classList.remove('is-landing'); }, 700);
							}
						}
					} else {
						line.hidden = true;
					}
				}
				[].forEach.call(panel.querySelectorAll('.wvp-slot'), function (slot) {
					var from = parseFloat(slot.getAttribute('data-from'));
					var to = parseFloat(slot.getAttribute('data-to'));
					slot.classList.toggle('is-now', live && now.hour >= from && now.hour < to);
				});
				if (panel === activePanel) tuckNowLine(panel);
			});
			if (jumpButton) jumpButton.hidden = !isLive(activePanel);
		}

		function scrollToFocus(panel, smooth) {
			var scroller = panel.querySelector('.wvp-scroller');
			if (!scroller) return;
			var behavior = smooth && !reduceMotion ? 'smooth' : 'auto';
			if (!isHorizontal(panel)) {
				// Wide layout: time runs downwards, so bring the NOW line into view.
				var line = panel.querySelector('.wvp-now');
				if (smooth && line && !line.hidden) line.scrollIntoView({ behavior: behavior, block: 'center' });
				return;
			}
			var target;
			if (isLive(panel)) {
				target = now.hour - start - 0.35;
			} else {
				var earliest = end;
				[].forEach.call(panel.querySelectorAll('.wvp-slot:not(.wvp-slot--daypart)'), function (slot) {
					earliest = Math.min(earliest, parseFloat(slot.getAttribute('data-from')));
				});
				target = (earliest < end ? earliest : start) - start;
			}
			var left = Math.max(0, target * hourWidth(panel));
			if (typeof scroller.scrollTo === 'function') {
				scroller.scrollTo({ left: left, behavior: behavior });
			} else {
				scroller.scrollLeft = left;
			}
		}

		function activate(tab, options) {
			options = options || {};
			tabs.forEach(function (t) {
				var selected = t === tab;
				t.setAttribute('aria-selected', selected ? 'true' : 'false');
				t.tabIndex = selected ? 0 : -1;
				var panel = panelFor(t);
				if (panel) panel.classList.toggle('is-active', selected);
			});
			activePanel = panelFor(tab);
			refreshNow();
			if (options.focus) tab.focus();
			scrollToFocus(activePanel, !!options.smooth);
			tuckNowLine(activePanel);
		}

		tabs.forEach(function (tab, i) {
			tab.addEventListener('click', function () { activate(tab, { smooth: true }); });
			tab.addEventListener('keydown', function (e) {
				var next = null;
				if (e.key === 'ArrowRight') next = tabs[(i + 1) % tabs.length];
				else if (e.key === 'ArrowLeft') next = tabs[(i - 1 + tabs.length) % tabs.length];
				else if (e.key === 'Home') next = tabs[0];
				else if (e.key === 'End') next = tabs[tabs.length - 1];
				if (next) {
					e.preventDefault();
					activate(next, { focus: true, smooth: true });
				}
			});
		});

		panels.forEach(function (panel) {
			var scroller = panel.querySelector('.wvp-scroller');
			if (scroller) scroller.addEventListener('scroll', function () { tuckNowLine(panel); }, { passive: true });
		});
		window.addEventListener('resize', function () { if (activePanel) tuckNowLine(activePanel); });

		// Stars
		function syncFavourites() {
			var count = 0;
			[].forEach.call(root.querySelectorAll('.wvp-slot[data-id]'), function (slot) {
				var on = favourites.has(slot.getAttribute('data-id'));
				slot.classList.toggle('is-fav', on);
				var star = slot.querySelector('.wvp-star');
				if (star) star.setAttribute('aria-pressed', on ? 'true' : 'false');
				if (on) count++;
			});
			if (counter) counter.textContent = String(count);
			return count;
		}

		root.addEventListener('click', function (e) {
			var star = e.target.closest && e.target.closest('.wvp-star');
			if (!star || !root.contains(star)) return;
			var slot = star.closest('.wvp-slot');
			var id = slot && slot.getAttribute('data-id');
			if (!id) return;
			if (favourites.has(id)) favourites.delete(id); else favourites.add(id);
			saveSet(storageKey, favourites);
			var count = syncFavourites();
			if (hint && root.getAttribute('data-filter') === 'mine') hint.hidden = count > 0;
		});

		filterButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				var mode = button.getAttribute('data-filter');
				filterButtons.forEach(function (b) { b.setAttribute('aria-pressed', b === button ? 'true' : 'false'); });
				root.setAttribute('data-filter', mode);
				if (hint) hint.hidden = !(mode === 'mine' && favourites.size === 0);
			});
		});

		if (jumpButton) {
			jumpButton.addEventListener('click', function () { scrollToFocus(activePanel, true); });
		}

		syncFavourites();

		// Start day: a #wvp-day-... link, otherwise today, otherwise the first day.
		var startTab = null;
		if (window.location.hash) {
			startTab = tabs.filter(function (t) { return '#' + t.getAttribute('aria-controls') === window.location.hash; })[0] || null;
		}
		if (!startTab) startTab = tabs.filter(function (t) { return t.classList.contains('is-today'); })[0] || tabs[0];
		activate(startTab);

		if (!now.simulated) setInterval(refreshNow, 30000);
	}

	function boot() {
		[].forEach.call(document.querySelectorAll('[data-wvp]'), init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
