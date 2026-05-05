<?php
/**
 * Data repository for airports, cities, and airlines.
 *
 * @package ConfrontaPrezziVoli
 */

class CPV_Data {
	/**
	 * Runtime cache.
	 *
	 * @var array<string, mixed>
	 */
	private static array $cache = array();

	/**
	 * Get Italian departure airports.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_italian_airports(): array {
		$airports = self::get_airports();

		$filtered = array_values(
			array_filter(
				$airports,
				static function ( array $airport ): bool {
					return 'IT' === ( $airport['country_code'] ?? '' )
						&& 'airport' === ( $airport['iata_type'] ?? '' )
						&& ! empty( $airport['flightable'] );
				}
			)
		);

		usort(
			$filtered,
			static fn( array $left, array $right ): int => strcmp( $left['display_name'], $right['display_name'] )
		);

		return $filtered;
	}

	/**
	 * Get spotlight airports.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_priority_airports(): array {
		$preferred = array( 'FCO', 'CIA', 'MXP', 'BGY', 'LIN', 'NAP', 'VCE', 'CTA' );
		$items     = array();

		foreach ( $preferred as $code ) {
			$airport = self::get_airport_by_code( $code );
			if ( $airport ) {
				$items[] = $airport;
			}
		}

		return $items;
	}

	/**
	 * Get selected airlines.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_priority_airlines(): array {
		$names = array( 'Ryanair', 'ITA Airways', 'Wizz Air', 'easyJet', 'Vueling', 'Neos' );
		$all   = self::get_airlines();
		$items = array();

		foreach ( $names as $name ) {
			foreach ( $all as $airline ) {
				if ( 0 === strcasecmp( $airline['display_name'], $name ) ) {
					$items[] = $airline;
					break;
				}
			}
		}

		return $items;
	}

	/**
	 * Get airport by slug.
	 */
	public static function get_airport_by_slug( string $slug ): ?array {
		foreach ( self::get_airports() as $airport ) {
			if ( $airport['slug'] === $slug ) {
				return $airport;
			}
		}

		return null;
	}

	/**
	 * Get airport by code.
	 */
	public static function get_airport_by_code( string $code ): ?array {
		foreach ( self::get_airports() as $airport ) {
			if ( strtoupper( $airport['code'] ?? '' ) === strtoupper( $code ) ) {
				return $airport;
			}
		}

		return null;
	}

	/**
	 * Get airline by slug.
	 */
	public static function get_airline_by_slug( string $slug ): ?array {
		foreach ( self::get_airlines() as $airline ) {
			if ( $airline['slug'] === $slug ) {
				return $airline;
			}
		}

		return null;
	}

	/**
	 * Get city by slug.
	 */
	public static function get_city_by_slug( string $slug ): ?array {
		foreach ( self::get_cities() as $city ) {
			if ( $city['slug'] === $slug ) {
				return $city;
			}
		}

		return null;
	}

	/**
	 * Resolve airport or city by slug.
	 */
	public static function get_location_by_slug( string $slug ): ?array {
		$airport = self::get_airport_by_slug( $slug );
		if ( $airport ) {
			$airport['entity_type'] = 'airport';
			return $airport;
		}

		$city = self::get_city_by_slug( $slug );
		if ( $city ) {
			$city['entity_type'] = 'city';
			return $city;
		}

		return null;
	}

