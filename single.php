<?php
/**
 * Default single template.
 *
 * @package ConfrontaPrezziVoli
 */

get_header();
?>
<section class="section">
	<div class="shell prose">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="cpv-article">
				<p class="eyebrow"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="cpv-article__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php
get_footer();
