<?php
/**
 * Airline page template.
 *
 * @package ConfrontaPrezziVoli
 */

$airline = CPV_Data::get_airline_by_slug( (string) get_query_var( 'cpv_airline' ) );
if ( ! $airline ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( '404' );
	return;
}

$compare_target = CPV_Data::get_airline_by_slug( 'wizz-air' );
if ( 'wizz-air' === $airline['slug'] ) {
	$compare_target = CPV_Data::get_airline_by_slug( 'ryanair' );
}

get_header();
?>
<section class="hero hero--compact">
	<div class="shell hero__grid hero__grid--single">
		<div>
			<p class="eyebrow">Airline Guide</p>
			<h1><?php echo esc_html( $airline['display_name'] ); ?>: bagagli, check-in e rotte dall’Italia</h1>
			<p class="hero__lede">Contenuto evergreen per keyword informative e funzionali, utile sia in SERP sia nella fase di conversione.</p>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="triple-grid">
			<div class="panel">
				<h2>Regole bagaglio</h2>
				<p><?php echo esc_html( ! empty( $airline['is_lowcost'] ) ? 'Tariffa base da verificare sempre: dimensioni bagaglio e posti prioritari incidono molto sul prezzo finale.' : 'Controlla sempre franchigia a mano e opzioni flex, spesso più generose rispetto ai vettori ultra low cost.' ); ?></p>
			</div>
			<div class="panel">
				<h3>Check-in per italiani</h3>
				<p>Consigli su documenti, tempi al gate, app mobile e differenze tra aeroporto e check-in online.</p>
			</div>
			<div class="panel">
				<h3>Rotte top</h3>
				<p><?php echo esc_html( ! empty( $airline['is_lowcost'] ) ? 'Weekend europei, leisure e city break ad alta sensibilità di prezzo.' : 'Rotte nazionali, business e connessioni medio-lungo raggio.' ); ?></p>
			</div>
		</div>

		<?php if ( $compare_target ) : ?>
			<div class="cta-banner">
				<div>
					<h2>Vuoi confrontarla con <?php echo esc_html( $compare_target['display_name'] ); ?>?</h2>
					<p>Pagina pSEO ideale per query comparative ad alta intenzione.</p>
				</div>
				<a class="button button--primary" href="<?php echo esc_url( home_url( '/confronto/' . $airline['slug'] . '-vs-' . $compare_target['slug'] . '/' ) ); ?>">Apri confronto</a>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

