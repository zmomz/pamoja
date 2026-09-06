import { pt } from './helpers.ts';
import type { SeedBlock } from './helpers.ts';

export interface SeedHomePage {
  _id: string;
  _type: 'homePage';
  heroTagline: string;
  positioningStatement: SeedBlock[];
  ground: string;
  roots: string;
  whoWeAre: string;
  ourWork: string;
  whatWeOffer: string;
  whoWeGrowWith: string;
  seeds: string;
}

export const homePage: SeedHomePage = {
  _id: 'homePage',
  _type: 'homePage',
  heroTagline: 'Pamoja means together.',
  positioningStatement: pt(
    '[Draft — final wording to be confirmed.]',
    'Newcomers are building this city. Pamoja works with newcomer Canadians who carry knowledge, wisdom, and traditions Hamilton needs — hosting, teaching, convening, and creating alongside anyone willing to build on common ground.',
    'Hamilton\u2019s settlement organizations do essential work, and our work would not be possible without them. We work in a different direction: building with newcomers, not delivering services to them.'
  ),
  ground:
    'Every tree stands on something. Pamoja stands on ground held in common: the Dish With One Spoon Wampum — take only what you need, leave some for others, keep the dish clean — the Two Row Wampum\u2019s ethic of two paths travelled side by side, and the Canadian Charter of Rights and Freedoms. The covenants are Indigenous teachings we learn from and honour. We do not claim them; we stand on them.',
  roots:
    'Two roots draw nourishment from that shared ground. Ta\u2019aruf: hospitality that builds ethical, dialogical relationship — protecting dignity, upholding difference without erasure. Takaful: mutual economic empowerment and collective stewardship, an economy where nobody thrives at another\u2019s expense. These are our own traditions, carried across oceans and replanted in Hamilton soil.',
  whoWeAre:
    'Pamoja means together, in Kiswahili. We are a grassroots collective led by newcomer Canadians of African Muslim origin, building community on ethical common ground in Hamilton. We do not claim to be everything to everyone. Belonging, as we practise it, is not comfort or sameness — it is building a city with the knowledge newcomers carry.',
  ourWork:
    'Everything Pamoja does serves four functions: connection across cultures, generations, and sectors; social cohesion built through shared practice; creative collaboration with partners who co-create rather than commission; and coordination that links existing efforts into something stronger. Most gatherings serve several branches at once — that overlap is the point.',
  whatWeOffer:
    'The tree gives back. Newcomer knowledge holders lead workshops, host ancestral coffee and tea rituals, and consult on dialogical intercultural programming — for universities, municipal departments, and non-profits ready to learn. The expertise is the community\u2019s; institutions arrive as guests and leave changed.',
  whoWeGrowWith:
    'A tree does not grow alone. Pamoja grows in a grove: the Community Permaculture Lab, whose front yard hosted our first gatherings; The Apothecary\u2019s Garden, co-creator of Two Row Citizens; TD Park People and the City of Hamilton, whose grants made the work possible; and the settlement organizations whose essential work ours depends on.',
  seeds:
    'What is growing: a Two Row Citizens series through 2026–27, a Kandakas\u2019 Feminism exhibition, and deeper roots across Hamilton. What we ask: space to gather, flexible multi-year trust-based funding, and institutional capacity — legal, financial, administrative. Not a donation request; an investment in a shared community.',
};
