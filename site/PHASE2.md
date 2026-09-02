# Phase 2 — newcomer women-led business directory

Notes for the phase-2 addition named in brief §10: a directory of newcomer women-led
businesses in Hamilton, particularly businesses founded by women from countries affected
by war and displacement. Not built yet — this is the approach.

## Why nothing in the current structure needs to change

The site already runs the exact pattern this feature needs:

- **Content type behind a CMS** — the directory is one more Sanity document type next to
  `event`, `service`, `partner`. Schemas live in `src/sanity/schemas/` and register in
  `src/sanity/schemas/index.ts`; the Studio picks them up with no other wiring.
- **Seed fallback** — add `src/seed/businesses.ts`, extend `src/lib/data.ts` with a
  `getBusinesses()` getter following the existing `getEvents()` shape, and the directory
  builds fully before any CMS entry exists.
- **Static render + client-side filter** — `/our-work` already does this: every card is
  server-rendered, `data-*` attributes carry the filter keys, a small inline script
  toggles visibility and syncs the URL (`?serves=connection`). The directory page copies
  that pattern verbatim and extends it with a **text search input** matching on name,
  trade, and neighborhood. No new infrastructure, no client framework, no rebuild.

## Proposed content type

`business` document, fields:

- **Name** (required) and **slug**.
- **Owner story** — short portable text, first person where possible; governed by the
  same consent discipline as event galleries (see below).
- **Trade / category tags** — a multi-select list (catering, tailoring, childcare,
  henna, bookkeeping…) so the page can offer both tag filters and text search.
- **Neighborhood** — plain string; filterable.
- **Contact / ordering info** — phone, email, Instagram, market stall — whatever the
  owner actually uses. Optional per channel.
- **Photo** — optional, with the **same `alt` + `consentConfirmed` pair** as event
  gallery images, including the validation rule that blocks publishing without confirmed
  consent.
- **Status / review date** — so listings can be re-confirmed with owners periodically
  rather than going stale silently.

## The page

`/directory` — server-rendered list of all businesses (cards, like `/our-work`), a text
search input plus tag filter chips, URL-synced (`/directory?trade=catering`), fully
usable without JavaScript (all cards visible, chips are plain links). Add it to
`src/lib/sections.ts`-style navigation only when it launches; the tree metaphor stays at
seven parts.

## Consent considerations — read before building

Listing women from countries affected by war and displacement carries real risk if done
carelessly. Baked into the schema and process:

- **Optional anonymity.** A listing must work without a face, without a full name, and
  without an identifiable story detail. First name + trade + neighborhood is a complete
  listing. The schema should make everything except business name and one contact
  channel optional.
- **No children's faces.** Same rule as the rest of the site (brief §9.3): never without
  explicit parental consent — simplest is to exclude children from listing photos
  entirely.
- **Consent is per-listing and revocable.** The `consentConfirmed` flag blocks
  publishing, but consent also needs an owner-initiated off-ramp: a standing "edit or
  remove my listing" contact on the page, and a named person at Pamoja who reviews each
  listing with the owner before it goes live and re-confirms periodically.
- **No scraping, no bulk export.** Consider `robots` meta `noindex` on individual
  listings if owners prefer not to appear in search engines — make it a per-listing
  choice, not a global one.
- **Copy register still applies.** Listings are written with owners as the actors —
  what they make, cook, sew, and run — never as people being helped into business.
