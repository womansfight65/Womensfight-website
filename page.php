<?php
/**
 * Generic page template — used for every WordPress Page (Digital
 * Marketing, Website Development, Landing Page, Video Production,
 * AI Agency, Case Study, Portfolio, Action Plan, Package, AI Agent,
 * Contact Us). Just prints the page's real, editable content — from the
 * block editor, or from Elementor once it's installed and used on the
 * page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main>
<?php
while ( have_posts() ) :
	the_post();
	the_content();
endwhile;
?>
</main>
<?php
get_footer();
