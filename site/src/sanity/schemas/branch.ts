import { defineType, defineField } from 'sanity';

export const branch = defineType({
  name: 'branch',
  title: 'Branch',
  type: 'document',
  description:
    'One of the four functions of Pamoja\u2019s work: connection, social cohesion, creative collaboration, coordination.',
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
      name: 'description',
      title: 'Description',
      type: 'text',
      rows: 4,
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'order',
      title: 'Order',
      type: 'number',
      description: 'Display order on Our Work (1–4).',
      validation: (Rule) => Rule.required().integer().min(1),
    }),
  ],
  orderings: [
    { title: 'Order', name: 'orderAsc', by: [{ field: 'order', direction: 'asc' }] },
  ],
});
