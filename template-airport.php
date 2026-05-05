<?php
/**
 * Airport hub template.
 *
 * @package ConfrontaPrezziVoli
 */

$airport = CPV_Data::get_airport_by_slug( (string) get_query_var( 'cpv_airport' ) );
if ( ! $airport ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( '404' );
	return;
}

$destinations = CPV_Data::get_airport_destinations( $airport );

get_header();
?>
<section class="hero hero--compact">
	<div class="shell hero__grid hero__grid--single">
		<div>
			<p class="eyebrow">Airport Hub</p>
			<h1>Voli da <?php echo esc_html( $airport['display_name'] ); ?></h1>
			<p class="hero__lede">Pagina programmatica per intercettare ricerche su partenze, rotte dirette, prezzo medio e informazioni utili sull’aeroporto.</p>
			<div class="hero__chips">
				<span class="chip"><?php echo esc_html( $airport['code'] ); ?></span>
				<span class="chip">Terminal info</span>
				<span class="chip">Alert prezzo</span>
				<span class="chip">Partenze ITA</span>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="triple-grid">
			<div class="panel">
				<h2>Partenze dirette in evidenza</h2>
				<p>Questa sezione e pronta per essere collegata a un feed API real-time di partenze e cancellazioni.</p>
			</div>
			<div class="panel">
				<h3>Info terminal</h3>
				<p>Check-in online, tempi medi ai controlli, consigli per il parcheggio e orari di punta.</p>
			</div>
			<div class="panel">
				<h3>Notifiche</h3>
				<p>Salva una tratta da <?php echo esc_html( $airport['display_name'] ); ?> e ricevi alert via WhatsApp o Telegram.</p>
			</div>
		</div>

		<div class="table-card">
			<div class="table-card__header">
				<h2>Destinazioni con miglior rapporto prezzo</h2>
				<p>Dati dimostrativi pronti da sostituire con una sorgente prezzi live o Amadeus.</p>
			</div>
			<table class="data-table">
				<thead>
					<tr>
						<th>Destinazione</th>
						<th>Da</th>
						<th>Durata</th>
						<th>Mese migliore</th>
						<th>Tipo</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $destinations as $row ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( home_url( '/voli/' . $airport['slug'] . '-a-' . $row['destination']['slug'] . '/' ) ); ?>"><?php echo esc_html( $row['destination']['display_name'] ); ?></a></td>
							<td><?php echo esc_html( CPV_Data::format_price( $row['price'] ) ); ?></td>
							<td><?php echo esc_html( $row['flight_duration'] ); ?></td>
							<td><?php echo esc_html( ucfirst( $row['best_month'] ) ); ?></td>
							<td><?php echo esc_html( $row['non_stop_available'] ? 'Diretto' : 'Con scalo' ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php
get_footer();
