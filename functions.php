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

function cpv_get_search_widget_src(): string {
	$params = array(
		'currency'         => 'eur',
		'campaign_id'      => '100',
		'promo_id'         => '7879',
		'plain'            => 'true',
		'border_radius'    => '0',
		'color_focused'    => '#32a8dd',
		'special'          => '#C4C4C4',
		'secondary'        => '#eef4fb',
		'light'            => '#FFFFFF',
		'dark'             => '#0B2447',
		'color_icons'      => '#ff7a45',
		'color_button'     => '#ff7a45',
		'primary_override' => '#0b2447',
		'searchUrl'        => 'app.confrontaprezzivoli.it/flights',
		'locale'           => 'it',
		'powered_by'       => 'false',
		'show_hotels'      => 'false',
		'shmarker'         => '146401',
		'trs'              => '27252',
	);

	return 'https://tpembd.com/content?' . http_build_query( $params );
}

function cpv_render_search_widget(): void {
	$widget_src   = cpv_get_search_widget_src();
	$is_homepage  = is_front_page();
	$widget_class = $is_homepage ? 'travel-widget travel-widget--home' : 'travel-widget travel-widget--inner';
	?>
	<section class="<?php echo esc_attr( $widget_class ); ?>">
		<div class="shell">
			<?php if ( $is_homepage ) : ?>
				<div class="travel-widget__hero-copy">
					<p class="eyebrow">Ricerca voli</p>
					<h1>Le migliori offerte voli dagli aeroporti italiani, verso qualsiasi destinazione.</h1>
					<p class="travel-widget__lede">Motore flight-only pensato per il mercato italiano, con confronto prezzi, rotte low cost e un’esperienza mobile-first ispirata ai grandi comparatori.</p>
				</div>
			<?php endif; ?>
			<div class="travel-widget__frame">
				<script async src="<?php echo esc_url( $widget_src ); ?>" charset="utf-8"></script>
			</div>
		</div>
	</section>
	<?php
}

function cpv_get_promo_location_code( array $location ): string {
	if ( 'airport' === ( $location['entity_type'] ?? '' ) && ! empty( $location['city_code'] ) ) {
		return strtoupper( (string) $location['city_code'] );
	}

	return strtoupper( (string) ( $location['code'] ?? '' ) );
}

function cpv_get_promo_calendar_context(): array {
	$fallback_origin = array(
		'code'         => 'MIL',
		'display_name' => 'Milano',
		'entity_type'  => 'city',
	);
	$fallback_destination = array(
		'code'         => 'PAR',
		'display_name' => 'Parigi',
		'entity_type'  => 'city',
	);

	if ( get_query_var( 'cpv_route' ) ) {
		$route_parts = explode( '__to__', (string) get_query_var( 'cpv_route' ) );
		$origin      = CPV_Data::get_location_by_slug( $route_parts[0] ?? '' );
		$destination = CPV_Data::get_location_by_slug( $route_parts[1] ?? '' );

		if ( $origin && $destination ) {
			return array(
				'origin_code'        => cpv_get_promo_location_code( $origin ),
				'destination_code'   => cpv_get_promo_location_code( $destination ),
				'origin_label'       => $origin['display_name'],
				'destination_label'  => $destination['display_name'],
				'headline'           => 'Calendario promozionale',
				'description'        => 'Migliori offerte da ' . $origin['display_name'] . ' a ' . $destination['display_name'] . ' con focus sui prossimi mesi piu convenienti.',
			);
		}
	}

	if ( get_query_var( 'cpv_airport' ) ) {
		$airport       = CPV_Data::get_airport_by_slug( (string) get_query_var( 'cpv_airport' ) );
		$destinations  = $airport ? CPV_Data::get_airport_destinations( $airport ) : array();
		$destination   = $destinations[0]['destination'] ?? null;

		if ( $airport && $destination ) {
			return array(
				'origin_code'       => cpv_get_promo_location_code( array_merge( $airport, array( 'entity_type' => 'airport' ) ) ),
				'destination_code'  => cpv_get_promo_location_code( array_merge( $destination, array( 'entity_type' => 'city' ) ) ),
				'origin_label'      => $airport['display_name'],
				'destination_label' => $destination['display_name'],
				'headline'          => 'Calendario promozionale',
				'description'       => 'Offerte consigliate in partenza da ' . $airport['display_name'] . ' verso ' . $destination['display_name'] . '.',
			);
		}
	}

	if ( get_query_var( 'cpv_city' ) ) {
		$city  = CPV_Data::get_city_by_slug( (string) get_query_var( 'cpv_city' ) );
		$deals = $city ? CPV_Data::get_city_deals( $city ) : array();
		$origin = $deals[0]['origin'] ?? null;

		if ( $city && $origin ) {
			return array(
				'origin_code'       => cpv_get_promo_location_code( array_merge( $origin, array( 'entity_type' => 'airport' ) ) ),
				'destination_code'  => cpv_get_promo_location_code( array_merge( $city, array( 'entity_type' => 'city' ) ) ),
				'origin_label'      => $origin['display_name'],
				'destination_label' => $city['display_name'],
				'headline'          => 'Calendario promozionale',
				'description'       => 'Date piu convenienti per volare da ' . $origin['display_name'] . ' a ' . $city['display_name'] . '.',
			);
		}
	}

	return array(
		'origin_code'       => $fallback_origin['code'],
		'destination_code'  => $fallback_destination['code'],
		'origin_label'      => $fallback_origin['display_name'],
		'destination_label' => $fallback_destination['display_name'],
		'headline'          => 'Calendario promozionale',
		'description'       => 'Controlla rapidamente le tariffe migliori sulla rotta Milano-Parigi, una delle piu cercate dagli utenti italiani.',
	);
}

function cpv_get_promo_calendar_src(): string {
	$context = cpv_get_promo_calendar_context();
	$params  = array(
		'currency'         => 'eur',
		'campaign_id'      => '100',
		'promo_id'         => '4041',
		'achieve'          => '#ff7a45',
		'light'            => '#FFFFFF',
		'dark'             => '#000000',
		'color_background' => '#ffffff',
		'primary'          => '#10233d',
		'range'            => '7,14',
		'period'           => 'year',
		'only_direct'      => 'true',
		'one_way'          => 'true',
		'destination'      => $context['destination_code'],
		'origin'           => $context['origin_code'],
		'powered_by'       => 'false',
		'locale'           => 'it',
		'searchUrl'        => 'app.confrontaprezzivoli.it/flights',
		'shmarker'         => '146401',
		'trs'              => '27252',
	);

	return 'https://tpembd.com/content?' . http_build_query( $params );
}

function cpv_render_promo_calendar(): void {
	if ( is_front_page() ) {
		return;
	}

	$context = cpv_get_promo_calendar_context();
	?>
	<section class="promo-calendar">
		<div class="shell">
			<div class="promo-calendar__copy">
				<p class="eyebrow">Calendario offerte</p>
				<h2><?php echo esc_html( $context['headline'] ); ?></h2>
				<p><?php echo esc_html( $context['description'] ); ?></p>
			</div>
			<div class="promo-calendar__frame">
				<script async src="<?php echo esc_url( cpv_get_promo_calendar_src() ); ?>" charset="utf-8"></script>
			</div>
		</div>
	</section>
	<?php
}

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
