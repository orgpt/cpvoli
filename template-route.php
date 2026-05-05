<?php
/**
 * Route page template.
 *
 * @package ConfrontaPrezziVoli
 */

$route_parts  = explode( '__to__', (string) get_query_var( 'cpv_route' ) );
$origin       = CPV_Data::get_location_by_slug( $route_parts[0] ?? '' );
$destination  = CPV_Data::get_location_by_slug( $route_parts[1] ?? '' );

if ( ! $origin || ! $destination ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( '404' );
	return;
}

$payload = CPV_Data::get_route_payload( $origin, $destination );

get_header();
?>
<section class="hero hero--compact">
	<div class="shell hero__grid hero__grid--single">
		<div>
			<p class="eyebrow">Route Page</p>
			<h1>Voli da <?php echo esc_html( $origin['display_name'] ); ?> a <?php echo esc_html( $destination['display_name'] ); ?></h1>
			<p class="hero__lede">Prezzo da <?php echo esc_html( CPV_Data::format_price( $payload['price'] ) ); ?>, miglior finestra in <?php echo esc_html( $payload['best_month'] ); ?> e indicazioni rapide per utenti italiani flessibili.</p>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="metric-grid">
			<div class="metric-card"><span>Prezzo da</span><strong><?php echo esc_html( CPV_Data::format_price( $payload['price'] ) ); ?></strong></div>
			<div class="metric-card"><span>Durata</span><strong><?php echo esc_html( $payload['flight_duration'] ); ?></strong></div>
			<div class="metric-card"><span>Mese migliore</span><strong><?php echo esc_html( ucfirst( $payload['best_month'] ) ); ?></strong></div>
			<div class="metric-card"><span>Operativita</span><strong><?php echo esc_html( $payload['non_stop_available'] ? 'Diretta disponibile' : 'Con scalo frequente' ); ?></strong></div>
		</div>

		<div class="results-shell">
			<div class="section__intro">
				<p class="eyebrow">Prezzi flessibili</p>
				<h2>Calendario con i giorni più convenienti</h2>
			</div>
			<div class="calendar-grid">
				<?php foreach ( $payload['calendar'] as $day ) : ?>
					<div class="calendar-cell<?php echo $day['is_lowest'] ? ' is-lowest' : ''; ?>">
						<strong><?php echo esc_html( $day['day'] ); ?></strong>
						<span><?php echo esc_html( CPV_Data::format_price( $day['price'] ) ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="triple-grid">
			<div class="panel">
				<h3>Compagnie su questa rotta</h3>
				<?php foreach ( $payload['serving_airlines'] as $airline ) : ?>
					<a class="inline-link" href="<?php echo esc_url( home_url( '/compagnie-aeree/' . $airline['slug'] . '/' ) ); ?>"><?php echo esc_html( $airline['display_name'] ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="panel">
				<h3>Quando prenotare</h3>
				<p>Il modello stima una finestra ideale di circa <?php echo esc_html( $payload['typical_booking_lead'] ); ?> giorni prima della partenza.</p>
			</div>
			<div class="panel">
				<h3>Alert one-tap</h3>
				<p>Salva la tratta e attiva notifiche di ribasso prezzo su WhatsApp o Telegram.</p>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();

