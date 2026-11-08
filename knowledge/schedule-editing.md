# Editing the schedule

How the programme gets onto the site today, what data the timeline needs, and the options for letting the association edit it themselves.

_Status: open decision. Last updated 2026-11-08._

## How it is delivered today

- The association publishes the programme as **poster images** (PNG). The 2026 set is in `reference/2026/`: an overview, the Waevertent and the horeca.
- It is **not known** what the posters are made from (Excel, Google Sheets, Canva, a designer working in InDesign, …). Ask the association before choosing an option below.
- For the concept, the 2026 programme was typed over from the posters into `waeverstad-schedule/data/schedule.json`. Waevertent times were read off the poster.
- Right now only someone comfortable editing JSON can change the programme. In practice that means the site maintainer.

## What the timeline needs

The data format is documented in the README ("Updating the programme"). In plain terms:

**Per act**

| Field | Required | Notes |
|---|---|---|
| Day | yes | A date within the carnival days |
| Location | yes | One of the known locations (Waevertent, Buiten podium, Gij&Ik, Buskens, De Keizer, Dientje) |
| Start and end time | stages | `HH:MM`, with `24:00` for midnight |
| Daypart | horeca | Afternoon, evening or all day. Horeca has no exact times, and we never invent them |
| Name | yes | The act or event |
| "With" line | no | e.g. "Samen met de Gimmertse hoogheden" |
| Label | no | e.g. `18+` |
| Highlight | no | Yes or no. Marks a special moment (red flag), e.g. "Eerste vat aanslaan" |

**Per day:** the theme title(s), with an optional subtitle and label.

**Fixed per year:** the list of locations and which ones are stages and which are horeca. The daypart times (afternoon 13:00–19:00, evening 19:00–24:00) are also fixed per year.

This fits naturally in a spreadsheet: one tab for acts and one tab for day themes.

## Options

| | Option | Effort | Who can edit | Risk |
|---|---|---|---|---|
| 1 | They send it in any format, the maintainer updates the JSON | None | Maintainer only | Every change waits for one person, including during carnival |
| 2 | **Spreadsheet template + CSV upload in WP-admin** | Medium | Anyone with WP access | Low: validated and previewed before it goes live |
| 3 | Linked Google Sheet, read by the site directly | Medium | Anyone with access to the sheet | No check before going live. Depends on Google, and needs caching and a fallback |
| 4 | Edit screen inside WordPress (forms per day and act) | High | Anyone with WP access | More code to maintain. Clumsy for long lists of acts |

### Recommendation: option 2

Volunteers already know spreadsheets, and if the programme is drafted in Excel or Sheets today, almost nothing changes for them. The upload step is the safety net:

1. Download the template (Excel / Google Sheets) and fill it in.
2. In WP-admin, open the plugin's page and upload the sheet as CSV.
3. The plugin checks every row and reports problems in plain Dutch:
   - "Rij 14: eindtijd ligt vóór begintijd"
   - "Locatie 'Waeverplein' bestaat niet"
   - "Gij&Ik heeft geen tijden, alleen middag/avond"
4. A preview shows the timeline as it will look. Publish only when it is right.
5. The previous version is kept, so a bad upload can be rolled back.

Checks to build in:
- The date falls on a carnival day.
- The location exists.
- Stages have start and end times, and horeca has a daypart.
- The end time is after the start time.
- Times are valid `HH:MM`.
- Overlapping acts on the same stage get a warning.
- Highlights are only allowed on stages.

Option 3 is attractive for last-minute changes during carnival. It could be added later as a second source, but not as the only source.

Whether a field plugin such as ACF is installed is unknown, because the front-end page doesn't show it. Check in WP-admin before considering option 4.

## Things to keep in mind

- **Stars depend on the act's identity.** A visitor's "Mijn programma" is stored per act, keyed on date, location, start time (or daypart) and name. Renaming an act or moving its start time during carnival removes it from visitors' stars. Fixing a typo before the programme is published is fine; changing names afterwards should be rare.
- **Stars are stored per year** (`year` in the data). A new year starts with an empty "Mijn programma".
- **No invented times for horeca.** If the association does have exact horeca times one year, the format would need to change. Don't put made-up times in the daypart fields.

## Questions for the association

1. What do you make the programme in today, before it becomes a poster?
2. Who puts it together, and who should be able to change it on the site?
3. How often does it change after it is published, and do changes happen during carnival itself?
4. Do the horeca venues ever have exact times, or always afternoon/evening?
5. When will the 2027 programme be ready? (2027 carnival: 4–9 February.)
