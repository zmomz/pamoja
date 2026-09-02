import { slug } from './helpers.ts';
import type { SeedSlug } from './helpers.ts';

export type SeedPartnerType = 'partner' | 'ally' | 'funder' | 'settlement-sector';

export interface SeedPartner {
  _id: string;
  _type: 'partner';
  name: string;
  slug: SeedSlug;
  type: SeedPartnerType;
  contribution: string;
  url?: string;
}

// Named with what they actually contributed — no logo walls (brief §5.7).
export const partners: SeedPartner[] = [
  {
    _id: 'partner-community-permaculture-lab',
    _type: 'partner',
    name: 'Community Permaculture Lab',
    slug: slug('community-permaculture-lab'),
    type: 'partner',
    contribution:
      'Gave space and roots. CPL\u2019s front yard hosted Pamoja\u2019s biweekly tea gathering from its first session, and in 2026 the gathering was handed on: CPL now owns and runs it entirely — a handover Pamoja counts as the work working.',
  },
  {
    _id: 'partner-apothecarys-garden',
    _type: 'partner',
    name: 'The Apothecary\u2019s Garden',
    slug: slug('the-apothecarys-garden'),
    type: 'partner',
    contribution:
      'Co-created Two Row Citizens at Churchill Park. Julia Hitchcock, steward of the garden, brought its teachings and her own practice into shared ground with newcomer knowledge holders.',
  },
  {
    _id: 'partner-td-park-people',
    _type: 'partner',
    name: 'TD Park People',
    slug: slug('td-park-people'),
    type: 'funder',
    contribution:
      'Grant support for Two Row Citizens at Churchill Park in 2024 and 2025.',
  },
  {
    _id: 'partner-city-of-hamilton',
    _type: 'partner',
    name: 'City of Hamilton',
    slug: slug('city-of-hamilton'),
    type: 'funder',
    contribution:
      'Downtown Placemaking Grant for the Coffee Ritual & Place-Conscious Dialogue at John Rebecca Park (September 2025), and a City grant for the 2026–27 Two Row Citizens series.',
  },
  {
    _id: 'partner-mcmaster-ccena',
    _type: 'partner',
    name: 'McMaster CCENA',
    slug: slug('mcmaster-ccena'),
    type: 'partner',
    contribution:
      'Supported the Kandakas\u2019 Feminism exhibition. Inclusion and framing await Abdo\u2019s confirmation.',
  },
];
