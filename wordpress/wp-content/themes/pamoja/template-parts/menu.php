<?php
/**
 * The tree menu: the whole map beside the four lists. Opened from the Map
 * button, by hovering a header link, or by the hamburger on small screens.
 * Without JavaScript it is simply the last thing on the page (see footer.php
 * for the plain sitemap that always renders).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$map  = pamoja_site_map();
$door = pamoja_current_door();
?>
<div class="veil" id="veil" hidden></div>
<div class="menu" id="menu" aria-label="<?php esc_attr_e( 'Site map', 'pamoja' ); ?>" hidden>
	<p class="menu-lead"><?php esc_html_e( 'Where would you like to go?', 'pamoja' ); ?></p>
	<div class="menu-map">
		<div class="stage">
			<?php pamoja_tree( array( 'lit' => 'all', 'id' => 'menutree' ) ); ?>
			<?php pamoja_tree_hotspots( false ); ?>
		</div>
	</div>
	<div class="menu-cols">
		<?php foreach ( $map as $key => $group ) : ?>
			<div class="menu-col">
				<span class="menu-part"><?php echo esc_html( $group['tree'] ); ?></span>
				<h4><a href="<?php echo esc_url( $group['url'] ); ?>" data-part="<?php echo esc_attr( $group['part'] ); ?>"><?php echo esc_html( $group['label'] ); ?></a></h4>
				<ul>
					<?php foreach ( $group['items'] as $item ) : ?>
						<li><a href="<?php echo esc_url( $item['url'] ); ?>" data-part="<?php echo esc_attr( $item['part'] ); ?>"><?php echo esc_html( $item['label'] ); ?><?php if ( ! empty( $item['desc'] ) ) : ?><small><?php echo esc_html( $item['desc'] ); ?></small><?php endif; ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php if ( 'engage' === $key ) : ?>
					<a class="btn menu-cta" href="<?php echo esc_url( pamoja_conversation_url() ); ?>"><?php pamoja_home_text( 'hero', 'cta' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="menu-foot">
		<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <?php esc_html_e( 'Hamilton, Ontario', 'pamoja' ); ?></span>
		<a href="mailto:<?php echo esc_attr( pamoja_contact_email_safe() ); ?>"><?php echo esc_html( pamoja_contact_email_safe() ); ?></a>
	</div>
</div>
