<?php
/**
 * Every passage of site copy, editable under Pamoja → Site copy.
 *
 * The schema below is the single source of truth: it builds the admin
 * page, sanitizes what is saved, and supplies the launch copy as defaults
 * (from Pamoja_Website.docx, September 2026), so the site renders in full
 * before anyone touches a field.
 *
 * Types: text (one line, plain), textarea (plain, line breaks kept),
 * html (rich text: paragraphs, bold, italics, links, lists), url.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PAMOJA_HOME_OPTION = 'pamoja_home';

/**
 * @return array<string, array{title:string, intro?:string, fields:array<string, array{label:string, type:string, default:string, help?:string}>}>
 */
function pamoja_home_schema(): array {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	// A field always carries label, type and default — a default may legitimately
	// be empty (the source links below), so only 'help' is dropped when unused.
	$field = static function ( string $type ) {
		return static function ( string $label, string $default, string $help = '' ) use ( $type ) {
			$out = array( 'label' => $label, 'type' => $type, 'default' => $default );
			if ( '' !== $help ) {
				$out['help'] = $help;
			}
			return $out;
		};
	};
	$t = $field( 'text' );
	$a = $field( 'textarea' );
	$h = $field( 'html' );
	$u = $field( 'url' );

	$schema = array(
		'hero' => array(
			'title'  => __( 'Home · Hero', 'pamoja' ),
			'intro'  => __( 'The first screen: the statement, the lead, and the tree as the map. The labels on the tree come from the page titles and headings below.', 'pamoja' ),
			'fields' => array(
				'tag'   => $t( __( 'Eyebrow', 'pamoja' ), 'Pamoja Cultural Collective · Hamilton' ),
				'title' => $h( __( 'The statement', 'pamoja' ), 'Treaty-committed neighbours coming together to create the conditions for <em>social cohesion</em> and <em>collective prosperity</em>.', __( 'Put a word in italics to pick it out in karkadeh red. Headings are one line, so paragraphs and lists are ignored here.', 'pamoja' ) ),
				'lead'  => $h( __( 'Lead', 'pamoja' ), '<p><em>Pamoja</em> is a collective built through relationships.</p>' ),
				'cta'   => $t( __( 'Button', 'pamoja' ), 'Start a conversation' ),
				'cta2'  => $t( __( 'Second link', 'pamoja' ), 'What’s coming up ↓' ),
				'hint'  => $t( __( 'Small line under the buttons', 'pamoja' ), 'The tree is the map. Pick a part.' ),
			),
		),
		'who' => array(
			'title'  => __( 'Home · Who we are', 'pamoja' ),
			'fields' => array(
				'tag'      => $t( __( 'Eyebrow', 'pamoja' ), 'Who we are' ),
				'title'    => $t( __( 'Heading', 'pamoja' ), 'A collective built through relationships.' ),
				'g1_title' => $t( __( 'Group 1 — name', 'pamoja' ), 'Neighbours' ),
				'g1_body'  => $a( __( 'Group 1 — text', 'pamoja' ), 'Newcomers and immigrants · Settlers and long-established residents · Indigenous neighbours' ),
				'g2_title' => $t( __( 'Group 2 — name', 'pamoja' ), 'Connectors & Partners' ),
				'g2_body'  => $a( __( 'Group 2 — text', 'pamoja' ), 'People and organizations who can open doors, share resources, create opportunities, connect us to communities, and help build this organization.' ),
				'g3_title' => $t( __( 'Group 3 — name', 'pamoja' ), 'Organizers' ),
				'g3_body'  => $a( __( 'Group 3 — text', 'pamoja' ), 'People who are helping shape, develop, and deliver the work.' ),
			),
		),
		'grown' => array(
			'title'  => __( 'Home · What we’ve grown', 'pamoja' ),
			'intro'  => __( 'The tiles are the events marked "Featured" under Pamoja → Events, in their Order.', 'pamoja' ),
			'fields' => array(
				'tag'   => $t( __( 'Eyebrow', 'pamoja' ), 'What we’ve grown' ),
				'title' => $t( __( 'Heading', 'pamoja' ), 'How we’ve come together so far.' ),
				'intro' => $h( __( 'Intro', 'pamoja' ), '<p>We have developed a number of initiatives that have allowed us to bring our values into practice. Each has grown from relationships and the willingness of people to come together.</p>' ),
				'all'   => $t( __( 'Link to all events', 'pamoja' ), 'Every gathering →' ),
			),
		),
		'coming' => array(
			'title'  => __( 'Home · Coming up', 'pamoja' ),
			'intro'  => __( 'Shows the events marked "Upcoming". Each is also a fruit on the tree.', 'pamoja' ),
			'fields' => array(
				'tag'         => $t( __( 'Eyebrow', 'pamoja' ), 'Coming up' ),
				'title'       => $t( __( 'Heading', 'pamoja' ), 'What’s next.' ),
				'empty'       => $t( __( 'When nothing is upcoming', 'pamoja' ), 'The next gathering is being planned. Ask us about it.' ),
				'keep_label'  => $t( __( '"Keep me posted" — field label', 'pamoja' ), 'Your email' ),
				'keep_button' => $t( __( '"Keep me posted" — button', 'pamoja' ), 'Tell me when it’s announced' ),
				'keep_done'   => $t( __( '"Keep me posted" — confirmation', 'pamoja' ), 'Noted. We’ll write when it’s announced.' ),
			),
		),
		'seeds' => array(
			'title'  => __( 'Home · Seeds', 'pamoja' ),
			'fields' => array(
				'tag'      => $t( __( 'Eyebrow', 'pamoja' ), 'Seeds' ),
				'title'    => $t( __( 'Heading', 'pamoja' ), 'Plant something with us.' ),
				'intro'    => $h( __( 'Intro', 'pamoja' ), '<p>Every gathering Pamoja has grown came from seed: a yard offered, a grant trusted, a partner’s time lent. Three ways in, and one first step for all of them: a conversation.</p>' ),
				's1_body'  => $t( __( 'Volunteer — one line', 'pamoja' ), 'Help shape, develop, and deliver the work.' ),
				's1_link'  => $t( __( 'Volunteer — link text', 'pamoja' ), 'I’d like to help →' ),
				's2_body'  => $t( __( 'Partner — one line', 'pamoja' ), 'Open doors, share resources, build with us.' ),
				's2_link'  => $t( __( 'Partner — link text', 'pamoja' ), 'How we work with partners →' ),
				's3_body'  => $t( __( 'Support — one line', 'pamoja' ), 'Space, funding, capacity.' ),
				's3_link'  => $t( __( 'Support — link text', 'pamoja' ), 'Offer something →' ),
			),
		),
		'about' => array(
			'title'  => __( 'About · Page head', 'pamoja' ),
			'fields' => array(
				'tag'   => $t( __( 'Eyebrow', 'pamoja' ), 'About' ),
				'title' => $t( __( 'Heading', 'pamoja' ), 'What holds the tree up.' ),
				'lede'  => $a( __( 'Lede', 'pamoja' ), 'Why we exist, our story, how we work together, and the ethics we grow from. One page, four stops.' ),
				'you'   => $t( __( '"You are reading" label', 'pamoja' ), 'You are reading' ),
			),
		),
		'about_why' => array(
			'title'  => __( 'About · Why we exist (soil)', 'pamoja' ),
			'fields' => array(
				'title'            => $t( __( 'Stop title (also in the menu)', 'pamoja' ), 'Why we exist' ),
				'body'             => $h( __( 'Opening', 'pamoja' ), '<p>Despite being one of the wealthiest countries in the world, inequities persist, especially for people whose knowledge, labour, and traditions are undervalued.</p><p>As newcomers engaging with these systems, we are rarely offered an honest account of this land and its Indigenous histories. We have witnessed gifted people welcomed for their labour yet pushed into menial jobs, while families struggle in isolation and vibrant living cultures are reduced to token gestures.</p><p>The story of multiculturalism, while noble in principle, can too easily conceal these enduring realities.</p>' ),
				'research_heading' => $t( __( 'Research — heading', 'pamoja' ), 'What research says' ),
				'research_intro'   => $a( __( 'Research — intro', 'pamoja' ), 'The opportunity we see is not only something we have experienced. Research points to a significant economic opportunity in better recognizing and mobilizing immigrant skills and entrepreneurship.' ),
				'stat1_num'        => $t( __( 'Statistic 1 — the number', 'pamoja' ), '40%+' ),
				'stat1_body'       => $a( __( 'Statistic 1 — text', 'pamoja' ), 'Immigrants are projected to account for more than 40% of Canadian entrepreneurs by 2034, up from 27% in 2014 and 34% in 2024. The share is already approaching half in Ontario and British Columbia.' ),
				'stat1_source'     => $t( __( 'Statistic 1 — source', 'pamoja' ), 'BDC, 2024' ),
				'stat1_url'        => $u( __( 'Statistic 1 — source link', 'pamoja' ), '', __( 'The source is shown as a link once this is filled in.', 'pamoja' ) ),
				'stat2_num'        => $t( __( 'Statistic 2 — the number', 'pamoja' ), 'Up to $50B' ),
				'stat2_body'       => $a( __( 'Statistic 2 — text', 'pamoja' ), 'Underutilizing immigrants’ skills and education is estimated to cost the Canadian economy as much as $50 billion in lost GDP each year.' ),
				'stat2_source'     => $t( __( 'Statistic 2 — source', 'pamoja' ), 'RBC, 2025' ),
				'stat2_url'        => $u( __( 'Statistic 2 — source link', 'pamoja' ), '' ),
				'pull'             => $a( __( 'Pull line', 'pamoja' ), 'There is enormous capacity already here. The question is whether we create the conditions for it to flourish.' ),
				'response_heading' => $t( __( 'Response — heading', 'pamoja' ), 'Pamoja exists as our response.' ),
				'response_body'    => $h( __( 'Response — text', 'pamoja' ), '<p>Our practice is rooted in complementarity and mutuality, not competition or exploitation. We use ethical dialogue, placemaking, entrepreneurship and professional development, social enterprise, and social finance, to create conditions where collective prosperity and mutual empowerment become the norm for everyone involved.</p><p>Guided by treaties and our own living traditions, we work to transform isolation into connection, and exploitation into collective empowerment.</p>' ),
				'response_link'    => $t( __( 'Response — link to the next stop', 'pamoja' ), 'Here’s what we’re working toward ↓' ),
			),
		),
		'about_story' => array(
			'title'  => __( 'About · Our story (trunk)', 'pamoja' ),
			'fields' => array(
				'title'        => $t( __( 'Stop title (also in the menu)', 'pamoja' ), 'Our story' ),
				'name_heading' => $t( __( 'Our name — heading', 'pamoja' ), 'Our name: Pamoja' ),
				'name_body'    => $h( __( 'Our name — text', 'pamoja' ), '<p>Pamoja means “Together” in Kiswahili. It is more than a word. It is our guiding principle. The name was given to us by our older brother Waleed Abdulhamid after one of his original songs named Pamoja.</p><p>Rooted in Sudan, Waleed is a professor and musician and he represents a Canadian voice singing for peace and justice not only across Canada but all over the world, with songs in English, Arabic, Swahili, Kinyarwanda, and more.</p>' ),
				'grew_heading' => $t( __( 'How we grew — heading', 'pamoja' ), 'How we grew' ),
				'grew_body'    => $h( __( 'How we grew — text', 'pamoja' ), '<p>Pamoja Cultural Collective began in 2023 when newcomer husband and wife Abdo Habbani and Fatima Shulli decided to bring a Sudanese tradition of coming together around tea into their new home in Hamilton.</p><p>With generous support from the Community Permaculture Lab, and by sharing their space with us, Pamoja began as a simple biweekly tea gathering. For more than three years, that steady rhythm of coming together nurtured relationships across cultures, generations, and experiences. Over time, those relationships grew into what is now the Pamoja Cultural Collective.</p><p>But the idea of creating spaces where people can live with dignity began much earlier.</p>' ),
				't1_when'      => $t( __( 'Timeline 1 — when', 'pamoja' ), '2018 · Sudan' ),
				't1_title'     => $t( __( 'Timeline 1 — title', 'pamoja' ), 'A vocational centre' ),
				't1_body'      => $a( __( 'Timeline 1 — text', 'pamoja' ), 'Abdo and Fatima designed a vocational centre in Sudan to respond to the barriers craftspeople faced in turning their skills into sustainable livelihoods.' ),
				't2_when'      => $t( __( 'Timeline 2 — when', 'pamoja' ), '2017–2025 · Canada' ),
				't2_title'     => $t( __( 'Timeline 2 — title', 'pamoja' ), 'The same problem, another form' ),
				't2_body'      => $a( __( 'Timeline 2 — text', 'pamoja' ), 'They repeatedly witnessed newcomers pushed into menial work while their existing skills remained unseen, and a lack of awareness about the treaties and Indigenous histories of the land they had made home.' ),
				't3_when'      => $t( __( 'Timeline 3 — when (highlighted)', 'pamoja' ), '2023 · Hamilton' ),
				't3_title'     => $t( __( 'Timeline 3 — title', 'pamoja' ), 'The tea gatherings' ),
				't3_body'      => $a( __( 'Timeline 3 — text', 'pamoja' ), 'Pamoja emerged not from a strategic plan, but from people consistently coming together around tea at the Community Permaculture Lab.' ),
				't4_when'      => $t( __( 'Timeline 4 — when', 'pamoja' ), 'Today' ),
				't4_title'     => $t( __( 'Timeline 4 — title', 'pamoja' ), 'Pamoja Cultural Collective' ),
				't4_body'      => $a( __( 'Timeline 4 — text', 'pamoja' ), 'Relationships across cultures, generations, and experiences, grown into a collective.' ),
				't5_when'      => $t( __( 'Timeline 5 — when', 'pamoja' ), 'Next' ),
				't5_title'     => $t( __( 'Timeline 5 — title', 'pamoja' ), 'A cultural hub' ),
				't5_body'      => $a( __( 'Timeline 5 — text', 'pamoja' ), 'Community-funded nonprofit work for ethical dialogue and knowledge mobilization, with a cultural social enterprise for economic empowerment.' ),
				'imagine_lead' => $a( __( 'Imagine — first line', 'pamoja' ), 'Imagine a mother working in an alienating factory while carrying generations of knowledge about cooking.' ),
				'imagine_body' => $a( __( 'Imagine — questions', 'pamoja' ), 'What if that knowledge could become part of a community kitchen? What if the skills people already carry could become sources of dignity, connection, and livelihood?' ),
				'imagine_note' => $t( __( 'Imagine — closing line', 'pamoja' ), 'This is the kind of possibility Pamoja is working toward.' ),
			),
		),
		'about_how' => array(
			'title'  => __( 'About · How we work together (roots)', 'pamoja' ),
			'fields' => array(
				'title'       => $t( __( 'Stop title (also in the menu)', 'pamoja' ), 'How we work together' ),
				'c1_tag'      => $t( __( 'Card 1 — eyebrow', 'pamoja' ), 'Within the collective' ),
				'c1_title'    => $t( __( 'Card 1 — title', 'pamoja' ), 'Consensus and consultation' ),
				'c1_body'     => $a( __( 'Card 1 — text', 'pamoja' ), 'Within our collective, decisions are made through consensus and consultation that reflect our shared values and responsibilities.' ),
				'c2_tag'      => $t( __( 'Card 2 — eyebrow', 'pamoja' ), 'In our partnerships' ),
				'c2_title'    => $t( __( 'Card 2 — title', 'pamoja' ), 'Mutual consultation and dialogue' ),
				'c2_body'     => $a( __( 'Card 2 — text', 'pamoja' ), 'In our partnerships, we practice mutual consultation and dialogue.' ),
				'terms_intro' => $h( __( 'Terms — intro', 'pamoja' ), '<p>Our way of working grows from <dfn>Ta’aruf</dfn> and <dfn>Takaful</dfn>, two traditions of our own.</p>' ),
				'taaruf'      => $a( __( 'Ta’aruf — plain-language explanation', 'pamoja' ), 'An Arabic concept of mutual and respectful acquaintance between different peoples. For us, a practice of hospitality that builds trust, protects dignity, and lets difference remain without erasure.' ),
				'takaful'     => $a( __( 'Takaful — plain-language explanation', 'pamoja' ), 'Mutual empowerment and collective stewardship. Prosperity that creates capacity for others rather than extracting from them.' ),
			),
		),
		'about_values' => array(
			'title'  => __( 'About · Ethics and values (roots)', 'pamoja' ),
			'fields' => array(
				'title'            => $t( __( 'Stop title (also in the menu)', 'pamoja' ), 'Ethics and values' ),
				'treaties_heading' => $t( __( 'Treaties — heading', 'pamoja' ), 'Treaties as models for partnership' ),
				'treaties_body'    => $h( __( 'Treaties — text', 'pamoja' ), '<p>We work with treaties not only as intellectual teachings and principles we learn about, but as embodied practice of relationship, responsibility, difference and shared future.</p><p>We understand our gatherings as sacred space, and we take responsibility for the land and its covenants, including the Two Row and Dish With One Spoon Wampum Covenants.</p><p>From the Nile River to the Grand River, we weave threads of belonging into an ancient tapestry of mutuality. Our own rituals of community have taught us to value relationship, reciprocity, and responsibility.</p><p>We do not claim these covenants as our own. We honour them as Indigenous teachings and learn from them as models of ethical partnership, while grounding ourselves in our own traditions of Ta’aruf and Takaful.</p>' ),
				'r1_num'           => $t( __( 'Root 1 — eyebrow', 'pamoja' ), 'Root 1' ),
				'r1_title'         => $t( __( 'Root 1 — name', 'pamoja' ), 'Epistemic sovereignty' ),
				'r1_guided'        => $t( __( 'Root 1 — guided by', 'pamoja' ), 'Guided by Ta’aruf and our understanding of the Two Row Covenant' ),
				'r1_essence'       => $a( __( 'Root 1 — essence (bold line)', 'pamoja' ), 'Walk side-by-side in mutual respect.' ),
				'r1_term'          => $a( __( 'Root 1 — term explained', 'pamoja' ), 'Epistemic sovereignty — our ways of knowing are ours: carried, practised, and taught on our own terms.' ),
				'r1_body'          => $h( __( 'Root 1 — text', 'pamoja' ), '<p>We honour stories, knowledge, and ways of being without assimilation or control. This resonates with our ethic of Ta’aruf, an Arabic concept of mutual and respectful acquaintance between different peoples.</p><p>For us, Ta’aruf is a practice of hospitality that builds ethical and dialogical relationships, develops trust, protects dignity, and allows difference to remain without erasure.</p>' ),
				'r2_num'           => $t( __( 'Root 2 — eyebrow', 'pamoja' ), 'Root 2' ),
				'r2_title'         => $t( __( 'Root 2 — name', 'pamoja' ), 'Economic solidarity' ),
				'r2_guided'        => $t( __( 'Root 2 — guided by', 'pamoja' ), 'Guided by Takaful and our understanding of the Dish With One Spoon Covenant' ),
				'r2_essence'       => $a( __( 'Root 2 — essence (bold line)', 'pamoja' ), "Take only what you need.\nLeave some for others.\nKeep the dish clean." ),
				'r2_term'          => $a( __( 'Root 2 — term explained', 'pamoja' ), 'Takaful — mutual empowerment and collective stewardship.' ),
				'r2_body'          => $h( __( 'Root 2 — text', 'pamoja' ), '<p>This aligns with our ethic of Takaful, mutual empowerment and collective stewardship.</p><p>We believe prosperity is strongest when it creates capacity for others rather than extracting from them.</p>' ),
				'cta'              => $t( __( 'Closing button', 'pamoja' ), 'See what this looks like in practice → Our programs' ),
			),
		),
		'engage' => array(
			'title'  => __( 'Engage · Page head', 'pamoja' ),
			'fields' => array(
				'tag'   => $t( __( 'Eyebrow', 'pamoja' ), 'Engage' ),
				'title' => $t( __( 'Heading', 'pamoja' ), 'Plant something with us.' ),
				'lede'  => $a( __( 'Lede', 'pamoja' ), 'Every gathering Pamoja has grown came from seed. Three ways in, and one first step for all of them: a conversation.' ),
			),
		),
		'engage_volunteer' => array(
			'title'  => __( 'Engage · Volunteer', 'pamoja' ),
			'fields' => array(
				'title' => $t( __( 'Section title (also in the menu)', 'pamoja' ), 'Volunteer' ),
				'for'   => $t( __( 'For whom', 'pamoja' ), 'For neighbours and organizers' ),
				'body'  => $h( __( 'Text', 'pamoja' ), '<p>Help shape, develop, and deliver the work: hosting a tea gathering, roasting for a coffee ritual, carrying chairs to a park, writing up a gathering, or lending a skill you already carry.</p><p>[Draft — wording to be confirmed by Pamoja.]</p>', __( 'A paragraph starting with "[" is a draft note and is not shown on the site.', 'pamoja' ) ),
				'cta'   => $t( __( 'Button', 'pamoja' ), 'I’d like to help' ),
			),
		),
		'engage_partner' => array(
			'title'  => __( 'Engage · Partner', 'pamoja' ),
			'intro'  => __( 'The offerings come from Pamoja → Services; the organizations from Pamoja → Partners & funders.', 'pamoja' ),
			'fields' => array(
				'title'            => $t( __( 'Section title (also in the menu)', 'pamoja' ), 'Partner' ),
				'for'              => $t( __( 'For whom', 'pamoja' ), 'For connectors, institutions and allies' ),
				'intro'            => $h( __( 'Intro', 'pamoja' ), '<p>Partners do not commission a community from us; they enter a relationship with one. Before any service, how we work together:</p>' ),
				'p1_title'         => $t( __( 'Step 1 — title', 'pamoja' ), 'Build trust first.' ),
				'p1_body'          => $a( __( 'Step 1 — text', 'pamoja' ), 'Every engagement begins with coffee or tea, stories, and understanding, before agendas.' ),
				'p2_title'         => $t( __( 'Step 2 — title', 'pamoja' ), 'Respect the process.' ),
				'p2_body'          => $a( __( 'Step 2 — text', 'pamoja' ), 'Consensus-based decisions, imbalances acknowledged openly, leadership and credit shared publicly.' ),
				'p3_title'         => $t( __( 'Step 3 — title', 'pamoja' ), 'A financial covenant.' ),
				'p3_body'          => $a( __( 'Step 3 — text', 'pamoja' ), 'Take only what you need, leave something for others, keep the dish clean.' ),
				'services_heading' => $t( __( 'Offerings — heading', 'pamoja' ), 'What we offer' ),
				'services_intro'   => $a( __( 'Offerings — intro', 'pamoja' ), 'Each of these is taught by newcomer knowledge holders and learned by the institutions who engage them. The community practises; your organization is the guest.' ),
				'partners_heading' => $t( __( 'Partners — heading', 'pamoja' ), 'Who we grow with' ),
				'partners_intro'   => $a( __( 'Partners — intro', 'pamoja' ), 'Organizations that build with Pamoja, and funders whose support made specific gatherings possible. Each is named with what they actually contributed.' ),
				'cta'              => $t( __( 'Button', 'pamoja' ), 'Open a door with us' ),
			),
		),
		'engage_support' => array(
			'title'  => __( 'Engage · Support', 'pamoja' ),
			'fields' => array(
				'title'    => $t( __( 'Section title (also in the menu)', 'pamoja' ), 'Support' ),
				'for'      => $t( __( 'For whom', 'pamoja' ), 'Space · funding · capacity' ),
				'intro'    => $h( __( 'Intro', 'pamoja' ), '<p>This is not a donation request. It is an invitation to invest in a community you belong to — to put something into the shared dish that the whole city eats from.</p>' ),
				'i1_title' => $t( __( 'Item 1 — title', 'pamoja' ), 'Space' ),
				'i1_body'  => $a( __( 'Item 1 — text', 'pamoja' ), 'Free or low-cost venues where gatherings can root — a room, a yard, a hall that sits empty on weekday evenings.' ),
				'i2_title' => $t( __( 'Item 2 — title', 'pamoja' ), 'Funding' ),
				'i2_body'  => $a( __( 'Item 2 — text', 'pamoja' ), 'Funding that trusts the people doing the work: multi-year, lightly restricted, sized to the work rather than to a funder’s calendar.' ),
				'i3_title' => $t( __( 'Item 3 — title', 'pamoja' ), 'Capacity' ),
				'i3_body'  => $a( __( 'Item 3 — text', 'pamoja' ), 'Legal, financial, and administrative strength lent from organizations that have it — for example, a legal or financial team’s time toward incorporation.' ),
				'cta'      => $t( __( 'Button', 'pamoja' ), 'Offer something' ),
			),
		),
		'contact' => array(
			'title'  => __( 'Engage · The conversation (form)', 'pamoja' ),
			'intro'  => __( 'The contact email is under Site settings.', 'pamoja' ),
			'fields' => array(
				'tag'       => $t( __( 'Eyebrow', 'pamoja' ), 'The first step' ),
				'title'     => $t( __( 'Heading', 'pamoja' ), 'We start with a conversation. And we don’t move fast.' ),
				'intro'     => $h( __( 'Intro', 'pamoja' ), '<p>Whether you’re a neighbour with something to contribute, a connector who can open a door, or someone ready to help organize, the first step is the same: sit down with us.</p>' ),
				'direct'    => $t( __( 'Line before the email address', 'pamoja' ), 'Prefer email? Write to us at' ),
				'form_note' => $h( __( 'Note above the form', 'pamoja' ), '<p><strong>This is not a booking form.</strong> Tell us who you are and what you’re imagining, and we’ll find a time to sit down together — coffee or tea first, agendas after.</p>' ),
				'submit'    => $t( __( 'Submit button', 'pamoja' ), 'Begin the conversation' ),
				'error'     => $a( __( 'Message when a submission fails', 'pamoja' ), 'Something in the form didn’t reach us — please check the required fields and try again, or write to us directly.' ),
			),
		),
		'listing' => array(
			'title'  => __( 'Other pages', 'pamoja' ),
			'intro'  => __( 'Headings for the events, blog and media listings, and the not-found page. The thank-you page is an ordinary page (Pages → The kettle is on.).', 'pamoja' ),
			'fields' => array(
				'events_tag'       => $t( __( 'Events — eyebrow', 'pamoja' ), 'Events' ),
				'events_title'     => $t( __( 'Events — heading', 'pamoja' ), 'Every gathering.' ),
				'events_intro'     => $a( __( 'Events — intro', 'pamoja' ), 'What’s coming up, what keeps going, and everything Pamoja’s community has grown so far, most recent first.' ),
				'upcoming_heading' => $t( __( 'Events — "Upcoming" heading', 'pamoja' ), 'Upcoming' ),
				'ongoing_heading'  => $t( __( 'Events — "Ongoing" heading', 'pamoja' ), 'Ongoing' ),
				'past_heading'     => $t( __( 'Events — "Past" heading', 'pamoja' ), 'Past events' ),
				'events_empty'     => $t( __( 'Events — when a list is empty', 'pamoja' ), 'Nothing here yet.' ),
				'blog_tag'         => $t( __( 'Blog — eyebrow', 'pamoja' ), 'Blog' ),
				'blog_title'       => $t( __( 'Blog — heading', 'pamoja' ), 'Stories from the work.' ),
				'blog_intro'       => $a( __( 'Blog — intro', 'pamoja' ), 'Announcements, invitations, reflections, and the stories behind our gatherings. Leaves that travel.' ),
				'blog_empty'       => $t( __( 'Blog — when empty', 'pamoja' ), 'The first story is on its way.' ),
				'media_tag'        => $t( __( 'Media — eyebrow', 'pamoja' ), 'In pictures' ),
				'media_title'      => $t( __( 'Media — heading', 'pamoja' ), 'Photos and video.' ),
				'media_intro'      => $a( __( 'Media — intro', 'pamoja' ), 'People hosting, pouring, teaching and making. Every photograph here is shared with the consent of the people in it.' ),
				'photos_heading'   => $t( __( 'Heading above a photo gallery', 'pamoja' ), 'In pictures' ),
				'consent_note'     => $a( __( 'Note under galleries', 'pamoja' ), 'Photographs are shared with the consent of the people in them.' ),
				'nf_tag'           => $t( __( 'Not found — eyebrow', 'pamoja' ), '404' ),
				'nf_title'         => $t( __( 'Not found — heading', 'pamoja' ), 'This ground is unplanted.' ),
				'nf_body'          => $h( __( 'Not found — body', 'pamoja' ), '<p>The page you were looking for has moved, or never took root. The rest of the tree is still here — start again from <a href="/">the tree</a>, or open the map in the menu.</p>' ),
			),
		),
	);

	return $schema;
}

