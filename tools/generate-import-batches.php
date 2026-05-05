<?php
declare(strict_types=1);

/**
 * Generate import-ready categories and post batches from JSON datasets.
 */

$root = dirname(__DIR__);

$paths = array(
	'airports' => $root . DIRECTORY_SEPARATOR . 'airports.json',
	'cities'   => $root . DIRECTORY_SEPARATOR . 'cities.json',
	'airlines' => $root . DIRECTORY_SEPARATOR . 'airlines.json',
	'imports'  => $root . DIRECTORY_SEPARATOR . 'imports',
);

foreach ( array( 'airports', 'cities', 'airlines' ) as $key ) {
	if ( ! file_exists( $paths[ $key ] ) ) {
		fwrite( STDERR, "Missing source file: {$paths[$key]}\n" );
		exit( 1 );
	}
}

$airports = json_decode( (string) file_get_contents( $paths['airports'] ), true, 512, JSON_THROW_ON_ERROR );
$cities   = json_decode( (string) file_get_contents( $paths['cities'] ), true, 512, JSON_THROW_ON_ERROR );
$airlines = json_decode( (string) file_get_contents( $paths['airlines'] ), true, 512, JSON_THROW_ON_ERROR );

$priorityAirportCodes = array( 'FCO', 'CIA', 'MXP', 'BGY', 'LIN', 'NAP', 'VCE', 'CTA', 'BLQ' );
$priorityCityCodes    = array( 'PAR', 'LON', 'BCN', 'MAD', 'BER', 'LIS', 'NYC', 'DXB' );
$cityNameOverrides    = array(
	'London'    => 'Londra',
	'Paris'     => 'Parigi',
	'Barcelona' => 'Barcellona',
	'Madrid'    => 'Madrid',
	'Berlin'    => 'Berlino',
	'Lisbon'    => 'Lisbona',
	'New York'  => 'New York',
	'Dubai'     => 'Dubai',
	'Rome'      => 'Roma',
	'Milan'     => 'Milano',
	'Naples'    => 'Napoli',
	'Venice'    => 'Venezia',
	'Florence'  => 'Firenze',
	'Catania'   => 'Catania',
	'Bologna'   => 'Bologna',
);
$europeanCountries = array(
	'AD', 'AL', 'AT', 'BA', 'BE', 'BG', 'BY', 'CH', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FO', 'FR', 'GB',
	'GE', 'GI', 'GR', 'HR', 'HU', 'IE', 'IS', 'IT', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO',
	'PL', 'PT', 'RO', 'RS', 'SE', 'SI', 'SK', 'SM', 'TR', 'UA', 'VA',
);

ensureDirectory( $paths['imports'] );
ensureDirectory( $paths['imports'] . DIRECTORY_SEPARATOR . 'categories' );
ensureDirectory( $paths['imports'] . DIRECTORY_SEPARATOR . 'airports' );
ensureDirectory( $paths['imports'] . DIRECTORY_SEPARATOR . 'cities' );
ensureDirectory( $paths['imports'] . DIRECTORY_SEPARATOR . 'airlines' );

$airportRecords = buildAirportRecords( $airports, $priorityCityCodes, $cityNameOverrides );
$cityRecords    = buildCityRecords( $cities, $airports, $priorityAirportCodes, $cityNameOverrides, $europeanCountries );
$airlineRecords = buildAirlineRecords( $airlines, $priorityAirportCodes );

$categories = buildCategories();
writeCsv( $paths['imports'] . DIRECTORY_SEPARATOR . 'categories' . DIRECTORY_SEPARATOR . 'categories.csv', $categories );

$airportBatches = writeBatches( $paths['imports'] . DIRECTORY_SEPARATOR . 'airports', 'airports', $airportRecords );
$cityBatches    = writeBatches( $paths['imports'] . DIRECTORY_SEPARATOR . 'cities', 'cities', $cityRecords );
$airlineBatches = writeBatches( $paths['imports'] . DIRECTORY_SEPARATOR . 'airlines', 'airlines', $airlineRecords );

