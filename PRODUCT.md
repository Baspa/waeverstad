# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

A small custom WordPress plugin that renders the programme through a shortcode, placed on the existing `/programma/` page via Divi. Chosen by the maintainer (Bas) over a pasted Divi Code module because the plugin works independently of the theme and keeps working year after year.

- Host site: waeverstadfeest.nl, WordPress 7.1.4 on the **Divi** theme (Elegant Themes) with the Divi Pixel and Wow Carousel for Divi plugins. Not Beaver Builder, which is what the maintainer first assumed.
- The plugin must not depend on Divi internals. It has to work inside any Divi section or row, and outside Divi too.
- Repository: `/var/www/waeverstad` locally (`/var/www` points to `~/Sites`), mirrored to GitHub as `Baspa/waeverstad`.

## Users

- **Visitors to Waeverstad Feest!** check what is on, when, and where across the carnival days, on both mobile and desktop (confirmed by the maintainer). *Inference, not yet confirmed:* the heaviest use is on a phone during the carnival itself ("what's on now or next, and where?"), with planning ahead at home as a secondary use.
- **The maintainer (Bas, volunteer)** enters the programme each year. He is comfortable editing data or code. This is the arrangement "for now"; a non-technical committee member may take over later.

## Product Purpose

Waeverstad Feest! is a carnival celebration run by a carnival association on and around 't Ridderplein in Gemert (Waeverstad during carnival). The programme page gathers everything that happens across the carnival days in one place: the Waevertent, the outdoor stage or square, and the participating bars and cafés.

Success means a visitor can find, on any device, what is happening at a given moment and location, without zooming into poster images. It also means the maintainer can swap in a new year's programme without rebuilding the page.

## Positioning

One festival spread over several venues in the centre of Gemert: the association's own tent, an outdoor stage on the square, and four independent horeca venues, each with its own programme. No single venue's site shows the whole picture; this page is the only place that does.

## Operating Context

- **Carnival week runs Thursday to Tuesday.** In 2026 that was Thursday 12 to Tuesday 17 February. The homepage already announces 2027 as Thursday 4 to Tuesday 9 February 2027.
- **The 2026 programme came in three parts**, each published only as a poster PNG:
  1. **Waevertent**: a timed schedule per day, roughly 13:00 to 00:00. It shows acts and DJs in time slots, special moments (Eerste vat aanslaan, Onthulling, Prijsuitreiking RW, Eindshow buiten), labelled blocks (Buiten podium on Saturday and Sunday, Rondje Waeverstad on Monday, Carnaval XXL on Thursday evening), and back-to-back slots alternating between a resident DJ and live acts.
  2. **Horeca programma**: four venues (Gij&Ik, Buskens, De Keizer, Dientje). For each day there is a *middag* (afternoon) and *avond* (evening) slot, each with an event title and the performers. Some entries span both parts of the day. Some venues have nothing on certain days.
  3. **Totaaloverzicht**: one headline theme or event per day.
     - Thursday: Vùrspoelen x Carnaval XXL
     - Friday: Nietwerkbal, Crazy Carnaval Cantus (18+)
     - Saturday: Klossen los! outside on 't Waeverplein, Waeverstad zingt with the CCC-band, the unveiling of the Waever
     - Sunday: Snipperdag XXL with the Groot Gemertse optocht (parade)
     - Monday: Rondje Waeverstad (theme: rood-wit-groen), Uit je heilige losbol
     - Tuesday: Nadruppelen with the closing show
- **Site navigation:** Info, Foto's '26, Programma, Tickets, Doneren. The site also has an Instagram feed, an aftermovie and a sponsor section.
- **Maintenance:** the programme changes completely every year, and the page goes live in the weeks before carnival.

## Capabilities and Constraints

- The programme data is replaced each year. Data stays separate from presentation so that a new year means new data, not new code.
- All content is in Dutch, including Gemert dialect words. Day and venue names follow the incumbent wording.
- Age markers (18+) and the NIX18 notice are part of the existing programme material and must stay.
- **Open:** where the data lives (a file in the plugin, or an editor in WP-admin). For now the maintainer edits it himself. A WP-admin editor for non-technical users is a possible later step, not a current requirement.
- **Open:** whether tickets or prices are tied to specific events. The site has a separate Tickets page; nothing is linked yet.
- **Open:** the 2027 programme is not known yet. The dates are known: 4 to 9 February 2027 (per the homepage).

## Brand Commitments

- **Binding (from the maintainer): the new page must match the existing site's style.** It is a better-working version inside the current identity, not a new visual identity. The current look needs to be documented from the live site before any visual work.
- Name as used: **Waeverstad Feest!** (with exclamation mark on the posters; "Waeverstadfeest" in the domain and page titles). Tagline on the site: "Samen carnavallen in heul Waeverstad!"
- Voice: festive, local and informal Brabant/Gemert dialect ("Wittút nog?!", "heul", "Vùrspoelen", "Gimmertse hoogheden").
- Existing assets:
  - the logo (`reference/2026/Logo-PNG.png`)
  - the site's display font, loaded as "WAEV" (Intro Rust, served from `wp-content/uploads/et-fonts/IntroRust-1.otf`)
  - the recurring mascot illustration used on the programme posters (a man in a cap, blue jacket and red scarf)

## Evidence on Hand

- `reference/2026/2026_Programma-Waevertent.png`: the full Waevertent schedule for 2026.
- `reference/2026/2026_Programma-horeca.png`: the full horeca programme for 2026.
- `reference/2026/Totaaloverzicht-WF2026.png`: the daily overview for 2026.
- `reference/2026/programma-pagina-snapshot.html` (local only, kept out of the repo because it contains WordPress nonces): the HTML of the current `/programma/` page, saved 2026-11-07. It shows the page as three stacked images with no text content.
- `reference/2026/Logo-PNG.png`: the logo.
- The 2026 programme is the working example content until the 2027 programme exists.
- **Absent, never to be invented:** the 2027 programme, start and end times for horeca entries (only *middag*/*avond* is known), ticket prices, venue addresses, and performer descriptions or links.

## Product Principles

1. **Readable in the crowd.** The programme is real text that works one-handed on a phone, not a poster you have to zoom into.
2. **One place for every location.** The tent, the outdoor stage and every café come together in a single overview, without hiding that each one is a separate venue.
3. **Swap the year, not the code.** A new carnival means new data. The structure holds whatever next year's programme looks like, including empty slots, slots spanning both parts of the day, and special moments.
4. **A good guest in the existing site.** The page fits Divi and the current identity and does not break the theme.
5. **Sounds like Waeverstad.** Copy keeps the association's own festive, dialect-rich voice.
