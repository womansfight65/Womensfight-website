<?php
/**
 * Demo Website Library (slug: demo-library).
 *
 * Rendered directly via PHP (not the_content()) so it bypasses
 * Elementor's content override and always queries the current list of
 * "Demo Websites" entries plus a fresh nonce on the request form —
 * same pattern as page-lead-form.php / page-contact.php / page-project.php.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wf_demos      = womensfight_get_demo_list();
$wf_categories = womensfight_demo_categories();

get_header();
?>
<main>
<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-grid"/></svg></div>
  <span class="eyebrow">Demo Library</span>
  <h1>ওয়েবসাইট ডিজাইন বেছে নিন</h1>
  <p>নিচের থেকে পছন্দের ডিজাইন দেখুন, Live Preview করুন, আর যেটা পছন্দ হবে সেটা সিলেক্ট করে অনুরোধ পাঠান।</p>
</div></div>

<section class="tight wrap" id="wf-demo-library">

  <?php if ( isset( $_GET['demo_submitted'] ) ) : ?>
    <div class="wf-cform-success" style="margin-bottom:28px; padding:22px;">
      <p>ধন্যবাদ। আপনার পছন্দের ডিজাইন অনুরোধ আমরা পেয়েছি — শীঘ্রই যোগাযোগ করব।</p>
    </div>
  <?php endif; ?>

  <div class="wf-demo-filters">
    <button type="button" class="wf-demo-filter active" data-filter="All">All</button>
    <?php foreach ( $wf_categories as $cat ) : ?>
      <button type="button" class="wf-demo-filter" data-filter="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></button>
    <?php endforeach; ?>
  </div>

  <?php if ( ! $wf_demos ) : ?>

    <p style="color:var(--ink-faint); margin-top:24px;">শীঘ্রই ডেমো যুক্ত হবে।</p>

  <?php else : ?>

  <div class="wf-demo-grid">
    <?php foreach ( $wf_demos as $wf_demo ) :
		$wf_pid_meta  = get_post_meta( $wf_demo->ID, 'wf_d_project_id', true );
		$wf_cat       = get_post_meta( $wf_demo->ID, 'wf_d_category', true );
		$wf_desc      = get_post_meta( $wf_demo->ID, 'wf_d_description', true );
		$wf_live_url  = get_post_meta( $wf_demo->ID, 'wf_d_live_url', true );
		$wf_shot_id   = get_post_meta( $wf_demo->ID, 'wf_d_screenshot_id', true );
		$wf_demo_name = get_the_title( $wf_demo->ID );
		?>
      <div class="wf-demo-card" data-category="<?php echo esc_attr( $wf_cat ); ?>">
        <div class="wf-demo-shot">
          <?php if ( $wf_shot_id ) : ?>
            <?php echo wp_get_attachment_image( $wf_shot_id, 'medium_large' ); ?>
          <?php else : ?>
            <div class="wf-demo-shot-placeholder"><svg><use href="#i-grid"/></svg></div>
          <?php endif; ?>
          <?php if ( $wf_cat ) : ?><span class="wf-demo-tag"><?php echo esc_html( $wf_cat ); ?></span><?php endif; ?>
        </div>
        <div class="wf-demo-body">
          <h3><?php echo esc_html( $wf_demo_name ); ?></h3>
          <?php if ( $wf_pid_meta ) : ?><div class="wf-demo-pid">Project ID: <?php echo esc_html( $wf_pid_meta ); ?></div><?php endif; ?>
          <?php if ( $wf_desc ) : ?><p><?php echo esc_html( $wf_desc ); ?></p><?php endif; ?>
          <div class="wf-demo-actions">
            <?php if ( $wf_live_url ) : ?>
              <a class="btn btn-ghost" href="<?php echo esc_url( $wf_live_url ); ?>" target="_blank" rel="noopener">Live Preview</a>
            <?php endif; ?>
            <button type="button" class="btn btn-primary" onclick="wfOpenDemoRequest('<?php echo esc_js( $wf_demo_name ); ?>','<?php echo esc_js( $wf_pid_meta ); ?>');">Choose This Design</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php endif; ?>

</section>

<div class="wf-modal-overlay" id="wfDemoModalOverlay" onclick="if(event.target===this){wfCloseDemoRequest();}">
  <div class="wf-modal">
    <button type="button" class="wf-modal-close" onclick="wfCloseDemoRequest();" aria-label="Close">&times;</button>
    <div class="wf-cform">
      <h3 style="margin:0 0 6px;">এই ডিজাইনটা নিতে চান?</h3>
      <p class="wf-dash-hint">নিচের তথ্য দিন, আমরা দ্রুত যোগাযোগ করব।</p>
      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="womensfight_submit_demo_request">
        <input type="hidden" name="dr_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
        <input type="hidden" name="dr_demo_name" id="wfDemoNameField" value="">
        <input type="hidden" name="dr_project_id" id="wfDemoProjectIdField" value="">
        <?php wp_nonce_field( 'womensfight_demo_request', 'womensfight_demo_request_nonce' ); ?>
        <div><label>Selected Demo</label><input type="text" id="wfDemoNameDisplay" readonly></div>
        <div class="frow">
          <div><label for="dr_name">নাম</label><input type="text" id="dr_name" name="dr_name" placeholder="আপনার নাম"></div>
          <div><label for="dr_whatsapp">WhatsApp</label><input type="tel" id="dr_whatsapp" name="dr_whatsapp" placeholder="01XXXXXXXXX"></div>
        </div>
        <div><label for="dr_business">Business Name</label><input type="text" id="dr_business" name="dr_business" placeholder="আপনার ব্যবসার নাম"></div>
        <div><label for="dr_requirement">Requirement / Changes</label><textarea id="dr_requirement" name="dr_requirement" rows="3" placeholder="এই ডিজাইনে কী পরিবর্তন চান (ঐচ্ছিক)"></textarea></div>
        <button class="btn btn-primary" type="submit">অনুরোধ পাঠান</button>
      </form>
    </div>
  </div>
</div>

<script>
function wfOpenDemoRequest(name, pid) {
	document.getElementById('wfDemoNameField').value = name;
	document.getElementById('wfDemoProjectIdField').value = pid;
	document.getElementById('wfDemoNameDisplay').value = name + (pid ? ' (' + pid + ')' : '');
	document.getElementById('wfDemoModalOverlay').classList.add('open');
	document.body.style.overflow = 'hidden';
}
function wfCloseDemoRequest() {
	document.getElementById('wfDemoModalOverlay').classList.remove('open');
	document.body.style.overflow = '';
}
document.querySelectorAll('.wf-demo-filter').forEach(function(btn){
	btn.addEventListener('click', function(){
		document.querySelectorAll('.wf-demo-filter').forEach(function(b){ b.classList.remove('active'); });
		this.classList.add('active');
		var filter = this.getAttribute('data-filter');
		document.querySelectorAll('.wf-demo-card').forEach(function(card){
			var show = (filter === 'All' || card.getAttribute('data-category') === filter);
			card.style.display = show ? '' : 'none';
		});
	});
});
</script>
</main>
<?php
get_footer();
