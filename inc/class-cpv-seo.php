<?php
/**
 * Metadata helpers for dynamic pages.
 *
 * @package ConfrontaPrezziVoli
 */

class CPV_SEO {
	public static function init(): void {
		add_filter( 'document_title_parts', array( __CLASS__, 'filter_document_title' ) );
		add_action( 'wp_head', array( __CLASS__, 'render_meta' ), 1 );
	}

	public static function filter_document_title( array $parts ): array {
		$context = self::get_context();
		if ( ! $context ) {
			return $parts;
		}

		$parts['title'] = $context['title'];
		return $parts;
	}

	public static function render_meta(): void {
		$context = self::get_context();
		if ( ! $context ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( $context['description'] ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $context['canonical'] ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $context['title'] ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $context['description'] ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $context['canonical'] ) . '">' . "\n";
		echo '<meta property="og:type" content="website">' . "\n";

		if ( ! empty( $context['schema'] ) ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $context['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
		}
	}

	public static function get_context(): ?array {
		$site_name = 'ConfrontaPrezziVoli';

		if ( get_query_var( 'cpv_airport' ) ) {
			$airport = CPV_Data::get_airport_by_slug( (string) get_query_var( 'cpv_airport' ) );
			if ( ! $airport ) {
				return null;
			}

			return array(
				'title'       => sprintf( 'Voli da %s | Offerte, rotte e prezzi | %s', $airport['display_name'], $site_name ),
				'description' => sprintf( 'Confronta voli in partenza da %s. Scopri tratte dirette, periodi più economici e compagnie aeree che volano dall’Italia.', $airport['display_name'] ),
				'canonical'   => home_url( '/voli-da/' . $airport['slug'] . '/' ),
				'schema'      => array(
					'@context' => 'https://schema.org',
					'@type'    => 'SearchResultsPage',
					'name'     => 'Voli da ' . $airport['display_name'],
				),
			);
		}

		if ( get_query_var( 'cpv_route' ) ) {
			$route   = explode( '__to__', (string) get_query_var( 'cpv_route' ) );
			$origin  = CPV_Data::get_location_by_slug( $route[0] ?? '' );
			$target  = CPV_Data::get_location_by_slug( $route[1] ?? '' );
			$payload = ( $origin && $target ) ? CPV_Data::get_route_payload( $origin, $target ) : null;

			if ( ! $origin || ! $target || ! $payload ) {
				return null;
			}

			return array(
				'title'       => sprintf( 'Voli Economici da %s a %s | Da %s | %s', $origin['display_name'], $target['display_name'], CPV_Data::format_price( $payload['price'] ), $site_name ),
				'description' => sprintf( 'Trova i voli più economici da %s a %s. Confronta prezzi di Ryanair, ITA e Wizz Air in un click.', $origin['display_name'], $target['display_name'] ),
				'canonical'   => home_url( '/voli/' . $origin['slug'] . '-a-' . $target['slug'] . '/' ),
				'schema'      => array(
					'@context' => 'https://schema.org',
					'@type'    => 'OfferCatalog',
					'name'     => sprintf( 'Voli da %s a %s', $origin['display_name'], $target['display_name'] ),
				),
			);
		}

		if ( get_query_var( 'cpv_city' ) ) {
			$city = CPV_Data::get_city_by_slug( (string) get_query_var( 'cpv_city' ) );
			if ( ! $city ) {
				return null;
			}

			return array(
				'title'       => sprintf( 'Voli per %s | Partenze dall’Italia | %s', $city['display_name'], $site_name ),
				'description' => sprintf( 'Confronta voli per %s dai principali aeroporti italiani. Scopri prezzi minimi, compagnie e periodi migliori per partire.', $city['display_name'] ),
				'canonical'   => home_url( '/voli-per/' . $city['slug'] . '/' ),
				'schema'      => array(
					'@context' => 'https://schema.org',
					'@type'    => 'SearchResultsPage',
					'name'     => 'Voli per ' . $city['display_name'],
				),
			);
		}

		if ( get_query_var( 'cpv_airline' ) ) {
			$airline = CPV_Data::get_airline_by_slug( (string) get_query_var( 'cpv_airline' ) );
			if ( ! $airline ) {
				return null;
			}

			return array(
				'title'       => sprintf( '%s: bagagli, check-in e rotte dall’Italia | %s', $airline['display_name'], $site_name ),
				'description' => sprintf( 'Guida completa %s per chi parte dall’Italia: bagaglio a mano, check-in online e tratte più cercate.', $airline['display_name'] ),
				'canonical'   => home_url( '/compagnie-aeree/' . $airline['slug'] . '/' ),
				'schema'      => array(
					'@context' => 'https://schema.org',
					'@type'    => 'Article',
					'headline' => $airline['display_name'],
				),
			);
		}

		if ( get_query_var( 'cpv_compare' ) ) {
			$route   = explode( '__vs__', (string) get_query_var( 'cpv_compare' ) );
			$left    = CPV_Data::get_airline_by_slug( $route[0] ?? '' );
			$right   = CPV_Data::get_airline_by_slug( $route[1] ?? '' );

			if ( ! $left || ! $right ) {
				return null;
			}

			return array(
				'title'       => sprintf( '%s vs %s | Confronto tariffe e servizi | %s', $left['display_name'], $right['display_name'], $site_name ),
				'description' => sprintf( 'Confronto completo tra %s e %s: bagagli, posti, rotte dall’Italia e fascia prezzo media.', $left['display_name'], $right['display_name'] ),
				'canonical'   => home_url( '/confronto/' . $left['slug'] . '-vs-' . $right['slug'] . '/' ),
				'schema'      => array(
					'@context' => 'https://schema.org',
					'@type'    => 'Review',
					'name'     => sprintf( '%s vs %s', $left['display_name'], $right['display_name'] ),
				),
			);
		}

		return null;
	}
}

CPV_SEO::init();