	/**
	 * Get airports grouped for an Italian city code.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_airports_for_city_code( string $city_code ): array {
		return array_values(
			array_filter(
				self::get_airports(),
				static function ( array $airport ) use ( $city_code ): bool {
					return strtoupper( $airport['city_code'] ?? '' ) === strtoupper( $city_code )
						&& 'airport' === ( $airport['iata_type'] ?? '' )
						&& ! empty( $airport['flightable'] );
				}
			)
		);
	}

	/**
	 * Generate synthetic route deals from Italian airports to a city.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_city_deals( array $destination_city ): array {
		$rows = array();

		foreach ( self::get_priority_airports() as $airport ) {
			$rows[] = array(
				'origin'              => $airport,
				'destination'         => $destination_city,
				'price'               => self::estimate_price( $airport['code'], $destination_city['code'] ),
				'flight_duration'     => self::estimate_duration( $airport['code'], $destination_city['code'] ),
				'best_month'          => self::estimate_best_month( $airport['code'], $destination_city['code'] ),
				'non_stop_available'  => self::estimate_non_stop( $airport['code'], $destination_city['code'] ),
			);
		}

		usort(
			$rows,
			static fn( array $left, array $right ): int => $left['price'] <=> $right['price']
		);

		return $rows;
	}

	/**
	 * Build airport departure destinations.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_airport_destinations( array $origin_airport ): array {
		$featured = array( 'londra', 'parigi', 'barcellona', 'madrid', 'berlino', 'lisbona', 'new-york', 'dubai' );
		$rows     = array();

		foreach ( $featured as $slug ) {
			$city = self::get_city_by_slug( $slug );
			if ( ! $city ) {
				continue;
			}

			$rows[] = array(
				'destination'        => $city,
				'price'              => self::estimate_price( $origin_airport['code'], $city['code'] ),
				'flight_duration'    => self::estimate_duration( $origin_airport['code'], $city['code'] ),
				'best_month'         => self::estimate_best_month( $origin_airport['code'], $city['code'] ),
				'non_stop_available' => self::estimate_non_stop( $origin_airport['code'], $city['code'] ),
			);
		}

		usort(
			$rows,
			static fn( array $left, array $right ): int => $left['price'] <=> $right['price']
		);

		return $rows;
	}

	/**
	 * Route insight payload.
	 */
	public static function get_route_payload( array $origin, array $destination ): array {
		$route_seed = self::hash_seed( $origin['code'] ?? $origin['slug'], $destination['code'] ?? $destination['slug'] );
		$airlines   = self::get_priority_airlines();
		$serving    = array_slice( $airlines, 0, 3 + ( $route_seed % 2 ) );

		return array(
			'price'                => self::estimate_price( $origin['code'] ?? $origin['slug'], $destination['code'] ?? $destination['slug'] ),
			'flight_duration'      => self::estimate_duration( $origin['code'] ?? $origin['slug'], $destination['code'] ?? $destination['slug'] ),
			'best_month'           => self::estimate_best_month( $origin['code'] ?? $origin['slug'], $destination['code'] ?? $destination['slug'] ),
			'non_stop_available'   => self::estimate_non_stop( $origin['code'] ?? $origin['slug'], $destination['code'] ?? $destination['slug'] ),
			'typical_booking_lead' => 21 + ( $route_seed % 28 ),
			'serving_airlines'     => $serving,
			'calendar'             => self::build_price_calendar( $origin['code'] ?? $origin['slug'], $destination['code'] ?? $destination['slug'] ),
		);
	}

	/**
	 * Build airline comparison data.
	 */
	public static function compare_airlines( array $left, array $right ): array {
		$rows = array(
			array(
				'label' => 'Bagaglio a mano',
				'left'  => ! empty( $left['is_lowcost'] ) ? 'Più restrittivo' : 'Più flessibile',
				'right' => ! empty( $right['is_lowcost'] ) ? 'Più restrittivo' : 'Più flessibile',
			),
			array(
				'label' => 'Posti inclusi',
				'left'  => ! empty( $left['is_lowcost'] ) ? 'Standard, extra a pagamento' : 'Maggiore flessibilità',
				'right' => ! empty( $right['is_lowcost'] ) ? 'Standard, extra a pagamento' : 'Maggiore flessibilità',
			),
			array(
				'label' => 'Rotte forti dall’Italia',
				'left'  => self::estimate_airline_route_focus( $left ),
				'right' => self::estimate_airline_route_focus( $right ),
			),
			array(
				'label' => 'Fascia prezzo media',
				'left'  => 'Da ' . self::format_price( 24 + self::hash_seed( $left['code'], 'price' ) % 90 ),
				'right' => 'Da ' . self::format_price( 24 + self::hash_seed( $right['code'], 'price' ) % 90 ),
			),
		);

		return array(
			'rows'        => $rows,
			'recommendation' => ! empty( $left['is_lowcost'] ) && empty( $right['is_lowcost'] )
				? $left['display_name'] . ' conviene per il prezzo, ' . $right['display_name'] . ' per servizi e flessibilità.'
				: $left['display_name'] . ' e ' . $right['display_name'] . ' vanno confrontate soprattutto su bagagli, orari e aeroporti serviti in Italia.',
		);
	}

	/**
	 * Get the main datasets.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_airports(): array {
		if ( isset( self::$cache['airports'] ) ) {
			return self::$cache['airports'];
		}

		$airports = self::load_json( CPV_PROJECT_ROOT . '/airports.json' );
		self::$cache['airports'] = self::hydrate_records( $airports );

		return self::$cache['airports'];
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_cities(): array {
		if ( isset( self::$cache['cities'] ) ) {
			return self::$cache['cities'];
		}

		$cities = self::load_json( CPV_PROJECT_ROOT . '/cities.json' );
		self::$cache['cities'] = self::hydrate_records(
			$cities,
			array(
				'LON' => 'londra',
				'PAR' => 'parigi',
				'BCN' => 'barcellona',
				'MAD' => 'madrid',
				'BER' => 'berlino',
				'LIS' => 'lisbona',
				'NYC' => 'new-york',
				'DXB' => 'dubai',
			)
		);

		return self::$cache['cities'];
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_airlines(): array {
		if ( isset( self::$cache['airlines'] ) ) {
			return self::$cache['airlines'];
		}

		$airlines = self::load_json( CPV_PROJECT_ROOT . '/airlines.json' );
		self::$cache['airlines'] = self::hydrate_records(
			$airlines,
			array(
				'FR' => 'ryanair',
				'AZ' => 'ita-airways',
				'U2' => 'easyjet',
				'W6' => 'wizz-air',
				'VY' => 'vueling',
				'NO' => 'neos',
			)
		);

		return self::$cache['airlines'];
	}

	/**
	 * Display name helper that prefers English labels.
	 */
	public static function display_name( array $item ): string {
		if ( ! empty( $item['name_translations']['en'] ) ) {
			return (string) $item['name_translations']['en'];
		}

		return (string) ( $item['name'] ?? $item['code'] ?? '' );
	}

