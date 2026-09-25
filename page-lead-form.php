<?php
/**
 * Dedicated template for the "Lead Form" page (slug: lead-form).
 *
 * Renders the customer information form directly via PHP instead of
 * through the_content(), so it's never affected by Elementor's content
 * override (which serves stale _elementor_data instead of live PHP
 * output) and the form's security nonce + submit-success detection are
 * always generated fresh on every request, never baked into stored
 * page content.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main>
<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-form"/></svg></div>
  <span class="eyebrow">যোগাযোগ</span>
  <h1>আপনার তথ্য জানান</h1>
  <p>নিচের ফর্মটি পূরণ করুন — সব ফিল্ড ঐচ্ছিক। আমাদের টিম শীঘ্রই আপনার সাথে যোগাযোগ করবে।</p>
</div></div>
<section class="tight wrap">
  <?php echo womensfight_render_customer_form(); ?>
</section>
</main>
<?php
get_footer();
