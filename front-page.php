<?php
/**
 * Front page template.
 *
 * @package ConfrontaPrezziVoli
 */

get_header();

$airports = CPV_Data::get_priority_airports();
$airlines = CPV_Data::get_priority_airlines();
$cities   = array_filter(
	array(
		CPV_Data::get_city_by_slug( 'londra' ),
		CPV_Data::get_city_by_slug( 'parigi' ),
		CPV_Data::get_city_by_slug( 'barcellona' ),
		CPV_Data::get_city_by_slug( 'dubai' ),
	)
);
$hero_origin  = CPV_Data::get_airport_by_code( 'FCO' );
$hero_target  = CPV_Data::get_city_by_slug( 'londra' );
$vs_left      = CPV_Data::get_airline_by_slug( 'ryanair' );
$vs_right     = CPV_Data::get_airline_by_slug( 'wizz-air' );
?>
<section class="hero">
	<div class="shell hero__grid">
		<div>
			<p class="eyebrow">Programmatic SEO per voli dall'Italia</p>
			<h1>Trova voli economici dagli aeroporti italiani con un’esperienza pensata per smartphone.</h1>
			<p class="hero__lede">Architettura SEO a silos, prezzi comparabili in un click, filtri rapidi e pagine dinamiche pronte a scalare su migliaia di combinazioni aeroporto, citta e compagnia.</p>
			<div class="hero__chips">
				<span class="chip">Non-stop</span>
				<span class="chip">Sotto €50</span>
				<span class="chip">Partenza mattina</span>
				<span class="chip">Alert prezzo</span>
			</div>
		</div>
		<form class="search-panel js-search-panel" action="<?php echo esc_url( ( $hero_origin && $hero_target ) ? home_url( '/voli/' . $hero_origin['slug'] . '-a-' . $hero_target['slug'] . '/' ) : home_url( '/' ) ); ?>" method="get">
			<div class="search-panel__row">
				<label>
					<span>Da</span>
					<input type="text" name="origin" value="Roma Fiumicino" placeholder="Es. Milano Malpensa">
				</label>
				<label>
					<span>A</span>
					<input type="text" name="destination" value="Londra" placeholder="Es. Barcellona">
				</label>
			</div>
			<div class="search-panel__row">
				<label>
					<span>Partenza</span>
					<input type="text" name="departure" value="<?php echo esc_attr( wp_date( 'd/m/Y', strtotime( '+20 days' ) ) ); ?>">
				</label>
				<label>
					<span>Ritorno</span>
					<input type="text" name="return" value="<?php echo esc_attr( wp_date( 'd/m/Y', strtotime( '+26 days' ) ) ); ?>">
				</label>
			</div>
			<div class="search-panel__actions">
				<button class="button button--primary" type="submit">Confronta voli</button>
				<button class="button button--ghost" type="button">Attiva alert</button>
			</div>
			<div class="search-panel__skeleton" aria-hidden="true">
				<span></span><span></span><span></span>
			</div>
		</form>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Sprint 1-2</p>
			<h2>Hub aeroporti prioritari per intercettare il grosso della domanda italiana.</h2>
		</div>
		<div class="card-grid">
			<?php foreach ( $airports as $airport ) : ?>
				<a class="card card--link" href="<?php echo esc_url( home_url( '/voli-da/' . $airport['slug'] . '/' ) ); ?>">
					<p class="card__code"><?php echo esc_html( $airport['code'] ); ?></p>
					<h3><?php echo esc_html( $airport['display_name'] ); ?></h3>
					<p>Rotte dirette, prezzi minimi stimati, terminal e alert dedicati.</p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--tinted">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Sprint 3</p>
			<h2>Risultati mobile-first con prezzo flessibile, filtri a pillola e zero-latency feel.</h2>
		</div>
		<div class="results-shell">
			<div class="pill-row">
				<span class="pill is-active">Non-stop</span>
				<span class="pill">Sotto €50</span>
				<span class="pill">Weekend</span>
				<span class="pill">Bagaglio incluso</span>
			</div>
			<div class="calendar-grid">
				<?php foreach ( CPV_Data::build_price_calendar( 'FCO', 'LON' ) as $day ) : ?>
					<div class="calendar-cell<?php echo $day['is_lowest'] ? ' is-lowest' : ''; ?>">
						<strong><?php echo esc_html( $day['day'] ); ?></strong>
						<span><?php echo esc_html( CPV_Data::format_price( $day['price'] ) ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="section__intro">
			<p class="eyebrow">Sprint 2-4</p>
			<h2>Silos pronti per città, rotte e compagnie.</h2>
		</div>
		<div class="triple-grid">
			<div class="panel">
				<h3>Voli per città</h3>
				<?php foreach ( $cities as $city ) : ?>
					<a class="inline-link" href="<?php echo esc_url( home_url( '/voli-per/' . $city['slug'] . '/' ) ); ?>">Voli per <?php echo esc_html( $city['display_name'] ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="panel">
				<h3>Compagnie più cercate</h3>
				<?php foreach ( $airlines as $airline ) : ?>
					<a class="inline-link" href="<?php echo esc_url( home_url( '/compagnie-aeree/' . $airline['slug'] . '/' ) ); ?>"><?php echo esc_html( $airline['display_name'] ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="panel">
				<h3>Backlog conversione</h3>
				<p>Newsletter, alert prezzo, caching, Lighthouse 90+, API live per timetable e tariffe.</p>
				<a class="button button--secondary" href="<?php echo esc_url( ( $vs_left && $vs_right ) ? home_url( '/confronto/' . $vs_left['slug'] . '-vs-' . $vs_right['slug'] . '/' ) : home_url( '/' ) ); ?>">Vedi confronto compagnie</a>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
