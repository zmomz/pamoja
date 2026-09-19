<?php
/**
 * Home: the tree as the map, then a short front door — who we are, what
 * we've grown, what's coming up, and the seeds.
 */

get_header();

$featured = pamoja_featured_events( 4 );
$upcoming = pamoja_upcoming_events();
$map      = pamoja_site_map();
?>

<section class="hero" aria-labelledby="hero-title">
	<div class="hero-copy">
		<p class="tag"><?php pamoja_home_text( 'hero', 'tag' ); ?></p>
		<h1 id="hero-title"><?php pamoja_home_inline( 'hero', 'title' ); ?></h1>
		<?php if ( '' !== pamoja_home( 'hero', 'lead' ) ) : ?>
			<div class="lead"><?php pamoja_home_html( 'hero', 'lead' ); ?></div>
		<?php endif; ?>
		<div class="hero-btns">
			<a class="btn" href="<?php echo esc_url( pamoja_conversation_url() ); ?>"><?php pamoja_home_text( 'hero', 'cta' ); ?></a>
			<a class="lnk" href="#coming"><?php pamoja_home_text( 'hero', 'cta2' ); ?></a>
		</div>
		<p class="hint"><?php pamoja_home_text( 'hero', 'hint' ); ?></p>
	</div>
	<div class="stage" id="hero-stage">
		<?php pamoja_tree( array( 'lit' => 'all', 'name' => 'tree', 'class' => 'tree-hero', 'label' => __( 'The Pamoja tree: a map of this site. Its parts lead to the blog, the events, our story, how we work together, our ethics and values, and why we exist.', 'pamoja' ) ) ); ?>
		<?php pamoja_tree_fruits(); ?>
		<?php pamoja_tree_hotspots( true ); ?>
	</div>
</section>

<nav class="doors" aria-label="<?php esc_attr_e( 'The four doors', 'pamoja' ); ?>">
	<?php
	$door_text = array(
		'about'  => __( 'Why we exist, our story, how we work, our values', 'pamoja' ),
		'events' => count( $upcoming ) ? sprintf( __( '%s, and everything so far', 'pamoja' ), ucfirst( pamoja_upcoming_count_label( count( $upcoming ) ) ) ) : __( 'Everything we have grown so far', 'pamoja' ),
		'blog'   => __( 'Stories from the work', 'pamoja' ),
		'engage' => __( 'Volunteer, partner, or support', 'pamoja' ),
	);
	$door_go   = array(
		'about'  => __( 'Read →', 'pamoja' ),
		'events' => __( 'See what’s on →', 'pamoja' ),
		'blog'   => __( 'Read →', 'pamoja' ),
		'engage' => __( 'Plant something →', 'pamoja' ),
	);
	foreach ( $map as $key => $door ) :
		?>
		<a class="door" href="<?php echo esc_url( $door['url'] ); ?>"><b><?php echo esc_html( $door['label'] ); ?></b><span><?php echo esc_html( $door_text[ $key ] ?? '' ); ?></span><i><?php echo esc_html( $door_go[ $key ] ?? __( 'Open →', 'pamoja' ) ); ?></i></a>
	<?php endforeach; ?>
</nav>

<section class="sec cream" aria-labelledby="who-title">
	<div class="wrap">
		<p class="tag"><?php pamoja_home_text( 'who', 'tag' ); ?></p>
		<h2 id="who-title"><?php pamoja_home_text( 'who', 'title' ); ?></h2>
		<div class="groups">
			<?php foreach ( array( 1, 2, 3 ) as $n ) : ?>
				<div class="group">
					<h3><?php pamoja_home_text( 'who', "g{$n}_title" ); ?></h3>
					<?php pamoja_home_lines( 'who', "g{$n}_body" ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( $featured ) : ?>
<section class="sec" id="grown" aria-labelledby="grown-title">
	<div class="wrap">
		<p class="tag"><?php pamoja_home_text( 'grown', 'tag' ); ?></p>
		<h2 id="grown-title"><?php pamoja_home_text( 'grown', 'title' ); ?></h2>
		<div class="lede prose"><?php pamoja_home_html( 'grown', 'intro' ); ?></div>
		<?php pamoja_tiles( $featured ); ?>
		<p class="sec-more"><a class="lnk" href="<?php echo esc_url( pamoja_events_url() ); ?>"><?php pamoja_home_text( 'grown', 'all' ); ?></a></p>
	</div>
</section>
<?php endif; ?>

<section class="sec cream" id="coming" aria-labelledby="coming-title">
	<div class="wrap">
		<p class="tag"><?php pamoja_home_text( 'coming', 'tag' ); ?></p>
		<h2 id="coming-title"><?php pamoja_home_text( 'coming', 'title' ); ?></h2>
		<?php if ( $upcoming ) : ?>
			<div class="up">
				<?php foreach ( $upcoming as $event ) : ?>
					<?php get_template_part( 'template-parts/cards/event-row', null, array( 'event' => $event ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="lede"><?php pamoja_home_text( 'coming', 'empty' ); ?> <a class="lnk" href="<?php echo esc_url( pamoja_conversation_url() ); ?>"><?php pamoja_home_text( 'hero', 'cta' ); ?> →</a></p>
		<?php endif; ?>
	</div>
</section>

<section class="sec" aria-labelledby="seeds-title">
	<div class="wrap">
		<p class="tag"><?php pamoja_home_text( 'seeds', 'tag' ); ?></p>
		<h2 id="seeds-title"><?php pamoja_home_text( 'seeds', 'title' ); ?></h2>
		<div class="lede prose"><?php pamoja_home_html( 'seeds', 'intro' ); ?></div>
		<div class="seeds">
			<?php foreach ( array( 1 => 'volunteer', 2 => 'partner', 3 => 'support' ) as $n => $id ) : ?>
				<?php $section = 'engage_' . $id; ?>
				<a class="seed" href="<?php echo esc_url( pamoja_site_map_item( 'engage', $id )['url'] ?? pamoja_engage_url() ); ?>"><b><?php pamoja_home_text( $section, 'title' ); ?></b><span><?php pamoja_home_text( 'seeds', "s{$n}_body" ); ?></span><i><?php pamoja_home_text( 'seeds', "s{$n}_link" ); ?></i></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_footer();