	/**
	 * Slugify text for routing.
	 */
	public static function slugify( string $value ): string {
		$slug = sanitize_title( remove_accents( $value ) );
		return $slug ?: strtolower( preg_replace( '/[^a-z0-9]+/', '-', $value ) );
	}

	/**
	 * Format EUR.
	 */
	public static function format_price( int $price ): string {
		return '€' . number_format_i18n( $price, 0 );
	}

	/**
	 * Build a simple price calendar.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function build_price_calendar( string $origin_code, string $destination_code ): array {
		$seed  = self::hash_seed( $origin_code, $destination_code );
		$days  = array();
		$month = wp_date( 'F Y', strtotime( '+1 month' ) );

		for ( $index = 1; $index <= 14; $index++ ) {
			$days[] = array(
				'day'       => $index,
				'month'     => $month,
				'price'     => 24 + ( ( $seed + ( $index * 11 ) ) % 155 ),
				'is_lowest' => 0 === $index % 5,
			);
		}

		return $days;
	}

	/**
	 * Load json from disk.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function load_json( string $path ): array {
		if ( ! file_exists( $path ) ) {
			return array();
		}

		$content = file_get_contents( $path );
		$data    = json_decode( $content ?: '[]', true );

		return is_array( $data ) ? $data : array();
	}

	/**
	 * Normalize dataset records and keep slugs unique.
	 *
	 * @param array<int, array<string, mixed>> $items
	 * @param array<string, string>            $preferred_slugs
	 * @return array<int, array<string, mixed>>
	 */
	private static function hydrate_records( array $items, array $preferred_slugs = array() ): array {
		$slug_counts = array();
		$records     = array();

		foreach ( $items as $item ) {
			$display_name = self::display_name( $item );
			$code         = (string) ( $item['code'] ?? '' );
			$base_slug    = $preferred_slugs[ $code ] ?? self::slugify( $display_name );

			if ( isset( $slug_counts[ $base_slug ] ) ) {
				$base_slug .= '-' . strtolower( $code );
			}

			$slug_counts[ $base_slug ] = true;
			$records[]                 = array_merge(
				$item,
				array(
					'display_name' => $display_name,
					'slug'         => $base_slug,
				)
			);
		}

		return $records;
	}

	/**
	 * Stable pseudo fare estimator.
	 */
	private static function estimate_price( string $origin_code, string $destination_code ): int {
		return 19 + ( self::hash_seed( $origin_code, $destination_code ) % 180 );
	}

	/**
	 * Stable pseudo duration estimator.
	 */
	private static function estimate_duration( string $origin_code, string $destination_code ): string {
		$seed    = self::hash_seed( $origin_code, $destination_code );
		$hours   = 1 + ( $seed % 11 );
		$minutes = array( '00', '10', '20', '30', '40', '50' )[ $seed % 6 ];
		return sprintf( '%dh %s', $hours, $minutes );
	}

	/**
	 * Stable pseudo month estimator.
	 */
	private static function estimate_best_month( string $origin_code, string $destination_code ): string {
		$months = array( 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre' );
		return $months[ self::hash_seed( $origin_code, $destination_code ) % count( $months ) ];
	}

	/**
	 * Stable pseudo direct availability.
	 */
	private static function estimate_non_stop( string $origin_code, string $destination_code ): bool {
		return 0 !== self::hash_seed( $origin_code, $destination_code ) % 3;
	}

	/**
	 * Airline route focus helper.
	 */
	private static function estimate_airline_route_focus( array $airline ): string {
		return ! empty( $airline['is_lowcost'] )
			? 'City break europee e tratte leisure'
			: 'Rotte business, nazionali e connessioni internazionali';
	}

	/**
	 * Seed helper.
	 */
	private static function hash_seed( string $left, string $right ): int {
		return abs( crc32( strtolower( $left . '|' . $right ) ) );
	}
}
