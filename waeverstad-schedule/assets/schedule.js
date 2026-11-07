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
		var minePanel = root.querySelector('.wvp-mine');
		var mineList = root.querySelector('.wvp-mine-list');
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

		// Hide the NOW line and ruler labels while they are scrolled underneath the sticky
		// location labels, so nothing shows up torn in half next to them.
		function tuckNowLine(panel) {
			var line = panel.querySelector('.wvp-now');
			var ticks = [].slice.call(panel.querySelectorAll('.wvp-tick'));
			if (!isHorizontal(panel)) {
				if (line) line.classList.remove('is-tucked');
				ticks.forEach(function (tick) { tick.classList.remove('is-tucked'); });
				return;
			}
			var scroller = panel.querySelector('.wvp-scroller');
			var labelWidth = panel.querySelector('.wvp-label').offsetWidth;
			var edge = scroller.getBoundingClientRect().left + labelWidth + 2;
			ticks.forEach(function (tick) {
				tick.classList.toggle('is-tucked', tick.getBoundingClientRect().left < edge);
			});
			if (line && !line.hidden) {
				var x = labelWidth + (now.hour - start) * hourWidth(panel) - scroller.scrollLeft;
				line.classList.toggle('is-tucked', x < labelWidth + 2);
			}
		}

		var tuckQueued = false;
		function queueTuck(panel) {
			if (tuckQueued) return;
			tuckQueued = true;
			window.requestAnimationFrame(function () {
				tuckQueued = false;
				tuckNowLine(panel);
			});
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
			if (jumpButton) jumpButton.hidden = !isLive(activePanel) || root.getAttribute('data-filter') === 'mine';
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
			if (!options.noScroll) scrollToFocus(activePanel, !!options.smooth);
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
			if (scroller) scroller.addEventListener('scroll', function () { queueTuck(panel); }, { passive: true });
		});
		window.addEventListener('resize', function () { if (activePanel) queueTuck(activePanel); });

		// Stars: the timeline slots and the rows in the "Mijn programma" list share data-id.
		function syncFavourites() {
			var count = 0;
			[].forEach.call(root.querySelectorAll('[data-id]'), function (item) {
				var on = favourites.has(item.getAttribute('data-id'));
				var isSlot = item.classList.contains('wvp-slot');
				item.classList.toggle(isSlot ? 'is-fav' : 'is-off', isSlot ? on : !on);
				var star = item.querySelector('.wvp-star');
				if (star) star.setAttribute('aria-pressed', on ? 'true' : 'false');
				if (isSlot && on) count++;
			});
			if (counter) counter.textContent = String(count);
			return count;
		}

		root.addEventListener('click', function (e) {
			var star = e.target.closest && e.target.closest('.wvp-star');
			if (star && root.contains(star)) {
				var holder = star.closest('[data-id]');
				var id = holder && holder.getAttribute('data-id');
				if (!id) return;
				if (favourites.has(id)) favourites.delete(id); else favourites.add(id);
				saveSet(storageKey, favourites);
				syncFavourites();
				return;
			}
			var go = e.target.closest && e.target.closest('.wvp-mine-go');
			if (go && root.contains(go)) goToSlot(go.parentNode.getAttribute('data-id'));
		});

		// "Mijn programma": the starred acts of every day as one list, so nothing hides off-screen.
		function element(tag, className, text) {
			var node = document.createElement(tag);
			if (className) node.className = className;
			if (text) node.textContent = text;
			return node;
		}

		function buildGroup(panel, slots, ended) {
			var tab = tabs.filter(function (t) { return panelFor(t) === panel; })[0];
			var dayName = tab ? tab.querySelector('.wvp-sr').textContent : panel.getAttribute('data-date');
			if (tab && tab.classList.contains('is-today')) dayName += ' · vandaag';
			var group = element('div', 'wvp-mine-day');
			group.appendChild(element('h4', 'wvp-mine-date', dayName));
			var list = element('ol');

			slots.forEach(function (slot) {
				var item = element('li', 'wvp-mine-item');
				item.setAttribute('data-id', slot.getAttribute('data-id'));
				if (!ended && slot.classList.contains('is-now')) {
					item.classList.add('is-now');
					item.appendChild(element('span', 'wvp-mine-now', 'Nu'));
				}

				var go = element('button', 'wvp-mine-go');
				go.type = 'button';
				var time = element('span', 'wvp-mine-time');
				var part = slot.querySelector('.wvp-part');
				var clock = slot.querySelector('.wvp-time');
				if (part) {
					time.textContent = part.textContent;
				} else if (clock) {
					var end = clock.querySelector('.wvp-time-end');
					time.textContent = clock.firstChild.textContent;
					if (end) time.appendChild(element('small', '', 'tot ' + end.textContent.replace(/^[–-]/, '')));
				}
				go.appendChild(time);

				var name = slot.querySelector('.wvp-name').cloneNode(true);
				name.className = 'wvp-mine-name';
				go.appendChild(name);

				var meta = element('span', 'wvp-mine-meta');
				var label = slot.closest('.wvp-row').querySelector('.wvp-label');
				meta.appendChild(element('b', '', label.firstChild.textContent));
				var withLine = slot.querySelector('.wvp-with');
				if (withLine) meta.appendChild(document.createTextNode(' · ' + withLine.textContent));
				go.appendChild(meta);
				item.appendChild(go);

				var star = slot.querySelector('.wvp-star');
				if (star) item.appendChild(star.cloneNode(true));
				list.appendChild(item);
			});

			group.appendChild(list);
			return group;
		}

		// During carnival the list starts with what is on now or next; whatever has
		// ended folds away under "Al geweest". Outside carnival it is plain day order.
		function buildMineList() {
			mineList.textContent = '';
			var duringCarnival = tabs.some(function (t) { return t.classList.contains('is-today'); });
			var coming = [];
			var past = [];
			var total = 0;
			var pastCount = 0;

			panels.forEach(function (panel) {
				var slots = [].slice.call(panel.querySelectorAll('.wvp-slot[data-id]')).filter(function (slot) {
					return favourites.has(slot.getAttribute('data-id'));
				});
				if (!slots.length) return;
				slots.sort(function (a, b) { return parseFloat(a.getAttribute('data-from')) - parseFloat(b.getAttribute('data-from')); });
				total += slots.length;

				var date = panel.getAttribute('data-date');
				var hasEnded = function (slot) {
					if (!duringCarnival) return false;
					if (date < now.date) return true;
					return date === now.date && parseFloat(slot.getAttribute('data-to')) <= now.hour;
				};
				var ended = slots.filter(hasEnded);
				var open = slots.filter(function (slot) { return !hasEnded(slot); });
				if (open.length) coming.push(buildGroup(panel, open, false));
				if (ended.length) {
					past.push(buildGroup(panel, ended, true));
					pastCount += ended.length;
				}
			});

			coming.forEach(function (group) { mineList.appendChild(group); });
			if (past.length) {
				var fold = element('details', 'wvp-mine-past');
				fold.appendChild(element('summary', '', 'Al geweest (' + pastCount + ')'));
				past.forEach(function (group) { fold.appendChild(group); });
				mineList.appendChild(fold);
			}
			minePanel.classList.toggle('is-empty', total === 0);
		}

		function setFilter(mode) {
			filterButtons.forEach(function (b) { b.setAttribute('aria-pressed', b.getAttribute('data-filter') === mode ? 'true' : 'false'); });
			root.setAttribute('data-filter', mode);
			if (minePanel) {
				if (mode === 'mine') buildMineList();
				minePanel.hidden = mode !== 'mine';
			}
			refreshNow();
			if (mode !== 'mine' && activePanel) {
				scrollToFocus(activePanel, false);
				tuckNowLine(activePanel);
			}
		}

		// From the list back to the timeline, with the act in view.
		function goToSlot(id) {
			var slot = [].filter.call(root.querySelectorAll('.wvp-slot[data-id]'), function (s) { return s.getAttribute('data-id') === id; })[0];
			if (!slot) return;
			var panel = slot.closest('.wvp-day');
			var tab = tabs.filter(function (t) { return panelFor(t) === panel; })[0];
			filterButtons.forEach(function (b) { b.setAttribute('aria-pressed', b.getAttribute('data-filter') === 'all' ? 'true' : 'false'); });
			root.setAttribute('data-filter', 'all');
			if (minePanel) minePanel.hidden = true;
			if (tab) activate(tab, { noScroll: true });

			if (isHorizontal(panel)) {
				var from = parseFloat(slot.getAttribute('data-from')) - start;
				panel.querySelector('.wvp-scroller').scrollLeft = Math.max(0, (from - (slot.classList.contains('wvp-slot--daypart') ? 0 : 0.25)) * hourWidth(panel));
				tuckNowLine(panel);
			}
			var rect = slot.getBoundingClientRect();
			var offset = rect.height > window.innerHeight * 0.6 ? 96 : (window.innerHeight - rect.height) / 2;
			window.scrollTo({ top: window.pageYOffset + rect.top - offset, behavior: reduceMotion ? 'auto' : 'smooth' });

			slot.classList.remove('is-found');
			void slot.offsetWidth;
			slot.classList.add('is-found');
			setTimeout(function () { slot.classList.remove('is-found'); }, 1900);
			var star = slot.querySelector('.wvp-star');
			if (star) star.focus({ preventScroll: true });
		}

		filterButtons.forEach(function (button) {
			button.addEventListener('click', function () { setFilter(button.getAttribute('data-filter')); });
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
