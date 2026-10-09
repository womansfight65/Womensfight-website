<?php
/**
 * SMM Service Request landing page (slug: smm-service).
 *
 * Single public landing page — no dashboard, no customer login, no
 * pricing/checkout. Visitors pick a platform + service and submit a
 * request; our team follows up manually (see inc/smm/smm-core.php for
 * the backend: a plain wf_smm_request post type + admin-post.php
 * handler, same pattern as Contact Messages).
 *
 * Rendered directly via PHP (not the_content()) so it always gets a
 * fresh nonce and a live ?smm_submitted/?smm_error check on every
 * request — same reasoning as page-contact.php.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_head',
	function () {
		?>
<style>
.smm-hero-visual{position:relative; margin-top:36px; max-width:460px;}
.smm-orbit-card{background:var(--surface); border:1px solid var(--border); border-radius:22px; box-shadow:var(--shadow); padding:26px; position:relative; z-index:1;}
.smm-orbit-card h4{font-size:.8rem; color:var(--ink-faint); text-transform:uppercase; letter-spacing:.04em; margin-bottom:16px;}
.smm-orbit-row{display:flex; align-items:center; gap:14px; padding:12px 0; border-top:1px solid var(--border);}
.smm-orbit-row:first-of-type{border-top:none;}
.smm-orbit-badge{width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex:none;}
.smm-orbit-badge svg{width:20px; height:20px; color:#fff;}
.smm-orbit-row b{display:block; font-size:.88rem;}
.smm-orbit-row span{font-size:.76rem; color:var(--ink-faint);}
.smm-orbit-bar{height:6px; border-radius:999px; background:var(--bg-2); margin-top:6px; overflow:hidden;}
.smm-orbit-bar i{display:block; height:100%; background:var(--grad); border-radius:999px;}
.smm-hero-visual::before{content:""; position:absolute; width:260px; height:260px; background:var(--grad); opacity:.28; filter:blur(70px); border-radius:50%; top:-60px; right:-60px; z-index:0;}

.smm-hl{background:linear-gradient(90deg,#d6336c,#6c4fd6 60%,#1d4ed8); -webkit-background-clip:text; background-clip:text; color:transparent; font-weight:700;}
.smm-highlight-text{font-weight:700; font-size:1.05rem; margin-top:14px;}

.smm-trust-row{display:flex; gap:18px; flex-wrap:wrap; margin-top:22px;}
.smm-trust-row span{display:inline-flex; align-items:center; gap:7px; font-size:.82rem; font-weight:700; color:var(--ink-faint);}
.smm-trust-row svg{width:16px; height:16px; stroke:var(--pink-light); fill:none; stroke-width:2;}

.smm-platform-grid{display:grid; grid-template-columns:repeat(5,1fr); gap:16px;}
.smm-platform-card{background:var(--surface); border:1px solid var(--border); border-top:3px solid transparent; border-radius:18px; padding:26px 14px; text-align:center; cursor:pointer; transition:transform .18s ease, border-color .18s ease, box-shadow .18s ease; display:flex; flex-direction:column; align-items:center; gap:12px;}
.smm-platform-card:hover{transform:translateY(-4px); border-top-color:var(--pink); box-shadow:0 16px 30px -18px rgba(0,0,0,.6);}
.smm-platform-card.active{border-color:var(--pink); border-top-color:var(--pink); box-shadow:var(--glow-pink);}
.smm-platform-badge{width:52px; height:52px; border-radius:16px; display:flex; align-items:center; justify-content:center;}
.smm-platform-badge svg{width:26px; height:26px; color:#fff;}
.smm-platform-card span{font-size:.86rem; font-weight:700;}
.smm-platform-card[data-platform="facebook"] .smm-platform-badge{background:#1877f2;}
.smm-platform-card[data-platform="instagram"] .smm-platform-badge{background:linear-gradient(135deg,#f58529,#dd2a7b 50%,#8134af);}
.smm-platform-card[data-platform="youtube"] .smm-platform-badge{background:#ff0000;}
.smm-platform-card[data-platform="tiktok"] .smm-platform-badge{background:#000;}
.smm-platform-card[data-platform="telegram"] .smm-platform-badge{background:#26a5e4;}
.smm-platform-card[data-platform="x"] .smm-platform-badge{background:#000;}
.smm-platform-card[data-platform="linkedin"] .smm-platform-badge{background:#0a66c2;}
.smm-platform-card[data-platform="spotify"] .smm-platform-badge{background:#1db954;}
.smm-platform-card[data-platform="snapchat"] .smm-platform-badge{background:#fffc00;}
.smm-platform-card[data-platform="snapchat"] .smm-platform-badge svg{color:#000;}
.smm-platform-card[data-platform="discord"] .smm-platform-badge{background:#5865f2;}

.smm-services-panel{margin-top:28px; background:var(--bg-2); border:1px solid var(--border); border-radius:var(--radius-lg); padding:30px; display:none;}
.smm-services-panel.show{display:block;}
.smm-services-panel h3{font-size:1.1rem; margin-bottom:18px;}
.smm-service-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:14px;}
.smm-service-card{background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:18px; display:flex; flex-direction:column; gap:12px; transition:border-color .15s ease;}
.smm-service-card:hover{border-color:var(--pink);}
.smm-service-card b{font-size:.92rem;}
.smm-service-card button{align-self:flex-start;}
.smm-service-ico{width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:var(--grad);}
.smm-service-ico svg{width:18px; height:18px; stroke:#fff; fill:none; stroke-width:1.9; stroke-linecap:round; stroke-linejoin:round;}

.smm-steps{display:grid; grid-template-columns:repeat(3,1fr); gap:20px;}
.smm-step{background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:28px; position:relative;}
.smm-step span.num{display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:50%; background:var(--grad); color:#fff; font-weight:700; margin-bottom:14px;}
.smm-step h3{font-size:1.02rem; margin-bottom:8px;}

.smm-form-note{font-size:.8rem; color:var(--ink-faint); margin-top:12px;}
.smm-field-error{font-size:.78rem; color:var(--pink-light); margin-top:-10px; margin-bottom:4px; display:none;}
.smm-field-error.show{display:block;}
.smm-hp{position:absolute; left:-9999px; top:-9999px;}

@media (max-width:980px){
  .smm-platform-grid{grid-template-columns:repeat(3,1fr);}
  .smm-service-grid{grid-template-columns:1fr 1fr;}
  .smm-steps{grid-template-columns:1fr;}
  .smm-hero-visual{display:none;}
}
@media (max-width:640px){
  .smm-platform-grid{grid-template-columns:1fr 1fr;}
  .smm-service-grid{grid-template-columns:1fr;}
}

/* A distinct, lighter look for just this page's main content — the
   rest of the site (header/footer/nav) stays the normal dark theme,
   only #smm-page overrides colors for its own children. */
