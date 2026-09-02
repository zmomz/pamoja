import { slug } from './helpers.ts';
import type { SeedSlug } from './helpers.ts';

export interface SeedBranch {
  _id: string;
  _type: 'branch';
  title: string;
  slug: SeedSlug;
  description: string;
  order: number;
}

// The four functions of Pamoja's work (brief §5.5).
export const branches: SeedBranch[] = [
  {
    _id: 'branch-connection',
    _type: 'branch',
    title: 'Connection',
    slug: slug('connection'),
    order: 1,
    description:
      'Intercultural, intergenerational, and cross-sector connection. Pamoja convenes people who would not otherwise meet — newcomer families, longtime neighbours, artists, and institutions — as contributors who each bring knowledge the others need.',
  },
  {
    _id: 'branch-social-cohesion',
    _type: 'branch',
    title: 'Social cohesion',
    slug: slug('social-cohesion'),
    order: 2,
    description:
      'Social cohesion built through practice rather than programming: shared meals, rituals, and dialogue where trust grows because people make something together and nobody arrives as a case to be managed.',
  },
  {
    _id: 'branch-creative-collaboration',
    _type: 'branch',
    title: 'Creative collaboration',
    slug: slug('creative-collaboration'),
    order: 3,
    description:
      'Creative collaborations and partnerships — art, storytelling, music, and ceremony co-created with partners, where community knowledge holders lead and institutions come to learn.',
  },
  {
    _id: 'branch-coordination',
    _type: 'branch',
    title: 'Coordination',
    slug: slug('coordination'),
    order: 4,
    description:
      'Coordinating and linking existing efforts: connecting people, organizations, and resources so that good work already happening in Hamilton finds its missing partners.',
  },
];