$manifest = array(
	'generated_at' => date( DATE_ATOM ),
	'counts'       => array(
		'categories' => count( $categories ),
		'airports'   => count( $airportRecords ),
		'cities'     => count( $cityRecords ),
		'airlines'   => count( $airlineRecords ),
	),
	'batches'      => array(
		'airports' => $airportBatches,
		'cities'   => $cityBatches,
		'airlines' => $airlineBatches,
	),
);

file_put_contents(
	$paths['imports'] . DIRECTORY_SEPARATOR . 'manifest.json',
	json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . PHP_EOL
);

file_put_contents(
	$paths['imports'] . DIRECTORY_SEPARATOR . 'README.md',
	buildImportsReadme( $manifest )
);

echo "Generated import files in {$paths['imports']}\n";

function buildCategories(): array {
	return array(
		array(
			'name'        => 'Landing SEO Voli',
			'slug'        => 'landing-seo-voli',
			'parent_slug' => '',
			'description' => 'Categoria madre per pagine SEO dedicate a voli, aeroporti, citta e compagnie.',
		),
		array(
			'name'        => 'Voli da aeroporti italiani',
			'slug'        => 'voli-da-aeroporti-italiani',
			'parent_slug' => 'landing-seo-voli',
			'description' => 'Pagine SEO dedicate alle partenze dagli aeroporti italiani.',
		),
		array(
			'name'        => 'Voli per citta',
			'slug'        => 'voli-per-citta',
			'parent_slug' => 'landing-seo-voli',
			'description' => "Landing page per destinazioni e citta raggiungibili dall'Italia.",
		),
		array(
			'name'        => 'Compagnie aeree',
			'slug'        => 'compagnie-aeree',
			'parent_slug' => 'landing-seo-voli',
			'description' => 'Schede e guide sulle compagnie aeree.',
		),
		array(
			'name'        => 'Rotte popolari',
			'slug'        => 'rotte-popolari',
			'parent_slug' => 'landing-seo-voli',
			'description' => 'Contenuti dedicati alle tratte piu cercate dagli utenti italiani.',
		),
		array(
			'name'        => 'Guide voli',
			'slug'        => 'guide-voli',
			'parent_slug' => 'landing-seo-voli',
			'description' => 'Guide utili per prenotazione, bagagli e check-in.',
		),
		array(
			'name'        => 'Voli da Milano',
			'slug'        => 'voli-da-milano',
			'parent_slug' => 'voli-da-aeroporti-italiani',
			'description' => "Partenze dagli aeroporti dell'area di Milano.",
		),
		array(
			'name'        => 'Voli da Roma',
			'slug'        => 'voli-da-roma',
			'parent_slug' => 'voli-da-aeroporti-italiani',
			'description' => "Partenze dagli aeroporti dell'area di Roma.",
		),
		array(
			'name'        => 'Voli da Napoli',
			'slug'        => 'voli-da-napoli',
			'parent_slug' => 'voli-da-aeroporti-italiani',
			'description' => "Partenze dall'aeroporto di Napoli e dal Sud Italia.",
		),
		array(
			'name'        => 'Voli in Europa',
			'slug'        => 'voli-in-europa',
			'parent_slug' => 'voli-per-citta',
			'description' => 'Destinazioni europee raggiungibili con voli diretti o con scalo.',
		),
		array(
			'name'        => 'Voli intercontinentali',
			'slug'        => 'voli-intercontinentali',
			'parent_slug' => 'voli-per-citta',
			'description' => 'Destinazioni extraeuropee per viaggi leisure e business.',
		),
		array(
			'name'        => 'Compagnie low cost',
			'slug'        => 'compagnie-low-cost',
			'parent_slug' => 'compagnie-aeree',
			'description' => 'Compagnie aeree orientate al prezzo e alle tariffe light.',
		),
		array(
			'name'        => 'Compagnie tradizionali',
			'slug'        => 'compagnie-tradizionali',
			'parent_slug' => 'compagnie-aeree',
			'description' => 'Compagnie full service e vettori di bandiera.',
		),
	);
}

