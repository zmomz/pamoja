<?php
/**
 * Launch content. The copy comes from Pamoja_Website.docx (the manager's
 * brief, September 2026). Every entry is written with the community as the
 * actor. Photographs listed under 'photos' were supplied by Pamoja for the
 * website; the importer records that source on each one.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'branches' => array(
		array(
			'id'          => 'branch-connection',
			'name'        => 'Connection',
			'slug'        => 'connection',
			'order'       => 1,
			'description' => 'Intercultural, intergenerational, and cross-sector connection. Pamoja convenes people who would not otherwise meet — newcomer families, longtime neighbours, artists, and institutions — as contributors who each bring knowledge the others need.',
		),
		array(
			'id'          => 'branch-social-cohesion',
			'name'        => 'Social cohesion',
			'slug'        => 'social-cohesion',
			'order'       => 2,
			'description' => 'Social cohesion built through practice rather than programming: shared meals, rituals, and dialogue where trust grows because people make something together and nobody arrives as a case to be managed.',
		),
		array(
			'id'          => 'branch-creative-collaboration',
			'name'        => 'Creative collaboration',
			'slug'        => 'creative-collaboration',
			'order'       => 3,
			'description' => 'Creative collaborations and partnerships — art, storytelling, music, and ceremony co-created with partners, where community knowledge holders lead and institutions come to learn.',
		),
		array(
			'id'          => 'branch-coordination',
			'name'        => 'Coordination',
			'slug'        => 'coordination',
			'order'       => 4,
			'description' => 'Coordinating and linking existing efforts: connecting people, organizations, and resources so that good work already happening in Hamilton finds its missing partners.',
		),
	),

	'partners' => array(
		array(
			'id'           => 'partner-community-permaculture-lab',
			'name'         => 'Community Permaculture Lab',
			'type'         => 'partner',
			'order'        => 1,
			'contribution' => 'Gave space and roots. Pamoja has been hosted steadily every two weeks at the Community Permaculture Lab since 2023; the tea gathering there is the foundation of the relationships, trust, and shared learning that continue to shape Pamoja.',
		),
		array(
			'id'           => 'partner-apothecarys-garden',
			'name'         => 'The Apothecary’s Garden',
			'type'         => 'partner',
			'order'        => 2,
			'contribution' => 'Co-created Two Row Citizens at Churchill Park. Julia Hitchcock, steward of the garden, provided both her garden as a venue and crucial access to funding, and walked side-by-side with Pamoja in this work.',
		),
		array(
			'id'           => 'partner-td-park-people',
			'name'         => 'TD Park People',
			'type'         => 'funder',
			'order'        => 3,
			'contribution' => 'Grant support for Two Row Citizens at Churchill Park in 2024 and 2025.',
		),
		array(
			'id'           => 'partner-city-of-hamilton',
			'name'         => 'City of Hamilton',
			'type'         => 'funder',
			'order'        => 4,
			'contribution' => 'Downtown Placemaking Grant for the Coffee Ritual & Place-Conscious Dialogue at John Rebecca Public Park (September 2025), and support for Two Row Neighbours 2026.',
		),
		array(
			'id'           => 'partner-mcmaster-ccena',
			'name'         => 'McMaster CCENA',
			'type'         => 'partner',
			'order'        => 5,
			'contribution' => 'Supports Kandakas’ Feminism.',
		),
		array(
			'id'           => 'partner-mcmaster-ablds',
			'name'         => 'Africa & Black Diaspora Studies, McMaster University',
			'type'         => 'partner',
			'order'        => 6,
			'contribution' => 'Co-hosted the Coffee Social at McMaster University.',
		),
		array(
			'id'           => 'partner-mcmaster-bssc',
			'name'         => 'Black Student Success Centre, McMaster University',
			'type'         => 'partner',
			'order'        => 7,
			'contribution' => 'Co-hosted the Coffee Social and opened its lounge to students, staff, faculty, community members, and children.',
		),
		array(
			'id'           => 'partner-east-african-student-association',
			'name'         => 'East African Student Association',
			'type'         => 'partner',
			'order'        => 8,
			'contribution' => 'Co-hosted the Coffee Social at McMaster University.',
		),
	),

	/*
	 * Photographs supplied by Pamoja for the website. Each is imported once
	 * (matched by file name), given its alt text and caption, and marked
	 * consent-confirmed with the source recorded, so the consent rule lets it
	 * through. Untick "Consent confirmed" in the Media Library to withdraw one.
	 */
	'photos' => array(
		'photo-tea-gathering' => array(
			'file'    => 'tea-gathering-community-permaculture-lab.jpg',
			'title'   => 'Pamoja Tea Gathering at the Community Permaculture Lab',
			'alt'     => 'Neighbours seated in a circle of garden chairs at the Community Permaculture Lab; a table of glasses, mugs and a flask of tea in the foreground.',
			'caption' => 'Pamoja Tea Gathering at the Community Permaculture Lab.',
		),
		'photo-two-row-group' => array(
			'file'    => 'two-row-citizens-churchill-park-july-2025.jpg',
			'title'   => 'Second Two Row Citizens gathering at Churchill Park, July 2025',
			'alt'     => 'Participants of the second Two Row Citizens gathering standing and sitting together on blankets on the grass at Churchill Park, a greenhouse behind them.',
			'caption' => 'Second Two Row Citizens gathering at Churchill Park, July 2025.',
		),
		'photo-two-row-tent' => array(
			'file'    => 'two-row-citizens-churchill-park-tent.jpg',
			'title'   => 'Two Row Citizens gathering under the tent at Churchill Park',
			'alt'     => 'Neighbours seated in a wide circle of chairs under a green tent between trees at Churchill Park.',
			'caption' => 'Two Row Citizens at Churchill Park.',
		),
		'photo-coffee-ritual' => array(
			'file'    => 'coffee-ritual-john-rebecca-park-september-2025.jpg',
			'title'   => 'Coffee Ritual and place-conscious dialogue at John Rebecca Public Park',
			'alt'     => 'Neighbours seated on chairs and blankets in John Rebecca Park, lending their ears to place-conscious dialogue under a young tree.',
			'caption' => 'Pamoja Cultural Collective hosting a Coffee Ritual and place-conscious dialogue at John Rebecca Public Park in Hamilton, September 2025. Photography by Ponders.',
		),
		'photo-coffee-social' => array(
			'file'    => 'coffee-social-mcmaster-university.jpg',
			'title'   => 'Coffee Social at McMaster University',
			'alt'     => 'A hand pours coffee from a gold jebena into a small white cup at the Coffee Social, with the partner logos below.',
			'caption' => 'Coffee Social at McMaster University, presented with Africa & Black Diaspora Studies, the Black Student Success Centre and the East African Student Association.',
		),
	),

	'events' => array(
		array(
			'id'           => 'event-biweekly-tea-gathering',
			'title'        => 'Pamoja Tea Gatherings',
			'slug'         => 'pamoja-tea-gatherings',
			'date_display' => 'Every two weeks since 2023',
			'start_date'   => '2023-01-01',
			'location'     => 'Community Permaculture Lab, Hamilton',
			'status'       => 'ongoing',
			'branches'     => array( 'branch-connection', 'branch-social-cohesion' ),
			'partners'     => array( 'partner-community-permaculture-lab' ),
			'pull_quote'   => 'The tea gathering is not simply an event. It is a practice of coming together.',
			'featured'     => true,
			'order'        => 1,
			'cover'        => 'photo-tea-gathering',
			'gallery'      => array( 'photo-tea-gathering' ),
			'body'         => array(
				'Pamoja has been hosted steadily every two weeks at the Community Permaculture Lab.',
				'What began in 2023 as a simple gathering around tea became the foundation for the relationships, trust, and shared learning that continue to shape Pamoja today.',
				'For us, the tea gathering is not simply an event. It is a practice of coming together.',
			),
		),
		array(
			'id'           => 'event-two-row-citizens-churchill-park',
			'title'        => 'Two Row Citizens',
			'slug'         => 'two-row-citizens-churchill-park',
			'date_display' => '2024 & 2025',
			'start_date'   => '2024-06-01',
			'end_date'     => '2025-07-31',
			'location'     => 'Churchill Park, Hamilton',
			'status'       => 'past',
			'branches'     => array( 'branch-connection', 'branch-creative-collaboration', 'branch-social-cohesion' ),
			'partners'     => array( 'partner-apothecarys-garden', 'partner-td-park-people' ),
			'featured'     => true,
			'order'        => 2,
			'cover'        => 'photo-two-row-group',
			'gallery'      => array( 'photo-two-row-group', 'photo-two-row-tent' ),
			'body'         => array(
				'This gathering, initiated by Pamoja Cultural Collective and partner Julia Hitchcock, is a continuing collective effort to prepare the soil for an ethical common ground.',
				'It is grounded in our learning from the Two Row Wampum Covenant, a teaching we were introduced to through the guidance of our friend and ally, Daniel Coleman, Professor Emeritus at McMaster University.',
				'We are grateful to have walked side-by-side in this work with Julia Hitchcock, steward of The Apothecary’s Garden, who provided both her garden as a venue and crucial access to funding, alongside grant support from TD Park People.',
				'The gathering has continued as an opportunity for people from different communities to meet, listen, learn, and consider what it means to build relationships across difference.',
			),
		),
		array(
			'id'           => 'event-coffee-ritual-place-conscious-dialogue',
			'title'        => 'Coffee Ritual & Place-Conscious Dialogue',
			'slug'         => 'coffee-ritual-place-conscious-dialogue',
			'date_display' => 'September 2025',
			'start_date'   => '2025-09-01',
			'location'     => 'John Rebecca Public Park, Hamilton',
			'status'       => 'past',
			'branches'     => array( 'branch-connection', 'branch-social-cohesion', 'branch-coordination' ),
			'partners'     => array( 'partner-city-of-hamilton' ),
			'featured'     => true,
			'order'        => 3,
			'cover'        => 'photo-coffee-ritual',
			'gallery'      => array( 'photo-coffee-ritual' ),
			'body'         => array(
				'This placemaking event brought our ancestral East African practice of Coffee Ritual into the heart of downtown Hamilton.',
				'Through coffee, music, treaty-informed storytelling, and conversation, we created a space for community connection in a public park.',
				'The event was made possible through the City of Hamilton Downtown Placemaking Grant.',
				'It was another opportunity to take a practice rooted in our own traditions and bring it into relationship with the place we now call home.',
			),
		),
		array(
			'id'           => 'event-coffee-social-mcmaster',
			'title'        => 'Coffee Social at McMaster University',
			'slug'         => 'coffee-social-mcmaster-university',
			'date_display' => '2025',
			'start_date'   => '2025-11-01',
			'location'     => 'Black Student Success Centre lounge, McMaster University',
			'status'       => 'past',
			'branches'     => array( 'branch-connection', 'branch-creative-collaboration', 'branch-social-cohesion' ),
			'partners'     => array( 'partner-mcmaster-ablds', 'partner-mcmaster-bssc', 'partner-east-african-student-association' ),
			'pull_quote'   => 'It was a beautiful sight. A space where different generations, communities, and experiences could simply be together.',
			'featured'     => true,
			'order'        => 4,
			'video_url'    => 'https://youtu.be/E4Sr0M-LUkM',
			'cover'        => 'photo-coffee-social',
			'gallery'      => array(),
			'body'         => array(
				'In partnership with Africa and Black Diaspora Studies, the Black Student Success Centre, and the East African Student Association, we hosted an intergenerational gathering that brought students, staff, faculty, community members, and children together in the Black Student Success Centre lounge at McMaster University.',
				'The event included a coffee ceremony, food, music, and dialogue.',
				'There were Indigenous kin grounding the day through drumming. Elders played music and shared wisdom. Children ran around being children. Coffee was roasted. A henna artist adorned people’s hands. Students decompressed. Staff and faculty came from across campus. Hamilton community members joined from all walks of life.',
				'It was a beautiful sight. A space where different generations, communities, and experiences could simply be together.',
			),
		),
		array(
			'id'           => 'event-two-row-citizens-series-2026',
			'title'        => 'Two Row Neighbours 2026',
			'slug'         => 'two-row-neighbours-2026',
			'date_display' => 'November / December 2026 · stay tuned',
			'start_date'   => '2026-11-15',
			'location'     => 'Hamilton',
			'status'       => 'upcoming',
			'branches'     => array( 'branch-connection', 'branch-creative-collaboration', 'branch-coordination' ),
			'partners'     => array( 'partner-apothecarys-garden', 'partner-city-of-hamilton' ),
			'featured'     => false,
			'body'         => array(
				'Coming together to continue the work towards building a sustainable organization for social cohesion and collective prosperity.',
				'Coming up in November/December. Stay tuned.',
			),
		),
		array(
			'id'           => 'event-kandakas-feminism',
			'title'        => 'Kandakas’ Feminism?',
			'slug'         => 'kandakas-feminism',
			'date_display' => 'January 2027',
			'start_date'   => '2027-01-01',
			'location'     => 'Hamilton',
			'status'       => 'upcoming',
			'branches'     => array( 'branch-creative-collaboration', 'branch-connection' ),
			'partners'     => array( 'partner-mcmaster-ccena' ),
			'featured'     => false,
			'body'         => array(
				'A community event that explores newcomer women’s experiences of care, resilience, and belonging in Hamilton, bringing together visual art, oral storytelling, cultural dialogue, music, and traditional coffee ceremony.',
			),
		),
	),

	'services' => array(
		array(
			'id'               => 'service-workshops',
			'title'            => 'Workshops',
			'order'            => 1,
			'summary'          => 'Hands-on sessions led by newcomer knowledge holders — cooking, ethical engagement, cultural practice — for teams and institutions ready to learn.',
			'what_it_is'       => 'A Pamoja workshop is a working session in which newcomer practitioners teach what they carry: a dish and the knowledge around it, a practice of hospitality, a way of holding dialogue. The community practises; your organization is the guest.',
			'what_happens'     => array(
				'Sessions open with hospitality — tea or coffee, stories, names — because Ta’aruf comes before agendas. Then the knowledge holder leads: hands in the dough, ears on the story, a practice tried rather than described.',
				'The session closes in conversation about what was learned and what it asks of the guests afterwards.',
			),
			'what_to_expect'   => array(
				'Expect to be a guest in someone else’s practice. That means following the lead of the knowledge holder, some discomfort, and a pace set by the practice rather than by the clock.',
			),
			'need_from_you'    => array(
				'A space that can hold a circle, not a boardroom that holds a hierarchy',
				'A group willing to participate, not observe',
				'Fair honorariums for the knowledge holders, agreed before the session',
				'Time: at least half a day',
				'Decision-makers in the room',
			),
			'protocols'        => array(
				'[Protocol wording to be confirmed — these are ancestral practices, not catering. The knowledge holders set the terms.]',
			),
			'what_this_is_not' => array(
				'Not diversity training delivered to a checklist',
				'Not a performance of culture for an audience',
				'Not a service delivered to newcomers',
				'Not a one-off that substitutes for a relationship',
			),
			'lead_time'        => 'Sessions typically run two to three hours. Ask at least six weeks ahead — trust-building comes first.',
		),
		array(
			'id'               => 'service-hospitality-rituals',
			'title'            => 'Hospitality rituals',
			'order'            => 2,
			'summary'          => 'Ancestral coffee and tea rituals, hosted by newcomer practitioners for your team, your gathering, or your public space.',
			'what_it_is'       => 'The coffee ritual and the tea ritual are ancestral practices of hospitality carried by newcomer families from Sudan and East Africa. Hosting one for your organization means being received as a guest, at the pace the practice demands.',
			'what_happens'     => array(
				'A practitioner roasts, grinds, and pours in the rhythm the practice demands — nothing is rushed. Guests are seated, served, and drawn into conversation; the ritual sets the terms of the gathering.',
			),
			'what_to_expect'   => array(
				'Expect to relinquish control of the agenda. The host leads; your organization is the guest. The ritual takes the time it takes.',
			),
			'need_from_you'    => array(
				'A quiet space where a small fire or burner can be used safely, or an outdoor site',
				'A group of a size the practitioners agree to',
				'Fair honorariums for the practitioners',
				'Patience with the pace',
				'A clear reason the gathering matters',
			),
			'protocols'        => array(
				'[Protocol wording to be confirmed — these are ancestral practices, not catering. The knowledge holders set the terms.]',
			),
			'what_this_is_not' => array(
				'Not catering, and not a beverage service for your event',
				'Not entertainment at the edge of a program',
				'Not something that can be shortened to fit a schedule',
				'Not a photo opportunity',
			),
			'lead_time'        => 'A ritual runs one to two hours and cannot be shortened. Ask at least eight weeks ahead.',
		),
		array(
			'id'               => 'service-consultation',
			'title'            => 'Consultation',
			'order'            => 3,
			'summary'          => 'Pamoja curates dialogical intercultural events and programming for social cohesion, with the community leading and your institution learning.',
			'what_it_is'       => 'For universities, municipal departments, and non-profits building intercultural programming: Pamoja co-designs gatherings, dialogue, and placemaking with newcomer knowledge holders at the centre.',
			'what_happens'     => array(
				'Engagements begin the Pamoja way: coffee or tea, stories, and understanding before agendas. Then the collective and your team design together — the shape, the partners, the money, the credit.',
			),
			'what_to_expect'   => array(
				'Expect collective decision-making, which is slower than a chain of command and sturdier than one. Expect leadership and credit to be shared publicly.',
			),
			'need_from_you'    => array(
				'Decision-makers in the room from the first conversation',
				'A budget that covers honorariums and operations',
				'Time for consensus',
				'Willingness to be taught rather than to consult',
				'A commitment beyond a single event',
			),
			'protocols'        => array(
				'[Protocol wording to be confirmed — these are ancestral practices, not catering. The knowledge holders set the terms.]',
			),
			'what_this_is_not' => array(
				'Not a vendor relationship — we do not deliver a community to a specification',
				'Not a consultation of newcomers about a plan already made',
				'Not a rate card',
				'Not a transaction',
			),
			'lead_time'        => 'Consultation engagements are scoped in conversation, not quoted from a rate card. Ask a season ahead.',
		),
	),

	'pages' => array(
		'home'      => array(
			'id'    => 'page-home',
			'title' => 'Home',
			'slug'  => 'home',
			'body'  => '',
		),
		'news'      => array(
			'id'    => 'page-news',
			'title' => 'Blog',
			'slug'  => 'blog',
			'body'  => '',
		),
		'about'     => array(
			'id'       => 'page-about',
			'title'    => 'About',
			'slug'     => 'about',
			'template' => 'page-about.php',
			'body'     => '',
		),
		'engage'    => array(
			'id'       => 'page-engage',
			'title'    => 'Engage',
			'slug'     => 'engage',
			'template' => 'page-engage.php',
			'body'     => '',
		),
		'thank-you' => array(
			'id'       => 'page-thank-you',
			'title'    => 'The kettle is on.',
			'slug'     => 'thank-you',
			'template' => 'page-thank-you.php',
			'body'     => "<!-- wp:paragraph {\"className\":\"lede\"} -->\n<p class=\"lede\">Your message has reached us. We read everything together, and we reply as a collective — so it may take a little time. That's how trust gets built.</p>\n<!-- /wp:paragraph -->",
		),
	),

	'news' => array(
		array(
			'id'     => 'news-how-to-write',
			'title'  => 'How to write for the blog (draft — delete or keep as a reminder)',
			'status' => 'draft',
			'body'   => array(
				'This is an example post, saved as a draft so it never appears on the site. Blog posts show up newest-first at /blog/.',
				'Five things to hold onto: newcomers are the subject, never the object. Never write "serving/helping/supporting newcomers", "beneficiaries" or "clients" for community members. Write instead: building with, working alongside, hosting, convening, teaching. Cut "giving newcomers a voice" entirely. When numbers appear, they describe the size of a gathering, never a caseload.',
				'A featured image only appears once it has alt text and "Consent confirmed" ticked in the media library.',
			),
		),
	),
);
