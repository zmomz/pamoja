<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * One page, one scroll, four stops in the manager's order: why we exist
 * (soil), our story (trunk), then the two that grow from the roots — how we
 * work together, and the ethics and values under it. The tree beside the
 * text lights the stop being read.
 */

get_header();

$stops = array(
	array( 'id' => 'why-we-exist', 'label' => pamoja_home( 'about_why', 'title' ), 'part' => 'soil', 'tag' => __( 'Soil', 'pamoja' ) ),
	array( 'id' => 'our-story', 'label' => pamoja_home( 'about_story', 'title' ), 'part' => 'trunk', 'tag' => __( 'Trunk', 'pamoja' ) ),
	array( 'id' => 'how-we-work-together', 'label' => pamoja_home( 'about_how', 'title' ), 'part' => 'roots', 'tag' => __( 'Roots', 'pamoja' ) ),
	array( 'id' => 'ethics-and-values', 'label' => pamoja_home( 'about_values', 'title' ), 'part' => 'roots', 'tag' => __( 'Roots', 'pamoja' ) ),
);

pamoja_page_header(
	array(
		'tag'   => pamoja_home( 'about', 'tag' ),
		'title' => pamoja_home( 'about', 'title' ),
		'lede'  => pamoja_home( 'about', 'lede' ),
		'crop'  => 'full',
		'lit'   => 'trunk',
	)
);
pamoja_toc( $stops, 'why-we-exist' );

