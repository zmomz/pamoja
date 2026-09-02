import { defineType, defineField, defineArrayMember } from 'sanity';

export const service = defineType({
  name: 'service',
  title: 'Service',
  type: 'document',
  description:
    'One of the three offerings. Every service page uses the same eight sections in the same order (brief §6.2). Remember: newcomers are the practitioners delivering the service; institutions are the learners.',
  fields: [
    defineField({
      name: 'title',
      title: 'Title',
      type: 'string',
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'slug',
      title: 'Slug',
      type: 'slug',
      options: { source: 'title' },
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'summary',
      title: 'Summary',
      type: 'text',
      rows: 3,
      description: 'One or two sentences for the What We Offer overview.',
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'whatItIs',
      title: '1. What it is',
      type: 'text',
      rows: 5,
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'whatHappens',
      title: '2. What actually happens',
      type: 'array',
      of: [defineArrayMember({ type: 'block' })],
      description: 'The shape of the session, honestly described.',
      validation: (Rule) => Rule.required().min(1),
    }),
    defineField({
      name: 'whatToExpect',
      title: '3. What to expect',
      type: 'array',
      of: [defineArrayMember({ type: 'block' })],
      description:
        'Include the discomfort. Belonging means embracing discomfort as a path to understanding — it does not disappear from the sales page.',
      validation: (Rule) => Rule.required().min(1),
    }),
    defineField({
      name: 'whatWeNeedFromYou',
      title: '4. What we need from you',
      type: 'array',
      of: [defineArrayMember({ type: 'string' })],
      description:
        'Space, time, budget, staff participation, willingness to be a guest in someone else\u2019s practice.',
      validation: (Rule) => Rule.required().min(1),
    }),
    defineField({
      name: 'culturalProtocols',
      title: '5. Cultural protocols',
      type: 'array',
      of: [defineArrayMember({ type: 'block' })],
      description:
        'How to engage respectfully with this specific offering. These are ancestral practices, not catering. Abdo supplies the protocol language for each service — leave this room generous.',
      validation: (Rule) => Rule.required().min(1),
    }),
    defineField({
      name: 'whatThisIsNot',
      title: '6. What this is not',
      type: 'array',
      of: [defineArrayMember({ type: 'string' })],
      description: 'A short, direct list. This section prevents the wrong inquiries.',
      validation: (Rule) => Rule.required().min(1),
    }),
    defineField({
      name: 'timeAndLeadTime',
      title: '7. Time and lead time',
      type: 'string',
      description: 'How long a session runs, and how far ahead to ask.',
      validation: (Rule) => Rule.required(),
    }),
  ],
  preview: {
    select: { title: 'title', subtitle: 'summary' },
  },
});
