import { defineType, defineField } from 'sanity';

export const partner = defineType({
  name: 'partner',
  title: 'Partner / Ally / Funder',
  type: 'document',
  description:
    'A member of the grove around the tree. No logo walls: every entry names what the partner actually contributed (brief §5.7).',
  fields: [
    defineField({
      name: 'name',
      title: 'Name',
      type: 'string',
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'slug',
      title: 'Slug',
      type: 'slug',
      options: { source: 'name' },
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'type',
      title: 'Type',
      type: 'string',
      options: {
        list: [
          { title: 'Partner', value: 'partner' },
          { title: 'Ally', value: 'ally' },
          { title: 'Funder', value: 'funder' },
          { title: 'Settlement sector', value: 'settlement-sector' },
        ],
        layout: 'radio',
      },
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'contribution',
      title: 'Contribution',
      type: 'text',
      rows: 4,
      description:
        'What they actually contributed — space, funding, teaching, stewardship. Required; a name without its contribution must not be published.',
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'url',
      title: 'Website',
      type: 'url',
    }),
    defineField({
      name: 'logo',
      title: 'Logo (optional)',
      type: 'image',
      description: 'Optional. The contribution text carries the credit, not the logo.',
      fields: [
        defineField({
          name: 'alt',
          title: 'Alt text',
          type: 'string',
          validation: (Rule) => Rule.required(),
        }),
      ],
    }),
  ],
  preview: {
    select: { title: 'name', subtitle: 'type' },
  },
});
