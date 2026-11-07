---
name: Waeverstad Feest! Programma
description: The carnival timetable for waeverstadfeest.nl, a live poster grid inside the incumbent Divi site style.
colors:
  brand-green: "#09a552"
  tent-green: "#124a2f"
  tent-green-veil: "rgba(18, 74, 47, 0.9)"
  carnival-red: "#e6342a"
  sticker-red: "#d42e24"
  festival-yellow: "#fbce06"
  pale-yellow: "#ffec96"
  horeca-green: "#a6eca3"
  white: "#ffffff"
typography:
  display:
    fontFamily: "'WAEV', 'Intro Rust', Poppins, Helvetica, Arial, sans-serif"
    fontSize: "clamp(2.4rem, 1.6rem + 3.4vw, 4.25rem)"
    fontWeight: 400
    lineHeight: 0.95
  headline:
    fontFamily: "'WAEV', 'Intro Rust', Poppins, Helvetica, Arial, sans-serif"
    fontSize: "clamp(1.4rem, 1rem + 1.6vw, 2.2rem)"
    fontWeight: 400
    lineHeight: 1.02
  title:
    fontFamily: "'WAEV', 'Intro Rust', Poppins, Helvetica, Arial, sans-serif"
    fontSize: "14px"
    fontWeight: 400
    lineHeight: 1.05
  label:
    fontFamily: "'WAEV', 'Intro Rust', Poppins, Helvetica, Arial, sans-serif"
    fontSize: "12px"
    fontWeight: 400
    lineHeight: 1.05
  body:
    fontFamily: "Poppins, Helvetica, Arial, sans-serif"
    fontSize: "15px"
    fontWeight: 400
    lineHeight: 1.4
  meta:
    fontFamily: "Poppins, Helvetica, Arial, sans-serif"
    fontSize: "12px"
    fontWeight: 600
    lineHeight: 1.2
    fontFeature: "tnum"
  daypart:
    fontFamily: "Poppins, Helvetica, Arial, sans-serif"
    fontSize: "11px"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "0.04em"
rounded:
  tag: "3px"
  pill: "4px"
  tab: "5px"
  chip: "50px"
spacing:
  gutter: "6px"
  sm: "8px"
  md: "14px"
  lg: "18px"
  xl: "22px"
components:
  day-tab:
    backgroundColor: "{colors.pale-yellow}"
    textColor: "{colors.tent-green}"
    typography: "{typography.label}"
    rounded: "{rounded.tab}"
    height: "52px"
  day-tab-active:
    backgroundColor: "{colors.sticker-red}"
    textColor: "{colors.white}"
    rounded: "{rounded.tab}"
  theme-banner:
    backgroundColor: "{colors.festival-yellow}"
    textColor: "{colors.tent-green}"
    typography: "{typography.headline}"
    padding: "8px 14px 10px"
  location-label:
    backgroundColor: "{colors.tent-green}"
    textColor: "{colors.white}"
    typography: "{typography.label}"
    rounded: "{rounded.tab}"
    width: "104px"
  slot:
    backgroundColor: "{colors.festival-yellow}"
    textColor: "{colors.tent-green}"
    typography: "{typography.title}"
    rounded: "{rounded.tab}"
    padding: "5px 8px"
    height: "60px"
  slot-alt:
    backgroundColor: "{colors.pale-yellow}"
    textColor: "{colors.tent-green}"
    rounded: "{rounded.tab}"
  slot-daypart:
    backgroundColor: "{colors.horeca-green}"
    textColor: "{colors.tent-green}"
    rounded: "{rounded.tab}"
  slot-live:
    backgroundColor: "{colors.white}"
    textColor: "{colors.tent-green}"
    rounded: "{rounded.tab}"
  slot-highlight:
    backgroundColor: "{colors.sticker-red}"
    textColor: "{colors.white}"
    rounded: "{rounded.tab}"
  highlight-flag:
    backgroundColor: "{colors.sticker-red}"
    textColor: "{colors.white}"
    rounded: "{rounded.tag}"
    padding: "3px 7px"
  now-sticker:
    backgroundColor: "{colors.sticker-red}"
    textColor: "{colors.white}"
    rounded: "{rounded.pill}"
    padding: "4px 9px 3px"
  chip:
    backgroundColor: "{colors.tent-green-veil}"
    textColor: "{colors.white}"
    rounded: "{rounded.chip}"
    padding: "0 14px"
    height: "44px"
  chip-active:
    backgroundColor: "{colors.white}"
    textColor: "{colors.tent-green}"
    rounded: "{rounded.chip}"
  chip-now:
    backgroundColor: "{colors.sticker-red}"
    textColor: "{colors.white}"
    rounded: "{rounded.chip}"
  info-pill:
    backgroundColor: "{colors.tent-green}"
    textColor: "{colors.pale-yellow}"
    rounded: "{rounded.pill}"
    padding: "4px 10px"
