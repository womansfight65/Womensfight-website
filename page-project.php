<?php
/**
 * Client Dashboard — private, token-only access (slug: project).
 *
 * No WordPress login: the ?token=... in the URL is the only key. Looked
 * up fresh via womensfight_get_project_by_token() on every request, so
 * nonces and the onboarding/revision/approval state are always current
 * — never baked into stored page content, and bypasses Elementor's
 * content override the same way page-lead-form.php and page-contact.php
 * do.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wf_token   = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';
$wf_project = womensfight_get_project_by_token( $wf_token );

get_header();
?>
<main>
<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-users"/></svg></div>
  <span class="eyebrow">ক্লায়েন্ট ড্যাশবোর্ড</span>
  <h1>আপনার প্রজেক্ট</h1>
</div></div>

<section class="tight wrap" id="wf-dash">
<?php if ( ! $wf_project ) : ?>

  <div class="wf-cform-success">
    <p>এই লিংকটি সঠিক নয় বা মেয়াদোত্তীর্ণ। সঠিক লিংকের জন্য আমাদের সাথে যোগাযোগ করুন।</p>
  </div>

<?php else :
	$wf_pid      = $wf_project->ID;
	$statuses    = womensfight_project_statuses();
	$payments    = womensfight_project_payment_statuses();
	$status      = get_post_meta( $wf_pid, 'wf_p_status', true );
	$payment     = get_post_meta( $wf_pid, 'wf_p_payment_status', true );
	$onboarded   = get_post_meta( $wf_pid, 'wf_p_onboarding_submitted', true );
	$approved    = get_post_meta( $wf_pid, 'wf_p_client_approved', true );
	$deliverables = get_post_meta( $wf_pid, 'wf_p_deliverable', false );
	$revisions    = get_post_meta( $wf_pid, 'wf_p_revision', false );
	?>

  <?php if ( isset( $_GET['submitted'] ) ) : ?>
    <div class="wf-cform-success" style="margin-bottom:28px; padding:24px;">
      <p>
        <?php
        if ( 'onboarding' === $_GET['submitted'] ) {
			echo 'ধন্যবাদ — আপনার Onboarding তথ্য জমা হয়েছে।';
		} elseif ( 'revision' === $_GET['submitted'] ) {
			echo 'ধন্যবাদ — আপনার Revision Request জমা হয়েছে, আমরা দেখে নেব।';
		} elseif ( 'approved' === $_GET['submitted'] ) {
			echo 'ধন্যবাদ — আপনি প্রজেক্ট অ্যাপ্রুভ করেছেন।';
		}
		?>
      </p>
    </div>
  <?php endif; ?>

  <div class="pkg" style="margin-bottom:24px;">
    <h3><?php echo esc_html( get_the_title( $wf_pid ) ); ?></h3>
    <p style="color:var(--ink-faint);"><?php echo esc_html( get_post_meta( $wf_pid, 'wf_p_service', true ) ); ?></p>
    <div class="frow" style="margin-top:10px;">
      <div><label>Project Status</label><div class="price" style="font-size:1.05rem;"><?php echo esc_html( isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status ); ?></div></div>
      <div><label>Payment Status</label><div class="price" style="font-size:1.05rem;"><?php echo esc_html( isset( $payments[ $payment ] ) ? $payments[ $payment ] : $payment ); ?></div></div>
    </div>
  </div>

  <?php if ( ! $onboarded ) : ?>
  <div class="wf-cform" style="margin-bottom:28px;">
    <h3 style="margin:0 0 6px;">Onboarding তথ্য দিন</h3>
    <p style="color:var(--ink-faint); font-size:.88rem; margin:0 0 6px;">প্রজেক্ট শুরু করতে নিচের তথ্যগুলো পূরণ করুন।</p>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
      <input type="hidden" name="action" value="womensfight_submit_onboarding">
      <input type="hidden" name="wf_token" value="<?php echo esc_attr( $wf_token ); ?>">
      <?php wp_nonce_field( 'womensfight_onboarding_' . $wf_token, 'womensfight_onboarding_nonce' ); ?>
      <div><label for="wf_p_onboarding_address">ঠিকানা</label><textarea id="wf_p_onboarding_address" name="wf_p_onboarding_address" rows="2"></textarea></div>
      <div><label for="wf_p_onboarding_assets_link">Brand Assets Link (লোগো/ছবি, যদি থাকে)</label><input type="url" id="wf_p_onboarding_assets_link" name="wf_p_onboarding_assets_link" placeholder="Google Drive / Dropbox লিংক"></div>
      <div><label for="wf_p_onboarding_access_notes">প্রয়োজনীয় Access/Login তথ্য (যদি লাগে)</label><textarea id="wf_p_onboarding_access_notes" name="wf_p_onboarding_access_notes" rows="2" placeholder="যেমন: ওয়েবসাইট হোস্টিং প্যানেল, ইত্যাদি"></textarea></div>
      <div><label for="wf_p_onboarding_notes">অতিরিক্ত নোট</label><textarea id="wf_p_onboarding_notes" name="wf_p_onboarding_notes" rows="3"></textarea></div>
      <button class="btn btn-primary" type="submit">জমা দিন</button>
    </form>
  </div>
  <?php else : ?>
  <div class="wf-cform-success" style="margin-bottom:28px; padding:20px;">
    <p>✅ Onboarding তথ্য জমা দেওয়া হয়ে গেছে।</p>
  </div>
  <?php endif; ?>

  <div style="margin-bottom:28px;">
    <h3>Deliverable Files</h3>
    <?php if ( $deliverables ) : ?>
      <ul style="display:flex; flex-direction:column; gap:8px; padding-left:0; list-style:none;">
        <?php foreach ( $deliverables as $attachment_id ) : ?>
          <li>
            <a class="btn btn-ghost" href="<?php echo esc_url( wp_get_attachment_url( $attachment_id ) ); ?>" target="_blank" rel="noopener">
              📄 <?php echo esc_html( basename( get_attached_file( $attachment_id ) ) ); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else : ?>
      <p style="color:var(--ink-faint);">এখনো কোনো ফাইল দেওয়া হয়নি — কাজ শেষ হলে এখানে দেখতে পাবেন।</p>
    <?php endif; ?>
  </div>

  <div class="wf-cform" style="margin-bottom:28px;">
    <h3 style="margin:0 0 6px;">Revision Request</h3>
    <p style="color:var(--ink-faint); font-size:.88rem; margin:0 0 6px;">কোনো পরিবর্তন দরকার হলে এখানে জানান।</p>
    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
      <input type="hidden" name="action" value="womensfight_submit_revision">
      <input type="hidden" name="wf_token" value="<?php echo esc_attr( $wf_token ); ?>">
      <?php wp_nonce_field( 'womensfight_revision_' . $wf_token, 'womensfight_revision_nonce' ); ?>
      <div><label for="wf_revision_note">কী পরিবর্তন দরকার?</label><textarea id="wf_revision_note" name="wf_revision_note" rows="3" required></textarea></div>
      <div><label for="wf_revision_file">ফাইল সংযুক্ত করুন (ঐচ্ছিক)</label><input type="file" id="wf_revision_file" name="wf_revision_file"></div>
      <button class="btn btn-primary" type="submit">Revision Request পাঠান</button>
    </form>

    <?php if ( $revisions ) : ?>
      <h4 style="margin-top:20px;">আগের Revision Request</h4>
      <div style="display:flex; flex-direction:column; gap:10px;">
        <?php foreach ( array_reverse( $revisions ) as $revision ) : ?>
          <div style="border:1px solid var(--border); border-radius:12px; padding:12px 14px;">
            <div style="font-size:.78rem; color:var(--ink-faint); margin-bottom:4px;"><?php echo esc_html( $revision['date'] ); ?></div>
            <div><?php echo nl2br( esc_html( $revision['note'] ) ); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="wf-cform" style="text-align:center;">
    <?php if ( $approved ) : ?>
      <p style="font-size:1.05rem; font-weight:700;">✅ আপনি এই প্রজেক্ট অ্যাপ্রুভ করেছেন (<?php echo esc_html( get_post_meta( $wf_pid, 'wf_p_approved_at', true ) ); ?>)।</p>
    <?php else : ?>
      <h3 style="margin:0 0 10px;">সব ঠিক আছে?</h3>
      <p style="color:var(--ink-faint); font-size:.88rem; margin:0 0 14px;">কাজ চূড়ান্তভাবে গ্রহণযোগ্য হলে নিচের বাটনে ক্লিক করুন।</p>
      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('আপনি কি নিশ্চিত এই প্রজেক্ট চূড়ান্তভাবে অ্যাপ্রুভ করতে চান?');">
        <input type="hidden" name="action" value="womensfight_submit_approval">
        <input type="hidden" name="wf_token" value="<?php echo esc_attr( $wf_token ); ?>">
        <?php wp_nonce_field( 'womensfight_approval_' . $wf_token, 'womensfight_approval_nonce' ); ?>
        <button class="btn btn-primary" type="submit">প্রজেক্ট অ্যাপ্রুভ করুন</button>
      </form>
    <?php endif; ?>
  </div>

<?php endif; ?>
</section>
</main>
<?php
get_footer();