$stat_source = function ( string $n ) {
	$label = pamoja_home( 'about_why', "stat{$n}_source" );
	$url   = pamoja_home( 'about_why', "stat{$n}_url" );
	if ( ! $label ) {
		return;
	}
	if ( $url ) {
		printf( '<a class="src" href="%s" rel="noopener">%s</a>', esc_url( $url ), esc_html( $label ) );
	} else {
		printf( '<span class="src">%s</span>', esc_html( $label ) );
	}
};
?>
<div class="climb">
	<aside class="climb-side" aria-hidden="true">
		<?php pamoja_tree( array( 'lit' => 'soil', 'id' => 'sidetree', 'class' => 'tree-side' ) ); ?>
		<p class="you"><?php pamoja_home_text( 'about', 'you' ); ?><b id="you-label"><?php echo esc_html( $stops[0]['label'] ); ?></b></p>
	</aside>

	<div class="stops">
		<section class="stop" id="<?php echo esc_attr( $stops[0]['id'] ); ?>" data-part="soil" aria-labelledby="stop-why">
			<p class="tag"><?php echo esc_html( $stops[0]['tag'] ); ?></p>
			<h2 id="stop-why"><?php echo esc_html( $stops[0]['label'] ); ?>.</h2>
			<div class="prose"><?php pamoja_home_html( 'about_why', 'body' ); ?></div>
			<h3><?php pamoja_home_text( 'about_why', 'research_heading' ); ?></h3>
			<?php pamoja_home_para( 'about_why', 'research_intro' ); ?>
			<div class="stats">
				<?php foreach ( array( '1', '2' ) as $n ) : ?>
					<div class="stat">
						<p class="num"><?php pamoja_home_text( 'about_why', "stat{$n}_num" ); ?></p>
						<?php pamoja_home_para( 'about_why', "stat{$n}_body" ); ?>
						<p class="stat-src"><?php $stat_source( $n ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
			<?php pamoja_home_para( 'about_why', 'pull', 'pull' ); ?>
			<h3><?php pamoja_home_text( 'about_why', 'response_heading' ); ?></h3>
			<div class="prose"><?php pamoja_home_html( 'about_why', 'response_body' ); ?></div>
			<p><a class="lnk" href="#our-story"><?php pamoja_home_text( 'about_why', 'response_link' ); ?></a></p>
		</section>

		<section class="stop" id="<?php echo esc_attr( $stops[1]['id'] ); ?>" data-part="trunk" aria-labelledby="stop-story">
			<p class="tag"><?php echo esc_html( $stops[1]['tag'] ); ?></p>
			<h2 id="stop-story"><?php echo esc_html( $stops[1]['label'] ); ?>.</h2>
			<h3><?php pamoja_home_text( 'about_story', 'name_heading' ); ?></h3>
			<div class="beside">
				<div class="prose"><?php pamoja_home_html( 'about_story', 'name_body' ); ?></div>
				<?php pamoja_story_figure( 'name_image', 'song_caption', 'song' ); ?>
			</div>
			<h3><?php pamoja_home_text( 'about_story', 'grew_heading' ); ?></h3>
			<div class="beside beside--flip">
				<div class="prose"><?php pamoja_home_html( 'about_story', 'grew_body' ); ?></div>
				<?php pamoja_story_figure( 'grew_image', 'grew_caption' ); ?>
			</div>
			<ol class="tl" aria-label="<?php esc_attr_e( 'How Pamoja came to be', 'pamoja' ); ?>">
				<?php foreach ( array( 1, 2, 3, 4, 5 ) as $n ) : ?>
					<li<?php echo 3 === $n ? ' class="now"' : ''; ?>>
						<b><?php pamoja_home_text( 'about_story', "t{$n}_when" ); ?></b>
						<h4><?php pamoja_home_text( 'about_story', "t{$n}_title" ); ?></h4>
						<?php pamoja_home_para( 'about_story', "t{$n}_body" ); ?>
					</li>
				<?php endforeach; ?>
			</ol>
			<div class="imagine">
				<?php pamoja_home_para( 'about_story', 'imagine_lead' ); ?>
				<?php pamoja_home_para( 'about_story', 'imagine_body' ); ?>
				<?php pamoja_home_para( 'about_story', 'imagine_note', 'small' ); ?>
			</div>
		</section>

		<section class="stop" id="<?php echo esc_attr( $stops[2]['id'] ); ?>" data-part="roots" aria-labelledby="stop-how">
			<p class="tag"><?php echo esc_html( $stops[2]['tag'] ); ?></p>
			<h2 id="stop-how"><?php echo esc_html( $stops[2]['label'] ); ?>.</h2>
			<div class="how2">
				<?php foreach ( array( 1, 2 ) as $n ) : ?>
					<div class="card">
						<span class="num"><?php pamoja_home_text( 'about_how', "c{$n}_tag" ); ?></span>
						<h3><?php pamoja_home_text( 'about_how', "c{$n}_title" ); ?></h3>
						<?php pamoja_home_para( 'about_how', "c{$n}_body" ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="prose terms-intro"><?php pamoja_home_html( 'about_how', 'terms_intro' ); ?></div>
			<div class="term"><b>Ta’aruf</b> — <?php pamoja_home_text( 'about_how', 'taaruf' ); ?></div>
			<div class="term"><b>Takaful</b> — <?php pamoja_home_text( 'about_how', 'takaful' ); ?></div>
		</section>

		<section class="stop" id="<?php echo esc_attr( $stops[3]['id'] ); ?>" data-part="roots" aria-labelledby="stop-values">
			<p class="tag"><?php echo esc_html( $stops[3]['tag'] ); ?></p>
			<h2 id="stop-values"><?php echo esc_html( $stops[3]['label'] ); ?>.</h2>
			<h3><?php pamoja_home_text( 'about_values', 'treaties_heading' ); ?></h3>
			<div class="prose"><?php pamoja_home_html( 'about_values', 'treaties_body' ); ?></div>
			<div class="cards2">
				<?php foreach ( array( 1 => 'two-row', 2 => 'dish' ) as $n => $glyph ) : ?>
					<article class="card root">
						<?php pamoja_glyph( $glyph ); ?>
						<span class="num"><?php pamoja_home_text( 'about_values', "r{$n}_num" ); ?></span>
						<h3><?php pamoja_home_text( 'about_values', "r{$n}_title" ); ?></h3>
						<p class="guided"><?php pamoja_home_text( 'about_values', "r{$n}_guided" ); ?></p>
						<?php pamoja_home_para( 'about_values', "r{$n}_essence", 'essence' ); ?>
						<?php if ( pamoja_home( 'about_values', "r{$n}_term" ) ) : ?><div class="term"><?php pamoja_home_text( 'about_values', "r{$n}_term" ); ?></div><?php endif; ?>
						<div class="prose"><?php pamoja_home_html( 'about_values', "r{$n}_body" ); ?></div>
					</article>
				<?php endforeach; ?>
			</div>
			<p class="stop-cta"><a class="btn" href="<?php echo esc_url( pamoja_events_url() ); ?>"><?php pamoja_home_text( 'about_values', 'cta' ); ?></a></p>
		</section>
	</div>
</div>
<?php
get_footer();