---

# Design System: Waeverstad Feest! Programma

## Overview

**Creative North Star: "The Waevertent Poster, Made Live"**

This system belongs to the incumbent waeverstadfeest.nl site, and it does not replace that site. The timetable sits on the site's brand-green ground with its woven-ribbon-and-confetti background. That ground comes from the host theme (Divi), and the plugin does not paint it. On top of the ground, the timetable sets the association's programme poster as real, scrollable text. Every location is a dark-green tab. Acts are yellow blocks on a single time axis. Moments that matter are red stickers, tilted as if slapped on by hand. The mood is festive, loud and local, but the grid is strict: one axis, one row or column per location, and one state marker for "now".

Density is high and deliberate. The whole day sits in one view: six locations on a phone, and 13:00 to 00:00 in one desktop column set without horizontal scroll. Legibility comes from block colour and uppercase display type, not from whitespace. Depth is physical: soft drop shadows lift tabs, slots and stickers off the green, and tilts between −1.2° and −5° say "sticker", never "card".

Everything is scoped under the `.wvp` root, and all tokens are custom properties on `.wvp` (`--wvp-green`, `--wvp-red-text`, `--wvp-display`, …). They are not on `:root`, so the theme cannot leak in and the plugin cannot leak out. Visitor copy is Dutch, in the association's dialect-rich voice. Code identifiers and comments are English.

**Key Characteristics:**
- Brand-green ground, dark-green structure, yellow content, red for state and specials.
- An uppercase display face for everything a visitor scans. Poppins for supporting lines.
- Tilted stickers with soft shadows. Tabs and blocks have 5px corners.
- One time axis that turns with the container: horizontal on phones, vertical at a container width of 1000px or more.
- One state marker, reserved for NOW.

## Colors

The palette is the incumbent site's carnival tricolour of green, red and yellow, used as solid block fills with no gradients.

### Primary
- **Brand Green** (`brand-green`): the page ground under the woven-ribbon background. The timetable never fills a block with it. It is the field everything else sits on, seen through the gaps between rows and columns.
- **Tent Green** (`tent-green`): the structure colour. It carries the time ruler, the location tabs, info pills, the "Vandaag" tag, the hint panel and body text on light fills. The translucent **Tent Green Veil** (`tent-green-veil`) is used for the ruler track, floating chips, the footer note and the "Al geweest" fold, where a little ground should show through.

### Secondary
- **Festival Yellow** (`festival-yellow`): the default act slot, the day-theme banner and the "Mijn programma" title banner. Also used for selection and the focus outline.
- **Pale Yellow** (`pale-yellow`): the alternate act slot (slots alternate yellow and pale yellow along a row), the inactive day tabs, and small text on tent green (date pill, location sublines).

### Tertiary
- **Sticker Red** (`sticker-red`): the text-safe red. White text on it measures 4.98:1. It is used for every red surface that carries small white type: the active day tab, the NU sticker, highlight slots and flags, age markers (18+), the count badge, the "Nu" jump chip and the starred-star fill. It is also the NIX18 text colour on white.
- **Carnival Red** (`carnival-red`): the site's own red. White text on it measures only 4.3:1, so it is a fill for large or non-text marks only. The timetable itself never uses it, and it stays in the palette for host-site elements such as the burger button.

### Neutral
- **Horeca Green** (`horeca-green`): part-of-day blocks for the four horeca venues. On desktop it also forms the translucent band and bracket at 16% and 85% alpha.
- **White** (`white`): display text on green and red, the live slot fill, the NU line, and the active chip.

### Named Rules
**The Text-Safe Red Rule.** Small white text sits only on Sticker Red or Tent Green, at 4.5:1 or better. Carnival Red never carries small white text.

**The One Marker Rule.** Red plus white means "now". The red tilted NU sticker, the white NU line and the white live slot with a red inset ring are the only state marker. No other state borrows that combination.

## Typography

**Display Font:** Intro Rust, loaded by the site as the family "WAEV", with a fallback to Poppins, Helvetica, Arial and sans-serif. The public demo cannot ship Intro Rust because it is a commercial font, so it overrides `--wvp-display` with Lilita One as a stand-in. Lilita One is a demo substitute only and does not belong in the system.
**Body Font:** Poppins (400 to 700), with Helvetica and Arial fallbacks.