function buildAirportRecords( array $airports, array $priorityCityCodes, array $cityNameOverrides ): array {
	$items = array();

	foreach ( $airports as $airport ) {
		if ( empty( $airport['flightable'] ) || 'airport' !== ( $airport['iata_type'] ?? '' ) || 'IT' !== ( $airport['country_code'] ?? '' ) ) {
			continue;
		}

		$name       = preferredLabel( displayName( $airport ), $cityNameOverrides );
		$slug       = uniqueSlug( 'voli-da-' . slugify( $name ) . '-' . strtolower( (string) $airport['code'] ), $items );
		$cityCode   = strtoupper( (string) ( $airport['city_code'] ?? $airport['code'] ) );
		$price      = estimatePrice( (string) $airport['code'], 'PAR' );
		$bestMonth  = estimateBestMonth( (string) $airport['code'], 'PAR' );
		$categories = array( 'Voli da aeroporti italiani' );

		if ( in_array( $cityCode, array( 'MIL', 'MXP', 'LIN', 'BGY' ), true ) ) {
			$categories[] = 'Voli da Milano';
		}

		if ( in_array( $cityCode, array( 'ROM', 'FCO', 'CIA' ), true ) ) {
			$categories[] = 'Voli da Roma';
		}

		if ( 'NAP' === $cityCode ) {
			$categories[] = 'Voli da Napoli';
		}

		$items[] = array(
			'post_type'               => 'post',
			'post_status'             => 'publish',
			'post_title'              => sprintf( 'Voli da %s (%s): offerte, compagnie e consigli 2026', $name, $airport['code'] ),
			'post_name'               => $slug,
			'post_excerpt'            => sprintf( 'Scopri le migliori offerte per volare da %s. Confronta compagnie, tratte popolari e consigli pratici per prenotare nel momento giusto.', $name ),
			'post_content'            => airportContent( $airport, $name, $priorityCityCodes ),
			'post_category'           => implode( '|', $categories ),
			'cpv_page_type'           => 'airport',
			'cpv_entity_code'         => (string) $airport['code'],
			'cpv_city_code'           => $cityCode,
			'cpv_target_path'         => '/voli-da/' . slugify( $name ) . '/',
			'rank_math_title'         => sprintf( 'Voli da %s (%s): offerte e compagnie | ConfrontaPrezziVoli', $name, $airport['code'] ),
			'rank_math_description'   => sprintf( 'Confronta voli da %s, scopri le tratte piu richieste e trova il periodo migliore per prenotare a partire da %s.', $name, formatPrice( $price ) ),
			'rank_math_focus_keyword' => sprintf( 'voli da %s', $name ),
			'seo_batch_label'         => 'airports',
			'seo_priority_note'       => sprintf( 'Mese tipicamente conveniente: %s', $bestMonth ),
		);
	}

	usort(
		$items,
		static fn( array $left, array $right ): int => strcmp( $left['post_title'], $right['post_title'] )
	);

	return $items;
}

