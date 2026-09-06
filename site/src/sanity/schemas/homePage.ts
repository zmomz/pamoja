import { defineType, defineField, defineArrayMember } from 'sanity';

const passage = (name: string, title: string, description: string) =>
  defineField({
    name,
    title,
    type: 'text',
    rows: 5,
    description,
    validation: (Rule) => Rule.required().min(40),
  });

export const homePage = defineType({
  name: 'homePage',
  title: 'Home Page',
  type: 'document',
  description:
    'Singleton. The scroll journey through the whole tree — one short passage per tree part (brief §5.1).',
  fields: [
    defineField({
      name: 'heroTagline',
      title: 'Hero tagline',
      type: 'string',
      initialValue: 'Pamoja means together.',
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'positioningStatement',
      title: 'Positioning statement',
      type: 'array',
      of: [defineArrayMember({ type: 'block' })],
      description:
        'The short-form "who we are / what we\u2019re not" passage — affirmative first, explicit contrast just below. Final wording to be confirmed.',
      validation: (Rule) => Rule.required().min(1),
    }),
    passage(
      'ground',
      'Ground — The Ground We Share',
      '40–80 words. The soil: Dish With One Spoon Wampum, Two Row Wampum, Canadian Charter — shared ground, honoured not claimed.'
    ),
    passage(
      'roots',
      'Roots — Our Roots',
      '40–80 words. Ta\u2019aruf and Takaful — Pamoja\u2019s own ethics, drawing nourishment from the shared ground.'
    ),
    passage(
      'whoWeAre',
      'Trunk — Who We Are',
      '40–80 words. The collective itself: name, mission, why Pamoja exists.'
    ),
    passage(
      'ourWork',
      'Branches — Our Work',
      '40–80 words. The four functions: connection, social cohesion, creative collaboration, coordination.'
    ),
    passage(
      'whatWeOffer',
      'What the tree offers — What We Offer',
      '40–80 words. Services: workshops, hospitality rituals, consultation — newcomer practitioners, institutional learners.'
    ),
    passage(
      'whoWeGrowWith',
      'The grove — Who We Grow With',
      '40–80 words. Partners, allies, funders — a tree does not grow alone.'
    ),
    passage(
      'seeds',
      'Seeds — What\u2019s next and Our Ask',
      '40–80 words. What is growing, and the invitation to partner with Pamoja.'
    ),
  ],
});
