# Pamoja Cultural Collective — website

The public website for Pamoja Cultural Collective, a grassroots collective led by
newcomer Canadians of African Muslim origin, building community on ethical common
ground in Hamilton, Ontario.

## Stack

- **Astro 5**, fully static output (`output: 'static'`) — one scrolling homepage
  (all content lives in anchored sections of `/`), plus `/thank-you` and `/404`.
  No client framework.
- **Sanity 3** — content backend, with the Studio embedded at `/studio`
  (`@sanity/astro` integration, `studioBasePath: '/studio'`).
- **Netlify** — hosting + Netlify Forms for the inquiry form (no third-party form service).
- Fonts self-hosted via `@fontsource/*` (no font CDN at runtime).
- Analytics: Plausible only, loaded when `PUBLIC_PLAUSIBLE_DOMAIN` (or the Site Settings
  field) is set. No Google Analytics, no trackers, no cookie banner.

## Local development

Requires **Node 22**.

```bash
npm install
npm run dev       # local dev server (site + studio at /studio)
npm run build     # static build to dist/
npm run preview   # serve the production build locally
```

## Environment variables

Copy `.env.example` to `.env`. All are optional for local dev — see "seed fallback" below.

| Variable | Purpose |
|---|---|
| `PUBLIC_SANITY_PROJECT_ID` | Sanity project id. Empty/`pamoja-placeholder` = seed mode. |
| `PUBLIC_SANITY_DATASET` | Defaults to `production`. |
| `PUBLIC_SITE_URL` | Canonical origin for sitemap, canonical links, OG tags. |
| `SANITY_WRITE_TOKEN` | Only for `npm run import-seed`. Write-scope token. **Never commit.** |
| `PUBLIC_PLAUSIBLE_DOMAIN` | Optional; enables the Plausible script tag. |

## How the seed fallback works

`src/lib/data.ts` is the single data layer. If `PUBLIC_SANITY_PROJECT_ID` is unset or
still `pamoja-placeholder`, every getter returns content from `src/seed/*.ts` instead of
hitting the Sanity API — **the site builds and runs fully without any Sanity project**.
Once a real project id is set, the same getters fetch from Sanity via GROQ. Pages never
talk to Sanity directly.

Consequence: copy edits before the CMS is populated happen in `src/seed/*`; after import,
they happen in the Studio.

## Populating a fresh Sanity project

1. Create a Sanity project (under the Pamoja-owned account — see checklist below) with a
   `production` dataset.
2. Put `PUBLIC_SANITY_PROJECT_ID` and `PUBLIC_SANITY_DATASET` in `.env`.
3. Create an API token with **Editor/Write** access (Sanity project settings → API) and
   set it as `SANITY_WRITE_TOKEN`.
4. Run `npm run import-seed`. It creates-or-replaces every seed document with stable ids,
   so it is safe to re-run.

## Deployment (Netlify)

`netlify.toml` is already configured: build command `npm run build`, publish `dist`,
Node 22, and an SPA fallback redirect for `/studio/*`.

1. Connect the repository to a Netlify site **owned by Pamoja**.
2. Set the environment variables above in Netlify → Site configuration → Environment
   variables (at minimum `PUBLIC_SANITY_PROJECT_ID`, `PUBLIC_SANITY_DATASET`,
   `PUBLIC_SITE_URL` once the domain is known).
3. Deploy. The inquiry form (`src/components/InquiryForm.astro`) is picked up by Netlify
   Forms automatically; submissions can route to email under Netlify → Forms →
   notifications. No external form service is involved.

## Account-ownership checklist (all under Pamoja-owned accounts, not the developer's)

- [ ] **Sanity** organization + project — Pamoja's account; developer added as a member.
- [ ] **Netlify** site — Pamoja's team; developer added as a collaborator.
- [ ] **Domain** — registered in Pamoja's name (brief §12: still an open decision).
- [ ] **Plausible** — Pamoja's account; set `PUBLIC_PLAUSIBLE_DOMAIN` to enable.

## Repository map

- `src/pages/` — `index.astro` (the whole site), `404.astro`, `thank-you.astro`.
- `src/layouts/Base.astro` — head (SEO/OG/canonical/Plausible), skip link, header, footer, journey rail, shared popover, and the reveal/scrollspy script.
- `src/components/` — header, footer, journey rail, popover, inquiry form; `src/components/sections/` holds the homepage sections in scroll order (hero → contact), including the inline SVG scenes.
- `src/lib/` — data layer (Sanity + seed fallback) and portable-text renderer.
- `src/seed/` — launch content, also the payload for `npm run import-seed`.
- `src/sanity/schemas/` — Sanity schema; `sanity.config.ts` mounts the Studio at `/studio`.
- `scripts/import-seed.mjs` — seed importer (idempotent).
- `DESIGN.md` — palette/type sources and the cultural-protocol rules baked into the design.
- `CMS-GUIDE.md` — editor's guide for Abdo. `PHASE2.md` — phase-2 directory notes.
