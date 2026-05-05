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
$vs_left      = CPV_Data::get_airline_by_slug( 'ryanair' );
$vs_right     = CPV_Data::get_airline_by_slug( 'wizz-air' );
?>
<section class="hero">
	<div class="shell">
		<div>
			<p class="eyebrow">Programmatic SEO per voli dall'Italia</p>
			<h2 class="hero__title">Architettura SEO scalabile per dominare le ricerche su voli da Milano, Roma e tutti gli aeroporti italiani.</h2>
			<p class="hero__lede">Il widget Travelpayouts ora vive sotto l’header su tutte le pagine; qui la homepage accompagna la scoperta con silos SEO, percorsi editoriali e landing programmatiche pensate per crescere con i dati.</p>
			<div class="hero__chips">
				<span class="chip">Non-stop</span>
				<span class="chip">Sotto €50</span>
				<span class="chip">Partenza mattina</span>
				<span class="chip">Alert prezzo</span>
			</div>
		</div>
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
