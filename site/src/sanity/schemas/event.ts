import { defineType, defineField, defineArrayMember } from 'sanity';

export const event = defineType({
  name: 'event',
  title: 'Event / Program',
  type: 'document',
  description:
    'A leaf on the tree. Write every entry with the community as the actor — who hosted, who taught, what knowledge was shared (brief §5.5).',
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
      name: 'dateOrRange',
      title: 'Date or date range (display)',
      type: 'string',
      description: 'As shown on the site, e.g. "2023–2026" or "September 2025".',
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'startDate',
      title: 'Start date (for sorting)',
      type: 'date',
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'endDate',
      title: 'End date (optional)',
      type: 'date',
    }),
    defineField({
      name: 'location',
      title: 'Location',
      type: 'string',
    }),
    defineField({
      name: 'status',
      title: 'Status',
      type: 'string',
      options: {
        list: [
          { title: 'Past', value: 'past' },
          { title: 'Upcoming', value: 'upcoming' },
          { title: 'Handed on', value: 'handed-on' },
        ],
        layout: 'radio',
      },
      validation: (Rule) => Rule.required(),
    }),
    defineField({
      name: 'handedOnTo',
      title: 'Handed on to',
      type: 'reference',
      to: [{ type: 'partner' }],
      description:
        'Who now owns and runs this work. Shown only when status is "Handed on" — the site should say so plainly, neither claiming the work nor deleting it.',
      hidden: ({ parent }) => parent?.status !== 'handed-on',
      validation: (Rule) =>
        Rule.custom((value, context) => {
          const parent = context.parent as { status?: string } | undefined;
          if (parent?.status === 'handed-on' && !value) {
            return 'Name who this work was handed on to.';
          }
          return true;
        }),
    }),
    defineField({
      name: 'branchesServed',
      title: 'Branches served',
      type: 'array',
      of: [defineArrayMember({ type: 'reference', to: [{ type: 'branch' }] })],
      description:
        'Drives the Our Work filter. Most events serve more than one branch — select every branch it genuinely applies to; the overlap is the argument.',
      validation: (Rule) => Rule.required().min(1).unique(),
    }),
    defineField({
      name: 'body',
      title: 'Description',
      type: 'array',
      of: [defineArrayMember({ type: 'block' })],
      description:
        '2–3 paragraphs. The community is the subject of active verbs — never "served", "helped", or "beneficiaries" (brief §1.1 reversal test).',
      validation: (Rule) => Rule.required().min(1),
    }),
    defineField({
      name: 'partnersCredited',
      title: 'Partners and funders credited',
      type: 'array',
      of: [defineArrayMember({ type: 'reference', to: [{ type: 'partner' }] })],
      description: 'Credit is shared publicly (brief §9.4). Name who co-created, hosted, or funded.',
      validation: (Rule) => Rule.unique(),
    }),
    defineField({
      name: 'gallery',
      title: 'Image gallery',
      type: 'array',
      of: [
        defineArrayMember({
          type: 'image',
          options: { hotspot: true },
          fields: [
            defineField({
              name: 'alt',
              title: 'Alt text',
              type: 'string',
              description:
                'Describes the image for screen readers. Alt text passes the §1.1 reversal test too: people hosting, teaching, pouring — never being served.',
              validation: (Rule) => Rule.required(),
            }),
            defineField({
              name: 'consentConfirmed',
              title: 'Consent confirmed',
              type: 'boolean',
              initialValue: false,
              description:
                "Confirm consent is on file for every identifiable person. Never publish children's faces without explicit parental consent.",
              validation: (Rule) => Rule.required(),
            }),
          ],
          validation: (Rule) =>
            Rule.custom((value) => {
              const image = value as { consentConfirmed?: boolean } | undefined;
              if (image?.consentConfirmed !== true) {
                return 'Publishing is blocked until consent is confirmed on file for every identifiable person (brief §9.3).';
              }
              return true;
            }),
        }),
      ],
    }),
    defineField({
      name: 'pullQuote',
      title: 'Pull quote (optional)',
      type: 'text',
      rows: 3,
    }),
    defineField({
      name: 'featured',
      title: 'Featured',
      type: 'boolean',
      initialValue: false,
    }),
  ],
  orderings: [
    {
      title: 'Most recent first',
      name: 'startDateDesc',
      by: [{ field: 'startDate', direction: 'desc' }],
    },
  ],
  preview: {
    select: { title: 'title', subtitle: 'dateOrRange', status: 'status' },
    prepare({ title, subtitle, status }) {
      return { title, subtitle: `${subtitle ?? ''} · ${status ?? ''}` };
    },
  },
});