function buildCityRecords( array $cities, array $airports, array $priorityAirportCodes, array $cityNameOverrides, array $europeanCountries ): array {
	$priorityAirports = buildPriorityAirportMap( $airports, $priorityAirportCodes, $cityNameOverrides );
	$items            = array();

	foreach ( $cities as $city ) {
		if ( empty( $city['has_flightable_airport'] ) ) {
			continue;
		}

		$name       = preferredLabel( displayName( $city ), $cityNameOverrides );
		$code       = strtoupper( (string) $city['code'] );
		$slug       = uniqueSlug( 'voli-per-' . slugify( $name ) . '-' . strtolower( $code ), $items );
		$price      = estimatePrice( 'MIL', $code );
		$bestMonth  = estimateBestMonth( 'MIL', $code );
		$categories = array( 'Voli per citta' );

		if ( in_array( strtoupper( (string) ( $city['country_code'] ?? '' ) ), $europeanCountries, true ) ) {
			$categories[] = 'Voli in Europa';
		} else {
			$categories[] = 'Voli intercontinentali';
		}

		$items[] = array(
			'post_type'               => 'post',
			'post_status'             => 'publish',
			'post_title'              => sprintf( 'Voli per %s (%s): offerte, compagnie e periodo migliore per prenotare', $name, $code ),
			'post_name'               => $slug,
			'post_excerpt'            => sprintf( 'Confronta voli per %s dai principali aeroporti italiani. Scopri prezzi indicativi, compagnie e consigli utili per organizzare la partenza.', $name ),
			'post_content'            => cityContent( $city, $name, $priorityAirports ),
			'post_category'           => implode( '|', $categories ),
			'cpv_page_type'           => 'city',
			'cpv_entity_code'         => $code,
			'cpv_target_path'         => '/voli-per/' . slugify( $name ) . '/',
			'rank_math_title'         => sprintf( 'Voli per %s (%s): offerte e guide | ConfrontaPrezziVoli', $name, $code ),
			'rank_math_description'   => sprintf( 'Trova voli per %s dai principali aeroporti italiani. Tariffe indicative da %s e consigli per scegliere il periodo migliore.', $name, formatPrice( $price ) ),
			'rank_math_focus_keyword' => sprintf( 'voli per %s', $name ),
			'seo_batch_label'         => 'cities',
			'seo_priority_note'       => sprintf( 'Mese tipicamente conveniente: %s', $bestMonth ),
		);
	}

	usort(
		$items,
		static fn( array $left, array $right ): int => strcmp( $left['post_title'], $right['post_title'] )
	);

	return $items;
}

function buildAirlineRecords( array $airlines, array $priorityAirportCodes ): array {
	$items = array();

	foreach ( $airlines as $airline ) {
		$name       = displayName( $airline );
		$code       = strtoupper( (string) ( $airline['code'] ?? '' ) );
		$slug       = uniqueSlug( 'compagnia-aerea-' . slugify( $name ) . '-' . strtolower( $code ), $items );
		$isLowCost  = ! empty( $airline['is_lowcost'] );
		$categories = array( 'Compagnie aeree', $isLowCost ? 'Compagnie low cost' : 'Compagnie tradizionali' );

		$items[] = array(
			'post_type'               => 'post',
			'post_status'             => 'publish',
			'post_title'              => sprintf( '%s (%s): bagagli, check-in, rotte e consigli 2026', $name, $code ),
			'post_name'               => $slug,
			'post_excerpt'            => sprintf( "Guida pratica su %s: bagaglio a mano, check-in, rotte popolari dall'Italia e consigli utili per prenotare.", $name ),
			'post_content'            => airlineContent( $airline, $priorityAirportCodes ),
			'post_category'           => implode( '|', $categories ),
			'cpv_page_type'           => 'airline',
			'cpv_entity_code'         => $code,
			'cpv_target_path'         => '/compagnie-aeree/' . slugify( $name ) . '/',
			'rank_math_title'         => sprintf( '%s: bagagli, check-in e rotte | ConfrontaPrezziVoli', $name ),
			'rank_math_description'   => sprintf( 'Scopri come volare con %s: bagaglio a mano, check-in, consigli e rotte piu cercate dagli utenti italiani.', $name ),
			'rank_math_focus_keyword' => sprintf( '%s bagaglio check-in', $name ),
			'seo_batch_label'         => 'airlines',
			'seo_priority_note'       => $isLowCost ? 'Compagnia classificata come low cost nel dataset.' : 'Compagnia classificata come tradizionale nel dataset.',
		);
	}

	usort(
		$items,
		static fn( array $left, array $right ): int => strcmp( $left['post_title'], $right['post_title'] )
	);

	return $items;
}

