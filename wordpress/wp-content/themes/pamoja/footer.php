</main>

<?php
$pamoja_ack    = pamoja_land_acknowledgment();
$pamoja_email  = pamoja_contact_email_safe();
$pamoja_social = pamoja_social_links();
$pamoja_map    = pamoja_site_map();
?>
<footer class="ft" id="sitemap">
	<div class="ft-grid<?php echo $pamoja_ack ? ' has-ack' : ''; ?>">
		<div class="ft-brand">
			<a class="ft-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — <?php esc_attr_e( 'home', 'pamoja' ); ?>">
				<img src="<?php echo esc_url( PAMOJA_THEME_URI . '/assets/brand/logo-white-860.png' ); ?>" alt="Pamoja" width="860" height="493" class="ft-logo" loading="lazy" decoding="async">
			</a>
			<p class="ft-tagline"><?php echo esc_html( pamoja_setting( 'footer_tagline' ) ); ?></p>
			<p class="ft-contact"><a href="mailto:<?php echo esc_attr( $pamoja_email ); ?>"><?php echo esc_html( $pamoja_email ); ?></a></p>
			<?php if ( $pamoja_social ) : ?>
				<ul class="ft-social">
					<?php foreach ( $pamoja_social as $link ) : ?>
						<li><a href="<?php echo esc_url( $link['url'] ); ?>" rel="noopener"><?php echo esc_html( $link['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="ft-map" aria-label="<?php esc_attr_e( 'Site map', 'pamoja' ); ?>">
			<?php foreach ( $pamoja_map as $group ) : ?>
				<div class="ft-col">
					<h4><a href="<?php echo esc_url( $group['url'] ); ?>"><?php echo esc_html( $group['label'] ); ?></a></h4>
					<ul>
						<?php foreach ( $group['items'] as $item ) : ?>
							<li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</nav>

		<?php if ( $pamoja_ack ) : ?>
			<div class="land-ack">
				<h2 class="land-ack-heading"><?php esc_html_e( 'Land acknowledgment', 'pamoja' ); ?></h2>
				<?php echo $pamoja_ack; // Sanitized in pamoja_land_acknowledgment(). ?>
			</div>
		<?php endif; ?>
	</div>

	<p class="ft-line"><?php echo esc_html( pamoja_setting( 'footer_line' ) ); ?></p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
