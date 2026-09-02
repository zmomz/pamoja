import { defineType, defineField, defineArrayMember } from 'sanity';

export const siteSettings = defineType({
  name: 'siteSettings',
  title: 'Site Settings',
  type: 'document',
  description: 'Singleton. Sitewide contact details, social links, and the footer land acknowledgment.',
  fields: [
    defineField({
      name: 'contactEmail',
      title: 'Contact email',
      type: 'string',
      validation: (Rule) => Rule.required().email(),
    }),
    defineField({
      name: 'socialLinks',
      title: 'Social links',
      type: 'array',
      of: [
        defineArrayMember({
          type: 'object',
          fields: [
            defineField({
              name: 'label',
              title: 'Label',
              type: 'string',
              validation: (Rule) => Rule.required(),
            }),
            defineField({
              name: 'url',
              title: 'URL',
              type: 'url',
              validation: (Rule) => Rule.required(),
            }),
          ],
          preview: { select: { title: 'label', subtitle: 'url' } },
        }),
      ],
    }),
    defineField({
      name: 'landAcknowledgment',
      title: 'Land acknowledgment (footer, sitewide)',
      type: 'array',
      of: [defineArrayMember({ type: 'block' })],
      description: 'Wording supplied by Abdo; do not improvise.',
      validation: (Rule) => Rule.required().min(1),
    }),
    defineField({
      name: 'ogDefaultImage',
      title: 'Default Open Graph image',
      type: 'image',
      fields: [
        defineField({
          name: 'alt',
          title: 'Alt text',
          type: 'string',
          validation: (Rule) => Rule.required(),
        }),
      ],
    }),
    defineField({
      name: 'plausibleDomain',
      title: 'Plausible analytics domain',
      type: 'string',
      description: 'Domain registered in Plausible (privacy-respecting analytics, brief §10).',
    }),
  ],
});
