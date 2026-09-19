# Pamoja Cultural Collective — website

The site lives in **`wordpress/`** — theme + content plugin, with News, Events
(with photo galleries), Media albums, editable homepage copy, the photo-consent rule
and an inquiry inbox. Start with `wordpress/README.md` (install/deploy) and
`wordpress/CMS-GUIDE.md` (for editors).

An earlier static build (Astro + Sanity) used to sit in `site/` and was served as a
preview on ports 8080 and 8123. It was removed on 19 September 2026, along with its
`pamoja-static@.service` systemd unit; `git log -- site/` still has all of it,
`site/DESIGN.md` included.

`Logo-and-colors/` holds the brand assets; `prototype.html` / `pamoja-prototype.html`
are the original prototypes.