function airportContent( array $airport, string $name, array $priorityCityCodes ): string {
	$code         = strtoupper( (string) $airport['code'] );
	$featuredRows = '';
	$chips        = array();
	$leadPrice    = formatPrice( estimatePrice( $code, 'PAR' ) );
	$leadMonth    = estimateBestMonth( $code, 'PAR' );
	$leadDuration = estimateDuration( $code, 'LON' );

	foreach ( $priorityCityCodes as $cityCode ) {
		$destinationLabel = cityLabelFromCode( $cityCode );
		$featuredRows    .= sprintf(
			'<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
			escHtml( $destinationLabel ),
			escHtml( formatPrice( estimatePrice( $code, $cityCode ) ) ),
			escHtml( estimateDuration( $code, $cityCode ) ),
			escHtml( estimateBestMonth( $code, $cityCode ) )
		);
		$chips[] = $destinationLabel;
	}

	$chipHtml = implode(
		'',
		array_map(
			static fn( string $label ): string => '<span class="cpv-chip">' . escHtml( $label ) . '</span>',
			array_slice( $chips, 0, 6 )
		)
	);

	return <<<HTML
<div class="cpv-landing-note">
  <p><strong>Panoramica rapida.</strong> Questa pagina raccoglie informazioni utili per chi cerca voli da {$name} ({$code}), con prezzi indicativi, tratte richieste e suggerimenti pratici per prenotare con maggiore sicurezza.</p>
</div>
<div class="cpv-stat-grid">
  <div class="cpv-stat"><strong>{$leadPrice}</strong><span>Tariffa indicativa da monitorare per le rotte europee.</span></div>
  <div class="cpv-stat"><strong>{$leadMonth}</strong><span>Mese spesso interessante per tenere d'occhio le offerte.</span></div>
  <div class="cpv-stat"><strong>{$leadDuration}</strong><span>Tempo medio orientativo su una rotta molto cercata.</span></div>
</div>
<h2>Offerte voli da {$name}</h2>
<p>Le partenze da {$name} intercettano sia city break europei sia rotte stagionali. Tenere monitorati i prezzi con anticipo aiuta a capire quando si apre la finestra migliore per prenotare.</p>
<table>
  <thead>
    <tr><th>Destinazione</th><th>Prezzo da</th><th>Durata indicativa</th><th>Mese da monitorare</th></tr>
  </thead>
  <tbody>
    {$featuredRows}
  </tbody>
</table>
<h2>Destinazioni che gli utenti controllano piu spesso</h2>
<div class="cpv-chip-list">{$chipHtml}</div>
<div class="cpv-landing-grid">
  <div class="cpv-landing-card"><strong>Partenze mattutine</strong><p>Ideali per city break e viaggi business con arrivo in giornata.</p></div>
  <div class="cpv-landing-card"><strong>Weekend brevi</strong><p>Le tariffe piu richieste si concentrano spesso sui weekend lunghi e sulle festivita.</p></div>
  <div class="cpv-landing-card"><strong>Rotte stagionali</strong><p>In estate e durante i ponti si intensificano le ricerche verso spiagge, capitali europee e destinazioni leisure.</p></div>
</div>
<h2>Informazioni utili per prenotare da {$name}</h2>
<ul>
  <li>Controlla sempre la politica bagagli della compagnia selezionata prima della prenotazione.</li>
  <li>Confronta aeroporti vicini quando il tempo di trasferimento resta accettabile.</li>
  <li>Valuta partenze infrasettimanali per aumentare le chance di trovare una tariffa piu bassa.</li>
</ul>
<h2>Domande frequenti sui voli da {$name}</h2>
<div class="cpv-faq">
  <details><summary>Quando conviene cercare voli da {$name}?</summary><p>Monitorare la rotta qualche settimana prima e verificare piu giorni di partenza aiuta a trovare tariffe piu convenienti.</p></details>
  <details><summary>Quali destinazioni vengono cercate piu spesso?</summary><p>Le ricerche si concentrano di solito su capitali europee, city break e mete leisure ad alta stagionalita.</p></details>
  <details><summary>Conviene confrontare solo voli diretti?</summary><p>Dipende dal viaggio: per tratte brevi il diretto e spesso preferibile, mentre per il lungo raggio uno scalo puo ridurre il prezzo finale.</p></details>
</div>
HTML;
}

