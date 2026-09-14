<?php
/**
 * Events: the canopy up close. Upcoming first (the fruit), then anything
 * ongoing, then everything grown so far.
 */

get_header();

$upcoming = pamoja_upcoming_events();
$ongoing  = pamoja_ongoing_events();
$past     = pamoja_past_events();

$stops = array( array( 'id' => 'upcoming', 'label' => pamoja_home( 'listing', 'upcoming_heading' ) ) );
if ( $ongoing ) {
	$stops[] = array( 'id' => 'ongoing', 'label' => pamoja_home( 'listing', 'ongoing_heading' ) );
}
$stops[] = array( 'id' => 'past', 'label' => pamoja_home( 'listing', 'past_heading' ) );

pamoja_page_header(
	array(
		'tag'    => pamoja_home( 'listing', 'events_tag' ),
		'title'  => pamoja_home( 'listing', 'events_title' ),
		'lede'   => pamoja_home( 'listing', 'events_intro' ),
		'crop'   => 'canopy',
		'lit'    => 'canopy',
		'fruits' => true,
		'spots'  => array(
			array( 'label' => pamoja_home( 'listing', 'upcoming_heading' ), 'url' => '#upcoming', 'part' => 'canopy', 'x' => 60, 'y' => 30, 'kind' => 'fruit' ),
			array( 'label' => pamoja_home( 'listing', 'past_heading' ), 'url' => '#past', 'part' => 'canopy', 'x' => 34, 'y' => 64, 'left' => true ),
		),
	)
);
pamoja_toc( $stops );
?>
<div class="wrap events-page">
	<section class="events-section" id="upcoming" aria-labelledby="events-upcoming">
		<h2 id="events-upcoming"><?php pamoja_home_text( 'listing', 'upcoming_heading' ); ?></h2>
		<?php if ( $upcoming ) : ?>
			<div class="up">
				<?php foreach ( $upcoming as $event ) : ?>
					<?php get_template_part( 'template-parts/cards/event-row', null, array( 'event' => $event ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="lede"><?php pamoja_home_text( 'coming', 'empty' ); ?> <a class="lnk" href="<?php echo esc_url( pamoja_conversation_url() ); ?>"><?php pamoja_home_text( 'hero', 'cta' ); ?> →</a></p>
		<?php endif; ?>
	</section>

	<?php if ( $ongoing ) : ?>
		<section class="events-section" id="ongoing" aria-labelledby="events-ongoing">
			<h2 id="events-ongoing"><?php pamoja_home_text( 'listing', 'ongoing_heading' ); ?></h2>
			<?php pamoja_tiles( $ongoing, 'strip strip--events' ); ?>
		</section>
	<?php endif; ?>

	<section class="events-section" id="past" aria-labelledby="events-past">
		<h2 id="events-past"><?php pamoja_home_text( 'listing', 'past_heading' ); ?></h2>
		<?php if ( $past ) : ?>
			<?php pamoja_tiles( $past, 'strip strip--events' ); ?>
		<?php else : ?>
			<p class="lede"><?php pamoja_home_text( 'listing', 'events_empty' ); ?></p>
		<?php endif; ?>
	</section>
</div>
<?php
get_footer();