/**
 * One saved-or-default value. Rich text comes back as HTML with paragraphs.
 */
function pamoja_home( string $section, string $key ): string {
	static $saved = null;
	if ( null === $saved ) {
		$saved = (array) get_option( PAMOJA_HOME_OPTION, array() );
	}
	$schema = pamoja_home_schema();
	$field  = $schema[ $section ]['fields'][ $key ] ?? null;
	if ( ! $field ) {
		return '';
	}
	$value = $saved[ $section ][ $key ] ?? null;
	if ( null === $value || '' === trim( (string) $value ) ) {
		$value = $field['default'];
	}
	if ( 'html' === $field['type'] ) {
		$value = pamoja_home_html_filter( wpautop( $value ) );
	}
	return (string) $value;
}

/**
 * Plain-text field, escaped.
 */
function pamoja_home_text( string $section, string $key ) {
	echo esc_html( pamoja_home( $section, $key ) );
}

/**
 * Rich-text field, sanitized (dfn allowed for the explained terms).
 */
function pamoja_home_html( string $section, string $key ) {
	$allowed        = wp_kses_allowed_html( 'post' );
	$allowed['dfn'] = array( 'title' => true );
	echo wp_kses( pamoja_home( $section, $key ), $allowed );
}

function pamoja_home_html_filter( string $html ): string {
	return pamoja_strip_draft_notes( $html );
}

