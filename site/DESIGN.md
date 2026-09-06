# Pamoja — design direction

## Where the palette comes from

The four brand swatches (`Logo-and-colors/colors-pallet.jpeg`) are the whole colour
system; the neutrals are derived from them. Tokens live in `src/styles/global.css`.

| Token | Value | Role |
|---|---|---|
| `--ochre` / `--ochre-light` | #D79031 / #E8B06A | Sun, seeds, soil rim, rules and glyphs. Display size and illustration only (2.5:1 on paper). |
| `--karkadeh` / `--karkadeh-deep` | #822B2D / #5E1F21 | Bark, roots, soil, the primary button, numbering. |
| `--river` / `--river-deep` / `--river-light` / `--river-pale` | #437261 / #33584A / #7FA693 / #AFCDBD | Canopy and leaves; links, eyebrows and card accents on light grounds; river-pale for eyebrows and links on dark grounds. |
| `--hibiscus` | #CC3300 | Micro-accent only: the period in "Pamoja.", focus rings, the current rail stop, the seeds mark. |
| `--paper` / `--cream` / `--white` | #FAF6EE / #F2EBDC / #FDFBF7 | Page, tinted bands, cards. |
| `--ink` / `--night` / `--ink-soft` / `--line` | #28211B / #211C17 / #5B5142 / #DFD4BD | Text, dark bands, secondary text, hairlines. |

Contrast (WCAG 2.1 AA): river on paper 5.1:1, river on cream 4.6:1, karkadeh on
paper 8.4:1, hibiscus on paper 4.8:1, river-pale on night 9.9:1. Ochre never carries
small text.

## Typography

- **Display:** Bricolage Grotesque — h1 to h4, chips, the positioning statement, the
  mobile menu. Section h2 caps at 4.5rem so only the hero word is larger. Display
  copy uses `text-wrap: balance`.
- **Body:** Source Serif 4 at 1.075rem / 1.65, measure 62ch. Falls back to Noto Naskh
  Arabic for Arabic text (§9.2: Arabic only where it carries meaning).
- **UI:** Karla — buttons, labels, form fields, the rail. Uppercase labels are at least
  0.72rem with 0.1–0.12em tracking, in two colours: river on light, river-pale on dark.
  Karkadeh is reserved for numbering.

Fonts are self-hosted via `@fontsource/*` — no third-party font CDN at runtime (§10).

## Layout grammar

- One scrolling page. Each stop is a `section.journey` with an `.art` column and a
  `.copy` column; on one column the copy comes first.
- Rows below the pair use `.block` (full width), `.block-split` (heading in the art
  column, body in the copy column — long prose never hugs the left edge with an
  empty half beside it) or `.block-end` (copy column only).
- Cards carry a 3px river top edge; list items carry a 4px ochre left edge. Radii:
  18px cards, 10px list items, 8px inputs, pills for chips and buttons.

## Illustration

- The hero tree is drawn the way the wordmark is drawn: flat swatch fills, cut
  highlights, a hand-drawn outline, no airbrush gradients and no drop shadow. Its
  parts carry the section numbers (01 Soil … 07 Seeds) as persistent labels, so the
  tree reads as the site's map on first sight and on touch.
- Every scene's ground fades out at its sides (a horizontal mask), like the hero
  mound. No scene shows a hard rectangular edge.

## Rules baked in from the brief

- **§9.1:** No Haudenosaunee visual motifs. The Dish With One Spoon and Two Row Wampum
  are represented through words and abstract forms of our own (two parallel paths; one
  shared vessel) — never wampum imagery, beadwork, or purple-and-white patterns.
- **§2:** The soil layer is drawn as shared neutral ground; the roots are distinctly
  Pamoja's. The Charter sits in the soil with the same visual weight as the covenants.
- **Motion:** one orchestrated moment — the descent out of the hero and the draw/grow
  entrance of each scene. Beyond that, each scene keeps at most one idle motion (the
  hero crown sways; the sun turns; root tips pulse; the grove's flows run; the sprout
  sways). Everything respects `prefers-reduced-motion` and works with JavaScript
  disabled.
