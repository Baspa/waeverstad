# Waeverstad

Website work for **Waeverstad Feest!**, the carnival celebration on and around 't Ridderplein in Gemert ([waeverstadfeest.nl](https://waeverstadfeest.nl)).

The first piece is a new programme page. The old page showed the programme as three poster images, which are unreadable on a phone. This replaces them with a timeline that works one-handed in the crowd and still looks like the rest of the site.

**Live concept:** https://baspa.github.io/waeverstad/ (2026 sample programme; use the links in the top bar to simulate a moment during carnival)

## What's in here

| Path | What |
|---|---|
| `waeverstad-schedule/` | WordPress plugin with the `[waeverstad_schedule]` shortcode |
| `waeverstad-schedule/data/schedule.json` | The programme. This is the file you edit each year |
| `demo/build.php` | Builds the public concept page into `docs/` using the same renderer |
| `docs/` | GitHub Pages output (do not edit by hand; rebuild instead) |
| `reference/2026/` | The old posters and logo, kept as source material |
| `PRODUCT.md` | Product context: who uses this, constraints, principles |
| `.impeccable/` | Design decision records (surface brief, decision sketches) |

## How the timeline works

- **Phone:** time runs horizontally and every location is a row. You swipe through the day; location labels stay pinned on the left. On a carnival day it opens on the current time, with a red **NU** sticker on the time line.
- **Desktop** (timeline at least 1000px wide): the axis turns and time runs downwards, like the old Waevertent poster. The whole day fits without horizontal scrolling.
- **Horeca** only has *middag*/*avond* (afternoon/evening), not exact times. Those entries span the daypart instead of pretending to have a start time. Several entries in the same daypart get their own lane.
- **Stars** save acts to "Mijn programma", which shows the starred acts of every day as one list. Tapping an act jumps back to it on the timeline. Stars are stored in the visitor's browser (localStorage), with no account and nothing sent anywhere.
- **The NOW line** runs behind the programme blocks, so it never strikes through an act's name; the act that is on now gets a white block with a red outline.
- **Without JavaScript** every day renders stacked underneath each other, so the content is always there.

The HTML is rendered server-side in PHP (`includes/class-renderer.php`); `schedule.js` only adds day switching, the NOW line and stars.

## Installing on the WordPress site

1. Zip the plugin folder: `zip -r waeverstad-schedule.zip waeverstad-schedule`
2. In WordPress: *Plugins → Add New → Upload Plugin*, choose the zip, activate.
3. Edit the `/programma/` page in Divi, add a **Code** (or Text) module and put in:
   ```
   [waeverstad_schedule]
   ```
   Optional: `[waeverstad_schedule title=""]` hides the plugin's own "Programma" heading, if the Divi hero already shows one.
4. Recommended page tweaks in Divi:
   - Remove the three poster images.
   - Make the hero on this page short (or remove it), so the timeline is visible without scrolling on a phone.
   - On phones, give the row containing the shortcode a width of 100% with ~16px padding. On touch screens the timeline already runs edge to edge.
   - If the site header is fixed on desktop, set its height so sticky labels don't hide behind it:
     ```css
     .wvp { --wvp-sticky-top: 80px; }
     ```

The plugin uses the theme's own fonts: `WAEV` (Intro Rust, already loaded by the theme) for display text and Poppins for body text. It doesn't load any fonts itself.

## Updating the programme

Edit `waeverstad-schedule/data/schedule.json` and upload the plugin again (or replace just that file over SFTP).

```jsonc
{
  "year": 2027,
  "sample": false,                 // true shows a "Voorbeeldprogramma" tag
  "note": "Optional line shown under the timeline.",
  "dayparts": {
    "afternoon": {"label": "Middag", "from": "13:00", "to": "19:00"},
    "evening":   {"label": "Avond",  "from": "19:00", "to": "24:00"}
  },
  "locations": [
    {"id": "waevertent", "name": "Waevertent", "kind": "stage"},
    {"id": "buiten", "name": "Buiten podium", "kind": "stage", "where": "op ’t Waeverplein"},
    {"id": "gijik", "name": "Gij&Ik", "kind": "bar"}
  ],
  "days": [
    {
      "date": "2027-02-04",
      "themes": [{"title": "Vùrspoelen", "with": "Optional subtitle", "label": "18+"}],
      "items": [
        // stages: exact times
        {"location": "waevertent", "session": "Carnaval XXL", "from": "19:00", "to": "20:30", "title": "Act name", "with": "Optional line"},
        // special moment (red flag)
        {"location": "waevertent", "from": "15:15", "to": "15:30", "title": "Eerste vat aanslaan", "highlight": true},
        // bars: afternoon | evening | allday
        {"location": "gijik", "daypart": "evening", "title": "Apres ski party", "with": "DJ MCHL"}
      ]
    }
  ]
}
```

- `kind: "stage"` locations get a time axis; `kind: "bar"` locations sit under the HORECA divider and use dayparts.
- Use `"24:00"` for midnight.
- A different file location can be set with the `waeverstad_schedule_data_path` filter.
- A visitor's stars are stored per `year`, so a new year starts with a clean "Mijn programma".

## Building the concept page

```sh
php demo/build.php   # writes docs/index.html and docs/assets/
```

Open `docs/index.html` locally, or push: GitHub Pages serves `docs/` from `main`. Add `?now=2026-02-14T16:20` to any URL to pretend it's that moment.

The demo uses **Lilita One** (Google Fonts) as a stand-in, because Intro Rust is a commercial font and isn't included in this public repo.

## Open points

- The 2027 programme isn't known yet. The 2026 data is a sample; times in the Waevertent were read off the poster.
- A clean cut-out of the mascot illustration would be nice for the page, but we need to ask the association for the original file.
- Linking programme items to the Tickets page hasn't been decided.
- If non-technical volunteers need to edit the programme later, a WP-admin editor can replace the JSON file.
- Ideas for later, from the design review: a cue for what's up next when the live act ends; the page title as the tilted ribbon banner from the posters; a sharper version of the woven background for phones.
