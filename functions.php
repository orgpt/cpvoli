<?php
/**
 * Theme bootstrap.
 *
 * @package ConfrontaPrezziVoli
 */

define( 'CPV_THEME_VERSION', '1.0.0' );
define( 'CPV_THEME_PATH', get_template_directory() );
define( 'CPV_THEME_URL', get_template_directory_uri() );
define( 'CPV_PROJECT_ROOT', dirname( CPV_THEME_PATH, 4 ) );

require_once CPV_THEME_PATH . '/inc/class-cpv-data.php';
require_once CPV_THEME_PATH . '/inc/class-cpv-router.php';
require_once CPV_THEME_PATH . '/inc/class-cpv-seo.php';

function cpv_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'confrontaprezzivoli' ),
			'footer'  => __( 'Footer Menu', 'confrontaprezzivoli' ),
		)
	);
}
add_action( 'after_setup_theme', 'cpv_setup' );

function cpv_enqueue_assets(): void {
	wp_enqueue_style( 'cpv-style', CPV_THEME_URL . '/assets/css/main.css', array(), CPV_THEME_VERSION );
	wp_enqueue_script( 'cpv-script', CPV_THEME_URL . '/assets/js/theme.js', array(), CPV_THEME_VERSION, true );

	wp_localize_script(
		'cpv-script',
		'cpvTheme',
		array(
			'dateFormat' => 'dd/mm/yyyy',
			'currency'   => 'EUR',
			'locale'     => 'it-IT',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cpv_enqueue_assets' );

function cpv_body_classes( array $classes ): array {
	if ( cpv_is_dynamic_page() ) {
		$classes[] = 'cpv-dynamic-page';
	}

	return $classes;
}
add_filter( 'body_class', 'cpv_body_classes' );

function cpv_is_dynamic_page(): bool {
	return (bool) ( get_query_var( 'cpv_airport' ) || get_query_var( 'cpv_route' ) || get_query_var( 'cpv_city' ) || get_query_var( 'cpv_airline' ) || get_query_var( 'cpv_compare' ) );
}

function cpv_template_include( string $template ): string {
	if ( get_query_var( 'cpv_airport' ) ) {
		return CPV_THEME_PATH . '/template-airport.php';
	}

	if ( get_query_var( 'cpv_route' ) ) {
		return CPV_THEME_PATH . '/template-route.php';
	}

	if ( get_query_var( 'cpv_city' ) ) {
		return CPV_THEME_PATH . '/template-city.php';
	}

	if ( get_query_var( 'cpv_airline' ) ) {
		return CPV_THEME_PATH . '/template-airline.php';
	}

	if ( get_query_var( 'cpv_compare' ) ) {
		return CPV_THEME_PATH . '/template-comparison.php';
	}

	return $template;
}
add_filter( 'template_include', 'cpv_template_include' );

