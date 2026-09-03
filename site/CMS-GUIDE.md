# CMS guide — editing the Pamoja site

For Abdo and whoever looks after content. No code needed for anything in this guide.

## Where the Studio is

- **After deploy:** `https://<your-domain>/studio` — log in with your Sanity account.
- **Locally:** `npm run dev`, then open `http://localhost:4321/studio`.

The Studio sidebar has: **Home Page** and **Site Settings** (single documents, one of
each), then **Events/Programs, Services, Branches, Partners**.

## Adding an event

**Content → Event / Program → +**. Every field, in one line:

- **Title** — the gathering's public name.
- **Slug** — click Generate; identifies the event internally. Events don't get
  their own pages — they appear as cards in the archive ("Things we've grown")
  on the homepage.
- **Date or date range (display)** — as it should read, e.g. "October 2026" or "2023–2026".
- **Start date** — real date; sorts the archive (most recent first).
- **End date** — optional, for ranges.
- **Location** — park, venue, neighbourhood; leave blank for just "Hamilton".
- **Status** — *Past* / *Upcoming* / *Handed on*. **Handed on** means the work now
  belongs to someone else (like the Tea Gathering, now CPL's) — the site says so plainly,
  neither claiming it nor deleting it. Choosing it reveals:
- **Handed on to** — the partner who now owns and runs it. Required when status is Handed on.
- **Branches served** — which of the four branches this gathering served; pick every one
  that genuinely applies (most events serve several — that overlap is the point). This
  drives the archive filter on the homepage.
- **Description** — 2–3 paragraphs. Write the community as the actor: who hosted, who
  taught, what knowledge was shared. See the copy rules below.
- **Partners and funders credited** — name everyone who co-created, hosted, or funded.
  Credit is shared publicly — that's a commitment, not a courtesy.
- **Image gallery** — photos from the gathering. Two things are enforced:
  - **Alt text is required** — describe the image for someone who can't see it. The
    reversal test applies here too: people hosting, pouring, teaching — not being served.
  - **Consent confirmed** — the checkbox that says consent is on file for every
    identifiable person. **Publishing is blocked until you tick it.** Never publish
    children's faces without explicit parental consent. When in doubt, leave the photo out.
- **Pull quote** — optional; one line that carries the gathering.
- **Featured** — optional flag for highlighting.

## Editing a service

**Content → Service** (Workshops, Hospitality rituals, Consultation). Services appear
as cards in the What We Offer section of the homepage; each card opens a popover that
compresses the sections below. Every service uses the same eight fixed fields, in this
order — the consistency is deliberate:

1. **Summary** — one sentence, used in cards and search previews.
2. **What it is** — a paragraph.
3. **What actually happens** — the shape of the session, honestly.
4. **What to expect** — include the discomfort; it's part of the offer.
5. **What we need from you** — bullet list: space, time, budget, participation.
6. **Cultural protocols** — how to engage respectfully with this specific practice.
   This is where your protocol wording goes — generous room is left for it.
7. **What this is not** — the short list that prevents wrong inquiries.
8. **Time and lead time** — how long, how far ahead to ask.

The popover ends with a built-in link to the contact section — no editing needed.

## Homepage content and site settings

- **Home Page** — the positioning statement (the dark band right after the hero).
  Any paragraph you start with `[` (a draft note) will **not** appear on the site —
  use that for works in progress. The per-tree-part passages and hero tagline stay
  in the CMS but are not shown while the homepage carries its own section copy.
- **Site Settings** — contact email (footer + contact section), social links, the **land
  acknowledgment** (appears in the footer of every page once real wording is entered),
  the default social-share image, and the Plausible analytics domain.

## The copy rules — five things to hold onto

These are protocol, not style (brief §1.1/§9.6). They apply to everything you write,
including alt text and headlines:

1. **Newcomers are the subject, never the object.** If a sentence still makes sense with
   newcomers as people being helped, rewrite it until they do the verb.
2. **Never write** "serving/helping/supporting newcomers", "beneficiaries", "clients"
   (for community members), "participants receiving", "newcomers in need of".
3. **Write instead:** building with · working alongside · hosting · convening · teaching ·
   contributors · knowledge holders · collaborators — newcomers **who bring** X.
4. **Cut "giving newcomers a voice" / "amplifying voices" entirely** — it presumes we
   granted something that was never ours to grant.
5. **The one exception:** on What We Offer, "the client" means the *institution* hiring
   us — that usage is correct. And when we describe settlement organizations' work
   ("they deliver services to people who need them"), that's describing them, not us.

When numbers appear, they describe the size of a gathering, never a caseload.
