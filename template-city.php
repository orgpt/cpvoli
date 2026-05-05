<?php
/**
 * City hub template.
 *
 * @package ConfrontaPrezziVoli
 */

$city = CPV_Data::get_city_by_slug( (string) get_query_var( 'cpv_city' ) );
if ( ! $city ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( '404' );
	return;
}

$deals = CPV_Data::get_city_deals( $city );

get_header();
?>
<section class="hero hero--compact">
	<div class="shell hero__grid hero__grid--single">
		<div>
			<p class="eyebrow">City Hub</p>
			<h1>Voli per <?php echo esc_html( $city['display_name'] ); ?></h1>
			<p class="hero__lede">Pagina hub per presidiare keyword come “voli per <?php echo esc_html( $city['display_name'] ); ?>” con tabelle dinamiche da aeroporti italiani.</p>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="table-card">
			<div class="table-card__header">
				<h2>Prezzi minimi dai principali aeroporti italiani</h2>
				<p>Perfetta per scalare su migliaia di combinazioni origine-destinazione.</p>
			</div>
			<table class="data-table">
				<thead>
					<tr>
						<th>Partenza</th>
						<th>Prezzo</th>
						<th>Durata</th>
						<th>Mese migliore</th>
						<th>Dettaglio</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $deals as $deal ) : ?>
						<tr>
							<td><?php echo esc_html( $deal['origin']['display_name'] ); ?></td>
							<td><?php echo esc_html( CPV_Data::format_price( $deal['price'] ) ); ?></td>
							<td><?php echo esc_html( $deal['flight_duration'] ); ?></td>
							<td><?php echo esc_html( ucfirst( $deal['best_month'] ) ); ?></td>
							<td><a href="<?php echo esc_url( home_url( '/voli/' . $deal['origin']['slug'] . '-a-' . $city['slug'] . '/' ) ); ?>">Vedi rotta</a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php
get_footer();

