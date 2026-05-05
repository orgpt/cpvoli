<?php
/**
 * Fallback blog/archive template.
 *
 * @package ConfrontaPrezziVoli
 */

get_header();
?>
<section class="section">
	<div class="shell prose">
		<p class="eyebrow">Travel Guide</p>
		<h1>Guide voli e bagagli per chi parte dall'Italia</h1>
		<p>Questa area ospita contenuti editoriali come guide low cost, mete stagionali e regole bagaglio 2026. Se non hai ancora articoli WordPress pubblicati, il tema resta pronto e usa questa pagina come placeholder editoriale.</p>
		<?php if ( have_posts() ) : ?>
			<div class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="post-card">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( get_the_excerpt() ?: wp_trim_words( wp_strip_all_tags( get_the_content() ), 28 ) ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();