function cityContent( array $city, string $name, array $priorityAirports ): string {
	$code      = strtoupper( (string) $city['code'] );
	$rows      = '';
	$chips     = array();
	$cheapest  = formatPrice( estimatePrice( 'MIL', $code ) );
	$bestMonth = estimateBestMonth( 'MIL', $code );

	foreach ( $priorityAirports as $airport ) {
		$rows .= sprintf(
			'<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
			escHtml( $airport['name'] . ' (' . $airport['code'] . ')' ),
			escHtml( formatPrice( estimatePrice( $airport['code'], $code ) ) ),
			escHtml( estimateDuration( $airport['code'], $code ) ),
			escHtml( estimateBestMonth( $airport['code'], $code ) )
		);
		$chips[] = $airport['name'];
	}

	$chipHtml = implode(
		'',
		array_map(
			static fn( string $label ): string => '<span class="cpv-chip">' . escHtml( $label ) . '</span>',
			array_slice( $chips, 0, 6 )
		)
	);

	return <<<HTML
<div class="cpv-landing-note">
  <p><strong>Perche cercare voli per {$name} qui.</strong> Questa pagina raccoglie un quadro rapido delle partenze italiane piu interessanti per {$name}, con tariffe indicative e consigli per scegliere il periodo piu vantaggioso.</p>
</div>
<div class="cpv-stat-grid">
  <div class="cpv-stat"><strong>{$cheapest}</strong><span>Prezzo indicativo da monitorare dalle principali aree aeroportuali italiane.</span></div>
  <div class="cpv-stat"><strong>{$bestMonth}</strong><span>Periodo spesso interessante per tenere sotto controllo la rotta.</span></div>
  <div class="cpv-stat"><strong>9</strong><span>Aeroporti italiani prioritari da confrontare per questa destinazione.</span></div>
</div>
<h2>Offerte voli per {$name}</h2>
<p>Per trovare il miglior equilibrio tra prezzo, durata e comfort, conviene verificare piu aeroporti di partenza italiani e confrontare giorni feriali e weekend.</p>
<table>
  <thead>
    <tr><th>Partenza</th><th>Prezzo da</th><th>Durata indicativa</th><th>Mese da monitorare</th></tr>
  </thead>
  <tbody>
    {$rows}
  </tbody>
</table>
<h2>Aeroporti italiani da cui conviene partire</h2>
<div class="cpv-chip-list">{$chipHtml}</div>
<div class="cpv-landing-grid">
  <div class="cpv-landing-card"><strong>City break</strong><p>Se {$name} e una meta urbana, le partenze del venerdi e del sabato sono spesso le piu monitorate.</p></div>
  <div class="cpv-landing-card"><strong>Viaggi stagionali</strong><p>In alcuni periodi dell'anno le tariffe possono variare molto, soprattutto vicino ai ponti e alle festivita.</p></div>
  <div class="cpv-landing-card"><strong>Confronto rapido</strong><p>Valuta aeroporti maggiori e secondari per capire se un piccolo spostamento porta a un risparmio concreto.</p></div>
</div>
<h2>Domande frequenti sui voli per {$name}</h2>
<div class="cpv-faq">
  <details><summary>Qual e il modo migliore per cercare voli per {$name}?</summary><p>Confronta piu aeroporti italiani, prova date flessibili e verifica anche partenze infrasettimanali per migliorare le possibilita di risparmio.</p></details>
  <details><summary>Meglio volo diretto o con scalo?</summary><p>Per tratte medio-brevi il diretto e spesso la scelta piu comoda, mentre su alcune rotte uno scalo puo ridurre il prezzo finale.</p></details>
  <details><summary>Quando controllare i prezzi per {$name}?</summary><p>Vale la pena iniziare a monitorare la rotta in anticipo e verificare con regolarita i giorni piu vicini alla partenza ideale.</p></details>
</div>
HTML;
}

