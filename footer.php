<?php
/**
 * Theme footer.
 *
 * @package ConfrontaPrezziVoli
 */

$footer_airport = CPV_Data::get_airport_by_code( 'MXP' );
$footer_city    = CPV_Data::get_city_by_slug( 'londra' );
$footer_left    = CPV_Data::get_airline_by_slug( 'ryanair' );
$footer_right   = CPV_Data::get_airline_by_slug( 'wizz-air' );
?>
</main>
<?php cpv_render_promo_calendar(); ?>
<footer class="site-footer">
	<div class="shell site-footer__grid">
		<div>
			<h2>ConfrontaPrezziVoli.it</h2>
			<p>Motore editoriale e comparatore voli focalizzato sulle partenze dagli aeroporti italiani.</p>
		</div>
		<div>
			<h3>SEO Silos</h3>
			<ul>
				<li><a href="<?php echo esc_url( $footer_airport ? home_url( '/voli-da/' . $footer_airport['slug'] . '/' ) : home_url( '/' ) ); ?>">Voli da Milano</a></li>
				<li><a href="<?php echo esc_url( ( $footer_airport && $footer_city ) ? home_url( '/voli/' . $footer_airport['slug'] . '-a-' . $footer_city['slug'] . '/' ) : home_url( '/' ) ); ?>">Rotte low cost</a></li>
				<li><a href="<?php echo esc_url( $footer_right ? home_url( '/compagnie-aeree/' . $footer_right['slug'] . '/' ) : home_url( '/' ) ); ?>">Guide compagnie</a></li>
			</ul>
		</div>
		<div>
			<h3>Conversione</h3>
			<ul>
				<li>Alert WhatsApp e Telegram</li>
				<li>Calendario flessibile prezzi</li>
				<li><?php echo esc_html( ( $footer_left && $footer_right ) ? $footer_left['display_name'] . ' vs ' . $footer_right['display_name'] : 'Schema e metadata italiani' ); ?></li>
			</ul>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
