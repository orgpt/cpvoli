<?php
/**
 * Airline comparison template.
 *
 * @package ConfrontaPrezziVoli
 */

$compare_parts = explode( '__vs__', (string) get_query_var( 'cpv_compare' ) );
$left          = CPV_Data::get_airline_by_slug( $compare_parts[0] ?? '' );
$right         = CPV_Data::get_airline_by_slug( $compare_parts[1] ?? '' );

if ( ! $left || ! $right ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( '404' );
	return;
}

$comparison = CPV_Data::compare_airlines( $left, $right );

get_header();
?>
<section class="hero hero--compact">
	<div class="shell hero__grid hero__grid--single">
		<div>
			<p class="eyebrow">Comparison Page</p>
			<h1><?php echo esc_html( $left['display_name'] ); ?> vs <?php echo esc_html( $right['display_name'] ); ?></h1>
			<p class="hero__lede">Confronto pensato per query ad alta intenzione: commissioni, posti, tipologia di rotte e praticità per chi parte dagli aeroporti italiani.</p>
		</div>
	</div>
</section>

<section class="section">
	<div class="shell">
		<div class="table-card">
			<table class="data-table">
				<thead>
					<tr>
						<th>Voce</th>
						<th><?php echo esc_html( $left['display_name'] ); ?></th>
						<th><?php echo esc_html( $right['display_name'] ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $comparison['rows'] as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row['label'] ); ?></td>
							<td><?php echo esc_html( $row['left'] ); ?></td>
							<td><?php echo esc_html( $row['right'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="panel">
			<h2>Takeaway</h2>
			<p><?php echo esc_html( $comparison['recommendation'] ); ?></p>
		</div>
	</div>
</section>
<?php
get_footer();

