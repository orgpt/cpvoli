<?php
/**
 * 404 template.
 *
 * @package ConfrontaPrezziVoli
 */

get_header();
?>
<section class="section">
	<div class="shell prose">
		<h1>Pagina non trovata</h1>
		<p>La rotta richiesta non e disponibile o il relativo hub non e stato ancora generato.</p>
		<a class="button button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">Torna alla home</a>
	</div>
</section>
<?php
get_footer();

