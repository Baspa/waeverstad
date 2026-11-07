---
version: 1
slug: "waeverstad-schedule"
primary_target: "waeverstad-schedule"
related_targets: []
---

# Programma (timetable), waeverstadfeest.nl/programma/

## Scope
- Surface: the programme shortcode `[waeverstad_schedule]` rendered by the `waeverstad-schedule` plugin (code in English, visitor copy in Dutch) inside the Divi page `/programma/`.
- Visitor mode: **Operate**. Success means the visitor knows what is on now, what comes next, and where, on a phone in the crowd. Planning ahead on desktop is secondary.
- Content: the Waevertent and the outdoor stage on a time axis, the four horeca venues by part of the day (middag/avond), and the day themes from the old overview poster.
- Constraints: the incumbent site style is binding (PRODUCT.md). The page renders server-side and stays readable without JS. JS adds day switching, the NOW line, and stars. No Divi dependency. All copy in Dutch.

## Direction contract
THESIS: One swipeable timetable. Every location is a row on a single time axis: the Waevertent poster's grid, made live and readable on a phone. It refuses the three stacked poster images, and it refuses a long list per venue.

OWN-WORLD: The incumbent site, untouched.
- Ground: brand green #09a552 with the woven-ribbon-and-confetti background.
- Dark green #124a2f carries the ruler and the location tabs.
- Slots alternate yellow #fbce06 and light yellow #ffec96; horeca part-of-day blocks are light green #a6eca3.
- Red #e6342a for fills; #d42e24 wherever small white type sits on red.
- Intro Rust ("WAEV") in uppercase for tabs, slots and labels; Poppins for the supporting lines.
- Stickers and banners tilted −1° to −5° with soft drop shadows, 4–5px corners, white display type with a 0 .1em .1em shadow.

STORY: The visitor opens on today with its theme and sees every location in one stack of rows, with the NU line showing where they are in the day. They swipe through the day, tap another day, and star acts to build "Mijn programma". They leave knowing what is on now, what is next, and where.

FIRST VIEWPORT, phone at 390:
- Compact title with a date pill, then six day tabs (the active one red and tilted), then the yellow day-theme banner.
- The timetable: a dark-green time ruler; sticky dark-green location tabs (Buiten podium, Waevertent, a HORECA divider, then Gij&Ik, Buskens, De Keizer, Dientje); slots at 190px per hour; horeca blocks spanning middag or avond.
- A white NU line with a red, tilted "NU 16:20" sticker.
- Bottom chips: "Hele dag" and "Mijn programma" with a count.
- Desktop (timeline container ≥ 1000px): the axis turns. Time runs down like the Waevertent poster, locations become columns, and the whole day 13:00–00:00 fits with normal page scrolling and no horizontal scroll. Short slots drop their times, as on the poster. The toolbar moves above the timeline.

FORM: Festival timetable: locations as rows, time running across. Position 5 of 7 on the ordered list, dealt lead. seed:fbcb793a061299f3.
- Signature move: the NU sticker rides the now-line, the timetable opens scrolled to now, and special moments hang as red flags above their slot.
- Raise from the gate board: one state marker, reserved for NOW.
- Raise from the catalogue: starred acts are kept on the phone as "Mijn programma".

FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance

## Unresolved
- Where the data lives long-term: a JSON file in the plugin for now; a WP-admin editor later.
- The mascot illustration as a clean cut-out (transparent PNG) is not in hand. Ask the association for it rather than cropping it from the posters.
- Links from programme items to the Tickets page: not decided.
