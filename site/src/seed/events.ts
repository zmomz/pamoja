import { pt, ref, slug } from './helpers.ts';
import type { SeedBlock, SeedRef, SeedSlug } from './helpers.ts';

export type SeedEventStatus = 'past' | 'upcoming' | 'handed-on';

export interface SeedEvent {
  _id: string;
  _type: 'event';
  title: string;
  slug: SeedSlug;
  dateOrRange: string;
  startDate: string;
  endDate?: string;
  location?: string;
  status: SeedEventStatus;
  handedOnTo?: SeedRef;
  branchesServed: SeedRef[];
  body: SeedBlock[];
  partnersCredited: SeedRef[];
  gallery: unknown[];
  pullQuote?: string;
  featured: boolean;
}

// The launch inventory from brief §5.5. Every entry is written with the
// community as the actor — who hosted, who taught, what knowledge was shared.
// Galleries are deliberately empty: no photograph is published without
// confirmed consent (brief §9.3).
export const events: SeedEvent[] = [
  {
    _id: 'event-biweekly-tea-gathering',
    _type: 'event',
    title: 'Pamoja Biweekly Tea Gathering',
    slug: slug('biweekly-tea-gathering'),
    dateOrRange: '2023–2026',
    startDate: '2023-01-01',
    endDate: '2026-06-30',
    location: 'Community Permaculture Lab front yard, Hamilton',
    status: 'handed-on',
    handedOnTo: ref('partner-community-permaculture-lab'),
    branchesServed: [ref('branch-connection'), ref('branch-social-cohesion')],
    body: pt(
      'For three years, newcomer families of African Muslim origin hosted a biweekly tea gathering in the front yard of the Community Permaculture Lab — roughly forty-five gatherings in all. Tea was poured the Sudanese way, stories travelled across languages, and neighbours who arrived as strangers left as collaborators.',
      'The gathering taught Hamilton what Pamoja means in practice. Knowledge holders led; guests learned to be guests. Intercultural and intergenerational connection happened because the community made it happen, week after week, in a yard that became common ground.',
      'This work has been handed on: the tea gathering is now fully owned and run by the Community Permaculture Lab. Pamoja claims no ownership of it — a practice rooted deeply enough to thrive without us is the point of the work.'
    ),
    partnersCredited: [ref('partner-community-permaculture-lab')],
    gallery: [],
    pullQuote:
      'A practice rooted deeply enough to thrive without us is the point of the work.',
    featured: true,
  },
  {
    _id: 'event-two-row-citizens-churchill-park',
    _type: 'event',
    title: 'Two Row Citizens at Churchill Park',
    slug: slug('two-row-citizens-churchill-park'),
    dateOrRange: '2024–2025',
    startDate: '2024-06-01',
    endDate: '2025-09-30',
    location: 'Churchill Park, Hamilton',
    status: 'past',
    branchesServed: [
      ref('branch-connection'),
      ref('branch-creative-collaboration'),
      ref('branch-social-cohesion'),
    ],
    body: pt(
      'Newcomer knowledge holders and Julia Hitchcock, steward of The Apothecary\u2019s Garden, co-created Two Row Citizens: gatherings in Churchill Park where the Two Row Wampum\u2019s ethic — two paths travelled side by side, neither steering the other\u2019s vessel — met the Sudanese hospitality traditions at Pamoja\u2019s roots.',
      'Across two seasons, participants practised what citizenship looks like when nobody is asked to erase themselves. Garden teachings and ancestral ceremony shared one ground; artists, families, and passers-by became co-creators rather than audience.',
      'TD Park People\u2019s grant support made both seasons possible.'
    ),
    partnersCredited: [ref('partner-apothecarys-garden'), ref('partner-td-park-people')],
    gallery: [],
    featured: true,
  },
  {
    _id: 'event-coffee-ritual-place-conscious-dialogue',
    _type: 'event',
    title: 'Coffee Ritual & Place-Conscious Dialogue',
    slug: slug('coffee-ritual-place-conscious-dialogue'),
    dateOrRange: 'September 2025',
    startDate: '2025-09-01',
    location: 'John Rebecca Park, Hamilton',
    status: 'past',
    branchesServed: [
      ref('branch-connection'),
      ref('branch-social-cohesion'),
      ref('branch-coordination'),
    ],
    body: pt(
      'Newcomer families hosted an ancestral coffee ritual in John Rebecca Park — roasting, grinding, and pouring as their grandmothers taught them — and invited downtown Hamilton to sit down with them.',
      'The ceremony set the terms of the afternoon: guests slowed to the pace of the roast, and a place-conscious dialogue unfolded about what this park is, who it is for, and what the city becomes when the people usually framed as newcomers do the hosting.',
      'The City of Hamilton\u2019s Downtown Placemaking Grant made the gathering possible.'
    ),
    partnersCredited: [ref('partner-city-of-hamilton')],
    gallery: [],
    pullQuote: 'The people usually framed as newcomers did the hosting.',
    featured: false,
  },
  {
    _id: 'event-two-row-citizens-series-2026',
    _type: 'event',
    title: 'Two Row Citizens series',
    slug: slug('two-row-citizens-series-2026'),
    dateOrRange: '2026 – February 2027',
    startDate: '2026-06-01',
    endDate: '2027-02-28',
    location: 'Hamilton',
    status: 'upcoming',
    branchesServed: [
      ref('branch-connection'),
      ref('branch-creative-collaboration'),
      ref('branch-coordination'),
    ],
    body: pt(
      'Two Row Citizens returns as a three-event series running through February 2027, expanding what began at Churchill Park. Newcomer knowledge holders again co-lead with The Apothecary\u2019s Garden, carrying the Two Row ethic of parallel paths into new neighbourhoods.',
      'Each gathering pairs ceremony with practice — art, storytelling, and shared food — coordinated with the partners and community efforts already rooted in each place.',
      'The series is made possible by a City of Hamilton grant.'
    ),
    partnersCredited: [ref('partner-city-of-hamilton'), ref('partner-apothecarys-garden')],
    gallery: [],
    featured: true,
  },
  {
    _id: 'event-kandakas-feminism',
    _type: 'event',
    title: 'Kandakas\u2019 Feminism exhibition',
    slug: slug('kandakas-feminism'),
    dateOrRange: 'October 2026',
    startDate: '2026-10-01',
    location: 'Hamilton',
    status: 'upcoming',
    branchesServed: [ref('branch-creative-collaboration'), ref('branch-connection')],
    body: pt(
      'Newcomer women lead Kandakas\u2019 Feminism: an exhibition of art, storytelling, music, and coffee ceremony that carries the name of the Kandakas — the women who led in ancient Sudan — into present-day Hamilton.',
      'The artists and knowledge holders are the curators of their own work; McMaster CCENA\u2019s support stands behind their leadership.',
      'Note: inclusion and framing of this exhibition await Abdo\u2019s confirmation (brief §12). Details here are provisional.'
    ),
    partnersCredited: [ref('partner-mcmaster-ccena')],
    gallery: [],
    featured: false,
  },
];
