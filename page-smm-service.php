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
.smm-platform-grid{display:grid; grid-template-columns:repeat(5,1fr); gap:14px;}
.smm-platform-card{background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:22px 14px; text-align:center; cursor:pointer; transition:transform .15s ease, border-color .15s ease; display:flex; flex-direction:column; align-items:center; gap:10px;}
.smm-platform-card:hover{transform:translateY(-3px); border-color:var(--pink);}
.smm-platform-card.active{border-color:var(--pink); box-shadow:var(--glow-pink);}
.smm-platform-card svg{width:30px; height:30px; color:var(--pink-light);}
.smm-platform-card span{font-size:.86rem; font-weight:700;}

.smm-services-panel{margin-top:28px; background:var(--bg-2); border:1px solid var(--border); border-radius:var(--radius-lg); padding:30px; display:none;}
.smm-services-panel.show{display:block;}
.smm-services-panel h3{font-size:1.1rem; margin-bottom:18px;}
.smm-service-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:14px;}
.smm-service-card{background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:18px; display:flex; flex-direction:column; gap:12px;}
.smm-service-card b{font-size:.92rem;}
.smm-service-card button{align-self:flex-start;}

.smm-steps{display:grid; grid-template-columns:repeat(3,1fr); gap:20px;}
.smm-step{background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:28px; position:relative;}
.smm-step span.num{display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:50%; background:var(--grad); color:#fff; font-weight:700; margin-bottom:14px;}
.smm-step h3{font-size:1.02rem; margin-bottom:8px;}

.smm-field-error{font-size:.78rem; color:var(--pink-light); margin-top:-10px; margin-bottom:4px; display:none;}
.smm-field-error.show{display:block;}
.smm-hp{position:absolute; left:-9999px; top:-9999px;}

@media (max-width:980px){
  .smm-platform-grid{grid-template-columns:repeat(3,1fr);}
  .smm-service-grid{grid-template-columns:1fr 1fr;}
  .smm-steps{grid-template-columns:1fr;}
}
@media (max-width:640px){
  .smm-platform-grid{grid-template-columns:1fr 1fr;}
  .smm-service-grid{grid-template-columns:1fr;}
}
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
?>
<main>

<div class="page-header wrap"><div class="inner">
  <div class="ico"><svg><use href="#i-megaphone"/></svg></div>
  <span class="eyebrow">সার্ভিস</span>
  <h1>আপনার সোশ্যাল মিডিয়ার প্রচারণায় প্রয়োজন সঠিক পরিকল্পনা ও সহযোগিতা।</h1>
  <p>Facebook, Instagram, YouTube, TikTok সহ বিভিন্ন সোশ্যাল মিডিয়া প্ল্যাটফর্মের জন্য আপনার প্রয়োজন অনুযায়ী সার্ভিস সম্পর্কে জানতে রিকোয়েস্ট করুন। আমাদের টিম আপনার সঙ্গে যোগাযোগ করে বিস্তারিত আলোচনা করবে।</p>
  <div style="display:flex; gap:14px; flex-wrap:wrap; margin-top:26px;">
    <a href="#smm-request-form" class="btn btn-primary">সার্ভিস রিকোয়েস্ট করুন</a>
    <a href="#smm-platforms" class="btn btn-ghost">আমাদের সার্ভিসসমূহ</a>
  </div>
</div></div>

<section class="tight wrap" id="smm-platforms">
  <div class="section-head"><span class="eyebrow">প্ল্যাটফর্ম</span><h2>কোন প্ল্যাটফর্মের জন্য সার্ভিস প্রয়োজন?</h2><p>একটি প্ল্যাটফর্মে ক্লিক করুন, নিচে সেই প্ল্যাটফর্মের জন্য আমাদের সার্ভিসগুলো দেখতে পাবেন।</p></div>
  <div class="smm-platform-grid" id="smm-platform-grid">
    <?php foreach ( $platforms as $key => $label ) : ?>
      <div class="smm-platform-card" data-platform="<?php echo esc_attr( $key ); ?>">
        <svg><use href="#i-sm-<?php echo esc_attr( $key ); ?>"/></svg>
        <span><?php echo esc_html( $label ); ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="smm-services-panel" id="smm-services-panel">
    <h3 id="smm-services-title"></h3>
    <div class="smm-service-grid" id="smm-service-grid"></div>
  </div>
</section>

<section class="tight alt"><div class="wrap">
  <div class="section-head center"><span class="eyebrow">কেন আমরা</span><h2>কেন আমাদের বেছে নেবেন</h2></div>
  <div class="feat-grid">
    <div class="feat"><div class="ico sm"><svg><use href="#i-target"/></svg></div><h3>প্রয়োজন অনুযায়ী সার্ভিস</h3><p>আপনার লক্ষ্য অনুযায়ী সঠিক প্ল্যাটফর্ম ও সার্ভিস নির্বাচনের সুযোগ।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-chat"/></svg></div><h3>অর্ডারের আগে আলোচনা</h3><p>কাজ শুরুর আগে বিস্তারিত আলোচনা করে বুঝে নেওয়ার সুযোগ।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-form"/></svg></div><h3>সহজ রিকোয়েস্ট প্রক্রিয়া</h3><p>কয়েকটি তথ্য দিয়েই রিকোয়েস্ট জমা দেওয়া যায়, কোনো জটিলতা নেই।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-users"/></svg></div><h3>সরাসরি টিমের সাথে যোগাযোগ</h3><p>থার্ড-পার্টি প্যানেল নয় — সরাসরি আমাদের টিমের সাথে কথা বলার সুযোগ।</p></div>
    <div class="feat"><div class="ico sm"><svg><use href="#i-check"/></svg></div><h3>পরিষ্কার ধারণা</h3><p>সার্ভিসের শর্ত, সময় ও খরচ সম্পর্কে স্পষ্ট ধারণা দেওয়া হয়।</p></div>
  </div>
</div></section>

<section class="tight wrap">
  <div class="section-head center"><span class="eyebrow">প্রক্রিয়া</span><h2>যেভাবে কাজ করবেন আমাদের সাথে</h2></div>
  <div class="smm-steps">
    <div class="smm-step"><span class="num">১</span><h3>সার্ভিস নির্বাচন করুন</h3><p>আপনার প্রয়োজন অনুযায়ী প্ল্যাটফর্ম ও সার্ভিস বেছে নিন।</p></div>
    <div class="smm-step"><span class="num">২</span><h3>রিকোয়েস্ট জমা দিন</h3><p>নাম, মোবাইল নম্বর, লিংক ও প্রয়োজনীয় পরিমাণ দিয়ে ফর্ম পূরণ করুন।</p></div>
    <div class="smm-step"><span class="num">৩</span><h3>আমাদের টিম যোগাযোগ করবে</h3><p>আপনার রিকোয়েস্ট পর্যালোচনা করে সার্ভিস, খরচ ও অন্যান্য বিষয় নিয়ে আলোচনা করা হবে।</p></div>
  </div>
</section>

<section class="tight alt"><div class="wrap" id="smm-request-form">

  <?php if ( $submitted ) : ?>

    <div class="wf-cform-success">
      <p>ধন্যবাদ! আপনার রিকোয়েস্ট সফলভাবে জমা হয়েছে। আমাদের টিম আপনার সঙ্গে যোগাযোগ করবে।</p>
    </div>

  <?php else : ?>

    <div class="section-head center"><span class="eyebrow">রিকোয়েস্ট ফর্ম</span><h2>আপনার প্রয়োজনীয় সার্ভিসের জন্য রিকোয়েস্ট করুন</h2><p>নিচের তথ্যগুলো পূরণ করুন। আমাদের টিম আপনার সঙ্গে যোগাযোগ করে বিস্তারিত জানাবে।</p></div>

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
    </form>

  <?php endif; ?>

</div></section>

<section class="tight wrap">
  <div class="section-head"><span class="eyebrow">সাধারণ প্রশ্ন</span><h2>যা জানতে চান</h2></div>
  <div class="faq">
    <details open><summary>কীভাবে সার্ভিসের জন্য রিকোয়েস্ট করব?</summary><p>উপরের প্ল্যাটফর্ম ও সার্ভিস থেকে পছন্দমতো একটি বেছে নিয়ে, অথবা সরাসরি নিচের ফর্ম পূরণ করে রিকোয়েস্ট জমা দিতে পারেন।</p></details>
    <details><summary>রিকোয়েস্ট জমা দিতে কি টাকা লাগবে?</summary><p>না, রিকোয়েস্ট জমা দেওয়া সম্পূর্ণ ফ্রি। পেমেন্টের বিষয়টি আলোচনার পর, আপনার সম্মতিতেই এগোয়।</p></details>
    <details><summary>রিকোয়েস্ট করার পর কী হবে?</summary><p>আমাদের টিম আপনার রিকোয়েস্ট পর্যালোচনা করে আপনার দেওয়া মোবাইল নম্বরে যোগাযোগ করবে।</p></details>
    <details><summary>সার্ভিসের খরচ কীভাবে জানব?</summary><p>আপনার সঙ্গে সরাসরি আলোচনা করে প্রয়োজন ও পরিমাণ অনুযায়ী খরচ জানানো হবে।</p></details>
    <details><summary>কাজ শুরু করার আগে বিস্তারিত আলোচনা করা যাবে?</summary><p>হ্যাঁ, অবশ্যই। কাজ শুরুর আগে সব বিষয় স্পষ্ট করে আলোচনা করা হয়, আপনার সম্মতি ছাড়া কোনো কিছু শুরু হয় না।</p></details>
    <details><summary>সব ধরনের সোশ্যাল মিডিয়া সার্ভিস কি পাওয়া যাবে?</summary><p>উপরে তালিকাভুক্ত প্ল্যাটফর্ম ও সার্ভিসগুলোই বর্তমানে আমরা দিয়ে থাকি। নির্দিষ্ট কোনো প্রয়োজন থাকলে রিকোয়েস্টের "অতিরিক্ত তথ্য" ঘরে জানাতে পারেন।</p></details>
  </div>
</section>

<section class="tight wrap"><div class="cta-band"><div><h2>আপনার সোশ্যাল মিডিয়ার জন্য কোন সার্ভিস প্রয়োজন?</h2><p>আপনার প্রয়োজন আমাদের জানান। আমাদের টিম বিস্তারিত আলোচনা করে উপযুক্ত সার্ভিস সম্পর্কে জানাবে।</p></div><a class="btn btn-primary" href="#smm-request-form">এখনই রিকোয়েস্ট করুন</a></div></section>

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
			opt.value = svc;
			opt.textContent = svc;
			if (svc === selectedService) { opt.selected = true; }
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
			card.innerHTML = '<b>' + svc + '</b><button type="button" class="btn btn-ghost">রিকোয়েস্ট করুন</button>';
			card.querySelector('button').addEventListener('click', function () {
				formPlatform.value = platformKey;
				populateServiceSelect(platformKey, svc);
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
