<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'pamoja' ); ?></a>

<?php
$pamoja_door = pamoja_current_door();
$pamoja_part = pamoja_current_part();
$pamoja_map  = pamoja_site_map();
?>
<header class="hd" id="hd">
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — <?php esc_attr_e( 'home', 'pamoja' ); ?>">
		<img src="<?php echo esc_url( PAMOJA_THEME_URI . '/assets/brand/logo-karkadeh-860.png' ); ?>" alt="Pamoja" width="860" height="493">
	</a>

	<nav class="nav" aria-label="<?php esc_attr_e( 'Primary', 'pamoja' ); ?>">
		<?php foreach ( $pamoja_map as $key => $door ) : ?>
			<a class="nav-link<?php echo $key === $pamoja_door ? ' on' : ''; ?>" href="<?php echo esc_url( $door['url'] ); ?>" data-door="<?php echo esc_attr( $key ); ?>" data-part="<?php echo esc_attr( $door['part'] ); ?>"<?php echo $key === $pamoja_door ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $door['label'] ); ?></a>
		<?php endforeach; ?>
		<button class="mapbtn" id="mapbtn" type="button" aria-expanded="false" aria-controls="menu">
			<?php pamoja_tree( array( 'lit' => $pamoja_part, 'id' => 'minitree', 'class' => 'tree-mini', 'name' => 'minitree' ) ); ?>
			<span class="mapbtn-label" data-open="<?php esc_attr_e( 'Close', 'pamoja' ); ?>" data-closed="<?php esc_attr_e( 'Map', 'pamoja' ); ?>"><?php esc_html_e( 'Map', 'pamoja' ); ?></span>
		</button>
		<a class="go" href="<?php echo esc_url( pamoja_conversation_url() ); ?>"><?php pamoja_home_text( 'hero', 'cta' ); ?></a>
	</nav>

	<button class="burger" id="burger" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'pamoja' ); ?>" aria-expanded="false" aria-controls="menu"><i aria-hidden="true"></i></button>
</header>

<?php get_template_part( 'template-parts/menu' ); ?>

<main id="main">
