import { pt, slug } from './helpers.ts';
import type { SeedBlock, SeedSlug } from './helpers.ts';

export interface SeedService {
  _id: string;
  _type: 'service';
  title: string;
  slug: SeedSlug;
  summary: string;
  whatItIs: string;
  whatHappens: SeedBlock[];
  whatToExpect: SeedBlock[];
  whatWeNeedFromYou: string[];
  culturalProtocols: SeedBlock[];
  whatThisIsNot: string[];
  timeAndLeadTime: string;
}

// The three offerings from brief §5.6. On this page the roles invert by
// design: newcomers are the practitioners delivering the service, and
// institutions are the learners.
const PROTOCOL_PLACEHOLDER =
  '[Protocol wording to be supplied by Abdo — these are ancestral practices, not catering.]';

export const services: SeedService[] = [
  {
    _id: 'service-workshops',
    _type: 'service',
    title: 'Workshops',
    slug: slug('workshops'),
    summary:
      'Hands-on sessions led by newcomer knowledge holders — cooking, ethical engagement, community-building, creative collaboration — for institutions ready to be the learners.',
    whatItIs:
      'A Pamoja workshop is a working session in which newcomer practitioners teach what they carry: a cuisine and the hospitality ethic inside it, the practice of building trust across difference, the craft of convening a room where everyone contributes. Your team does not attend to observe a community; it arrives to learn from one.',
    whatHappens: pt(
      'Sessions open with hospitality — tea or coffee, stories, names — because Ta\u2019aruf takes time and agendas come after relationship. The practitioner leading the session then sets the practice: cooking together, mapping a community\u2019s knowledge, or working through an ethical-engagement framework drawn from lived experience.',
      'Participants work, they do not watch. A session closes with shared food and a plainspoken round of what was learned and what changes on Monday morning.'
    ),
    whatToExpect: pt(
      'Expect to be a guest in someone else\u2019s practice. That means following the lead of the practitioner in the room, including when their way of working differs from your organization\u2019s habits.',
      'Expect some discomfort. Belonging means embracing discomfort as a path to understanding, and a good session surfaces the assumptions your team didn\u2019t know it carried. That discomfort is part of what you are paying for — and part of what makes the learning hold.'
    ),
    whatWeNeedFromYou: [
      'A space that can hold a circle, not a boardroom that holds a hierarchy',
      'Time — a real half-day, not a lunch-hour slot',
      'Budget for fair honorariums for the practitioners leading the session',
      'Staff who participate fully, including leadership',
      'Willingness to be a guest in someone else\u2019s practice',
    ],
    culturalProtocols: pt(
      PROTOCOL_PLACEHOLDER,
      'In the meantime: arrive as guests. The practitioners leading these sessions carry practices passed down through families and communities; treat the knowledge as theirs, credit it publicly, and do not photograph anyone without explicit consent.'
    ),
    whatThisIsNot: [
      'Not diversity training delivered to a checklist',
      'Not a workshop for newcomers — newcomers are the practitioners leading it',
      'Not a cultural performance booked for an event',
      'Not a one-off consultation that ends when the invoice is paid',
    ],
    timeAndLeadTime:
      'Sessions typically run two to three hours. Ask at least six weeks ahead — trust-building starts before the session does, and we do not move fast.',
  },
  {
    _id: 'service-hospitality-rituals',
    _type: 'service',
    title: 'Hospitality rituals',
    slug: slug('hospitality-rituals'),
    summary:
      'Ancestral coffee and tea rituals, hosted by newcomer practitioners for your team, your community, or your event — ceremonies that teach by slowing everyone down.',
    whatItIs:
      'The coffee ritual and the tea ritual are ancestral practices of hospitality carried by Pamoja\u2019s knowledge holders. Hosted in your space or in a shared public one, a ritual turns a gathering of strangers into a room of guests — and teaches, by practice rather than lecture, what hospitality-as-ethics actually feels like.',
    whatHappens: pt(
      'A practitioner roasts, grinds, and pours in the rhythm the practice demands — the coffee ritual can take the better part of an hour, and that slowness is the teaching. Guests sit close, receive rather than serve themselves, and learn the meanings carried in each round.',
      'Conversation unfolds inside the ritual\u2019s structure: stories first, then the questions your group brought with it, held at the pace the ceremony sets.'
    ),
    whatToExpect: pt(
      'Expect to relinquish control of the agenda. The host leads; your organization follows. Phones down, ranks dissolved — a director and an intern receive the same cup in the same order.',
      'Expect the discomfort of slowness in a culture of meetings. Sitting with that discomfort, rather than rushing past it, is where the practice does its work.'
    ),
    whatWeNeedFromYou: [
      'A quiet space where a small fire or burner can be used safely, or a venue we agree on together',
      'Unhurried time — never schedule a ritual between two meetings',
      'Budget for fair honorariums and materials',
      'A group small enough to be hosted as guests, not processed as an audience',
      'Willingness to follow the host\u2019s lead for the duration of the ritual',
    ],
    culturalProtocols: pt(
      PROTOCOL_PLACEHOLDER,
      'In the meantime: these are ancestral practices, not catering. The host\u2019s word governs the ceremony — when it begins, how it proceeds, when it ends. Photography only with the host\u2019s permission and the explicit consent of every person identifiable in the frame.'
    ),
    whatThisIsNot: [
      'Not catering, and not a beverage service for your event',
      'Not a performance to be watched — everyone present is a guest and participant',
      'Not bookable on demand at short notice',
      'Not a team-building icebreaker to be rushed through',
    ],
    timeAndLeadTime:
      'A ritual runs one to two hours and cannot be shortened. Ask at least eight weeks ahead; the relationship comes before the booking.',
  },
  {
    _id: 'service-consultation',
    _type: 'service',
    title: 'Consultation',
    slug: slug('consultation'),
    summary:
      'Pamoja curates dialogical intercultural events and programming for social cohesion — designed with institutions, led by the community knowledge holders whose practice it is.',
    whatItIs:
      'For universities, municipal departments, and non-profits building intercultural programming, Pamoja offers consultation grounded in practice: we curate dialogical events, design programming for social cohesion, and bring the community knowledge holders whose expertise the work depends on into the design from the first meeting — not as a consultation checkbox, but as co-authors.',
    whatHappens: pt(
      'Engagements begin the Pamoja way: coffee or tea, stories, and understanding before agendas. From there we map what you are hoping to build, who else is involved, and what knowledge the community holds that the work needs.',
      'Design happens in dialogue — mutual consultation, shared leadership, public credit shared publicly. What emerges is programming the community leads and your institution learns to stand behind.'
    ),
    whatToExpect: pt(
      'Expect collective decision-making, which is slower than a chain of command and sturdier than one. Expect your timelines to be examined, not simply accepted.',
      'Expect the discomfort of acknowledged imbalance: we name power differences in the room rather than pretend them away, because that naming is what makes genuine collaboration possible.'
    ),
    whatWeNeedFromYou: [
      'Decision-makers in the room from the first conversation',
      'Patience with collective, consensus-based decision-making',
      'Budget structured as a financial covenant — take only what you need, leave something for others',
      'Public credit shared with the knowledge holders who shape the work',
      'Commitment to long-term mutual thriving, not a one-off extraction',
    ],
    culturalProtocols: pt(
      PROTOCOL_PLACEHOLDER,
      'In the meantime: engagement follows Ta\u2019aruf — trust first, agendas second. Knowledge shared in consultation remains the community\u2019s; it is credited, compensated, and never republished without permission.'
    ),
    whatThisIsNot: [
      'Not a vendor relationship — we do not deliver a community to a specification',
      'Not access to newcomers as research subjects or consultation fodder',
      'Not a diversity audit of your organization',
      'Not fast — if the timeline cannot bend, we are not the right partner',
    ],
    timeAndLeadTime:
      'Consultation engagements are scoped in conversation, not quoted from a rate card. Begin the conversation at least two months before you hope to start.',
  },
];
