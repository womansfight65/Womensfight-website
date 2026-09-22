<?php
/**
 * Fallback template (used only if WordPress can't find a more specific
 * one — this theme's real content lives on the Pages it creates).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main class="wrap tight">
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<article style="padding-block:24px;border-bottom:1px solid var(--border);">
			<h2><a href="<?php the_permalink(); ?>" style="text-decoration:none;"><?php the_title(); ?></a></h2>
			<div><?php the_excerpt(); ?></div>
		</article>
		<?php
	endwhile;
else :
	echo '<p>কিছু পাওয়া যায়নি।</p>';
endif;
?>
</main>
<?php
get_footer();
