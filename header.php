<?php
/**
 * Theme header.
 *
 * @package ConfrontaPrezziVoli
 */

$header_airport = CPV_Data::get_airport_by_code( 'FCO' );
$header_city    = CPV_Data::get_city_by_slug( 'londra' );
$header_airline = CPV_Data::get_airline_by_slug( 'ryanair' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<div class="shell site-header__inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="brand__badge">CPV</span>
			<span>
				<strong>ConfrontaPrezziVoli</strong>
				<small>Voli dall'Italia, senza rumore</small>
			</span>
		</a>
		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Navigazione principale', 'confrontaprezzivoli' ); ?>">
			<a href="<?php echo esc_url( $header_airport ? home_url( '/voli-da/' . $header_airport['slug'] . '/' ) : home_url( '/' ) ); ?>">Voli da aeroporti</a>
			<a href="<?php echo esc_url( $header_city ? home_url( '/voli-per/' . $header_city['slug'] . '/' ) : home_url( '/' ) ); ?>">Voli per citta</a>
			<a href="<?php echo esc_url( $header_airline ? home_url( '/compagnie-aeree/' . $header_airline['slug'] . '/' ) : home_url( '/' ) ); ?>">Compagnie</a>
			<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Guide</a>
		</nav>
	</div>
</header>
<?php cpv_render_travelpayouts_widget(); ?>
<main class="site-main">
