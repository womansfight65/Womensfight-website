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
	// Reading settings haven't been pointed at the Home page yet. Use
	// setup_postdata() (not a bare apply_filters call) so the global
	// $post and post ID are correctly set to the Home page before
	// the_content() runs — filters that key off the current post
	// (Elementor's rendering included) need that to find the right page.
	$home = get_page_by_path( 'home' );
	if ( $home ) {
		setup_postdata( $home );
		the_content();
		wp_reset_postdata();
	}
endif;
?>
</main>
<?php
get_footer();