/**
 * A rich-text field printed inside a heading: the same copy, minus the block
 * tags the editor wraps around it, so <em> still marks the words to pick out
 * but a stray paragraph cannot break the heading.
 */
function pamoja_home_inline( string $section, string $key ) {
	$allowed = array(
		'em'     => array(),
		'i'      => array(),
		'strong' => array(),
		'b'      => array(),
		'br'     => array(),
	);
	$html = wp_kses( pamoja_home( $section, $key ), $allowed );
	echo trim( preg_replace( '/\s+/', ' ', $html ) ); // Sanitized above.
}

/**
 * Plain paragraphs from a textarea field (line breaks kept).
 */
function pamoja_home_para( string $section, string $key, string $class = '' ) {
	$text = pamoja_home( $section, $key );
	if ( '' === $text ) {
		return;
	}
	printf( '<p%s>%s</p>', $class ? ' class="' . esc_attr( $class ) . '"' : '', nl2br( esc_html( $text ) ) );
}

/* ---------- Admin page ---------- */

function pamoja_home_register_setting() {
	register_setting(
		'pamoja_home',
		PAMOJA_HOME_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'pamoja_home_sanitize',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'pamoja_home_register_setting' );

/**
 * Only the submitted section is replaced; everything else is kept.
 */
function pamoja_home_sanitize( $input ): array {
	$existing = (array) get_option( PAMOJA_HOME_OPTION, array() );
	$schema   = pamoja_home_schema();
	foreach ( (array) $input as $section => $fields ) {
		if ( ! isset( $schema[ $section ] ) ) {
			continue;
		}
		$clean = array();
		foreach ( $schema[ $section ]['fields'] as $key => $field ) {
			$raw = isset( $fields[ $key ] ) ? (string) $fields[ $key ] : '';
			switch ( $field['type'] ) {
				case 'html':
					$allowed        = wp_kses_allowed_html( 'post' );
					$allowed['dfn'] = array( 'title' => true );
					$raw            = wp_kses( $raw, $allowed );
					break;
				case 'textarea':
					$raw = sanitize_textarea_field( $raw );
					break;
				case 'url':
					$raw = esc_url_raw( $raw );
					break;
				default:
					$raw = sanitize_text_field( $raw );
			}
			// Saving the default (or nothing) keeps the field on its default.
			if ( '' !== trim( $raw ) && trim( $raw ) !== trim( $field['default'] ) ) {
				$clean[ $key ] = $raw;
			}
		}
		$existing[ $section ] = $clean;
	}
	return $existing;
}

function pamoja_home_admin_menu() {
	$parent = function_exists( 'pamoja_register_post_types' ) ? 'pamoja' : 'themes.php';
	add_submenu_page(
		$parent,
		__( 'Site copy', 'pamoja' ),
		__( 'Site copy', 'pamoja' ),
		'edit_theme_options',
		'pamoja-homepage',
		'pamoja_home_render_page',
		1
	);
}
add_action( 'admin_menu', 'pamoja_home_admin_menu', 15 );

function pamoja_home_render_page() {
	$schema  = pamoja_home_schema();
	$current = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'hero'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $schema[ $current ] ) ) {
		$current = 'hero';
	}
	$saved   = (array) get_option( PAMOJA_HOME_OPTION, array() );
	$section = $schema[ $current ];
	?>
	<div class="wrap pamoja-home-admin">
		<h1><?php esc_html_e( 'Site copy', 'pamoja' ); ?></h1>
		<p class="description" style="max-width:62ch"><?php esc_html_e( 'Every passage on the home, About and Engage pages, section by section. Empty a field to go back to the launch wording. Stop and section titles also name the places on the tree and in the menu.', 'pamoja' ); ?></p>
		<?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved.', 'pamoja' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View the site →', 'pamoja' ); ?></a></p></div>
		<?php endif; ?>
		<nav class="nav-tab-wrapper pamoja-settings-tabs">
			<?php foreach ( $schema as $slug => $s ) : ?>
				<a class="nav-tab<?php echo $slug === $current ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=pamoja-homepage&tab=' . $slug ) ); ?>"><?php echo esc_html( $s['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<form method="post" action="options.php">
			<?php settings_fields( 'pamoja_home' ); ?>
			<div class="pamoja-settings-section" style="margin-top:20px">
				<h2><?php echo esc_html( $section['title'] ); ?></h2>
				<?php if ( ! empty( $section['intro'] ) ) : ?>
					<p class="pamoja-section-intro"><?php echo esc_html( $section['intro'] ); ?></p>
				<?php endif; ?>
				<table class="form-table" role="presentation">
					<?php foreach ( $section['fields'] as $key => $field ) : ?>
						<?php
						$name  = PAMOJA_HOME_OPTION . '[' . $current . '][' . $key . ']';
						$id    = 'pamoja-home-' . $current . '-' . $key;
						$value = $saved[ $current ][ $key ] ?? $field['default'];
						?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
							<td>
								<?php if ( 'html' === $field['type'] ) : ?>
									<?php
									wp_editor(
										$value,
										$id,
										array(
											'textarea_name' => $name,
											'textarea_rows' => max( 4, min( 14, substr_count( $value, '<p' ) * 3 + 2 ) ),
											'media_buttons' => false,
											'quicktags'     => true,
											'tinymce'       => array(
												'toolbar1'      => 'formatselect,bold,italic,link,unlink,bullist,numlist,removeformat,undo,redo',
												'toolbar2'      => '',
												'block_formats' => 'Paragraph=p;Heading 4=h4',
											),
										)
									);
									?>
								<?php elseif ( 'textarea' === $field['type'] ) : ?>
									<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
								<?php else : ?>
									<input type="<?php echo 'url' === $field['type'] ? 'url' : 'text'; ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
								<?php endif; ?>
								<?php if ( ! empty( $field['help'] ) ) : ?>
									<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button( __( 'Save this section', 'pamoja' ) ); ?>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Return to the same tab after saving.
 */
function pamoja_home_redirect_to_tab( $location ) {
	if ( isset( $_POST['option_page'] ) && 'pamoja_home' === $_POST['option_page'] && isset( $_POST[ PAMOJA_HOME_OPTION ] ) && is_array( $_POST[ PAMOJA_HOME_OPTION ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$tabs = array_keys( wp_unslash( $_POST[ PAMOJA_HOME_OPTION ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$tab  = sanitize_key( (string) reset( $tabs ) );
		if ( $tab ) {
			return add_query_arg( array( 'page' => 'pamoja-homepage', 'tab' => $tab, 'settings-updated' => 'true' ), admin_url( 'admin.php' ) );
		}
	}
	return $location;
}
add_filter( 'wp_redirect', 'pamoja_home_redirect_to_tab' );
