<?php
/**
 * Front page template. WordPress always uses this file for the site
 * root ("/"), regardless of the Reading setting, so it needs to print
 * whichever content the front page actually is — the "Home" Page this
 * theme creates on activation, editable the same way as any other page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main>
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
else :
	// Reading settings haven't been pointed at the Home page yet.
	$home = get_page_by_path( 'home' );
	if ( $home ) {
		echo apply_filters( 'the_content', $home->post_content );
	}
endif;
?>
</main>
<?php
get_footer();
