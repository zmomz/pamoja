# Pamoja Cultural Collective — WordPress site

The Pamoja website. **The tree is the map of the site**, at three sizes: the home page
hero (click a part, arrive at that place), the menu (the whole tree beside the four
lists; hover a place and its part lights up), and the small "you are here" mark in the
header of every page. Underneath, every page is one scroll: Home, About, Events, Blog,
Engage, plus a page per event, post and album. Nothing goes more than two levels deep.

Two pieces, both required:

| Folder | What it is |
|---|---|
| `wp-content/themes/pamoja` | The theme: templates, the design system CSS, the site script, self-hosted fonts, brand assets, the tree, and the Site copy / Site settings screens. |
| `wp-content/plugins/pamoja-content` | The content plugin: Events, Media albums, Services, Partners, the Branch taxonomy, the "Blog" labelling of posts, the photo-consent rule, the inquiry inbox (the conversation form and "tell me when it's announced"), and the launch-content importer. |

Content types live in the plugin, not the theme, so nothing is lost if the theme is ever
switched or redesigned.

## Requirements

- WordPress 6.4 or newer, PHP 8.1 or newer (tested on 7.1 / PHP 8.5).
- No other plugins are required. No page builder, no ACF, no form service.
- Outbound mail from the host (or an SMTP plugin) so form submissions reach the inbox.
  Every submission is also stored under **Pamoja → Inquiries**, so nothing is lost if
  mail is misconfigured.

## Install on a host

1. Install WordPress as usual.
2. Copy `wp-content/plugins/pamoja-content` and `wp-content/themes/pamoja` into the
   site's `wp-content`.
3. **Plugins → activate "Pamoja Content".**
4. **Appearance → Themes → activate "Pamoja".**
5. **Pamoja → Import launch content → Import now.** This creates the four branches, the
   partners and funders, the three services, the six launch events with their
   photographs, the Home / About / Events / Blog / Engage / Thank-you pages, and sets the
   homepage, the blog and the permalinks. Safe to run again later: it updates rather
   than duplicates, and never touches anything you added yourself.
6. **Pamoja → Site settings:** the contact email (forms go there), social links, the
   land acknowledgment once the wording is confirmed, and optionally a Plausible domain.
7. **Settings → General:** confirm the site title, tagline and timezone.

Or, with WP-CLI: `wp plugin activate pamoja-content && wp theme activate pamoja && wp pamoja seed`.

## Run it locally (Docker)

```bash
cd wordpress
docker compose up -d
# then open http://localhost:8080, finish the install wizard,
# activate the plugin + theme, run Pamoja → Import launch content.
```

## The map

| Door | Part of the tree | Page | What is on it |
|---|---|---|---|
| About | soil · trunk · branches · roots | `/about/` | Why we exist, Our story, How we work together, Ethics and values — four stops on one scroll; the tree beside the text lights the stop being read |
| Events | fruit (the canopy) | `/events/` | Upcoming (each one is a fruit on the tree), ongoing, past; every event has its own page |
| Blog | leaves (the canopy) | `/blog/` | Posts, newest first |
| Engage | seeds | `/engage/` | Volunteer, Partner (the protocol, the offerings, who we grow with), Support, and the conversation form |

Other URLs: `/events/<slug>/`, `/blog/<slug>/`, `/media/` and `/media/<slug>/` (albums),
`/branch/<slug>/`, `/thank-you/`.

## What editors control, and where

| Where | What |
|---|---|
| **Blog** (the built-in Posts, relabelled) | Stories from the work. |
| **Pamoja → Events** | Date as displayed, start/end dates, location, status (upcoming / ongoing / past / handed on), video link, branches, partners credited, pull quote, **Featured** (the home page tiles, in their Order), photo gallery. |
| **Pamoja → Media albums** | Photo sets and/or a video, optionally linked to an event. |
| **Pamoja → Services** | The offerings shown under Engage → Partner. |
| **Pamoja → Partners & funders** | Name, type, contribution text, website, optional logo. Shown under Engage → Partner and credited on event pages. |
| **Pamoja → Branches** (under Events) | The four functions; name, description, order. |
| **Pamoja → Site copy** | Every passage on the home, About and Engage pages, tab by tab. Stop and section titles also name the places on the tree and in the menu. Empty a field to return to the launch wording. |
| **Pamoja → Site settings** | Contact email, description, footer lines, social links, land acknowledgment, share image, analytics. |
| **Pamoja → Inquiries** | Every conversation-form submission and every "tell me when it's announced" request. |

See `CMS-GUIDE.md` for the editor's guide, including the photo-consent rule.

## The photo-consent rule

Every image in the Media Library has a **Consent confirmed** checkbox. A photo appears
on the public site — in a gallery, as a tile, as a share image — **only** when that box
is ticked *and* the image has alt text. The five photographs supplied for the website
were imported with consent confirmed and the source recorded on each one; untick the
box to withdraw any of them.

## Design notes

The theme's `assets/css/pamoja.css` is the design system — the palette, the type
scale and the rules behind them. (It grew out of `site/DESIGN.md` in the earlier
Astro build, which was removed on 19 September 2026; `git log -- site/DESIGN.md`
has it if the reasoning is ever wanted.) The tree is one SVG (`template-parts/tree/tree.php`) rendered per
instance by `pamoja_tree()` with its ids suffixed; parts are dimmed by `.lit-*` classes;
crops (the canopy for Events and Blog, the seeds for Engage) are a different viewBox.
Page transitions use the browser's cross-document view transitions, so the tree morphs
into the page's slice where supported and simply loads elsewhere.

Fonts are self-hosted. No third-party requests are made at runtime unless a Plausible
domain is set. The site works with JavaScript disabled: the header links go to their
pages, the footer carries the whole map, and the forms post normally.

## Ownership checklist

- [ ] WordPress hosting account in Pamoja's name; developer added as a user.
- [ ] Domain registered in Pamoja's name; DNS pointed at the host.
- [ ] Administrator account for Pamoja; developer's account can be removed later.
- [ ] Contact email set under Pamoja → Site settings and a test inquiry received.
- [ ] Automatic updates on for WordPress core and the site backed up by the host.