function airlineContent( array $airline, array $priorityAirportCodes ): string {
	$name      = displayName( $airline );
	$isLowCost = ! empty( $airline['is_lowcost'] );
	$service   = $isLowCost ? 'Low cost' : 'Tradizionale';
	$bagText   = $isLowCost
		? 'Verifica con attenzione dimensioni e priorita del bagaglio a mano.'
		: 'Controlla franchigia e servizi inclusi in base alla tariffa scelta.';
	$routeNote = $isLowCost
		? 'La compagnia e spesso cercata per city break, leisure e rotte sensibili al prezzo.'
		: 'La compagnia viene spesso valutata per tratte business, nazionali o con servizi aggiuntivi.';

	$chipHtml = implode(
		'',
		array_map(
			static fn( string $airportCode ): string => '<span class="cpv-chip">' . escHtml( $airportCode ) . '</span>',
			array_slice( $priorityAirportCodes, 0, 6 )
		)
	);

	return <<<HTML
<div class="cpv-landing-note">
  <p><strong>Guida pratica {$name}.</strong> Qui trovi una sintesi utile per capire come prenotare con {$name}, cosa controllare su bagagli e check-in e quali aspetti confrontare prima di acquistare il volo.</p>
</div>
<div class="cpv-stat-grid">
  <div class="cpv-stat"><strong>{$service}</strong><span>Tipologia di compagnia secondo il dataset disponibile.</span></div>
  <div class="cpv-stat"><strong>Check-in online</strong><span>Da verificare sempre sul sito ufficiale prima della partenza.</span></div>
  <div class="cpv-stat"><strong>Bagagli</strong><span>{$bagText}</span></div>
</div>
<h2>{$name}: cosa controllare prima di prenotare</h2>
<p>{$routeNote} Prima della prenotazione conviene confrontare orari, politica bagagli, flessibilita della tariffa e aeroporto di partenza disponibile in Italia.</p>
<div class="cpv-chip-list">{$chipHtml}</div>
<div class="cpv-landing-grid">
  <div class="cpv-landing-card"><strong>Bagaglio a mano</strong><p>Controlla sempre misure, peso e possibilita di aggiungere priorita o trolley in cabina.</p></div>
  <div class="cpv-landing-card"><strong>Check-in</strong><p>Verifica finestre di apertura, app mobile, costi in aeroporto e documenti richiesti.</p></div>
  <div class="cpv-landing-card"><strong>Partenze italiane</strong><p>Confronta i principali aeroporti italiani per capire quali rotte o fasce orarie sono piu interessanti.</p></div>
</div>
<h2>Domande frequenti su {$name}</h2>
<div class="cpv-faq">
  <details><summary>Come verificare le regole bagaglio di {$name}?</summary><p>Consulta sempre la pagina ufficiale prima dell'acquisto: le regole possono cambiare in base alla tariffa, alla rotta e alla data di viaggio.</p></details>
  <details><summary>{$name} e adatta a chi cerca il prezzo piu basso?</summary><p>Dipende dalla rotta e dai servizi necessari. In molti casi le tariffe base sono competitive, ma conviene verificare gli extra prima di concludere la prenotazione.</p></details>
  <details><summary>Quali aeroporti italiani monitorare?</summary><p>Le partenze da Milano, Roma, Napoli, Venezia, Catania e Bologna sono tra quelle che gli utenti confrontano piu spesso.</p></details>
</div>
HTML;
}

function buildPriorityAirportMap( array $airports, array $priorityCodes, array $cityNameOverrides ): array {
	$map = array();

	foreach ( $priorityCodes as $code ) {
		foreach ( $airports as $airport ) {
			if ( strtoupper( (string) ( $airport['code'] ?? '' ) ) !== $code ) {
				continue;
			}

			$map[] = array(
				'code' => $code,
				'name' => preferredLabel( displayName( $airport ), $cityNameOverrides ),
			);
			break;
		}
	}

	return $map;
}

function writeBatches( string $directory, string $prefix, array $records ): array {
	$batches = array();
	$chunks  = array_chunk( $records, 100 );

	foreach ( $chunks as $index => $chunk ) {
		$file = sprintf( '%s-batch-%03d.csv', $prefix, $index + 1 );
		writeCsv( $directory . DIRECTORY_SEPARATOR . $file, $chunk );
		$batches[] = array(
			'file'  => $prefix . DIRECTORY_SEPARATOR . $file,
			'count' => count( $chunk ),
		);
	}

	return $batches;
}

function writeCsv( string $path, array $rows ): void {
	$handle = fopen( $path, 'wb' );
	if ( false === $handle ) {
		throw new RuntimeException( 'Unable to open file for writing: ' . $path );
	}

	if ( empty( $rows ) ) {
		fclose( $handle );
		return;
	}

	fputcsv( $handle, array_keys( $rows[0] ) );

	foreach ( $rows as $row ) {
		fputcsv( $handle, $row );
	}

	fclose( $handle );
}