#smm-page{background:linear-gradient(180deg,#f5f3fc 0%,#ece7fb 45%,#ddd4f7 100%); padding-bottom:1px; overflow-x:hidden;}
#smm-page h1, #smm-page h2, #smm-page h3{color:#2c1f5e;}
#smm-page p{color:#5c537a;}
#smm-page .smm-highlight-text{color:#2c1f5e;}
#smm-page .eyebrow{color:#6c4fd6;}
#smm-page .eyebrow::before{background:linear-gradient(90deg,#6c4fd6,#a78bfa);}
#smm-page .page-header{border-bottom:1px solid #e2d9f7;}
#smm-page .page-header::before{background:linear-gradient(120deg,#8b5cf6,#6c4fd6); opacity:.18;}
#smm-page .ico svg{stroke:#6c4fd6;}
#smm-page .smm-trust-row span{color:#4b3a8a;}
#smm-page .smm-trust-row svg{stroke:#6c4fd6;}
#smm-page .feat, #smm-page .smm-platform-card, #smm-page .smm-step, #smm-page .smm-service-card, #smm-page .faq details, #smm-page .wf-cform, #smm-page .wf-cform-success, #smm-page .smm-orbit-card, #smm-page .smm-overview-wrap{background:#fff; border-color:#e5defa; box-shadow:0 14px 34px -22px rgba(76,55,150,.35);}
#smm-page .feat .ico.sm svg{stroke:#6c4fd6;}
#smm-page section.alt{background:#efe9fb;}
#smm-page label{color:#7a71a0;}
#smm-page .smm-form-note{color:#7a71a0;}
#smm-page input, #smm-page textarea, #smm-page select{background:#faf8ff; border-color:#e2d9f7; color:#2c1f5e;}
#smm-page input::placeholder, #smm-page textarea::placeholder{color:#a79dc9;}
#smm-page .btn-primary{background:linear-gradient(135deg,#6c4fd6,#9333ea); color:#fff; box-shadow:0 12px 24px -10px rgba(108,79,214,.55);}
#smm-page .btn-ghost{border-color:#d9cdf5; color:#4b3a8a; background:#fff;}
#smm-page .btn-ghost:hover{border-color:#6c4fd6;}
#smm-page .smm-platform-card{border-top-color:transparent;}
#smm-page .smm-platform-card:hover{border-top-color:#6c4fd6; box-shadow:0 16px 30px -18px rgba(76,55,150,.4);}
#smm-page .smm-platform-card.active{border-color:#6c4fd6; border-top-color:#6c4fd6; box-shadow:0 0 0 3px rgba(108,79,214,.15);}
#smm-page .smm-platform-card span{color:#2c1f5e;}
#smm-page .smm-services-panel{background:#efe9fb; border-color:#e2d9f7;}
#smm-page .smm-service-card b{color:#2c1f5e;}
#smm-page .smm-service-ico{background:linear-gradient(135deg,#6c4fd6,#9333ea);}
#smm-page .smm-orbit-row b{color:#2c1f5e;}
#smm-page .smm-orbit-row span{color:#7a71a0;}
#smm-page .smm-orbit-card h4{color:#9388b8;}
#smm-page .smm-orbit-row{border-top-color:#eee6fb;}
#smm-page .smm-step span.num{background:linear-gradient(135deg,#6c4fd6,#9333ea);}
#smm-page .faq summary{color:#2c1f5e;}
#smm-page .faq summary::after{color:#6c4fd6;}
#smm-page .cta-band{background:linear-gradient(135deg,#6c4fd6,#4a2f9e);}
#smm-page .cta-band::before{background:rgba(147,51,234,.35);}
#smm-page .cta-band::after{background:rgba(108,79,214,.3);}
#smm-page .cta-band h2{color:#fff;}
#smm-page .cta-band p{color:rgba(255,255,255,.82);}
#smm-page .cta-band .btn-primary{background:#fff; color:#4a2f9e; box-shadow:none;}