**Character:** A chunky, rough poster face in uppercase does the shouting: titles, tabs, slot names, labels, chips and stickers. Poppins carries the quiet facts: times, performers, notes. The display face is always set at weight 400. Its weight comes from the face itself.

### Hierarchy
- **Display** (`display`): the page title "PROGRAMMA", white with a `0 0.1em 0.1em` dark text shadow.
- **Headline** (`headline`): the day-theme banner and the "Mijn programma" banner, balanced wrapping.
- **Title** (`title`): act names in slots, clamped to 2 lines. Short (half-hour) slots use 12px with 3 lines. On desktop the size is 15px with 3 lines, or 13px with 2 lines for short slots. List items in "Mijn programma" use 16px.
- **Label** (`label`): location tabs (12px on phones, 15px in columns), ruler ticks (14px, 11px for the half hours), chips (14px), daypart markers (12px).
- **Body** (`body`): base text on `.wvp`, theme subline (14px/600), notes and hints (13–14px).
- **Meta** (`meta`): slot times, set in tabular figures and never wrapped.
- **Daypart** (`daypart`): MIDDAG / AVOND inside horeca blocks. This is the only tracked uppercase Poppins in the timetable, and it is data, not a decoration.

### Named Rules
**The Whole Name Rule.** Names never auto-hyphenate (`hyphens: manual`). They wrap between words or clamp. "Waeverstad" is never split.

**The Shout/Speak Rule.** Uppercase display type is for what a visitor scans. Poppins sentence case is for what they read.

## Layout

The timetable is a single grid whose time axis turns with its container (container query `wvp` on the timeline).

- **Phones and narrow containers:** time runs horizontally at 190px per hour, and locations are rows. Each row is a 60px lane, and two-lane locations stack with a 6px gap. Location labels (104px wide) stick to the left edge, and slot text stays sticky beside them while the day scrolls by. A dark-green ruler with hour and half-hour ticks heads the scroll area, and faint white hour lines (32% alpha) run down through the rows. Rows that carry a highlight flag gain 30px of flag room above the lane, so the flag stays inside the row. The view opens scrolled to now. On coarse-pointer screens up to 980px the timeline breaks out to full viewport width.
- **Container at 1000px or wider:** the axis turns like the Waevertent poster. Time runs down at 92px per hour, with a 64px time rail on the left. Locations become columns behind 64px headers, two-lane columns grow proportionally, and the HORECA divider becomes a narrow vertical-text column. The whole day fits with normal page scrolling and no horizontal scroll. Short slots drop their times.
- **Viewport at 900px or wider:** day tabs show full day names, and the chip toolbar moves from a floating sticky bar at the bottom to a static row above the timeline.
- **Rhythm:** a 6px gutter between tabs, rows, lanes and list items, and 8px between toolbar chips. Larger steps of 12–22px separate header, tabs, banner and timeline. The root is capped at 1200px wide, and the "Mijn programma" list at 760px.
- **Without JS:** all days stack in order, 36px apart, and the tabs and toolbar are hidden.

## Elevation & Depth

The system has physical depth with soft, ambient drop shadows only. Surfaces read as paper on the green: shadows grow with tilt and importance, and live state lifts a slot higher. There are no hard offset shadows.

### Shadow Vocabulary
- **Resting block** (`box-shadow: 0 2px 5px rgba(0,0,0,0.16)`): slots and list items.
- **Resting tab** (`box-shadow: 0 2px 4px rgba(0,0,0,0.14)`): inactive day tabs.
- **Chip** (`box-shadow: 0 3px 8px rgba(0,0,0,0.22)`): floating toolbar chips.
- **Banner** (`box-shadow: 0 4px 10px rgba(0,0,0,0.2)`): tilted theme and "Mijn programma" banners.
- **Sticker** (`box-shadow: 0 3px 7px rgba(0,0,0,0.32)`, or `0 2px 5px rgba(0,0,0,0.28)` for flags): NU sticker, flags and small stickers.
- **Live / active lift** (`box-shadow: 0 4px 10px rgba(0,0,0,0.25)`, active tab `0 4px 9px rgba(0,0,0,0.3)`): the live slot and the selected day tab.

### Named Rules
**The Behind-The-Names Rule.** The NU line sits at z-index 1 and slots at 2 (highlights at 3, sticky labels at 6, toolbar at 8). The line shows between rows and on the ruler and never strikes through a name.

## Shapes