function buildImportsReadme( array $manifest ): string {
	$airportBatchCount = count( $manifest['batches']['airports'] );
	$cityBatchCount    = count( $manifest['batches']['cities'] );
	$airlineBatchCount = count( $manifest['batches']['airlines'] );

	return <<<MD
# Import Pack SEO

Questa cartella contiene:

- `categories/categories.csv` da importare per prima
- batch CSV per aeroporti, citta e compagnie
- `manifest.json` con conteggi e percorsi

## Ordine consigliato

1. Importa `categories/categories.csv`
2. Importa i batch aeroporti (`{$airportBatchCount}` file)
3. Importa i batch citta (`{$cityBatchCount}` file)
4. Importa i batch compagnie (`{$airlineBatchCount}` file)

## Formato campi principali

- `post_title`
- `post_name`
- `post_excerpt`
- `post_content`
- `post_category`
- `rank_math_title`
- `rank_math_description`
- `rank_math_focus_keyword`
- campi `cpv_*` per struttura e metadati interni

## Rigenerazione

Esegui:

```powershell
php tools/generate-import-batches.php
```
MD;
}

function displayName( array $item ): string {
	if ( ! empty( $item['name_translations']['en'] ) ) {
		return (string) $item['name_translations']['en'];
	}

	return (string) ( $item['name'] ?? $item['code'] ?? 'n-a' );
}

function preferredLabel( string $name, array $overrides ): string {
	return $overrides[ $name ] ?? $name;
}

function cityLabelFromCode( string $code ): string {
	static $labels = array(
		'PAR' => 'Parigi',
		'LON' => 'Londra',
		'BCN' => 'Barcellona',
		'MAD' => 'Madrid',
		'BER' => 'Berlino',
		'LIS' => 'Lisbona',
		'NYC' => 'New York',
		'DXB' => 'Dubai',
	);

	return $labels[ strtoupper( $code ) ] ?? strtoupper( $code );
}

function slugify( string $value ): string {
	$value = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $value ) ?: $value;
	$value = strtolower( $value );
	$value = preg_replace( '/[^a-z0-9]+/', '-', $value ) ?: '';
	$value = trim( $value, '-' );

	return '' !== $value ? $value : 'item';
}

function uniqueSlug( string $base, array $existingRows ): string {
	$used = array_map(
		static fn( array $row ): string => (string) $row['post_name'],
		$existingRows
	);

	if ( ! in_array( $base, $used, true ) ) {
		return $base;
	}

	$counter = 2;
	while ( in_array( $base . '-' . $counter, $used, true ) ) {
		++$counter;
	}

	return $base . '-' . $counter;
}

function formatPrice( int $price ): string {
	return 'EUR ' . number_format( $price, 0, ',', '.' );
}

function estimatePrice( string $originCode, string $destinationCode ): int {
	return 19 + ( hashSeed( $originCode, $destinationCode ) % 180 );
}

function estimateDuration( string $originCode, string $destinationCode ): string {
	$seed    = hashSeed( $originCode, $destinationCode );
	$hours   = 1 + ( $seed % 11 );
	$minutes = array( '00', '10', '20', '30', '40', '50' )[ $seed % 6 ];
	return $hours . 'h ' . $minutes;
}

function estimateBestMonth( string $originCode, string $destinationCode ): string {
	$months = array( 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre' );
	return $months[ hashSeed( $originCode, $destinationCode ) % count( $months ) ];
}

function hashSeed( string $left, string $right ): int {
	return abs( crc32( strtolower( $left . '|' . $right ) ) );
}

function ensureDirectory( string $path ): void {
	if ( is_dir( $path ) ) {
		return;
	}

	if ( ! mkdir( $path, 0777, true ) && ! is_dir( $path ) ) {
		throw new RuntimeException( 'Unable to create directory: ' . $path );
	}
}

function escHtml( string $value ): string {
	return htmlspecialchars( $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}
