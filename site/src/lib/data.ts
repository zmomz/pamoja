import { createClient } from '@sanity/client';
import type { SanityClient } from '@sanity/client';
import { branches, events, homePage, partners, services, siteSettings } from '../seed/index.ts';
import type { SeedBranch } from '../seed/branches';
import type { SeedEvent } from '../seed/events';
import type { SeedHomePage } from '../seed/homePage';
import type { SeedPartner, SeedPartnerType } from '../seed/partners';
import type { SeedService } from '../seed/services';
import type { SeedSiteSettings } from '../seed/siteSettings';

// ---------------------------------------------------------------------------
// Domain types (mirror the Sanity schemas, with slugs flattened and
// references resolved).
// ---------------------------------------------------------------------------

export interface PortableTextSpan {
  _type: string;
  _key: string;
  text: string;
  marks: string[];
}

export interface PortableTextBlock {
  _type: string;
  _key: string;
  style: string;
  markDefs: unknown[];
  children: PortableTextSpan[];
}

export type PortableText = PortableTextBlock[];

export interface Branch {
  _id: string;
  title: string;
  slug: string;
  description: string;
  order: number;
}

export type PartnerType = SeedPartnerType;

export interface ImageWithAlt {
  alt: string;
  url?: string;
}

export interface Partner {
  _id: string;
  name: string;
  slug: string;
  type: PartnerType;
  contribution: string;
  url?: string;
  logo?: ImageWithAlt;
}

export type EventStatus = 'past' | 'upcoming' | 'handed-on';

export interface GalleryImage extends ImageWithAlt {
  consentConfirmed: boolean;
}

export interface Event {
  _id: string;
  title: string;
  slug: string;
  dateOrRange: string;
  startDate: string;
  endDate?: string;
  location?: string;
  status: EventStatus;
  handedOnTo?: Partner;
  branchesServed: Branch[];
  body: PortableText;
  partnersCredited: Partner[];
  gallery: GalleryImage[];
  pullQuote?: string;
  featured: boolean;
}

export interface Service {
  _id: string;
  title: string;
  slug: string;
  summary: string;
  whatItIs: string;
  whatHappens: PortableText;
  whatToExpect: PortableText;
  whatWeNeedFromYou: string[];
  culturalProtocols: PortableText;
  whatThisIsNot: string[];
  timeAndLeadTime: string;
}

export interface HomePage {
  heroTagline: string;
  positioningStatement: PortableText;
  ground: string;
  roots: string;
  whoWeAre: string;
  ourWork: string;
  whatWeOffer: string;
  whoWeGrowWith: string;
  seeds: string;
}

export interface SocialLink {
  label: string;
  url: string;
}

export interface SiteSettings {
  contactEmail: string;
  socialLinks: SocialLink[];
  landAcknowledgment: PortableText;
  ogDefaultImage?: ImageWithAlt;
  plausibleDomain?: string;
}

// ---------------------------------------------------------------------------
// Sanity client (only when a real project is configured)
// ---------------------------------------------------------------------------

const env = (import.meta as { env?: Record<string, string | undefined> }).env ?? {};
const projectId = env.PUBLIC_SANITY_PROJECT_ID;
const dataset = env.PUBLIC_SANITY_DATASET || 'production';

/** True only when PUBLIC_SANITY_PROJECT_ID points at a real project. */
export const sanityConfigured = Boolean(
  projectId && projectId !== 'pamoja-placeholder'
);

let client: SanityClient | null = null;
if (sanityConfigured) {
  client = createClient({
    projectId: projectId as string,
    dataset,
    apiVersion: '2025-01-01',
    useCdn: true,
  });
}

// ---------------------------------------------------------------------------
// GROQ projections
// ---------------------------------------------------------------------------

const PARTNER_PROJECTION = `{
  _id, name, "slug": slug.current, type, contribution, url,
  "logo": logo { alt, "url": asset->url }
}`;

const EVENT_PROJECTION = `{
  _id, title, "slug": slug.current, dateOrRange, startDate, endDate, location,
  status, pullQuote, "featured": coalesce(featured, false),
  body,
  "handedOnTo": handedOnTo->${PARTNER_PROJECTION},
  "branchesServed": branchesServed[]->{_id, title, "slug": slug.current, description, order},
  "partnersCredited": partnersCredited[]->${PARTNER_PROJECTION},
  "gallery": gallery[] { alt, consentConfirmed, "url": asset->url }
}`;

const SERVICE_PROJECTION = `{
  _id, title, "slug": slug.current, summary, whatItIs, whatHappens,
  whatToExpect, whatWeNeedFromYou, culturalProtocols, whatThisIsNot,
  timeAndLeadTime
}`;

// ---------------------------------------------------------------------------
// Seed fallback resolution (references -> full objects)
// ---------------------------------------------------------------------------

const seedBranchById = new Map<string, Branch>(
  branches.map((b) => [b._id, toBranch(b)])
);
const seedPartnerById = new Map<string, Partner>(
  partners.map((p) => [p._id, toPartner(p)])
);

function toBranch(doc: SeedBranch): Branch {
  return {
    _id: doc._id,
    title: doc.title,
    slug: doc.slug.current,
    description: doc.description,
    order: doc.order,
  };
}

function toPartner(doc: SeedPartner): Partner {
  return {
    _id: doc._id,
    name: doc.name,
    slug: doc.slug.current,
    type: doc.type,
    contribution: doc.contribution,
    url: doc.url,
  };
}