Corners are small and consistent: 5px for tabs, slots, location labels and panels, 4px for info pills and the NU sticker, and 3px for tags, flags and age markers. Chips and the count badge are full pills (50px). The star button is a circle. Tilt is the signature form: the active day tab at −3°, banners at −1.2°, flags at −2° (−3° on desktop), age markers at −4° and the NU sticker at −5°. Tilt always marks a sticker or a selection and is never applied to a plain slot. Empty stretches at the start of a row are dashed white outlines (2px, 75% alpha) on a faint green veil.

## Components

### Day Tabs
Pale-yellow tabs, one per carnival day, showing the abbreviated day (full day on wide screens) over a large date. The active tab turns Sticker Red with white type, tilts −3° and lifts. Today carries a small tent-green "Vandaag" tag hanging under the tab. Hover turns an inactive tab white.

### Day-Theme Banner
A festival-yellow strip tilted −1.2° with a soft shadow. It carries the day's theme as a headline and a Poppins 600 subline. The "Mijn programma" title reuses this banner.

### Location Labels
Tent-green tabs in uppercase display type, with an optional pale-yellow Poppins subline ("op ’t Waeverplein"). They are sticky on phones and become centred column headers on desktop.

### Slots (signature)
- **Act slot:** yellow, alternating with pale yellow, with 5px corners and the resting shadow. Time in Meta, name in Title, optional performers in 12px Poppins. A 28px star button sits top right.
- **Horeca daypart:** horeca green, showing MIDDAG / AVOND / MIDDAG + AVOND and never a clock time. On desktop the block becomes a translucent band (16% alpha) with a ticked bracket on its left edge reading "from–to", plus a compact horeca-green card that takes only the room its text needs.
- **Live:** a white fill with a 3px Sticker Red inset ring and the lift shadow. Live horeca keeps its green and gains the ring. A live highlight keeps its red and gains a 3px white outer ring.
- **Starred:** the star fills Sticker Red and tilts −8°. The slot gains a faint 2px red inset (55% alpha).
- **Highlight:** a Sticker Red slot with white type, carrying the time. A tilted red flag holds the name. On phones the flag sits in the row's flag room. On desktop it hangs above slots shorter than half an hour, and roomier slots take the name inside.

### NU Line and Sticker
A 3px white line with a faint green edge, marking the current time across all locations. It rides with a Sticker Red "NU 16:20" sticker in display type, tilted −5°, which lands with a short slap animation (0.6s, `cubic-bezier(0.16, 1, 0.3, 1)`). Reduced motion removes all transitions and animations.

### Chips (toolbar)
Pill buttons at 44px or taller, in uppercase display type on Tent Green Veil: "Hele dag" and "Mijn programma" with a red count badge. The pressed chip is white with tent-green text. The "Nu" jump chip is Sticker Red. Hover lifts a chip by 1px.

### Mijn Programma List
A list grouped per day. During carnival it starts with what is on now or next, and whatever has ended folds into an "Al geweest (n)" disclosure pill at 70% opacity. Items alternate yellow and pale yellow, with a time column (dayparts for horeca), the name in display type and the location in Poppins. A live item takes the live-slot treatment plus a small tilted red NU sticker. The star becomes a 44px target.

### Info Pills
Tent-green pills with pale-yellow Poppins 600 text for the date range. White pills with tent-green text mark the sample programme. The footer note uses the veil with white 13px text, and the NIX18 marker is Sticker Red display type on white.

## Do's and Don'ts

### Do:
- **Do** set every token as a custom property on `.wvp` and scope every selector under `.wvp`.
- **Do** put small white text only on Sticker Red or Tent Green, at 4.5:1 or better.
- **Do** keep the NU sticker, the white NU line and the white-with-red-ring live slot as the only state marker, with the line behind the slots.
- **Do** turn the axis on the timeline container (`wvp`, at 1000px or wider), not on the viewport.
- **Do** show horeca entries by part of the day (middag, avond, or both), with no clock times.
- **Do** tilt only stickers, flags, banners and the active tab, between −1.2° and −5°, with soft shadows.
- **Do** write visitor copy in Dutch and code in English.
- **Do** name Intro Rust ("WAEV") as the display face. Lilita One is a stand-in for the public demo only.

### Don't:
- **Don't** invent start or end times for horeca entries.
- **Don't** let automatic hyphenation split a name.
- **Don't** use Carnival Red behind small white text.
- **Don't** let the NU line cross over a slot or a name.
- **Don't** give any other state (starred, found, today) the red-sticker-plus-white-line combination.
- **Don't** link to or bundle font files. The host site supplies the display face.
