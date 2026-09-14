<?php
/**
 * Template Name: Engage
 * Template Post Type: page
 *
 * Three ways in — volunteer, partner, support — and one conversation.
 */

get_header();

$stops = array(
	array( 'id' => 'volunteer', 'label' => pamoja_home( 'engage_volunteer', 'title' ) ),
	array( 'id' => 'partner', 'label' => pamoja_home( 'engage_partner', 'title' ) ),
	array( 'id' => 'support', 'label' => pamoja_home( 'engage_support', 'title' ) ),
	array( 'id' => 'conversation', 'label' => pamoja_home( 'hero', 'cta' ) ),
);

pamoja_page_header(
	array(
		'tag'   => pamoja_home( 'engage', 'tag' ),
		'title' => pamoja_home( 'engage', 'title' ),
		'lede'  => pamoja_home( 'engage', 'lede' ),
		'crop'  => 'seeds',
		'lit'   => 'seeds',
	)
);
pamoja_toc( $stops );

$services = pamoja_services();
$partners = pamoja_partners();
?>
<div class="ways">
	<section class="way" id="volunteer" aria-labelledby="way-volunteer">
		<div class="way-head"><h2 id="way-volunteer"><?php pamoja_home_text( 'engage_volunteer', 'title' ); ?></h2><p class="for"><?php pamoja_home_text( 'engage_volunteer', 'for' ); ?></p></div>
		<div class="way-body">
			<div class="prose"><?php pamoja_home_html( 'engage_volunteer', 'body' ); ?></div>
			<a class="btn" href="#conversation" data-role="organizer"><?php pamoja_home_text( 'engage_volunteer', 'cta' ); ?></a>
		</div>
	</section>

	<section class="way" id="partner" aria-labelledby="way-partner">
		<div class="way-head"><h2 id="way-partner"><?php pamoja_home_text( 'engage_partner', 'title' ); ?></h2><p class="for"><?php pamoja_home_text( 'engage_partner', 'for' ); ?></p></div>
		<div class="way-body">
			<div class="prose"><?php pamoja_home_html( 'engage_partner', 'intro' ); ?></div>
			<ol class="protocol">
				<?php foreach ( array( 1, 2, 3 ) as $n ) : ?>
					<li><b><?php pamoja_home_text( 'engage_partner', "p{$n}_title" ); ?></b> <?php pamoja_home_text( 'engage_partner', "p{$n}_body" ); ?></li>
				<?php endforeach; ?>
			</ol>

			<?php if ( $services ) : ?>
				<h3><?php pamoja_home_text( 'engage_partner', 'services_heading' ); ?></h3>
				<?php pamoja_home_para( 'engage_partner', 'services_intro', 'muted' ); ?>
				<div class="services">
					<?php foreach ( $services as $service ) : ?>
						<?php
						$need = (array) pamoja_meta( $service->ID, 'need_from_you', array() );
						$not  = (array) pamoja_meta( $service->ID, 'what_this_is_not', array() );
						?>
						<details class="service">
							<summary><span class="service-title"><?php echo esc_html( get_the_title( $service ) ); ?></span><span class="service-summary"><?php echo esc_html( (string) pamoja_meta( $service->ID, 'summary' ) ); ?></span></summary>
							<div class="service-body">
								<?php if ( pamoja_meta( $service->ID, 'what_it_is' ) ) : ?><h4><?php esc_html_e( 'What it is', 'pamoja' ); ?></h4><p><?php echo esc_html( (string) pamoja_meta( $service->ID, 'what_it_is' ) ); ?></p><?php endif; ?>
								<?php if ( pamoja_service_html( $service->ID, 'what_happens' ) ) : ?><h4><?php esc_html_e( 'What actually happens', 'pamoja' ); ?></h4><?php echo pamoja_service_html( $service->ID, 'what_happens' ); // Sanitized. ?><?php endif; ?>
								<?php if ( pamoja_service_html( $service->ID, 'what_to_expect' ) ) : ?><h4><?php esc_html_e( 'What to expect', 'pamoja' ); ?></h4><?php echo pamoja_service_html( $service->ID, 'what_to_expect' ); // Sanitized. ?><?php endif; ?>
								<?php if ( $need ) : ?><h4><?php esc_html_e( 'What we need from you', 'pamoja' ); ?></h4><ul><?php foreach ( $need as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul><?php endif; ?>
								<?php if ( pamoja_service_html( $service->ID, 'protocols' ) ) : ?><h4><?php esc_html_e( 'Cultural protocols', 'pamoja' ); ?></h4><?php echo pamoja_service_html( $service->ID, 'protocols' ); // Sanitized. ?><?php endif; ?>
								<?php if ( $not ) : ?><h4><?php esc_html_e( 'What this is not', 'pamoja' ); ?></h4><ul><?php foreach ( $not as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul><?php endif; ?>
								<?php if ( pamoja_meta( $service->ID, 'lead_time' ) ) : ?><h4><?php esc_html_e( 'Time and lead time', 'pamoja' ); ?></h4><p><?php echo esc_html( (string) pamoja_meta( $service->ID, 'lead_time' ) ); ?></p><?php endif; ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $partners ) : ?>
				<h3><?php pamoja_home_text( 'engage_partner', 'partners_heading' ); ?></h3>
				<?php pamoja_home_para( 'engage_partner', 'partners_intro', 'muted' ); ?>
				<ul class="partners">
					<?php foreach ( $partners as $partner ) : ?>
						<?php $url = (string) pamoja_meta( $partner->ID, 'url' ); ?>
						<li>
							<b><?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>" rel="noopener"><?php echo esc_html( get_the_title( $partner ) ); ?></a><?php else : ?><?php echo esc_html( get_the_title( $partner ) ); ?><?php endif; ?></b>
							<span><?php echo esc_html( pamoja_partner_contribution( $partner ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<a class="btn" href="#conversation" data-role="connector"><?php pamoja_home_text( 'engage_partner', 'cta' ); ?></a>
		</div>
	</section>

	<section class="way" id="support" aria-labelledby="way-support">
		<div class="way-head"><h2 id="way-support"><?php pamoja_home_text( 'engage_support', 'title' ); ?></h2><p class="for"><?php pamoja_home_text( 'engage_support', 'for' ); ?></p></div>
		<div class="way-body">
			<div class="prose"><?php pamoja_home_html( 'engage_support', 'intro' ); ?></div>
			<ul class="asks">
				<?php foreach ( array( 1, 2, 3 ) as $n ) : ?>
					<li><b><?php pamoja_home_text( 'engage_support', "i{$n}_title" ); ?></b> <?php pamoja_home_text( 'engage_support', "i{$n}_body" ); ?></li>
				<?php endforeach; ?>
			</ul>
			<a class="btn" href="#conversation" data-role="connector"><?php pamoja_home_text( 'engage_support', 'cta' ); ?></a>
		</div>
	</section>
</div>

<section class="contact" id="conversation" aria-labelledby="contact-title">
	<div class="contact-inner">
		<div class="contact-copy">
			<p class="tag"><?php pamoja_home_text( 'contact', 'tag' ); ?></p>
			<h2 id="contact-title"><?php pamoja_home_text( 'contact', 'title' ); ?></h2>
			<div class="prose"><?php pamoja_home_html( 'contact', 'intro' ); ?></div>
			<p class="direct"><?php pamoja_home_text( 'contact', 'direct' ); ?> <a href="mailto:<?php echo esc_attr( pamoja_contact_email_safe() ); ?>"><?php echo esc_html( pamoja_contact_email_safe() ); ?></a></p>
		</div>
		<?php get_template_part( 'template-parts/inquiry-form' ); ?>
	</div>
</section>
<?php
get_footer();
