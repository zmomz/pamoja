# Pamoja — design direction

## Where the palette comes from

Every colour is taken from the physical world of this work, not from a template:

| Token | Value | Source |
|---|---|---|
| `--soil-deep` | #2e2419 | Wet topsoil — the ground layer of the tree illustration |
| `--soil` | #4a3826 | Park earth in July (Churchill Park, John Rebecca Park) |
| `--clay` | #6b5233 | Dried clay footpaths |
| `--coffee` | #3b2a1e | Roasted coffee bean — the coffee ritual |
| `--tea` | #a5743c | Black tea in a glass — the biweekly tea gathering |
| `--coffee-cherry` | #8c3b2e | The fruit around the bean — the single restrained accent |
| `--bark` | #54402c | Tree trunk |
| `--leaf-deep` / `--leaf` / `--leaf-light` / `--moss` | #3d5231 / #5f7a45 / #93a86f / #77875c | Canopy in layered light |
| `--paper` | #f2ede1 | Parchment field-notes paper — deliberately warmer and darker than stock "cream" |
| `--ink` | #24291f | Deep green-black ink |

The brief (§7) explicitly rules out the cream background + high-contrast serif + terracotta
accent combination that appears on every third non-profit site. The old prototype used exactly
that (`--cream: #f4efe4`, Fraunces everywhere, `--orange: #b66e42`); this palette replaces it.

## Typography

- **Display:** Bricolage Grotesque — a characterful face used sparingly (headings, the hero
  line "Pamoja means together"). Full Latin extended range covers the diacritics in Ta'aruf,
  Takaful, and Kiswahili terms.
- **Body:** Source Serif 4 — comfortable for the long reading on Who We Are and The Ground We
  Share. Falls back to Noto Naskh Arabic for any Arabic text (§9.2: Arabic appears only where
  it carries meaning, set in a proper Arabic typeface, never as ornament).
- **UI:** Karla — buttons, labels, form fields, the tree indicator.

Fonts are self-hosted via `@fontsource/*` — no third-party font CDN at runtime (privacy, §10).

## Rules baked in from the brief

- **§9.1:** No Haudenosaunee visual motifs. The Dish With One Spoon and Two Row Wampum are
  represented through words and abstract forms of our own (two parallel paths; one shared
  vessel) — never wampum imagery, beadwork, or purple-and-white patterns.
- **§2:** The soil layer is drawn as shared neutral ground; the roots are distinctly Pamoja's.
  The Charter sits in the soil with the same visual weight as the covenants.
- **Contrast (WCAG 2.1 AA):** token values are unchanged from this table, but usage is
  constrained — `--tea` (#a5743c) is 3.5:1 on `--paper`, so it is display-size text and
  illustration only. Small text (tags, counters, badges, captions) uses `--soil`
  (9.5:1 on paper) or darker; on `--soil-deep`, small text uses `--leaf-light` (5.8:1)
  or `--paper`.
- **Motion:** one orchestrated moment — the descent into the ground and the growth back up.
  Everything respects `prefers-reduced-motion` and works with JavaScript disabled.