function resolveBranchRefs(refs: { _ref: string }[]): Branch[] {
  return refs
    .map((r) => seedBranchById.get(r._ref))
    .filter((b): b is Branch => Boolean(b));
}

function resolvePartnerRefs(refs: { _ref: string }[] = []): Partner[] {
  return refs
    .map((r) => seedPartnerById.get(r._ref))
    .filter((p): p is Partner => Boolean(p));
}

function toEvent(doc: SeedEvent): Event {
  return {
    _id: doc._id,
    title: doc.title,
    slug: doc.slug.current,
    dateOrRange: doc.dateOrRange,
    startDate: doc.startDate,
    endDate: doc.endDate,
    location: doc.location,
    status: doc.status,
    handedOnTo: doc.handedOnTo
      ? seedPartnerById.get(doc.handedOnTo._ref)
      : undefined,
    branchesServed: resolveBranchRefs(doc.branchesServed),
    body: doc.body,
    partnersCredited: resolvePartnerRefs(doc.partnersCredited),
    gallery: [],
    pullQuote: doc.pullQuote,
    featured: doc.featured,
  };
}

function toService(doc: SeedService): Service {
  return {
    _id: doc._id,
    title: doc.title,
    slug: doc.slug.current,
    summary: doc.summary,
    whatItIs: doc.whatItIs,
    whatHappens: doc.whatHappens,
    whatToExpect: doc.whatToExpect,
    whatWeNeedFromYou: doc.whatWeNeedFromYou,
    culturalProtocols: doc.culturalProtocols,
    whatThisIsNot: doc.whatThisIsNot,
    timeAndLeadTime: doc.timeAndLeadTime,
  };
}

function toHomePage(doc: SeedHomePage): HomePage {
  const { _id, _type, ...rest } = doc;
  return rest;
}

function toSiteSettings(doc: SeedSiteSettings): SiteSettings {
  return {
    contactEmail: doc.contactEmail,
    socialLinks: doc.socialLinks,
    landAcknowledgment: doc.landAcknowledgment,
    plausibleDomain: doc.plausibleDomain,
  };
}

// ---------------------------------------------------------------------------
// Getters — Sanity when configured, seed modules otherwise
// ---------------------------------------------------------------------------

export async function getBranches(): Promise<Branch[]> {
  if (!client) return [...branches.map(toBranch)].sort((a, b) => a.order - b.order);
  return client.fetch(
    `*[_type == "branch"]{_id, title, "slug": slug.current, description, order} | order(order asc)`
  );
}

export async function getPartners(): Promise<Partner[]> {
  if (!client) return partners.map(toPartner);
  return client.fetch(`*[_type == "partner"]${PARTNER_PROJECTION}`);
}

export async function getEvents(): Promise<Event[]> {
  if (!client) return sortEvents(events.map(toEvent));
  const result = await client.fetch<Event[]>(`*[_type == "event"]${EVENT_PROJECTION}`);
  return sortEvents(result);
}

export async function getEventBySlug(slug: string): Promise<Event | undefined> {
  if (!client) return events.map(toEvent).find((e) => e.slug === slug);
  return client.fetch<Event | null>(
    `*[_type == "event" && slug.current == $slug][0]${EVENT_PROJECTION}`,
    { slug }
  ).then((e) => e ?? undefined);
}

export async function getServices(): Promise<Service[]> {
  if (!client) return services.map(toService);
  return client.fetch(`*[_type == "service"]${SERVICE_PROJECTION}`);
}

export async function getServiceBySlug(slug: string): Promise<Service | undefined> {
  if (!client) return services.map(toService).find((s) => s.slug === slug);
  return client.fetch<Service | null>(
    `*[_type == "service" && slug.current == $slug][0]${SERVICE_PROJECTION}`,
    { slug }
  ).then((s) => s ?? undefined);
}

export async function getHomePage(): Promise<HomePage> {
  if (!client) return toHomePage(homePage);
  return client.fetch(`*[_type == "homePage"][0]{
    heroTagline, positioningStatement, ground, roots, whoWeAre, ourWork,
    whatWeOffer, whoWeGrowWith, seeds
  }`);
}

export async function getSiteSettings(): Promise<SiteSettings> {
  const settings: SiteSettings = !client
    ? toSiteSettings(siteSettings)
    : await client.fetch(`*[_type == "siteSettings"][0]{
    contactEmail, socialLinks, landAcknowledgment, plausibleDomain,
    "ogDefaultImage": ogDefaultImage { alt, "url": asset->url }
  }`);
  // The contact inbox is deploy configuration: PUBLIC_CONTACT_EMAIL wins
  // over both the CMS value and the seed fallback.
  const contactEmail = env.PUBLIC_CONTACT_EMAIL ?? settings.contactEmail;
  if (contactEmail.endsWith('.example')) {
    console.warn(
      '[pamoja] contactEmail is still the placeholder — set PUBLIC_CONTACT_EMAIL to the real inbox before launch.'
    );
  }
  return { ...settings, contactEmail };
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/** Events that served the given branch (the Our Work filter). */
export function eventsForBranch(allEvents: Event[], branchSlug: string): Event[] {
  return allEvents.filter((event) =>
    event.branchesServed.some((branch) => branch.slug === branchSlug)
  );
}

/** Most recent first by startDate (the brief's default view). */
export function sortEvents(allEvents: Event[]): Event[] {
  return [...allEvents].sort((a, b) => b.startDate.localeCompare(a.startDate));
}