.smm-overview-wrap{overflow-x:auto; border-radius:var(--radius-lg); margin-top:36px;}
.smm-overview-table{width:100%; border-collapse:collapse; min-width:640px;}
.smm-overview-table thead th{background:linear-gradient(90deg,#6c4fd6,#8b5cf6); color:#fff; text-align:left; padding:16px 20px; font-size:.86rem; font-weight:700;}
.smm-overview-table thead th:first-child{border-top-left-radius:var(--radius-lg);}
.smm-overview-table thead th:last-child{border-top-right-radius:var(--radius-lg);}
.smm-overview-table tbody td{padding:16px 20px; border-top:1px dashed #e5defa; font-size:.88rem; color:#4b3a8a;}
.smm-overview-table tbody td:first-child{font-weight:700; color:#2c1f5e; white-space:nowrap;}
</style>
		<?php
	}
);

get_header();

$submitted   = isset( $_GET['smm_submitted'] ) && '1' === $_GET['smm_submitted'];
$error_codes = isset( $_GET['smm_error'] ) ? array_filter( explode( ',', sanitize_text_field( wp_unslash( $_GET['smm_error'] ) ) ) ) : array();
$error_labels = array(
	'name'     => 'আপনার নাম লিখুন।',
	'mobile'   => 'সঠিক বাংলাদেশি মোবাইল নম্বর দিন (যেমন ০১৭XXXXXXXX)।',
	'platform' => 'একটি Platform নির্বাচন করুন।',
	'service'  => 'একটি Service নির্বাচন করুন।',
	'link'     => 'সঠিক লিংক দিন (http:// বা https:// দিয়ে শুরু)।',
	'quantity' => 'প্রয়োজনীয় পরিমাণ ১ বা তার বেশি একটি সংখ্যা হতে হবে।',
);

$platforms = wf_smm_platforms();
$services  = wf_smm_services();
$overview  = wf_smm_platform_overview();
?>
<main id="smm-page">

<div class="page-header wrap"><div class="inner" style="display:flex; flex-wrap:wrap; gap:40px; align-items:center; max-width:none;">
  <div style="flex:1 1 300px; min-width:0; max-width:620px; width:100%;">
    <div class="ico"><svg><use href="#i-megaphone"/></svg></div>
    <span class="eyebrow">আপনার সোশ্যাল মিডিয়া গ্রোথ পার্টনার</span>
    <h1>সোশ্যাল মিডিয়ায় <span class="smm-hl">লাইক</span>, <span class="smm-hl">ফলোয়ার</span> বা <span class="smm-hl">প্রমোশন</span> কম থাকায় কাস্টমার আপনার ব্যবসাকে বিশ্বাস করতে পারছেন না?</h1>
    <p>আপনার ব্র্যান্ডের অনলাইন পরিচিতি বাড়াতে <span class="smm-hl">Women's Fight</span> দিচ্ছে সোশ্যাল মিডিয়া লাইক, ফলোয়ার, ভিউ ও প্রমোশন সার্ভিস।</p>
    <p class="smm-highlight-text">আপনার ব্র্যান্ডের পরিচিতি বাড়ান, সঠিক অডিয়েন্সের কাছে পৌঁছান!</p>
    <div style="display:flex; gap:14px; flex-wrap:wrap; margin-top:26px;">
      <a href="#smm-request-form" class="btn btn-primary">এখনই সার্ভিস নিন</a>
      <a href="#smm-platforms" class="btn btn-ghost">সার্ভিসগুলো দেখুন</a>
    </div>
    <div class="smm-trust-row">
      <span><svg><use href="#i-grid"/></svg>১০টি সোশ্যাল প্ল্যাটফর্ম</span>
      <span><svg><use href="#i-check"/></svg>ফ্রি সার্ভিস রিকোয়েস্ট</span>
      <span><svg><use href="#i-chat"/></svg>টিমের সঙ্গে সরাসরি আলোচনা</span>
    </div>
  </div>
  <div class="smm-hero-visual">
    <div class="smm-orbit-card">
      <h4>জনপ্রিয় প্ল্যাটফর্ম</h4>
      <div class="smm-orbit-row">
        <div class="smm-orbit-badge" style="background:#1877f2;"><svg><use href="#i-sm-facebook"/></svg></div>
        <div style="flex:1;"><b>Facebook</b><span>Page ও Post প্রোমোশন</span></div>
      </div>
      <div class="smm-orbit-row">
        <div class="smm-orbit-badge" style="background:linear-gradient(135deg,#f58529,#dd2a7b 50%,#8134af);"><svg><use href="#i-sm-instagram"/></svg></div>
        <div style="flex:1;"><b>Instagram</b><span>Profile ও Reels প্রোমোশন</span></div>
      </div>
      <div class="smm-orbit-row">
        <div class="smm-orbit-badge" style="background:#ff0000;"><svg><use href="#i-sm-youtube"/></svg></div>
        <div style="flex:1;"><b>YouTube</b><span>Channel ও Video প্রোমোশন</span></div>
      </div>
    </div>
  </div>
</div></div>

<section class="tight wrap" id="smm-platforms">
  <div class="section-head center"><span class="eyebrow">প্ল্যাটফর্ম</span><h2>কোন প্ল্যাটফর্মে গ্রো করতে চান?</h2><p>Facebook, Instagram, YouTube, TikTok সহ জনপ্রিয় প্ল্যাটফর্মে আপনার প্রয়োজনীয় সার্ভিস বেছে নিন।</p></div>
  <div class="smm-platform-grid" id="smm-platform-grid">
    <?php foreach ( $platforms as $key => $label ) : ?>
      <div class="smm-platform-card" data-platform="<?php echo esc_attr( $key ); ?>">
        <div class="smm-platform-badge"><svg><use href="#i-sm-<?php echo esc_attr( $key ); ?>"/></svg></div>
        <span><?php echo esc_html( $label ); ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="smm-services-panel" id="smm-services-panel">
    <h3 id="smm-services-title"></h3>
    <div class="smm-service-grid" id="smm-service-grid"></div>
  </div>

  <div class="smm-overview-wrap">
    <table class="smm-overview-table">
      <thead><tr><th>প্ল্যাটফর্ম</th><th>উপলব্ধ গ্রোথ সার্ভিস</th><th>সাধারণ ব্যবহার</th></tr></thead>
      <tbody>
        <?php foreach ( $overview as $key => $row ) : ?>
          <tr><td><?php echo esc_html( $platforms[ $key ] ); ?></td><td><?php echo esc_html( $row['services'] ); ?></td><td><?php echo esc_html( $row['uses'] ); ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="tight wrap">
  <div class="section-head center"><span class="eyebrow">প্রক্রিয়া</span><h2>মাত্র ৩ ধাপে সার্ভিস নিন</h2></div>
  <div class="smm-steps">
    <div class="smm-step"><span class="num">১</span><h3>প্ল্যাটফর্ম নির্বাচন</h3><p>আপনার পছন্দের প্ল্যাটফর্ম নির্বাচন করুন।</p></div>
    <div class="smm-step"><span class="num">২</span><h3>রিকোয়েস্ট জমা দিন</h3><p>সার্ভিস ও পরিমাণ দিয়ে রিকোয়েস্ট জমা দিন।</p></div>
    <div class="smm-step"><span class="num">৩</span><h3>টিমের সঙ্গে আলোচনা</h3><p>আমাদের টিমের সঙ্গে খরচ ও বিস্তারিত আলোচনা করুন।</p></div>
  </div>
</section>

<section class="tight alt"><div class="wrap" id="smm-request-form">

  <?php if ( $submitted ) : ?>

    <div class="wf-cform-success">
      <p>ধন্যবাদ! আপনার রিকোয়েস্ট সফলভাবে জমা হয়েছে। আমাদের টিম আপনার সঙ্গে যোগাযোগ করবে।</p>
    </div>

  <?php else : ?>

    <div class="section-head center"><span class="eyebrow">রিকোয়েস্ট ফর্ম</span><h2>আপনার পেজের গ্রোথ শুরু করতে প্রস্তুত?</h2><p>প্রয়োজনীয় সার্ভিস নির্বাচন করে রিকোয়েস্ট জমা দিন। আমাদের টিম আপনার সঙ্গে যোগাযোগ করবে।</p></div>

    <form class="wf-cform" id="smm-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
      <input type="hidden" name="action" value="wf_smm_submit_request">
      <input type="hidden" name="wf_smm_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
      <input type="hidden" name="wf_smm_ts" value="<?php echo esc_attr( time() ); ?>">
      <?php wp_nonce_field( 'wf_smm_request', 'wf_smm_nonce' ); ?>
      <div class="smm-hp"><label for="wf_smm_website">Website</label><input type="text" id="wf_smm_website" name="wf_smm_website" tabindex="-1" autocomplete="off"></div>

      <div class="frow">
        <div>
          <label for="wf_smm_name">আপনার নাম</label>
          <input type="text" id="wf_smm_name" name="wf_smm_name" required placeholder="আপনার পূর্ণ নাম">
          <div class="smm-field-error<?php echo in_array( 'name', $error_codes, true ) ? ' show' : ''; ?>"><?php echo esc_html( $error_labels['name'] ); ?></div>
        </div>
        <div>
          <label for="wf_smm_mobile">মোবাইল নম্বর</label>
          <input type="tel" id="wf_smm_mobile" name="wf_smm_mobile" required placeholder="০১৭XXXXXXXX">
          <div class="smm-field-error<?php echo in_array( 'mobile', $error_codes, true ) ? ' show' : ''; ?>"><?php echo esc_html( $error_labels['mobile'] ); ?></div>
        </div>
      </div>

      <div class="frow">
        <div>
          <label for="wf_smm_platform">প্ল্যাটফর্ম নির্বাচন করুন</label>
          <select id="wf_smm_platform" name="wf_smm_platform" required>
            <option value="">সিলেক্ট করুন</option>
            <?php foreach ( $platforms as $key => $label ) : ?>
              <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
          </select>
          <div class="smm-field-error<?php echo in_array( 'platform', $error_codes, true ) ? ' show' : ''; ?>"><?php echo esc_html( $error_labels['platform'] ); ?></div>
        </div>
        <div>
          <label for="wf_smm_service">সার্ভিস নির্বাচন করুন</label>
          <select id="wf_smm_service" name="wf_smm_service" required>
            <option value="">আগে প্ল্যাটফর্ম সিলেক্ট করুন</option>
          </select>
          <div class="smm-field-error<?php echo in_array( 'service', $error_codes, true ) ? ' show' : ''; ?>"><?php echo esc_html( $error_labels['service'] ); ?></div>
        </div>
      </div>

      <div>
        <label for="wf_smm_link">পেজ / প্রোফাইল / পোস্ট / ভিডিও লিংক</label>
        <input type="url" id="wf_smm_link" name="wf_smm_link" required placeholder="https://...">
        <div class="smm-field-error<?php echo in_array( 'link', $error_codes, true ) ? ' show' : ''; ?>"><?php echo esc_html( $error_labels['link'] ); ?></div>
      </div>

      <div>
        <label for="wf_smm_quantity">প্রয়োজনীয় পরিমাণ (Quantity)</label>
        <input type="number" id="wf_smm_quantity" name="wf_smm_quantity" min="1" step="1" required placeholder="যেমন: ১০০০">
        <div class="smm-field-error<?php echo in_array( 'quantity', $error_codes, true ) ? ' show' : ''; ?>"><?php echo esc_html( $error_labels['quantity'] ); ?></div>
      </div>

      <div>
        <label for="wf_smm_extra">অতিরিক্ত তথ্য (ঐচ্ছিক)</label>
        <textarea id="wf_smm_extra" name="wf_smm_extra" placeholder="আপনার অতিরিক্ত কোনো তথ্য থাকলে লিখুন"></textarea>
      </div>

      <button class="btn btn-primary" type="submit" id="smm-submit-btn">রিকোয়েস্ট জমা দিন</button>
      <p class="smm-form-note">রিকোয়েস্ট জমা দেওয়া সম্পূর্ণ ফ্রি।</p>
    </form>

  <?php endif; ?>

</div></section>

<section class="tight wrap">
  <div class="section-head"><span class="eyebrow">সাধারণ প্রশ্ন</span><h2>যা জানতে চান</h2></div>
  <div class="faq">
    <details open><summary>কীভাবে সার্ভিসের জন্য রিকোয়েস্ট করব?</summary><p>উপরে আপনার পছন্দের প্ল্যাটফর্ম ও সার্ভিস বেছে নিন, অথবা সরাসরি নিচের ফর্মটি পূরণ করুন — দুই ক্ষেত্রেই আপনার রিকোয়েস্ট আমাদের কাছে পৌঁছে যাবে।</p></details>
    <details><summary>রিকোয়েস্ট জমা দিতে কি কোনো টাকা লাগবে?</summary><p>না, রিকোয়েস্ট জমা দেওয়া সম্পূর্ণ ফ্রি। কোনো পেমেন্ট ছাড়াই আপনি আপনার প্রয়োজন জানাতে পারেন — টাকা-পয়সার বিষয় আলোচনার পর, আপনার সম্মতি পেলেই এগোয়।</p></details>
    <details><summary>সার্ভিসের খরচ কীভাবে জানব?</summary><p>আপনার প্রয়োজন, সার্ভিসের ধরন ও পরিমাণ বুঝে আমাদের টিম সরাসরি আপনার সঙ্গে আলোচনা করে খরচ জানাবে — কোনো ফিক্সড প্যাকেজ নয়, আপনার চাহিদা অনুযায়ী।</p></details>
    <details><summary>রিকোয়েস্ট করার পর কী হবে?</summary><p>আমাদের টিম আপনার রিকোয়েস্টটি পর্যালোচনা করে আপনার দেওয়া মোবাইল নম্বরে যোগাযোগ করবে এবং পরবর্তী ধাপ নিয়ে কথা বলবে।</p></details>
    <details><summary>কাজ শুরু করার আগে বিস্তারিত আলোচনা করা যাবে কি?</summary><p>হ্যাঁ, অবশ্যই। কাজ শুরুর আগে সার্ভিস, সময় ও খরচ — সব বিষয় স্পষ্ট করে আলোচনা করা হয়। আপনার সম্মতি ছাড়া কোনো কাজ শুরু হয় না।</p></details>
    <details><summary>সব ধরনের সোশ্যাল মিডিয়া সার্ভিস কি পাওয়া যাবে?</summary><p>উপরে তালিকাভুক্ত ১০টি প্ল্যাটফর্ম ও তাদের সার্ভিসগুলোই বর্তমানে আমরা দিয়ে থাকি। এর বাইরে নির্দিষ্ট কোনো প্রয়োজন থাকলে ফর্মের "অতিরিক্ত তথ্য" ঘরে লিখে জানাতে পারেন, আমরা দেখে জানাব এটা সম্ভব কিনা।</p></details>
  </div>
</section>

<section class="tight wrap"><div class="cta-band"><div><h2>আপনার ব্যবসাকে অনলাইনে আরও পরিচিত করতে চান?</h2><p>Women's Fight-এর সোশ্যাল মিডিয়া সার্ভিস সম্পর্কে জানতে আজই যোগাযোগ করুন।</p></div><a class="btn btn-primary" href="#smm-request-form">এখনই সার্ভিস নিন</a></div></section>

</main>

<script>
(function () {
	var servicesData = <?php echo wp_json_encode( $services ); ?>;
	var platformLabels = <?php echo wp_json_encode( $platforms ); ?>;

	var grid = document.getElementById('smm-platform-grid');
	var panel = document.getElementById('smm-services-panel');
	var panelTitle = document.getElementById('smm-services-title');
	var serviceGrid = document.getElementById('smm-service-grid');
	var formPlatform = document.getElementById('wf_smm_platform');
	var formService = document.getElementById('wf_smm_service');

	function populateServiceSelect(platformKey, selectedService) {
		var list = servicesData[platformKey] || [];
		formService.innerHTML = '';
		if (!list.length) {
			formService.innerHTML = '<option value="">এই প্ল্যাটফর্মে এখনো কোনো সার্ভিস নেই</option>';
			return;
		}
		formService.innerHTML = '<option value="">সিলেক্ট করুন</option>';
		list.forEach(function (svc) {
			var opt = document.createElement('option');
			opt.value = svc.label;
			opt.textContent = svc.label;
			if (svc.label === selectedService) { opt.selected = true; }
			formService.appendChild(opt);
		});
	}

	function showPlatformServices(platformKey) {
		var list = servicesData[platformKey] || [];
		panelTitle.textContent = (platformLabels[platformKey] || '') + ' — সার্ভিসসমূহ';
		serviceGrid.innerHTML = '';
		list.forEach(function (svc) {
			var card = document.createElement('div');
			card.className = 'smm-service-card';
			card.innerHTML = '<div class="smm-service-ico"><svg><use href="#' + svc.icon + '"/></svg></div><b>' + svc.label + '</b><button type="button" class="btn btn-ghost">রিকোয়েস্ট করুন</button>';
			card.querySelector('button').addEventListener('click', function () {
				formPlatform.value = platformKey;
				populateServiceSelect(platformKey, svc.label);
				var formEl = document.getElementById('smm-request-form');
				if (formEl) { formEl.scrollIntoView({ behavior: 'smooth' }); }
			});
			serviceGrid.appendChild(card);
		});
		panel.classList.add('show');
	}

	if (grid) {
		grid.querySelectorAll('.smm-platform-card').forEach(function (card) {
			card.addEventListener('click', function () {
				grid.querySelectorAll('.smm-platform-card').forEach(function (c) { c.classList.remove('active'); });
				card.classList.add('active');
				showPlatformServices(card.getAttribute('data-platform'));
			});
		});
	}

	if (formPlatform) {
		formPlatform.addEventListener('change', function () {
			populateServiceSelect(formPlatform.value, '');
		});
	}

	var form = document.getElementById('smm-form');
	if (form) {
		form.addEventListener('submit', function () {
			var btn = document.getElementById('smm-submit-btn');
			if (btn) {
				btn.disabled = true;
				btn.textContent = 'পাঠানো হচ্ছে...';
			}
		});
	}
})();
</script>
<?php
get_footer();
